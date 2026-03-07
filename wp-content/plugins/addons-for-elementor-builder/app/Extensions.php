<?php

namespace AFEB;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Extensions Class
 * 
 * @class Extensions
 * @version 1.0.3
 */
class Extensions extends Base
{
    /**
     * "Vertex Addons for Elementor" Extensions URL
     */
    const AFEB_EXTENSIONS_URL = self::AFEB_URL . '/extensions/';

    /**
     * "Vertex Addons for Elementor" PFX
     */
    const PFX = 'PV_';

    /**
     * Initialize "Vertex Addons for Elementor" Extensions
     * 
     * @since 1.0.3
     */
    public function init()
    {
        $this->actions();
    }

    /**
     * Extensions Class Actions
     * 
     * @since 1.4.0
     */
    public function actions()
    {
        add_action('init', function () {
            $this->register_extensions();
        });
    }

    /**
     * All extensions list
     * 
     * @since 1.0.3
     * 
     * @return array
     */
    public function extensions()
    {
        return apply_filters('afeb/extensions/list', [
            'Wrapper_Link' => [
                'title' => esc_html__('Wrapper Link', 'addons-for-elementor-builder'),
                'status' => 1
            ],
            'Sticky' => [
                'title' => esc_html__('Sticky', 'addons-for-elementor-builder'),
                'status' => 1
            ],
            'Ripple_Effects' => [
                'title' => esc_html__('Ripple Effects', 'addons-for-elementor-builder'),
                'status' => 1
            ],
            'Visibility_Controls' => [
                'title' => esc_html__('Visibility Controls', 'addons-for-elementor-builder'),
                'status' => 1,
                'nsp' => 1
            ],
            'Preloader' => [
                'title' => esc_html__('Preloader', 'addons-for-elementor-builder'),
                'status' => 1
            ],
            'Custom_Cssjs' => [
                'title' => esc_html__('Custom CSS/JS', 'addons-for-elementor-builder'),
                'status' => 1
            ],
            'Going_Up' => [
                'title' => esc_html__('Scroll to Top', 'addons-for-elementor-builder'),
                'status' => 1
            ]
        ]);
    }

    /**
     * Register all extensions
     * 
     * @since 1.0.3
     * 
     */
    public function register_extensions($name_space = '')
    {
        $extensions = array_replace_recursive($this->extensions(), get_option('afeb-extensions-status', []));
        foreach ($extensions as $extension_key => $extension) {
            if (isset($extension['status']) && $extension['status'] == 1) {

                if (isset($extension['nsp']) && trim($extension['nsp'])) {
                    if ($extension['nsp'] == 1) {
                        $extension_key = str_replace('_', '', $extension_key) . '\\' . $extension_key;
                    } else {
                        $extension_key = trim($extension['nsp']) . '\\' . $extension_key;
                    }
                } elseif (trim($name_space)) {
                    $extension_key = $name_space . '\\' . $extension_key;
                }

                $pro_init = '\AFEB\PRO\Extensions\\' . str_replace('_', '', $extension_key);

                if (class_exists($pro_init)) {
                    new $pro_init();
                } else {
                    $lite_init = '\AFEB\Extensions\\' . str_replace('_', '', $extension_key);

                    if (class_exists($lite_init)) {
                        new $lite_init();
                    }
                }
            }
        }
        do_action('afeb/extensions/after_register_extensions');
    }
}
