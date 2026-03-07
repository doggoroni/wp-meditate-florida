<?php

class LSDR_Personalize_Headings extends LSDR_Personalize
{
    /**
     * Generates personalized heading styles and applies them to the CSS.
     *
     * @param string|null $CSS The raw CSS content. If null, loads the default CSS.
     * @return string Modified CSS with personalized heading styles.
     */
    public static function generate(string $CSS = ''): string
    {
        if (!$CSS) $CSS = file_get_contents(get_template_directory() . '/assets/css/personalized-headings.raw');

        // Typography
        $typography = LSDR_Settings::get('listdomer_page_title_typography', []);
        $desc_typography = LSDR_Settings::get('listdomer_page_title_description_typography', []);

        $heading_settings = [
            // Typography settings
            'page_title_font_family' => sanitize_text_field($typography['font-family'] ?? 'Poppins'),
            'page_title_font_size' => sanitize_text_field($typography['font-size'] ?? '42px'),
            'page_title_font_weight' => sanitize_text_field($typography['font-weight'] ?? '600'),
            'page_title_font_style' => !empty($typography['font-style'])
                ? sanitize_text_field($typography['font-style'])
                : 'normal',
            'page_title_alignment' => sanitize_text_field($typography['text-align'] ?? 'center'),
            'page_title_line_height' => sanitize_text_field($typography['line-height'] ?? '46px'),
            'page_title_color' => sanitize_text_field($typography['color'] ?? '#000'),

            // Typography settings
            'page_desc_font_family' => sanitize_text_field($desc_typography['font-family'] ?? 'Poppins'),
            'page_desc_font_size' => sanitize_text_field($desc_typography['font-size'] ?? '21px'),
            'page_desc_font_weight' => sanitize_text_field($desc_typography['font-weight'] ?? '400'),
            'page_desc_font_style' => !empty($desc_typography['font-style'])
                ? sanitize_text_field($desc_typography['font-style'])
                : 'normal',
            'page_desc_alignment' => sanitize_text_field($desc_typography['text-align'] ?? 'center'),
            'page_desc_line_height' => sanitize_text_field($desc_typography['line-height'] ?? '26px'),
            'page_desc_color' => sanitize_text_field($desc_typography['color'] ?? '#66686b'),

            // Background settings
            'page_title_bg_color' => sanitize_text_field(LSDR_Settings::get('listdomer_page_title_background_color', '#e8f1ff')),
            'page_title_image' => sanitize_text_field(LSDR_Settings::get('listdomer_page_title_image', '')['url'] ?? ''),
            'page_title_bg_position' => sanitize_text_field(LSDR_Settings::get('listdomer_page_title_bg_position', 'center center')),
            'page_title_bg_repeat' => sanitize_text_field(LSDR_Settings::get('listdomer_page_title_bg_repeat', 'no-repeat')),
            'page_title_bg_size' => sanitize_text_field(LSDR_Settings::get('listdomer_page_title_bg_size', 'cover')),
            'page_title_bg_attachment' => sanitize_text_field(LSDR_Settings::get('listdomer_page_title_bg_attachment', 'scroll')),
            // Border
            'page_title_border' => self::borders(),
            'page_title_display' => LSDR_Settings::get('listdomer_page_title_display', true) ? 'block' : 'none',
            'page_title_shape_display' => LSDR_Settings::get('listdomer_page_title_shapes', true) ? 'block' : 'none',
        ];

        return self::replacement($CSS, $heading_settings);
    }

    /**
     * Retrieves and sanitizes border settings for headings.
     *
     * @return array Sanitized border settings (top, right, bottom, left, style, color).
     */
    private static function borders(): array
    {
        $border = LSDR_Settings::get('listdomer_page_title_border');

        $top = sanitize_text_field($border['border-top'] ?? '0');
        $right = sanitize_text_field($border['border-right'] ?? '0');
        $bottom = sanitize_text_field($border['border-bottom'] ?? '0');
        $left = sanitize_text_field($border['border-left'] ?? '0');
        $style = sanitize_text_field($border['border-style'] ?? 'none');
        $color = sanitize_text_field($border['border-color'] ?? '#fff');

        return [
            'border' => sprintf('%s %s %s %s', $top, $right, $bottom, $left),
            'border_style' => $style,
            'border_color' => $color,
        ];
    }

    /**
     * Replaces heading style placeholders in the CSS with actual values.
     *
     * @param string $CSS The raw CSS content.
     * @param array $heading_settings The heading settings to apply.
     * @return string Modified CSS with applied heading styles.
     */
    private static function replacement(string $CSS, array $heading_settings): string
    {
        $placeholders = [
            '((page_title_font_family))',
            '((page_title_font_size))',
            '((page_title_font_weight))',
            '((page_title_font_style))',
            '((page_title_line_height))',
            '((page_title_color))',
            '((page_title_alignment))',
            '((page_desc_font_family))',
            '((page_desc_font_size))',
            '((page_desc_font_weight))',
            '((page_desc_font_style))',
            '((page_desc_line_height))',
            '((page_desc_color))',
            '((page_desc_alignment))',
            '((page_title_bg_color))',
            '((page_title_image))',
            '((page_title_bg_position))',
            '((page_title_bg_repeat))',
            '((page_title_bg_size))',
            '((page_title_bg_attachment))',
            '((page_title_border))',
            '((page_title_border_style))',
            '((page_title_border_color))',
            '((page_title_shape_display))',
            '((page_title_display))',
        ];

        $values = [
            $heading_settings['page_title_font_family'],
            $heading_settings['page_title_font_size'],
            $heading_settings['page_title_font_weight'],
            $heading_settings['page_title_font_style'],
            $heading_settings['page_title_line_height'],
            $heading_settings['page_title_color'],
            $heading_settings['page_title_alignment'],
            $heading_settings['page_desc_font_family'],
            $heading_settings['page_desc_font_size'],
            $heading_settings['page_desc_font_weight'],
            $heading_settings['page_desc_font_style'],
            $heading_settings['page_desc_line_height'],
            $heading_settings['page_desc_color'],
            $heading_settings['page_desc_alignment'],
            $heading_settings['page_title_bg_color'],
            $heading_settings['page_title_image'],
            $heading_settings['page_title_bg_position'],
            $heading_settings['page_title_bg_repeat'],
            $heading_settings['page_title_bg_size'],
            $heading_settings['page_title_bg_attachment'],
            $heading_settings['page_title_border']['border'],
            $heading_settings['page_title_border']['border_style'],
            $heading_settings['page_title_border']['border_color'],
            $heading_settings['page_title_shape_display'],
            $heading_settings['page_title_display'],
        ];

        return str_replace($placeholders, $values, $CSS);
    }
}
