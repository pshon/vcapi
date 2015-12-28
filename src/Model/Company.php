<?php
namespace VCAPI\Model;

class Company
{

    public $id;

    public $name;

    /** @var \VCAPI\Model\CompanyType CompanyType */
    public $company_type;

    /** @var \VCAPI\Model\City City */
    public $city;

    public $type;

    public $master_type;

    public $corporation_master;

    public $company_level;

    public $products_sold;

    public $take_funds;

    public $produced_items;

    public $company_income;

    public $storage_level;

    public $production_status;

    public $production_status_title;

    /** @var bool */
    public $currently_producing;

    public $private;

    public $manager_id;

    public $user_id;

    public $vd_balance;

    public $vg_balance;

    /** @var \VCAPI\Model\CompanyProduction CompanyProduction */
    public $current_production;

    public $workersAllCnt = 0;

    public $workersForeignCnt = 0;

    public $workplaces = 0;

    public $workers;

    public $vacancy = array();

    /**
     * Company constructor.
     * @param $item
     * @throws \ErrorException
     */
    public function __construct($item)
    {
        if (!($item instanceof \stdClass)) {
            throw new \ErrorException('Incoming data not found');
        }

        foreach (get_object_vars($item) as $name => $value) {
            if (property_exists($this, $name)) {
                $this->{$name} = $value;
            }
        }

        if (property_exists($item, 'city_id')) {
            $this->city = new \VCAPI\Model\City($item->city_id);
        }

        if (property_exists($item, 'current_production')) {
            $this->current_production = new \VCAPI\Model\CompanyProduction($item->current_production);
        }

        if (property_exists($item, 'company_type_id')) {
            $this->company_type = new \VCAPI\Model\CompanyType($item->company_type_id);
        }
    }

    /**
     * @return array|bool
     */
    public function getProductionList()
    {
        $result = \VCAPI\Common\Request::get('/companies/production_list/' . $this->id . '.json', false);
        $list = [];

        foreach ($result->company_productions as $item) {
            $list[] = new \VCAPI\Model\Product($item);
        }

        return $list;
    }

    public function setProductionId($productionId)
    {
        if ($this->current_production->id == $productionId) {
            return false;
        }

        $result = \VCAPI\Common\Request::post('/companies/set_production.json', array(
            'data' => array(
                'Company' => array(
                    'id'                 => $this->id,
                    'current_production' => $productionId
                )
            )
        ), false);

        return true;
    }

    public function setManagerId($managerId)
    {
        if ($this->private) {
            $result = \VCAPI\Common\Request::post('/companies/set_manager.json', array(
                'data' => array(
                    'Company' => array(
                        'id' => $this->id,
                        'manager_id' => $managerId
                    )
                )
            ), false);
        } else {
            $result = \VCAPI\Common\Request::post('/corporation_companies/set_manager.json', array(
                'data' => array(
                    'Company' => array(
                        'id' => $this->id,
                        'manager_id' => $managerId
                    )
                )
            ), false);
        }

        return true;
    }

    /**
     * @param $id
     * @return Company
     * @throws \ErrorException
     */
    public static function loadById($id)
    {
        $result = \VCAPI\Common\Request::get('/companies/info/' . $id . '.json', false);

        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
        }

        $company = new self($result->company);
        $company->getWorkers();

        return $company;
    }

    public function getStorage($id = false)
    {
        $result = \VCAPI\Common\Request::get('/company_items/storage/' . $this->id . '.json', false);

        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
            return false;
        }

        if ($id != false) {
            $count = 0;
            foreach ($result->storage as $item) {
                if ($item->CompanyItem->item_type_id == $id) {
                    $count = $item->CompanyItem->quantity;

                    return $count;
                }
            }
        }

        return $result->storage;
    }

    public function moveItemToCorporation($itemId, $qty)
    {
        $result = \VCAPI\Common\Request::post('/company_items/move_items_to_corporation/' . $this->id . '/' . $itemId . '/' . $qty . '.json');

        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
            return false;
        }

        return true;
    }

    public function getWorkers()
    {
        $result = \VCAPI\Common\Request::get('/company_workers/list_workers/' . $this->id . '.json', false);

        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
        }

        $this->workplaces = $result->currentCompany->Company->company_level * 5;
        $this->workersAllCnt = count($result->currentCompany->CompanyWorker) + count($result->currentCompany->UserForeignWorker);
        $this->workersForeignCnt = count($result->currentCompany->UserForeignWorker);
        $this->workers = array();
        $this->vacancy = array_shift($result->currentCompany->CompanyVacancy);

        if (!empty($result->currentCompany->CompanyWorker)) {
            foreach ($result->currentCompany->CompanyWorker as $item) {
                $this->workers[] = new \VCAPI\Model\Worker($item);
            }
        }

        if (!empty($result->currentCompany->UserForeignWorker)) {
            foreach ($result->currentCompany->UserForeignWorker as $item) {
                $this->workers[] = new \VCAPI\Model\Worker($item);
            }
        }
    }

    public function addForeignWorker($level, $qty)
    {
        $worker = new \VCAPI\Model\Worker();
        return $worker->hire($this->id, $level, $qty);
    }

    public function deleteWorker($worker)
    {
        if ($worker instanceof \VCAPI\Model\Worker) {
            if ($worker->isForeign) {
                $this->deleteForeignWorker($worker->id);
            } else {
                $this->deleteUserWorker($worker->id);
            }
        }
    }

    public function deleteForeignWorker($workerId)
    {
        $result = \VCAPI\Common\Request::post('/company_foreign_workers/delete/' . $this->id . '/' . $workerId . '/1.json');

        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
        }

        return $result;
    }

    public function deleteUserWorker($workerId)
    {
        $result = \VCAPI\Common\Request::post('/company_workers/delete_worker/' . $workerId . '/1.json');

        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
        }

        return $result;
    }

    public function takeMoney($amount)
    {
        if ($this->private) {
            $result = \VCAPI\Common\Request::post('/companies/take_funds.json', array(
                'data' => array(
                    'Company' => array(
                        'id' => 3785,
                        'amount_take' => 100
                    )
                )
            ), false);
        } else {
            $result = \VCAPI\Common\Request::post('/corporation_companies/take_funds.json', array(
                'data' => array(
                    'Company' => array(
                        'id' => 3785,
                        'amount_take' => 100
                    )
                )
            ), false);
        }

        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
        }

        return true;
    }

    public function addMoney($amount)
    {
        if ($this->private) {
            $result = \VCAPI\Common\Request::post('/corporation_companies/add_funds.json', array(
                'data' => array(
                    'Company' => array(
                        'id' => $this->id,
                        'amount_add' => $amount
                    )
                )
            ), false);
        } else {
            $result = \VCAPI\Common\Request::post('/corporation_companies/add_funds.json', array(
                'data' => array(
                    'Company' => array(
                        'id' => $this->id,
                        'amount_add' => $amount
                    )
                )
            ), false);
        }

        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
        }

        return true;
    }

    public function saveVacantion()
    {
        $query = array(
            'data' => array(
                'CompanyVacancy' => array(
                    'company_id' => $this->id,
                    'salary' => $this->vacancy->salary,
                    'level' => $this->vacancy->level,
                    'is_hiring' => $this->vacancy->is_hiring
                )
            )
        );

        $result = \VCAPI\Common\Request::post('/vacancies/save_vacancy.json', $query, false);

        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
        }

        return true;
    }

    public function reopenVacantion()
    {
        $this->vacancy->is_hiring = 1;
        $this->saveVacantion();
    }
}