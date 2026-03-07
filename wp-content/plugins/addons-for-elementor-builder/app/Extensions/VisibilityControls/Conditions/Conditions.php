<?php

namespace AFEB\Extensions\VisibilityControls\Conditions;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Conditions (VisibilityControls) Class
 * 
 * @class Conditions
 * @version 1.5.0
 */
abstract class Conditions
{
    protected static $instances = [];
    protected $element_id;

    public static function class_name()
    {
        return get_called_class();
    }

    public static function instance()
    {
        if (empty(static::$instances[static::class_name()])) {
            static::$instances[static::class_name()] = new static();
        }

        return static::$instances[static::class_name()];
    }

    public static function is_supported()
    {
        return true;
    }

    public function get_name() {}

    public function get_title() {}

    public function get_name_control()
    {
        return false;
    }

    public function get_value_control() {}

    public function check($relation, $val) {}

    public function compare($left_val, $right_val, $relation)
    {
        switch ($relation) {
            case 'is':
                return $left_val == $right_val;
            case 'not':
                return $left_val != $right_val;
            default:
                return $left_val === $right_val;
        }
    }

    public function set_element_id($id)
    {
        $this->element_id = $id;
    }

    protected function get_element_id()
    {
        return $this->element_id;
    }
}
