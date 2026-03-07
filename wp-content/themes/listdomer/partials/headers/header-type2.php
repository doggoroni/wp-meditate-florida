<?php
/**
 * Name: Type 2
 */
?>
<header role="banner" id="masthead" class="site-header site-header-type2">
    <div class="site-branding">
        <?php
            if (LSDR_Theme::has_dark_logo()) echo LSDR_Theme::logo(true);
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
            if (trim($description)) echo '<div class="site-description">' . esc_html($description) . '</div>';
        ?>
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
</header>
