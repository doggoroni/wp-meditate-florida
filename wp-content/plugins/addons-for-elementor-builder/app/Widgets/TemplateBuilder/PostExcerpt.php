<?php

namespace AFEB\Widgets\TemplateBuilder;

use AFEB\Modules\DynamicTags\Tags\Post\PostExcerpt as PostExcerptTag;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PostExcerpt Widget Class
 * 
 * @class PostExcerpt
 * @version 1.3.0
 */
class PostExcerpt extends RenderContent
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
        return 'afeb_post_excerpt';
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
        return esc_html__('Post Excerpt', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-post-excerpt';
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
        return ['post', 'excerpt', 'description'];
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
     * Register PostExcerpt widget controls
     *
     * @since 1.3.0
     */
    public function register_controls()
    {
        parent::controls($this);

        $post_excerpt = new PostExcerptTag();
        $dynamic_tag_name = $post_excerpt->get_name();

        $this->update_control(
            'text_render',
            [
                'dynamic' => ['default' => Plugin::instance()->dynamic_tags->tag_data_to_tag_text(null, $dynamic_tag_name),],
            ],
            ['recursive' => true,]
        );
    }

    /**
     * Register PostExcerpt widget controls
     *
     * @since 1.3.0
     */
    public function render()
    {
        parent::render();
    }
}
