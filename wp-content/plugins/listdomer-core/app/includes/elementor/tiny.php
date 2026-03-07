<?php

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;

class LSDRC_Elementor_Tiny extends LSDRC_Elementor_Base
{
    public function get_name(): string
    {
        return 'lsdrc-tiny';
    }

    public function get_title(): ?string
    {
        return esc_html__('Tiny Image', 'listdomer-core');
    }

    public function get_icon(): string
    {
        return 'eicon-image';
    }

    public function get_categories(): array
    {
        return ['listdomer'];
    }

    protected function register_controls()
    {
        // Content Controls
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
                    'style3' => esc_html__('Style 3', 'listdomer-core'),
                ],
                'default' => 'style1',
            ]
        );

        $this->add_control(
            'image',
            [
                'label' => esc_html__('Image', 'listdomer-core'),
                'type' => Controls_Manager::MEDIA,
            ]
        );

        $this->add_control(
            'title',
            [
                'label' => esc_html__('Title', 'listdomer-core'),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__('97%', 'listdomer-core'),
            ]
        );

        $this->add_control(
            'subtitle',
            [
                'label' => esc_html__('Subtitle', 'listdomer-core'),
                'type' => Controls_Manager::TEXTAREA,
                'default' => esc_html__('Happy Clients', 'listdomer-core'),
            ]
        );

        $this->add_control(
            'icon',
            [
                'label' => esc_html__('Icon', 'listdomer-core'),
                'type' => Controls_Manager::ICONS,
            ]
        );

        $this->add_control(
            'video',
            [
                'label' => esc_html__('Video', 'listdomer-core'),
                'type' => Controls_Manager::URL,
                'description' => esc_html__("If you insert video URL, it will show instead of the icon.", 'listdomer-core'),
            ]
        );

        $this->end_controls_section();

        // Title Typography Section
        $this->start_controls_section(
            'lsdrc_title_typography_section',
            [
                'label' => esc_html__('Title Typography', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Typography', 'listdomer-core'),
                'selector' => '{{WRAPPER}} .listdomer-tw-stat h5',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => esc_html__('Color', 'listdomer-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000',
                'selectors' => [
                    '{{WRAPPER}} .listdomer-tw-stat h5' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->end_controls_section();

        // Subtitle Typography Section
        $this->start_controls_section(
            'lsdrc_subtitle_typography_section',
            [
                'label' => esc_html__('Subtitle Typography', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'subtitle_typography',
                'label' => esc_html__('Typography', 'listdomer-core'),
                'selector' => '{{WRAPPER}} .listdomer-tw-stat h6',
            ]
        );

        $this->add_control(
            'subtitle_color',
            [
                'label' => esc_html__('Color', 'listdomer-core'),
                'type' => Controls_Manager::COLOR,
                'default' => '#000',
                'selectors' => [
                    '{{WRAPPER}} .listdomer-tw-stat h6' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->end_controls_section();

        // Background Section
        $this->start_controls_section(
            'lsdrc_box_section',
            [
                'label' => esc_html__('Box', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'box-background',
                'label' => esc_html__('Background', 'listdomer-core'),
                'types' => ['gradient'],
                'selector' => '{{WRAPPER}} .listdomer-tw-stat',
            ]
        );

        $this->add_control(
            'icon_color',
            [
                'label' => esc_html__('Icon Color', 'listdomer-core'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .listdomer-tw-icon i' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .listdomer-tw-video i' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'lsdrc_circle_box_section',
            [
                'label' => esc_html__('Circle', 'listdomer-core'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name' => 'circle-background',
                'label' => esc_html__('Background Circle', 'listdomer-core'),
                'selector' => '{{WRAPPER}} .listdomer-tw-style-style1::before',
            ]
        );

        $this->end_controls_section();
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();

        $image = isset($settings['image']) && is_array($settings['image']) ? $settings['image'] : [];
        $attachment_id = $image['id'] ?? null;

        $img = $attachment_id ? wp_get_attachment_image_src($attachment_id, [400, 400]) : '';
        $alt = $attachment_id ? $this->get_alt($attachment_id) : '';

        $icon = isset($settings['icon']) && is_array($settings['icon']) ? $settings['icon'] : [];
        $i = isset($icon['value']) && trim($icon['value']) ? $icon['value'] : '';

        $video = isset($settings['video']) && is_array($settings['video']) ? $settings['video'] : [];
        $url = isset($video['url']) && trim($video['url']) ? $video['url'] : '';
        $blank = isset($video['is_external']) && $video['is_external'] === 'on';
        $nofollow = isset($video['nofollow']) && $video['nofollow'] === 'on';

        $title = isset($settings['title']) && trim($settings['title']) ? $settings['title'] : '';
        $subtitle = isset($settings['subtitle']) && trim($settings['subtitle']) ? $settings['subtitle'] : '';
        $style = isset($settings['style']) && trim($settings['style']) ? $settings['style'] : 'style1';

        $method = trim($url) ? 'video' : 'icon';
        ?>
        <div class="listdomer-tiny-widget listdomer-tw-style-<?php echo esc_attr($style); ?>">
            <?php if (is_array($img) && isset($img[0]) && trim($img[0])): ?>
                <div class="listdomer-tw-image">
                    <img src="<?php echo esc_url($img[0]); ?>" alt="<?php echo esc_attr($alt); ?>">
                </div>
            <?php endif; ?>
            <div class="listdomer-tw-stat">
                <?php if ($method === 'icon' && trim($i)): ?>
                    <div class="listdomer-tw-icon"><i class="<?php echo esc_attr($i); ?>"></i></div>
                <?php endif; ?>

                <h5><?php echo esc_html($title); ?></h5>
                <h6><?php echo esc_html($subtitle); ?></h6>
                <?php if ($method === 'video' && trim($url)): ?>
                    <div class="listdomer-tw-video">
                        <a href="<?php echo esc_url($url); ?>" <?php echo $blank ? 'target="_blank"' : ''; ?> <?php echo $nofollow ? 'rel="nofollow"' : ''; ?>>
                            <i class="fas fa-play"></i>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
