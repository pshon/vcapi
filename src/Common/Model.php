<?php

namespace VCAPI\Common;

class Model {
    
    /**
     * Get plain array from current instance
     *  
     * @return array
     */
    public function toObject() {
        return $this->getObjectVars();
    }
    
    /**
     * Filling current instance from array
     * 
     * @param array $data
     */
    public function fillModel($data) {
        if($data instanceof \stdClass) {
            foreach (get_object_vars($data) as $property => $value) {
                if (property_exists(get_class($this), $property)) {
                    $this->{$property} = $value;
                }
            }
        } elseif(is_array($data)) {
            foreach ($data as $property => $value) {
                if(property_exists(get_class($this), $property)) {
                    $this->{$property} = $value;
                }
            }
        }
    }
    
    private function getObjectVars()
    {
        $result = array();
        foreach (get_class_vars(get_class($this)) as $property => $value) {
            if (!property_exists(__CLASS__, $property) && property_exists(get_class($this), $property)) {
                if ($this->{$property} instanceof Model) {
                    $result[$property] = $this->{$property}->getObjectVars();
                } else {
                    $result[$property] = $this->{$property};
                }
            }
        }
    
        return (empty($result))? null : $result;
    }
}