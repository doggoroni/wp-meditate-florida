<?php

namespace AFEB\Modules\DynamicTags\Tags\Post;

use AFEB\Helper;
use AFEB\Modules\DynamicTags\Tags\Post\Module;
use AFEB\Modules\DynamicTags\Tags\Post\Module as PostModule;
use Elementor\Core\DynamicTags\Tag as Base_Tag;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PostContent Tag Class
 * 
 * @class PostContent
 * @version 1.3.0
 */
class PostContent extends Base_Tag
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
        return 'afeb-post-content';
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
        return esc_html__('Post Content', 'addons-for-elementor-builder');
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
     * Render PostContent tag output on the frontend
     *
     * @since 1.3.0
     */
    public function render()
    {
        $post_id = get_the_ID();

        if (post_password_required($post_id)) {
            echo get_the_password_form($post_id);
            return;
        }

        echo Helper::get_page_as_element($post_id);
    }
}
