<?php

class LSDRC_Settings_General extends LSDRC_Settings
{
    /**
     * Set general settings section for Redux.
     * @return void
     */
    public function register()
    {
        Redux::set_section(
            $this->opt_name,
            [
                'title' => esc_html__('General Settings', 'listdomer-core'),
                'id' => 'listdomer-general-settings',
                'desc' => esc_html__('General settings for the theme.', 'listdomer-core'),
                'customizer_width' => '400px',
                'icon' => 'el el-cogs',
                'fields' => $this->get_general_fields(),
            ]
        );
    }

    /**
     * Returns the fields for the general settings.
     * @return array Fields array.
     */
    private function get_general_fields(): array
    {
        return [
            [
                'id' => 'site_logo',
                'type' => 'media',
                'title' => esc_html__('Site Logo', 'listdomer-core'),
                'desc' => '<p>' . esc_html__('Upload a logo for the site.', 'listdomer-core') . '</p>',
                'default' => '',
            ],
        ];
    }
}
