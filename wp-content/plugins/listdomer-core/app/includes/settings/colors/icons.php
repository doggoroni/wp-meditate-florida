<?php

class LSDRC_Settings_Colors_Icons extends LSDRC_Settings
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
                'title' => esc_html__('Icons', 'listdomer-core'),
                'id' => 'icons',
                'desc' => esc_html__('Icons color settings.', 'listdomer-core'),
                'subsection' => true,
                'fields' => [
                    [
                        'id' => 'listdomer_icons_bg_color',
                        'type' => 'color',
                        'title' => esc_html__('Background Color', 'listdomer-core'),
                        'default' => '#e6f7ff',
                    ],
                    [
                        'id' => 'listdomer_icons_text_color',
                        'type' => 'color',
                        'title' => esc_html__('Text Color', 'listdomer-core'),
                        'default' => get_theme_mod('listdomer_second_bg_color', '#0ab0fe'),
                    ],
                ],
            ]
        );
    }
}
