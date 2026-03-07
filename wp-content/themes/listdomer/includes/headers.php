<?php

class LSDR_Headers extends LSDR_Base
{
    public function init()
    {
        add_action('customize_register', [$this, 'customizer']);
    }

    /**
     * @param WP_Customize_Manager $manager Theme Customizer object.
     */
    public function customizer($manager)
    {
        // Header Type
        $manager->add_setting('listdomer_header_type', [
            'default' => 'type1',
            'transport' => 'refresh',
        ]);

        $manager->add_control(new WP_Customize_Control(
            $manager,
            'listdomer_header_type_control',
            [
                'label' => esc_html__('Header Type', 'listdomer'),
                'settings' => 'listdomer_header_type',
                'section' => 'listdomer_header',
                'description' => esc_html__('You need listdomer pro version to see all header types.', 'listdomer'),
                'type' => 'select',
                'choices' => LSDR_Headers::all(),
            ]
        ));
    }

    public static function all()
    {
        $headers = [
            'type1' => esc_html__('Type 1', 'listdomer'),
            'type2' => esc_html__('Type 2', 'listdomer'),
            'type3' => esc_html__('Type 3', 'listdomer'),
            'type4' => esc_html__('Type 4', 'listdomer'),
            'type5' => esc_html__('Type 5', 'listdomer'),
        ];

        $headers = apply_filters('lsdr_header_types', $headers);
        $headers['disabled'] = esc_html__('Disabled', 'listdomer');

        return $headers;
    }

    public static function display($type = null)
    {
        // Header Type
        if (!$type)
        {
            // Global Header
            $type = LSDR_Settings::get('listdomer_header_type', 'type1');

            global $post;
            if ($post && isset($post->ID))
            {
                // Header Per Page
                $header = get_post_meta($post->ID, 'lsdr_header', true);
                if ($header && $header !== 'inherit') $type = $header;
            }

            // Blog Home
            if (is_home())
            {
                $blog_page_id = get_option('page_for_posts');

                // Header Per Page
                if ($blog_page_id)
                {
                    $header = get_post_meta($blog_page_id, 'lsdr_header', true);
                    if ($header && $header !== 'inherit') $type = $header;
                }
            }
        }

        // Elementor Header
        if (is_numeric($type) && class_exists('Elementor\Plugin'))
        {
            echo Elementor\Plugin::instance()
                ->frontend
                ->get_builder_content_for_display($type, true);
        }
        // Ensure header is not disabled
        else if ($type !== 'disabled')
        {
            get_template_part('partials/headers/header', $type);
        }
    }
}
