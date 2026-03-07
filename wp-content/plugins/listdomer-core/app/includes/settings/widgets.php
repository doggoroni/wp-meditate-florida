<?php

class LSDRC_Settings_Widgets extends LSDRC_Settings
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
                'title' => esc_html__('Widgets', 'listdomer-core'),
                'id' => 'listdomer-widgets',
                'desc' => esc_html__('Widget settings.', 'listdomer-core'),
                'fields' => $this->get_widget_fields(),
            ]
        );
    }

    /**
     * Returns the fields for the Widget settings.
     * @return array Fields array.
     */
    private function get_widget_fields(): array
    {
        return [
            [
                'id' => 'listdomer_widget_bg_color',
                'type' => 'color',
                'title' => esc_html__('Background Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the background color of the widget.', 'listdomer-core'),
                'default' => '#ffffff',
            ],
            [
                'id' => 'listdomer_widget_title_text_color',
                'type' => 'color',
                'title' => esc_html__('Title Text Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the text color.', 'listdomer-core'),
                'default' => '#000',
            ],
            [
                'id' => 'listdomer_widget_title_border_color',
                'type' => 'color',
                'title' => esc_html__('Title Border Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border color.', 'listdomer-core'),
                'default' => '#ececec',
            ],
            [
                'id' => 'listdomer_widget_content_text_color',
                'type' => 'color',
                'title' => esc_html__('Content Text Color', 'listdomer-core'),
                'subtitle' => esc_html__('Set the content text color.', 'listdomer-core'),
                'default' => '#8d919c',
            ],
            [
                'id' => 'listdomer_widget_border_radius',
                'type' => 'slider',
                'title' => esc_html__('Border Radius', 'listdomer-core'),
                'subtitle' => esc_html__('Set the border radius of the widget container.', 'listdomer-core'),
                'default' => 5,
                'min' => 0,
                'max' => 50,
                'step' => 1,
            ],
        ];
    }
}
