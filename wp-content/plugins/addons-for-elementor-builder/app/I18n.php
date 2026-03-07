<?php

namespace AFEB;

/**
 * "Vertex Addons for Elementor" I18N Class
 *
 * @class I18n
 * @version 1.0.0
 */
class I18n extends Base
{
    /**
     * Initialize "Vertex Addons for Elementor" I18N
     *
     * @since 1.0.0
     */
    public function init()
    {
        add_action('init', [$this, 'load_languages']);
    }

    /**
     * Load plugin languages
     *
     * @since 1.0.0
     */
    public function load_languages()
    {
        /**
         * Getting current locale
         * Filter the locale of the blog
         */
        $locale = apply_filters('afeb/i18n/locale', get_locale(), 'addons-for-elementor-builder');

        // WordPress' language directory /wp-content/languages/addons-for-elementor-builder-en_US.mo
        $language_file_path = WP_LANG_DIR . '/addons-for-elementor-builder-' . $locale . '.mo';

        // If language file exists on WordPress language directory use it
        if (file_exists($language_file_path))
        {
            load_textdomain('addons-for-elementor-builder', $language_file_path);
        }
    }
}
