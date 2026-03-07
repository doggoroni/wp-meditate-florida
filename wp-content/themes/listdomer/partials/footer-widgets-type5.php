<?php
/**
 * Template part for displaying sub-footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Listdomer
 */

$footer1 = is_active_sidebar('footer-1');
?>
<?php if ($footer1): ?>
    <div class="site-main-footer container">
        <div class="row">
            <div class="site-footer-1 col-md-12">
                <?php dynamic_sidebar('footer-1'); ?>
            </div>
        </div>
    </div>
<?php endif;
