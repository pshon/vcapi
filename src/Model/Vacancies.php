<?php
namespace VCAPI\Model;

use VCAPI\Common\Request;

class Vacancies
{
    public $instanceIdentifier = '';

    private $vacancies = array();

    private $indexes = array();

    private $companies = array();

    /**
     * Vacancies constructor.
     * @param string $instanceIdentifier
     */
    public function __construct($instanceIdentifier = '')
    {
        $this->instanceIdentifier = $instanceIdentifier;

        $this->loadVacancies();
    }

    /**
     * @param $companyId
     * @return bool
     */
    public function getCompanyName($companyId)
    {
        return (!empty($this->companies->{$companyId})) ? $this->companies->{$companyId} : false;
    }

    /**
     * @param $professionId
     * @return array|bool
     */
    public function getListByProfId($professionId)
    {
        if (empty($this->vacancies)) {
            return false;
        }
        if (empty($this->indexes['by-prof']) || empty($this->indexes['by-prof'][$professionId])) {
            return false;
        }
        
        $list = array();
        
        foreach ($this->indexes['by-prof'][$professionId] as $vacancyIndex) {
            $list[] = new Vacancy($this->vacancies[$vacancyIndex], $this->instanceIdentifier);
        }
        
        return $list;
    }

    /**
     * @param $companyId
     * @return array|bool
     */
    public function getListByCompanyId($companyId)
    {
        if (empty($this->vacancies)) {
            return false;
        }
        if (empty($this->indexes['by-company']) || empty($this->indexes['by-company'][$companyId])) {
            return false;
        }
        
        $list = array();
        
        foreach ($this->indexes['by-company'][$companyId] as $vacancyIndex) {
            $list[] = new Vacancy($this->vacancies[$vacancyIndex], $this->instanceIdentifier);
        }
        
        return $list;
    }

    /**
     * @param $corporationId
     * @return array|bool
     */
    public function getListByCorpId($corporationId)
    {
        if (empty($this->vacancies)) {
            return false;
        }
        if (empty($this->indexes['by-corp']) || empty($this->indexes['by-corp'][$corporationId])) {
            return false;
        }
        
        $list = array();
        
        foreach ($this->indexes['by-corp'][$corporationId] as $vacancyIndex) {
            $list[] = new Vacancy($this->vacancies[$vacancyIndex], $this->instanceIdentifier);
        }
        
        return $list;
    }

    public function loadVacancies()
    {
        $result = Request::get('/vacancies/index.json', $this->instanceIdentifier);
        $this->companies = $result->companies;
        if (empty($result->vacancies)) {
            $this->vacancies = array();
            $this->indexes = array();
        } else {
            $this->vacancies = $result->vacancies;
            $this->rebuildIndexes();
        }
    }

    private function rebuildIndexes()
    {
        $this->indexes = array(
            'by-prof' => array(),
            'by-company' => array(),
            'by-corp' => array()
        );
        
        if (empty($this->vacancies)) {
            return;
        }
        
        foreach ($this->vacancies as $index => $vacancy) {
            if (empty($this->indexes['by-prof'][$vacancy->CompanyVacancy->profession_type_id])) {
                $this->indexes['by-prof'][$vacancy->CompanyVacancy->profession_type_id] = array();
            }
            
            $this->indexes['by-prof'][$vacancy->CompanyVacancy->profession_type_id][] = $index;
            
            if (empty($this->indexes['by-company'][$vacancy->Company->id])) {
                $this->indexes['by-company'][$vacancy->Company->id] = array();
            }
            
            $this->indexes['by-company'][$vacancy->Company->id][] = $index;
            
            if ($vacancy->Company->master_type == 'corporation') {
                if (empty($this->indexes['by-corp'][$vacancy->Company->corporation_master])) {
                    $this->indexes['by-corp'][$vacancy->Company->corporation_master] = array();
                }
                
                $this->indexes['by-corp'][$vacancy->Company->corporation_master][] = $index;
            }
        }
    }
}