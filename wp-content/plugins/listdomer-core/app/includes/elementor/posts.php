<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;

class LSDRC_Elementor_Posts extends LSDRC_Elementor_Base
{
    public function get_name(): string
    {
        return 'lsdrc-posts';
    }

    public function get_title(): ?string
    {
        return esc_html__('Posts', 'listdomer-core');
    }

    public function get_icon(): string
    {
        return 'eicon-document-file';
    }

    public function get_categories(): array
    {
        return ['listdomer'];
    }

    protected function register_controls()
    {
        $categories = get_categories();

        $categories_options = ['' => esc_html__('All Categories', 'listdomer-core')];
        foreach ($categories as $category)
        {
            $categories_options[$category->term_id] = $category->name;
        }

        // Start Content Section
        $this->start_controls_section(
            'lsdrc_content_section',
            [
                'label' => esc_html__('Posts', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'posts_count',
            [
                'label' => esc_html__('Number of Posts', 'listdomer-core'),
                'type' => Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 20,
                'step' => 1,
            ]
        );

        $this->add_control(
            'columns_desktop',
            [
                'label' => esc_html__('Columns', 'listdomer-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 3,
                'options' => [
                    1 => '1 Column',
                    2 => '2 Columns',
                    3 => '3 Columns',
                    4 => '4 Columns',
                    6 => '6 Columns',
                ],
            ]
        );

        $this->add_control(
            'columns_tablet',
            [
                'label' => esc_html__('Tablet Columns', 'listdomer-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 2,
                'options' => [
                    1 => '1 Column',
                    2 => '2 Columns',
                    3 => '3 Columns',
                    4 => '4 Columns',
                    6 => '6 Columns',
                ],
                'condition' => [
                    'enable_owl_carousel' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'columns_mobile',
            [
                'label' => esc_html__('Mobile Columns', 'listdomer-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 1,
                'options' => [
                    1 => '1 Column',
                    2 => '2 Columns',
                    3 => '3 Columns',
                    4 => '4 Columns',
                    6 => '6 Columns',
                ],
                'condition' => [
                    'enable_owl_carousel' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'lsdrc_query_section',
            [
                'label' => esc_html__('Query', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Post Type Control
        $this->add_control(
            'post_type',
            [
                'label' => esc_html__('Select Post Type', 'listdomer-core'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'post' => esc_html__('Post', 'listdomer-core'),
                    'page' => esc_html__('Page', 'listdomer-core'),
                    'listing' => esc_html__('Listing', 'listdomer-core'),
                ],
            ]
        );

        $this->add_control(
            'post_category',
            [
                'label' => esc_html__('Post Category', 'listdomer-core'),
                'type' => Controls_Manager::SELECT,
                'options' => $categories_options,
                'condition' => [
                    'post_type' => 'post',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'lsdrc_slider_section',
            [
                'label' => esc_html__('Carousel', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'enable_owl_carousel',
            [
                'label' => esc_html__('Enable Carousel', 'listdomer-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'listdomer-core'),
                'label_off' => esc_html__('No', 'listdomer-core'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'enable_loop',
            [
                'label' => esc_html__('Enable Loop', 'listdomer-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'listdomer-core'),
                'label_off' => esc_html__('No', 'listdomer-core'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'enable_owl_carousel' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'enable_autoplay',
            [
                'label' => esc_html__('Enable AutoPlay', 'listdomer-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'listdomer-core'),
                'label_off' => esc_html__('No', 'listdomer-core'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'enable_owl_carousel' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'autoplay_duration',
            [
                'label' => esc_html__('Autoplay duration (ms)', 'listdomer-core'),
                'type' => Controls_Manager::TEXT,
                'default' => 3000,
                'label_block' => true,
                'condition' => [
                    'enable_owl_carousel' => 'yes',
                    'enable_autoplay' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'enable_navigations',
            [
                'label' => esc_html__('Enable Navigations', 'listdomer-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'listdomer-core'),
                'label_off' => esc_html__('No', 'listdomer-core'),
                'return_value' => 'yes',
                'default' => 'yes',
                'condition' => [
                    'enable_owl_carousel' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'gap',
            [
                'label' => esc_html__('Gap Between Slides', 'listdomer-core'),
                'type' => Controls_Manager::NUMBER,
                'default' => 10,
                'min' => 0,
                'max' => 50,
                'step' => 1,
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'lsdrc_card_section',
            [
                'label' => esc_html__('Cards', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'equal_height',
            [
                'label' => esc_html__('Equal Height', 'listdomer-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'listdomer-core'),
                'label_off' => esc_html__('No', 'listdomer-core'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_responsive_control(
            'non_slider_gap',
            [
                'label' => esc_html__('Gap Between Items', 'listdomer-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'unit' => 'px',
                    'size' => 10,
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                        'step' => 1,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 5,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} ul.listdomer-posts-widget' => 'gap: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'enable_owl_carousel!' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        // Start Style Section
        $this->start_controls_section(
            'lsdrc_title_section',
            [
                'label' => esc_html__('Title', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Title Styling
        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Title Color', 'listdomer-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .listdomer-pw-content h5 a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Title Typography', 'listdomer-core'),
                'selector' => '{{WRAPPER}} .listdomer-pw-content h5 a',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'lsdrc_category_section',
            [
                'label' => esc_html__('Category', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'show_category',
            [
                'label' => esc_html__('Show Category', 'listdomer-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'listdomer-core'),
                'label_off' => esc_html__('Hide', 'listdomer-core'),
                'default' => 'yes',
            ]
        );

        // Category Styling
        $this->add_control(
            'category_color',
            [
                'label' => esc_html__('Category Color', 'listdomer-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .listdomer-pw-category' => 'color: {{VALUE}} !important;',
                ],
                'condition' => [
                    'show_category' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'category_typography',
                'label' => esc_html__('Category Typography', 'listdomer-core'),
                'selector' => '{{WRAPPER}} .listdomer-pw-category',
                'fields_options' => [
                    'typography' => [
                        'default' => 'custom',
                    ],
                    'font_size' => [
                        'default' => [
                            'size' => 14,
                            'unit' => 'px',
                        ],
                    ],
                ],
                'condition' => [
                    'show_category' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'lsdrc_author_section',
            [
                'label' => esc_html__('Author', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'show_author',
            [
                'label' => esc_html__('Show Author', 'listdomer-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'listdomer-core'),
                'label_off' => esc_html__('Hide', 'listdomer-core'),
                'default' => 'yes',
            ]
        );

        // Author Styling
        $this->add_control(
            'author_color',
            [
                'label' => esc_html__('Author Color', 'listdomer-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .listdomer-pw-author' => 'color: {{VALUE}} !important;',
                ],
                'condition' => [
                    'show_author' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'author_typography',
                'label' => esc_html__('Author Typography', 'listdomer-core'),
                'selector' => '{{WRAPPER}} .listdomer-pw-author',
                'fields_options' => [
                    'typography' => [
                        'default' => 'custom',
                    ],
                    'font_size' => [
                        'default' => [
                            'size' => 14,
                            'unit' => 'px',
                        ],
                    ],
                ],
                'condition' => [
                    'show_author' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'lsdrc_date_section',
            [
                'label' => esc_html__('Date', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'show_date',
            [
                'label' => esc_html__('Show Date', 'listdomer-core'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Show', 'listdomer-core'),
                'label_off' => esc_html__('Hide', 'listdomer-core'),
                'default' => 'yes',
            ]
        );

        // Date Styling
        $this->add_control(
            'date_color',
            [
                'label' => esc_html__('Date Color', 'listdomer-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .listdomer-pw-date' => 'color: {{VALUE}} !important;',
                ],
                'condition' => [
                    'show_date' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'date_typography',
                'label' => esc_html__('Date Typography', 'listdomer-core'),
                'selector' => '{{WRAPPER}} .listdomer-pw-date',
                'fields_options' => [
                    'typography' => [
                        'default' => 'custom',
                    ],
                    'font_size' => [
                        'default' => [
                            'size' => 14,
                            'unit' => 'px',
                        ],
                    ],
                ],
                'condition' => [
                    'show_date' => 'yes',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'lsdrc_arrows_section',
            [
                'label' => esc_html__('Slider Arrows', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'enable_owl_carousel' => 'yes',
                    'enable_navigations' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_position',
            [
                'label' => esc_html__('Position', 'listdomer-core'),
                'type' => Controls_Manager::SELECT,
                'default' => 'absolute',
                'options' => [
                    'default' => esc_html__('Default (Relative)', 'listdomer-core'),
                    'absolute' => esc_html__('Absolute', 'listdomer-core'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .listdomer-posts-widget .owl-nav button.owl-prev, {{WRAPPER}} .listdomer-posts-widget .owl-nav button.owl-next' => 'position: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_offset',
            [
                'label' => esc_html__('Offset', 'listdomer-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', 'vh', 'vw'],
                'allowed_dimensions' => ['right', 'left'],
                'selectors' => [
                    '{{WRAPPER}} .listdomer-posts-widget .owl-nav button.owl-prev' => 'left: {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .listdomer-posts-widget .owl-nav button.owl-next' => 'right: {{RIGHT}}{{UNIT}};',
                ],
                'condition' => [
                    'arrow_position!' => 'default',
                ],
            ]
        );

        $this->add_control(
            'arrow_color',
            [
                'label' => esc_html__('Color', 'listdomer-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .listdomer-posts-widget .owl-nav button.owl-prev, {{WRAPPER}} .listdomer-posts-widget .owl-nav button.owl-next' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .listdomer-posts-widget .owl-nav button.owl-prev i, {{WRAPPER}} .listdomer-posts-widget .owl-nav button.owl-next i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'arrow_bg_color',
            [
                'label' => esc_html__('Background Color', 'listdomer-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .listdomer-posts-widget .owl-nav' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_size',
            [
                'label' => esc_html__('Size', 'listdomer-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .listdomer-posts-widget .owl-nav button.owl-prev, {{WRAPPER}} .listdomer-posts-widget .owl-nav button.owl-next' => 'font-size: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_padding',
            [
                'label' => esc_html__('Padding', 'listdomer-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'default' => [
                    'top' => 14,
                    'right' => 20,
                    'bottom' => 14,
                    'left' => 20,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .listdomer-posts-widget .owl-nav button.owl-prev, {{WRAPPER}} .listdomer-posts-widget .owl-nav button.owl-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'arrow_border_radius',
            [
                'label' => esc_html__('Border Radius', 'listdomer-core'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .listdomer-posts-widget .owl-nav button.owl-prev, {{WRAPPER}} .listdomer-posts-widget .owl-nav button.owl-next' => 'border-radius: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $PT = isset($settings['post_type']) && trim($settings['post_type']) ? $settings['post_type'] : 'post';

        $show_category = $settings['show_category'] === 'yes';
        $show_date = $settings['show_date'] === 'yes';
        $show_author = $settings['show_author'] === 'yes';
        $posts_count = $settings['posts_count'] ?? 3;
        $post_category = $settings['post_category'];
        $columns = (int) ($settings['columns_desktop'] ?? 4);
        $columns_tablet = (int) ($settings['columns_tablet'] ?? 2);
        $columns_mobile = (int) ($settings['columns_mobile'] ?? 1);
        $enable_slider = $settings['enable_owl_carousel'] === 'yes';
        $loop = $settings['enable_loop'] === 'yes';
        $autoplay = $settings['enable_autoplay'] === 'yes';
        $autoplay_duration = (int) ($settings['autoplay_duration'] ?? 3000);
        $gap = (int) ($settings['gap'] ?? 10);
        $enable_navigation = $settings['enable_navigations'] === 'yes';
        $equal_height = $settings['equal_height'] === 'yes';
        $equal_height_class = $equal_height ? 'lsd-equal-height' : '';

        include LSDRC_ABSPATH . '/templates/elementor/posts.php';
    }
}
