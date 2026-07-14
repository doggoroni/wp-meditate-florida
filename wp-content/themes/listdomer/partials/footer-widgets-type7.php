<?php
/**
 * Template part for displaying sub-footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Listdomer
 */

$footer1 = is_active_sidebar('footer-1');
$footer2 = is_active_sidebar('footer-2');
$footer3 = is_active_sidebar('footer-3');
$footer4 = is_active_sidebar('footer-4');
?>
<?php if ($footer1 || $footer2 || $footer3 || $footer4): ?>
    <div class="site-main-footer container">
        <div class="row">
            <?php if ($footer1): ?>
                <div class="site-footer-1 col-md-6">
                    <?php dynamic_sidebar('footer-1'); ?>
                </div>
            <?php endif; ?>

            <?php if ($footer2): ?>
                <div class="site-footer-2 col-md-2">
                    <?php dynamic_sidebar('footer-2'); ?>
                </div>
            <?php endif; ?>

            <?php if ($footer3): ?>
                <div class="site-footer-3 col-md-2">
                    <?php dynamic_sidebar('footer-3'); ?>
                </div>
            <?php endif; ?>

            <?php if ($footer4): ?>
                <div class="site-footer-4 col-md-2">
                    <?php dynamic_sidebar('footer-4'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif;
