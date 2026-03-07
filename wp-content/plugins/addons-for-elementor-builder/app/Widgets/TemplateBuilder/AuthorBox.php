<?php

namespace AFEB\Widgets\TemplateBuilder;

use AFEB\Assets;
use AFEB\Controls\Helper;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Utils;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" AuthorBox Widget Class
 * 
 * @class AuthorBox
 * @version 1.3.0
 */
class AuthorBox extends Widget_Base
{
    /**
     * @var Assets
     */
    private $assets;

    /**
     * @var CHelper
     */
    private $controls;

    /**
     * AuthorBox Constructor
     * 
     * @since 1.3.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->controls = new Helper($this);
        $this->assets->author_box_style();
    }

    /**
     * Get widget name
     *
     * @since 1.3.0
     *
     * @return string Widget name
     */
    public function get_name()
    {
        return 'afeb_author_box';
    }

    /**
     * Get widget title
     *
     * @since 1.3.0
     *
     * @return string Widget title
     */
    public function get_title()
    {
        return esc_html__('Author Box', 'addons-for-elementor-builder');
    }

    /**
     * Get widget icon
     *
     * @since 1.3.0
     *
     * @return string Widget icon
     */
    public function get_icon()
    {
        return 'afeb-iconsvg-author-box';
    }

    /**
     * Get widget categories
     *
     * @since 1.3.0
     *
     * @return array Widget categories
     */
    public function get_categories()
    {
        return ['theme-elements-archive', 'recommended', 'theme-elements-single'];
    }

    /**
     * Get widget keywords
     *
     * @since 1.3.0
     *
     * @return array Widget keywords
     */
    public function get_keywords()
    {
        return ['author', 'user', 'profile', 'biography', 'avatar'];
    }

    /**
     * Retrieve the list of style dependencies the widget requires
     *
     * @since 1.3.0
     *
     * @return array Widget style dependencies
     */
    public function get_style_depends(): array
    {
        return ['afeb-author-box-style'];
    }

