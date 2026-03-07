<?php
namespace AFEB\Widgets\Woo;

use AFEB\Assets;
use Elementor\Widget_Base;
use AFEB\Controls\Helper;
use WC_Product;

/**
 * Woo Product Tags Widget
 *
 * Display WooCommerce product tags for current or selected product
 *
 * @since 1.0.0
 */
class ProductTags extends Widget_Base
{

    /**
     * @var Helper
     */
    protected $helper;

    public function __construct($data = [], $args = null)
    {
        parent::__construct($data, $args);
        $this->helper = new Helper($this);

        if (class_exists('\AFEB\Assets'))
        {
            $assets = new Assets();
            $assets->woo_product_tags_style();
        }
    }

    public function get_name(): string
    {
        return 'afeb_woo_product_tags';
    }

    public function get_title(): string
    {
        return esc_html__('Woo Product Tags', 'addons-for-elementor-builder');
    }

    public function get_icon(): string
    {
        return 'afeb-iconsvg-product-tags';
    }

    public function get_categories(): array
    {
        return ['afeb-basic', 'woocommerce-elements-single', 'single'];
    }

    public function get_keywords(): array
    {
        return ['woocommerce', 'product', 'tags', 'wc-tags', esc_html__('Product Tags', 'addons-for-elementor-builder')];
    }

    /**
     * Register widget controls
     */
    protected function register_controls()
    {
        // ==== Content Tab: Product Source ====
        $this->helper->tab_content_section('section_product_source', [
            'label' => esc_html__('Product Source', 'addons-for-elementor-builder'),
        ], function ()
        {
            $this->helper->select('product_source', [
                'label' => esc_html__('Source', 'addons-for-elementor-builder'),
                'options' => [
                    'current' => esc_html__('Current Product', 'addons-for-elementor-builder'),
                    'specific' => esc_html__('Specific Product', 'addons-for-elementor-builder'),
                ],
                'default' => 'current',
            ]);

            $this->helper->dynamic_select('specific_product', [
                'label' => esc_html__('Select Product', 'addons-for-elementor-builder'),
                'query_slug' => 'product',
                'options' => 'get_posts_by_type',
                'condition' => [
                    'product_source' => 'specific',
                ],
            ]);
        });

        // ==== Style Tab: Tags Container ====
        $this->helper->tab_style_section('section_tags_style', [
            'label' => esc_html__('Tags Container', 'addons-for-elementor-builder'),
        ], function ()
        {

            $this->helper->choose('tags_layout', [
                'label' => esc_html__('Layout', 'addons-for-elementor-builder'),
                'options' => [
                    'inline' => ['title' => esc_html__('Inline', 'addons-for-elementor-builder'), 'icon' => 'eicon-ellipsis-h'],
                    'masonry' => ['title' => esc_html__('Masonry', 'addons-for-elementor-builder'), 'icon' => 'eicon-post-list'],
                    'block' => ['title' => esc_html__('Block', 'addons-for-elementor-builder'), 'icon' => 'eicon-editor-list-ul'],
                ],
                'default' => 'inline',
                'toggle' => false,
            ]);

            $this->helper->background(['selector' => '{{WRAPPER}} .afeb-woo-tags']);
            $this->helper->border(['selector' => '{{WRAPPER}} .afeb-woo-tags']);
            $this->helper->box_shadow([
                'name' => 'tags_container_box_shadow',
                'selector' => '{{WRAPPER}} .afeb-woo-tags',
            ]);

            $this->helper->padding('tags_padding', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-woo-tags' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);
            $this->helper->margin('tags_margin', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-woo-tags' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);
        });

        // ==== Style Tab: Individual Tag ====
        $this->helper->tab_style_section('section_tag_item_style', [
            'label' => esc_html__('Individual Tag', 'addons-for-elementor-builder'),
        ], function ()
        {

            $this->helper->text_color('tag_text_color', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-woo-tag' => 'color: {{VALUE}};',
                ],
            ]);

            $this->helper->background_color('tag_bg_color', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-woo-tag' => 'background-color: {{VALUE}};',
                ],
            ]);

            $this->helper->border([
                'selector' => '{{WRAPPER}} .afeb-woo-tag',
                'name' => 'tag_border',
            ]);

            $this->helper->border_radius('tag_border_radius', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-woo-tag' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->helper->padding('tag_padding', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-woo-tag' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->helper->margin('tag_margin', [
                'selectors' => [
                    '{{WRAPPER}} .afeb-woo-tag' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]);

            $this->helper->typography([
                'name' => 'tag_typography',
                'selector' => '{{WRAPPER}} .afeb-woo-tag',
            ]);

            $this->helper->box_shadow([
                'name' => 'tag_box_shadow',
                'selector' => '{{WRAPPER}} .afeb-woo-tag',
            ]);
        });
    }

    /**
     * Render the widget output
     */
    protected function render()
    {
        if (!function_exists('wc_get_product'))
        {
            echo '<div class="afeb-woo-tags">' . esc_html__('WooCommerce not active.', 'addons-for-elementor-builder') . '</div>';
            return;
        }

        $settings = $this->get_settings_for_display();
        $product = null;

        if ($settings['product_source'] === 'current')
        {
            global $product;

            // If no product in context, fetch latest published product
            if (!$product instanceof WC_Product)
            {
                $latest_products = wc_get_products([
                    'status' => 'publish',
                    'limit' => 1,
                    'orderby' => 'date',
                    'order' => 'DESC',
                ]);
                if (!empty($latest_products))
                {
                    $product = $latest_products[0];
                }
            }
        }
        else if (!empty($settings['specific_product']))
        {
            $product = wc_get_product((int) $settings['specific_product']);
        }

        // Fallback if still no product
        if (!$product instanceof WC_Product)
        {
            echo '<div class="afeb-woo-tags">' . esc_html__('No product found.', 'addons-for-elementor-builder') . '</div>';
            return;
        }

        $tag_ids = $product->get_tag_ids();
        if (empty($tag_ids))
        {
            echo '<div class="afeb-woo-tags">' . esc_html__('No tags assigned.', 'addons-for-elementor-builder') . '</div>';
            return;
        }

        $layout_class = !empty($settings['tags_layout']) ? 'layout-' . esc_attr($settings['tags_layout']) : 'layout-inline';

        echo '<div class="afeb-woo-tags ' . $layout_class . '">';
        foreach ($tag_ids as $tag_id)
        {
            $term = get_term($tag_id, 'product_tag');
            if ($term && !is_wp_error($term))
            {
                echo '<span class="afeb-woo-tag">' . esc_html($term->name) . '</span>';
            }
        }
        echo '</div>';
    }
}
