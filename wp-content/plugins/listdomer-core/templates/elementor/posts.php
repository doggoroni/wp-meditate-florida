<?php
// No Direct Access
defined('ABSPATH') || die();

/** @var LSDRC_Elementor_Posts $this */
/** @var string $PT */
/** @var bool $show_category */
/** @var bool $show_date */
/** @var bool $show_author */
/** @var int $posts_count */
/** @var int $post_category */
/** @var int $columns */
/** @var int $columns_tablet */
/** @var int $columns_mobile */
/** @var bool $enable_slider */
/** @var string $loop */
/** @var string $autoplay */
/** @var int $autoplay_duration */
/** @var int $gap */
/** @var string $enable_navigation */
/** @var string $equal_height_class */
/** @var bool $equal_height */

// Query
$query = new WP_Query([
    'post_type' => $PT,
    'posts_per_page' => $posts_count,
    'cat' => $post_category,
]);

// Nothing Found
if (!$query->have_posts())
{
    wp_reset_postdata();
    return;
}
?>
<ul class="listdomer-posts-widget listdomer-font-m <?php echo $enable_slider ? 'posts-owl-carousel lsd-flex' : 'lsd-grid lsd-g-' . $columns . '-columns'; ?> <?php echo sanitize_html_class($equal_height_class); ?>">
    <?php while ($query->have_posts()): $query->the_post(); $post = get_post(); $categories = get_the_category(); ?>
        <li>
            <div class="listdomer-pw-wrapper">
                <div class="listdomer-pw-featured-image">
                    <a href="<?php echo esc_url(get_the_permalink()); ?>">
                        <?php echo get_the_post_thumbnail($post, [300, 200]); ?>
                    </a>
                </div>
                <div class="listdomer-pw-content">
                    <div class="listdomer-pw-content-top">
                        <?php if ($show_date): ?>
                            <div class="listdomer-pw-date"> <?php echo get_the_date(); ?> </div>
                        <?php endif; ?>

                        <?php if ($show_category && $categories && count($categories)): ?>
                            <div class="listdomer-pw-category"> <?php echo $categories[0]->name; ?> </div>
                        <?php endif; ?>
                    </div>
                    <h5>
                        <a href="<?php echo esc_url(get_the_permalink()); ?>">
                            <?php echo get_the_title(); ?>
                        </a>
                    </h5>
                    <div class="listdomer-pw-content-bottom">
                        <?php if ($show_author): ?>
                            <div class="listdomer-pw-author">
                                <img alt="<?php echo esc_attr(get_the_author()); ?>" src="<?php echo esc_url(get_avatar_url($post->post_author)); ?>">
                                <?php echo get_the_author(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </li>
    <?php endwhile; ?>
</ul>
<?php wp_reset_postdata(); ?>

<?php if ($enable_slider): ?>
<script>
jQuery(document).ready(function ($)
{
    $('.posts-owl-carousel').owlCarousel(
    {
        items: <?php echo $columns; ?>,
        margin: <?php echo $gap; ?>,
        loop: <?php echo $loop ? 'true' : 'false'; ?>,
        autoplay: <?php echo $autoplay ? 'true' : 'false'; ?>,
        autoplayTimeout: <?php echo $autoplay_duration; ?>,
        autoplayHoverPause: true,
        nav: <?php echo $enable_navigation ? 'true' : 'false'; ?>,
        dots: false,
        responsive: {
            0: {
                items: <?php echo $columns_mobile; ?>,
                margin: <?php echo $gap; ?>,
                nav: <?php echo $enable_navigation ? 'true' : 'false'; ?>,
                dots: false,
            },
            // For tablets
            600: {
                items: <?php echo $columns_tablet; ?>,
                margin: <?php echo $gap; ?>,
                nav: <?php echo $enable_navigation ? 'true' : 'false'; ?>,
                dots: false,
            },
            // For desktops
            1000: {
                items: <?php echo $columns; ?>,
                margin: <?php echo $gap; ?>,
                nav: <?php echo $enable_navigation ? 'true' : 'false'; ?>,
                dots: false,
            }
        }
    });
});
</script>

    <?php if(!$enable_navigation): ?>
        <style>
            .posts-owl-carousel .owl-nav
            {
                display: none;
            }
        </style>
    <?php endif; ?>
<?php endif; ?>
<?php if ($enable_slider && $equal_height): ?>
<script>
function setOwlEqualHeight()
{
    const owlItems = document.querySelectorAll('.posts-owl-carousel.lsd-equal-height .owl-item');
    let maxHeight = 0;

    owlItems.forEach(item => {
        item.style.height = 'auto'; // reset first
        const itemHeight = item.offsetHeight;
        if (itemHeight > maxHeight) maxHeight = itemHeight;
    });

    owlItems.forEach(item => {
        item.style.height = maxHeight + 'px';
    });
}

setOwlEqualHeight();

jQuery('.posts-owl-carousel.lsd-equal-height').on('changed.owl.carousel resized.owl.carousel refreshed.owl.carousel', function()
{
    setOwlEqualHeight();
});
</script>
<?php endif;
