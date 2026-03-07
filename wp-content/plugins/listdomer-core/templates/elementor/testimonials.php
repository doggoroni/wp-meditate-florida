<?php
// No Direct Access
defined('ABSPATH') || die();

/** @var LSDRC_Elementor_Testimonials $this */
/** @var array $slides */
/** @var string $navigation */
/** @var bool $autoplay */
/** @var bool $loop */
/** @var int $count */
/** @var string $style */
/** @var int $gap */

// No Slides Found
if (!count($slides)) return;
?>
<div id="listdomer_carousel_<?php echo esc_attr($this->get_id()); ?>"
     class="listdomer-carousel listdomer-testimonials-widget listdomer-font-m listdomer-tsw-style-<?php echo esc_attr($style); ?>"
     data-autoplay="<?php echo esc_attr($autoplay); ?>" data-count="<?php echo esc_attr($count); ?>"
     data-navigation="<?php echo esc_attr($navigation); ?>" data-loop="<?php echo esc_attr($loop); ?>">
    <?php foreach ($slides as $slide): ?>
        <?php
        $image = isset($slide['image']) && is_array($slide['image']) ? $slide['image'] : [];
        $attachment_id = $image['id'] ?? null;

        $img = ($attachment_id ? wp_get_attachment_image_src($attachment_id) : '');
        $alt = $this->get_alt($attachment_id);
        ?>
        <div class="listdomer-carousel-item">
            <blockquote
                class="listdomer-tsw-content"><?php echo isset($slide['content']) && trim($slide['content']) != '' ? esc_html($slide['content']) : ''; ?></blockquote>
            <div class="listdomer-tsw-footer">
                <?php if (is_array($img) && isset($img[0]) && trim($img[0])): ?>
                    <div class="listdomer-tsw-image">
                        <img src="<?php echo esc_url($img[0]); ?>" alt="<?php echo esc_attr($alt); ?>">
                    </div>
                <?php endif; ?>

                <?php if (isset($slide['name']) && trim($slide['name'])): ?>
                    <div class="listdomer-tsw-name"><?php echo esc_html($slide['name']); ?></div>
                <?php endif; ?>

                <?php if (isset($slide['title']) && trim($slide['title'])): ?>
                    <div class="listdomer-tsw-title"><?php echo esc_html($slide['title']); ?></div>
                <?php endif; ?>
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
                items: 1,
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
