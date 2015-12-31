<?php
namespace VCAPI\Model;

use VCAPI\Common\Collection;
use VCAPI\Common\Error;
use VCAPI\Common\Request;

class Corporation
{
    public $instanceIdentifier = '';

    public $id;

    public $name;

    public $vd_balance;

    public $vg_balance;

    public $companies;

    /**
     * Corporation constructor.
     * @param bool $id
     * @param string $instanceIdentifier
     */
    public function __construct($id = false, $instanceIdentifier = '')
    {
        $this->instanceIdentifier = $instanceIdentifier;

        if ($id !== false) {
            $this->id = $id;
            $this->getInfo();
        }
    }

    /**
     * @param bool $orderBy
     * @param string $orderDirection
     * @return array|bool
     * @throws \ErrorException
     */
    public function getShareHolders($orderBy = false, $orderDirection = 'ASC')
    {
        $result = Request::get('/corporations/corporation_stockholders/' . $this->id . '.json', $this->instanceIdentifier);
        
        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
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

    /**
     * @param bool $id
     * @return bool|\VCAPI\Common\Collection
     * @throws \ErrorException
     */
    public function getStorage($id = false)
    {
        $result = Request::get('/corporation_items/storage/' . $this->id . '.json', $this->instanceIdentifier);
        
        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        $storageCollection = new Collection();
        
        foreach ($result->storage as $item) {
            if ($id != false ) {
                if ( $item->CorporationItem->item_type_id == $id) {
                    $count = $item->CorporationItem->quantity;
                
                    return $count;
                }
            } else {
                $product = new Product($item->ItemType);
                $product->quantity = $item->CorporationItem->quantity;
                
                $storageCollection->add($product);
            }
            
        }
        
        return $storageCollection;
    }

    /**
     * @return bool
     * @throws \ErrorException
     */
    public function getInfo()
    {
        $result = Request::get('/corporations/corporation_office/' . $this->id . '.json', $this->instanceIdentifier);
        
        $this->name = $result->currentCorporation->name;
        $this->vd_balance = $result->currentCorporation->vd_balance;
        $this->vg_balance = $result->currentCorporation->vg_balance;
                
        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        if (!empty($result->companies)) {
            foreach ($result->companies as $item) {
                $this->companies[] = new Company($item);
            }
        }
    }

    /**
     * @param $itemTypeId
     * @param $companyId
     * @param $qty
     * @return bool
     * @throws \ErrorException
     */
    public function moveItemToCompany($itemTypeId, $companyId, $qty)
    {
        $result = Request::post(
            '/corporation_items/move_items_to_company/' . $companyId . '/' . $itemTypeId . '/' . $qty . '.json',
            array(),
            $this->instanceIdentifier
        );
        
        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        return true;
    }

    /**
     * @param $amount
     * @return bool
     * @throws \ErrorException
     */
    public function investVD($amount)
    {
        $result = Request::post('/corporations/invest_vd.json', array(
            'data' => array(
                'Corporation' => array(
                    'id' => $this->id,
                    'vd_invest' => $amount
                )
            )
        ), $this->instanceIdentifier);
        
        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        return true;
    }

    /**
     * @param $production
     * @param $count
     * @param $price
     * @param string $currency
     * @return bool
     * @throws \ErrorException
     */
    public function sellProduction($production, $count, $price, $currency = 'vdollars') {
        $result = Request::post('/exchanges/add_corporation_exchange.json', array(
            'data' => array(
                'Exchange' => array(
                    'price' => floatval($price),
                    'currency' => $currency,
                    'number' =>$count,
                    'item_type_id'=> ($production instanceof Product)? $production->id : $production,
                    'corporation_id' => $this->id
                )
            )
        ), $this->instanceIdentifier);
        
        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        return true;
    }
    
    
}
