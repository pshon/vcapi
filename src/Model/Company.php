<?php
namespace VCAPI\Model;

class Company
{

    public $id;

    public $name;

    public $productionId;

    public $private;

    public $managerId;

    public $vd_balance;

    public $vg_balance;

    public $production;

    public $productionName;

    public $productionStatus;

    public $workersAllCnt = 0;

    public $workersForeignCnt = 0;

    public $workplaces = 0;

    public $workers;

    public $vacancy = array();

    public function __construct($item = false)
    {
        if ($item !== false && $item instanceof \stdClass) {
            $this->id = $item->id;
            $this->name = $item->name;
            $this->managerId = $item->manager_id;
            $this->productionName = $item->current_production->name;
            $this->productionStatus = $item->current_production->currently_producing;
            $this->production = $item->current_production;
        }
    }

    public function getCompanyProductionList()
    {
        if (! $this->id)
            return false;
        
        $result = \VCAPI\Common\Request::get('/companies/production_list/' . $this->id . '.json', false);
        $list = array();
        
        foreach ($result->company_productions as $item) {
            $list[] = new \VCAPI\Model\Product($item);
        }
        
        return $list;
    }

    public function setProductionId($productionId)
    {
        if ($this->productionId == $productionId)
            return false;
        
        $result = \VCAPI\Common\Request::post('/companies/set_production.json', array(
            'data' => array(
                'Company' => array(
                    'id' => $this->id,
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

    public function getInfo()
    {
        $result = \VCAPI\Common\Request::get('/companies/info/' . $this->id . '.json', false);
        
        if (! empty($result->error)) {
            throw new \ErrorException($result->setFlash[0]->msg);
            return false;
        }
        
        $this->name = $result->company->name;
        $this->managerId = $result->company->manager_id;
        $this->vd_balance = $result->company->vd_balance;
        $this->vg_balance = $result->company->vg_balance;
        $this->getWorkers();
    }

    public function getStorage($id = false)
    {
        $result = \VCAPI\Common\Request::get('/company_items/storage/' . $this->id . '.json', false);
        
        if (! empty($result->error)) {
            throw new \ErrorException($result->setFlash[0]->msg);
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
        
        if (! empty($result->error)) {
            throw new \ErrorException($result->setFlash[0]->msg);
            return false;
        }
        
        return true;
    }

    public function getWorkers()
    {
        $result = \VCAPI\Common\Request::get('/company_workers/list_workers/' . $this->id . '.json', false);
        
        if (! empty($result->error)) {
            throw new \ErrorException($result->setFlash[0]->msg);
            return false;
        }
        
        $this->workplaces = $result->currentCompany->company_level * 5;
        $this->workersAllCnt = count($result->currentCompany->CompanyWorker) + count($result->currentCompany->UserForeignWorker);
        $this->workersForeignCnt = count($result->currentCompany->UserForeignWorker);
        $this->workers = array();
        $this->vacancy = array_shift($result->currentCompany->CompanyVacancy);
        
        if (! empty($result->currentCompany->CompanyWorker)) {
            foreach ($result->currentCompany->CompanyWorker as $item) {
                $this->workers[] = new \VCAPI\Model\Worker($item);
            }
        }
        
        if (! empty($result->currentCompany->UserForeignWorker)) {
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
        
        if (! empty($result->error)) {
            throw new \ErrorException($result->setFlash[0]->msg);
            return false;
        }
        
        return $result;
    }

    public function deleteUserWorker($workerId)
    {
        $result = \VCAPI\Common\Request::post('/company_workers/delete_worker/' . $workerId . '/1.json');
        
        if (! empty($result->error)) {
            throw new \ErrorException($result->setFlash[0]->msg);
            return false;
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
        
        if (! empty($result->error)) {
            throw new \ErrorException($result->setFlash[0]->msg);
            return false;
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
        
        if (! empty($result->error)) {
            throw new \ErrorException($result->setFlash[0]->msg);
            return false;
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
        
        if (! empty($result->error)) {
            throw new \ErrorException($result->setFlash[0]->msg);
            return false;
        }
        
        return true;
    }

    public function reopenVacantion()
    {
        $this->vacancy->is_hiring = 1;
        $this->saveVacantion();
    }
}