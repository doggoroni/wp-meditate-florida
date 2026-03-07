<?php

class LSDRC_Settings_Colors extends LSDRC_Settings
{
    /**
     * Set colors settings section for Redux.
     * @return void
     */
    public function register()
    {
        Redux::set_section(
            $this->opt_name,
            [
                'title' => esc_html__('Colors and Styles', 'listdomer-core'),
                'id' => 'listdomer-colors-styles',
                'customizer_width' => '400px',
                'icon' => 'el el-tint',
            ]
        );

        // Submenu Section: Colors
        Redux::set_section(
            $this->opt_name,
            [
                'title' => esc_html__('Colors', 'listdomer-core'),
                'id' => 'listdomer-colors',
                'desc' => esc_html__('Settings for General Styles.', 'listdomer-core'),
                'subsection' => true,
                'fields' => $this->get_color_fields(),
            ]
        );

        // (new LSDRC_Settings_Colors_Styles())->register();
        (new LSDRC_Settings_Colors_Buttons())->register();
    }

    /**
     * Returns the fields for the colors settings.
     * @return array Fields array.
     */
    private function get_color_fields(): array
    {
        return [
            [
                'id' => 'listdomer_body_bg_color',
                'type' => 'color',
                'title' => esc_html__('Background Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_body_bg_color', '#e8f1ff'),
                'desc' => esc_html__('Change Main Background color.', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_g_color1',
                'type' => 'color',
                'title' => esc_html__('Primary Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_g_color1', '#33c6ff'),
                'desc' => esc_html__('Change gradient first color.', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_g_color2',
                'type' => 'color',
                'title' => esc_html__('Secondary Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_g_color2', '#306be6'),
                'desc' => esc_html__('Change gradient second color.', 'listdomer-core'),
            ],
        ];
    }
}
