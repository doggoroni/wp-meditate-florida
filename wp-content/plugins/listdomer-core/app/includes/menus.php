<?php

class LSDRC_Menus extends LSDRC_Base
{
    public function init()
    {
        // Register Listdomer Menus
        add_action('admin_menu', [$this, 'register_menus'], 1);
    }

    public function register_menus()
    {
        $icon = plugins_url() . '/' . LSDRC_DIRNAME . '/assets/img/listdomer-icon.svg';

        add_menu_page(esc_html__('Listdomer', 'listdomer-core'), esc_html__('Listdomer', 'listdomer-core'), 'manage_options', 'listdomer', [$this, 'dashboard'], $icon, 59);

        // Demo Import
        if (class_exists(OCDI_Plugin::class))
        {
            add_submenu_page('listdomer', esc_html__('Demo Import', 'listdomer-core'), esc_html__('Demo Import', 'listdomer-core'), 'manage_options', 'listdomer', [$this, 'dashboard'], 1);
        }

        // Listdomer Settings
        if (class_exists(Redux_Framework_Plugin::class))
        {
            add_submenu_page('listdomer', esc_html__('Settings', 'listdomer-core'), esc_html__('Settings', 'listdomer-core'), 'manage_options', 'listdomer-settings', null, 2);
        }

        add_submenu_page('listdomer', esc_html__('Documentation', 'listdomer-core'), esc_html__('Documentation', 'listdomer-core'), 'manage_options', 'https://api.webilia.com/go/listdomer-doc', null, 3);
        add_submenu_page('listdomer', esc_html__('Support', 'listdomer-core'), esc_html__('Support', 'listdomer-core'), 'manage_options', 'https://listdom.net/support/', null, 4);
    }

    public function dashboard()
    {
        if (class_exists(OCDI_Plugin::class)) return;

        echo '<div class="wrap about-wrap">
            <div class="about-text">
                <div class="lsd-alert lsd-info">
                    <h3>' . esc_html__('Recommended plugin not installed.', 'listdomer-core') . '</h3>
                    <p>' . sprintf(esc_html__('Listdomer recommends installing the %s plugin for full functionality.', 'listdomer-core'), '<strong>One Click Demo Import</strong>') . '</p>
                    <p>' . esc_html__("Install the plugin from the notice above or go to Plugins > Add New in your WordPress dashboard.", 'listdomer-core') . '</p>
                </div>
            </div>
        </div>';
    }
}
