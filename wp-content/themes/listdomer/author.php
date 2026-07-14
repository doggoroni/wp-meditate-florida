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

$page_title = LSDR_Settings::get('listdomer_page_title_archive_display');
$description_position = LSDR_Settings::get('listdomer_page_desc_archive_position');
$breadcrumb = LSDR_Settings::get('listdomer_breadcrumb_display');
$page_description = $description_position !== 'disable';

$taxonomies = LSDR_Settings::get('listdomer_page_title_taxonomies');
$author = is_array($taxonomies) && isset($taxonomies['author']) && $taxonomies['author'];
$layout = LSDR_Settings::get('listdomer_archive_layout_type', 'list');
$sidebar = LSDR_Settings::get('listdomer_archive_post_layout');
$columns = LSDR_Settings::get('listdomer_archive_grid_columns', 2);

$column_class = 'col-md-6';
if ($columns == 1) $column_class = 'col-md-12';
else if ($columns == 3) $column_class = 'col-md-4';
else if ($columns == 4) $column_class = 'col-md-3';
?>
<?php if ($author && ($page_title || $breadcrumb || ($page_description && $description_position === 'before'))): ?>
    <div class="listdomer-header">
        <?php if ($page_title): ?>
            <?php the_archive_title('<h1 class="page-title">', '</h1>'); ?>
        <?php endif; ?>
        <?php if ($breadcrumb): ?>
            <?php do_action('lsdr_breadcrumb'); ?>
        <?php endif; ?>
        <?php if ($page_description && $description_position === 'before'): ?>
            <?php get_template_part('partials/archive/description'); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>
    <div id="content" class="site-content container">
        <div class="row">
            <?php if ($sidebar === 'left') get_sidebar(); ?>

            <div class="<?php echo $sidebar === 'full' ? 'col-lg-12' : 'col-lg-8'; ?> <?php echo $layout === 'grid' ? 'lsd-archive-grid' : ''; ?>">
                <main role="main">
                    <?php
                    if (have_posts()):

                        if (is_home() && !is_front_page()): ?>
                            <header role="banner">
                                <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
                            </header>
                        <?php endif; ?>

                        <?php if ($layout === 'grid'): ?>
                        <div class="row">
                            <?php while (have_posts()): the_post(); ?>
                                <div class="lsd-col-equal-height <?php echo esc_attr($column_class); ?>">
                                    <?php get_template_part('partials/content', get_post_type()); ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <?php
                        while (have_posts())
                        {
                            the_post();
                            get_template_part('partials/content', get_post_type());
                        }
                        ?>
                    <?php endif; ?>

                        <?php
                        // Print Pagination
                        LSDR_Theme::pagination();

                    else:

                        get_template_part('partials/content', 'none');

                    endif;
                    ?>

                </main>
            </div>

            <?php if ($sidebar === 'right') get_sidebar(); ?>
        </div>

        <?php if ($page_description && $description_position === 'after'): ?>
            <div class="lsd-page-archive-description">
                <?php get_template_part('partials/archive/description'); ?>
            </div>
        <?php endif; ?>
    </div>
<?php
get_footer();
