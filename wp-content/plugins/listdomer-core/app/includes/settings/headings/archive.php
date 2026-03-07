<?php

class LSDRC_Settings_Headings_Archive extends LSDRC_Settings
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
                'title' => esc_html__('Archive', 'listdomer-core'),
                'id' => 'headings-archive',
                'subsection' => true,
                'fields' => $this->get_headings_archive(),
            ]
        );
    }

    /**
     * Returns the fields for the headings settings.
     * @return array Fields array.
     */
    private function get_headings_archive(): array
    {
        return [
            // Archive Page title Settings
            [
                'id' => 'listdomer_page_title_taxonomies',
                'type' => 'checkbox',
                'title' => '',
                'subtitle' => esc_html__('Select taxonomies where the page title should be displayed.', 'listdomer-core'),
                'options' => [
                    'categories' => esc_html__('Categories', 'listdomer-core'),
                    'tags' => esc_html__('Tags', 'listdomer-core'),
                    'author' => esc_html__('Author', 'listdomer-core'),
                ],
                'default' => [
                    'categories' => true,
                    'tags' => true,
                    'author' => true,
                ],
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
            ],
            [
                'id' => 'listdomer_page_title_archive_display',
                'type' => 'switch',
                'title' => esc_html__('Archive Title', 'listdomer-core'),
                'default' => true,
                'on' => esc_html__('Enable', 'listdomer-core'),
                'off' => esc_html__('Disable', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
            ],
            [
                'id' => 'listdomer_breadcrumb_display',
                'type' => 'switch',
                'title' => esc_html__('Archive Breadcrumb', 'listdomer-core'),
                'default' => false,
                'on' => esc_html__('Enable', 'listdomer-core'),
                'off' => esc_html__('Disable', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
            ],
            [
                'id' => 'listdomer_page_title_include_context',
                'type' => 'switch',
                'title' => esc_html__('Include Context in Title', 'listdomer-core'),
                'subtitle' => esc_html__('Show prefix like "Category:", "Tag:", or "Author:" before the archive title.', 'listdomer-core'),
                'default' => true,
                'on' => esc_html__('Yes', 'listdomer-core'),
                'off' => esc_html__('No', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
            ],

            // archive page description Settings
            [
                'id' => 'listdomer_page_desc_archive_position',
                'type' => 'select',
                'title' => esc_html__('Archive Description Position', 'listdomer-core'),
                'options' => [
                    'disable' => esc_html__('Disable', 'listdomer-core'),
                    'before' => esc_html__('Inside Page Header Section', 'listdomer-core'),
                    'after' => esc_html__('After Content', 'listdomer-core'),
                ],
                'default' => 'before',
            ],
            [
                'id' => 'listdomer_page_desc_archive_limit_enable',
                'type' => 'switch',
                'title' => esc_html__('Description Length', 'listdomer-core'),
                'default' => false,
                'on' => esc_html__('Limited', 'listdomer-core'),
                'off' => esc_html__('Full Text', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_desc_archive_position', '!=', 'disable'],
                ],
            ],
            [
                'id' => 'listdomer_page_desc_archive_limit',
                'type' => 'spinner',
                'title' => esc_html__('Description Character Limit', 'listdomer-core'),
                'subtitle' => esc_html__('Set the maximum number of characters to show before "Show More".', 'listdomer-core'),
                'default' => 300,
                'min' => 50,
                'step' => 10,
                'max' => 100000,
                'required' => [
                    ['listdomer_page_desc_archive_position', '!=', 'disable'],
                    ['listdomer_page_desc_archive_limit_enable', '=', true],
                ],
            ],
        ];
    }
}
