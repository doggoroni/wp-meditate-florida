<?php
/**
 * Listdomer Child — Header Type 1 (sticky + search bar)
 *
 * Overrides parent's partials/headers/header.php.
 * Changes vs parent:
 *  - Added mfl-header-search form between branding and nav
 *  - Added mobile search toggle button
 *  - Wrapper class mfl-sticky added (CSS targets #masthead directly)
 */

$listdomer_logo_bg = LSDR_Settings::get('listdomer_logo_bg', 1);
?>
<header role="banner" id="masthead" class="site-header d-flex flex-column flex-md-row site-header-type1">

    <!-- Logo / site name -->
    <div class="site-branding <?php echo $listdomer_logo_bg ? '' : 'site-branding-no-bg'; ?>">
        <?php
            if (has_custom_logo() || !empty(LSDR_Settings::get('site_logo')['url'])) {
                echo LSDR_Theme::logo();
            } else {
                $blog_name = get_bloginfo('name');
                if (trim($blog_name)) {
                    if ((is_front_page() || is_home()) && !is_page()) {
                        echo '<h1><a href="' . esc_url(home_url()) . '">' . esc_html($blog_name) . '</a></h1>';
                    } else {
                        echo '<a href="' . esc_url(home_url()) . '">' . esc_html($blog_name) . '</a>';
                    }
                }
            }

            $description = get_bloginfo('description');
            if (trim($description)) {
                echo '<div class="site-description">' . esc_html($description) . '</div>';
            }
        ?>
    </div>

    <!-- Inline search bar (hidden on mobile, toggled by button) -->
    <div class="mfl-header-search" id="mfl-header-search" role="search">
        <form method="get" action="<?php echo esc_url(home_url('/')); ?>"
              aria-label="<?php esc_attr_e('Search meditation listings', 'listdomer-child'); ?>">
            <input
                type="search"
                name="s"
                value="<?php echo esc_attr(get_search_query()); ?>"
                placeholder="<?php esc_attr_e('Search centers, retreats, studios…', 'listdomer-child'); ?>"
                aria-label="<?php esc_attr_e('Search listings', 'listdomer-child'); ?>"
            >
            <input type="hidden" name="post_type" value="listdom-listing">
            <button type="submit" aria-label="<?php esc_attr_e('Submit search', 'listdomer-child'); ?>">
                <!-- Search icon (inline SVG — no extra HTTP request) -->
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true" focusable="false">
                    <circle cx="11" cy="11" r="8"/>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </button>
        </form>
    </div>

    <!-- Primary navigation -->
    <nav class="main-navigation navbar-collapse offcanvas-collapse" role="navigation"
         aria-label="<?php esc_attr_e('Primary', 'listdomer'); ?>" id="site-navigation">
        <button id="main-navigation-close" class="main-navigation-close"
                aria-label="<?php esc_attr__('Navigation Close', 'listdomer'); ?>">
            <?php esc_html_e('Close', 'listdomer'); ?> <i class="fas fa-times" aria-hidden="true"></i>
        </button>
        <?php
            wp_nav_menu([
                'theme_location' => 'menu-1',
                'menu'           => LSDR_Settings::get('listdomer_menu_select', 'primary-menu'),
                'menu_id'        => 'primary-menu',
            ]);
        ?>
    </nav>

    <!-- Header action buttons (login, submit listing, etc.) -->
    <div id="header-buttons" class="header-buttons">
        <?php do_action('listdomer_header_buttons'); // phpcs:ignore WPThemeReview.CoreFunctionality.PrefixAllGlobals.NonPrefixedHooknameFound ?>

        <!-- Mobile: search toggle -->
        <button id="mfl-search-toggle" class="mfl-search-toggle"
                aria-label="<?php esc_attr_e('Toggle search', 'listdomer-child'); ?>"
                aria-expanded="false"
                aria-controls="mfl-header-search">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                 stroke-linejoin="round" aria-hidden="true" focusable="false">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        </button>

        <!-- Mobile: hamburger menu -->
        <button id="listdomer-responsive-sidebar-menu" class="listdomer-responsive-sidebar-menu"
                aria-label="<?php esc_attr__('Responsive sidebar menu', 'listdomer'); ?>">
            <span>
                <i class="fas fa-align-right" aria-hidden="true"></i>
            </span>
        </button>
    </div>

</header>
