<?php
namespace AFEB\Modules\DynamicTags\Tags\Woo;

class ProductSalePrice extends WooProductTag
{
    public function get_name()
    {
        return 'afeb_woo_product_sale_price';
    }

    public function get_title()
    {
        return esc_html__('Product Sale Price', 'addons-for-elementor-builder');
    }

    public function render()
    {
        $product = $this->get_product();
        if (!$product) return;

        $price = $product->get_sale_price();
        if ('' === $price || null === $price) return;

        if (function_exists('wc_price')) $this->echo_text(wc_price((float) $price));
        else $this->echo_text((string) $price);
    }
}
