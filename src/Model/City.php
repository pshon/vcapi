<?php
namespace VCAPI\Model;

class City
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
     * @param $id
     */
    public function __construct($id)
    {
        $this->id = $id;

        if (array_key_exists($id, self::$cities)) {
            $this->name = self::$cities[$id];
        }
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