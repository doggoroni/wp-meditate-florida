<?php

class LSDRC_Settings_Search extends LSDRC_Settings
{
    /**
     * Set search settings section for Redux.
     * @return void
     */
    public function register()
    {
        Redux::set_section(
            $this->opt_name,
            [
                'title' => esc_html__('Search', 'listdomer-core'),
                'id' => 'listdomer-search-settings',
                'desc' => esc_html__('Configure how search functionality behaves across your site.', 'listdomer-core'),
                'customizer_width' => '400px',
                'icon' => 'el el-search',
                'fields' => $this->get_search_fields(),
            ]
        );
    }

    /**
     * Returns the fields for the search settings.
     * @return array Fields array.
     */
    private function get_search_fields(): array
    {
        return [
            [
                'id' => 'listdomer_search_default',
                'type' => 'switch',
                'title' => esc_html__('Use Default WordPress Search', 'listdomer-core'),
                'subtitle' => esc_html__('Enable to use WordPress native search behavior. Disable to customize post types for search results.', 'listdomer-core'),
                'default' => true,
                'on' => esc_html__('Enabled', 'listdomer-core'),
                'off' => esc_html__('Disabled', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_search_post_types',
                'type' => 'checkbox',
                'title' => esc_html__('PostTypes To Include In Search', 'listdomer-core'),
                'subtitle' => esc_html__('Select which post types should appear in search results when the default search is disabled.', 'listdomer-core'),
                'data' => 'post_types',
                'default' => [
                    'post' => true,
                    'page' => true,
                    'listdom-listing' => true,
                ],
                'required' => [
                    ['listdomer_search_default', '=', false],
                ],
            ],
        ];
    }
}
