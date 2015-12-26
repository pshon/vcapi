<?php
namespace VCAPI\Model;

class Vacancy
{

    public $id;

    public $companyId;

    public $salary;

    public $level;

    public $professionId;

    public $profssionName;

    public $companyName;

    public $isCorporation = false;

    public $corporationId = 0;

    public function __construct(\stdClass $vacancyItem)
    {
        $this->id = $vacancyItem->CompanyVacancy->id;
        $this->companyId = $vacancyItem->CompanyVacancy->company_id;
        $this->companyName = $vacancyItem->Company->name;
        $this->isCorporation = $vacancyItem->Company->master_type == 'corporation' ? true : false;
        $this->corporationId = $vacancyItem->Company->corporation_master;
        $this->salary = $vacancyItem->CompanyVacancy->salary;
        $this->level = $vacancyItem->CompanyVacancy->level;
        $this->professionId = $vacancyItem->ProfessionType->id;
        $this->profssionName = $vacancyItem->ProfessionType->name;
    }

    public function getJob()
    {
        $user = new \VCAPI\Model\User();
        
        if ($user->level < $this->level) {
            throw new \ErrorException('User professional level is low, be need ' . $this->level . ' level or higher');
        }
        
        $result = \VCAPI\Common\Request::post('/vacancies/work_vacancy.json', array(
            'data' => array(
                'CompanyVacancy' => array(
                    'id' => $this->id
                )
            )
        ), false);
        
        return $result;
    }
}