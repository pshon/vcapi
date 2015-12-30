<?php

namespace VCAPI\Model;

class Corporations {
    
    public function __construct() {
        $this->getShortList();
    }
    
    public function getShortList() {
        $result = \VCAPI\Common\Request::get('/corporations/corporation_list.json');
        
        if (!empty($result->error)) {
            \VCAPI\Common\Error::exception($result->setFlash[0]->msg);
            return false;
        }
        
        return $result->corporations;
    }
    
    public function getAll() {
        $list = new \VCAPI\Common\Collection();
        $items = $this->getShortList();
        
        if(empty($items)) {
            return $list;
        }
        
        foreach($items as $item) {
            $list->add(new \VCAPI\Model\Corporation($item->id));
        }
        
        return $list;
    }
    
  
}