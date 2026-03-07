<?php

namespace AFEB\Modules\DynamicTags\Tags\Post;

use AFEB\Modules\DynamicTags\Tags\Post\Module as PostModule;
use Elementor\Core\DynamicTags\Data_Tag;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PostURL Tag Class
 * 
 * @class PostURL
 * @version 1.3.0
 */
class PostURL extends Data_Tag
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
        return 'afeb-post-url';
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
        return esc_html__('Post URL', 'addons-for-elementor-builder');
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
        return get_permalink();
    }
}
