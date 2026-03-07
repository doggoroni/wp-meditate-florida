<?php

namespace AFEB\Modules\DynamicTags\Tags\Site;

use AFEB\Controls\Helper as CHelper;
use AFEB\Modules\DynamicTags\Tags\Site\Module as SiteModule;
use Elementor\Core\DynamicTags\Data_Tag;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" InternalURL Tag Class
 * 
 * @class InternalURL
 * @version 1.4.0
 */
class InternalURL extends Data_Tag
{
    /**
     * Get tag name
     *
     * @since 1.4.0
     *
     * @return string Tag name
     */
    public function get_name()
    {
        return 'afeb-internal-url';
    }

    /**
     * Get tag title
     *
     * @since 1.4.0
     *
     * @return string Tag title
     */
    public function get_title()
    {
        return esc_html__('Internal URL', 'addons-for-elementor-builder');
    }

    /**
     * Get tag group
     *
     * @since 1.4.0
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
     * @since 1.4.0
     *
     * @return array Tag categories
     */
    public function get_categories()
    {
        return [Module::URL_CATEGORY];
    }

    /**
     * Register InternalURL tag controls
     *
     * @since 1.4.0
     */
    protected function register_controls()
    {
        $controls = new CHelper($this);
        $controls->select('type', [
            'label' => esc_html__('Type', 'addons-for-elementor-builder'),
            'options' => [
                'post' => esc_html__('Post', 'addons-for-elementor-builder'),
                'page' => esc_html__('Page', 'addons-for-elementor-builder'),
                // 'taxonomy' => esc_html__('Taxonomy', 'addons-for-elementor-builder'),
            ],
        ]);

        $controls->dynamic_select('post_id', [
            'label' => esc_html__('Select Post', 'addons-for-elementor-builder'),
            'options' => 'get_posts_by_type',
            'query_slug' => 'post',
            'default' => '',
            'condition' => ['type' => 'post',]
        ]);

        $controls->dynamic_select('page_id', [
            'label' => esc_html__('Select Page', 'addons-for-elementor-builder'),
            'options' => 'get_posts_by_type',
            'query_slug' => 'page',
            'default' => '',
            'condition' => ['type' => 'page',]
        ]);

        $controls->select('taxonomy', [
            'label' => esc_html__('Select Taxonomy', 'addons-for-elementor-builder'),
            'options' => [
                'cat' => esc_html__('Category', 'addons-for-elementor-builder'),
                'tag' => esc_html__('Tag', 'addons-for-elementor-builder'),
            ],
            'condition' => ['type' => 'taxonomy',]
        ]);

        $controls->dynamic_select('category_id', [
            'label' => esc_html__('Select Category', 'addons-for-elementor-builder'),
            'options' => 'get_terms_by_tax',
            'query_slug' => 'category',
            'default' => '',
            'condition' => ['taxonomy' => 'cat',]
        ]);

        $controls->dynamic_select('tag_id', [
            'label' => esc_html__('Select Category', 'addons-for-elementor-builder'),
            'options' => 'get_terms_by_tax',
            'query_slug' => 'post_tag',
            'default' => '',
            'condition' => ['taxonomy' => 'tag',]
        ]);
    }

    /**
     * Get tag value
     *
     * @since 1.4.0
     * 
     * @param array $options
     *
     * @return string Tag value
     */
    public function get_value(array $options = [])
    {
        $settings = $this->get_settings();

        $type = $settings['type'];
        $url = '';

        if ($type === 'post' && !empty($settings['post_id'])) {
            $url = get_permalink((int) $settings['post_id']);
        } elseif ($type === 'page' && !empty($settings['page_id'])) {
            $url = get_permalink((int) $settings['page_id']);
        } elseif ($type === 'taxonomy') {
            if ($settings['taxonomy'] === 'cat' && !empty($settings['category_id'])) {
                $url = get_category_link((int) $settings['category_id']);
            } else if ($settings['taxonomy'] === 'tag' && !empty($settings['tag_id'])) {
                $url = get_tag_link((int) $settings['tag_id']);
            }
        }

        if (!is_wp_error($url)) {
            return $url;
        }

        return '';
    }
}
