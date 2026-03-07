<?php

class LSDRC_Settings_Headings_Listings extends LSDRC_Settings
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
                'title' => esc_html__('Listings Archive', 'listdomer-core'),
                'id' => 'headings-listings',
                'subsection' => true,
                'fields' => $this->get_headings_listings(),

            ]
        );
    }

    /**
     * Returns the fields for the headings settings.
     * @return array Fields array.
     */
    private function get_headings_listings(): array
    {
        return [
            // Listing Page title Settings
            [
                'id' => 'listdomer_page_title_listing_taxonomies',
                'type' => 'checkbox',
                'title' => '',
                'subtitle' => esc_html__('Select taxonomies where the page header should be displayed.', 'listdomer-core'),
                'options' => [
                    'categories' => esc_html__('Categories', 'listdomer-core'),
                    'locations' => esc_html__('Locations', 'listdomer-core'),
                    'labels' => esc_html__('Labels', 'listdomer-core'),
                    'features' => esc_html__('Features', 'listdomer-core'),
                    'tags' => esc_html__('Tags', 'listdomer-core'),
                ],
                'default' => [
                    'categories' => true,
                    'locations' => true,
                    'labels' => true,
                    'features' => true,
                    'tags' => true,
                ],
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
            ],
            [
                'id' => 'listdomer_page_title_listing_archive_display',
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
                'id' => 'listdomer_listing_breadcrumb_display',
                'type' => 'switch',
                'title' => esc_html__('Archive Breadcrumb', 'listdomer-core'),
                'default' => false,
                'on' => esc_html__('Enable', 'listdomer-core'),
                'off' => esc_html__('Disable', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
            ],

            // Listing page description Settings
            [
                'id' => 'listdomer_page_desc_listing_archive_position',
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
                'id' => 'listdomer_page_desc_listing_archive_limit_enable',
                'type' => 'switch',
                'title' => esc_html__('Description Length', 'listdomer-core'),
                'default' => false,
                'on' => esc_html__('Limited', 'listdomer-core'),
                'off' => esc_html__('Full Text', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_desc_listing_archive_position', '!=', 'disable'],
                ],
            ],
            [
                'id' => 'listdomer_page_desc_listing_archive_limit',
                'type' => 'spinner',
                'title' => esc_html__('Description Character Limit', 'listdomer-core'),
                'subtitle' => esc_html__('Set the maximum number of characters to show before "Show More".', 'listdomer-core'),
                'default' => 300,
                'min' => 50,
                'step' => 10,
                'max' => 100000,
                'required' => [
                    ['listdomer_page_desc_listing_archive_position', '!=', 'disable'],
                    ['listdomer_page_desc_listing_archive_limit_enable', '=', true],
                ],
            ],
        ];
    }
}
