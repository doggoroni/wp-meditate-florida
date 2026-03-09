<?php
/**
 * Listdomer Child — Blog index (Posts Page)
 *
 * WordPress loads this template when a static page is assigned as the Posts Page.
 */

get_header();
?>

<!-- ════════════════════════════════════════════════ BLOG HEADER ══ -->
<header class="mfl-arch-header">
    <div class="container">
        <h1 class="mfl-arch-header__title"><?php esc_html_e('Mindfulness Journal', 'listdomer-child'); ?></h1>
        <p class="mfl-arch-header__sub"><?php esc_html_e('Tips, guides, and stories from Florida\'s meditation community.', 'listdomer-child'); ?></p>
    </div>
</header>

<!-- ════════════════════════════════════════════════ BLOG POSTS ══ -->
<div class="container" style="padding-top: 40px; padding-bottom: 60px;">
    <div id="primary" class="content-area">
        <main id="main" class="site-main">

            <?php if (have_posts()) : ?>
                <?php while (have_posts()) : the_post(); ?>
                    <?php get_template_part('partials/content', get_post_format()); ?>
                <?php endwhile; ?>

                <?php the_posts_navigation(); ?>

            <?php else : ?>
                <p><?php esc_html_e('No posts found.', 'listdomer-child'); ?></p>
            <?php endif; ?>

        </main>
    </div>
</div>

<?php get_footer(); ?>
