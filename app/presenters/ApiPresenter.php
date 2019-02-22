<?php

namespace App\Presenters;

use App\Model\DataModel;

class ApiPresenter extends \Nette\Application\UI\Presenter
{

    /** @var DataModel @inject */
    public $model;

    private $user;

    public function startup()
    {
        parent::startup();
        $this->user = ltrim($this->getRequest()->getParameter('user'));
        if(is_numeric($this->user)) {
            $this->user = $this->model->findUser($this->user);
        }

        if($this->user) {
            if(!$this->model->checkUser($this->user)) {
                $this->error('Not valid user', 403);
            }
        }
        else {
            $this->error('User ID missing', 401);
        }
    }

    public function actionList($user)
    {
        $this->sendJson($this->model->getList($this->user));
    }

    public function actionRegister($user, $code)
    {
        $state = false;
        $parts = explode('_', $code);
        if ($this->model->checkVariant($parts[0], $parts[1])) {
            $state = $this->model->register($this->user, $parts[0], $parts[1]);

        }
        $this->sendJson(['result' => $state]);

    }


    public function actionUnregister($user, $code)
    {
        if(!$code) {
            $this->error('No CODE.', 403);
        }
        $state = false;
        $parts = explode('_', $code);
        if ($this->model->checkVariant($parts[0], $parts[1])) {
            $state = $this->model->unregister($this->user, $parts[0], $parts[1]);

        }
        $this->sendJson(['result' => $state]);

    }

    public function afterRender()
    {
        parent::afterRender();
        $this->terminate();
    }
}