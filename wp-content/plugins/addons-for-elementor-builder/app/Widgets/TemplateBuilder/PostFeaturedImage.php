<?php

namespace AFEB\Widgets\TemplateBuilder;

use AFEB\Modules\DynamicTags\Tags\Post\FeaturedImage;
use Elementor\Plugin;
use Elementor\Widget_Image;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PostFeaturedImage Widget Class
 * 
 * @class PostFeaturedImage
 * @version 1.3.0
 */
class PostFeaturedImage extends Widget_Image
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
        return 'afeb_post_featured_image';
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
        return esc_html__('Featured Image', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-post-featured-image';
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
        return ['image', 'featured', 'thumbnail'];
    }

    protected function register_controls()
    {
        parent::register_controls();

        $this->update_control(
            'section_title',
            ['label' => esc_html__('Featured Image', 'addons-for-elementor-builder'),]
        );

        $featured_image = new FeaturedImage();
        $dynamic_tag_name = $featured_image->get_name();

        $this->update_control(
            'image',
            [
                'dynamic' => ['default' => Plugin::$instance->dynamic_tags->tag_data_to_tag_text(null, $dynamic_tag_name),],
            ],
            ['recursive' => true,]
        );
    }
}
