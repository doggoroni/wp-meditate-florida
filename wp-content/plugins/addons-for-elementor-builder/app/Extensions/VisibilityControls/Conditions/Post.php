<?php

namespace AFEB\Extensions\VisibilityControls\Conditions;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Post Class
 * 
 * @class Post
 * @version 1.5.0
 */
class Post extends Conditions
{
    public function get_name()
    {
        return 'post';
    }

    public function get_title()
    {
        return esc_html__('Post', 'addons-for-elementor-builder');
    }

    public function get_group()
    {
        return 'post';
    }

    public function get_control_settings()
    {
        return [
            'label'       => esc_html__('Search & Select', 'addons-for-elementor-builder'),
            'options' => 'get_posts_by_type',
            'query_slug' => 'post',
            'default'     => '',
            'multiple'    => true,
            'description' => esc_html__('Leave blank or select all for any post type.', 'addons-for-elementor-builder'),
        ];
    }

    public function get_control($context, $ctrl_key, $ctrl_settings)
    {
        $context->dynamic_select($ctrl_key, $ctrl_settings);
    }

    public function check($relation, $val)
    {
        if (!is_single() || !is_singular()) {
            return false;
        }

        $show = false;

        if (is_array($val) && ! empty($val)) {
            foreach ($val as $key => $value) {
                if (is_single($value) || is_singular($value)) {
                    $show = true;
                    break;
                }
            }
        } else {
            $show = is_single($val) || is_singular($val);
        }

        return $this->compare($show, true, $relation);
    }
}
