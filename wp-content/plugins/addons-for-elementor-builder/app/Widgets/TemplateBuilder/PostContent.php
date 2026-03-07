<?php

namespace AFEB\Widgets\TemplateBuilder;

use AFEB\Modules\DynamicTags\Tags\Post\PostContent as PostContentTag;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PostContent Widget Class
 * 
 * @class PostContent
 * @version 1.3.0
 */
class PostContent extends RenderContent
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
        return 'afeb_post_content';
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
        return esc_html__('Post Content', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-post-content';
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
        return ['recommended', 'theme-elements-single'];
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
        return ['content', 'post'];
    }

    protected function is_url(): bool
    {
        return false;
    }

    protected function is_html_tag(): bool
    {
        return false;
    }

    protected function is_hover_controls(): bool
    {
        return false;
    }

    protected function is_text_for_global_settings(): bool
    {
        return true;
    }

    /**
     * Register PostContent widget controls
     *
     * @since 1.3.0
     */
    public function register_controls()
    {
        parent::controls($this);

        $post_content = new PostContentTag();
        $dynamic_tag_name = $post_content->get_name();

        $this->update_control(
            'text_render',
            [
                'dynamic' => ['default' => Plugin::instance()->dynamic_tags->tag_data_to_tag_text(null, $dynamic_tag_name),],
            ],
            ['recursive' => true,]
        );
    }

    /**
     * Render PostContent output on the frontend
     *
     * @since 1.3.0
     */
    public function render()
    {
        parent::render();
    }
}
