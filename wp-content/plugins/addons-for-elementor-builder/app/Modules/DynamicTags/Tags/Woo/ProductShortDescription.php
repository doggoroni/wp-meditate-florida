<?php
namespace AFEB\Modules\DynamicTags\Tags\Woo;

class ProductShortDescription extends WooProductTag
{
    public function get_name()
    {
        return 'afeb_woo_product_short_description';
    }

    public function get_title()
    {
        return esc_html__('Product Short Description', 'addons-for-elementor-builder');
    }

    public function render()
    {
        $product = $this->get_product();
        if (!$product) return;

        $desc = $product->get_short_description();
        if (!$desc) return;

        $this->echo_text($desc);
    }
}
