<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Listdomer
 */
get_header();
?>
    <div class="listdomer-header">
        <?php
        the_archive_title('<h1 class="page-title">', '</h1>');
        the_archive_description('<div class="archive-description"><p>', '</p></div>'); ?>
    </div>
    <div id="content" class="site-content container">
        <div class="row">
            <?php if (LSDR_Settings::get('listdomer_archive_post_layout') === 'left') get_sidebar(); ?>

            <div class="<?php echo LSDR_Settings::get('listdomer_archive_post_layout') === 'full' ? 'col-lg-12' : 'col-lg-8'; ?>">
                <main role="main">
                    <?php
                    if (have_posts()):

                        if (is_home() && !is_front_page()):
                            ?>
                            <header role="banner">
                                <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
                            </header>
                        <?php
                        endif;

                        /* Start the Loop */
                        while (have_posts())
                        {
                            the_post();

                            /*
                             * Include the Post-Type-specific template for the content.
                             * If you want to override this in a child theme, then include a file
                             * called content-___.php (where ___ is the Post Type name) and that will be used instead.
                             */
                            get_template_part('partials/content', get_post_type());
                        }

                        // Print Pagination
                        LSDR_Theme::pagination();

                    else:

                        get_template_part('partials/content', 'none');

                    endif;
                    ?>

                </main>
            </div>

            <?php if (LSDR_Settings::get('listdomer_archive_post_layout') === 'right') get_sidebar(); ?>
        </div>
    </div>
<?php
get_footer();
