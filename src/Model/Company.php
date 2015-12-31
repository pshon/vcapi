<?php
namespace VCAPI\Model;

use VCAPI\Common\Error;
use VCAPI\Common\Request;

class Company
{
    public $instanceIdentifier = '';

    public $id;

    public $name;

    /** @var \VCAPI\Model\CompanyType CompanyType */
    public $company_type;

    /** @var string Name of city */
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
     * @param string $instanceIdentifier
     * @throws \ErrorException
     */
    public function __construct($item, $instanceIdentifier = '')
    {
        if (!($item instanceof \stdClass)) {
            Error::exception('Incoming data not found');
            return false;
        }

        $this->instanceIdentifier = $instanceIdentifier;

        foreach (get_object_vars($item) as $name => $value) {
            if (property_exists($this, $name)) {
                $this->{$name} = $value;
            }
        }

        if (property_exists($item, 'city_id')) {
            $this->city = City::getNameById($item->city_id);
        }

        if (property_exists($item, 'current_production')) {
            $this->current_production = new CompanyProduction($item->current_production);
        }

        if (property_exists($item, 'company_type_id')) {
            $this->company_type = new CompanyType($item->company_type_id);
        }
    }

    /**
     * @return array|bool
     */
    public function getProductionList()
    {
        $result = Request::get('/companies/production_list/' . $this->id . '.json');
        $list = [];

        foreach ($result->company_productions as $item) {
            $list[] = new Product($item);
        }

        return $list;
    }

    /**
     * @param $productionId
     * @return bool
     */
    public function setProductionId($productionId)
    {
        if ($this->current_production->id == $productionId) {
            return false;
        }

        $result = Request::post('/companies/set_production.json', array(
            'data' => array(
                'Company' => array(
                    'id'                 => $this->id,
                    'current_production' => $productionId
                )
            )
        ));

        return true;
    }

    /**
     * @param $managerId
     * @return bool
     */
    public function setManagerId($managerId)
    {
        if ($this->private) {
            $result = Request::post('/companies/set_manager.json', array(
                'data' => array(
                    'Company' => array(
                        'id' => $this->id,
                        'manager_id' => $managerId
                    )
                )
            ));
        } else {
            $result = Request::post('/corporation_companies/set_manager.json', array(
                'data' => array(
                    'Company' => array(
                        'id' => $this->id,
                        'manager_id' => $managerId
                    )
                )
            ));
        }

        return true;
    }

    /**
     * @param $id
     * @param string $instanceIdentifier
     * @return Company
     * @throws \ErrorException
     */
    public static function loadById($id, $instanceIdentifier = '')
    {
        $result = Request::get('/companies/info/' . $id . '.json', false);

        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
        }

        $company = new self($result->company, $instanceIdentifier);
        $company->getWorkers();

        return $company;
    }

    /**
     * @param bool $id
     * @return bool|int
     * @throws \ErrorException
     */
    public function getStorage($id = false)
    {
        $result = Request::get('/company_items/storage/' . $this->id . '.json');

        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
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

    /**
     * @param $itemId
     * @param $qty
     * @return bool
     * @throws \ErrorException
     */
    public function moveItemToCorporation($itemId, $qty)
    {
        $result = Request::post('/company_items/move_items_to_corporation/' . $this->id . '/' . $itemId . '/' . $qty . '.json');

        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
            return false;
        }

        return true;
    }

    /**
     * @throws \ErrorException
     */
    public function getWorkers()
    {
        $result = Request::get('/company_workers/list_workers/' . $this->id . '.json');

        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
        }

        $this->workplaces = $result->currentCompany->Company->company_level * 5;
        $this->workersAllCnt = count($result->currentCompany->CompanyWorker) + count($result->currentCompany->UserForeignWorker);
        $this->workersForeignCnt = count($result->currentCompany->UserForeignWorker);
        $this->workers = array();
        $this->vacancy = array_shift($result->currentCompany->CompanyVacancy);

        if (!empty($result->currentCompany->CompanyWorker)) {
            foreach ($result->currentCompany->CompanyWorker as $item) {
                $this->workers[] = new Worker($item, $this->instanceIdentifier);
            }
        }

        if (!empty($result->currentCompany->UserForeignWorker)) {
            foreach ($result->currentCompany->UserForeignWorker as $item) {
                $this->workers[] = new Worker($item, $this->instanceIdentifier);
            }
        }
    }

    /**
     * @param $level
     * @param $qty
     * @return bool
     */
    public function addForeignWorker($level, $qty)
    {
        $worker = new Worker($this->instanceIdentifier);
        return $worker->hire($this->id, $level, $qty);
    }

    /**
     * @param $worker
     */
    public function deleteWorker($worker)
    {
        if ($worker instanceof Worker) {
            if ($worker->isForeign) {
                $this->deleteForeignWorker($worker->id);
            } else {
                $this->deleteUserWorker($worker->id);
            }
        }
    }

    /**
     * @param $workerId
     * @return mixed
     * @throws \ErrorException
     */
    public function deleteForeignWorker($workerId)
    {
        $result = Request::post('/company_foreign_workers/delete/' . $this->id . '/' . $workerId . '/1.json');

        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
        }

        return $result;
    }

    /**
     * @param $workerId
     * @return mixed
     * @throws \ErrorException
     */
    public function deleteUserWorker($workerId)
    {
        $result = Request::post('/company_workers/delete_worker/' . $workerId . '/1.json');

        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
        }

        return $result;
    }

    /**
     * @param $amount
     * @return bool
     * @throws \ErrorException
     */
    public function takeMoney($amount)
    {
        if ($this->private) {
            $result = Request::post('/companies/take_funds.json', array(
                'data' => array(
                    'Company' => array(
                        'id' => 3785,
                        'amount_take' => 100
                    )
                )
            ), false);
        } else {
            $result = Request::post('/corporation_companies/take_funds.json', array(
                'data' => array(
                    'Company' => array(
                        'id' => 3785,
                        'amount_take' => 100
                    )
                )
            ), false);
        }

        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
        }

        return true;
    }

    /**
     * @param $amount
     * @return bool
     * @throws \ErrorException
     */
    public function addMoney($amount)
    {
        if ($this->private) {
            $result = Request::post('/corporation_companies/add_funds.json', array(
                'data' => array(
                    'Company' => array(
                        'id' => $this->id,
                        'amount_add' => $amount
                    )
                )
            ));
        } else {
            $result = Request::post('/corporation_companies/add_funds.json', array(
                'data' => array(
                    'Company' => array(
                        'id' => $this->id,
                        'amount_add' => $amount
                    )
                )
            ));
        }

        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
        }

        return true;
    }

    /**
     * @return bool
     * @throws \ErrorException
     */
    public function saveVacancy()
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

        $result = Request::post('/vacancies/save_vacancy.json', $query);

        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
        }

        return true;
    }

    public function reopenVacancy()
    {
        $this->vacancy->is_hiring = 1;
        $this->saveVacantion();
    }
}
