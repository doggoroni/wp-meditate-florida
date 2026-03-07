<?php
namespace AFEB\Modules\DynamicTags\Tags\Woo;

class ProductStockStatus extends WooProductTag
{
    public function get_name()
    {
        return 'afeb_woo_product_stock_status';
    }

    public function get_title()
    {
        return esc_html__('Product Stock Status', 'addons-for-elementor-builder');
    }

    public function render()
    {
        $product = $this->get_product();
        if (!$product) return;

        $status = $product->get_stock_status();
        if (!$status) return;

        if (function_exists('wc_get_stock_status_name')) $status = wc_get_stock_status_name($status);

        $this->echo_text($status);
    }
}
