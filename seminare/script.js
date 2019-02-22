var apiUrl = "http://seminare.mladezbrno.cz/"

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
};

function appendBlock(id, content) {

    return $('div.seminare').append("<div id='" + id + "' class='h4'>" + content + "</div>");
}

function appendPair(description, content, element) {

    element.append("<div class='info'><strong>" + description + "</strong> " + content + "</div>");
}

function appendVariant(id, time, place, registered, element) {

    checked_str = registered ? "checked" : "";

    element.append("<div class='form-group margined'><div class='checkbox'><input type='checkbox' class='checkbox variant' data-id='" + id + "' " + checked_str + "> " +
        time + "</div>");


}

$(document).ready(function () {

    var userParameter = getUrlParameter('user');


    if (userParameter == null) {
        location.replace('kod.html');
    }

    $.ajax({
        url: apiUrl + "list?user=" + userParameter,
        dataType: 'json',
        success: function (data) {
            var items = [];
            $.each(data, function (key, val) {

                    seminarBlok = appendBlock('seminar', val.name)
                    appendPair('Řečník:', val.speaker, seminarBlok);

                    $.each(val.variants, function (keyv, valv) {
                        appendVariant(key + '_' + keyv, valv.time, valv.place, valv.registered, seminarBlok)
                    })

                    console.log(val);

                },
            );

            $("input.checkbox.variant").change(function (event) {
                var element = this
                console.log($(event.target).data('id'));

                if (this.checked) {
                    $.getJSON(apiUrl + "register?user=" + userParameter + "&q=" + $(element).data('id'), function (data) {
                        console.log('OK_REG');
                    });
                } else {
                    $.getJSON(apiUrl + "unregister?user=" + userParameter + "&q=" + $(element).data('id'), function (data) {
                        console.log('OK_DEREG');
                    });
                }

            })

        },
        error: function () {
            location.replace('kod.html');
        }
    });
    $.getJSON("http://localhost/list?user=" + userParameter,);

});