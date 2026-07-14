<?php

class LSDRC_Settings_Blog_Single extends LSDRC_Settings
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
                'title' => esc_html__('Single', 'listdomer-core'),
                'id' => 'blog-single',
                'desc' => esc_html__('Blog Single settings.', 'listdomer-core'),
                'subsection' => true,
                'fields' => $this->get_single_fields(),
            ]
        );
    }

    /**
     * Returns the fields for the form colors and styles settings.
     * @return array Fields array.
     */
    private function get_single_fields(): array
    {
        return [
            [
                'id' => 'listdomer_blog_typography',
                'type' => 'section',
                'title' => esc_html__('Typography', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_single_post_title_typography',
                'type' => 'typography',
                'title' => esc_html__('Title Typography', 'listdomer-core'),
                'subtitle' => esc_html__('Specify the font properties for title.', 'listdomer-core'),
                'desc' => esc_html__('You can choose one of Google fonts.', 'listdomer-core'),
                'google' => true,
                'font_family_clear' => false,
                'subsets' => false,
                'default' => [
                    'font-size' => '54px',
                    'font-family' => get_theme_mod('listdomer_h1_font', 'Poppins'),
                    'font-weight' => '700',
                    'line-height' => '62px',
                    'color' => get_theme_mod('listdomer_headlines_color', '#000000'),
                    'text-align' => 'left',
                ],
            ],
            [
                'id' => 'listdomer_single_post_content_typography',
                'type' => 'typography',
                'title' => esc_html__('Content Typography', 'listdomer-core'),
                'subtitle' => esc_html__('Specify the font properties for content.', 'listdomer-core'),
                'desc' => esc_html__('You can choose one of Google fonts.', 'listdomer-core'),
                'google' => true,
                'font_family_clear' => false,
                'subsets' => false,
                'default' => [
                    'font-size' => '14px',
                    'font-family' => 'Poppins',
                    'font-weight' => '400',
                    'line-height' => '32px',
                    'color' => '#66686b',
                    'text-align' => 'left',
                ],
            ],
            [
                'id' => 'listdomer_blog_elements',
                'type' => 'section',
                'title' => esc_html__('Elements and Layout', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_single_post_layout',
                'type' => 'image_select',
                'title' => esc_html__('Single Post Layout', 'listdomer-core'),
                'subtitle' => esc_html__('Choose the layout for single blog post pages.', 'listdomer-core'),
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
                'id' => 'listdomer_display_category',
                'type' => 'switch',
                'title' => esc_html__('Display Category', 'listdomer-core'),
                'subtitle' => esc_html__('Show or hide categories on blog posts.', 'listdomer-core'),
                'default' => true,
                'on' => esc_html__('Show', 'listdomer-core'),
                'off' => esc_html__('Hide', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_display_author',
                'type' => 'switch',
                'title' => esc_html__('Display Author', 'listdomer-core'),
                'subtitle' => esc_html__('Show or hide the author\'s name on blog posts.', 'listdomer-core'),
                'default' => true,
                'on' => esc_html__('Show', 'listdomer-core'),
                'off' => esc_html__('Hide', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_display_date',
                'type' => 'switch',
                'title' => esc_html__('Display Published Date', 'listdomer-core'),
                'subtitle' => esc_html__('Show or hide the published date on blog posts.', 'listdomer-core'),
                'default' => true,
                'on' => esc_html__('Show', 'listdomer-core'),
                'off' => esc_html__('Hide', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_display_navigation',
                'type' => 'switch',
                'title' => esc_html__('Display Blog Navigation', 'listdomer-core'),
                'subtitle' => esc_html__('Show or hide navigation links on single blog posts.', 'listdomer-core'),
                'default' => true,
                'on' => esc_html__('Show', 'listdomer-core'),
                'off' => esc_html__('Hide', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_navigation_title_word_limit',
                'type' => 'slider',
                'title' => esc_html__('Navigation Title Word Limit', 'listdomer-core'),
                'subtitle' => esc_html__('Set the maximum number of words for navigation titles.', 'listdomer-core'),
                'default' => 5,
                'min' => 0,
                'max' => 15,
                'step' => 1,
                'desc' => esc_html__('Drag the slider to set the word limit. Set to 0 for no limit.', 'listdomer-core'),
                'required' => [
                    ['listdomer_display_navigation', '=', true],
                ],
            ],
            [
                'id' => 'listdomer_blog_post_settings',
                'type' => 'section',
                'title' => esc_html__('Colors', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_content_color',
                'type' => 'color',
                'title' => esc_html__('Content Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_content_color', '#66686b'),
                'desc' => esc_html__('Color of blog content.', 'listdomer-pro'),
            ],
            [
                'id' => 'listdomer_content_a_color',
                'type' => 'color',
                'title' => esc_html__('Post Meta Text Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_content_a_color', '#8d919c'),
                'desc' => esc_html__('Color of anchor tags.', 'listdomer-pro'),
            ],

            [
                'id' => 'listdomer_blog_post_comment_settings',
                'type' => 'section',
                'title' => esc_html__('Comment Form', 'listdomer-core'),
            ],
            [
                'id' => 'listdomer_comment_input_bg_color',
                'type' => 'color',
                'title' => esc_html__('Background Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_comment_input_bg_color', '#ffffff'),
                'desc' => esc_html__('Set the background color for the comment form.', 'listdomer-pro'),
            ],
            [
                'id' => 'listdomer_comment_input_text_color',
                'type' => 'color',
                'title' => esc_html__('Text Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_comment_input_text_color', '#a4a8b5'),
                'desc' => esc_html__('Set the text color in the comment form.', 'listdomer-pro'),
            ],
            [
                'id' => 'listdomer_comment_input_placeholder_text_color',
                'type' => 'color',
                'title' => esc_html__('Placeholder Text Color', 'listdomer-core'),
                'default' => get_theme_mod('listdomer_comment_input_placeholder_text_color', '#a4a8b5'),
                'desc' => esc_html__('Set the color of the placeholder text in the comment form.', 'listdomer-pro'),
            ],
            [
                'id' => 'listdomer_comment_input_border',
                'type' => 'border',
                'title' => esc_html__('Border', 'listdomer-core'),
                'all' => true,
                'default' => [
                    'border-top' => '1px',
                    'border-right' => '1px',
                    'border-bottom' => '1px',
                    'border-left' => '1px',
                    'border-style' => 'solid',
                    'border-color' => '#f4f5f7',
                ],
            ],
            [
                'id' => 'listdomer_comment_input_hover_border',
                'type' => 'border',
                'title' => esc_html__('Border Hover', 'listdomer-core'),
                'all' => true,
                'default' => [
                    'border-top' => '1px',
                    'border-right' => '1px',
                    'border-bottom' => '1px',
                    'border-left' => '1px',
                    'border-style' => 'solid',
                    'border-color' => '#306be6',
                ],
            ],
            [
                'id' => 'listdomer_comment_input_border_radius',
                'type' => 'slider',
                'title' => esc_html__('Border Radius', 'listdomer-core'),
                'default' => 5,
                'min' => 0,
                'max' => 50,
                'step' => 1,
            ],
        ];
    }
}
