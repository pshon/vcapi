<?php
namespace VCAPI\Model;

class Worker
{

    public $isForeign = false;

    public $id;

    public $userId;

    public $userName;

    public $salary;

    public $level;
    
    // level => workerTypeId
    public $foreignLevels = array(
        5 => 26,
        10 => 27,
        15 => 28,
        20 => 29,
        25 => 30
    );

    public function __construct($item = false)
    {
        if ($item !== false && $item instanceof \stdClass) {
            $this->id = $item->id;
            $this->level = $item->profession_level;
            $this->salary = $item->salary;
            
            if (!empty($item->foreign_worker_type_id)) {
                $this->isForeign = true;
            } else {
                $this->userId = $item->user_id;
                $this->userName = $item->username;
            }
        }
    }

    public function getTypeIdByLevel($levelId)
    {
        return (isset($this->foreignLevels[$levelId])) ? $this->foreignLevels[$levelId] : false;
    }

    public function hire($companyId, $level, $qty)
    {
        $typeId = $this->getTypeIdByLevel($level);
        $qty = intval($qty);
        if ($typeId) {
            \VCAPI\Common\Error::exception('Bad worker level.');
            return false;
        }
        
        $result = \VCAPI\Common\Request::post('/company_foreign_workers/add/' . $companyId . '/' . $typeId . '/' . $qty . '.json');
        
        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        return true;
    }
}