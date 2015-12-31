<?php

namespace VCAPI\Model;

use VCAPI\Common\Collection;
use VCAPI\Common\Error;
use VCAPI\Common\Request;

class User
{
    public $instanceIdentifier = '';

    public $userId;

    public $level;

    public $energy;

    public $maxEnergy;

    public $balance;

    public $health;
    
    public $maxHealth;

    public $city;
    
    public $avatarId = 0;

    private static $instances = [];

    private function __construct($instanceIdentifier)
    {
        $this->instanceIdentifier = $instanceIdentifier;
    }

    /**
     * @param string $key
     * @return User
     */
    public static function getInstance($key = '')
    {
        if(!array_key_exists($key, self::$instances)) {
            self::$instances[$key] = new self($key);
        }

        return self::$instances[$key];
    }

    /**
     * @param $login
     * @param $pass
     * @return bool
     * @throws \ErrorException
     */
    public function auth($login, $pass)
    {
        $this->unAuth();
        $result = Request::post('/users/app_auth.json', array(
            'data' => array(
                'User' => array(
                    'username' => $login,
                    'password' => $pass
                )
            )
        ), $this->instanceIdentifier);
        
        $this->getShortInfo();
        
        if (empty($this->userId)) {
            return Error::exception('Authorization error');
        }
        
        return true;
    }

    /**
     * @return bool
     * @throws \ErrorException
     */
    public function getShortInfo()
    {
        $result = Request::get('/users/short_infos.json', $this->instanceIdentifier);

        if (!property_exists($result, 'userId')) {
            return Error::exception('Authorization error');
        }
        
        $this->userId = $result->userId;
        $this->balance = $result->user->User->vd_balance;
        $this->energy = $result->user->User->energy;
        $this->maxEnergy = $result->user->User->max_energy;
        $this->health = $result->user->User->health;
        $this->maxHealth = $result->user->User->max_health;
        $this->level = $result->user->UserLevel->level;
        $this->city = new City($result->user->User->city_id);
        $this->avatarId = $result->user->User->avatar;
        
        return true;
    }

    /**
     * @return mixed
     * @throws \ErrorException
     */
    public function getFullInfo() {
        $result = Request::get('/users/infos.json', $this->instanceIdentifier);
        
        if (empty($result->userId)) {
            return Error::exception('Authorization error');
        }
        
        return $result->user;
    }

    /**
     * Get all companies that belong to user
     *
     * @return \VCAPI\Common\Collection
     * @throws \ErrorException
     */
    public function getCompanies() {
        $result = Request::get('/companies/user_companies.json', $this->instanceIdentifier);

        if (empty($result->userId)) {
            return Error::exception('Authorization error');
        }

        $companies = new Collection();

        foreach ($result->companies as $company) {
            $companies->add(new Company($company->Company, $this->instanceIdentifier));
        }

        return $companies;
    }

    /**
     * Get all corporations that belong to user
     *
     * @return \VCAPI\Common\Collection
     * @throws \ErrorException
     */
    public function getCorporations() {
        $model = new Corporations($this->instanceIdentifier);
        
        return $model->getAll();
    }

    public function unAuth()
    {
        Request::removeCookie($this->instanceIdentifier);
    }

    private function __clone() { }
}