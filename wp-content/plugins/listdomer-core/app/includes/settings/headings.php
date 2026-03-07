<?php

class LSDRC_Settings_Headings extends LSDRC_Settings
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
                'title' => esc_html__('Page Heading', 'listdomer-core'),
                'id' => 'listdomer-page-titles-settings',
                'desc' => esc_html__('Page Heading settings.', 'listdomer-core'),
                'customizer_width' => '400px',
                'icon' => 'el el-file',
            ]
        );

        Redux::set_section(
            $this->opt_name,
            [
                'title' => esc_html__('General', 'listdomer-core'),
                'id' => 'listdomer-page-titles-general-settings',
                'desc' => esc_html__('Page Heading Settings', 'listdomer-core'),
                'subsection' => true,
                'fields' => $this->get_page_title_fields(),
            ]
        );

        (new LSDRC_Settings_Headings_Listings())->register();
        (new LSDRC_Settings_Headings_Archive())->register();
        (new LSDRC_Settings_Headings_Pages())->register();
    }

    /**
     * Returns the fields for the page title settings.
     * @return array Fields array.
     */
    private function get_page_title_fields(): array
    {
        return [
            [
                'id' => 'listdomer_page_title_display',
                'type' => 'switch',
                'title' => esc_html__('Enable Page Header Section', 'listdomer-core'),
                'default' => true,
                'on' => esc_html__('Show', 'listdomer-core'),
                'off' => esc_html__('Hide', 'listdomer-core'),
            ],

            // Typography Settings
            [
                'id' => 'listdomer_page_title_typography',
                'type' => 'typography',
                'title' => esc_html__('Title Typography', 'listdomer-core'),
                'subtitle' => esc_html__('Set typography for page titles.', 'listdomer-core'),
                'google' => true,
                'font_family_clear' => false,
                'subsets' => false,
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
                'default' => [
                    'font-size' => '42px',
                    'font-family' => 'Poppins',
                    'font-weight' => '600',
                    'font-style' => '',
                    'line-height' => '46px',
                    'color' => '#000',
                    'text-align' => 'center',
                ],
            ],
            // Typography Settings
            [
                'id' => 'listdomer_page_title_description_typography',
                'type' => 'typography',
                'title' => esc_html__('Description Typography', 'listdomer-core'),
                'subtitle' => esc_html__('Set typography for page description.', 'listdomer-core'),
                'google' => true,
                'font_family_clear' => false,
                'subsets' => false,
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
                'default' => [
                    'font-size' => '21px',
                    'font-family' => 'Poppins',
                    'font-weight' => '400',
                    'font-style' => '',
                    'line-height' => '26px',
                    'color' => '#66686b',
                    'text-align' => 'center',
                ],
            ],
            // Background Color
            [
                'id' => 'listdomer_page_title_background_color',
                'type' => 'color',
                'title' => esc_html__('Background Color', 'listdomer-core'),
                'default' => '#e8f1ff',
                'transparent' => false,
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
            ],
            [
                'id' => 'listdomer_page_title_image',
                'type' => 'media',
                'title' => esc_html__('Background Image', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
                'default' => [
                    'url' => '',
                ],
            ],
            [
                'id' => 'listdomer_page_title_bg_position',
                'type' => 'select',
                'title' => esc_html__('Background Position', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
                'options' => [
                    'left top' => esc_html__('Left Top', 'listdomer-core'),
                    'left center' => esc_html__('Left Center', 'listdomer-core'),
                    'left bottom' => esc_html__('Left Bottom', 'listdomer-core'),
                    'center top' => esc_html__('Center Top', 'listdomer-core'),
                    'center center' => esc_html__('Center Center', 'listdomer-core'),
                    'center bottom' => esc_html__('Center Bottom', 'listdomer-core'),
                    'right top' => esc_html__('Right Top', 'listdomer-core'),
                    'right center' => esc_html__('Right Center', 'listdomer-core'),
                    'right bottom' => esc_html__('Right Bottom', 'listdomer-core'),
                ],
                'default' => 'center center',
            ],
            [
                'id' => 'listdomer_page_title_bg_repeat',
                'type' => 'select',
                'title' => esc_html__('Background Repeat', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
                'options' => [
                    'no-repeat' => esc_html__('No Repeat', 'listdomer-core'),
                    'repeat' => esc_html__('Repeat', 'listdomer-core'),
                    'repeat-x' => esc_html__('Repeat Horizontally', 'listdomer-core'),
                    'repeat-y' => esc_html__('Repeat Vertically', 'listdomer-core'),
                ],
                'default' => 'no-repeat',
            ],
            [
                'id' => 'listdomer_page_title_bg_size',
                'type' => 'select',
                'title' => esc_html__('Background Size', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
                'options' => [
                    'auto' => esc_html__('Auto', 'listdomer-core'),
                    'cover' => esc_html__('Cover', 'listdomer-core'),
                    'contain' => esc_html__('Contain', 'listdomer-core'),
                ],
                'default' => 'cover',
            ],
            [
                'id' => 'listdomer_page_title_bg_attachment',
                'type' => 'select',
                'title' => esc_html__('Background Attachment', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
                'options' => [
                    'scroll' => esc_html__('Scroll', 'listdomer-core'),
                    'fixed' => esc_html__('Fixed', 'listdomer-core'),
                ],
                'default' => 'scroll',
            ],
            // Border
            [
                'id' => 'listdomer_page_title_border',
                'type' => 'border',
                'title' => esc_html__('Border', 'listdomer-core'),
                'default' => [
                    'border-color' => '#fff',
                    'border-style' => 'none',
                    'border-top' => '0px',
                    'border-right' => '0px',
                    'border-bottom' => '0px',
                    'border-left' => '0px',
                ],
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
            ],
            [
                'id' => 'listdomer_page_title_shapes',
                'type' => 'switch',
                'title' => esc_html__('Enable Shapes', 'listdomer-core'),
                'default' => true,
                'on' => esc_html__('Show', 'listdomer-core'),
                'off' => esc_html__('Hide', 'listdomer-core'),
                'required' => [
                    ['listdomer_page_title_display', '=', true],
                ],
            ],
        ];
    }
}
