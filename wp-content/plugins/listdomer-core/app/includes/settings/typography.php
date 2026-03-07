<?php

class LSDRC_Settings_Typography extends LSDRC_Settings
{
    /**
     * Set typography settings section for Redux.
     * @return void
     */
    public function register()
    {
        Redux::set_section(
            $this->opt_name,
            [
                'title' => esc_html__('Typography', 'listdomer-core'),
                'id' => 'typography',
                'icon' => 'el el-font',
                'parent_id' => 'listdomer',
                'fields' => $this->get_typography_fields(),
            ]
        );
    }

    /**
     * Returns the fields for the typography settings.
     * @return array Fields array.
     */
    private function get_typography_fields(): array
    {
        // Main font for body text
        return [
            [
                'id' => 'listdomer_main_font',
                'type' => 'typography',
                'title' => esc_html__('Main Font', 'listdomer-core'),
                'subtitle' => esc_html__('Specify the main font properties.', 'listdomer-core'),
                'desc' => esc_html__('You can choose one of Google fonts.', 'listdomer-core'),
                'google' => true,
                'font_family_clear' => false,
                'subsets' => false,
                'default' => [
                    'font-size' => '16px',
                    'font-family' => get_theme_mod('listdomer_main_font', 'Poppins'),
                    'font-weight' => '400',
                    'line-height' => '24px',
                    'color' => get_theme_mod('listdomer_widget_color', '#7e8086'),
                ],
            ],
            [
                'id' => 'listdomer_h1_font',
                'type' => 'typography',
                'title' => esc_html__('H1 Font', 'listdomer-core'),
                'subtitle' => esc_html__('Specify the font properties for H1 tags.', 'listdomer-core'),
                'desc' => esc_html__('You can choose one of Google fonts.', 'listdomer-core'),
                'google' => true,
                'font_family_clear' => false,
                'subsets' => false,
                'default' => [
                    'font-size' => '54px',
                    'font-family' => get_theme_mod('listdomer_h1_font', 'Poppins'),
                    'font-weight' => '700',
                    'line-height' => '62px',
                    'color' => get_theme_mod('listdomer_headlines_color', '#000000'),
                ],
                'output' => ['h1'],
            ],
            [
                'id' => 'listdomer_h2_font',
                'type' => 'typography',
                'title' => esc_html__('H2 Font', 'listdomer-core'),
                'subtitle' => esc_html__('Specify the font properties for H2 tags.', 'listdomer-core'),
                'desc' => esc_html__('You can choose one of Google fonts.', 'listdomer-core'),
                'google' => true,
                'font_family_clear' => false,
                'subsets' => false,
                'default' => [
                    'font-size' => '32px',
                    'font-family' => get_theme_mod('listdomer_h2_font', 'Poppins'),
                    'font-weight' => '600',
                    'line-height' => '44px',
                    'color' => get_theme_mod('listdomer_headlines_color', '#000000'),
                ],
                'output' => ['h2'],
            ],
            [
                'id' => 'listdomer_h3_font',
                'type' => 'typography',
                'title' => esc_html__('H3 Font', 'listdomer-core'),
                'subtitle' => esc_html__('Specify the font properties for H3 tags.', 'listdomer-core'),
                'desc' => esc_html__('You can choose one of Google fonts.', 'listdomer-core'),
                'google' => true,
                'font_family_clear' => false,
                'subsets' => false,
                'default' => [
                    'font-size' => '22px',
                    'font-family' => get_theme_mod('listdomer_h3_font', 'Poppins'),
                    'font-weight' => '500',
                    'line-height' => '34px',
                    'color' => get_theme_mod('listdomer_headlines_color', '#000000'),
                ],
                'output' => ['h3'],
            ],
            [
                'id' => 'listdomer_h4_font',
                'type' => 'typography',
                'title' => esc_html__('H4 Font', 'listdomer-core'),
                'subtitle' => esc_html__('Specify the font properties for H4 tags.', 'listdomer-core'),
                'desc' => esc_html__('You can choose one of Google fonts.', 'listdomer-core'),
                'google' => true,
                'font_family_clear' => false,
                'subsets' => false,
                'default' => [
                    'font-size' => '18px',
                    'font-family' => get_theme_mod('listdomer_h4_font', 'Poppins'),
                    'font-weight' => '500',
                    'line-height' => '32px',
                    'color' => get_theme_mod('listdomer_headlines_color', '#000000'),
                ],
                'output' => ['h4'],
            ],
            [
                'id' => 'listdomer_h5_font',
                'type' => 'typography',
                'title' => esc_html__('H5 Font', 'listdomer-core'),
                'subtitle' => esc_html__('Specify the font properties for H5 tags.', 'listdomer-core'),
                'desc' => esc_html__('You can choose one of Google fonts.', 'listdomer-core'),
                'google' => true,
                'font_family_clear' => false,
                'subsets' => false,
                'default' => [
                    'font-size' => '16px',
                    'font-family' => get_theme_mod('listdomer_h5_font', 'Poppins'),
                    'font-weight' => '400',
                    'line-height' => '26px',
                    'color' => get_theme_mod('listdomer_headlines_color', '#000000'),
                ],
                'output' => ['h5'],
            ],
            [
                'id' => 'listdomer_h6_font',
                'type' => 'typography',
                'title' => esc_html__('H6 Font', 'listdomer-core'),
                'subtitle' => esc_html__('Specify the font properties for H6 tags.', 'listdomer-core'),
                'desc' => esc_html__('You can choose one of Google fonts.', 'listdomer-core'),
                'google' => true,
                'font_family_clear' => false,
                'subsets' => false,
                'default' => [
                    'font-size' => '14px',
                    'font-family' => get_theme_mod('listdomer_h6_font', 'Poppins'),
                    'font-weight' => '400',
                    'line-height' => '24px',
                    'color' => get_theme_mod('listdomer_headlines_color', '#000000'),
                ],
                'output' => ['h6'],
            ],
        ];
    }
}
