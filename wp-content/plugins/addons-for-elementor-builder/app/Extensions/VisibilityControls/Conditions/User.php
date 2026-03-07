<?php

namespace AFEB\Extensions\VisibilityControls\Conditions;

use AFEB\Helper;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" User Class
 * 
 * @class User
 * @version 1.5.0
 */
class User extends Conditions
{
    public function get_name()
    {
        return 'user';
    }

    public function get_title()
    {
        return esc_html__('User', 'addons-for-elementor-builder');
    }

    public function get_group()
    {
        return 'user';
    }

    public function get_control_settings()
    {
        return [
            'label'       => esc_html__('Search & Select', 'addons-for-elementor-builder'),
            'options' => Helper::get_users(),
            'default'     => '',
            'multiple'    => true,
            'label_block' => true,
            'description' => esc_html__('Works only when visitor is a logged in user. Leave blank for all users.', 'addons-for-elementor-builder'),
        ];
    }

    public function get_control($context, $ctrl_key, $ctrl_settings)
    {
        $context->select2($ctrl_key, $ctrl_settings);
    }

    public function check($relation, $val)
    {
        $show = false;

        if (!empty($val) && is_array($val)) {
            foreach ($val as $key => $value) {
                if ($value == get_current_user_id()) {
                    $show = true;
                    break;
                }
            }
        } else {
            $show = $val == get_current_user_id();
        }

        return $this->compare($show, true, $relation);
    }
}
