<?php

namespace AFEB\Widgets\TemplateBuilder;

use AFEB\Modules\DynamicTags\Tags\Post\PostComments as PostCommentsTag;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PostComments Widget Class
 * 
 * @class PostComments
 * @version 1.3.0
 */
class PostComments extends RenderContent
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
        return 'afeb_post_comments';
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
        return esc_html__('Post Comments', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-post-comments';
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
        return ['comments', 'heading', 'post'];
    }

    protected function is_url(): bool
    {
        return false;
    }

    protected function is_html_tag(): bool
    {
        return false;
    }

    protected function html_tag(): string
    {
        return 'span';
    }

    protected function is_text_for_global_settings(): bool
    {
        return true;
    }

    protected function register_controls()
    {
        parent::controls($this);

        $post_comments = new PostCommentsTag();
        $dynamic_tag_name = $post_comments->get_name();

        $this->update_control(
            'text_render',
            [
                'dynamic' => ['default' => Plugin::instance()->dynamic_tags->tag_data_to_tag_text(null, $dynamic_tag_name),],
            ],
            ['recursive' => true,]
        );
    }

    public function render()
    {
        parent::render();
    }
}
