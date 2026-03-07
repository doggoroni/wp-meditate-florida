<?php

class LSDR_Navs extends LSDR_Base
{
    public function init()
    {
        add_action('after_setup_theme', [$this, 'setup']);
    }

    public function setup()
    {
        register_nav_menus([
            'menu-1' => esc_html__('Primary', 'listdomer'),
            'menu-2' => esc_html__('Sub-footer', 'listdomer'),
        ]);
    }
}
