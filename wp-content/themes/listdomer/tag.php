<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Listdomer
 */
get_header();

$page_title = LSDR_Settings::get('listdomer_page_title_archive_display');
$page_description = LSDR_Settings::get('listdomer_page_desc_archive_display');
$description_position = LSDR_Settings::get('listdomer_page_desc_archive_position');

$taxonomies = LSDR_Settings::get('listdomer_page_title_taxonomies');
$tags = is_array($taxonomies) && isset($taxonomies['tags']) && $taxonomies['tags'];
?>
<?php if ($page_title && $tags): ?>
    <div class="listdomer-header">
        <?php the_archive_title('<h1 class="page-title">', '</h1>'); ?>

        <?php if ($page_description && $description_position === 'before'): ?>
            <?php get_template_part('partials/archive/description'); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

    <div id="content" class="site-content container">
        <div class="row">
            <div class="col-lg-12">
                <main role="main">
                    <?php
                    if (have_posts())
                    {
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
                    }
                    else
                    {
                        get_template_part('partials/content', 'none');
                    }
                    ?>
                </main>
            </div>
        </div>

        <?php if ($page_description && $description_position === 'after'): ?>
            <div class="lsd-page-archive-description">
                <?php get_template_part('partials/archive/description'); ?>
            </div>
        <?php endif; ?>
    </div>

<?php
get_footer();
