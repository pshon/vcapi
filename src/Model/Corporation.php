<?php
namespace VCAPI\Model;

class Corporation
{

    public $id;

    public $name;

    public $vd_balance;

    public $vg_balance;

    public $compaies;

    public function __construct($id = false)
    {
        if ($id !== false) {
            $this->id = $id;
            $this->getInfo();
        }
    }

    public function getShareHolders($orderBy = false, $orderDirection = 'ASC')
    {
        $result = \VCAPI\Common\Request::get('/corporations/corporation_stockholders/' . $this->id . '.json', false);
        
        if (! empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        $plain = array();
        $names = array();
        $qty = array();
        
        foreach ($result->listStockholders as $userName => $holder) {
            $plain[] = array(
                'name' => $userName,
                'userId' => $holder->id,
                'status' => $holder->status,
                'qty' => $holder->stock_quantity,
                'procent' => $holder->stock_share
            );
            
            $names[] = $userName;
            $qty[] = $holder->stock_quantity;
        }
        
        if ($orderBy == 'name') {
            $direction = ($orderDirection == 'ASC') ? SORT_ASC : SORT_DESC;
            array_multisort($names, $direction, $plain);
        } elseif ($orderBy == 'qty') {
            $direction = ($orderDirection == 'ASC') ? SORT_ASC : SORT_DESC;
            array_multisort($qty, $direction, $plain);
        }
        
        return $plain;
    }

    public function getStorage($id = false)
    {
        $result = \VCAPI\Common\Request::get('/corporation_items/storage/' . $this->id . '.json', false);
        
        if (! empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        if ($id != false) {
            $count = 0;
            foreach ($result->storage as $item) {
                if ($item->CorporationItem->item_type_id == $id) {
                    $count = $item->CorporationItem->quantity;
                    
                    return $count;
                }
            }
        }
        
        return $result->storage;
    }

    public function getInfo()
    {
        $result = \VCAPI\Common\Request::get('/corporations/corporation_office/' . $this->id . '.json');
        
        $this->name = $result->currentCorporation->name;
        $this->vd_balance = $result->currentCorporation->vd_balance;
        $this->vg_balance = $result->currentCorporation->vg_balance;
                
        if (! empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        if (! empty($result->companies)) {
            foreach ($result->companies as $item) {
                $this->compaies[] = new \VCAPI\Model\Company($item);
            }
        }
    }

    public function moveItemToCompany($itemTypeId, $companyId, $qty)
    {
        $result = \VCAPI\Common\Request::post('/corporation_items/move_items_to_company/' . $companyId . '/' . $itemTypeId . '/' . $qty . '.json');
        
        if (! empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
            
            return false;
        }
        
        return true;
    }

    public function investVD($amount)
    {
        $result = \VCAPI\Common\Request::post('/corporations/invest_vd.json', array(
            'data' => array(
                'Corporation' => array(
                    'id' => $this->id,
                    'vd_invest' => $amount
                )
            )
        ), false);
        
        if (! empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        return true;
    }
}