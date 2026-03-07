<?php

namespace AFEB\Modules\DynamicTags\Tags\Post;

use AFEB\Controls\Helper;
use AFEB\Modules\DynamicTags\Tags\Post\Module as PostModule;
use Elementor\Core\DynamicTags\Tag as Base_Tag;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PostTerms Tag Class
 * 
 * @class PostTerms
 * @version 1.3.0
 */
class PostTerms extends Base_Tag
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
        return 'afeb-post-terms';
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
        return esc_html__('Post Terms', 'addons-for-elementor-builder');
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
     * Register PostTerms tag controls
     *
     * @since 1.3.0
     */
    protected function register_controls()
    {
        $controls = new Helper($this);
        $post_types = get_post_types([
            'public'   => true,
            '_builtin' => true,
        ]);
        $taxonomies = get_object_taxonomies(array_keys($post_types), 'objects');
        $options = [];

        foreach ($taxonomies as $taxonomy => $object)
            $options[$taxonomy] = $object->label;

        $controls->select('taxonomy', [
            'label' => esc_html__('Taxonomy', 'addons-for-elementor-builder'),
            'options' => $options,
            'default' => 'post_tag',
        ]);

        $controls->text('separator', [
            'label' => esc_html__('Separator', 'addons-for-elementor-builder'),
            'placeholder' => ',',
            'ai' => ['active' => false,],
            'dynamic' => ['active' => false,],
        ]);

        $controls->yn_switcher('link', [
            'label' => esc_html__('Link', 'addons-for-elementor-builder'),
            'default' => 'yes',
        ]);
    }

    /**
     * Render PostTitle tag output on the frontend
     *
     * @since 1.3.0
     */
    public function render()
    {
        $settings = $this->get_settings();
        $separator = trim($settings['separator']) ? $settings['separator'] : ', ';

        if ($settings['link'] == 'yes') {
            $value = get_the_term_list(get_the_ID(), $settings['taxonomy'], '', $separator);
        } else {
            $terms = get_the_terms(get_the_ID(), $settings['taxonomy']);

            if (is_wp_error($terms) || empty($terms)) return '';

            $term_names = [];

            foreach ($terms as $term)
                $term_names[] = '<span>' . $term->name . '</span>';

            $value = implode($separator, $term_names);
        }

        echo wp_kses_post($value);
    }
}
