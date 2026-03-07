<?php
namespace AFEB\Modules\DynamicTags\Tags\Woo;

use Elementor\Core\DynamicTags\Tag;
use Elementor\Modules\DynamicTags\Module as DT;

abstract class WooProductTag extends Tag
{
    public function get_group()
    {
        return Module::AFEB_GROUP;
    }

    public function get_categories()
    {
        return [DT::TEXT_CATEGORY];
    }

    /**
     * Try to resolve the current product.
     */
    protected function get_product()
    {
        if (!function_exists('wc_get_product')) return null;

        global $product;

        if ($product && is_a($product,'\WC_Product')) return $product;

        $id = get_queried_object_id();
        if ( $id )
        {
            $p = wc_get_product($id);
            if ($p) return $p;
        }

        $id = get_the_ID();
        if ($id) return wc_get_product($id);

        return null;
    }

    protected function echo_text($text)
    {
        echo wp_kses_post($text);
    }
}
