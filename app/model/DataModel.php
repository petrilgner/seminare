<?php
/**
 * Created by PhpStorm.
 * User: petr
 * Date: 2/22/2019
 * Time: 12:13 PM
 */

namespace App\Model;

use Nette\Application\UI\Presenter;
use Nette\Database\Context;
use Nette\Database\Table\Selection;
use Nette\Database\UniqueConstraintViolationException;
use Nette\SmartObject;
use Nette\Utils\DateTime;

class DataModel
{

    use SmartObject;

    /** @var array */
    private $data;

    /** @var Context */
    private $db;


    public function __construct($data, Context $context)
    {
        $this->db = $context;
        $this->data = $data;


    }

    public function getRegistrationsTable(): Selection
    {
        return $this->db->table('registrations');
    }

    public function getQrTable(): Selection
    {
        return $this->db->table('qr');
    }

    public function getList($userKey)
    {

        $list = $this->data;

        foreach ($list as $index => $l) {

            foreach ($l['variants'] as $short => $variantData) {
                $list[$index]['variants'][$short]['registered'] = $this->isRegistered($userKey, $index, $short);
            }
        }

        return $list;
    }

    public function checkUser($user): bool
    {
        $sel = $this->getQrTable()->get($user);
        return $sel == true;

    }

    public function findUser($key): string
    {
        $sel = $this->getQrTable()
            ->where('id', $key)
            ->select('code')
            ->fetch();

        if ($sel) {
            return $sel['code'];
        }

    }

    public function isRegistered($userKey, $action, $variant): bool
    {
        $registration = $this->getRegistrationsTable()
            ->where([
                'user' => $userKey,
                'seminar' => $action,
                'varianta' => $variant
            ])
            ->fetch();

        return $registration == true;
    }

    public function register($userKey, $action, $variant): bool
    {
        try {
            $this->getRegistrationsTable()->insert(
                ['user' => $userKey,
                    'seminar' => $action,
                    'varianta' => $variant,
                    'ip' => 'TEST',
                    'time' => new DateTime()]
            );
            return true;
        } catch (UniqueConstraintViolationException $exception) {
            return false;
        }

    }

    public function unregister($userKey, $action, $variant)
    {
        $selection = $this->getRegistrationsTable()
            ->where(['user' => $userKey,
                'seminar' => $action,
                'varianta' => $variant]);

        if (!empty($selection)) {
            $selection->delete();
            return true;
        }
        return false;

    }


    public function checkVariant($action, $variant)
    {
        if (isset($this->data[$action]['variants'][$variant])) {
            return true;
        }
        return false;
    }

}