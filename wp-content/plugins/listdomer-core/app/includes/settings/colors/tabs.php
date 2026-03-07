<?php

class LSDRC_Settings_Colors_Tabs extends LSDRC_Settings
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
                'title' => esc_html__('Tabs', 'listdomer-core'),
                'id' => 'tabs-colors',
                'desc' => esc_html__('Tabs color settings.', 'listdomer-core'),
                'subsection' => true,
                'fields' => $this->get_tabs_colors(),
            ]
        );
    }

    private function get_tabs_colors(): array
    {
        // Solid Tabss
        return [
            [
                'id' => 'listdomer_tabs_bg_color',
                'type' => 'color',
                'title' => esc_html__('Background Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the background color of the tabs.', 'listdomer-core'),
                'default' => '#fff',
            ],
            [
                'id' => 'listdomer_tabs_text_color',
                'type' => 'color',
                'title' => esc_html__('Text Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the text color of the tabs.', 'listdomer-core'),
                'default' => '#0ab0fe',
            ],
            [
                'id' => 'listdomer_tabs_hover_bg_color',
                'type' => 'color',
                'title' => esc_html__('Hover Background Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border color of the tabs.', 'listdomer-core'),
                'default' => '#0ab0fe',
            ],
            [
                'id' => 'listdomer_tabs_hover_text_color',
                'type' => 'color',
                'title' => esc_html__('Hover Text Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border color of the tabs.', 'listdomer-core'),
                'default' => '#fff',
            ],
            [
                'id' => 'listdomer_tabs_border',
                'type' => 'border',
                'title' => esc_html__('Tabs Border', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border style, width, and color for the tabs.', 'listdomer-core'),
                'all' => true,
                'default' => [
                    'border-top' => '0',
                    'border-right' => '0',
                    'border-bottom' => '0',
                    'border-left' => '0',
                    'border-style' => 'none',
                    'border-color' => '#fff',
                ],
            ],
            [
                'id' => 'listdomer_tabs_border_hover',
                'type' => 'border',
                'title' => esc_html__('Tabs Border Hover', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border style, width, and color for the tabs on hover.', 'listdomer-core'),
                'all' => true,
                'default' => [
                    'border-top' => '0',
                    'border-right' => '0',
                    'border-bottom' => '0',
                    'border-left' => '0',
                    'border-style' => 'none',
                    'border-color' => '#fff',
                ],

            ],
            [
                'id' => 'listdomer_tabs_border_radius',
                'type' => 'slider',
                'title' => esc_html__('Tabs Border Radius', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border radius of the tabs.', 'listdomer-core'),
                'default' => 4,
                'min' => 0,
                'max' => 50,
                'step' => 1,
            ],
        ];
    }
}
