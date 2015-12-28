<?php
namespace VCAPI\Model;

class CompanyType
{
    private static $types = [
        26 => 'Животноводческая ферма',
        33 => 'Перерабатывающий завод',

        /** Another ids should be added ... */
    ];

    public $id;

    public $name;

    /**
     * CompanyType constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->name = self::getNameById($id);
    }

    /**
     * Get product name by ID
     *
     * @param $id
     * @return string
     */
    public static function getNameById($id) {
        if (array_key_exists($id, static::$types)) {
            return static::$types[$id];
        }

        return '';
    }
}