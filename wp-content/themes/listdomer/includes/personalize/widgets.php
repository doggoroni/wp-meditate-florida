<?php

class LSDR_Personalize_Widgets extends LSDR_Personalize
{
    /**
     * Generates personalized widget styles and applies them to the CSS.
     *
     * @param string|null $CSS The raw CSS content. If null, loads the default CSS.
     * @return string Modified CSS with personalized widget styles.
     */
    public static function generate(string $CSS = ''): string
    {
        if (!$CSS) $CSS = file_get_contents(get_template_directory() . '/assets/css/personalized-widget.raw');

        $widget_settings = [
            'widget_bg_color' => sanitize_text_field(LSDR_Settings::get('listdomer_widget_bg_color', '#fff')),
            'widget_border_radius' => sanitize_text_field(LSDR_Settings::get('listdomer_widget_border_radius', '5')) . 'px',
            'widget_title_text_color' => sanitize_text_field(LSDR_Settings::get('listdomer_widget_title_text_color', '#000')),
            'widget_title_border_color' => sanitize_text_field(LSDR_Settings::get('listdomer_widget_title_border_color', '#ececec')),
            'widget_content_text_color' => sanitize_text_field(LSDR_Settings::get('listdomer_widget_content_text_color', '#8d919c')),
        ];

        return self::replacement($CSS, $widget_settings);
    }

    /**
     * Replaces widget style placeholders in the CSS with actual values.
     *
     * @param string $CSS The raw CSS content.
     * @param array $widget_settings The widget settings to apply.
     * @return string Modified CSS with applied widget styles.
     */
    private static function replacement(string $CSS, array $widget_settings): string
    {
        $placeholders = [
            '((widget_bg_color))',
            '((widget_border_radius))',
            '((widget_title_text_color))',
            '((widget_title_border_color))',
            '((widget_content_text_color))',
        ];

        $values = [
            $widget_settings['widget_bg_color'],
            $widget_settings['widget_border_radius'],
            $widget_settings['widget_title_text_color'],
            $widget_settings['widget_title_border_color'],
            $widget_settings['widget_content_text_color'],
        ];

        return str_replace($placeholders, $values, $CSS);
    }
}
