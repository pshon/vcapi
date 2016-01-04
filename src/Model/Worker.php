<?php
namespace VCAPI\Model;

use VCAPI\Common\Error;
use VCAPI\Common\Request;

class Worker
{
    public $instanceIdentifier = '';

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

    /**
     * Worker constructor.
     * @param bool $item
     * @param string $instanceIdentifier
     */
    public function __construct($item = false, $instanceIdentifier = '')
    {
        $this->instanceIdentifier = $instanceIdentifier;

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

    /**
     * @param $levelId
     * @return bool
     */
    public function getTypeIdByLevel($levelId)
    {
        return (isset($this->foreignLevels[$levelId])) ? $this->foreignLevels[$levelId] : false;
    }

    /**
     * @param $companyId
     * @param $level
     * @param $qty
     * @return bool
     * @throws \ErrorException
     */
    public function hire($companyId, $level, $qty)
    {
        $typeId = $this->getTypeIdByLevel($level);
        $qty = intval($qty);
        if ($typeId) {
            Error::exception('Bad worker level.');
            return false;
        }
        
        $result = Request::post(
            '/company_foreign_workers/add/' . $companyId . '/' . $typeId . '/' . $qty . '.json',
            array(),
            $this->instanceIdentifier
        );
        
        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        return true;
    }
}