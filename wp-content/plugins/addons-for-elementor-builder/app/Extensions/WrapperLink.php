<?php

namespace AFEB\Extensions;

use AFEB\Assets;
use AFEB\Base;
use AFEB\Controls\CHelper;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" WrapperLink Extension Class
 * 
 * @class WrapperLink
 * @version 1.0.3
 */
class WrapperLink
{
    /**
     * @var CHelper
     */
    private $CHelper;

    /**
     * WrapperLink Constructor
     * 
     * @since 1.0.3
     */
    public function __construct()
    {
        $this->CHelper = new CHelper();
        $this->actions();
    }

    /**
     * WrapperLink Class Actions
     * 
     * @since 1.0.3
     */
    public function actions()
    {
        add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_controls']);
        add_action('elementor/element/column/section_advanced/after_section_end', [$this, 'register_controls']);
        add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'register_controls']);
        add_action('elementor/element/container/section_layout/after_section_end', [$this, 'register_controls']);
        add_action('elementor/frontend/before_render', [$this, 'before_render'], 1);
        add_action('elementor/frontend/before_render', [$this, 'enqueue_wrapper_link']);
        add_action('elementor/frontend/container/after_render', [$this, 'enqueue_wrapper_link']);
    }

    /**
     * Register WrapperLink extension controls
     *
     * @since 1.0.3
     * 
     * @param object $obj
     */
    public function register_controls($obj)
    {
        $this->CHelper->add_adv_sctn($obj, 'afeb-ext-as2', esc_html__('Wrapper Link', 'addons-for-elementor-builder'), function ($obj) {
            $this->CHelper->yn_swtchr($obj, 'afeb_wrpr_lnk', __('Enable Wrapper Link', 'addons-for-elementor-builder'), 0);
            $this->CHelper->url($obj, 'afeb_wrpr_lnk_url', 0, Base::AFEB_URL, ['afeb_wrpr_lnk' => 'yes'], 0, 1);
        });
    }

    /**
     * Apply changes to the element before rendering
     *
     * @since 1.0.3
     * 
     * @param object $obj
     */
    public function before_render($obj)
    {
        $wrpr_lnk_url = $obj->get_settings_for_display('afeb_wrpr_lnk_url');
        if ($obj->get_settings_for_display('afeb_wrpr_lnk') == 'yes' && trim($wrpr_lnk_url['url'])) {
            $obj->add_render_attribute(
                '_wrapper',
                [
                    'data-afeb-wrapper-link' => wp_json_encode($wrpr_lnk_url),
                    'class' => 'afeb-wrapper-link'
                ]
            );
        }
    }

    /**
     * Add required scripts and styles
     *
     * @since 1.0.3
     * 
     * @param object $obj
     */
    public function enqueue_wrapper_link($obj)
    {
        $wrpr_lnk_url = $obj->get_settings_for_display('afeb_wrpr_lnk_url');
        if ($obj->get_settings_for_display('afeb_wrpr_lnk') == 'yes' && trim($wrpr_lnk_url['url'])) {
            $assets = new Assets();
            $assets->wrapper_link_style();
            $assets->wrapper_link_script();
        }
    }
}
