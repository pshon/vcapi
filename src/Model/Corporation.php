<?php
namespace VCAPI\Model;

class Corporation
{

    public $id;

    public $name;

    public $vd_balance;

    public $vg_balance;

    public $companies;

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
        
        if (!empty($result->error)) {
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
        
        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        $storageCollection = new \VCAPI\Common\Collection();
        
        foreach ($result->storage as $item) {
            if ($id != false ) {
                if ( $item->CorporationItem->item_type_id == $id) {
                    $count = $item->CorporationItem->quantity;
                
                    return $count;
                }
            } else {
                $product = new \VCAPI\Model\Product($item->ItemType);
                $product->quantity = $item->CorporationItem->quantity;
                
                $storageCollection->add($product);
            }
            
        }
        
        return $storageCollection;
    }
    
    public function getInfo()
    {
        $result = \VCAPI\Common\Request::get('/corporations/corporation_office/' . $this->id . '.json');
        
        $this->name = $result->currentCorporation->name;
        $this->vd_balance = $result->currentCorporation->vd_balance;
        $this->vg_balance = $result->currentCorporation->vg_balance;
                
        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        if (!empty($result->companies)) {
            foreach ($result->companies as $item) {
                $this->companies[] = new \VCAPI\Model\Company($item);
            }
        }
    }

    public function moveItemToCompany($itemTypeId, $companyId, $qty)
    {
        $result = \VCAPI\Common\Request::post('/corporation_items/move_items_to_company/' . $companyId . '/' . $itemTypeId . '/' . $qty . '.json');
        
        if (!empty($result->error)) {
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
        
        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        return true;
    }
    
    public function sellProduction($production, $count, $price, $currency = 'vdollars') {
        $result = \VCAPI\Common\Request::post('/exchanges/add_corporation_exchange.json', array(
            'data' => array(
                'Exchange' => array(
                    'price' => floatval($price),
                    'currency' => $currency,
                    'number' =>$count,
                    'item_type_id'=> ($production instanceof \VCAPI\Model\Product)? $production->id : $production,
                    'corporation_id' => $this->id
                )
            )
        ));
        
        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        return true;
    }
    
    
}
