<?php

class LSDRC_i18n
{
    public function init()
    {
        // Register Language Files
        add_action('init', [$this, 'load_languages']);
    }

    public function load_languages()
    {
        // Get current locale
        $locale = apply_filters('plugin_locale', get_locale(), 'listdomer-core');

        // WordPress' language directory /wp-content/languages/listdomer-core-en_US.mo
        $path = WP_LANG_DIR . '/listdomer-core-' . $locale . '.mo';

        // If language file exists on WordPress' language directory use it
        if (file_exists($path))
        {
            load_textdomain('listdomer-core', $path);
        }
        // Otherwise use plugin directory /path/to/plugin/i18n/languages/listdomer-core-en_US.mo
        else
        {
            load_plugin_textdomain('listdomer-core', false, dirname(LSDRC_BASENAME) . '/i18n/languages/');
        }
    }
}
