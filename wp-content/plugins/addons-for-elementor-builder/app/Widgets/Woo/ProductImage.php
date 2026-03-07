<?php

namespace AFEB\Widgets\Woo;

use AFEB\Controls\Helper;
use AFEB\Helper as GeneralHelper;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) exit;

class ProductImage extends Widget_Base
{
    protected $generalHelper;

    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
        $this->generalHelper = new GeneralHelper();
    }

    public function get_name(): string
    {
        return 'afeb-product-image';
    }

    public function get_title(): string
    {
        return __('Product Image', 'addons-for-elementor-builder');
    }

    public function get_icon(): string
    {
        return 'afeb-iconsvg-product-img';
    }

    public function get_categories(): array
    {
        return ['afeb-asic', 'woocommerce-elements-single'];
    }

    public function get_keywords(): array
    {
        return ['product image', 'woocommerce', 'image', 'gallery'];
    }

    public function get_style_depends(): array
    {
        return ['afeb-woo-product-image'];
    }

    public function get_script_depends(): array
    {
        return ['afeb-woo-product-images'];
    }

    public function register_controls()
    {
        $helper = new Helper($this);

        /**
         * ------------------
         * Content: General
         */
        $helper->tab_content_section('section_content', [
            'label' => esc_html__('Content', 'addons-for-elementor-builder'),
        ], function () use ($helper)
        {
            $helper->select('product_source', [
                'label' => esc_html__('Product Source', 'addons-for-elementor-builder'),
                'options' => [
                    'current' => esc_html__('Current Product', 'addons-for-elementor-builder'),
                    'specific' => esc_html__('Specific Product', 'addons-for-elementor-builder'),
                ],
                'default' => 'current',
                'render_type' => 'template',
                'label_block' => true,
            ]);
            $helper->dynamic_select('product_select', [
                'options' => 'get_posts_by_type',
                'label' => esc_html__('Product Select', 'addons-for-elementor-builder'),
                'query_slug' => 'product',
                'condition' => ['product_source' => 'specific'],
                'render_type' => 'template',
            ]);
            $helper->number('product_id', [
                'label' => esc_html__('Or Enter Product ID', 'addons-for-elementor-builder'),
                'condition' => ['product_source' => 'specific'],
                'render_type' => 'template',
            ]);
            $helper->select('gallery_layout', [
                'label' => esc_html__('Gallery Layout', 'addons-for-elementor-builder'),
                'options' => [
                    'slider' => __('Slider', 'addons-for-elementor-builder'),
                    'grid' => __('Grid', 'addons-for-elementor-builder'),
                    'stacked' => __('Stacked', 'addons-for-elementor-builder'),
                ],
                'default' => 'slider',
            ]);
            $helper->switcher('show_thumbs', [
                'label' => __('Show Thumbnails', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);
            $helper->select('thumbs_position', [
                'label' => esc_html__('Thumbs Position', 'addons-for-elementor-builder'),
                'options' => [
                    'bottom' => esc_html__('Bottom', 'addons-for-elementor-builder'),
                    'top' => esc_html__('Top', 'addons-for-elementor-builder'),
                    // 'left'   => esc_html__('Left', 'addons-for-elementor-builder'),
                    //'right'  => esc_html__('Right', 'addons-for-elementor-builder'),
                ],
                'default' => 'bottom',
                'render_type' => 'template',
                'prefix_class' => 'afeb-thumbs-',
            ]);
            $helper->switcher('enable_lightbox', [
                'label' => __('Enable Lightbox', 'addons-for-elementor-builder'),
                'default' => 'yes',
            ]);

        });
        do_action('afeb/widget/content/after_render_content_section', $this);

        /** --- Style Sections from the EA-style version I gave you earlier --- */
        $helper->tab_style_section('section_style_main', [
            'label' => esc_html__('Main Image', 'addons-for-elementor-builder'),
        ], function () use ($helper)
        {
            $selector = '{{WRAPPER}} .afeb-main-swiper img, {{WRAPPER}} .afeb-grid img, {{WRAPPER}} .afeb-stacked img';
            $helper->slider('main_width', ['label' => __('Width', 'addons-for-elementor-builder'), 'selectors' => [$selector => 'width:{{SIZE}}{{UNIT}};']]);
            $helper->slider('main_height', ['label' => __('Height', 'addons-for-elementor-builder'), 'selectors' => [$selector => 'height:{{SIZE}}{{UNIT}};object-fit:cover;']]);
            $helper->border(['selector' => $selector]);
            $helper->border_radius('main_radius', ['selectors' => [$selector => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
            //$helper->box_shadow(['selector' => $selector]);
            $helper->background_color('main_bg', ['selectors' => [$selector => 'background-color:{{VALUE}};']]);
            $helper->padding('main_padding', ['selectors' => [$selector => 'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
            $helper->margin('main_margin', ['selectors' => [$selector => 'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        });

        $helper->tab_style_section('section_style_thumbs', [
            'label' => esc_html__('Thumbnails', 'addons-for-elementor-builder'),
        ], function () use ($helper)
        {
            $selector = '{{WRAPPER}} .afeb-woo-pi-thumbs img';
            $helper->slider('thumb_size', ['label' => __('Size', 'addons-for-elementor-builder'), 'selectors' => [$selector => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};']]);
            $helper->slider('thumb_gap', [
                'label' => __('Gap', 'addons-for-elementor-builder'),
                'selectors' => [
                    '{{WRAPPER}} .afeb-woo-pi.layout-grid .afeb-grid' => '--afeb-main-gap:{{SIZE}}{{UNIT}}!important;',
                    '{{WRAPPER}} .afeb-woo-pi.layout-slider .afeb-woo-pi-thumbs .swiper-slide' => 'margin-right:{{SIZE}}{{UNIT}}!important;',
                ],
            ]);
            $helper->border(['selector' => $selector]);
            $helper->border_radius('thumb_radius', ['selectors' => [$selector => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
            $helper->box_shadow([
                'name' => 'product_image_thumb_box_shadow',
                'selector' => $selector,
            ]);
            $helper->color('thumb_border_active', ['label' => __('Active Border Color', 'addons-for-elementor-builder'), 'selectors' => ['{{WRAPPER}} .afeb-woo-pi-thumbs .swiper-slide-thumb-active img' => 'border-color: {{VALUE}};']]);
        });

        $helper->tab_style_section('section_style_arrows', [
            'label' => esc_html__('Slider Arrows', 'addons-for-elementor-builder'),
        ], function () use ($helper)
        {
            $selector = '{{WRAPPER}} .swiper-button-next, {{WRAPPER}} .swiper-button-prev';

            $helper->slider('arrow_padding', [
                'label' => __('Padding', 'addons-for-elementor-builder'),
                'selectors' => [$selector => 'padding: {{SIZE}}{{UNIT}};'],
            ]);

            $helper->slider('arrow_size', ['label' => __('Size', 'addons-for-elementor-builder'), 'selectors' => [$selector => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};']]);
            $helper->color('arrow_color', ['label' => __('Color', 'addons-for-elementor-builder'), 'selectors' => [$selector => 'color: {{VALUE}};']]);
            $helper->background_color('arrow_bg', ['label' => __('Background', 'addons-for-elementor-builder'), 'selectors' => [$selector => 'background-color:{{VALUE}};']]);
            $helper->border_radius('arrow_radius', ['selectors' => [$selector => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
            $helper->box_shadow([
                'name' => 'product_image_arrow_box_shadow',
                'selector' => $selector,
            ]);
        });

        $helper->tab_style_section('section_style_dots', [
            'label' => esc_html__('Pagination Dots', 'addons-for-elementor-builder'),
        ], function () use ($helper)
        {
            $selector = '{{WRAPPER}} .swiper-pagination-bullet';
            $helper->slider('dot_size', ['label' => __('Size', 'addons-for-elementor-builder'), 'selectors' => [$selector => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};']]);
            $helper->color('dot_color', ['label' => __('Color', 'addons-for-elementor-builder'), 'selectors' => [$selector => 'background-color:{{VALUE}};']]);
            $helper->color('dot_color_active', ['label' => __('Active Color', 'addons-for-elementor-builder'), 'selectors' => ['{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color:{{VALUE}};']]);
        });

        $helper->tab_style_section('section_style_lightbox', [
            'label' => esc_html__('Lightbox Overlay Icon', 'addons-for-elementor-builder'),
        ], function () use ($helper)
        {
            $selector = '{{WRAPPER}} a.glightbox::after';
            $helper->slider('lightbox_icon_size', ['label' => __('Icon Size', 'addons-for-elementor-builder'), 'selectors' => [$selector => 'font-size:{{SIZE}}{{UNIT}};']]);
            $helper->color('lightbox_icon_color', ['label' => __('Icon Color', 'addons-for-elementor-builder'), 'selectors' => [$selector => 'color:{{VALUE}};']]);
            $helper->background_color('lightbox_icon_bg', ['label' => __('Background Color', 'addons-for-elementor-builder'), 'selectors' => [$selector => 'background-color:{{VALUE}};']]);
            $helper->border_radius('lightbox_icon_radius', ['selectors' => [$selector => 'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};']]);
        });
    }

    protected function render()
    {
        $settings = $this->get_settings_for_display();
        $position = $settings['thumbs_position'] ?? 'bottom';
        $layout = $settings['gallery_layout'] ?? 'slider';

        // Wrapper attributes
        $this->add_render_attribute('wrapper', 'class', [
            'afeb-woo-pi',
            'afeb-thumbs-' . esc_attr($position),
            'layout-' . esc_attr($layout),
        ]);
        if (!empty($settings['feat_zoom']) && $settings['feat_zoom'] === 'yes')
        {
            $this->add_render_attribute('wrapper', 'class', 'zoom-enabled');
        }

        $this->add_render_attribute('wrapper', 'data-layout', esc_attr($layout));
        $this->add_render_attribute('wrapper', 'data-prev-icon', esc_attr($settings['carousel_prev_icon']['value'] ?? 'fas fa-arrow-circle-left'));
        $this->add_render_attribute('wrapper', 'data-next-icon', esc_attr($settings['carousel_next_icon']['value'] ?? 'fas fa-arrow-circle-right'));
        $this->add_render_attribute('wrapper', 'data-zoom-icon', esc_attr($settings['zoom_icon']['value'] ?? 'fas fa-search-plus'));

        // Detect product ID
        $product_id = 0;
        if (($settings['product_source'] ?? '') === 'specific')
        {
            if (!empty($settings['product_select']))
            {
                $product_id = (int) $settings['product_select'];
            }
            else if (!empty($settings['product_id']))
            {
                $product_id = (int) $settings['product_id'];
            }
        }
        else
        {
            $product_id = get_the_ID();
            if ($this->generalHelper->is_edit_mode() && get_post_type($product_id) !== 'product')
            {
                $preview_product = get_posts([
                    'post_type' => 'product',
                    'status' => 'publish',
                    'posts_per_page' => 1,
                    'orderby' => 'date',
                    'order' => 'DESC',
                    'fields' => 'ids',
                ]);
                $product_id = $preview_product[0] ?? 0;
            }
        }
        $product = $product_id ? wc_get_product($product_id) : null;

        echo '<div id="afeb-pi-' . esc_attr($this->get_id()) . '" ' . $this->get_render_attribute_string('wrapper') . '>';

        if ($product)
        {
            $main_id = $product->get_image_id();
            $gallery_ids = $product->get_gallery_image_ids();
            $all_images = [];
            if ($main_id) $all_images[] = $main_id;
            if (!empty($gallery_ids)) $all_images = array_merge($all_images, $gallery_ids);

            $gallery_name = 'afeb-gallery-' . esc_attr($this->get_id());

            switch ($layout)
            {
                case 'slider':
                    // Main slider
                    echo '<div class="afeb-woo-pi-featured"><div class="afeb-main-swiper swiper"><div class="swiper-wrapper">';
                    foreach ($all_images as $gid)
                    {
                        $full_url = wp_get_attachment_url($gid);
                        $img_html = wp_get_attachment_image($gid, 'woocommerce_single');
                        echo '<div class="swiper-slide">';
                        if (!empty($settings['enable_lightbox']) && $settings['enable_lightbox'] === 'yes')
                        {
                            echo '<a href="' . esc_url($full_url) . '" class="glightbox" data-gallery="' . esc_attr($gallery_name) . '">' . $img_html . '</a>';
                        }
                        else
                        {
                            echo $img_html;
                        }
                        echo '</div>';
                    }
                    echo '</div></div></div>';

                    // Thumbnails
                    if (!empty($settings['show_thumbs']) && $settings['show_thumbs'] === 'yes')
                    {
                        echo '<div class="afeb-woo-pi-thumbs swiper"><div class="swiper-wrapper">';
                        foreach ($all_images as $gid)
                        {
                            $thumb_url = wp_get_attachment_url($gid);
                            $thumb_html = wp_get_attachment_image($gid, 'woocommerce_thumbnail');
                            echo '<div class="swiper-slide">';
                            if (!empty($settings['enable_lightbox']) && $settings['enable_lightbox'] === 'yes')
                            {
                                echo '<a href="' . esc_url($thumb_url) . '" class="glightbox" data-gallery="' . esc_attr($gallery_name) . '">' . $thumb_html . '</a>';
                            }
                            else
                            {
                                echo $thumb_html;
                            }
                            echo '</div>';
                        }
                        echo '</div></div>';
                    }
                    break;

                case 'grid':
                    echo '<div class="afeb-woo-pi-featured afeb-grid">';
                    foreach ($all_images as $gid)
                    {
                        $full_url = wp_get_attachment_url($gid);
                        $img_html = wp_get_attachment_image($gid, 'woocommerce_single');
                        if (!empty($settings['enable_lightbox']) && $settings['enable_lightbox'] === 'yes')
                        {
                            echo '<a href="' . esc_url($full_url) . '" class="glightbox" data-gallery="' . esc_attr($gallery_name) . '">' . $img_html . '</a>';
                        }
                        else
                        {
                            echo $img_html;
                        }
                    }
                    echo '</div>';
                    break;

                case 'stacked':
                    echo '<div class="afeb-woo-pi-featured afeb-stacked">';
                    foreach ($all_images as $gid)
                    {
                        $full_url = wp_get_attachment_url($gid);
                        $img_html = wp_get_attachment_image($gid, 'woocommerce_single');
                        if (!empty($settings['enable_lightbox']) && $settings['enable_lightbox'] === 'yes')
                        {
                            echo '<a href="' . esc_url($full_url) . '" class="glightbox" data-gallery="' . esc_attr($gallery_name) . '">' . $img_html . '</a>';
                        }
                        else
                        {
                            echo $img_html;
                        }
                    }
                    echo '</div>';
                    break;
            }

        }
        else
        {
            // Placeholder handling for no product
            $placeholder_main = wc_placeholder_img_src('woocommerce_single');
            $placeholder_thumb = wc_placeholder_img_src();

            if ($layout === 'slider')
            {
                echo '<div class="afeb-woo-pi-featured"><div class="afeb-main-swiper swiper"><div class="swiper-wrapper">';
                echo '<div class="swiper-slide"><img src="' . esc_url($placeholder_main) . '" alt=""></div>';
                echo '</div></div></div>';
                if (!empty($settings['show_thumbs']) && $settings['show_thumbs'] === 'yes')
                {
                    echo '<div class="afeb-woo-pi-thumbs swiper"><div class="swiper-wrapper">';
                    for ($i = 0; $i < 4; $i++)
                    {
                        echo '<div class="swiper-slide"><img src="' . esc_url($placeholder_thumb) . '" alt=""></div>';
                    }
                    echo '</div></div>';
                }
            }
            else if ($layout === 'grid')
            {
                echo '<div class="afeb-woo-pi-featured afeb-grid">';
                for ($i = 0; $i < 4; $i++)
                {
                    echo '<img src="' . esc_url($placeholder_main) . '" alt="">';
                }
                echo '</div>';
            }
            else
            { // stacked
                echo '<div class="afeb-woo-pi-featured afeb-stacked">';
                for ($i = 0; $i < 4; $i++)
                {
                    echo '<img src="' . esc_url($placeholder_main) . '" alt="">';
                }
                echo '</div>';
            }
        }

        echo '</div>'; // wrapper
    }
}
