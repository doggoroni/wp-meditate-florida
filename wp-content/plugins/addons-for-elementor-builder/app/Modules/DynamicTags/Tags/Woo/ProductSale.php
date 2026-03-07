<?php
namespace AFEB\Modules\DynamicTags\Tags\Woo;

class ProductSale extends WooProductTag
{
    public function get_name()
    {
        return 'afeb_woo_product_sale';
    }

    public function get_title()
    {
        return esc_html__('Product Sale', 'addons-for-elementor-builder');
    }

    public function render()
    {
        $product = $this->get_product();
        if (!$product) return;

        if ($product->is_on_sale()) $this->echo_text(\esc_html__('Sale!', 'addons-for-elementor-builder'));
    }
}
