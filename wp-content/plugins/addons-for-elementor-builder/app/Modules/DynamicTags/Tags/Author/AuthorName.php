<?php

namespace AFEB\Modules\DynamicTags\Tags\Author;

use AFEB\Modules\DynamicTags\Tags\Author\Module as AuthorModule;
use Elementor\Core\DynamicTags\Data_Tag;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" AuthorName Tag Class
 * 
 * @class AuthorName
 * @version 1.3.0
 */
class AuthorName extends Data_Tag
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
        return 'afeb-author-name';
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
        return esc_html__('Author Name', 'addons-for-elementor-builder');
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
        return AuthorModule::AFEB_GROUP;
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
        return wp_kses_post(get_the_author());
    }
}
