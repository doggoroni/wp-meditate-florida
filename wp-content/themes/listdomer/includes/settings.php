<?php

class LSDR_Settings extends LSDR_Base
{
    public static function get(string $key, $default = null)
    {
        // Listdomer Settings
        if (class_exists('LSDRC_Settings')) return LSDRC_Settings::get($key, $default);

        // Default Value
        return get_theme_mod($key, $default);
    }

    public static function should_display_element($setting_single, $setting_archive)
    {
        if (is_singular()) return LSDR_Settings::get($setting_single);
        else if (is_archive() || is_home() || is_search()) return LSDR_Settings::get($setting_archive);

        return false;
    }
}
