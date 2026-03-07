<?php

namespace AFEB\Widgets\TemplateBuilder;

use AFEB\Assets;
use AFEB\Controls\Helper as CHelper;
use AFEB\Helper;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * "Vertex Addons for Elementor" CommentsForm Widget Class
 * 
 * @class CommentsForm
 * @version 1.3.0
 */
class CommentsForm extends Widget_Base
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
     * CommentsForm Constructor
     * 
     * @since 1.3.0
     */
    public function __construct($data = [], $args = [])
    {
        parent::__construct($data, $args);
        $this->assets = new Assets();
        $this->assets->comments_form_style();

        $this->controls = new CHelper($this);
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
        return 'afeb_comments_form';
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
        return esc_html__('Comments Form', 'addons-for-elementor-builder');
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
        return 'afeb-iconsvg-comments-form';
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
        return ['theme-elements-single'];
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
        return ['comments', 'form'];
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
        return ['afeb-comments-form-style'];
    }

    /**
     * Register CommentsForm widget controls
     *
     * @since 1.3.0
     */
    public function register_controls()
    {
        $this->controls->tab_content_section('comments_content_section', [
            'label' => esc_html__('Comments', 'addons-for-elementor-builder')
        ], function () {

            $this->controls->sh_switcher('comments_avatar', [
                'label' => esc_html__('Show Avatar', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);

            $this->controls->number('comments_avatar_size', [
                'label' => esc_html__('Avatar Size', 'addons-for-elementor-builder'),
                'default' => 60,
                'min' => 10,
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-avatar img' => 'width: {{SIZE}}px;',
                ],
                'render_type' => 'template',
                'condition' => [
                    'comments_avatar' => 'yes'
                ],
            ]);

            $this->controls->select('comments_reply_location', [
                'label' => esc_html__('Reply Location', 'addons-for-elementor-builder'),
                'default' => 'separate',
                'options' => [
                    'inline' => esc_html__('Inline', 'addons-for-elementor-builder'),
                    'separate' => esc_html__('Separate', 'addons-for-elementor-builder'),
                ],
                'prefix_class' => 'afeb-comment-reply-',
                'render_type' => 'template',

            ]);
        });
        /**
         *
         * Form
         *
         */
        $this->controls->tab_content_section('comments_form_content_section', [
            'label' => esc_html__('Form', 'addons-for-elementor-builder')
        ], function () {
            $this->controls->text('comment_form_title', [
                'label' => esc_html__('Title', 'addons-for-elementor-builder'),
                'default' => esc_html__('Leave a Reply', 'addons-for-elementor-builder'),
                'dynamic' => ['active' => true,],
            ]);

            $this->controls->sh_switcher('comment_form_labels', [
                'label' => esc_html__('Show Labels', 'addons-for-elementor-builder'),
                'default' => 'yes',
                'separator' => 'before',
            ]);

            $this->controls->sh_switcher('comment_form_placeholders', [
                'label' => esc_html__('Show Placeholders', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);

            $this->controls->sh_switcher('comment_form_website', [
                'label' => esc_html__('Show Website Field', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);

            $this->controls->text('comment_form_submit_text', [
                'label' => esc_html__('Submit Button Text', 'addons-for-elementor-builder'),
                'default' => esc_html__('Submit', 'addons-for-elementor-builder'),
                'separator' => 'before',
                'dynamic' => ['active' => true,],
                'ai' => ['active' => false,],
            ]);
        });
        do_action('afeb/widget/content/after_render_content_section', $this);
        /**
         *
         * Comments
         *
         */
        $this->controls->tab_style_section('comments_style_section', [
            'label' => esc_html__('Comments', 'addons-for-elementor-builder'),
        ], function () {
            /**
             * Tabs
             */
            $this->controls->tabs('comments_tab_style', [
                'comments_odd' => [
                    'label' => esc_html__('Odd', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'comments_bg_odd',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .even .afeb-post-comment',
                        ]);

                        $this->controls->border([
                            'name' => 'comments_border_odd',
                            'selector' => '{{WRAPPER}} .even .afeb-post-comment',
                            'separator' => '',
                        ]);
                    },
                ],
                'comments_even' => [
                    'label' => esc_html__('Even', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'comments_bg_even',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .odd .afeb-post-comment',
                        ]);

                        $this->controls->border([
                            'name' => 'comments_border_even',
                            'selector' => '{{WRAPPER}} .odd .afeb-post-comment',
                            'separator' => '',
                        ]);
                    },
                ],
                'comments_by_post_author' => [
                    'label' => esc_html__('Post Author', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'comments_bg_by_post_author',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .bypostauthor .afeb-post-comment',
                        ]);

                        $this->controls->border([
                            'name' => 'comments_border_by_post_author',
                            'selector' => '{{WRAPPER}} .bypostauthor .afeb-post-comment',
                            'separator' => '',
                        ]);
                    },
                ],
            ]);

            $this->controls->responsive()->slider('comments_indent', [
                'label' => esc_html__('Nested Indent', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 24,
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comments-list ul.children' => 'padding-left: {{SIZE}}{{UNIT}}',
                ],
                'separator' => 'before',
            ]);

            $this->controls->responsive()->margin('comments_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-post-comment' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->padding('comments_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-post-comment' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->border_radius('comments_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-post-comment' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->box_shadow([
                'name' => 'comments_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .afeb-post-comment',
            ]);
        });
        /**
         *
         * Avatar
         *
         */
        $this->controls->tab_style_section('avatar_style_section', [
            'label' => esc_html__('Avatar', 'addons-for-elementor-builder'),
            'condition' => [
                'comments_avatar' => 'yes'
            ],
        ], function () {

            $this->controls->responsive()->slider('avatar_height', [
                'label' => esc_html__('Height', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-avatar img' => 'height: {{SIZE}}{{UNIT}}',
                ],
                'separator' => 'before',
            ]);

            $this->controls->responsive()->slider('avatar_width', [
                'label' => esc_html__('Width', 'addons-for-elementor-builder'),
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 300,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-avatar img' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]);

            $this->controls->responsive()->margin('avatar_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-avatar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->border([
                'name' => 'avatar_border',
                'selector' => '{{WRAPPER}} .afeb-comment-avatar',
                'separator' => '',
            ]);

            $this->controls->responsive()->border_radius('avatar_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->box_shadow([
                'name' => 'avatar_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .afeb-comment-avatar',
            ]);
        });
        /**
         *
         * Nickname
         *
         */
        $this->controls->tab_style_section('nickname_style_section', [
            'label' => esc_html__('Nickname', 'addons-for-elementor-builder'),
        ], function () {
            /**
             * Tabs
             */
            $this->controls->tabs('nickname_tab_style', [
                'nickname_odd' => [
                    'label' => esc_html__('Odd', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('nickname_color_odd', [
                            'selectors' => [
                                '{{WRAPPER}} .even .afeb-comment-author span' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .even .afeb-comment-author a' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->typography([
                            'name' => 'nickname_typography_odd',
                            'selector' => '{{WRAPPER}} .even .afeb-comment-author'
                        ]);
                    },
                ],
                'nickname_even' => [
                    'label' => esc_html__('Even', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('nickname_color_even', [
                            'selectors' => [
                                '{{WRAPPER}} .odd .afeb-comment-author span' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .odd .afeb-comment-author a' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->typography([
                            'name' => 'nickname_typography_even',
                            'selector' => '{{WRAPPER}} .odd .afeb-comment-author'
                        ]);
                    },
                ],
                'nickname_by_post_author' => [
                    'label' => esc_html__('Post Author', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('nickname_color_by_post_author', [
                            'selectors' => [
                                '{{WRAPPER}} .bypostauthor .afeb-comment-author span' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .bypostauthor .afeb-comment-author a' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->typography([
                            'name' => 'nickname_typography_by_post_author',
                            'selector' => '{{WRAPPER}} .bypostauthor .afeb-comment-author'
                        ]);
                    },
                ],
            ]);

            $this->controls->responsive()->margin('nickname_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-author' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]);

            $this->controls->responsive()->padding('nickname_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-author' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);
        });
        /**
         *
         * Date and Time
         *
         */
        $this->controls->tab_style_section('date_and_time_style_section', [
            'label' => esc_html__('Date and Time', 'addons-for-elementor-builder'),
        ], function () {
            /**
             * Tabs
             */
            $this->controls->tabs('date_and_time_tab_style', [
                'date_and_time_odd' => [
                    'label' => esc_html__('Odd', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('date_and_time_color_odd', [
                            'selectors' => [
                                '{{WRAPPER}} .even .afeb-comment-metadata' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .even .afeb-comment-metadata a' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .even .afeb-comment-reply:before' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->typography([
                            'name' => 'date_and_time_typography_odd',
                            'selector' => '{{WRAPPER}} .even .afeb-comment-metadata',
                        ]);
                    },
                ],
                'date_and_time_even' => [
                    'label' => esc_html__('Even', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('date_and_time_color_even', [
                            'selectors' => [
                                '{{WRAPPER}} .odd .afeb-comment-metadata' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .odd .afeb-comment-metadata a' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .odd .afeb-comment-reply:before' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->typography([
                            'name' => 'date_and_time_typography_even',
                            'selector' => '{{WRAPPER}} .odd .afeb-comment-metadata',
                        ]);
                    },
                ],
                'date_and_time_by_post_author' => [
                    'label' => esc_html__('Post Author', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('date_and_time_color_by_post_author', [
                            'selectors' => [
                                '{{WRAPPER}} .bypostauthor .afeb-comment-metadata' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .bypostauthor .afeb-comment-metadata a' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .bypostauthor .afeb-comment-reply:before' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->typography([
                            'name' => 'date_and_time_typography_by_post_author',
                            'selector' => '{{WRAPPER}} .bypostauthor .afeb-comment-metadata',
                        ]);
                    },
                ],
            ]);

            $this->controls->responsive()->margin('date_and_time_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-metadata' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]);

            $this->controls->responsive()->padding('date_and_time_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-metadata' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);
        });
        /**
         *
         * Comment Text
         *
         */
        $this->controls->tab_style_section('comment_text_style_section', [
            'label' => esc_html__('Comment Text', 'addons-for-elementor-builder'),
        ], function () {
            /**
             * Tabs
             */
            $this->controls->tabs('comment_text_tab_style', [
                'comment_text_odd' => [
                    'label' => esc_html__('Odd', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('comment_text_color_odd', [
                            'selectors' => [
                                '{{WRAPPER}} .even .afeb-comment-content' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->text_color('comment_text_link_color_odd', [
                            'label' => esc_html__('Link Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .even .afeb-comment-content a' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->typography([
                            'name' => 'comment_text_typography_odd',
                            'selector' => '{{WRAPPER}} .even .afeb-comment-content',
                        ]);
                    },
                ],
                'comment_text_even' => [
                    'label' => esc_html__('Even', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('comment_text_color_even', [
                            'selectors' => [
                                '{{WRAPPER}} .odd .afeb-comment-content' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->text_color('comment_text_link_color_even', [
                            'label' => esc_html__('Link Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .odd .afeb-comment-content a' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->typography([
                            'name' => 'comment_text_typography_even',
                            'selector' => '{{WRAPPER}} .odd .afeb-comment-content',
                        ]);
                    },
                ],
                'comment_text_by_post_author' => [
                    'label' => esc_html__('Post Author', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->text_color('comment_text_color_by_post_author', [
                            'selectors' => [
                                '{{WRAPPER}} .bypostauthor .afeb-comment-content' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->text_color('comment_text_link_color_by_post_author', [
                            'label' => esc_html__('Link Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .bypostauthor .afeb-comment-content a' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->typography([
                            'name' => 'comment_text_typography_by_post_author',
                            'selector' => '{{WRAPPER}} .bypostauthor .afeb-comment-content',
                        ]);
                    },
                ],
            ]);
        });
        /**
         *
         * Reply Link
         *
         */
        $this->controls->tab_style_section('reply_link_style_section', [
            'label' => esc_html__('Reply Link', 'addons-for-elementor-builder'),
        ], function () {
            /**
             * Tabs
             */
            $this->controls->tabs('reply_link_tab_style', [
                'reply_link_odd' => [
                    'label' => esc_html__('Odd', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->heading('reply_link_normal_odd', [
                            'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                        ]);

                        $this->controls->background([
                            'name' => 'reply_link_bg_odd',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .even .afeb-comment-reply a',
                        ]);

                        $this->controls->text_color('reply_link_color_odd', [
                            'selectors' => [
                                '{{WRAPPER}} .even .afeb-comment-reply a' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'reply_link_border_odd',
                            'selector' => '{{WRAPPER}} .even .afeb-comment-reply a',
                            'separator' => '',
                        ]);

                        $this->controls->heading('reply_link_hover_odd', [
                            'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                        ]);

                        $this->controls->background([
                            'name' => 'reply_link_bg_hover_odd',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .even .afeb-comment-reply a:hover',
                        ]);

                        $this->controls->text_color('reply_link_color_hover_odd', [
                            'selectors' => [
                                '{{WRAPPER}} .even .afeb-comment-reply a:hover' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'reply_link_border_hover_odd',
                            'selector' => '{{WRAPPER}} .even .afeb-comment-reply a:hover',
                            'separator' => '',
                        ]);
                    },
                ],
                'reply_link_even' => [
                    'label' => esc_html__('Even', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->heading('reply_link_normal_even', [
                            'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                        ]);

                        $this->controls->background([
                            'name' => 'reply_link_bg_even',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .odd .afeb-comment-reply a',
                        ]);

                        $this->controls->text_color('reply_link_color_even', [
                            'selectors' => [
                                '{{WRAPPER}} .odd .afeb-comment-reply a' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'reply_link_border_even',
                            'selector' => '{{WRAPPER}} .odd .afeb-comment-reply a',
                            'separator' => '',
                        ]);

                        $this->controls->heading('reply_link_hover_even', [
                            'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                        ]);

                        $this->controls->background([
                            'name' => 'reply_link_bg_hover_even',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .odd .afeb-comment-reply a:hover',
                        ]);

                        $this->controls->text_color('reply_link_color_hover_even', [
                            'selectors' => [
                                '{{WRAPPER}} .odd .afeb-comment-reply a:hover' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'reply_link_border_hover_even',
                            'selector' => '{{WRAPPER}} .odd .afeb-comment-reply a:hover',
                            'separator' => '',
                        ]);
                    },
                ],
                'reply_link_by_post_author' => [
                    'label' => esc_html__('Post Author', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->heading('reply_link_normal_by_post_author', [
                            'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                        ]);

                        $this->controls->background([
                            'name' => 'reply_link_bg_by_post_author',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .bypostauthor .afeb-comment-reply a',
                        ]);

                        $this->controls->text_color('reply_link_color_by_post_author', [
                            'selectors' => [
                                '{{WRAPPER}} .bypostauthor .afeb-comment-reply a' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'reply_link_border_by_post_author',
                            'selector' => '{{WRAPPER}} .bypostauthor .afeb-comment-reply a',
                            'separator' => '',
                        ]);

                        $this->controls->heading('reply_link_hover_by_post_author', [
                            'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                        ]);

                        $this->controls->background([
                            'name' => 'reply_link_bg_hover_by_post_author',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .bypostauthor .afeb-comment-reply a:hover',
                        ]);

                        $this->controls->text_color('reply_link_color_hover_by_post_author', [
                            'selectors' => [
                                '{{WRAPPER}} .bypostauthor .afeb-comment-reply a:hover' => 'color: {{VALUE}}',
                            ],
                        ]);

                        $this->controls->border([
                            'name' => 'reply_link_border_hover_by_post_author',
                            'selector' => '{{WRAPPER}} .bypostauthor .afeb-comment-reply a:hover',
                            'separator' => '',
                        ]);
                    },
                ],
            ]);

            $this->controls->responsive()->margin('reply_link_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-reply a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before'
            ]);

            $this->controls->responsive()->padding('reply_link_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-reply a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->border_radius('reply_link_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-reply a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->box_shadow([
                'name' => 'reply_link_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .afeb-comment-reply a',
            ]);
        });
        /**
         *
         * Comments Form Title
         *
         */
        $this->controls->tab_style_section('comments_form_title_style_section', [
            'label' => esc_html__('Comments Form Title', 'addons-for-elementor-builder'),
            'condition' => [
                'comment_form_title!' => ''
            ],
        ], function () {
            $this->controls->background([
                'name' => 'comments_form_title_bg',
                'exclude' => ['image'],
                'selector' => '{{WRAPPER}} .afeb-comment-reply-title',
            ]);

            $this->controls->text_color('comments_form_title_color', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-reply-title' => 'color: {{VALUE}}; fill: {{VALUE}};',
                ],
                'separator' => 'before',
            ]);

            $this->controls->typography([
                'name' => 'comments_form_title_typography',
                'selector' => '{{WRAPPER}} .afeb-comment-reply-title',
            ]);

            $this->controls->responsive()->margin('comments_form_title_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-reply-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->responsive()->padding('comments_form_title_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-reply-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->border([
                'name' => 'comments_form_title_border',
                'selector' => '{{WRAPPER}} .afeb-comment-reply-title',
            ]);

            $this->controls->responsive()->border_radius('comments_form_title_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-reply-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->controls->box_shadow([
                'name' => 'comments_form_title_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .afeb-comment-reply-title',
            ]);
        });
        /**
         *
         * Comments Form Fields
         *
         */
        $this->controls->tab_style_section('comments_form_fields_style_section', [
            'label' => esc_html__('Comments Form Fields', 'addons-for-elementor-builder'),
        ], function () {
            $this->controls->heading('comments_form_fields_logged_in_as', [
                'label' => esc_html__('Logged In As', 'addons-for-elementor-builder'),
            ]);

            $this->controls->text_color('comments_form_fields_logged_in_as_color', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-form .logged-in-as a' => 'color: {{VALUE}}',
                    '{{WRAPPER}} .afeb-comment-form .logged-in-as .required-field-message' => 'color: {{VALUE}}',
                ],
            ]);

            $this->controls->typography([
                'name' => 'comments_form_fields_logged_in_as_typography',
                'selector' => '{{WRAPPER}} .afeb-comment-form .logged-in-as',
            ]);

            $this->controls->heading('comments_form_fields_labels', [
                'label' => esc_html__('Label', 'addons-for-elementor-builder'),
                'separator' => 'before',
            ]);

            $this->controls->text_color('comments_form_fields_labels_color', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-form label' => 'color: {{VALUE}}',
                ],
            ]);

            $this->controls->typography([
                'name' => 'comments_form_fields_label_typography',
                'selector' => '{{WRAPPER}} .afeb-comment-form label',
            ]);


            $this->controls->responsive()->margin('comments_form_fields_labels_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-form label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
                'separator' => 'after',
            ]);
            /**
             * Tabs
             */
            $this->controls->tabs('comments_form_fields_tab_style', [
                'comments_form_fields_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'comments_form_fields_bg',
                            'exclude' => ['image'],
                            'selector' =>
                            '{{WRAPPER}} .afeb-comment-form input[type=text],' .
                                '{{WRAPPER}} .afeb-comment-form textarea',
                        ]);

                        $this->controls->text_color('comments_form_fields_color', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-comment-form input[type=text]' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .afeb-comment-form textarea' => 'color: {{VALUE}}',
                            ],
                            'separator' => 'before',
                        ]);

                        $this->controls->text_color('comments_form_fields_placeholder_color', [
                            'label'  => esc_html__('Placeholder Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-comment-form input[type=text]::placeholder' => 'color: {{VALUE}}; opacity: 1',
                                '{{WRAPPER}} .afeb-comment-form textarea::placeholder' => 'color: {{VALUE}}; opacity: 1',
                                '{{WRAPPER}} .afeb-comment-form input[type=text]::-ms-input-placeholder' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .afeb-comment-form textarea::-ms-input-placeholder' => 'color: {{VALUE}}',
                            ],
                        ]);
                    },
                ],
                'comments_form_fields_focus' => [
                    'label' => esc_html__('Focus', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'comments_form_fields_bg_focus',
                            'exclude' => ['image'],
                            'selector' =>
                            '{{WRAPPER}} .afeb-comment-form input[type=text]:focus,' .
                                '{{WRAPPER}} .afeb-comment-form textarea:focus',
                        ]);

                        $this->controls->text_color('comments_form_fields_color_focus', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-comment-form input[type=text]:focus' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .afeb-comment-form textarea:focus' => 'color: {{VALUE}}',
                            ],
                            'separator' => 'before',
                        ]);

                        $this->controls->text_color('comments_form_fields_placeholder_color_focus', [
                            'label'  => esc_html__('Placeholder Color', 'addons-for-elementor-builder'),
                            'selectors' => [
                                '{{WRAPPER}} .afeb-comment-form input[type=text]:focus::placeholder' => 'color: {{VALUE}}; opacity: 1',
                                '{{WRAPPER}} .afeb-comment-form textarea:focus::placeholder' => 'color: {{VALUE}}; opacity: 1',
                                '{{WRAPPER}} .afeb-comment-form input[type=text]:focus::-ms-input-placeholder' => 'color: {{VALUE}}',
                                '{{WRAPPER}} .afeb-comment-form textarea:focus::-ms-input-placeholder' => 'color: {{VALUE}}',
                            ],
                        ]);
                    },
                ],
            ]);

            $this->controls->divider('separator_comments_form_fields_style_controls');

            $this->controls->typography([
                'name' => 'comments_form_fields_typography',
                'selector' => '{{WRAPPER}} .afeb-comment-form input[type=text],' .
                    '{{WRAPPER}} .afeb-comment-form textarea',
            ]);

            $this->controls->responsive()->margin('comments_form_fields_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-form input[type=text]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                    '{{WRAPPER}} .afeb-comment-form textarea' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]);

            $this->controls->responsive()->padding('comments_form_fields_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-form input[type=text]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                    '{{WRAPPER}} .afeb-comment-form textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]);

            $this->controls->border([
                'name' => 'comments_form_fields_border',
                'selector' => '{{WRAPPER}} .afeb-comment-form input[type=text],' .
                    '{{WRAPPER}} .afeb-comment-form textarea',
            ]);

            $this->controls->responsive()->border_radius('comments_form_fields_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-comment-form input[type=text]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                    '{{WRAPPER}} .afeb-comment-form textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]);

            $this->controls->box_shadow([
                'name' => 'comments_form_fields_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .afeb-comment-form input[type=text],' .
                    '{{WRAPPER}} .afeb-comment-form textarea',
            ]);
        });
        /**
         *
         * Submit Button
         *
         */
        $this->controls->tab_style_section('submit_button_style_section', [
            'label' => esc_html__('Submit Button', 'addons-for-elementor-builder'),
        ], function () {
            /**
             * Tabs
             */
            $this->controls->tabs('submit_button_tab_style', [
                'submit_button_normal' => [
                    'label' => esc_html__('Normal', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'submit_button_bg',
                            'exclude' => ['image'],
                            'selector' => '{{WRAPPER}} .afeb-submit-comment',
                        ]);

                        $this->controls->text_color('submit_button_color', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-submit-comment' => 'color: {{VALUE}}',
                            ],
                            'separator' => 'before',
                        ]);
                    },
                ],
                'submit_button_hover' => [
                    'label' => esc_html__('Hover', 'addons-for-elementor-builder'),
                    'callback' => function () {

                        $this->controls->background([
                            'name' => 'submit_button_bg_hover',
                            'exclude' => ['image'],
                            'selector' =>
                            '{{WRAPPER}} .afeb-submit-comment:hover',
                        ]);

                        $this->controls->text_color('submit_button_color_hover', [
                            'selectors' => [
                                '{{WRAPPER}} .afeb-submit-comment:hover' => 'color: {{VALUE}}',
                            ],
                            'separator' => 'before',
                        ]);
                    },
                ],
            ]);

            // $this->controls->divider('separator_submit_button_style_controls');

            $this->controls->typography([
                'name' => 'submit_button_typography',
                'selector' => '{{WRAPPER}} .afeb-submit-comment',
            ]);

            $this->controls->responsive()->margin('submit_button_margin', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-submit-comment' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]);

            $this->controls->responsive()->padding('submit_button_padding', [
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-submit-comment' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]);

            $this->controls->border([
                'name' => 'submit_button_border',
                'selector' => '{{WRAPPER}} .afeb-submit-comment',
            ]);

            $this->controls->responsive()->border_radius('submit_button_border_radius', [
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .afeb-submit-comment' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]);

            $this->controls->box_shadow([
                'name' => 'submit_button_box_shadow',
                'exclude' => [
                    'box_shadow_position',
                ],
                'selector' => '{{WRAPPER}} .afeb-submit-comment'
            ]);
        });
    }

    /**
     * Render attributes
     *
     * @since 1.3.0
     * 
     * @param array $settings
     */
    protected function render_attrs($settings = [])
    {
        $this->add_render_attribute(
            [
                'comments_form' => ['class' => "afeb-comments-form",]
            ]
        );
    }

    /**
     * Render CommentsForm widget output on the frontend
     *
     * @since 1.3.0
     */
    protected function render()
    {
        if (!comments_open(get_the_ID())) return;

        $settings = $this->get_settings_for_display();

        if (Helper::is_edit_mode()) {
            $store_current_user = wp_get_current_user()->ID;
            wp_set_current_user(0);
        }

        $this->render_attrs($settings);

        ?>
        <div <?php $this->print_render_attribute_string('comments_form'); ?>>
        <?php

        $count = get_comments_number(get_the_ID());

        if ($count) {
            $get_comments = get_comments(['post_id' => get_the_ID()]);

            echo '<ul class="afeb-comments-list">';
            wp_list_comments(['callback' => [$this, 'list_comments']], $get_comments);
            echo '</ul>';

            if (get_comment_pages_count($get_comments) > 1 && get_option('page_comments')) {
                echo '<div class="afeb-comments-navigation afeb-comments-navigation-' . esc_attr($settings['comments_navigation_align']) . '">';
                paginate_comments_links([
                    'base' => add_query_arg('cpage', '%#%'),
                    'format' => '',
                    'total' => get_comment_pages_count($get_comments),
                    'echo' => true,
                    'add_fragment' => '#comments',
                    'prev_text' => '<i class="eicon-arrow-left"></i> ' . esc_html__('Previous', 'addons-for-elementor-builder'),
                    'next_text' => esc_html__('Next', 'addons-for-elementor-builder') . ' <i class="eicon-arrow-right"></i>',
                ]);
                echo '</div>';
            }
        }

        add_filter('comment_form_default_fields', function ($defaults) {
            $settings = $this->get_settings_for_display();
            $author_label = $email_label = $url_label = '';
            $author_place_holder = $email_place_holder = $url_place_holder = '';
            $req = get_option('require_name_email');

            if ($settings['comment_form_labels'] == 'yes') {
                $author_label = '<label>' . esc_html__('Name', 'addons-for-elementor-builder') . ($req ? '<span>*</span>' : '') . '</label>';
                $email_label = '<label>' . esc_html__('Email', 'addons-for-elementor-builder') . ($req ? '<span>*</span>' : '') . '</label>';
                $url_label = '<label>' . esc_html__('Website', 'addons-for-elementor-builder') . '</label>';
            }

            if ($settings['comment_form_placeholders'] == 'yes') {
                $author_place_holder = esc_html__('Name', 'addons-for-elementor-builder') . ($req ? '*' : '');
                $email_place_holder = esc_html__('Email', 'addons-for-elementor-builder') . ($req ? '*' : '');
                $url_place_holder = esc_html__('Website', 'addons-for-elementor-builder');
            }

            $fields = [
                'author' => '<div class="afeb-comment-form-fields"><div class="afeb-comment-form-author">' . $author_label .
                    '<input type="text" name="author" placeholder="' . esc_attr($author_place_holder) . '"/></div>',
                'email' => '<div class="afeb-comment-form-email">' . $email_label .
                    '<input type="text" name="email" placeholder="' . esc_attr($email_place_holder) . '"/></div>',
                'url' => '<div class="afeb-comment-form-url">' . $url_label .
                    '<input type="text" name="url" placeholder="' . esc_url($url_place_holder) . '"/></div></div>',
            ];

            if ($settings['comment_form_website'] == '')
                $fields['url'] = '</div>';

            return $fields;
        });

        add_filter('comment_form_defaults', function ($defaults) {
            $settings = $this->get_settings_for_display();
            $text_label = $text_place_holder = '';
            $req = get_option('require_name_email');

            if ($settings['comment_form_labels'] == 'yes')
                $text_label = '<label>' . esc_html__('Message', 'addons-for-elementor-builder') . ($req ? '<span>*</span>' : '') . '</label>';

            if ($settings['comment_form_placeholders'] == 'yes')
                $text_place_holder = esc_html__('Message', 'addons-for-elementor-builder') . ($req ? '*' : '');

            $defaults['id_form'] = 'afeb-comment-form';
            $defaults['class_form'] = 'afeb-comment-form';

            if ($settings['comment_form_website'] == '')
                $defaults['class_form'] .= ' afeb-no-url';

            $defaults['title_reply'] = $settings['comment_form_title'];
            $defaults['title_reply_before'] = '<h3 id="afeb-reply-title" class="afeb-comment-reply-title">';
            $defaults['title_reply_after'] = '</h3>';

            $defaults['comment_field']  = '<div class="afeb-comment-form-text">' . $text_label;
            $defaults['comment_field'] .= '<textarea name="comment" placeholder="' . esc_attr($text_place_holder) . '" cols="45" rows="8" maxlength="65525"></textarea>';
            $defaults['comment_field'] .= '</div>';

            $defaults['id_submit'] = 'afeb-submit-comment';
            $defaults['class_submit'] = 'afeb-submit-comment';
            $defaults['label_submit'] = $settings['comment_form_submit_text'];

            return $defaults;
        });

        comment_form();

        echo '</div>';

        if (Helper::is_edit_mode()) {
            wp_set_current_user($store_current_user);
        }
    }

    public function list_comments($comment, $args, $depth)
    {
        $comment_class = implode(' ', get_comment_class($comment->has_children ? 'parent' : '', $comment));
        $author_url = get_comment_author_url($comment);
        $author_name = get_comment_author($comment);

        echo '<li id="comment-' . esc_attr(get_comment_ID()) . '" class="' . esc_attr($comment_class) . '">';
        echo '<article class="afeb-post-comment elementor-clearfix">';

        $settings = $this->get_settings_for_display();

        if (!empty($settings['comments_avatar'])) {
            echo '<div class="afeb-comment-avatar">';
            echo get_avatar($comment, $settings['comments_avatar_size']);
            echo '</div>';
        }

        echo '<div class="afeb-comment-meta">
                <div class="afeb-comment-author">';

        if ($author_url == '') echo '<span>' . esc_html($author_name) . '</span>';
        else echo '<a href="' . esc_url($author_url) . '">' . esc_html($author_name) . '</a>';

        echo '</div>';

        echo '<div class="afeb-comment-metadata elementor-clearfix">';
        echo '<span>' . esc_html(get_comment_date('', $comment)) . esc_html__(' at ', 'addons-for-elementor-builder') . esc_html(get_comment_time()) . '</span>';
        edit_comment_link(esc_html__('Edit', 'addons-for-elementor-builder'), ' | ', '');

        if ($settings['comments_reply_location'] == 'inline') {
            comment_reply_link(
                array_merge($args, [
                    'depth' => $depth,
                    'max_depth' => $args['max_depth'],
                    'before' => '<div class="afeb-comment-reply">',
                    'after' => '</div>',
                ])
            );
        }

        if ($comment->comment_approved == '0')
            echo '<p>' . esc_html__('Your comment is awaiting moderation.', 'addons-for-elementor-builder') . '</p>';

        echo '</div>
            </div>';

        echo '<div class="afeb-comment-content">';
        comment_text($comment);
        echo '</div>';

        if ($settings['comments_reply_location'] == 'separate') {
            comment_reply_link(
                array_merge($args, [
                    'depth' => $depth,
                    'max_depth' => $args['max_depth'],
                    'before' => '<div class="afeb-comment-reply">',
                    'after' => '</div>',
                ])
            );
        }

        echo '</article>
            </li>';
    }
}
