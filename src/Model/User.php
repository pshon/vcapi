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
    
    public $maxHealth;

    public $city;
    
    public $avatarId = 0;

    public static $instance = false;

    public function __construct($clearSession = false)
    {
        self::$instance = $this;
        
        if ($clearSession) {
            $this->UnAuth();
        }
        
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
        $this->UnAuth();
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
            \VCAPI\Common\Error::exception('Authorization error');
            return false;
        }
        
        return true;
    }

    public function getShortInfo()
    {
        $result = \VCAPI\Common\Request::get('/users/short_infos.json');
                
        if (empty($result->userId)) {
            return false;
        }
        
        $this->userId = $result->userId;
        $this->balance = $result->user->User->vd_balance;
        $this->energy = $result->user->User->energy;
        $this->maxEnergy = $result->user->User->max_energy;
        $this->health = $result->user->User->health;
        $this->maxHealth = $result->user->User->max_health;
        $this->level = $result->user->UserLevel->level;
        $this->city = new \VCAPI\Model\City($result->user->User->city_id);
        $this->avatarId = $result->user->User->avatar;
        
        return true;
    }
    
    public function getFullInfo() {
        $result = \VCAPI\Common\Request::get('/users/infos.json');
        
        if (empty($result->userId)) {
            return false;
        }
        
        return $result->user;
    }

    /**
     * Get all companies that belong to user
     *
     * @return array|bool
     */
    public function getCompanies() {
        $result = \VCAPI\Common\Request::get('/companies/user_companies.json');

        if (empty($result->userId)) {
            return false;
        }

        $companies = [];

        foreach ($result->companies as $company) {
            $companies[] = new \VCAPI\Model\Company($company->Company);
        }

        return $companies;
    }

    public function UnAuth()
    {
        \VCAPI\Common\Request::removeCookie();
    }
}