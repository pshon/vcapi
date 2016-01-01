<?php
namespace VCAPI\Model;

use VCAPI\Common\Model;

class City extends Model
{

    private static $cities = array(
        '3'  => 'vМосква',
        '14' => 'vКуала-Лумпур',
        '12' => 'vВаршава'
    );

    public $id;

    public $name;

    /**
     * City constructor.
     * @param null $data
     */
    public function __construct($data = null)
    {
        $this->fillModel($data);
    }

    /**
     * @return array
     */
    public function getCitiesList()
    {
        return self::$cities;
    }

    /**
     * Get city name by ID
     *
     * @param $id
     * @return string
     */
    public static function getNameById($id) {
        if (array_key_exists($id, static::$cities)) {
            return static::$cities[$id];
        }

        return '';
    }
}