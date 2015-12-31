<?php
namespace VCAPI\Model;

use VCAPI\Common\Error;
use VCAPI\Common\Request;

class Vacancy
{
    public $instanceIdentifier = '';

    public $id;

    public $companyId;

    public $salary;

    public $level;

    public $professionId;

    public $professionName;

    public $companyName;

    public $isCorporation = false;

    public $corporationId = 0;

    /**
     * Vacancy constructor.
     * @param \stdClass $vacancyItem
     * @param string $instanceIdentifier
     */
    public function __construct(\stdClass $vacancyItem, $instanceIdentifier = '')
    {
        $this->instanceIdentifier = $instanceIdentifier;

        $this->id = $vacancyItem->CompanyVacancy->id;
        $this->companyId = $vacancyItem->CompanyVacancy->company_id;
        $this->companyName = $vacancyItem->Company->name;
        $this->isCorporation = $vacancyItem->Company->master_type == 'corporation' ? true : false;
        $this->corporationId = $vacancyItem->Company->corporation_master;
        $this->salary = $vacancyItem->CompanyVacancy->salary;
        $this->level = $vacancyItem->CompanyVacancy->level;
        $this->professionId = $vacancyItem->ProfessionType->id;
        $this->professionName = $vacancyItem->ProfessionType->name;
    }

    public function getJob()
    {
        if (User::getInstance()->level < $this->level) {
            Error::exception('User professional level is low, be need ' . $this->level . ' level or higher');
            return false;
        }
        
        $result = Request::post('/vacancies/work_vacancy.json', array(
            'data' => array(
                'CompanyVacancy' => array(
                    'id' => $this->id
                )
            )
        ), $this->instanceIdentifier);
        
        return $result;
    }
}