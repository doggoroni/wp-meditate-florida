<?php

namespace AFEB\Modules\DynamicTags\Tags\Post;

use \Elementor\Modules\DynamicTags\Module as ElModule;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Module Class
 * 
 * @class Module
 * @version 1.3.0
 */
class Module extends ElModule
{
    /**
     * TAGS GROUP
     */
    const AFEB_GROUP = 'afeb_dynamic_tags_post';

    /**
     * Retrieve the dynamic tag classes names
     *
     * @since 1.3.0
     *
     * @return array
     */
    public function get_tag_classes_names()
    {
        return apply_filters('afeb/tags/post_tags', [
            'FeaturedImage',
            'PostComments',
            'PostContent',
            'PostDate',
            'PostExcerpt',
            'PostTerms',
            'PostTime',
            'PostTitle',
            'PostURL'
        ]);
    }

    /**
     * Retrieve the dynamic tag groups
     *
     * @since 1.3.0
     *
     * @return array
     */
    public function get_groups()
    {
        return [
            self::AFEB_GROUP => ['title' => esc_html__('Post (Vertex Addons)', 'addons-for-elementor-builder')]
        ];
    }

    /**
     * Add all the available dynamic tags
     *
     * @since 1.3.0
     *
     * @param Manager $dynamic_tags
     */
    public function register_tags($dynamic_tags)
    {
        foreach ($this->get_tag_classes_names() as $tag_class):
            $tag_class = '\AFEB\Modules\DynamicTags\Tags\Post\\' . $tag_class;
            if (class_exists($tag_class)) {

                // "register_tag" method is deprecated since v3.5.0
                if (method_exists($dynamic_tags, 'register')) $dynamic_tags->register(new $tag_class());
                else $dynamic_tags->register_tag(new $tag_class());
            };
        endforeach;
    }
}
