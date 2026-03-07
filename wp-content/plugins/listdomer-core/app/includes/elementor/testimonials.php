<?php

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;

class LSDRC_Elementor_Testimonials extends LSDRC_Elementor_Base
{
    public function get_name(): string
    {
        return 'lsdrc-testimonials';
    }

    public function get_title(): ?string
    {
        return esc_html__('Testimonials', 'listdomer-core');
    }

    public function get_icon(): string
    {
        return 'eicon-testimonial';
    }

    public function get_categories(): array
    {
        return ['listdomer'];
    }

    protected function register_controls()
    {
        // Content Section
        $this->start_controls_section(
            'lsdrc_content_section',
            [
                'label' => esc_html__('Content', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'style',
            [
                'label' => esc_html__('Style', 'listdomer-core'),
                'type' => Controls_Manager::SELECT2,
                'options' => [
                    'style1' => esc_html__('Style 1', 'listdomer-core'),
                    'style2' => esc_html__('Style 2', 'listdomer-core'),
                ],
                'default' => 'style1',
            ]
        );

        $repeater = new Repeater();
        $repeater->add_control(
            'content',
            [
                'label' => esc_html__('Content', 'listdomer-core'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'listdomer-core'),
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => esc_html__('Image', 'listdomer-core'),
                'type' => Controls_Manager::MEDIA,
            ]
        );

        $repeater->add_control(
            'name',
            [
                'label' => esc_html__('Name', 'listdomer-core'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('John Smith', 'listdomer-core'),
            ]
        );

        $repeater->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'listdomer-core'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('CEO', 'listdomer-core'),
            ]
        );

        $this->add_control(
            'slides',
            [
                'label' => esc_html__('Testimonials', 'listdomer-core'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'content' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'listdomer-core'),
                        'image' => null,
                        'name' => esc_html__('John Smith', 'listdomer-core'),
                        'title' => esc_html__('CEO', 'listdomer-core'),
                    ],
                    [
                        'content' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'listdomer-core'),
                        'image' => null,
                        'name' => esc_html__('John Smith', 'listdomer-core'),
                        'title' => esc_html__('CEO', 'listdomer-core'),
                    ],
                    [
                        'content' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'listdomer-core'),
                        'image' => null,
                        'name' => esc_html__('John Smith', 'listdomer-core'),
                        'title' => esc_html__('CEO', 'listdomer-core'),
                    ],
                    [
                        'content' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'listdomer-core'),
                        'image' => null,
                        'name' => esc_html__('John Smith', 'listdomer-core'),
                        'title' => esc_html__('CEO', 'listdomer-core'),
                    ],
                ],
                'separator' => 'after',
            ]
        );

        // Carousel Controls
        $this->add_carousel_controls();

        $this->end_controls_section();

        // Start Style Section
        $this->start_controls_section(
            'lsdrc_testimonial_content_section',
            [
                'label' => esc_html__('Content', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Content Styling
        $this->add_control(
            'content_color',
            [
                'label' => esc_html__('Content Color', 'listdomer-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#66686b',
                'selectors' => [
                    '{{WRAPPER}} .listdomer-tsw-content' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => esc_html__('Content Typography', 'listdomer-core'),
                'selector' => '{{WRAPPER}} .listdomer-tsw-content',
                'fields_options' => [
                    'typography' => [
                        'default' => 'custom',
                    ],
                    'font_size' => [
                        'default' => [
                            'size' => 18,
                            'unit' => 'px',
                        ],
                    ],
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'lsdrc_testimonial_box_content_section',
            [
                'label' => esc_html__('Box', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'content_bg',
                'label' => esc_html__('Content Background', 'listdomer-core'),
                'types' => ['classic', 'gradient', 'image'], // Supports solid colors, gradients, and images
                'selector' => '{{WRAPPER}} .listdomer-carousel-item',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'content_border',
                'label' => esc_html__('Content Border', 'listdomer-core'),
                'selector' => '{{WRAPPER}} .listdomer-carousel-item',
            ]
        );

        // Border Radius Control
        $this->add_control(
            'content_border_radius',
            [
                'label' => esc_html__('Border Radius', 'listdomer-core'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'], // Units for border-radius
                'selectors' => [
                    '{{WRAPPER}} .listdomer-carousel-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'gap',
            [
                'label' => esc_html__('Gap Between Items', 'listdomer-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', '%'],
                'default' => [
                    'unit' => 'px',
                    'size' => 15,
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
                    '{{WRAPPER}} .listdomer-testimonials-widget .owl-item' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'lsdrc_name_section',
            [
                'label' => esc_html__('Name', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Name Styling
        $this->add_control(
            'name_color',
            [
                'label' => esc_html__('Name Color', 'listdomer-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000',
                'selectors' => [
                    '{{WRAPPER}} .listdomer-tsw-name' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'name_typography',
                'label' => esc_html__('Name Typography', 'listdomer-core'),
                'selector' => '{{WRAPPER}} .listdomer-tsw-name',
                'fields_options' => [
                    'typography' => [
                        'default' => 'custom',
                    ],
                    'font_size' => [
                        'default' => [
                            'size' => 16,
                            'unit' => 'px',
                        ],
                    ],
                ],
            ]
        );

        $this->end_controls_section();

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
                'default' => '#A4A8B5',
                'selectors' => [
                    '{{WRAPPER}} .listdomer-tsw-title' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Title Typography', 'listdomer-core'),
                'selector' => '{{WRAPPER}} .listdomer-tsw-title',
                'fields_options' => [
                    'typography' => [
                        'default' => 'custom',
                    ],
                    'font_size' => [
                        'default' => [
                            'size' => 13,
                            'unit' => 'px',
                        ],
                    ],
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'lsdrc_quote_mark_section',
            [
                'label' => esc_html__('Quote Mark', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'style' => 'style1',
                ],
            ]
        );

        // Quote Mark Styling
        $this->add_control(
            'quote_mark_color',
            [
                'label' => esc_html__('Color', 'listdomer-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#33bdfc',
                'selectors' => [
                    '{{WRAPPER}} .listdomer-tsw-style-style1::before' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        // Font Size
        $this->add_control(
            'quote_mark_font_size',
            [
                'label' => esc_html__('Font Size', 'listdomer-core'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem'],
                'range' => [
                    'px' => [
                        'min' => 10,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 46,
                    'unit' => 'px',
                ],
                'selectors' => [
                    '{{WRAPPER}} .listdomer-tsw-style-style1::before' => 'font-size: {{SIZE}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $slides = isset($settings['slides']) && is_array($settings['slides']) ? $settings['slides'] : [];
        $navigation = $settings['navigation'] ?? 'dots';
        $autoplay = isset($settings['autoplay']) && $settings['autoplay'] === 'yes';
        $loop = isset($settings['loop']) && $settings['loop'] === 'yes';
        $count = isset($settings['count']) && trim($settings['count']) ? (int) $settings['count'] : 1;
        $style = isset($settings['style']) && trim($settings['style']) ? $settings['style'] : 'style1';
        $gap = isset($settings['gap']['size']) ? (int) $settings['gap']['size'] : 15;

        include LSDRC_ABSPATH . '/templates/elementor/testimonials.php';
    }
}
