<?php

use Elementor\Core\Documents_Manager;
use Elementor\Plugin;
use Elementor\Widgets_Manager;

class LSDRC_Elementor extends LSDRC_Base
{
    public function init()
    {
        // Widgets
        add_action('elementor/widgets/register', [$this, 'widgets']);

        // Category
        add_action('elementor/init', [$this, 'category']);

        // Documents
        add_action('elementor/documents/register', [$this, 'documents']);

        // Dynamic Headers & Footers
        add_filter('lsdr_header_types', [$this, 'headers'], 20);
        add_filter('lsdr_footer_types', [$this, 'footers'], 20);
    }

    public function widgets(Widgets_Manager $manager)
    {
        // Posts Widget
        $manager->register(new LSDRC_Elementor_Posts());

        // Carousel Widget
        $manager->register(new LSDRC_Elementor_Carousel());

        // Tiny Widget
        $manager->register(new LSDRC_Elementor_Tiny());

        // Testimonial Widget
        $manager->register(new LSDRC_Elementor_Testimonials());
    }

    public function category()
    {
        Plugin::$instance->elements_manager->add_category('listdomer', [
            'title' => esc_html__('Listdomer', 'listdomer-core'),
            'icon' => 'fas fa-palette',
        ]);
    }

    public function documents(Documents_Manager $documents_manager)
    {
        $documents_manager->register_document_type('lsdr-header', LSDRC_Documents_Header::class);
        $documents_manager->register_document_type('lsdr-footer', LSDRC_Documents_Footer::class);
    }

    public function headers(array $types): array
    {
        // Elementor Doesn't Exist
        if (!class_exists(Plugin::class)) return $types;

        $headers = LSDRC_Base::get_elementor_headers();
        foreach ($headers as $header)
        {
            $types[$header->ID] = $header->post_title;
        }

        return $types;
    }

    public function footers(array $types): array
    {
        // Elementor Doesn't Exist
        if (!class_exists(Plugin::class)) return $types;

        $footers = LSDRC_Base::get_elementor_footers();
        foreach ($footers as $footer)
        {
            $types[$footer->ID] = $footer->post_title;
        }

        return $types;
    }
}
