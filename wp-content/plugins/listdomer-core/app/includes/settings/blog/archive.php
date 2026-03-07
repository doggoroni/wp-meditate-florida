<?php

class LSDRC_Settings_Blog_Archive extends LSDRC_Settings
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
                'title' => esc_html__('Archive', 'listdomer-core'),
                'id' => 'blog-archive',
                'desc' => esc_html__('Blog Archive settings.', 'listdomer-core'),
                'subsection' => true,
                'fields' => $this->get_archive_fields(),
            ]
        );
    }

    /**
     * Returns the fields for the form colors and styles settings.
     * @return array Fields array.
     */
    private function get_archive_fields(): array
    {
        return [
            [
                'id' => 'listdomer_blog_elements',
                'type' => 'section',
                'title' => esc_html__('Elements and Layout', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_archive_post_layout',
                'type' => 'image_select',
                'title' => esc_html__('Archive Post Layout', 'listdomer-core'),
                'subtitle' => esc_html__('Choose the layout for archive blog post pages.', 'listdomer-core'),
                'options' => [
                    'left' => [
                        'alt' => esc_html__('Left Sidebar', 'listdomer-core'),
                        'img' => get_template_directory_uri() . '/assets/img/left.png',
                    ],
                    'full' => [
                        'alt' => esc_html__('Full Width', 'listdomer-core'),
                        'img' => get_template_directory_uri() . '/assets/img/none.png',
                    ],
                    'right' => [
                        'alt' => esc_html__('Right Sidebar', 'listdomer-core'),
                        'img' => get_template_directory_uri() . '/assets/img/right.png',
                    ],
                ],
                'default' => 'right',
            ],
            [
                'id' => 'listdomer_archive_layout_type',
                'type' => 'button_set',
                'title' => esc_html__('Archive Layout Type', 'listdomer-core'),
                'subtitle' => esc_html__('Choose between list style and grid style for the archive layout.', 'listdomer-core'),
                'options' => [
                    'list' => esc_html__('List Style', 'listdomer-core'),
                    'grid' => esc_html__('Grid Style', 'listdomer-core'),
                ],
                'default' => 'list',
            ],
            [
                'id' => 'listdomer_archive_grid_columns',
                'type' => 'button_set',
                'title' => esc_html__('Number of Columns', 'listdomer-core'),
                'subtitle' => esc_html__('Select how many columns to display.', 'listdomer-core'),
                'options' => [
                    1 => esc_html__('1 Column', 'listdomer-core'),
                    2 => esc_html__('2 Columns', 'listdomer-core'),
                    3 => esc_html__('3 Columns', 'listdomer-core'),
                    4 => esc_html__('4 Columns', 'listdomer-core'),
                ],
                'default' => 2,
                'required' => ['listdomer_archive_layout_type', '=', 'grid'],
            ],
            [
                'id' => 'listdomer_archive_post_title_typography',
                'type' => 'typography',
                'title' => esc_html__('Title Typography', 'listdomer-core'),
                'subtitle' => esc_html__('Specify the main font properties.', 'listdomer-core'),
                'desc' => esc_html__('You can choose one of Google fonts.', 'listdomer-core'),
                'google' => true,
                'font_family_clear' => false,
                'subsets' => false,
                'default' => [
                    'font-size' => '21px',
                    'font-family' => get_theme_mod('listdomer_main_font', 'Poppins'),
                    'font-weight' => '600',
                    'line-height' => '44px',
                    'color' => '#000',
                    'text-align' => 'left',
                ],
            ],
            [
                'id' => 'listdomer_archive_post_title_decoration',
                'type' => 'select',
                'title' => esc_html__('Title Text Decoration', 'listdomer-core'),
                'subtitle' => esc_html__('Choose the text decoration style for title on the blog archive.', 'listdomer-core'),
                'options' => [
                    'none' => esc_html__('None', 'listdomer-core'),
                    'underline' => esc_html__('Underline', 'listdomer-core'),
                    'overline' => esc_html__('Overline', 'listdomer-core'),
                    'line-through' => esc_html__('Line Through', 'listdomer-core'),
                ],
                'default' => 'none',
            ],
            [
                'id' => 'listdomer_archive_pagination_style',
                'type' => 'select',
                'title' => esc_html__('Pagination Style', 'listdomer-core'),
                'subtitle' => esc_html__('Choose the style for pagination on the blog archive.', 'listdomer-core'),
                'options' => [
                    'next_prev' => esc_html__('Next/Previous', 'listdomer-core'),
                    'numeric' => esc_html__('Numeric', 'listdomer-core'),
                ],
                'default' => 'next_prev',
            ],
            [
                'id' => 'listdomer_archive_display_category',
                'type' => 'switch',
                'title' => esc_html__('Display Category', 'listdomer-core'),
                'subtitle' => esc_html__('Show or hide the display of categories on blog posts.', 'listdomer-core'),
                'default' => true,
                'on' => esc_html__('Show', 'listdomer-core'),
                'off' => esc_html__('Hide', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_archive_display_author',
                'type' => 'switch',
                'title' => esc_html__('Display Author', 'listdomer-core'),
                'subtitle' => esc_html__('Show or hide the display of the author\'s name on blog posts.', 'listdomer-core'),
                'default' => true,
                'on' => esc_html__('Show', 'listdomer-core'),
                'off' => esc_html__('Hide', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_archive_display_date',
                'type' => 'switch',
                'title' => esc_html__('Display Published Date', 'listdomer-core'),
                'subtitle' => esc_html__('Show or hide the display of the published date on blog posts.', 'listdomer-core'),
                'default' => true,
                'on' => esc_html__('Show', 'listdomer-core'),
                'off' => esc_html__('Hide', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_blog_posts_per_page',
                'type' => 'slider',
                'title' => esc_html__('Number of Posts per Page', 'listdomer-core'),
                'subtitle' => esc_html__('Control how many posts should appear on the blog page.', 'listdomer-core'),
                'default' => 6,
                'min' => 1,
                'max' => 50,
                'step' => 1,
                'display_value' => 'label',
            ],
        ];
    }
}
