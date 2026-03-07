<?php

namespace AFEB\Modules\DynamicTags\Tags\Archive;

use AFEB\Controls\Helper as CHelper;
use AFEB\Helper;
use AFEB\Modules\DynamicTags\Tags\Archive\Module as ArchiveModule;
use Elementor\Core\DynamicTags\Tag as Base_Tag;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" ArchiveTitle Tag Class
 * 
 * @class ArchiveTitle
 * @version 1.3.0
 */
class ArchiveTitle extends Base_Tag
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
        return 'afeb-archive-title';
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
        return esc_html__('Archive Title', 'addons-for-elementor-builder');
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
        return ArchiveModule::AFEB_GROUP;
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
     * Register ArchiveTitle tag controls
     *
     * @since 1.3.0
     */
    protected function register_controls()
    {
        $controls = new CHelper($this);
        $controls->yn_switcher('include_context', [
            'label' => esc_html__('Include Context', 'addons-for-elementor-builder'),
            'default' => 'yes',
        ]);
    }

    /**
     * Check loop taxonomy
     *
     * @since 1.3.0
     *
     * @return bool
     */
    public function is_loop_taxonomy(): bool
    {
        global $wp_query;
        return $wp_query->is_loop_taxonomy ?? false;
    }

    /**
     * Render taxonomy content by key
     *
     * @since 1.3.0
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
     * Render ArchiveTitle tag output on the frontend
     *
     * @since 1.3.0
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
     * Render ArchiveTitle tag output on the frontend
     *
     * @since 1.3.0
     */
    private function render_post()
    {
        $include_context = 'yes' === $this->get_settings('include_context');
        $title = Helper::get_page_title($include_context);

        echo wp_kses_post($title);
    }

    private function render_loop_taxonomy()
    {
        $this->render_taxonomy_content_by_key('name');
    }
}
