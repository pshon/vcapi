<?php

namespace VCAPI\Model;

class User
{

    public $userId;

    public $level;

    public $energy;

    public $maxEnergy;

    public $balance;

    public $health;

    public $city;

    public static $instance = false;

    public function __construct()
    {
        self::$instance = $this;
        $this->getShortInfo();
    }

    public static function instance()
    {
        if (self::$instance) {
            return self::$instance;
        } else {
            self::$instance = new User();
        }
    }

    public function Auth($login, $pass)
    {
        $result = \VCAPI\Common\Request::post('/users/app_auth.json', array(
            'data' => array(
                'User' => array(
                    'username' => $login,
                    'password' => $pass
                )
            )
        ), false);
        
        $this->getShortInfo();
        
        if (empty($this->userId)) {
            throw new \ErrorException('Authorize fail!');
            return false;
        }
        
        return true;
    }

    public function getShortInfo()
    {
        $result = \VCAPI\Common\Request::get('/users/short_infos.json');
        
        var_dump($result);
        
        if (empty($result->userId)) {
            return false;
        }
        
        $this->userId = $result->userId;
        $this->balance = $result->user->User->vd_balance;
        $this->energy = $result->user->User->energy;
        $this->maxEnergy = $result->user->User->max_energy;
        $this->health = $result->user->User->health;
        $this->level = $result->user->UserLevel->level;
        $this->city = new \VCAPI\Model\City($result->user->User->city_id);
        
        return true;
    }

    public function UnAuth()
    {
        \VCAPI\Common\Request::removeCookie();
    }
}