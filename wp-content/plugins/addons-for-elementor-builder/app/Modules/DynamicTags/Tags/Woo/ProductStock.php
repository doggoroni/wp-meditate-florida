<?php
namespace AFEB\Modules\DynamicTags\Tags\Woo;

class ProductStock extends WooProductTag
{
    public function get_name()
    {
        return 'afeb_woo_product_stock';
    }

    public function get_title()
    {
        return esc_html__('Product Stock', 'addons-for-elementor-builder');
    }

    public function render()
    {
        $product = $this->get_product();
        if (!$product) return;

        $stock = $product->get_stock_quantity();
        if ('' === $stock || null === $stock) return;

        $this->echo_text((string) $stock);
    }
}
