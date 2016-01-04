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

    public $img;

    public $quantity;

    public $currently_producing;

    /**
     * @var array[title, needs, have, needs_for_start]
     */
    public $resources = [];

    /**
     * CompanyProduction constructor.
     *
     * @param $item
     */
    public function __construct($item)
    {
        if (is_numeric($item)) {
            $this->name = self::getNameById($item);
        } elseif (is_object($item)) {
            foreach (get_object_vars($item) as $name => $value) {
                if (property_exists($this, $name)) {
                    $this->{$name} = $value;
                }
            }
        }
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