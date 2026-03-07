<?php
namespace AFEB\Modules\DynamicTags\Tags\Woo;

class ProductRegularPrice extends WooProductTag
{
    public function get_name()
    {
        return 'afeb_woo_product_regular_price';
    }

    public function get_title()
    {
        return esc_html__('Product Regular Price', 'addons-for-elementor-builder');
    }

    public function render()
    {
        $product = $this->get_product();
        if (!$product) return;

        $price = $product->get_regular_price();
        if ('' === $price || null === $price) return;

        if (function_exists('wc_price')) $this->echo_text(wc_price((float) $price));
        else $this->echo_text((string) $price);
    }
}
