<?php
namespace VCAPI\Model;

class Product extends \VCAPI\Common\Model
{

    public $id;
    
    public $produce_hours;
    
    public $class;
    
    public $stack;
    
    public $type;
    
    public $min_damage;
    
    public $max_damage;
    
    public $critical;
    
    public $anticritical;
    
    public $dodge;
    
    public $antidodge;
    
    public $dmg_absorb;
    
    public $hp_bonus;
    
    public $level;
    
    public $add_energy;
    
    public $restore_energy;
    
    public $min_range;
    
    public $max_range;
    
    public $image;
    
    public $prestige;
    
    public $expires;
    
    public $name;
    
    public $receipt_type;
    
    public $heal_health;
    
    public $strength;
    
    public $strength_val;
    
    public $ammunition_id;
    
    public $bonus_damage;
    
    public $fire_speed;
    
    public $weapon_type;
    
    public $need_approve;
    
    public $receipt_id;
    
    public $creator_id;
    
    public $comission;
    
    public $sold;
    
    public $quick_slot;
    
    public $user_creator_id;
    
    public $special;
    
    public $quantity_in_stack;
    
    public $can_use;
    
    public $description;
    
    public $category_id;
    
    public $prod;
    
    public $energy_bonus;
    
    public $energy_rest_speed_bonus;
    
    public $energy_rest_speed_expire;
    
    public $animation_type;
    
    public $recalculate_prod_hours;
    
    public $subtype;
    
    public $quantity;
    
    
    public function __construct($item = false)
    {
        if ($item instanceof \stdClass) {
            $this->fillModel($item);
        }
    }
}