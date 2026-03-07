<?php

class LSDRC_Settings_NotFound extends LSDRC_Settings
{
    /**
     * Sets the 404 section and fields in Redux Framework.
     * @return void
     */
    public function register()
    {
        Redux::set_section(
            $this->opt_name,
            [
                'title' => esc_html__('404 page', 'listdomer-core'),
                'id' => 'listdomer-notfound',
                'desc' => esc_html__('Settings for 404 page.', 'listdomer-core'),
                'customizer_width' => '400px',
                'icon' => 'el el-error',
                'fields' => $this->get_404_fields(),
            ]
        );
    }

    /**
     * Returns the fields for the 404 settings.
     * @return array Fields array.
     */
    private function get_404_fields(): array
    {
        return [
            [
                'id' => 'listdomer_404_image',
                'type' => 'media',
                'title' => esc_html__('Image', 'listdomer-core'),
                'subtitle' => esc_html__('Upload an image for the 404 page.', 'listdomer-core'),
                'default' => [
                    'url' => get_template_directory_uri() . '/assets/img/404.png',
                ],
            ],
            [
                'id' => 'listdomer_404_title',
                'type' => 'text',
                'title' => esc_html__('Title', 'listdomer-core'),
                'subtitle' => esc_html__('Set a title for the 404 page.', 'listdomer-core'),
                'default' => esc_html__('Oops! Error 404.', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_404_content',
                'type' => 'textarea',
                'title' => esc_html__('Content', 'listdomer-core'),
                'subtitle' => esc_html__('Set content for the 404 page.', 'listdomer-core'),
                'default' => esc_html__("We can't find the page you're looking for.", 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_404_button_text',
                'type' => 'text',
                'title' => esc_html__('Button Text', 'listdomer-core'),
                'subtitle' => esc_html__('Set text for the button on the 404 page.', 'listdomer-core'),
                'default' => esc_html__('Go to Home', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_404_button_page',
                'type' => 'select',
                'title' => esc_html__('Button Page', 'listdomer-core'),
                'subtitle' => esc_html__('Select the target page for the button on the 404 page.', 'listdomer-core'),
                'data' => 'pages',
                'default' => get_option('page_on_front'),
            ],
        ];
    }
}
