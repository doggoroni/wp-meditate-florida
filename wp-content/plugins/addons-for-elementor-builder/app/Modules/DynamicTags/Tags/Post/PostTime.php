<?php

namespace AFEB\Modules\DynamicTags\Tags\Post;

use AFEB\Controls\Helper;
use AFEB\Modules\DynamicTags\Tags\Post\Module as PostModule;
use Elementor\Core\DynamicTags\Tag as Base_Tag;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PostTime Tag Class
 * 
 * @class PostTime
 * @version 1.3.0
 */
class PostTime extends Base_Tag
{
    /**
     * Get tag name
     *
     * @since 1.3.0
     *
     * @return string Tag name
     */
    public function get_name()
    {
        return 'afeb-post-time';
    }

    /**
     * Get tag title
     *
     * @since 1.3.0
     *
     * @return string Tag title
     */
    public function get_title()
    {
        return esc_html__('Post Time', 'addons-for-elementor-builder');
    }

    /**
     * Get tag group
     *
     * @since 1.3.0
     *
     * @return array Tag group
     */
    public function get_group()
    {
        return PostModule::AFEB_GROUP;
    }

    /**
     * Get tag categories
     *
     * @since 1.3.0
     *
     * @return array Tag categories
     */
    public function get_categories()
    {
        return [Module::TEXT_CATEGORY];
    }

    /**
     * Register PostTime tag controls
     *
     * @since 1.3.0
     */
    protected function register_controls()
    {
        $controls = new Helper($this);
        $controls->select('post_time_format', [
            'label' => esc_html__('Format', 'addons-for-elementor-builder'),
            'options' => [
                'g:i a' => gmdate('g:i a'),
                'g:i A' => gmdate('g:i A'),
                'H:i' => gmdate('H:i')
            ],
            'default' => 'g:i a',
        ]);
    }

    /**
     * Render PostTime tag output on the frontend
     *
     * @since 1.3.0
     */
    public function render()
    {
        $post_time_format = $this->get_settings('post_time_format');
        $post_time_format = $post_time_format != 'default' ? $post_time_format : '';
        $value = get_the_date(str_replace('+', ' ', $post_time_format));

        echo wp_kses_post($value);
    }
}
