<?php

class LSDR_Personalize extends LSDR_Base
{
    public static function generate()
    {
        // General colors
        $body_bg_color = sanitize_text_field(LSDR_Settings::get('listdomer_body_bg_color', '#e8f1ff'));

        $header_bg_color = sanitize_text_field(LSDR_Settings::get('listdomer_header_bg_color', '#ffffff'));
        $header_text_color = sanitize_text_field(LSDR_Settings::get('listdomer_header_text_color', '#000000'));
        $header_hover_color = sanitize_text_field(LSDR_Settings::get('listdomer_header_hover_text_color', '#0ab0fe'));
        $header_active_color = sanitize_text_field(LSDR_Settings::get('listdomer_header_active_text_color', '#0ab0fe'));

        $content_color = sanitize_text_field(LSDR_Settings::get('listdomer_content_color', '#66686b'));

        // Single Post Title Typography
        $single_page_title_typo = LSDR_Settings::get('listdomer_single_post_title_typography', []);
        $single_page_title_color = sanitize_text_field($single_page_title_typo['color'] ?? '#000000');
        $single_page_title_font_family = sanitize_text_field($single_page_title_typo['font-family'] ?? 'Poppins');
        $single_page_title_font_size = sanitize_text_field($single_page_title_typo['font-size'] ?? '54px');
        $single_page_title_font_weight = sanitize_text_field($single_page_title_typo['font-weight'] ?? '700');
        $single_page_title_font_style = !empty($single_page_title_typo['font-style']) ? sanitize_text_field($single_page_title_typo['font-style']) : 'normal';
        $single_page_title_line_height = sanitize_text_field($single_page_title_typo['line-height'] ?? '62px');
        $single_page_title_text_align = sanitize_text_field($single_page_title_typo['text-align'] ?? 'left');

        // Single Post Content Typography
        $single_page_content_typo = LSDR_Settings::get('listdomer_single_post_content_typography', []);
        $single_page_content_color = sanitize_text_field($single_page_content_typo['color'] ?? '#66686b');
        $single_page_content_font_family = sanitize_text_field($single_page_content_typo['font-family'] ?? 'Poppins');
        $single_page_content_font_size = sanitize_text_field($single_page_content_typo['font-size'] ?? '14px');
        $single_page_content_font_weight = sanitize_text_field($single_page_content_typo['font-weight'] ?? '400');
        $single_page_content_font_style = !empty($single_page_content_typo['font-style']) ? sanitize_text_field($single_page_content_typo['font-style']) : 'normal';
        $single_page_content_line_height = sanitize_text_field($single_page_content_typo['line-height'] ?? '32px');
        $single_page_content_text_align = sanitize_text_field($single_page_content_typo['text-align'] ?? 'left');

        $content_a_color = sanitize_text_field(LSDR_Settings::get('listdomer_content_a_color', '#8d919c'));
        $content_a_active_color = sanitize_text_field(LSDR_Settings::get('listdomer_content_a_active_color', '#3a3b40'));
        $archive_a_text_decoration = sanitize_text_field(LSDR_Settings::get('listdomer_archive_post_title_decoration', 'none'));

        $single_icon_color = sanitize_text_field(LSDR_Settings::get('listdomer_single_icon_color', '#33bdfc'));
        $single_icon_color2 = sanitize_text_field(LSDR_Settings::get('listdomer_single_icon_color2', '#2b93ff'));
        $single_star_color = sanitize_text_field(LSDR_Settings::get('listdomer_single_star_color', '#fecc39'));
        $single_active_color = sanitize_text_field(LSDR_Settings::get('listdomer_single_active_color', '#a4a8b5'));
        $single_slider_button_bg = sanitize_text_field(LSDR_Settings::get('listdomer_single_slider_button_bg', '#000000'));
        $single_slider_button_color = sanitize_text_field(LSDR_Settings::get('listdomer_single_slider_button_color', '#ffffff'));

        $footer_bg_color = sanitize_text_field(LSDR_Settings::get('listdomer_footer_bg_color', '#1c1c1c'));
        $footer_text_color = sanitize_text_field(LSDR_Settings::get('listdomer_footer_text_color', '#ffffff'));

        $g_color1 = sanitize_text_field(LSDR_Settings::get('listdomer_g_color1', '#33c6ff'));
        $g_color2 = sanitize_text_field(LSDR_Settings::get('listdomer_g_color2', '#306be6'));

        $icons_bg_color = sanitize_text_field(LSDR_Settings::get('listdomer_icons_bg_color', 'transparent'));
        if (trim($icons_bg_color) === '') $icons_bg_color = 'transparent';
        $icons_text_color = sanitize_text_field(LSDR_Settings::get('listdomer_icons_text_color', '#0ab0fe'));

        $raw = file_get_contents(get_template_directory() . '/assets/css/personalized.raw');

        $CSS = str_replace('((body_bg_color))', $body_bg_color, $raw);
        $CSS = str_replace('((header_bg_color))', $header_bg_color, $CSS);
        $CSS = str_replace('((header_text_color))', $header_text_color, $CSS);
        $CSS = str_replace('((header_hover_color))', $header_hover_color, $CSS);
        $CSS = str_replace('((header_active_color))', $header_active_color, $CSS);
        $CSS = str_replace('((content_color))', $content_color, $CSS);
        $CSS = str_replace('((content_a_color))', $content_a_color, $CSS);
        $CSS = str_replace('((content_a_active_color))', $content_a_active_color, $CSS);
        $CSS = str_replace('((single_icon_color))', $single_icon_color, $CSS);
        $CSS = str_replace('((single_icon_color2))', $single_icon_color2, $CSS);
        $CSS = str_replace('((single_star_color))', $single_star_color, $CSS);
        $CSS = str_replace('((single_active_color))', $single_active_color, $CSS);
        $CSS = str_replace('((single_slider_button_bg))', $single_slider_button_bg, $CSS);
        $CSS = str_replace('((single_slider_button_color))', $single_slider_button_color, $CSS);
        $CSS = str_replace('((footer_bg_color))', $footer_bg_color, $CSS);
        $CSS = str_replace('((footer_text_color))', $footer_text_color, $CSS);
        $CSS = str_replace('((g_color1))', $g_color1, $CSS);
        $CSS = str_replace('((g_color2))', $g_color2, $CSS);
        $CSS = str_replace('((archive_post_title_decoration))', $archive_a_text_decoration, $CSS);

        $CSS = str_replace('((single_page_title_color))', $single_page_title_color, $CSS);
        $CSS = str_replace('((single_page_title_font_size))', $single_page_title_font_size, $CSS);
        $CSS = str_replace('((single_page_title_font_family))', $single_page_title_font_family, $CSS);
        $CSS = str_replace('((single_page_title_font_weight))', $single_page_title_font_weight, $CSS);
        $CSS = str_replace('((single_page_title_font_style))', $single_page_title_font_style, $CSS);
        $CSS = str_replace('((single_page_title_line_height))', $single_page_title_line_height, $CSS);
        $CSS = str_replace('((single_page_title_text_align))', $single_page_title_text_align, $CSS);

        $CSS = str_replace('((single_page_content_color))', $single_page_content_color, $CSS);
        $CSS = str_replace('((single_page_content_font_size))', $single_page_content_font_size, $CSS);
        $CSS = str_replace('((single_page_content_font_family))', $single_page_content_font_family, $CSS);
        $CSS = str_replace('((single_page_content_font_weight))', $single_page_content_font_weight, $CSS);
        $CSS = str_replace('((single_page_content_font_style))', $single_page_content_font_style, $CSS);
        $CSS = str_replace('((single_page_content_line_height))', $single_page_content_line_height, $CSS);
        $CSS = str_replace('((single_page_content_text_align))', $single_page_content_text_align, $CSS);

        // Icons
        $CSS = str_replace('((icons_bg_color))', $icons_bg_color, $CSS);
        $CSS = str_replace('((icons_text_color))', $icons_text_color, $CSS);

        $CSS = LSDR_Personalize_Forms::generate($CSS);
        $CSS = LSDR_Personalize_Typography::generate($CSS);
        $CSS = LSDR_Personalize_Buttons::generate($CSS);
        $CSS = LSDR_Personalize_Widgets::generate($CSS);
        $CSS = LSDR_Personalize_Headings::generate($CSS);

        // Blog ID
        $blog_id = get_current_blog_id();

        // Write the generated CSS file
        file_put_contents(get_template_directory() . '/assets/css/personalized' . ($blog_id > 1 ? '-' . $blog_id : '') . '.css', $CSS);
    }

    public static function assets()
    {
        // Main Font
        $font = LSDR_Settings::get('listdomer_main_font', [
            'font-family' => 'Poppins',
        ]);

        $main_font = sanitize_text_field($font['font-family'] ?? 'Poppins');

        // Include the Font
        wp_enqueue_style('google-font-' . sanitize_title($main_font), 'https://fonts.googleapis.com/css?family=' . urlencode($main_font) . ':wght@500&display=swap');

        // CSS File
        $css = get_template_directory_uri() . '/assets/css/personalized.css';

        // Blog ID
        $blog_id = get_current_blog_id();

        // Blog CSS File
        if ($blog_id > 1 && LSD_File::exists(get_template_directory() . '/assets/css/personalized-' . $blog_id . '.css')) $css = get_template_directory_uri() . '/assets/css/personalized-' . $blog_id . '.css';

        // Include Listdomer personalized CSS file
        wp_enqueue_style('listdomer-personalized', $css, ['listdomer'], LSDR_Assets::version());
    }
}
