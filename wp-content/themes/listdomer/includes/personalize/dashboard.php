<?php

class LSDR_Personalize_Dashboard extends LSDR_Personalize
{
    /**
     * Generates personalized dashboard styles and applies them to the CSS.
     *
     * @param string|null $CSS The raw CSS content. If null, loads the default CSS.
     * @return string Modified CSS with personalized dashboard styles.
     */
    public static function generate(string $CSS = ''): string
    {
        if (!$CSS) $CSS = file_get_contents(get_template_directory() . '/assets/css/personalized-dashboard.raw');

        $dashboard_settings = [
	        'dashboard_container_bg' => sanitize_text_field(LSDR_Settings::get('listdomer_dashboard_container_bg_color', '#0ab0fe')),
	        'dashboard_container_border_radius' => sanitize_text_field(LSDR_Settings::get('listdomer_dashboard_container_border_radius', '5')) . 'px',
	        'dashboard_bg_color' => sanitize_text_field(LSDR_Settings::get('listdomer_dashboard_bg', '#0ab0fe')),
	        'dashboard_text_color' => sanitize_text_field(LSDR_Settings::get('listdomer_dashboard_text_color', '#0ab0fe')),
	        'dashboard_icon_color' => sanitize_text_field(LSDR_Settings::get('listdomer_dashboard_icon_color', '#fff')),
            'dashboard_hover_bg_color' => sanitize_text_field(LSDR_Settings::get('listdomer_dashboard_hover_bg_color', '#0ab0fe')),
            'dashboard_hover_text_color' => sanitize_text_field(LSDR_Settings::get('listdomer_dashboard_hover_text_color', '#fff')),
            'dashboard_border' => self::borders('listdomer_dashboard_border', '1', 'solid', '#0ab0fe'),
            'dashboard_border_hover' => self::borders('listdomer_dashboard_border_hover', '1', 'solid', '#ffffff29'),
            'dashboard_border_radius' => sanitize_text_field(LSDR_Settings::get('listdomer_dashboard_border_radius', '5')) . 'px',
        ];

        return self::replacement($CSS, $dashboard_settings);
    }

    /**
     * Retrieves and sanitizes border settings for dashboard.
     *
     * @param string $key The setting key for the border.
     * @param string $default_width Default border width if not set.
     * @param string $default_style Default border style if not set.
     * @param string $default_color Default border color if not set.
     * @return array Sanitized border settings (top, right, bottom, left, style, color).
     */
    private static function borders(string $key, string $default_width, string $default_style, string $default_color): array
    {
        $border = LSDR_Settings::get($key);

        $top = isset($border['border-top']) ? sanitize_text_field($border['border-top']) : $default_width;
        $right = isset($border['border-right']) ? sanitize_text_field($border['border-right']) : $default_width;
        $bottom = isset($border['border-bottom']) ? sanitize_text_field($border['border-bottom']) : $default_width;
        $left = isset($border['border-left']) ? sanitize_text_field($border['border-left']) : $default_width;
        $style = isset($border['border-style']) ? sanitize_text_field($border['border-style']) : $default_style;
        $color = isset($border['border-color']) ? sanitize_text_field($border['border-color']) : $default_color;

        return [
            'border' => sprintf('%s %s %s %s', $top, $right, $bottom, $left),
            'border_style' => $style,
            'border_color' => $color,
        ];
    }

    /**
     * Replaces dashboard style placeholders in the CSS with actual values.
     *
     * @param string $CSS The raw CSS content.
     * @param array $dashboard_settings The dashboard settings to apply.
     * @return string Modified CSS with applied dashboard styles.
     */
    private static function replacement(string $CSS, array $dashboard_settings): string
    {
        $placeholders = [
	        '((dashboard_container_bg_color))',
	        '((dashboard_container_border_radius))',
            '((dashboard_bg_color))',
	        '((dashboard_text_color))',
	        '((dashboard_icon_color))',
            '((dashboard_hover_bg_color))',
            '((dashboard_hover_text_color))',
            '((dashboard_border))',
            '((dashboard_border_style))',
            '((dashboard_border_color))',
            '((dashboard_border_hover))',
            '((dashboard_border_hover_style))',
            '((dashboard_border_hover_color))',
            '((dashboard_border_radius))',
        ];

        $values = [
	        $dashboard_settings['dashboard_container_bg'],
	        $dashboard_settings['dashboard_container_border_radius'],
            $dashboard_settings['dashboard_bg_color'],
	        $dashboard_settings['dashboard_text_color'],
	        $dashboard_settings['dashboard_icon_color'],
            $dashboard_settings['dashboard_hover_bg_color'],
            $dashboard_settings['dashboard_hover_text_color'],
            $dashboard_settings['dashboard_border']['border'],
            $dashboard_settings['dashboard_border']['border_style'],
            $dashboard_settings['dashboard_border']['border_color'],
            $dashboard_settings['dashboard_border_hover']['border'],
            $dashboard_settings['dashboard_border_hover']['border_style'],
            $dashboard_settings['dashboard_border_hover']['border_color'],
            $dashboard_settings['dashboard_border_radius'],
        ];

        return str_replace($placeholders, $values, $CSS);
    }
}
