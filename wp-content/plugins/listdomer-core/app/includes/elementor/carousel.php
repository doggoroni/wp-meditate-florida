<?php

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

class LSDRC_Elementor_Carousel extends LSDRC_Elementor_Base
{
    public function get_name(): string
    {
        return 'lsdrc-carousel';
    }

    public function get_title(): ?string
    {
        return esc_html__('Content Carousel', 'listdomer-core');
    }

    public function get_icon(): string
    {
        return 'eicon-slider-push';
    }

    public function get_categories(): array
    {
        return ['listdomer'];
    }

    protected function register_controls()
    {
        // Start Section
        $this->start_controls_section(
            'lsdrc_content_section',
            [
                'label' => esc_html__('Content', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        $repeater = new Repeater();
        $repeater->add_control(
            'number',
            [
                'label' => esc_html__('Number', 'listdomer-core'),
                'type' => Controls_Manager::NUMBER,
                'default' => 1,
            ]
        );

        $repeater->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'listdomer-core'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('How it works', 'listdomer-core'),
            ]
        );

        $repeater->add_control(
            'content',
            [
                'label' => esc_html__('Content', 'listdomer-core'),
                'type' => Controls_Manager::TEXTAREA,
            ]
        );

        $this->add_control(
            'slides',
            [
                'label' => esc_html__('Slides', 'listdomer-core'),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'number' => 1,
                        'title' => esc_html__('How it works', 'listdomer-core'),
                        'content' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'listdomer-core'),
                    ],
                    [
                        'number' => 2,
                        'title' => esc_html__('How it works', 'listdomer-core'),
                        'content' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'listdomer-core'),
                    ],
                    [
                        'number' => 3,
                        'title' => esc_html__('How it works', 'listdomer-core'),
                        'content' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'listdomer-core'),
                    ],
                    [
                        'number' => 4,
                        'title' => esc_html__('How it works', 'listdomer-core'),
                        'content' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'listdomer-core'),
                    ],
                    [
                        'number' => 5,
                        'title' => esc_html__('How it works', 'listdomer-core'),
                        'content' => esc_html__('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut elit tellus, luctus nec ullamcorper mattis, pulvinar dapibus leo.', 'listdomer-core'),
                    ],
                ],
                'separator' => 'after',
            ]
        );

        // Carousel Controls
        $this->add_carousel_controls();

        // End Section
        $this->end_controls_section();

        // Start Style Section
        $this->start_controls_section(
            'lsdrc_number_section',
            [
                'label' => esc_html__('Number', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        // Number Styling
        $this->add_control(
            'number_color',
            [
                'label' => esc_html__('Number Color', 'listdomer-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .listdomer-carousel-number div' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'number_typography',
                'label' => esc_html__('Number Typography', 'listdomer-core'),
                'selector' => '{{WRAPPER}} .listdomer-carousel-number div',
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
                'selectors' => [
                    '{{WRAPPER}} .listdomer-carousel-item h6' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Title Typography', 'listdomer-core'),
                'selector' => '{{WRAPPER}} .listdomer-carousel-item h6',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'lsdrc_carousel_content_section',
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
                'selectors' => [
                    '{{WRAPPER}} .listdomer-carousel-item p' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'content_typography',
                'label' => esc_html__('Content Typography', 'listdomer-core'),
                'selector' => '{{WRAPPER}} .listdomer-carousel-item p',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'lsdrc_carousel_box_section',
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
                    '{{WRAPPER}} .listdomer-carousel .owl-item' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        // End Style Section
        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $slides = isset($settings['slides']) && is_array($settings['slides']) ? $settings['slides'] : [];
        $navigation = $settings['navigation'] ?? 'dots';
        $autoplay = isset($settings['autoplay']) && $settings['autoplay'] === 'yes' ? 1 : 0;
        $loop = isset($settings['loop']) && $settings['loop'] === 'yes' ? 1 : 0;
        $count = isset($settings['count']) && trim($settings['count']) ? (int) $settings['count'] : 1;
        $gap = isset($settings['gap']['size']) ? (int) $settings['gap']['size'] : 15;

        include LSDRC_ABSPATH . '/templates/elementor/carousel.php';
    }
}
