<?php
namespace VCAPI\Model;

class Product
{

    public function __construct($item = false)
    {
        if ($item && $item instanceof \stdClass) {
            
            /*
             * [item_type] => stdClass Object
             * (
             * [id] => 349
             * [produce_hours] => 8
             * [class] => first_weapon_items
             * [stack] => 0
             * [type] => closeRangeWeapon
             * [min_damage] => 2.41
             * [max_damage] => 4.81
             * [critical] => 0
             * [anticritical] => 0
             * [dodge] => 0
             * [antidodge] => 0
             * [dmg_absorb] => 0
             * [hp_bonus] => 0
             * [level] => 0
             * [add_energy] => 0
             * [restore_energy] => 0
             * [min_range] => 1
             * [max_range] => 1
             * [image] => /img/items/8ba78f83b3ca05f44af6eea3f7490adc.png
             * [prestige] => 0
             * [expires] => -1
             * [name] => ����������� �������
             * [receipt_type] =>
             * [heal_health] => 0
             * [strength] => 0
             * [strength_val] => 10
             * [ammunition_id] =>
             * [bonus_damage] => 0
             * [fire_speed] => 0
             * [weapon_type] => melee
             * [need_approve] => 0
             * [receipt_id] => 0
             * [creator_id] => 0
             * [comission] => 1
             * [sold] => 1
             * [quick_slot] => 0
             * [user_creator_id] => 0
             * [special] => 0
             * [quantity_in_stack] => 1
             * [can_use] => 0
             * [description] =>
             * [category_id] => 16
             * [prod] => 48
             * [energy_bonus] => 0
             * [energy_rest_speed_bonus] => 0
             * [energy_rest_speed_expire] => 0
             * [animation_type] => beat
             * [recalculate_prod_hours] => 0
             * [subtype] =>
             * [i18n_ItemType_description_rus] =>
             * [i18n_ItemType_name_rus] => ����������� �������
             * [I18n] => stdClass Object
             * (
             * [ItemType_description] =>
             * [ItemType_name] => ����������� �������
             * )
             *
             * [ItemTypeResource] => Array
             * (
             * [0] => stdClass Object
             * (
             * [id] => 2577
             * [item_type_main_id] => 321
             * [item_type_id] => 349
             * [resource_count] => 5
             * [production_count] => 1
             * [ItemTypeMain] => stdClass Object
             * (
             * [id] => 321
             * [produce_hours] => 1
             * [class] =>
             * [stack] => 1
             * [type] => material
             * [min_damage] => 0
             * [max_damage] => 0
             * [critical] => 0
             * [anticritical] => 0
             * [dodge] => 0
             * [antidodge] => 0
             * [dmg_absorb] => 0
             * [hp_bonus] => 0
             * [level] => 1
             * [add_energy] => 0
             * [restore_energy] => 0
             * [min_range] => 0
             * [max_range] => 0
             * [image] => /img/items/4754a8d2c0f5757659715eb43c2a2bc1.png
             * [prestige] => 0
             * [expires] => -1
             * [name] => ��������
             * [receipt_type] =>
             * [heal_health] => 0
             * [strength] => 0
             * [strength_val] => 0
             * [ammunition_id] =>
             * [bonus_damage] => 0
             * [fire_speed] => 0
             * [weapon_type] =>
             * [need_approve] => 0
             * [receipt_id] => 0
             * [creator_id] => 0
             * [comission] => 1
             * [sold] => 1
             * [quick_slot] => 0
             * [user_creator_id] => 0
             * [special] => 0
             * [quantity_in_stack] => 250
             * [can_use] => 0
             * [description] =>
             * [category_id] => 1
             * [prod] => 8
             * [energy_bonus] => 0
             * [energy_rest_speed_bonus] => 0
             * [energy_rest_speed_expire] => 0
             * [animation_type] =>
             * [recalculate_prod_hours] => 0
             * [subtype] =>
             * [i18n_ItemType_description_rus] =>
             * [i18n_ItemType_name_rus] => ��������
             * )
             *
             * )
             *
             * )
             *
             * )
             *
             * [resources] => Array
             * (
             * [0] => stdClass Object
             * (
             * [title] => ��������
             * [needs] => 5
             * [have] => 572
             * )
             *
             * )
             *
             * [current_production] =>
             * [production_progress] => 3.84
             * [receipt] =>
             */
        }
    }
}