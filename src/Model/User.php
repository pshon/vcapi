<?php

namespace VCAPI\Model;

use VCAPI\Common\Collection;
use VCAPI\Common\Error;
use VCAPI\Common\Model;
use VCAPI\Common\Request;

class User extends Model
{
    public $instanceIdentifier = '';

    public $id;

    public $avatar = 0;

    public $avatar_img;

    public $username;

    public $vd_balance;

    public $vg_balance;

    public $fight_points;

    public $health;

    public $max_health;

    public $energy;

    public $max_energy;

    public $prestige;

    public $social_status_title;

    public $social_status;

    public $city_id;

    public $city_name;

    public $party_name;

    public $last_up_energy;

    public $delta_recovery_energy;

    public $military_rank;

    public $military_rank_img;

    /** @var \VCAPI\Model\UserLevel */
    public $UserLevel;

    /** @var \VCAPI\Model\City */
    public $City;

    /** @var \VCAPI\Model\MilitaryRank */
    public $MilitaryRank;

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
        
        if (empty($this->id)) {
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

        $this->fillModel($result->user->User);

        if (property_exists($result->user, 'UserLevel')) {
            $this->UserLevel = new UserLevel($result->user->UserLevel);
        }

        if (property_exists($result->user, 'City')) {
            $this->City = new City($result->user->City);
        }

        if (property_exists($result->user, 'MilitaryRank')) {
            $this->MilitaryRank = new MilitaryRank($result->user->MilitaryRank);
        }

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

        $this->fillModel($result->user->User);

        if (property_exists($result->user, 'UserLevel')) {
            $this->UserLevel = new UserLevel($result->user->UserLevel);
        }

        if (property_exists($result->user, 'City')) {
            $this->City = new City($result->user->City);
        }

        if (property_exists($result->user, 'MilitaryRank')) {
            $this->MilitaryRank = new MilitaryRank($result->user->MilitaryRank);
        }

        return true;
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

    /**
     * @return string
     */
    public function getAvatarURL()
    {
        return 'http://news.vircities.com/img/avatars/' . $this->avatar_img;
    }

    public function unAuth()
    {
        Request::removeCookie($this->instanceIdentifier);
    }

    private function __clone() { }
}