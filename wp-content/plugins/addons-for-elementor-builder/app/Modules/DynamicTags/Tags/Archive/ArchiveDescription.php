<?php

namespace AFEB\Modules\DynamicTags\Tags\Archive;

use AFEB\Modules\DynamicTags\Tags\Archive\Module as ArchiveModule;
use Elementor\Core\DynamicTags\Tag as Base_Tag;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" ArchiveDescription Tag Class
 * 
 * @class ArchiveDescription
 * @version 1.5.0
 */
class ArchiveDescription extends Base_Tag
{
    /**
     * Get tag name
     *
     * @since 1.5.0
     *
     * @return string Tag name
     */
    public function get_name()
    {
        return 'afeb-archive-description';
    }

    /**
     * Get tag title
     *
     * @since 1.5.0
     *
     * @return string Tag title
     */
    public function get_title()
    {
        return esc_html__('Archive Description', 'addons-for-elementor-builder');
    }

    /**
     * Get tag group
     *
     * @since 1.5.0
     *
     * @return array Tag group
     */
    public function get_group()
    {
        return ArchiveModule::AFEB_GROUP;
    }

    /**
     * Get tag categories
     *
     * @since 1.5.0
     *
     * @return array Tag categories
     */
    public function get_categories()
    {
        return [Module::TEXT_CATEGORY];
    }

    /**
     * Check loop taxonomy
     *
     * @since 1.5.0
     *
     * @return bool
     */
    private function is_loop_taxonomy(): bool
    {
        global $wp_query;
        return $wp_query->is_loop_taxonomy ?? false;
    }

    /**
     * Render taxonomy content by key
     *
     * @since 1.5.0
     */
    private function render_taxonomy_content_by_key(string $key = 'name'): void
    {
        global $wp_query;

        if (!isset($wp_query->loop_term) || !is_object($wp_query->loop_term)) {
            return;
        }

        $content = '';

        if (isset($wp_query->loop_term->$key)) {
            $content = $wp_query->loop_term->$key;
        }

        echo wp_kses_post($content);
    }

    /**
     * Render ArchiveDescription tag output on the frontend
     *
     * @since 1.5.0
     */
    public function render()
    {
        if ($this->is_loop_taxonomy()) {
            $this->render_loop_taxonomy();
            return;
        }

        $this->render_post();
    }

    /**
     * Render ArchiveDescription tag output on the frontend
     *
     * @since 1.5.0
     */
    private function render_post()
    {
        echo wp_kses_post(get_the_archive_description());
    }

    private function render_loop_taxonomy()
    {
        $this->render_taxonomy_content_by_key('description');
    }
}
