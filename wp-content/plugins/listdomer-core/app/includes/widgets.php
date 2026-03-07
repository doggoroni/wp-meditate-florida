<?php

class LSDRC_Widgets extends LSDRC_Base
{
    public function init()
    {
        add_action('widgets_init', [$this, 'register']);
    }

    public function register()
    {
        // Contact Widget
        register_widget('LSDRC_Widgets_Contact');
    }
}
