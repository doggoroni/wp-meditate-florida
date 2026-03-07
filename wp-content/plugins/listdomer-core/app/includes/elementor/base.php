<?php

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

class LSDRC_Elementor_Base extends Widget_Base
{
    /**
     * @inheritDoc
     */
    public function get_name()
    {
    }

    public function add_post_type_control()
    {
        $post_types = get_post_types([
            'public' => true,
        ], 'objects');

        $options = [];
        foreach ($post_types as $post_type) $options[$post_type->name] = $post_type->label;

        $this->add_control(
            'post_type',
            [
                'label' => esc_html__('Post Type', 'listdomer-core'),
                'type' => Controls_Manager::SELECT2,
                'options' => $options,
                'default' => 'post',
            ]
        );
    }

    public function add_carousel_controls()
    {
        $this->add_control(
            'navigation',
            [
                'label' => esc_html__('Navigation', 'listdomer-core'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'dots' => ['title' => esc_html__('Dots', 'listdomer-core'), 'icon' => 'fas fa-pager'],
                    'nav' => ['title' => esc_html__('Next / Previous Buttons', 'listdomer-core'), 'icon' => 'fas fa-arrows-alt-h'],
                ],
                'default' => 'dots',
            ]
        );

        $this->add_control(
            'autoplay',
            [
                'label' => esc_html__('Autoplay', 'listdomer-core'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 0,
            ]
        );

        $this->add_control(
            'loop',
            [
                'label' => esc_html__('Loop', 'listdomer-core'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 0,
            ]
        );

        $this->add_control(
            'count',
            [
                'label' => esc_html__('Items', 'listdomer-core'),
                'type' => Controls_Manager::SELECT2,
                'options' => [
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                ],
                'default' => 1,
            ]
        );
    }

    public function get_alt($attachment_id): string
    {
        // Get ALT
        $alt = get_post_meta($attachment_id, '_wp_attachment_image_alt', true);
        if (!trim($alt))
        {
            $attachment = get_post($attachment_id);

            $alt = $attachment->post_excerpt ?? '';
            if (!trim($alt)) $alt = $attachment->post_title ?? '';
        }

        return trim(wp_strip_all_tags($alt));
    }
}
