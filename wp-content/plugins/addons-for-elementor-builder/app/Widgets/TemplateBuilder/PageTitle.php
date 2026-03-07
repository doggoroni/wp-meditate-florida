<?php

namespace AFEB\Widgets\TemplateBuilder;

use AFEB\Modules\DynamicTags\Tags\Site\PageTitle as PageTitleTag;
use Elementor\Plugin;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" PageTitle Widget Class
 * 
 * @class PageTitle
 * @version 1.3.0
 */
class PageTitle extends RenderContent
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
        return 'afeb_page_title';
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
        return esc_html__('Page Title', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-page-title';
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
        return ['theme-elements'];
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
        return ['title', 'heading', 'page'];
    }

    protected function register_controls()
    {
        parent::controls($this);

        $page_title = new PageTitleTag();
        $dynamic_tag_name = $page_title->get_name();

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
