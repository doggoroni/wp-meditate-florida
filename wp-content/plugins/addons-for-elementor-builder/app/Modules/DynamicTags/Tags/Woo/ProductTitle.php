<?php
namespace AFEB\Modules\DynamicTags\Tags\Woo;

class ProductTitle extends WooProductTag
{
    public function get_name()
    {
        return 'afeb_woo_product_title';
    }

    public function get_title()
    {
        return esc_html__('Product Title', 'addons-for-elementor-builder');
    }

    public function render()
    {
        $product = $this->get_product();
        if (!$product) return;

        $this->echo_text($product->get_name());
    }
}
