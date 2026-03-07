<?php

class LSDRC_Settings_Codes extends LSDRC_Settings
{
    /**
     * Set codes settings section for Redux.
     * @return void
     */
    public function register()
    {
        Redux::set_section(
            $this->opt_name,
            [
                'title' => esc_html__('Codes', 'listdomer-core'),
                'id' => 'listdomer-codes',
                'desc' => esc_html__('Code settings for various elements.', 'listdomer-core'),
                'customizer_width' => '400px',
                'icon' => 'fas fa-code',
                'fields' => $this->get_code_fields(),
            ]
        );
    }

    /**
     * Returns the fields for the Codes settings.
     * @return array Fields array.
     */
    private function get_code_fields(): array
    {
        return [
            // Custom CSS
            [
                'id' => 'listdomer_after_header',
                'type' => 'ace_editor',
                'title' => esc_html__('Custom CSS', 'listdomer-core'),
                'subtitle' => esc_html__('Insert custom CSS code that will be applied globally to the site.', 'listdomer-core'),
                'mode' => 'css',
                'theme' => 'monokai',
                'default' => get_theme_mod('listdomer_after_header', ''),
            ],

            // Custom JavaScript
            [
                'id' => 'listdomer_custom_js',
                'type' => 'ace_editor',
                'title' => esc_html__('Custom JavaScript', 'listdomer-core'),
                'subtitle' => esc_html__('Insert custom JavaScript code to be executed on every page.', 'listdomer-core'),
                'mode' => 'javascript',
                'theme' => 'chrome',
                'default' => '',
            ],

            // Header Tracking Codes (e.g., Google Analytics)
            [
                'id' => 'listdomer_header_tracking_codes',
                'type' => 'textarea',
                'title' => esc_html__('Header Tracking Codes', 'listdomer-core'),
                'subtitle' => esc_html__('Paste your Google Analytics or other tracking codes that should go in the <head> section.', 'listdomer-core'),
                'default' => '',
                'validate' => 'no_html',
            ],

            // Footer Tracking Codes
            [
                'id' => 'listdomer_footer_tracking_codes',
                'type' => 'textarea',
                'title' => esc_html__('Footer Tracking Codes', 'listdomer-core'),
                'subtitle' => esc_html__('Paste your tracking codes that should go just before the closing </body> tag.', 'listdomer-core'),
                'default' => '',
                'validate' => 'no_html',
            ],
        ];
    }
}
