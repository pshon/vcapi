<?php
namespace VCAPI\Model;

use VCAPI\Common\Error;
use VCAPI\Common\Model;
use VCAPI\Common\Request;

class Job extends Model
{
    public $instanceIdentifier = '';

    private $professions = array(
        1 => "Фермер",
        7 => "Рабочий",
        6 => "Рыбак",
        4 => "Повар",
        3 => "Шахтер",
        2 => "Лесоруб"
    );

    public $professionId;

    public $professionName;

    public $worker = false;

    public $salary = 0;

    public $companyId;

    public $companyWorkerId;

    public $companyName;

    /**
     * Job constructor.
     * @param string $instanceIdentifier
     */
    public function __construct($instanceIdentifier = '')
    {
        $this->instanceIdentifier = $instanceIdentifier;

        $this->getShortInfo();
    }

    public function getShortInfo()
    {
        $result = Request::get('/users/user_work.json', $this->instanceIdentifier);

        if (!$result->worker) {
            $this->worker = false;
        } else {
            $this->worker = true;
            $this->professionId = $result->profession_type_id;
            $this->professionName = $result->profession_name;
            $this->salary = $result->salary;
            $this->companyId = $result->company_id;
            $this->companyWorkerId = $result->company_worker_id;
            $this->companyName = $result->company_name;
        }
    }

    /**
     * @return array
     */
    public function getProfessions()
    {
        return $this->professions;
    }

    public function resign()
    {
        if (!$this->worker) {
            Error::exception("User not work now");
            return false;
        }
        
        $result = Request::post('/company_workers/resign_from_work.json', array(
            'data' => array(
                'CompanyWorker' => array(
                    'worker_id' => $this->companyWorkerId
                )
            )
        ), $this->instanceIdentifier);
    }

    /**
     * @param $energy
     * @return array|bool
     * @throws \ErrorException
     */
    public function doWork($energy)
    {
        if (!$this->worker) {
            return Error::exception("User doesn't work now");
        }

        if (!User::getInstance($this->instanceIdentifier)->energy || User::getInstance($this->instanceIdentifier)->energy < $energy) {
            return Error::exception('No energy');
        }

        if ($energy === 0) {
            $energy = User::getInstance($this->instanceIdentifier)->energy;
        }

        if (!$energy = intval($energy)) {
            return Error::exception('Energy must be bigger than null');
        }

        $energy = $this->splitEnergy($energy);
        $statistic = array(
            'energy'            => 0,
            'salary'            => 0,
            'experience'        => 0,
            'production_points' => 0
        );

        foreach ($energy as $value) {
            $query = array(
                'data' => array(
                    'User' => array(
                        'energy' => $value
                    ),
                    'Company' => array(
                        'id' => $this->companyId
                    )
                )
            );

            $result = Request::post('/users/begin_work.json', $query, $this->instanceIdentifier);

            $statistic['energy'] += $result->work_report->user_energy_spent;
            $statistic['salary'] += $result->short_work_report->salary;
            $statistic['experience'] += $result->short_work_report->exp;
            $statistic['production_points'] += $result->short_work_report->today_production_points;
        }

        return $statistic;
    }

    /**
     * @param $energy
     * @return array
     */
    private function splitEnergy($energy)
    {
        $energy = floor($energy / 10) * 10;
        $splited = array();
        
        if ($energy >= 100) {
            $pices = floor($energy / 100);
            for ($i = 0; $i < $pices; $i++) {
                $splited[] = 100;
                $energy -= 100;
            }
        }
        
        if ($energy >= 50) {
            $pices = floor($energy / 50);
            for ($i = 0; $i < $pices; $i++) {
                $splited[] = 50;
                $energy -= 50;
            }
        }
        
        if ($energy >= 10) {
            $pices = floor($energy / 10);
            for ($i = 0; $i < $pices; $i++) {
                $splited[] = 10;
                $energy -= 10;
            }
        }
        
        return $splited;
    }
}