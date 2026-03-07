<?php

namespace AFEB\Extensions\VisibilityControls\Conditions;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Page Class
 * 
 * @class Page
 * @version 1.5.0
 */
class Page extends Conditions
{
    public function get_name()
    {
        return 'page';
    }

    public function get_title()
    {
        return esc_html__('Page', 'addons-for-elementor-builder');
    }

    public function get_group()
    {
        return 'post';
    }

    public function get_control_settings()
    {
        return [
            'label' => esc_html__('Select Page', 'addons-for-elementor-builder'),
            'default' => 'front',
            'options' => [
                'front' => esc_html__('Front', 'addons-for-elementor-builder'),
                '404' => esc_html__('404', 'addons-for-elementor-builder'),
                'search' => esc_html__('Search', 'addons-for-elementor-builder'),
                'custom' => esc_html__('Custom Page', 'addons-for-elementor-builder'),
            ],
        ];
    }

    public function get_control($context, $ctrl_key, $ctrl_settings)
    {
        $context->select($ctrl_key, $ctrl_settings);
    }

    public function check($relation, $val, $custom_page_id = false)
    {
        if (is_single() || is_singular()) {
            return false;
        }

        if ($val === 'front') {
            return $this->compare(is_front_page(), true, $relation);
        } elseif ($val === '404') {
            return $this->compare(is_404(), true, $relation);
        } elseif ($val === 'search') {
            return $this->compare(is_search(), true, $relation);
        } elseif ('custom' === $val) {
            $page_id = '';
            $page_id = get_the_id();

            if (0 === (int) $custom_page_id) {
                $show = false;
            } else {
                $show = (int) $custom_page_id === (int) $page_id ? true : false;
            }

            return $this->compare($show, true, $relation);
        }
    }
}
