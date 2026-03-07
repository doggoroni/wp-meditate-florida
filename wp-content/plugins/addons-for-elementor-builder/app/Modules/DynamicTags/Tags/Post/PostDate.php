<?php

namespace AFEB\Modules\DynamicTags\Tags\Post;

use AFEB\Controls\Helper;
use AFEB\Modules\DynamicTags\Tags\Post\Module as PostModule;
use Elementor\Core\DynamicTags\Tag as Base_Tag;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PostDate Tag Class
 * 
 * @class PostDate
 * @version 1.3.0
 */
class PostDate extends Base_Tag
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
        return 'afeb-post-date';
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
        return esc_html__('Post Date', 'addons-for-elementor-builder');
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
     * Register PostDate tag controls
     *
     * @since 1.3.0
     */
    protected function register_controls()
    {
        $controls = new Helper($this);
        $controls->select('post_date_format', [
            'label' => esc_html__('Format', 'addons-for-elementor-builder'),
            'options' => [
                'F j, Y' => gmdate('F j, Y'),
                'Y-m-d' => gmdate('Y-m-d'),
                'm/d/Y' => gmdate('m/d/Y'),
                'd/m/Y' => gmdate('d/m/Y')
            ],
            'default' => 'F j, Y',
        ]);
    }

    /**
     * Render PostDate tag output on the frontend
     *
     * @since 1.3.0
     */
    public function render()
    {
        $post_date_format = $this->get_settings('post_date_format');
        $value = get_the_date(str_replace('+', ' ', $post_date_format));

        echo wp_kses_post($value);
    }
}
