<?php

namespace AFEB\Extensions\VisibilityControls\Conditions;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Authentication Class
 * 
 * @class Authentication
 * @version 1.5.0
 */
class Authentication extends Conditions
{
    public function get_name()
    {
        return 'authentication';
    }

    public function get_title()
    {
        return esc_html__('Login Status', 'addons-for-elementor-builder');
    }

    public function get_group()
    {
        return 'user';
    }

    public function get_control_settings()
    {
        return [
            'default' => 'authenticated',
            'label_block' => true,
            'options' => ['authenticated' => esc_html__('Logged in', 'addons-for-elementor-builder'),],
        ];
    }

    public function get_control($context, $ctrl_key, $ctrl_settings)
    {
        $context->select($ctrl_key, $ctrl_settings);
    }

    public function check($relation, $val)
    {
        return $this->compare(is_user_logged_in(), true, $relation);
    }
}
