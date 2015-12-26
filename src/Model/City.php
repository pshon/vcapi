<?php
namespace VCAPI\Model;

class City
{

    private $list = array(
        '3' => 'vÌîñêâà',
        '14' => 'vÊóàëà-Ëóìïóğ',
        '12' => 'vÂàğøàâà'
    );

    public $id;

    public $name;

    public function __construct($cityId)
    {
        $this->id = $cityId;
        if (! empty($this->list[$cityId])) {
            $this->name = $this->list[$this->id];
        }
    }

    public function getCitiesList()
    {
        return $this->list;
    }
}