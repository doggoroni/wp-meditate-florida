<?php

namespace AFEB\Widgets\TemplateBuilder;

use AFEB\Modules\DynamicTags\Tags\Archive\ArchiveTitle as ArchiveTitleTag;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" ArchiveTitle Widget Class
 * 
 * @class ArchiveTitle
 * @version 1.3.0
 */
class ArchiveTitle extends RenderContent
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
        return 'afeb_archive_title';
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
        return esc_html__('Archive Title', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-archive-title';
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
        return ['theme-elements-archive', 'recommended'];
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
        return ['title', 'heading', 'archive'];
    }

    /**
     * Register ArchiveTitle widget controls
     *
     * @since 1.3.0
     */
    protected function register_controls()
    {
        parent::controls($this);

        $archive_title = new ArchiveTitleTag();
        $dynamic_tag_name = $archive_title->get_name();

        $this->update_control(
            'text_render',
            [
                'dynamic' => ['default' => Plugin::instance()->dynamic_tags->tag_data_to_tag_text(null, $dynamic_tag_name),],
            ],
            ['recursive' => true,]
        );
    }

    /**
     * Render ArchiveTitle output on the frontend
     *
     * @since 1.3.0
     */
    public function render()
    {
        parent::render();
    }
}
