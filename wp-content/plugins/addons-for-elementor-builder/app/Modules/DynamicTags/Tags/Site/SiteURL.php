<?php

namespace AFEB\Modules\DynamicTags\Tags\Site;

use AFEB\Modules\DynamicTags\Tags\Site\Module as SiteModule;
use Elementor\Core\DynamicTags\Data_Tag;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" SiteURL Tag Class
 * 
 * @class SiteURL
 * @version 1.3.0
 */
class SiteURL extends Data_Tag
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
        return 'afeb-site-url';
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
        return esc_html__('Site URL', 'addons-for-elementor-builder');
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
        return [Module::URL_CATEGORY];
    }

    /**
     * Get tag value
     *
     * @since 1.3.0
     * 
     * @param array $options
     *
     * @return string Tag value
     */
    public function get_value(array $options = [])
    {
        return home_url();
    }
}
