<?php

class LSDR_Personalize_Buttons extends LSDR_Personalize
{
    /**
     * Generates the CSS for different button types (primary, secondary, solid) by gathering
     * settings and applying them to the CSS file.
     *
     * @param string|null $CSS The raw CSS content to be modified. If null, it loads the default CSS file.
     * @return string The modified CSS with personalized button styles.
     */
    public static function generate(string $CSS = ''): string
    {
        if (!$CSS) $CSS = file_get_contents(get_template_directory() . '/assets/css/personalized.txt');

        $buttons_settings = [
            'primary' => self::buttons('primary'),
            'secondary' => self::buttons('secondary'),
            'solid' => self::buttons('solid'),
        ];

        return self::replacement($CSS, $buttons_settings);
    }

    /**
     * Retrieves the button styles and settings for a given button type.
     *
     * @param string $button_type The type of button (e.g., primary, secondary, solid).
     * @return array An associative array containing button style settings like background color,
     *               text color, border radius, and hover styles.
     */
    private static function buttons(string $button_type): array
    {
        $prefix = "listdomer_{$button_type}_button";

        return [
            'bg_color' => sanitize_text_field(LSDR_Settings::get("{$prefix}_bg_color", '#33c6ff')),
            'bg_color_2' => ($button_type == 'primary') ? sanitize_text_field(LSDR_Settings::get("{$prefix}_bg_color_2", '#306be6')) : '',
            'text_color' => sanitize_text_field(LSDR_Settings::get("{$prefix}_text_color", '#fff')),
            'border_radius' => sanitize_text_field(LSDR_Settings::get("{$prefix}_border_radius", '4')) . 'px',
            'hover_bg_color' => sanitize_text_field(LSDR_Settings::get("{$prefix}_hover_bg_color", '#29a6e1')),
            'hover_text_color' => sanitize_text_field(LSDR_Settings::get("{$prefix}_hover_text_color", '#eee')),
            'border' => self::get_border("{$prefix}_border"),
            'border_hover' => self::get_border("{$prefix}_border_hover"),
        ];
    }

    /**
     * Retrieves and sanitizes border settings for the button styles.
     *
     * @param string $prefix The prefix used to fetch the border settings (e.g., primary_button_border).
     * @return array An associative array containing the border settings such as top, right, bottom, left,
     *               border style, and border color.
     */
    private static function get_border(string $prefix): array
    {
        // Default border if not set
        $border = LSDR_Settings::get($prefix, [
            'border-top' => '0',
            'border-right' => '0',
            'border-bottom' => '0',
            'border-left' => '0',
            'border-style' => 'none',
            'border-color' => 'transparent',
        ]);

        return [
            'top' => sanitize_text_field($border['border-top']),
            'right' => sanitize_text_field($border['border-right']),
            'bottom' => sanitize_text_field($border['border-bottom']),
            'left' => sanitize_text_field($border['border-left']),
            'style' => sanitize_text_field($border['border-style']),
            'color' => sanitize_text_field($border['border-color']),
        ];
    }

    /**
     * Replaces the placeholders in the CSS with the personalized button styles.
     *
     * @param string $CSS The raw CSS content to be modified.
     * @param array $buttons_settings The array of button styles to apply to the CSS.
     * @return string The modified CSS with the button styles replaced.
     */
    private static function replacement(string $CSS, array $buttons_settings): string
    {
        foreach ($buttons_settings as $button_type => $settings)
        {
            $CSS = str_replace("(({$button_type}_button_bg_color))", $settings['bg_color'], $CSS);
            if ($button_type == 'primary') $CSS = str_replace("(({$button_type}_button_bg_color_2))", $settings['bg_color_2'], $CSS);
            $CSS = str_replace("(({$button_type}_button_text_color))", $settings['text_color'], $CSS);
            $CSS = str_replace("(({$button_type}_button_border))", self::borders($settings['border']), $CSS);
            $CSS = str_replace("(({$button_type}_button_border_style))", $settings['border']['style'], $CSS);
            $CSS = str_replace("(({$button_type}_button_border_color))", $settings['border']['color'], $CSS);
            $CSS = str_replace("(({$button_type}_button_border_radius))", $settings['border_radius'], $CSS);
            $CSS = str_replace("(({$button_type}_button_hover_bg_color))", $settings['hover_bg_color'] ?? '', $CSS);
            $CSS = str_replace("(({$button_type}_button_hover_text_color))", $settings['hover_text_color'] ?? '', $CSS);
            $CSS = str_replace("(({$button_type}_button_border_hover))", self::borders($settings['border_hover']), $CSS);
            $CSS = str_replace("(({$button_type}_button_border_hover_style))", $settings['border_hover']['style'] ?? '', $CSS);
            $CSS = str_replace("(({$button_type}_button_border_hover_color))", $settings['border_hover']['color'] ?? '', $CSS);
        }

        return $CSS;
    }

    /**
     * Returns the formatted border CSS for the given border settings.
     *
     * @param array $border The border settings containing top, right, bottom, and left values.
     * @return string The border CSS string combining the four sides of the border.
     */
    private static function borders(array $border): string
    {
        return sprintf('%s %s %s %s', $border['top'], $border['right'], $border['bottom'], $border['left']);
    }
}
