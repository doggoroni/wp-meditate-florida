<?php

namespace AFEB\Widgets\TemplateBuilder;

use AFEB\Widgets\Dynamic\DynamicGridCarousel;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" DynamicArchivePosts Widget Class
 * 
 * @class DynamicArchivePosts
 * @version 1.3.0
 */
class DynamicArchivePosts extends DynamicGridCarousel
{
    /**
     * Get widget name
     *
     * @since 1.3.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_dynamic_archive_posts';
    }

    /**
     * Get widget title
     *
     * @since 1.3.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html__('Dynamic Archive Posts', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @since 1.3.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-dynamic-archive-posts';
    }

    /**
     * Get widget categories
     *
     * @since 1.3.0
     *
     * @return array Widget categories
     */
    public function get_categories()
    {
        return ['theme-elements-archive'];
    }

    /**
     * Get widget keywords
     *
     * @since 1.3.0
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return ['posts', 'cpt', 'archive', 'loop', 'query', 'cards', 'custom post type'];
    }

    public function register_controls()
    {
        parent::register_controls();

        $this->update_control(
            'dgrd_src',
            ['default' => 'archive',]
        );
    }

    public function render()
    {
        parent::render();
    }
}
