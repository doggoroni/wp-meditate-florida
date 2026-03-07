<?php

class LSDRC_Settings_Colors_Buttons extends LSDRC_Settings
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
                'title' => esc_html__('Buttons', 'listdomer-core'),
                'id' => 'buttons-colors',
                'desc' => esc_html__('Button color settings.', 'listdomer-core'),
                'subsection' => true,
                'fields' => array_merge(
                    $this->get_primary_button_colors(),
                    $this->get_secondary_button_colors()
                ),
            ]
        );
    }

    /**
     * Returns the fields for the colors settings.
     * @return array Fields array.
     */
    private function get_primary_button_colors(): array
    {
        // Primary Buttons
        return [
            [
                'id' => 'listdomer_primary_buttons',
                'type' => 'section',
                'title' => esc_html__('Primary Buttons', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_primary_button_bg_color',
                'type' => 'color',
                'title' => esc_html__('Background Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the background color of the buttons.', 'listdomer-core'),
                'default' => '#33c6ff',
            ],
            [
                'id' => 'listdomer_primary_button_bg_color_2',
                'type' => 'color',
                'title' => esc_html__('Background Color 2', 'listdomer-core'),
                'subtitle' => esc_html__('Set the background color of the buttons.', 'listdomer-core'),
                'default' => '#306be6',
            ],
            [
                'id' => 'listdomer_primary_button_text_color',
                'type' => 'color',
                'title' => esc_html__('Text Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the text color of the buttons.', 'listdomer-core'),
                'default' => '#fff',
            ],
            [
                'id' => 'listdomer_primary_button_hover_bg_color',
                'type' => 'color',
                'title' => esc_html__('Hover Background Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border color of the buttons.', 'listdomer-core'),
                'default' => '#306be6',
            ],
            [
                'id' => 'listdomer_primary_button_hover_text_color',
                'type' => 'color',
                'title' => esc_html__('Hover Text Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border color of the buttons.', 'listdomer-core'),
                'default' => '#fff',
            ],
            [
                'id' => 'listdomer_primary_button_border',
                'type' => 'border',
                'title' => esc_html__('Button Border', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border style, width, and color for the primary button.', 'listdomer-core'),
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
                'id' => 'listdomer_primary_button_border_hover',
                'type' => 'border',
                'title' => esc_html__('Button Border Hover', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border style, width, and color for the primary button on hover.', 'listdomer-core'),
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
                'id' => 'listdomer_primary_button_border_radius',
                'type' => 'slider',
                'title' => esc_html__('Button Border Radius', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border radius of the primary button.', 'listdomer-core'),
                'default' => 4,
                'min' => 0,
                'max' => 50,
                'step' => 1,
            ],
        ];
    }

    private function get_secondary_button_colors(): array
    {
        // Secondary Buttons
        return [
            [
                'id' => 'listdomer_secondary_buttons',
                'type' => 'section',
                'title' => esc_html__('Secondary Buttons', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_secondary_button_bg_color',
                'type' => 'color',
                'title' => esc_html__('Background Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the background color of the buttons.', 'listdomer-core'),
                'default' => '#e6f7ff',
            ],
            [
                'id' => 'listdomer_secondary_button_text_color',
                'type' => 'color',
                'title' => esc_html__('Text Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the text color of the buttons.', 'listdomer-core'),
                'default' => '#0ab0fe',
            ],
            [
                'id' => 'listdomer_secondary_button_hover_bg_color',
                'type' => 'color',
                'title' => esc_html__('Hover Background Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border color of the buttons.', 'listdomer-core'),
                'default' => '#306be6',
            ],
            [
                'id' => 'listdomer_secondary_button_hover_text_color',
                'type' => 'color',
                'title' => esc_html__('Hover Text Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border color of the buttons.', 'listdomer-core'),
                'default' => '#fff',
            ],
            [
                'id' => 'listdomer_secondary_button_border',
                'type' => 'border',
                'title' => esc_html__('Button Border', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border style, width, and color for the secondary button.', 'listdomer-core'),
                'all' => true,
                'default' => [
                    'border-top' => '1px',
                    'border-right' => '1px',
                    'border-bottom' => '1px',
                    'border-left' => '1px',
                    'border-style' => 'solid',
                    'border-color' => '#bceaff',
                ],
            ],
            [
                'id' => 'listdomer_secondary_button_border_hover',
                'type' => 'border',
                'title' => esc_html__('Button Border Hover', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border style, width, and color for the secondary button on hover.', 'listdomer-core'),
                'all' => true,
                'default' => [
                    'border-top' => '1px',
                    'border-right' => '1px',
                    'border-bottom' => '1px',
                    'border-left' => '1px',
                    'border-style' => 'solid',
                    'border-color' => '#bceaff',
                ],
            ],
            [
                'id' => 'listdomer_secondary_button_border_radius',
                'type' => 'slider',
                'title' => esc_html__('Button Border Radius', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border radius of the secondary button.', 'listdomer-core'),
                'default' => 4,
                'min' => 0,
                'max' => 50,
                'step' => 1,
            ],
        ];
    }
}
