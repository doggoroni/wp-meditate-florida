<?php

namespace AFEB\Modules\DynamicTags\Tags\Woo;

class ProductContent extends WooProductTag
{
    public function get_name()
    {
        return 'afebp-woo-product-content-tag';
    }

    public function get_title()
    {
        return esc_html__('Product Content', 'elementor-pro');
    }

    public function render()
    {
        $product = $this->get_product();
        if (!$product) return;

        $this->echo_text($product->get_description());
    }
}
