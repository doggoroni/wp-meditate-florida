<?php

namespace AFEB\Menus;

use AFEB\Assets;
use AFEB\Extensions;
use AFEB\Helper;
use AFEB\Menus;
use AFEB\Widgets;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" Dashboard Menus Class
 * 
 * @class Dashboard
 * @version 1.0.0
 */
class Dashboard extends Menus
{
    /**
     * Dashboard Page ID
     */
    const DASHBOARD_PAGE_ID = 'toplevel_page_afeb';

    /**
     * Dashboard Latest News Feed
     */
    const DASHBOARD_LATEST_NEWS_FEED = self::AFEB_URL . '/feed';

    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var Widgets
     */
    private $widgets;

    /**
     * @var Extensions
     */
    private $extensions;

    /**
     * @var string
     */
    public $tab;

    /**
     * Initialize "Vertex Addons for Elementor" Dashboard
     * 
     * @since 1.0.0
     */
    public function init()
    {
        $this->assets = new Assets();
        $this->actions();
    }

    /**
     * Dashboard Class Actions
     * 
     * @since 1.0.0
     */
    public function actions()
    {
        add_action('init', [$this, 'save_changes']);
        add_action('current_screen', [$this, 'current_screen']);
    }

    /**
     * Dashboard menu HTML output
     * 
     * @since 1.0.0
     */
    public function output()
    {
        $this->tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'dashboard'; // Get the current tab

        // Generate output
        $this->get_template_part($this->get_path() . '/html/admin/menus/dashboard/');
    }

    /**
     * Execute the relevant code on the current page
     * 
     * @since 1.0.0
     * 
     * @param object $screen
     */
    public function current_screen($screen)
    {
        if (isset($screen->id) && $screen->id == self::DASHBOARD_PAGE_ID) {
            add_action('admin_enqueue_scripts', function () {
                $this->assets->normalize_style();
                $this->assets->fontawesome_style();
                $this->assets->jq_dialog_style();
                wp_enqueue_script('jquery-effects-scale');
                $this->assets->templates_kit_script();
                $this->assets->component_script();
            });

            add_filter('admin_body_class', [$this, 'change_body_class']);
        }
    }

    /**
     * Change dashboard page body class
     * 
     * @since 1.0.0
     * 
     * @param string $classes
     * 
     * @return string
     */
    public function change_body_class($classes)
    {
        return $classes . ' afeb-body afeb-dashboard-body';
    }

