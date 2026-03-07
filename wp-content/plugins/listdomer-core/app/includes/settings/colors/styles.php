<?php

class LSDRC_Settings_Colors_Styles extends LSDRC_Settings
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
                'title' => esc_html__('Styles', 'listdomer-core'),
                'id' => 'styles',
                'desc' => esc_html__('Settings for General Styles.', 'listdomer-core'),
                'subsection' => true,
                'fields' => $this->get_styles(),
            ]
        );
    }

    /**
     * Returns the fields for the colors settings.
     * @return array Fields array.
     */
    /**
     * Returns the fields for the styles settings.
     * @return array Fields array.
     */
    private function get_styles(): array
    {
        return [
            [
                'id' => 'rounding',
                'type' => 'select',
                'title' => esc_html__('Rounding', 'listdomer-core'),
                'subtitle' => esc_html__('Choose a rounding option for general elements.', 'listdomer-core'),
                'options' => [
                    'none' => esc_html__('None', 'listdomer-core'),
                    'small' => esc_html__('Small (4px)', 'listdomer-core'),
                    'medium' => esc_html__('Medium (8px)', 'listdomer-core'),
                    'large' => esc_html__('Large (16px)', 'listdomer-core'),
                ],
                'default' => 'medium',
            ],
            [
                'id' => 'custom_rounding',
                'type' => 'slider',
                'title' => esc_html__('Custom Rounding', 'listdomer-core'),
                'subtitle' => esc_html__('Set a custom rounding value for finer control over the element corners.', 'listdomer-core'),
                'default' => 8,
                'min' => 0,
                'max' => 50,
                'step' => 1,
                'display_value' => 'label',
            ],
        ];
    }
}
