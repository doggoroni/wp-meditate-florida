<?php

namespace AFEB;

use Elementor\Plugin;

if (!defined('ABSPATH'))
{
    exit;
}

/**
 * "Vertex Addons for Elementor" Assets Class
 *
 * @class Assets
 * @version 1.0.0
 */
class Assets extends Base
{
    /**
     * Initialize "Vertex Addons for Elementor" Assets
     *
     * @since 1.0.0
     */
    public function init()
    {
        $this->actions();
    }

    /**
     * Assets Class Actions
     *
     * @since 1.0.0
     */
    public function actions()
    {
        if (class_exists('WooCommerce'))
        {
            add_action('wp_enqueue_scripts', function ()
            {
                wp_enqueue_script('wc-add-to-cart');

                $this->woo_product_image_style();
                $this->woo_product_image_script();
                $this->product_image();
                $this->add_to_cart();
                $this->woo_product_tags_style();
            });
        }

        if (is_admin())
        {
            add_action('admin_enqueue_scripts', function ()
            {
                $this->common_style();
                $this->backend_style();
                $this->elementor_element_manager_styles();
                $this->backend_script();
            });
        }
        else
        {
            add_action('wp_enqueue_scripts', function ()
            {
                $this->common_style();
                $this->fontawesome_style();
            });
        }

        add_action('wp_enqueue_scripts', [$this, 'woo_add_to_cart_script']);
        add_action('elementor/editor/after_enqueue_styles', [$this, 'elementor_editor_styles']);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'editor_script']);
    }

    /**
     * Script enqueue for iProduct image widget
     *
     * @since 1.0.0
     */
    public function product_image()
    {
        // CSS
        wp_enqueue_style(
            'afeb-woo-product-image',
            $this->assets_url('css/widgets/afeb-woo-product-image.css'),
            [],
            AFEB_VERSION,
            time()
        );

        // Swiper
        $this->register_swiper_assets();
        wp_enqueue_style('swiper');
        wp_enqueue_script('swiper');

        // Lightbox
        $this->register_glightbox_assets();
        wp_enqueue_style('glightbox');
        wp_enqueue_script('glightbox');

        // Widget JS
        wp_enqueue_script(
            'afeb-woo-product-images',
            $this->assets_url('js/afeb-woo-product-images.js'),
            ['jquery', 'swiper', 'glightbox'],
            AFEB_VERSION,
            true
        );
    }

    /**
     * Styles of Backend
     *
     * @since 1.0.0
     */
    public function backend_style()
    {
        $handle = 'afeb-backend-style';
        wp_register_style($handle, $this->assets_url('css/backend.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style for Woo Product Image widget
     */
    public function woo_product_image_style()
    {
        $handle = 'afeb-woo-product-image';
        wp_register_style(
            $handle,
            $this->assets_url('css/widgets/afeb-woo-product-image.css'),
            [],
            AFEB_VERSION
        );
    }

    /**
     * Script for Woo Product Image widget
     */
    public function woo_product_image_script()
    {
        $handle = 'afeb-woo-product-images';
        $this->register_swiper_assets();
        $this->register_glightbox_assets();
        wp_register_script(
            $handle,
            $this->assets_url('js/afeb-woo-product-images.js'),
            ['jquery', 'swiper', 'glightbox'],
            AFEB_VERSION,
            true
        );
    }

    public function woo_product_tags_style()
    {
        $handle = 'afeb-woo-product-tags-style';
        wp_register_style(
            $handle,
            $this->assets_url('css/widgets/woo-product-tags.css'), [],
            AFEB_VERSION
        );
        wp_enqueue_style($handle);
    }

    /**
     * Scripts of Backend
     *
     * @since 1.0.0
     */
    public function backend_script()
    {
        $handle = 'afeb-backend-script';
        wp_register_script($handle, $this->assets_url('js/backend.min.js'), ['jquery'], AFEB_VERSION, true);
        $this->localize_script($handle);
        wp_enqueue_script($handle);
    }

    /**
     *  enqueue assets for add to cart
     *
     * @since 1.0.0
     *
     */

    public function add_to_cart()
    {
        wp_enqueue_script(
            'afeb-woo-atc-redirect',
            $this->assets_url('js/afeb-woo-atc-redirect.js'),
            ['jquery', 'wc-add-to-cart'],
            AFEB_VERSION,
            true
        );
        // Localize redirect URLs from WooCommerce
        wp_localize_script('afeb-woo-atc-redirect', 'afeb_atc_params', [
            'cart_url' => function_exists('wc_get_cart_url') ? wc_get_cart_url() : '',
            'checkout_url' => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '',
        ]);

        add_action('elementor/frontend/after_register_styles', function ()
        {
            wp_register_style(
                'afeb-woo-add-to-cart',
                $this->assets_url(
                    'css/widgets/afeb-woo-add-to-cart.css'),
                [],
                AFEB_VERSION
            );
        });

        add_action('wp_enqueue_scripts', function ()
        {
            wp_enqueue_style('afeb-woo-add-to-cart');
        }, 99);
    }

    /**
     * Style of WordPress Color Picker Module
     *
     * @since 1.3.0
     */
    public function wp_color_picker_style()
    {
        wp_enqueue_style('wp-color-picker');
    }

    /**
     * Script of WordPress Color Picker Module
     *
     * @since 1.3.0
     */
    public function wp_color_picker_script()
    {
        wp_enqueue_script('wp-color-picker');
    }

    /**
     * Style of CommentsForm widget
     *
     * @since 1.3.0
     */
    public function comments_form_style()
    {
        $handle = 'afeb-comments-form-style';
        wp_register_style($handle, $this->assets_url('css/widgets/comments-form.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Common style for Frontend and Backend
     *
     * @since 1.0.0
     */
    public function common_style()
    {
        $handle = 'afeb-common-style';
        wp_register_style($handle, $this->assets_url('css/common.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of CreativeButton widget
     *
     * @since 1.5.0
     */
    public function creative_button_style()
    {
        $handle = 'afeb-creative-button-style';
        wp_register_style($handle, $this->assets_url('css/widgets/creative-button.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Normalize style for Backend
     *
     * @since 1.0.0
     */
    public function normalize_style()
    {
        $handle = 'afeb-normalize-style';
        wp_register_style($handle, $this->assets_url('css/normalize.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of FlipBox widget
     *
     * @since 1.5.0
     */
    public function flip_box_style()
    {
        $handle = 'afeb-flip-box-style';
        wp_register_style($handle, $this->assets_url('css/widgets/flip-box.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of Fontawesome package
     *
     * @since 1.0.0
     */
    public function fontawesome_style()
    {
        $handle = 'afeb-fontawesome-style';
        wp_register_style($handle, $this->assets_url('packages/font-awesome/fontawesome.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of Form Builder widget
     *
     * @since 1.4.0
     */
    public function form_builder_style()
    {
        wp_register_style('afeb-form-builder-style', $this->assets_url('css/widgets/form-builder.min.css'), [], AFEB_VERSION);
    }

    /**
     * Script of Form Builder widget
     *
     * @since 1.4.0
     */
    public function form_builder_script()
    {
        wp_register_script('afeb-form-builder-script', $this->assets_url('js/form-builder.min.js'), ['jquery'], AFEB_VERSION, true);
    }

    /**
     * Elementor editor style
     *
     * @since 1.0.0
     */
    public function elementor_editor_styles()
    {
        wp_register_style('afeb-elementor-editor-styles', $this->assets_url('css/elementor-editor.min.css'), [], AFEB_VERSION);
        wp_enqueue_style('afeb-elementor-editor-styles');

        wp_register_style('afeb-elementor-widget-icons-styles', $this->assets_url('css/widgets/widgets-icons.min.css'), [], AFEB_VERSION);
        wp_enqueue_style('afeb-elementor-widget-icons-styles');
    }

    /**
     * Elementor widget manager page style
     *
     * @since 1.0.0
     */
    public function elementor_element_manager_styles()
    {
        wp_register_style('afeb-element-manager-styles', $this->assets_url('css/element-manager.min.css'), [], AFEB_VERSION);
        wp_enqueue_style('afeb-element-manager-styles');

        wp_register_style('afeb-elementor-widget-icons-styles', $this->assets_url('css/widgets/widgets-icons.min.css'), [], AFEB_VERSION);
        wp_enqueue_style('afeb-elementor-widget-icons-styles');
    }

    /**
     * Component script for Backend
     *
     * @since 1.0.0
     */
    public function component_script()
    {
        $handle = 'afeb-component-script';
        wp_register_script($handle, $this->assets_url('js/global/component.min.js'), [
            'jquery',
            'jquery-ui-accordion',
            'jquery-ui-dialog',
        ], AFEB_VERSION, true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Accordion widget
     *
     * @since 1.0.0
     */
    public function accordion_style()
    {
        $handle = 'afeb-accordion-style';
        wp_register_style($handle, $this->assets_url('css/widgets/accordion.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Accordion widget
     *
     * @since 1.0.0
     */
    public function accordion_script()
    {
        $handle = 'afeb-accordion-script';
        wp_register_script($handle, $this->assets_url('js/accordion.min.js'), ['jquery'], AFEB_VERSION, true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Alert Box widget
     *
     * @since 1.0.0
     */
    public function alert_box_style()
    {
        $handle = 'afeb-alert-box-style';
        wp_register_style($handle, $this->assets_url('css/widgets/alert-box.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Ripple Effects package
     *
     * @since 1.5.0
     */
    public function ripple_effects_pkg_script()
    {
        $handle = 'afeb-ripple-effects-pkg-script';
        wp_register_script($handle, $this->assets_url('packages/ripple/ripple.min.js'), ['jquery'], AFEB_VERSION, true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Ripple Effects extension
     *
     * @since 1.5.0
     */
    public function ripple_effects_style()
    {
        $handle = 'afeb-ripple-effects-style';
        wp_register_style($handle, $this->assets_url('css/extensions/ripple-effects.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Ripple Effects extension
     *
     * @since 1.5.0
     */
    public function ripple_effects_script()
    {
        $handle = 'afeb-ripple-effects-script';
        wp_register_script($handle, $this->assets_url('js/ripple-effects.min.js'), ['jquery'], AFEB_VERSION, true);
        wp_enqueue_script($handle);
    }

    /**
     * RTL Style of Alert Box widget
     *
     * @since 1.0.0
     */
    public function rtl_alert_box_style()
    {
        $handle = 'rtl-afeb-alert-box-style';
        wp_register_style($handle, $this->assets_url('css/widgets/rtl/alert-box.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Alert Box widget
     *
     * @since 1.0.0
     */
    public function alert_box_script()
    {
        $handle = 'afeb-alert-box-script';
        wp_register_script($handle, $this->assets_url('js/alert-box.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Author Box widget
     *
     * @since 1.0.0
     */
    public function author_box_style()
    {
        $handle = 'afeb-author-box-style';
        wp_register_style($handle, $this->assets_url('css/widgets/author-box.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of Breadcrumb widget
     *
     * @since 1.0.0
     */
    public function breadcrumb_style()
    {
        $handle = 'afeb-breadcrumb-style';
        wp_register_style($handle, $this->assets_url('css/widgets/breadcrumb.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * JS file for custom CSS JS
     *
     * @since 1.5.0
     */
    public function custom_css_js()
    {
        $handle = 'afeb-custom-css-js-script';
        wp_register_script($handle, $this->assets_url('js/custom-css-js.min.js'), ['jquery'], AFEB_VERSION, []);
        wp_enqueue_script($handle);
    }

    /**
     * Style sheet for custom header CSS
     *
     * @since 1.0.4
     */
    public function custom_header_style()
    {
        $handle = 'afeb-custom-header-style';
        wp_register_style($handle, $this->assets_url('css/custom-header.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * JS file for custom header JS
     *
     * @since 1.0.4
     */
    public function custom_header_script()
    {
        $handle = 'afeb-custom-header-script';
        wp_register_script($handle, $this->assets_url('js/globalcustom-header.js'), ['jquery'], AFEB_VERSION, []);
        wp_enqueue_script($handle);
    }

    /**
     * Style sheet for custom footer CSS
     *
     * @since 1.0.4
     */
    public function custom_footer_style()
    {
        $handle = 'afeb-custom-footer-style';
        wp_register_style($handle, $this->assets_url('css/custom-footer.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * JS file for custom footer JS
     *
     * @since 1.0.4
     */
    public function custom_footer_script()
    {
        $handle = 'afeb-custom-footer-script';
        wp_register_script($handle, $this->assets_url('js/global/custom-footer.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Countdown widget
     *
     * @since 1.0.0
     */
    public function countdown_style()
    {
        $handle = 'afeb-countdown-style';
        wp_register_style($handle, $this->assets_url('css/widgets/countdown.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Countdown widget
     *
     * @since 1.0.0
     */
    public function countdown_script()
    {
        $handle = 'afeb-countdown-script';
        wp_register_script($handle, $this->assets_url('js/countdown.min.js'), ['jquery'], AFEB_VERSION);
        wp_enqueue_script($handle);
    }

    /**
     * Style of DataTable package
     *
     * @since 1.2.0
     */
    public function data_table_pkg_style()
    {
        $handle = 'afeb-data-table-pkg-style';
        wp_register_style($handle, $this->assets_url('packages/data-table/data-table.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of DataTable package
     *
     * @since 1.2.0
     */
    public function data_table_pkg_script()
    {
        $handle = 'afeb-data-table-pkg-script';
        wp_register_script($handle, $this->assets_url('packages/data-table/data-table.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Script of DataTable CSV package
     *
     * @since 1.2.0
     */
    public function data_table_csv_pkg_script()
    {
        $handle = 'afeb-data-table-csv-pkg-script';
        wp_register_script($handle, $this->assets_url('packages/data-table/data-table-csv.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of DataTable Buttons package
     *
     * @since 1.2.0
     */
    public function data_table_btns_pkg_style()
    {
        $handle = 'afeb-data-table-pkg-style';
        wp_register_style($handle, $this->assets_url('packages/data-table/data-table-buttons.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of DataTable Buttons package
     *
     * @since 1.2.0
     */
    public function data_table_btns_pkg_script()
    {
        $handle = 'afeb-data-table-btns-pkg-script';
        wp_register_script($handle, $this->assets_url('packages/data-table/data-table-buttons.min.js'), ['jquery', 'afeb-data-table-pkg-script'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of DataTable widget
     *
     * @since 1.2.0
     */
    public function data_table_style()
    {
        $handle = 'afeb-data-table-style';
        wp_register_style($handle, $this->assets_url('css/widgets/data-table.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of DataTable widget
     *
     * @since 1.2.0
     */
    public function data_table_script()
    {
        $handle = 'afeb-data-table-script';
        wp_register_script($handle, $this->assets_url('js/data-table.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Dynamic Hook scripts
     *
     * @since 1.0.0
     */
    public function dynamic_hook_script()
    {
        $handle = 'afeb-dynamic-hook-script';
        wp_register_script($handle, $this->assets_url('js/global/dynamic-hook.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Dynamic Select scripts
     *
     * @since 1.0.7
     */
    public function dynamic_select_script()
    {
        $handle = 'afeb-dynamic-select-script';
        wp_register_script($handle, $this->assets_url('js/dynamic-select.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Editor scripts
     *
     * @since 1.0.0
     */
    public function editor_script()
    {
        $handle = 'afeb-editor-script';
        wp_register_script($handle, $this->assets_url('js/editor.min.js'), ['jquery'], AFEB_VERSION, [], true);
        $this->localize_script($handle);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Fancy Text widget
     *
     * @since 1.0.0
     */
    public function fancy_text_style()
    {
        $handle = 'afeb-fancy-text-style';
        wp_register_style($handle, $this->assets_url('css/widgets/fancy-text.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Fancy Text widget
     *
     * @since 1.0.0
     */
    public function fancy_text_script()
    {
        $handle = 'afeb-fancy-text-script';
        wp_register_script($handle, $this->assets_url('js/fancy-text.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Map widget
     *
     * @since 1.2.0
     */
    public function map_style()
    {
        $handle = 'afeb-map-style';
        wp_register_style($handle, $this->assets_url('css/widgets/map.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Map widget
     *
     * @since 1.2.0
     */
    public function map_script()
    {
        $handle = 'afeb-map-script';
        wp_register_script($handle, $this->assets_url('js/map.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Script of GMaps package
     *
     * @param string $key
     * @since 1.2.0
     *
     */
    public function gmaps_pkg(string $key = '')
    {
        $handle = 'afeb-gmap-pkg-script';
        wp_register_script($handle . '-api', "https://maps.googleapis.com/maps/api/js?key={$key}", ['jquery'], AFEB_VERSION);
        wp_register_script($handle, $this->assets_url('packages/gmaps/gmaps.min.js'), ['jquery', $handle . '-api'], AFEB_VERSION);
        wp_enqueue_script($handle);
    }

    /**
     * Script of OSMaps package
     *
     * @since 1.2.0
     */
    public function osmaps_pkg()
    {
        $handle = 'afeb-osmap-pkg-';
        $css = $handle . 'style';
        wp_register_style($css, $this->assets_url('packages/leaflet/leaflet.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($css);

        $js = $handle . 'script';
        wp_register_script($js, $this->assets_url('packages/leaflet/leaflet.min.js'), ['jquery'], AFEB_VERSION);
        wp_enqueue_script($js);
    }

    /**
     * Script of mixitup package
     *
     * @since 1.5.0
     */
    public function mixitup_pkg_script()
    {
        wp_register_script('afeb-mixitup-pkg-script', $this->assets_url('packages/mixitup/mixitup.min.js'), ['jquery'], AFEB_VERSION, [], true);
    }

    /**
     * Style of Going Up extension
     *
     * @since 1.0.0
     */
    public function going_up_style()
    {
        $handle = 'afeb-going-up-style';
        wp_register_style($handle, $this->assets_url('css/extensions/going-up.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Going Up extension
     *
     * @since 1.0.0
     */
    public function going_up_script()
    {
        $handle = 'afeb-going-up-script';
        wp_register_script($handle, $this->assets_url('js/going-up.min.js'), ['jquery'], AFEB_VERSION, [], true);
        $this->localize_script($handle);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Hotspot widget
     *
     * @since 1.0.0
     */
    public function hotspot_style()
    {
        $handle = 'afeb-hotspot';
        wp_register_style($handle, $this->assets_url('css/widgets/hotspot.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of Font Icon Picker Package
     *
     * @since 1.3.0
     */
    public function icon_picker_style()
    {
        $handle = 'afeb-hotspot';
        wp_register_style($handle, $this->assets_url('packages/font-iconpicker/fonticonpicker.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Font Icon Picker Package
     *
     * @since 1.3.0
     */
    public function icon_picker_script()
    {
        $handle = 'afeb-icon-picker-script';
        wp_register_script($handle, $this->assets_url('packages/font-iconpicker/fonticonpicker.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Info Box widget
     *
     * @since 1.0.0
     */
    public function info_box_style()
    {
        $handle = 'afeb-info-box-style';
        wp_register_style($handle, $this->assets_url('css/widgets/information-box.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of Info Box widget
     *
     * @since 1.2.0
     */
    public function jq_dialog_style()
    {
        $handle = 'afeb-jq-dialog-style';
        wp_register_style($handle, $this->assets_url('packages/jquery-ui/dialog.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Localizes a registered script with data for a JavaScript variable
     *
     * @param string $handle
     * @param string $key
     * @param int $index
     * @since 1.0.4
     *
     */
    public function localize_script($handle = '', $key = 'AFEB', $index = 0)
    {
        if (!function_exists('wp_create_nonce'))
            require_once(ABSPATH . '/wp-includes/pluggable.php');
        $options = [
            [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('afeb_ajax_nonce'),
            ],
        ];

        wp_localize_script($handle, $key, $options[$index]);
    }

    /**
     * Style of Dynamic Grid Carousel widget
     *
     * @since 1.3.0
     */
    public function dynamic_grid_carousel_style()
    {
        wp_register_style('afeb-dynamic-grid-carousel-style', $this->assets_url('css/widgets/dynamic-grid-carousel.min.css'), [], AFEB_VERSION);
    }

    /**
     * Script of Dynamic Grid Carousel widget
     *
     * @since 1.3.0
     */
    public function dynamic_grid_carousel_script()
    {
        wp_register_script('afeb-dynamic-grid-carousel-script', $this->assets_url('js/dynamic-grid-carousel.min.js'), ['jquery', 'afeb-mixitup-pkg-script', 'afeb-slick-pkg-script'], AFEB_VERSION, [], true);
    }

    /**
     * Style of Loop Item document
     *
     * @since 1.3.0
     */
    public function dynamic_loop_item_style()
    {
        $handle = 'afeb-dynamic-loop-item-style';
        wp_register_style($handle, $this->assets_url('css/documents/dynamic-loop-item.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of LRForm widget
     *
     * @since 1.0.3
     */
    public function lrform_style()
    {
        $handle = 'afeb-lrform-style';
        wp_register_style($handle, $this->assets_url('css/widgets/login-register.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of LRForm widget
     *
     * @since 1.0.0
     */
    public function lrform_script()
    {
        $handle = 'afeb-lrform-script';
        wp_register_script($handle, $this->assets_url('js/login-register.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of AdvancedMenus widget
     *
     * @since 1.3.0
     */
    public function advanced_menus_style()
    {
        $handle = 'afeb-advanced-menus-style';
        wp_register_style($handle, $this->assets_url('css/widgets/advanced-menus.min.css'), [], AFEB_VERSION);
    }

    /**
     * Script of AdvancedMenus widget
     *
     * @since 1.3.0
     */
    public function advanced_menus_script()
    {
        $handle = 'afeb-advanced-menus-script';
        wp_register_script($handle, $this->assets_url('js/advanced-menus.min.js'), ['jquery', 'afeb-smart-menus-pkg-script'], AFEB_VERSION, [], true);
    }

    /**
     * Script of Lotties package
     *
     * @since 1.2.0
     */
    public function lotties_pkg()
    {
        $handle = 'afeb-lotties-pkg-script';
        wp_register_script($handle, $this->assets_url('packages/lottie/lottie.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);

        $handle = 'afeb-lotties-script';
        wp_register_script($handle, $this->assets_url('js/lottie.min.js'), ['jquery', 'afeb-lotties-pkg-script'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Newsticker widget
     *
     * @since 1.0.0
     */
    public function newsticker_style()
    {
        $handle = 'afeb-newsticker-style';
        wp_register_style($handle, $this->assets_url('css/widgets/news-ticker.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Newsticker widget
     *
     * @since 1.0.0
     */
    public function newsticker_script()
    {
        $handle = 'afeb-newsticker-script';
        wp_register_script($handle, $this->assets_url('js/news-ticker.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of NoticeBox widget
     *
     * @since 1.0.0
     */
    public function notice_box_style()
    {
        $handle = 'afeb-notice-box-style';
        wp_register_style($handle, $this->assets_url('css/widgets/notice-box.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of Offcanvas widget
     *
     * @since 1.2.0
     */
    public function offcanvas_style()
    {
        $handle = 'afeb-off-canvas-style';
        wp_register_style($handle, $this->assets_url('css/widgets/off-canvas.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Offcanvas widget
     *
     * @since 1.2.0
     */
    public function offcanvas_script()
    {
        $handle = 'afeb-off-canvas-script';
        wp_register_script($handle, $this->assets_url('js/off-canvas.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Preloader extension
     *
     * @since 1.5.0
     */
    public function preloader_style()
    {
        $handle = 'afeb-preloader-style';
        wp_register_style($handle, $this->assets_url('css/extensions/preloader.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Preloader extension
     *
     * @since 1.5.0
     */
    public function preloader_script()
    {
        $handle = 'afeb-preloader-script';
        wp_register_script($handle, $this->assets_url('js/preloader.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Popup document
     *
     * @since 1.2.0
     */
    public function popup_style()
    {
        $handle = 'afeb-popup-style';
        wp_register_style($handle, $this->assets_url('css/documents/popup.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Popup document
     *
     * @since 1.2.0
     */
    public function popup_script()
    {
        $handle = 'afeb-popup-script';
        wp_register_script($handle, $this->assets_url('js/popup.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of PostNavigation widget
     *
     * @since 1.5.0
     */
    public function post_navigation_style()
    {
        $handle = 'afeb-post-navigation-style';
        wp_register_style($handle, $this->assets_url('css/widgets/post-navigation.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of PriceBox widget
     *
     * @since 1.0.0
     */
    public function price_box_style()
    {
        $handle = 'afeb-price-box-style';
        wp_register_style($handle, $this->assets_url('css/widgets/price-box.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of Pro Version control
     *
     * @since 1.0.4
     */
    public function pro_version_style()
    {
        $handle = 'afeb-pro-version-style';
        wp_register_style($handle, $this->assets_url('css/pro-version.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of Search Input widget
     *
     * @since 1.0.0
     */
    public function search_form_style()
    {
        wp_register_style('afeb-search-form-style', $this->assets_url('css/widgets/search-form.min.css'), [], AFEB_VERSION);
    }

    /**
     * Script of Search Input widget
     *
     * @since 1.5.0
     */
    public function search_form_script()
    {
        $handle = 'afeb-search-form-script';
        wp_register_script($handle, $this->assets_url('js/search-form.min.js'), ['jquery'], AFEB_VERSION, [], true);
        $this->localize_script($handle);
    }

    /**
     * Script of Smart Menus package
     *
     * @since 1.3.0
     */
    public function smart_menus_pkg_script()
    {
        $handle = 'afeb-smart-menus-pkg-script';
        wp_register_script($handle, $this->assets_url('packages/smart-menus/smartmenus.min.js'), ['jquery'], AFEB_VERSION, [], true);
    }

    /**
     * Style of Sound Player widget
     *
     * @since 1.0.0
     */
    public function sound_player_style()
    {
        $handle = 'afeb-sound-player-style';
        wp_register_style($handle, $this->assets_url('css/widgets/sound-player.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Sound Player widget
     *
     * @since 1.0.0
     */
    public function sound_player_script()
    {
        $handle = 'afeb-sound-player-script';
        wp_register_script($handle, $this->assets_url('js/sound-player.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Script of Sticky package
     *
     * @since 1.4.0
     */
    public function sticky_pkg_script()
    {
        $handle = 'afeb-sticky-pkg-script';
        wp_register_script($handle, $this->assets_url('packages/sticky/sticky.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Script of Sticky extension
     *
     * @since 1.4.0
     */
    public function sticky_script()
    {
        $handle = 'afeb-sticky-script';
        wp_register_script($handle, $this->assets_url('js/sticky.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Script of Howler package
     *
     * @since 1.0.0
     */
    public function howler_script()
    {
        $handle = 'afeb-howler-script';
        wp_register_script($handle, $this->assets_url('packages/howler/howler.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Dynamic Loop Item document (Editor)
     *
     * @since 1.3.0
     */
    public function editor_dynamic_loop_item_style()
    {
        $handle = 'afeb-editor-dynamic-loop-item-style';
        wp_register_style($handle, $this->assets_url('css/documents/editor/dynamic-loop-item.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of Popup document (Editor)
     *
     * @since 1.2.0
     */
    public function editor_popup_style()
    {
        $handle = 'afeb-editor-popup-style';
        wp_register_style($handle, $this->assets_url('css/documents/editor/popup.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of Template Builder document (Editor)
     *
     * @since 1.5.0
     */
    public function editor_template_builder_style()
    {
        $handle = 'afeb-editor-template-builder-style';
        wp_register_style($handle, $this->assets_url('css/documents/editor/template-builder.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Slick package
     *
     * @since 1.3.0
     */
    public function slick_pkg()
    {
        $handle = 'afeb-slick-pkg-';
        $css = $handle . 'style-theme';
        wp_register_style($css, $this->assets_url('packages/slick/slick-theme.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($css);

        $css = $handle . 'style';
        wp_register_style($css, $this->assets_url('packages/slick/slick.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($css);

        $js = $handle . 'script';
        wp_register_script($js, $this->assets_url('packages/slick/slick.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($js);
    }

    /**
     * Style of Slides widget
     *
     * @since 1.5.0
     */
    public function slides_style()
    {
        wp_register_style('afeb-slides-style', $this->assets_url('css/widgets/slides.min.css'), [], AFEB_VERSION);
    }

    /**
     * Script of Slides widget
     *
     * @since 1.5.0
     */
    public function slides_script()
    {
        wp_register_script('afeb-slides-script', $this->assets_url('js/slides.min.js'), ['jquery'], AFEB_VERSION, [], true);
    }

    /**
     * Style of Tabs widget
     *
     * @since 1.1.1
     */
    public function tabs_style()
    {
        $handle = 'afeb-tabs-style';
        wp_register_style($handle, $this->assets_url('css/widgets/tabs.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Tabs widget
     *
     * @since 1.1.1
     */
    public function tabs_script()
    {
        $handle = 'afeb-tabs-script';
        wp_register_script($handle, $this->assets_url('js/tabs.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of TeamMemberCarousel widget
     *
     * @since 1.5.0
     */
    public function team_member_carousel_style()
    {
        wp_register_style('afeb-team-member-carousel-style', $this->assets_url('css/widgets/team-member-carousel.min.css'), [], AFEB_VERSION);
    }

    /**
     * Script of TeamMemberCarousel widget
     *
     * @since 1.5.0
     */
    public function team_member_carousel_script()
    {
        wp_register_script('afeb-team-member-carousel-script', $this->assets_url('js/team-member-carousel.min.js'), ['jquery'], AFEB_VERSION, [], true);
    }

    /**
     * Script of Templates Kit Improt
     *
     * @since 1.0.0
     */
    public function templates_kit_script()
    {
        $handle = 'afeb-templates-kit-script';
        wp_register_script($handle, $this->assets_url('js/templates-kit.min.js'), ['jquery'], AFEB_VERSION, [], true);
        $this->localize_script($handle);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Testimonial Carousel widget
     *
     * @since 1.0.0
     */
    public function testimonial_carousel_style()
    {
        $handle = 'afeb-testimonial-carousel-style';
        wp_register_style($handle, $this->assets_url('css/widgets/testimonial-carousel.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Testimonial Carousel widget
     *
     * @since 1.0.0
     */
    public function testimonial_carousel_script()
    {
        $handle = 'afeb-testimonial-carousel-script';
        wp_register_script($handle, $this->assets_url('js/testimonial-carousel.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    /**
     * Style of Woo Checkout widget
     *
     * @since 1.0.0
     */
    public function woo_checkout()
    {
        wp_register_style(
            'afeb-woo-checkout-style',
            $this->assets_url('css/widgets/woo-checkout.min.css'),
            [],
            AFEB_VERSION
        );

        wp_register_style(
            'afeb-woo-checkout-layout',
            $this->assets_url('css/widgets/woo-checkout-layout.css'),
            [],
            '1.0.1'
        );

        if (
            (function_exists('is_checkout') && is_checkout()) ||
            (class_exists(Plugin::class) && Plugin::$instance->editor->is_edit_mode())
        ) {
            wp_enqueue_style('afeb-woo-checkout-style');
            wp_enqueue_style('afeb-woo-checkout-layout');
        }
    }

    /**
     * Style of Timeline widget
     *
     * @since 1.0.0
     */
    public function timeline_style()
    {
        $handle = 'afeb-timeline-style';
        wp_register_style($handle, $this->assets_url('css/widgets/timeline.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Style of Woo My Account widget
     *
     * @since 1.3.0
     */
    public function woo_my_account()
    {
        $handle = 'afeb-woo-my-account-style';
        wp_register_style($handle, $this->assets_url('css/widgets/woo-my-account.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Woo My Account widget
     *
     * @since 1.3.0
     */
    public function woo_my_account_script()
    {
        $handle = 'afeb-woo-my-account-script';
        wp_register_script(
            $handle,
            $this->assets_url('js/woo-my-account-tabs.js'),
            ['jquery'],
            AFEB_VERSION,
            ['in_footer' => true]
        );
        wp_enqueue_script($handle);
    }

    /**
     * Style of Wrapper Link extension
     *
     * @since 1.0.3
     */
    public function wrapper_link_style()
    {
        $handle = 'afeb-wrapper-link-style';
        wp_register_style($handle, $this->assets_url('css/extensions/wrapper-link.min.css'), [], AFEB_VERSION);
        wp_enqueue_style($handle);
    }

    /**
     * Script of Wrapper Link extension
     *
     * @since 1.0.3
     */
    public function wrapper_link_script()
    {
        $handle = 'afeb-wrapper-link-script';
        wp_register_script($handle, $this->assets_url('js/wrapper-link.min.js'), ['jquery'], AFEB_VERSION, [], true);
        wp_enqueue_script($handle);
    }

    private function register_swiper_assets()
    {
        if (!wp_style_is('swiper', 'registered'))
        {
            wp_register_style(
                'swiper',
                $this->assets_url('packages/swiper/swiper-bundle.min.css'),
                [],
                '9.3.2'
            );
        }

        if (!wp_script_is('swiper', 'registered'))
        {
            wp_register_script(
                'swiper',
                $this->assets_url('packages/swiper/swiper-bundle.min.js'),
                [],
                '9.3.2',
                true
            );
        }
    }

    private function register_glightbox_assets()
    {
        if (!wp_style_is('glightbox', 'registered'))
        {
            wp_register_style(
                'glightbox',
                $this->assets_url('packages/glightbox/glightbox.min.css'),
                [],
                '3.2.0'
            );
        }

        if (!wp_script_is('glightbox', 'registered'))
        {
            wp_register_script(
                'glightbox',
                $this->assets_url('packages/glightbox/glightbox.min.js'),
                [],
                '3.2.0',
                true
            );
        }
    }

    public function woo_add_to_cart_script()
    {
        if (class_exists('WooCommerce'))
        {
            wp_enqueue_script('wc-add-to-cart');
        }

        wp_enqueue_script(
            'afeb-woo-atc-redirect',
            $this->assets_url('js/afeb-woo-atc-redirect.js'),
            ['jquery', 'wc-add-to-cart'],
            AFEB_VERSION,
            true
        );

        wp_localize_script('afeb-woo-atc-redirect', 'afeb_atc_params', [
            'cart_url' => function_exists('wc_get_cart_url') ? wc_get_cart_url() : '',
            'checkout_url' => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : '',
        ]);
    }
}
