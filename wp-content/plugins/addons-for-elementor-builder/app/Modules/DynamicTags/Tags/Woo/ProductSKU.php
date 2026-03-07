<?php
namespace AFEB\Modules\DynamicTags\Tags\Woo;

class ProductSKU extends WooProductTag
{
    public function get_name()
    {
        return 'afeb_woo_product_sku';
    }

    public function get_title()
    {
        return esc_html__('Product SKU', 'addons-for-elementor-builder');
    }

    public function render()
    {
        $product = $this->get_product();
        if (!$product) return;

        $sku = $product->get_sku();
        if (!$sku) return;

        $this->echo_text($sku);
    }
}
