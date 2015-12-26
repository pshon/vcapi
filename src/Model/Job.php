<?php
namespace VCAPI\Model;

class Job
{

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

    public function __construct()
    {
        $this->getShortInfo();
    }

    public function getShortInfo()
    {
        $result = \VCAPI\Common\Request::get('/users/user_work.json', false);
        
        if (! $result->worker) {
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

    public function getProfessions()
    {
        return $this->professions;
    }

    public function resign()
    {
        if (! $this->worker) {
            throw new \ErrorException("User not work now");
            return false;
        }
        
        $result = \VCAPI\Common\Request::post('/company_workers/resign_from_work.json', array(
            'data' => array(
                'CompanyWorker' => array(
                    'worker_id' => $this->companyWorkerId
                )
            )
        ), false);
    }

    public function job($energy)
    {
        if (! $this->worker)
            throw new \ErrorException('User not work now');
        $user = \VCAPI\Model\User::$instance();
        if (! $user->energy || $user->energy < $energy)
            throw new \ErrorException('No energy');
        if ($energy === 0) {
            $energy = $user->energy;
        }
        if (! $energy = intval($energy))
            throw new \ErrorException('Energy must be bigger than null');
        
        $energy = $this->splitEnergy($energy);
        $statistic = array(
            'energy' => 0,
            'salary' => 0,
            'expirience' => 0,
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
            
            $result = \VCAPI\Common\Request::post('/users/begin_work.json', $query, false);
            
            $statistic['energy'] += $result->work_report->user_energy_spent;
            $statistic['salary'] += $result->short_work_report->salary;
            $statistic['expirience'] += $result->short_work_report->exp;
            $statistic['production_points'] += $result->short_work_report->today_production_points;
        }
        
        return $statistic;
    }

    private function splitEnergy($energy)
    {
        $energy = floor($energy / 10) * 10;
        $splited = array();
        
        if ($energy >= 100) {
            $pices = floor($energy / 100);
            for ($i = 0; $i < $pices; $i ++) {
                $splited[] = 100;
                $energy -= 100;
            }
        }
        
        if ($energy >= 50) {
            $pices = floor($energy / 50);
            for ($i = 0; $i < $pices; $i ++) {
                $splited[] = 50;
                $energy -= 50;
            }
        }
        
        if ($energy >= 10) {
            $pices = floor($energy / 10);
            for ($i = 0; $i < $pices; $i ++) {
                $splited[] = 10;
                $energy -= 10;
            }
        }
        
        return $splited;
    }
}