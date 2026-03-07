<?php
/**
 * Theme util functions
 * @package Listdomer
 */
if (!function_exists('wp_body_open'))
{
    function wp_body_open() // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedFunctionFound
    {
        do_action('wp_body_open'); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound
    }
}
