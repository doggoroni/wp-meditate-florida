<?php

class LSDRC_Settings_Headings_Pages extends LSDRC_Settings
{
    /**
     * Set settings section for Redux.
     * @return void
     */
    public function register()
    {
        Redux::set_section(
            $this->opt_name,
            [
                'title' => esc_html__('Pages', 'listdomer-core'),
                'id' => 'headings-pages',
                'subsection' => true,
                'fields' => $this->get_headings_pages(),
            ]
        );
    }

    /**
     * Returns the fields for the headings settings.
     * @return array Fields array.
     */
    private function get_headings_pages(): array
    {
        return [
            // Pages Page title Settings
            [
                'id' => 'listdomer_page_title_pages_display',
                'type' => 'switch',
                'title' => esc_html__('Page Title', 'listdomer-core'),
                'default' => true,
                'on' => esc_html__('Enable', 'listdomer-core'),
                'off' => esc_html__('Disable', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
            ],
            [
                'id' => 'listdomer_pages_breadcrumb_display',
                'type' => 'switch',
                'title' => esc_html__('Page Breadcrumb', 'listdomer-core'),
                'default' => false,
                'on' => esc_html__('Enable', 'listdomer-core'),
                'off' => esc_html__('Disable', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
            ],
            // pages page description Settings
            [
                'id' => 'listdomer_page_desc_pages_position',
                'type' => 'select',
                'title' => esc_html__('Page Description Position', 'listdomer-core'),
                'options' => [
                    'disable' => esc_html__('Disable', 'listdomer-core'),
                    'before' => esc_html__('Inside Page Header Section', 'listdomer-core'),
                    'after' => esc_html__('After Content', 'listdomer-core'),
                ],
                'default' => 'disable',
            ],
            [
                'id' => 'listdomer_page_desc_pages_limit_enable',
                'type' => 'switch',
                'title' => esc_html__('Description Length', 'listdomer-core'),
                'default' => false,
                'on' => esc_html__('Limited', 'listdomer-core'),
                'off' => esc_html__('Full Text', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_desc_pages_position', '!=', 'disable'],
                ],
            ],
            [
                'id' => 'listdomer_page_desc_pages_limit',
                'type' => 'spinner',
                'title' => esc_html__('Description Character Limit', 'listdomer-core'),
                'subtitle' => esc_html__('Set the maximum number of characters to show before "Show More".', 'listdomer-core'),
                'default' => 300,
                'min' => 50,
                'step' => 10,
                'max' => 100000,
                'required' => [
                    ['listdomer_page_desc_pages_position', '!=', 'disable'],
                    ['listdomer_page_desc_pages_limit_enable', '=', true],
                ],
            ],
        ];
    }
}
