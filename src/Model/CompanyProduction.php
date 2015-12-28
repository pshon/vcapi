<?php
namespace VCAPI\Model;

class CompanyProduction
{
    private static $productions = [
        /** Перерабатывающий завод */
        337 => 'Кожа',

        /** Животноводческая ферма */
        25  => 'Молоко',
        590 => 'Мясо',
        422 => 'Шкура',

        /** Another ids should be added ... */
    ];

    public $id;

    public $name;

    /**
     * CompanyProduction constructor.
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
        if (array_key_exists($id, static::$productions)) {
            return static::$productions[$id];
        }

        return '';
    }
}