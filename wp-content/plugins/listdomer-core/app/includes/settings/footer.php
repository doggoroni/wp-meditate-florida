<?php

class LSDRC_Settings_Footer extends LSDRC_Settings
{
    /**
     * Set footer settings section for Redux.
     * @return void
     */
    public function register()
    {
        Redux::set_section(
            $this->opt_name,
            [
                'title' => esc_html__('Footer Settings', 'listdomer-core'),
                'id' => 'listdomer-footer-settings',
                'desc' => esc_html__('Options for configuring the footer.', 'listdomer-core'),
                'customizer_width' => '400px',
                'parent_id' => 'listdomer',
                'icon' => 'el el-arrow-down',
                'fields' => $this->get_footer_fields(),
            ]
        );
    }

    /**
     * Returns the fields for the footer settings.
     * @return array Fields array.
     */
    private function get_footer_fields(): array
    {
        $footer_options = class_exists('LSDR_Footers') ? LSDR_Footers::all() : [];

        return [
            // Footer Type
            [
                'id' => 'listdomer_footer_type',
                'type' => 'select',
                'title' => esc_html__('Footer Type', 'listdomer-core'),
                'options' => $footer_options,
                'default' => get_theme_mod('listdomer_footer_type', 'type1'),
                'desc' => esc_html__('You need listdomer pro version to see all footer types.', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_footer_bg_color',
                'type' => 'color',
                'title' => esc_html__('Background Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_footer_bg_color', '#1c1c1c'),
                'desc' => esc_html__('Easily change footer background color.', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_footer_text_color',
                'type' => 'color',
                'title' => esc_html__('Title Text Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_footer_text_color', '#ffffff'),
                'desc' => esc_html__('Easily change footer text color.', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_footer_widget_link',
                'type' => 'raw',
                'title' => esc_html__('Footer Widgets', 'listdomer-core'),
                'desc' => sprintf(
                    esc_html__('Manage footer columns in the theme widgets. %sClick here%s.', 'listdomer-core'),
                    '<a href="' . esc_url(admin_url('widgets.php')) . '" target="_blank">', '</a>'
                ),
            ],
            [
                'id' => 'listdomer_footer_menu_select',
                'type' => 'select',
                'title' => esc_html__('Select Menu', 'listdomer-core'),
                'options' => wp_list_pluck(wp_get_nav_menus(), 'name', 'term_id'),
                'default' => get_theme_mod('listdomer_footer_menu_select'),
                'desc' => esc_html__(
                    'Select a menu for the sub-footer. Choosing a menu here will override the one selected in Appearance > Menus > Manage Locations.',
                    'listdomer-core'
                ),
            ],
            // Footer copyright text
            [
                'id' => 'listdomer_footer_copyright_text',
                'type' => 'text',
                'title' => esc_html__('Footer Copyright Text', 'listdomer-core'),
                'default' => '',
                'desc' => esc_html__('Enter the text for your footer copyright. This text will be displayed at the bottom of your footer.', 'listdomer-core'),
            ],
        ];
    }
}
