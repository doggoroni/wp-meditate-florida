<?php

namespace AFEB\Extensions\VisibilityControls\Conditions;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Role Class
 * 
 * @class Role
 * @version 1.5.0
 */
class Role extends Conditions
{
    public function get_name()
    {
        return 'role';
    }

    public function get_title()
    {
        return esc_html__('User Role', 'addons-for-elementor-builder');
    }

    public function get_group()
    {
        return 'user';
    }

    public function get_control_settings()
    {
        global $wp_roles;

        return [
            'label' => esc_html__('Role', 'addons-for-elementor-builder'),
            'options' => $wp_roles->get_names(),
            'default' => 'subscriber',
            'description' => esc_html__('Warning: This condition applies only to logged in visitors.', 'addons-for-elementor-builder'),
        ];
    }

    public function get_control($context, $ctrl_key, $ctrl_settings)
    {
        $context->select($ctrl_key, $ctrl_settings);
    }

    public function check($relation, $val)
    {
        $user_role = wp_get_current_user()->roles;
        return $this->compare(is_user_logged_in() && in_array($val, $user_role), true, $relation);
    }
}
