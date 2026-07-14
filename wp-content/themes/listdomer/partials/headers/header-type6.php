<?php
/**
 * Name: Type 6
 */

$listdomer_logo_bg = LSDR_Settings::get('listdomer_logo_bg', 1);
$listdomer_search_shortcode = LSDR_Settings::get('listdomer_search_shortcode');
?>
<header role="banner" id="masthead" class="site-header site-header-type6">
    <div class="lsdr-site-header-type6">
        <div class="site-branding <?php echo $listdomer_logo_bg ? '' : 'site-branding-no-bg'; ?>">
            <?php
            if (has_custom_logo() || !empty(LSDR_Settings::get('site_logo')['url'])) echo LSDR_Theme::logo();
            else
            {
                $blog_name = get_bloginfo('name');
                if (trim($blog_name))
                {
                    if ((is_front_page() || is_home()) && !is_page()) echo '<h1><a href="' . esc_url(home_url()) . '">' . esc_html($blog_name) . '</a></h1>';
                    else echo '<a href="' . esc_url(home_url()) . '">' . esc_html($blog_name) . '</a>';
                }
            }

            $description = get_bloginfo('description');
            if (LSDR_Settings::get('listdomer_header_description', true) && trim($description))
            {
                echo '<div class="site-description">' . esc_html($description) . '</div>';
            }
            ?>
        </div>

        <div class="lsd-header-search">
            <?php echo do_shortcode('[listdom-search id='. $listdomer_search_shortcode .']'); ?>
        </div>
        <nav class="main-navigation navbar-collapse offcanvas-collapse" role="navigation"
             aria-label="<?php esc_attr_e('Primary', 'listdomer'); ?>" id="site-navigation">
            <button id="main-navigation-close" class="main-navigation-close" aria-label="<?php echo esc_attr__('Navigation Close', 'listdomer'); ?>">
                <?php esc_html_e('Close', 'listdomer'); ?> <i class="fas fa-times"></i>
            </button>
            <?php
            wp_nav_menu([
                'theme_location' => 'menu-1',
                'menu' => LSDR_Settings::get('listdomer_menu_select', 'primary-menu'),
                'menu_id' => 'primary-menu',
            ]);
            ?>
        </nav>

        <div id="header-buttons" class="header-buttons">
            <?php do_action('listdomer_header_buttons'); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound ?>

            <button id="listdomer-responsive-sidebar-menu" class="listdomer-responsive-sidebar-menu" aria-label="<?php echo esc_attr__('Responsive sidebar menu', 'listdomer'); ?>">
            <span>
                <i class="fas fa-align-right"></i>
            </span>
            </button>
        </div>
    </div>

    <div class="lsd-header-search-mobile">
        <?php echo do_shortcode('[listdom-search id='. $listdomer_search_shortcode .']'); ?>
    </div>
</header>
