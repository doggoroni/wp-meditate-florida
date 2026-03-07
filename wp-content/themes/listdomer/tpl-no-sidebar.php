<?php
/**
 * Template Name: Boxed - No Sidebar
 *
 * @package Listdomer
 */
get_header();
?>
    <div id="content" class="site-content container">
        <div class="row">
            <div class="col-lg-12">
                <main role="main" class="site-main">
                    <?php
						while (have_posts())
						{
							the_post();

							get_template_part('partials/content', 'classic');

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
    </div>
<?php
get_footer();
