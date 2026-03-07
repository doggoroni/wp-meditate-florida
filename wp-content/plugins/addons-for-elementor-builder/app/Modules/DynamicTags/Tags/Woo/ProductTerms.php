<?php
namespace AFEB\Modules\DynamicTags\Tags\Woo;

use Elementor\Controls_Manager;
use Elementor\Modules\DynamicTags\Module as DT;

class ProductTerms extends WooProductTag
{
    public function get_name()
    {
        return 'afeb_woo_product_terms';
    }

    public function get_title()
    {
        return esc_html__('Product Terms', 'addons-for-elementor-builder');
    }

    public function get_categories()
    {
        return [DT::TEXT_CATEGORY];
    }

    protected function register_controls()
    {
        $this->add_control('taxonomy', [
            'label' => esc_html__('Taxonomy', 'addons-for-elementor-builder'),
            'type' => Controls_Manager::SELECT,
            'options' => [
                'product_cat' => esc_html__('Product Categories', 'addons-for-elementor-builder'),
                'product_tag' => esc_html__('Product Tags', 'addons-for-elementor-builder'),
            ],
            'default' => 'product_cat',
        ]);

        $this->add_control('separator', [
            'label' => esc_html__('Separator', 'addons-for-elementor-builder'),
            'type' => Controls_Manager::TEXT,
            'default' => ', ',
            'dynamic' => ['active' => false],
        ]);

        $this->add_control('link', [
            'label' => esc_html__('Link', 'addons-for-elementor-builder'),
            'type' => Controls_Manager::SWITCHER,
            'default' => 'yes',
        ]);
    }

    public function render()
    {
        $product = $this->get_product();
        if (!$product) return;

        $settings = $this->get_settings();
        $taxonomy = $settings['taxonomy'];
        $separator = trim($settings['separator']) !== '' ? $settings['separator'] : ', ';

        if ('yes' === $settings['link'])
        {
            $terms = get_the_term_list($product->get_id(), $taxonomy, '', $separator);
            if (is_wp_error($terms) || !$terms) return;

            $this->echo_text($terms);
        }
        else
        {
            $terms = get_the_terms($product->get_id(), $taxonomy);
            if (is_wp_error($terms) || empty($terms)) return;

            $names = wp_list_pluck($terms, 'name');
            $this->echo_text(implode($separator, $names));
        }
    }
}
