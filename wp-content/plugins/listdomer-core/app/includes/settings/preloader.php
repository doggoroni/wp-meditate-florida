<?php

class LSDRC_Settings_PreLoader extends LSDRC_Settings
{
    /**
     * Sets the preloader section and fields in Redux Framework.
     * @return void
     */
    public function register()
    {
        Redux::set_section(
            $this->opt_name,
            [
                'title' => esc_html__('PreLoader', 'listdomer-core'),
                'id' => 'listdomer-preloader',
                'desc' => esc_html__('Settings preloader.', 'listdomer-core'),
                'customizer_width' => '400px',
                'icon' => 'el el-repeat',
                'fields' => $this->get_preloader_fields(),
            ]
        );
    }

    /**
     * Returns the fields for the preloader settings.
     * @return array Fields array.
     */
    private function get_preloader_fields(): array
    {
        $icon = plugins_url() . '/' . LSDRC_DIRNAME . '/assets/img/pre-loader.svg';

        return [
            [
                'id' => 'listdomer_activate_preloader',
                'type' => 'switch',
                'title' => esc_html__('Display Preloader', 'listdomer-core'),
                'default' => false,
                'on' => esc_html__('Show', 'listdomer-core'),
                'off' => esc_html__('Hide', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_preloader_loading_style',
                'type' => 'select',
                'title' => esc_html__('Select Loading Style', 'listdomer-core'),
                'options' => [
                    'none' => esc_html__('None', 'listdomer-core'),
                    'spinner' => esc_html__('Spinner', 'listdomer-core'),
                    'multi-spinner' => esc_html__('Multi Spinner', 'listdomer-core'),
                    'bars' => esc_html__('Bars', 'listdomer-core'),
                    'circle' => esc_html__('Circle', 'listdomer-core'),
                    'pulse' => esc_html__('Pulse', 'listdomer-core'),
                    'dots' => esc_html__('Dots', 'listdomer-core'),
                ],
                'default' => 'none',
                'required' => [
                    ['listdomer_activate_preloader', '=', '1'],
                ],
            ],
            [
                'id' => 'listdomer_preloader_icon',
                'type' => 'media',
                'title' => esc_html__('Icon', 'listdomer-core'),
                'subtitle' => esc_html__('Upload an icon for the pre-loader.', 'listdomer-core'),
                'default' => [
                    'url' => $icon,
                ],
                'required' => [
                    ['listdomer_activate_preloader', '=', '1'],
                ],
            ],
        ];
    }
}
