<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Listdomer
 */
get_header();
$page_title = LSDR_Settings::get('listdomer_page_title_pages_display');
$description_position = LSDR_Settings::get('listdomer_page_desc_pages_position');
$breadcrumb = LSDR_Settings::get('listdomer_pages_breadcrumb_display');
$page_description = $description_position !== 'disable';
?>

<?php if ($page_title || $breadcrumb || ($page_description && $description_position === 'before')): ?>
    <div class="listdomer-header">
        <?php if($page_title): ?>
            <h1><?php the_title(); ?></h1>
        <?php endif; ?>
        <?php if($breadcrumb): ?>
            <?php do_action('lsdr_breadcrumb'); ?>
        <?php endif; ?>
        <?php if ($page_description && $description_position === 'before'): ?>
            <?php get_template_part('partials/archive/description'); ?>
        <?php endif; ?>
    </div>
<?php endif; ?>

    <div id="content" class="site-content container">
        <div class="row">
            <div class="col-lg-12">
                <main role="main" class="site-main">
                    <?php
						while (have_posts())
						{
							the_post();

							get_template_part('partials/content', 'page');

							// If comments are open, or we have at least one comment, load up the comment template.
							if (comments_open() || get_comments_number())
							{
								comments_template();
							}
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
