<?php
// No Direct Access
defined('ABSPATH') || die();

/** @var LSDRC_Elementor_Carousel $this */
/** @var array $slides */
/** @var string $navigation */
/** @var int $autoplay */
/** @var int $loop */
/** @var int $count */
/** @var int $gap */

// No Slides Found
if (!count($slides)) return;
?>
<div id="listdomer_carousel_<?php echo esc_attr($this->get_id()); ?>"
     class="listdomer-carousel listdomer-font-m"
     data-autoplay="<?php echo esc_attr($autoplay); ?>"
     data-count="<?php echo esc_attr($count); ?>"
     data-navigation="<?php echo esc_attr($navigation); ?>"
     data-loop="<?php echo esc_attr($loop); ?>">
    <?php foreach ($slides as $slide): ?>
        <div class="listdomer-carousel-item">
            <div class="listdomer-carousel-number">
                <div><?php echo isset($slide['number']) && trim($slide['number']) != '' ? esc_html($slide['number']) : ''; ?></div>
            </div>
            <div>
                <h6><?php echo isset($slide['title']) && trim($slide['title']) ? esc_html($slide['title']) : ''; ?></h6>
                <p><?php echo isset($slide['content']) && trim($slide['content']) ? esc_html($slide['content']) : ''; ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<script>
jQuery(document).ready(function ($)
{
    $('#listdomer_carousel_<?php echo esc_attr($this->get_id()); ?>').owlCarousel(
    {
        items: <?php echo $count; ?>,
        margin: <?php echo $gap; ?>,
        loop: <?php echo $loop ? 'true' : 'false'; ?>,
        autoplay: <?php echo $autoplay ? 'true' : 'false'; ?>,
        autoplayHoverPause: true,
        nav: <?php echo $navigation === 'nav' ? 'true' : 'false'; ?>,
        dots: <?php echo $navigation === 'dots' ? 'true' : 'false'; ?>,
        responsive: {
            0: {
                items: 1,
                margin: <?php echo $gap; ?>,
            },
            600: {
                items: 2,
                margin: <?php echo $gap; ?>,
            },
            1000: {
                items: <?php echo $count; ?>,
                margin: <?php echo $gap; ?>,
            }
        }
    });
});
</script>
