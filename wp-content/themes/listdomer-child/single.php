<?php
/**
 * Listdomer Child — single post template.
 * Extends parent single.php with a directory CTA block before comments.
 */
get_header();

$words_limit = LSDR_Settings::get('listdomer_navigation_title_word_limit', 5);

$prev_post = get_previous_post();
$next_post = get_next_post();

$prev = $words_limit > 0 && $prev_post ? wp_trim_words(get_the_title($prev_post->ID), $words_limit, '...') : '%title';
$next = $words_limit > 0 && $next_post ? wp_trim_words(get_the_title($next_post->ID), $words_limit, '...') : '%title';
?>
<div id="content" class="site-content container">
    <div class="lsdr-breadcrumb-wrapper">
        <?php do_action('lsdr_breadcrumb'); ?>
    </div>
    <div class="row">
        <?php if (LSDR_Settings::get('listdomer_single_post_layout') === 'left') get_sidebar(); ?>

        <div class="<?php echo LSDR_Settings::get('listdomer_single_post_layout') === 'full' ? 'col-lg-12' : 'col-lg-8'; ?>">
            <main role="main">
                <?php while (have_posts()) : the_post(); ?>

                    <?php get_template_part('partials/content', get_post_type()); ?>

                    <?php if (LSDR_Settings::get('listdomer_display_navigation')) : ?>
                        <?php the_post_navigation([
                            'prev_text' => '<span class="nav-subtitle">' . esc_html__('Previous:', 'listdomer') . '</span> <span class="nav-title">' . $prev . '</span>',
                            'next_text' => '<span class="nav-subtitle">' . esc_html__('Next:', 'listdomer') . '</span> <span class="nav-title">' . $next . '</span>',
                        ]); ?>
                    <?php endif; ?>

                    <?php if (is_singular('post')) : ?>
                    <div class="mfl-post-cta">
                        <p class="mfl-post-cta__eyebrow">Explore the Directory</p>
                        <h3 class="mfl-post-cta__heading">Find meditation &amp; wellness near you</h3>
                        <p class="mfl-post-cta__body">Browse 800+ yoga studios, meditation centers, and retreat spaces across Florida &mdash; filter by city, category, and rating.</p>
                        <a href="<?php echo esc_url(home_url('/listings/')); ?>" class="mfl-post-cta__btn">
                            Browse Florida Locations &rarr;
                        </a>
                    </div>
                    <?php endif; ?>

                    <?php if (comments_open() || get_comments_number()) : ?>
                        <?php comments_template(); ?>
                    <?php endif; ?>

                <?php endwhile; ?>
            </main>
        </div>

        <?php if (LSDR_Settings::get('listdomer_single_post_layout') === 'right') get_sidebar(); ?>
    </div>
</div>
<?php get_footer(); ?>
