<?php

class LSDR_Personalize_Forms extends LSDR_Personalize
{
    /**
     * Generates personalized form styles and applies them to the CSS.
     *
     * @param string|null $CSS The raw CSS content. If null, loads the default CSS.
     * @return string Modified CSS with personalized form styles.
     */
    public static function generate(string $CSS = ''): string
    {
        if (!$CSS) $CSS = file_get_contents(get_template_directory() . '/assets/css/personalized.txt');

        $forms_settings = [
            'comment' => self::forms('comment'),
        ];

        return self::replacement($CSS, $forms_settings);
    }

    /**
     * Retrieves and sanitizes form input settings for a given form type.
     *
     * @param string $form_type The type of form (e.g., 'comment').
     * @return array Sanitized form input settings.
     */
    private static function forms(string $form_type): array
    {
        $prefix = "listdomer_{$form_type}_input";

        return [
            'bg_color' => sanitize_text_field(LSDR_Settings::get("{$prefix}_bg_color", '#ffffff')),
            'text_color' => sanitize_text_field(LSDR_Settings::get("{$prefix}_text_color", '#a4a8b5')),
            'placeholder_text_color' => sanitize_text_field(LSDR_Settings::get("{$prefix}_placeholder_text_color", '#a4a8b5')),
            'radius' => sanitize_text_field(LSDR_Settings::get("{$prefix}_border_radius", '5')) . 'px',
            'border' => self::get_border("{$prefix}_border"),
            'hover_border' => self::get_border("{$prefix}_hover_border"),
        ];
    }

    /**
     * Retrieves and sanitizes border settings for the input fields.
     *
     * @param string $prefix The prefix used to fetch the border settings.
     * @return array An associative array containing the border settings.
     */
    private static function get_border(string $prefix): array
    {
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
     * Replaces form settings placeholders in the CSS with actual values.
     *
     * @param string $CSS The raw CSS content.
     * @param array $forms_settings The form settings to apply.
     * @return string Modified CSS.
     */
    private static function replacement(string $CSS, array $forms_settings): string
    {
        foreach ($forms_settings as $form_type => $settings)
        {
            foreach ($settings as $key => $value)
            {
                if($key === 'border' || $key === 'hover_border') {
                    $CSS = str_replace("(({$form_type}_input_border))", self::borders($settings['border']), $CSS);
                    $CSS = str_replace("(({$form_type}_input_border_style))", $settings['border']['style'], $CSS);
                    $CSS = str_replace("(({$form_type}_input_border_color))", $settings['border']['color'], $CSS);
                    $CSS = str_replace("(({$form_type}_input_hover_border))", self::borders($settings['hover_border']), $CSS);
                    $CSS = str_replace("(({$form_type}_input_hover_border_style))", $settings['hover_border']['style'] ?? '', $CSS);
                    $CSS = str_replace("(({$form_type}_input_hover_border_color))", $settings['hover_border']['color'] ?? '', $CSS);
                    continue;
                }
                $placeholder = "(({$form_type}_input_$key))";
                $CSS = str_replace($placeholder, $value, $CSS);
            }
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