    /**
     * Register AuthorBox widget controls
     *
     * @since 1.3.0
     */
    public function register_controls()
    {
        $this->controls->tab_content_section('author_info_content_section', [
            'label' => esc_html__('Author Info', 'addons-for-elementor-builder')
        ], function () {
            $this->controls->select('source', [
                'label' => esc_html__('Source', 'addons-for-elementor-builder'),
                'options' => [
                    'current' => esc_html__('Current Author', 'addons-for-elementor-builder'),
                    'custom' => esc_html__('Custom', 'addons-for-elementor-builder'),
                ],
                'default' => 'current'
            ]);

            $this->controls->sh_switcher('show_avatar', [
                'label' => esc_html__('Profile Picture', 'addons-for-elementor-builder'),
                'prefix_class' => 'afeb-author-box-avatar-',
                'default' => 'yes',
                'separator' => 'before',
                'condition' => [
                    'source' => 'current',
                ],
                'render_type' => 'template',
            ]);

            $this->controls->number('avatar_size', [
                'label' => esc_html__('Picture Size', 'addons-for-elementor-builder'),
                'default' => 300,
                'condition' => [
                    'source' => 'current',
                    'show_avatar' => 'yes',
                ],
            ]);

            $this->controls->media('author_avatar', [
                'label' => esc_html__('Profile Picture', 'addons-for-elementor-builder'),
                'condition' => [
                    'source' => 'custom',
                ],
                'separator' => 'before',
                'dynamic' => [
                    'active' => true,
                ],
            ]);

            $this->controls->sh_switcher('show_name', [
                'label' => esc_html__('Display Name', 'addons-for-elementor-builder'),
                'prefix_class' => 'afeb-author-box-name-',
                'default' => 'yes',
                'separator' => 'before',
                'condition' => [
                    'source' => 'current',
                ],
                'render_type' => 'template',
            ]);

            $this->controls->text('author_name', [
                'label' => esc_html__('Name', 'addons-for-elementor-builder'),
                'default' => esc_html__('John Doe', 'addons-for-elementor-builder'),
                'condition' => [
                    'source' => 'custom',
                    'show_name' => 'yes',

                ],
                'separator' => 'before',
                'ai' => [
                    'active' => false,
                ],
            ]);

            $this->controls->select('author_name_tag', [
                'label' => esc_html__('HTML Tag', 'addons-for-elementor-builder'),
                'options' => [
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6',
                    'div' => 'div',
                    'span' => 'span',
                ],
                'default' => 'h4',
                'condition' => [
                    'show_name' => 'yes',
                ],
            ]);

            $this->controls->select('link_to', [
                'label' => esc_html__('Link', 'addons-for-elementor-builder'),
                'options' => [
                    '' => esc_html__('None', 'addons-for-elementor-builder'),
                    'website' => esc_html__('Website', 'addons-for-elementor-builder'),
                    'posts_archive' => esc_html__('Posts Archive', 'addons-for-elementor-builder'),
                ],
                'condition' => [
                    'source' => 'current',
                    'show_name' => 'yes',
                ],
                'description' => esc_html__('Link for the Author Name and Image', 'addons-for-elementor-builder'),
            ]);

            $this->controls->sh_switcher('show_biography', [
                'label' => esc_html__('Biography', 'addons-for-elementor-builder'),
                'prefix_class' => 'afeb-author-box-biography-',
                'default' => 'yes',
                'separator' => 'before',
                'condition' => [
                    'source' => 'current',
                ],
                'render_type' => 'template',
            ]);

            $this->controls->sh_switcher('show_link', [
                'label' => esc_html__('Archive Button', 'addons-for-elementor-builder'),
                'prefix_class' => 'afeb-author-box-link-',
                'default' => 'no',
                'condition' => [
                    'source' => 'current',
                ],
                'render_type' => 'template',
                'separator' => 'before',
            ]);

            $this->controls->url('author_website', [
                'condition' => [
                    'source' => 'custom',
                ],
                'description' => esc_html__('Link for the Author Name and Image', 'addons-for-elementor-builder'),
            ]);

            $this->controls->text_area('author_bio', [
                'label' => esc_html__('Biography', 'addons-for-elementor-builder'),
                'default' => esc_html__('Lorem ipsum dolor sit amet consectetur adipiscing elit dolor', 'addons-for-elementor-builder'),
                'rows' => 3,
                'condition' => [
                    'source' => 'custom',
                ],
                'separator' => 'before',
            ]);

            $this->controls->url('posts_url', [
                'label' => esc_html__('Archive Button', 'addons-for-elementor-builder'),
                'condition' => [
                    'source' => 'custom',
                ],
            ]);

            $this->controls->text('link_text', [
                'label' => esc_html__('Archive Text', 'addons-for-elementor-builder'),
                'default' => esc_html__('All Posts', 'addons-for-elementor-builder'),
                'ai' => [
                    'active' => false,
                ],
                'condition' => [
                    'show_link' => 'yes',
                ],
            ]);

            $this->controls->choose('layout', [
                'label' => esc_html__('Layout', 'addons-for-elementor-builder'),
                'options' => [
                    'left' => [
                        'title' => esc_html__('Left', 'addons-for-elementor-builder'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'above' => [
                        'title' => esc_html__('Above', 'addons-for-elementor-builder'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', 'addons-for-elementor-builder'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'separator' => 'before',
                'prefix_class' => 'afeb-author-box-layout-image-',
            ]);

            $this->controls->choose('alignment', [
                'label' => esc_html__('Alignment', 'addons-for-elementor-builder'),
                'options' => [
                    'left' => ['title' => esc_html__('Left', 'addons-for-elementor-builder'), 'icon' => 'eicon-text-align-left',],
                    'center' => ['title' => esc_html__('Center', 'addons-for-elementor-builder'), 'icon' => 'eicon-text-align-center',],
                    'right' => ['title' => esc_html__('Right', 'addons-for-elementor-builder'), 'icon' => 'eicon-text-align-right',],
                ],
                'prefix_class' => 'afeb-author-box-align-',
            ]);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Image Styles
         *
         */
        $this->controls->tab_style_section('image_style_section', [
            'label' => esc_html__('Image', 'addons-for-elementor-builder'),
            'conditions' => [
                'relation' => 'or',
                'terms' => [
                    [
                        'relation' => 'and',
                        'terms' => [
                            ['name' => 'source', 'operator' => '===', 'value' => 'current',],
                            ['name' => 'show_avatar', 'operator' => '===', 'value' => 'yes',],
                        ],
                    ],
                    [
                        'relation' => 'and',
                        'terms' => [
                            ['name' => 'source', 'operator' => '===', 'value' => 'custom',],
                            ['name' => 'author_avatar[url]', 'operator' => '!==', 'value' => '',],
                        ],
                    ],
                ],
            ],
        ], function () {
            $this->controls->choose('image_vertical_align', [
                'label' => esc_html__('Vertical Align', 'addons-for-elementor-builder'),
                'options' => [
                    'top' => [
                        'title' => esc_html__('Top', 'addons-for-elementor-builder'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'middle' => [
                        'title' => esc_html__('Middle', 'addons-for-elementor-builder'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                ],
                'prefix_class' => 'afeb-author-box-image-valign-',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'current',
                                ],
                                [
                                    'name' => 'show_avatar',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                                [
                                    'name' => 'layout',
                                    'operator' => '!==',
                                    'value' => 'above',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'custom',
                                ],
                                [
                                    'name' => 'author_avatar[url]',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                                [
                                    'name' => 'layout',
                                    'operator' => '!==',
                                    'value' => 'above',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->controls->responsive()->slider('image_size', [
                'label' => esc_html__('Image Size', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 200,
                    ],
                    'em' => [
                        'max' => 20,
                    ],
                    'rem' => [
                        'max' => 20,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-author-box-avatar img' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'current',
                                ],
                                [
                                    'name' => 'show_avatar',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'custom',
                                ],
                                [
                                    'name' => 'author_avatar[url]',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->controls->responsive()->slider('image_gap', [
                'label' => esc_html__('Gap', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                    'em' => [
                        'max' => 10,
                    ],
                    'rem' => [
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    'body.rtl {{WRAPPER}}.afeb-author-box-layout-image-left .afeb-author-box-avatar,
					 body:not(.rtl) {{WRAPPER}}:not(.afeb-author-box-layout-image-above) .afeb-author-box-avatar' => 'margin-right: {{SIZE}}{{UNIT}}; margin-left: 0;',

                    'body:not(.rtl) {{WRAPPER}}.afeb-author-box-layout-image-right .afeb-author-box-avatar,
					 body.rtl {{WRAPPER}}:not(.afeb-author-box-layout-image-above) .afeb-author-box-avatar' => 'margin-left: {{SIZE}}{{UNIT}}; margin-right:0;',

                    '{{WRAPPER}}.afeb-author-box-layout-image-above .afeb-author-box-avatar' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'current',
                                ],
                                [
                                    'name' => 'show_avatar',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'custom',
                                ],
                                [
                                    'name' => 'author_avatar[url]',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->controls->border([
                'name' => 'image_border',
                'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 20,
                    ],
                    'em' => [
                        'max' => 2,
                    ],
                    'rem' => [
                        'max' => 2,
                    ],
                ],
                'selector' => '{{WRAPPER}} .afeb-author-box-avatar img',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'current',
                                ],
                                [
                                    'name' => 'show_avatar',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'custom',
                                ],
                                [
                                    'name' => 'author_avatar[url]',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->controls->border_radius('image_border_radius', [
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-author-box-avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'current',
                                ],
                                [
                                    'name' => 'show_avatar',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'custom',
                                ],
                                [
                                    'name' => 'author_avatar[url]',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->controls->box_shadow([
                'name' => 'image_box_shadow',
                'selector' => '{{WRAPPER}} .afeb-author-box-avatar img',
                'fields_options' => [
                    'box_shadow_type' => [
                        'separator' => 'default',
                    ],
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'current',
                                ],
                                [
                                    'name' => 'show_avatar',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'custom',
                                ],
                                [
                                    'name' => 'author_avatar[url]',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        });
        /**
         *
         * Author Styles
         *
         */
        $this->controls->tab_style_section('author_style_section', [
            'label' => esc_html__('Author', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->heading('heading_name_style', [
                'label' => esc_html__('Name', 'addons-for-elementor-builder'),
                'separator' => 'before',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'current',
                                ],
                                [
                                    'name' => 'show_name',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'custom',
                                ],
                                [
                                    'name' => 'author_name',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->controls->text_color('name_color', [
                'selectors' => [
                    '{{WRAPPER}} .widget-image-caption' => 'color: {{VALUE}};',
                ],
                'global' => [
                    'default' => Global_Colors::COLOR_SECONDARY,
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-author-box-name' => 'color: {{VALUE}}',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'current',
                                ],
                                [
                                    'name' => 'show_name',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'custom',
                                ],
                                [
                                    'name' => 'author_name',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->controls->typography([
                'name' => 'name_typography',
                'selector' => '{{WRAPPER}} .afeb-author-box-name',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'current',
                                ],
                                [
                                    'name' => 'show_name',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'custom',
                                ],
                                [
                                    'name' => 'author_name',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->controls->responsive()->slider('name_gap', [
                'label' => esc_html__('Gap', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                    'em' => [
                        'max' => 10,
                    ],
                    'rem' => [
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-author-box-name' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'current',
                                ],
                                [
                                    'name' => 'show_name',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'custom',
                                ],
                                [
                                    'name' => 'author_name',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->controls->heading('heading_bio_style', [
                'label' => esc_html__('Biography', 'addons-for-elementor-builder'),
                'separator' => 'before',
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'current',
                                ],
                                [
                                    'name' => 'show_name',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'custom',
                                ],
                                [
                                    'name' => 'author_name',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->controls->text_color('bio_color', [
                'global' => ['default' => Global_Colors::COLOR_TEXT,],
                'selectors' => [
                    '{{WRAPPER}} .afeb-author-box-bio' => 'color: {{VALUE}}',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'current',
                                ],
                                [
                                    'name' => 'show_biography',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'custom',
                                ],
                                [
                                    'name' => 'author_bio',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->controls->typography([
                'name' => 'bio_typography',
                'selector' => '{{WRAPPER}} .afeb-author-box-bio',
                'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT,
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'current',
                                ],
                                [
                                    'name' => 'show_biography',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'custom',
                                ],
                                [
                                    'name' => 'author_bio',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            $this->controls->responsive()->slider('bio_gap', [
                'label' => esc_html__('Gap', 'addons-for-elementor-builder'),
                'size_units' => ['px', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                    'em' => [
                        'max' => 10,
                    ],
                    'rem' => [
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-author-box-bio' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
                'conditions' => [
                    'relation' => 'or',
                    'terms' => [
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'current',
                                ],
                                [
                                    'name' => 'show_biography',
                                    'operator' => '===',
                                    'value' => 'yes',
                                ],
                            ],
                        ],
                        [
                            'relation' => 'and',
                            'terms' => [
                                [
                                    'name' => 'source',
                                    'operator' => '===',
                                    'value' => 'custom',
                                ],
                                [
                                    'name' => 'author_bio',
                                    'operator' => '!==',
                                    'value' => '',
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        });
        /**
         *
         * Button Styles
         *
         */
        $this->controls->tab_style_section('button_style_section', [
            'label' => esc_html__('Button', 'addons-for-elementor-builder'),
            'condition' => [
                'show_link' => 'yes',
            ],
        ], function () {
            $this->controls->tabs('button_style_tab', [
                'normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'condition' => [
                        'show_link' => 'yes',
                    ],
                    'callback' => function () {
                        $this->controls->background([
                            'name' => 'button_background',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-author-box-button',
                            'condition' => [
                                'show_link' => 'yes',
                            ],
                        ]);

                        $this->controls->text_color('button_text_color', [
                            'global' => [
                                'default' => Global_Colors::COLOR_SECONDARY,
                            ],
                            'default' => '',
                            'selectors' => [
                                '{{WRAPPER}} .afeb-author-box-button' => 'color: {{VALUE}}; border-color: {{VALUE}}',
                            ],
                            'condition' => [
                                'show_link' => 'yes',
                            ],
                        ]);

                        $this->controls->typography([
                            'name' => 'button_typography',
                            'global' => [
                                'default' => Global_Typography::TYPOGRAPHY_ACCENT,
                            ],
                            'selector' => '{{WRAPPER}} .afeb-author-box-button',
                            'condition' => [
                                'show_link' => 'yes',
                            ],
                        ]);
                    },
                ],
                'hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'condition' => [
                        'show_link' => 'yes',
                    ],
                    'callback' => function () {
                        $this->controls->text_color('button_hover_color', [
                            'global' => [
                                'default' => Global_Colors::COLOR_SECONDARY,
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-author-box-button:hover' => 'border-color: {{VALUE}}; color: {{VALUE}};',
                            ],
                            'condition' => [
                                'show_link' => 'yes',
                            ],
                        ]);

                        $this->controls->background([
                            'name' => 'button_background_hover_color',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-author-box-button:hover',
                            'condition' => [
                                'show_link' => 'yes',
                            ],
                        ]);

                        $this->controls->slider('button_hover_transition_duration', [
                            'label' => esc_html__('Transition Duration', 'addons-for-elementor-builder'),
                            'size_units' => ['s', 'ms', 'custom'],
                            'default' => [
                                'unit' => 'ms',
                            ],
                            'selectors' => [
                                '{{WRAPPER}} .afeb-author-box-button' => 'transition-duration: {{SIZE}}{{UNIT}}',
                            ],
                            'condition' => [
                                'show_link' => 'yes',
                            ],
                        ]);

                        $this->controls->hover_animation('button_hover_animation', [
                            'condition' => [
                                'show_link' => 'yes',
                            ],
                        ]);
                    },
                ],
            ]);

            $this->controls->padding('button_padding', [
                'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-author-box-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
                'condition' => [
                    'show_link' => 'yes',
                ],
            ]);

            $this->controls->border([
                'name' => 'button_border',
                'size_units' => ['px', '%', 'em', 'rem', 'vw', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 20,
                    ],
                    'em' => [
                        'max' => 2,
                    ],
                    'rem' => [
                        'max' => 2,
                    ],
                ],
                'selector' => '{{WRAPPER}} .afeb-author-box-button',
                'separator' => 'before',
                'condition' => [
                    'show_link' => 'yes',
                ],
            ]);

            $this->controls->border_radius('button_border_radius', [
                'size_units' => ['px', '%', 'em', 'rem', 'custom'],
                'range' => [
                    'px' => [
                        'max' => 100,
                    ],
                    'em' => [
                        'max' => 10,
                    ],
                    'rem' => [
                        'max' => 10,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-author-box-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'after',
                'condition' => [
                    'show_link' => 'yes',
                ],
            ]);
        });
    }

    /**
     * Render AuthorBox widget output on the frontend
     *
     * @since 1.3.0
     */
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $author = [];
        $link_tag = 'div';
        $link_url = '';
        $link_target = '';
        $author_name_tag = Utils::validate_html_tag($settings['author_name_tag']);

        $custom_src = ($settings['source'] == 'custom');

        if ($settings['source'] == 'current') {

            $avatar_args['size'] = $settings['avatar_size'];
            $user_id = get_current_user_id();
            $author['avatar'] = get_avatar_url($user_id, $avatar_args);
            $author['display_name'] = get_the_author_meta('display_name', $user_id);
            $author['website'] = get_the_author_meta('user_url', $user_id);
            $author['bio'] = get_the_author_meta('description', $user_id);
            $author['posts_url'] = get_author_posts_url($user_id);
        } elseif ($custom_src) {
            if (!empty($settings['author_avatar']['url'])) {
                $avatar_src = $settings['author_avatar']['url'];
                if ($settings['author_avatar']['id']) {
                    $attachment_image_src = wp_get_attachment_image_src($settings['author_avatar']['id'], 'medium');
                    if (!empty($attachment_image_src[0])) {
                        $avatar_src = $attachment_image_src[0];
                    }
                }
                $author['avatar'] = $avatar_src;
            }

            $author['display_name'] = $settings['author_name'];
            $author['website'] = $settings['author_website']['url'];
            $author['bio'] = wpautop($settings['author_bio']);
            $author['posts_url'] = $settings['posts_url']['url'];
        }

        $print_avatar = ((!$custom_src && 'yes' === $settings['show_avatar']) || ($custom_src && !empty($author['avatar'])));
        $print_name = ((!$custom_src && 'yes' === $settings['show_name']) || ($custom_src && !empty($author['display_name'])));
        $print_bio = ((!$custom_src && 'yes' === $settings['show_biography']) || ($custom_src && !empty($author['bio'])));
        $print_link = ((!$custom_src && 'yes' === $settings['show_link']) && !empty($settings['link_text']) || ($custom_src && !empty($author['posts_url']) && !empty($settings['link_text'])));

        if (!empty($settings['link_to']) || $custom_src) {
            if (($custom_src || 'website' === $settings['link_to']) && !empty($author['website'])) {
                $link_tag = 'a';
                $link_url = $author['website'];

                if ($custom_src) {
                    $link_target = $settings['author_website']['is_external'] ? '_blank' : '';
                } else {
                    $link_target = '_blank';
                }
            } elseif ('posts_archive' === $settings['link_to'] && !empty($author['posts_url'])) {
                $link_tag = 'a';
                $link_url = $author['posts_url'];
            }

            if (!empty($link_url)) {
                $this->add_render_attribute('author_link', 'href', esc_url($link_url));

                if (!empty($link_target))
                    $this->add_render_attribute('author_link', 'target', $link_target);
            }
        }

        $this->add_render_attribute(
            'button',
            'class',
            [
                'afeb-author-box-button',
                'elementor-button',
                'elementor-size-xs',
            ]
        );

        if ($print_link)
            $this->add_render_attribute('button', 'href', esc_url($author['posts_url']));

        if ($print_link && !empty($settings['button_hover_animation'])) {
            $this->add_render_attribute(
                'button',
                'class',
                'elementor-animation-' . $settings['button_hover_animation']
            );
        }

        if ($print_avatar) {
            $this->add_render_attribute(
                'avatar',
                [
                    'src' => esc_url($author['avatar']),
                    'alt' => (!empty($author['display_name']))
                        ? sprintf(
                            /* translators: %s: Author display name. */
                            esc_attr__('Picture of %s', 'addons-for-elementor-builder'),
                            $author['display_name']
                        )
                        : esc_html__('Author picture', 'addons-for-elementor-builder'),
                    'loading' => 'lazy',
                ]
            );
        }
?>
        <div class="afeb-author-box">
            <?php if ($print_avatar) { ?>
                <<?php Utils::print_validated_html_tag($link_tag); ?> <?php $this->print_render_attribute_string('author_link'); ?> class="afeb-author-box-avatar">
                    <img <?php $this->print_render_attribute_string('avatar'); ?>>
                </<?php Utils::print_validated_html_tag($link_tag); ?>>
            <?php } ?>

            <div class="afeb-author-box-text">
                <?php if ($print_name) : ?>
                    <<?php Utils::print_validated_html_tag($link_tag); ?> <?php $this->print_render_attribute_string('author_link'); ?>>
                        <<?php Utils::print_validated_html_tag($author_name_tag); ?> class="afeb-author-box-name">
                            <?php Utils::print_unescaped_internal_string($author['display_name']); ?>
                        </<?php Utils::print_validated_html_tag($author_name_tag); ?>>
                    </<?php Utils::print_validated_html_tag($link_tag); ?>>
                <?php endif; ?>

                <?php if ($print_bio) : ?>
                    <div class="afeb-author-box-bio">
                        <?php Utils::print_unescaped_internal_string($author['bio']); ?>
                    </div>
                <?php endif; ?>

                <?php if ($print_link) : ?>
                    <a <?php $this->print_render_attribute_string('button'); ?>>
                        <?php $this->print_unescaped_setting('link_text'); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
<?php
    }
}
