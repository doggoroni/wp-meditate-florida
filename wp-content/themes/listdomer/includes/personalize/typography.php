<?php

class LSDR_Personalize_Typography extends LSDR_Personalize
{
    /**
     * Generates personalized font settings and applies them to the CSS.
     *
     * @param string|null $CSS The raw CSS content. If null, loads the default CSS.
     * @return string Modified CSS with personalized fonts.
     */
    public static function generate(string $CSS = null): string
    {
        if (!$CSS) $CSS = file_get_contents(get_template_directory() . '/assets/css/personalized-fonts.raw');

        $fonts_settings = [
            'main' => self::fonts('listdomer_main_font', [
                'font-family' => 'Poppins',
                'font-size' => '16px',
                'font-weight' => '400',
                'line-height' => '24px',
                'font-style' => 'normal',
                'text-align' => 'left',
                'color' => '#7e8086',
            ]),
            'archive_post_title' => self::fonts('listdomer_archive_post_title_typography', [
                'font-size' => '21px',
                'font-family' => 'Poppins',
                'font-weight' => '600',
                'font-style' => 'normal',
                'line-height' => '44px',
                'color' => '#000',
                'text-align' => 'left',
            ]),
            'h1' => self::fonts('listdomer_h1_font', [
                'font-size' => '32px',
                'font-family' => 'Poppins',
                'font-weight' => '700',
                'line-height' => '40px',
                'font-style' => 'normal',
                'text-align' => 'left',
                'color' => '#000',
            ]),
            'h2' => self::fonts('listdomer_h2_font', [
                'font-size' => '28px',
                'font-family' => 'Poppins',
                'font-weight' => '600',
                'line-height' => '36px',
                'font-style' => 'normal',
                'text-align' => 'left',
                'color' => '#000',
            ]),
            'h3' => self::fonts('listdomer_h3_font', [
                'font-size' => '24px',
                'font-family' => 'Poppins',
                'font-weight' => '500',
                'line-height' => '32px',
                'font-style' => 'normal',
                'text-align' => 'left',
                'color' => '#000',
            ]),
            'h4' => self::fonts('listdomer_h4_font', [
                'font-size' => '20px',
                'font-family' => 'Poppins',
                'font-weight' => '500',
                'line-height' => '28px',
                'font-style' => 'normal',
                'text-align' => 'left',
                'color' => '#000',
            ]),
            'h5' => self::fonts('listdomer_h5_font', [
                'font-size' => '18px',
                'font-family' => 'Poppins',
                'font-weight' => '400',
                'line-height' => '26px',
                'font-style' => 'normal',
                'text-align' => 'left',
                'color' => '#000',
            ]),
            'h6' => self::fonts('listdomer_h6_font', [
                'font-size' => '16px',
                'font-family' => 'Poppins',
                'font-weight' => '400',
                'line-height' => '24px',
                'font-style' => 'normal',
                'text-align' => 'left',
                'color' => '#000',
            ]),
        ];

        return self::replacement($CSS, $fonts_settings);
    }

    /**
     * Retrieves and sanitizes font settings for a given key.
     *
     * @param string $key The setting key.
     * @param array $default Default font settings.
     * @return array Sanitized font settings.
     */
    private static function fonts(string $key, array $default): array
    {
        $font = LSDR_Settings::get($key, $default);

        return [
            'font-family' => sanitize_text_field($font['font-family'] ?? $default['font-family']),
            'font-size' => sanitize_text_field($font['font-size'] ?? $default['font-size']),
            'font-weight' => sanitize_text_field($font['font-weight'] ?? $default['font-weight']),
            'font-style' => !empty($font['font-style'])
                ? sanitize_text_field($font['font-style'])
                : sanitize_text_field($default['font-style']),
            'text-align' => sanitize_text_field($font['text-align'] ?? $default['text-align']),
            'line-height' => sanitize_text_field($font['line-height'] ?? $default['line-height']),
            'color' => sanitize_text_field($font['color'] ?? $default['color']),
        ];
    }

    /**
     * Replaces font settings placeholders in the CSS.
     *
     * @param string $CSS The raw CSS content.
     * @param array $fonts_settings The font settings to apply.
     * @return string Modified CSS.
     */
    private static function replacement(string $CSS, array $fonts_settings): string
    {
        foreach ($fonts_settings as $type => $settings)
        {
            $CSS = str_replace("(({$type}_font))", $settings['font-family'], $CSS);
            $CSS = str_replace("(({$type}_font_size))", $settings['font-size'], $CSS);
            $CSS = str_replace("(({$type}_font_weight))", $settings['font-weight'], $CSS);
            $CSS = str_replace("(({$type}_font_style))", $settings['font-style'], $CSS);
            $CSS = str_replace("(({$type}_text_align))", $settings['text-align'], $CSS);
            $CSS = str_replace("(({$type}_line_height))", $settings['line-height'], $CSS);
            $CSS = str_replace("(({$type}_color))", $settings['color'], $CSS);
        }

        return $CSS;
    }
}
