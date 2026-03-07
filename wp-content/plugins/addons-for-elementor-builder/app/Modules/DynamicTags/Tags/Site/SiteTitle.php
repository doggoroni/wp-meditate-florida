<?php

namespace AFEB\Modules\DynamicTags\Tags\Site;

use AFEB\Modules\DynamicTags\Tags\Site\Module as SiteModule;
use Elementor\Core\DynamicTags\Tag as Base_Tag;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" SiteTitle Tag Class
 * 
 * @class SiteTitle
 * @version 1.3.0
 */
class SiteTitle extends Base_Tag
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
        return 'afeb-site-title';
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
        return esc_html__('Site Title', 'addons-for-elementor-builder');
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
        return SiteModule::AFEB_GROUP;
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
     * Render SiteTitle tag output on the frontend
     *
     * @since 1.3.0
     */
    public function render()
    {
        echo wp_kses_post(get_bloginfo());
    }
}