    /**
     * Save changes to different pars of the dashboard
     * 
     * @since 1.0.0
     */
    public function save_changes()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if (isset($_POST['afeb_active_widgets'])) $this->save_widgets();

        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if (isset($_POST['afeb_active_3rdpt_widgets'])) $this->save_3rdpt_widgets();

        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if (isset($_POST['afeb_active_extensions'])) $this->save_extensions();

        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if (isset($_POST['afeb_settings'])) $this->save_settings();
    }

    /**
     * Save widget section changes
     * 
     * @since 1.0.0
     */
    public function save_widgets()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if (empty($_POST['afeb-widgets-nonce']))
            Helper::admin_notice(esc_html__('The submitted form is not secure, Nonce is not set.', 'addons-for-elementor-builder'), 'error', true);

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['afeb-widgets-nonce'])), 'afeb-widgets-action'))
            Helper::admin_notice(esc_html__('Security token did not match.', 'addons-for-elementor-builder'), 'error', true);

        $this->widgets = new Widgets();
        $widgets = array_merge($this->widgets->widgets(), $this->widgets->template_builder_widgets());
        $post_widgets = isset($_POST['afeb_active_widgets']) && is_array($_POST['afeb_active_widgets']) ? map_deep($_POST['afeb_active_widgets'], 'intval') : [];
        $filter_val = [];
        foreach ($post_widgets as $widget_key => $widget_value) {
            if (isset($widgets[$widget_key]))
                if ($widget_value['status'] != $widgets[$widget_key]['status'])
                    $filter_val[$widget_key] = ['status' => $widget_value['status']];
        }

        delete_option('afeb-widgets-status');
        $update_option = update_option('afeb-widgets-status', $filter_val);
        if ($update_option) Helper::admin_notice(esc_html__('Changes saved successfully.', 'addons-for-elementor-builder'), 'success', true);
        else Helper::admin_notice(esc_html__('There was a problem when saving the changes.', 'addons-for-elementor-builder'), 'error', true);
    }

    /**
     * Save 3rdpt widget section changes
     * 
     * @since 1.0.2
     */
    public function save_3rdpt_widgets()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if (empty($_POST['afeb-widgets-nonce']))
            Helper::admin_notice(esc_html__('The submitted form is not secure, Nonce is not set.', 'addons-for-elementor-builder'), 'error', true);

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['afeb-widgets-nonce'])), 'afeb-widgets-action'))
            Helper::admin_notice(esc_html__('Security token did not match.', 'addons-for-elementor-builder'), 'error', true);

        $this->widgets = new Widgets();
        $trdpt_widgets = $this->widgets->trdpt_widgets();
        $post_trdpt_widgets = isset($_POST['afeb_active_3rdpt_widgets']) && is_array($_POST['afeb_active_3rdpt_widgets']) ? map_deep($_POST['afeb_active_3rdpt_widgets'], 'intval') : [];
        $filter_val = [];
        foreach ($post_trdpt_widgets as $trdpt_widget_key => $trdpt_widget_value) {
            if (isset($trdpt_widgets[$trdpt_widget_key]))
                if ($trdpt_widget_value['status'] != $trdpt_widgets[$trdpt_widget_key]['status'])
                    $filter_val[$trdpt_widget_key] = ['status' => $trdpt_widget_value['status']];
        }

        delete_option('afeb-3rdpt-widgets-status');
        $update_option = update_option('afeb-3rdpt-widgets-status', $filter_val);
        if ($update_option) Helper::admin_notice(esc_html__('Changes saved successfully.', 'addons-for-elementor-builder'), 'success', true);
        else Helper::admin_notice(esc_html__('There was a problem when saving the changes.', 'addons-for-elementor-builder'), 'error', true);
    }

    /**
     * Save extensions section changes
     * 
     * @since 1.0.3
     */
    public function save_extensions()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if (empty($_POST['afeb-extensions-nonce']))
            Helper::admin_notice(esc_html__('The submitted form is not secure, Nonce is not set.', 'addons-for-elementor-builder'), 'error', true);

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['afeb-extensions-nonce'])), 'afeb-extensions-action'))
            Helper::admin_notice(esc_html__('Security token did not match.', 'addons-for-elementor-builder'), 'error', true);

        $this->extensions = new Extensions();
        $extensions = $this->extensions->extensions();
        $post_extensions = isset($_POST['afeb_active_extensions']) && is_array($_POST['afeb_active_extensions']) ? map_deep($_POST['afeb_active_extensions'], 'intval') : [];
        $filter_val = [];
        foreach ($post_extensions as $extension_key => $extension_value) {
            if (isset($extensions[$extension_key]))
                if ($extension_value['status'] != $extensions[$extension_key]['status'])
                    $filter_val[$extension_key] = ['status' => $extension_value['status']];
        }

        delete_option('afeb-extensions-status');
        $update_option = update_option('afeb-extensions-status', $filter_val);
        if ($update_option) Helper::admin_notice(esc_html__('Changes saved successfully.', 'addons-for-elementor-builder'), 'success', true);
        else Helper::admin_notice(esc_html__('There was a problem when saving the changes.', 'addons-for-elementor-builder'), 'error', true);
    }

    /**
     * Save settings section changes
     * 
     * @since 1.2.0
     */
    public function save_settings()
    {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing
        if (empty($_POST['afeb-settings-nonce']))
            Helper::admin_notice(esc_html__('The submitted form is not secure, Nonce is not set.', 'addons-for-elementor-builder'), 'error', true);

        if (!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['afeb-settings-nonce'])), 'afeb-settings-action'))
            Helper::admin_notice(esc_html__('Security token did not match.', 'addons-for-elementor-builder'), 'error', true);

        $post_settings = isset($_POST['afeb_settings']) && is_array($_POST['afeb_settings']) ? map_deep($_POST['afeb_settings'], 'intval') : [];

        delete_option('afeb-settings');
        $update_option = update_option('afeb-settings', $post_settings);
        if ($update_option) Helper::admin_notice(esc_html__('Changes saved successfully.', 'addons-for-elementor-builder'), 'success', true);
        else Helper::admin_notice(esc_html__('There was a problem when saving the changes.', 'addons-for-elementor-builder'), 'error', true);
    }
}
