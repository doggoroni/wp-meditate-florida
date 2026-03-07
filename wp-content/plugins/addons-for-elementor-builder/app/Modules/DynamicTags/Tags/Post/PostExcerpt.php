<?php

namespace AFEB\Modules\DynamicTags\Tags\Post;

use AFEB\Controls\CHelper;
use AFEB\Helper;
use AFEB\Modules\DynamicTags\Tags\Post\Module;
use AFEB\Modules\DynamicTags\Tags\Post\Module as PostModule;
use Elementor\Core\DynamicTags\Tag as Base_Tag;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PostExcerpt Tag Class
 * 
 * @class PostExcerpt
 * @version 1.3.0
 */
class PostExcerpt extends Base_Tag
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
        return 'afeb-post-excerpt';
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
        return esc_html__('Post Excerpt', 'addons-for-elementor-builder');
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
     * Register PostExcerpt tag controls
     *
     * @since 1.3.0
     */
    protected function register_controls()
    {
        $chelper = new CHelper();
        $chelper->num($this, 'pe_lnt', esc_html__('Excerpt Length', 'addons-for-elementor-builder'), 0);
    }

    /**
     * Render PostExcerpt tag output on the frontend.
     *
     * @since 1.3.0
     */
    public function render()
    {
        $max_length = $this->get_settings('pe_lnt');
        $value = get_the_excerpt();
        $value = Helper::limit_words($value, $max_length);

        echo wp_kses_post($value);
    }
}
