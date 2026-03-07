<?php

class LSDR_Sidebars extends LSDR_Base
{
    public function init()
    {
        add_action('widgets_init', [$this, 'register']);
    }

    public function register()
    {
        register_sidebar([
            'name' => esc_html__('Sidebar', 'listdomer'),
            'id' => 'sidebar-1',
            'description' => esc_html__('Blog Sidebar', 'listdomer'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        ]);

        register_sidebar([
            'name' => esc_html__('Footer 1', 'listdomer'),
            'id' => 'footer-1',
            'description' => esc_html__('Footer Sidebar', 'listdomer'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        ]);

        register_sidebar([
            'name' => esc_html__('Footer 2', 'listdomer'),
            'id' => 'footer-2',
            'description' => esc_html__('Footer Sidebar', 'listdomer'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        ]);

        register_sidebar([
            'name' => esc_html__('Footer 3', 'listdomer'),
            'id' => 'footer-3',
            'description' => esc_html__('Footer Sidebar', 'listdomer'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        ]);

        register_sidebar([
            'name' => esc_html__('Footer 4', 'listdomer'),
            'id' => 'footer-4',
            'description' => esc_html__('Footer Sidebar', 'listdomer'),
            'before_widget' => '<section id="%1$s" class="widget %2$s">',
            'after_widget' => '</section>',
            'before_title' => '<h2 class="widget-title">',
            'after_title' => '</h2>',
        ]);
    }
}
