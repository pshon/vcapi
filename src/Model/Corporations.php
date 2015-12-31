<?php

namespace VCAPI\Model;

use VCAPI\Common\Collection;
use VCAPI\Common\Error;
use VCAPI\Common\Request;

class Corporations {

    public $instanceIdentifier = '';
    
    public function __construct($instanceIdentifier = '') {
        $this->instanceIdentifier = $instanceIdentifier;

        $this->getShortList();
    }

    /**
     * @return mixed
     * @throws \ErrorException
     */
    public function getShortList() {
        $result = Request::get('/corporations/corporation_list.json', $this->instanceIdentifier);
        
        if (!empty($result->error)) {
            Error::exception($result->setFlash[0]->msg);
        }
        
        return $result->corporations;
    }

    /**
     * @return Collection
     */
    public function getAll() {
        $list = new Collection();
        $items = $this->getShortList();
        
        if(empty($items)) {
            return $list;
        }
        
        foreach($items as $item) {
            $list->add(new Corporation($item->id, $this->instanceIdentifier));
        }
        
        return $list;
    }
    
  
}