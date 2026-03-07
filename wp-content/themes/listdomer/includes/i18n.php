<?php

class LSDR_I18n extends LSDR_Base
{
    public function init()
    {
        add_action('after_setup_theme', [$this, 'setup']);
    }

    public function setup()
    {
        /*
         * Make theme available for translation.
         * Translations can be filed in the /i18n/languages/ directory.
         * If you're building a theme based on Listdomer, use a find and replace
         * to change 'listdomer' to the name of your theme in all the template files.
         */
        load_theme_textdomain('listdomer', get_template_directory() . '/languages');
    }
}
