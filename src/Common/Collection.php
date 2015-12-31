<?php

namespace VCAPI\Common;

class Collection {
    
    private $collection = array();
    
    
    /**
     * Add object to collection
     * 
     * @param object $item
     */
    public function add($item) {
        $this->collection[] = $item;
    }
    
    /**
     * Find element from collection by key and value
     * 
     * @param string $key - search index
     * @param string $value - search value
     * @param [boolean $onlyIndex] - this flag can return only indixes of records found
     * @return NULL|Collection <NULL, \VCAPI\Common\Collection>
     */
    
    public function find($key, $value, $onlyIndex = false) {
        if(empty($this->collection)) return null;
        $return = new Collection();
        
        foreach($this->collection as $index => $item) {            
            if(is_array($item)) {
                if(!isset($item[$key])) continue;
                if($item[$key] == $value) {
                    if($onlyIndex) {
                        $return->add($index);
                    } else {
                        $return->add($item);
                    }
                } 
            } elseif(is_object($item)) {
                if(!property_exists($item, $key)) continue;
                if($item->{$key} == $value) {
                    if($onlyIndex) {
                        $return->add($index);
                    } else {
                        $return->add($item);
                    }
                }
            }
        }
        
        return ($return->count())? $return : null;  
    }
    
    
    /**
     * Sorting collection by key
     * 
     * @param string $key - element key
     * @param string $direction - sorting direction (SQL style)
     * @return Collection <\VCAPI\Common\Collection>
     */
    public function sort($key, $direction = 'ASC') {
        $keys = array();
        foreach($this->collection as $item) {
           if(is_array($item)) {
               if(!isset($item[$key])) continue;
               $keys[] = $item[$key];
           } elseif(is_object($item)) {
               if(!property_exists($item, $key)) continue;
               $keys[] = $item->{$key};
           } 
        }
        
        if(empty($keys)) {
            Error::exception('No valid data to sort');
            return $this->collection;
        }
        
        $sorted = $this->collection;
        $direction = ($direction == 'DESC')? SORT_DESC : SORT_ASC;
        
        array_multisort($keys, $direction, $sorted);
        
        return $sorted;
    }
    
    /**
     * Get collection items count
     * @return int
     */
    public function count() {
        return count($this->collection);
    }
    
    /**
     * Get collection element by index
     * @param integer $index
     * @return Null|Item <NULL, Mixed>
     */
    public function item($index) {
        return (isset($this->collection[$index]))? $this->collection[$index] : null;
    }
    
    /**
     * Get plain array from collection
     * @return array <object>
     */
    public function toObject() {
        return $this->collection;
    }
}