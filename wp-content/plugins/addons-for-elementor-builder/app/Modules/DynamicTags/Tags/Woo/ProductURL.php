<?php
namespace AFEB\Modules\DynamicTags\Tags\Woo;

use Elementor\Core\DynamicTags\Data_Tag;
use Elementor\Modules\DynamicTags\Module as DT;

class ProductURL extends Data_Tag
{
    public function get_name()
    {
        return 'afeb_woo_product_url';
    }

    public function get_title()
    {
        return esc_html__('Product URL', 'addons-for-elementor-builder');
    }

    public function get_group()
    {
        return Module::AFEB_GROUP;
    }

    public function get_categories()
    {
        return [DT::URL_CATEGORY];
    }

    protected function get_product()
    {
        if (!function_exists('wc_get_product')) return null;

        global $product;

        if ($product && is_a($product, '\\WC_Product')) return $product;

        $id = get_queried_object_id();

        if ($id)
        {
            $p = wc_get_product($id);
            if ($p) return $p;
        }

        $id = get_the_ID();
        if ($id) return wc_get_product($id);

        return null;
    }

    public function get_value(array $options = [])
    {
        $product = $this->get_product();
        if (!$product) return '';

        return $product->get_permalink();
    }
}
