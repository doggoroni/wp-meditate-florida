<?php

class LSDR_Footers extends LSDR_Base
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
        // Footer Type
        $manager->add_setting('listdomer_footer_type', [
            'default' => 'type1',
            'transport' => 'refresh',
        ]);

        $manager->add_control(new WP_Customize_Control(
            $manager,
            'listdomer_footer_type_control',
            [
                'label' => esc_html__('Footer Type', 'listdomer'),
                'settings' => 'listdomer_footer_type',
                'section' => 'listdomer_footer',
                'description' => esc_html__('You need listdomer pro version to see all footer types.', 'listdomer'),
                'type' => 'select',
                'choices' => LSDR_Footers::all(),
            ]
        ));
    }

    public static function all()
    {
        $footers = [
            'type1' => esc_html__('Type 1', 'listdomer'),
            'type2' => esc_html__('Type 2', 'listdomer'),
            'type3' => esc_html__('Type 3', 'listdomer'),
            'type4' => esc_html__('Type 4', 'listdomer'),
            'type5' => esc_html__('Type 5', 'listdomer'),
            'tiny' => esc_html__('Tiny', 'listdomer'),
        ];

        $footers = apply_filters('lsdr_footer_types', $footers);
        $footers['disabled'] = esc_html__('Disabled', 'listdomer');

        return $footers;
    }

    public static function display($type = null)
    {
        // Footer Type
        if (!$type)
        {
            // Global Footer
            $type = LSDR_Settings::get('listdomer_footer_type', 'type1');

            global $post;
            if ($post && isset($post->ID))
            {
                // Footer Per Page
                $footer = get_post_meta($post->ID, 'lsdr_footer', true);
                if ($footer && $footer !== 'inherit') $type = $footer;
            }

            // Blog Home
            if (is_home())
            {
                $blog_page_id = get_option( 'page_for_posts' );

                // Footer Per Page
                if ($blog_page_id)
                {
                    $footer = get_post_meta($blog_page_id, 'lsdr_footer', true);
                    if ($footer && $footer !== 'inherit') $type = $footer;
                }
            }
        }

        // Elementor Footer
        if (is_numeric($type) && class_exists('Elementor\Plugin'))
        {
            echo Elementor\Plugin::instance()
                ->frontend
                ->get_builder_content_for_display($type, true);
        }
        // Ensure footer is not disabled
        else if ($type !== 'disabled')
        {
            get_template_part('partials/footers/footer', $type);
        }
    }
}
