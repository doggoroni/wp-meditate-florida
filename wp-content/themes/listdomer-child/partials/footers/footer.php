<?php
/**
 * Listdomer Child — Footer Type 1
 *
 * Overrides parent's partials/footers/footer.php.
 * Changes vs parent:
 *  - Adds newsletter signup band above widget areas
 *  - If the 'mfl-newsletter' sidebar has a widget, renders that instead
 *    of the built-in form (so you can drop in a MailChimp/Mailerlite widget)
 */
?>
<footer role="contentinfo" id="colophon" class="site-footer">

    <!-- ─── Newsletter band ──────────────────────────────────────────────── -->
    <div class="mfl-newsletter-band">
        <div class="container">
            <?php if (is_active_sidebar('mfl-newsletter')): ?>
                <?php dynamic_sidebar('mfl-newsletter'); ?>
            <?php else: ?>
                <h3><?php esc_html_e('Stay Mindful. Stay Connected.', 'listdomer-child'); ?></h3>
                <p><?php esc_html_e('Get the latest meditation centers, retreats, and wellness events across Florida — delivered to your inbox monthly.', 'listdomer-child'); ?></p>

                <?php
                $nl_status = sanitize_key($_GET['mfl_newsletter'] ?? '');
                if ($nl_status === 'sent') : ?>
                    <p class="mfl-newsletter-notice mfl-newsletter-notice--success">
                        <?php esc_html_e('Thanks for subscribing! We\'ll be in touch.', 'listdomer-child'); ?>
                    </p>
                <?php elseif ($nl_status === 'error') : ?>
                    <p class="mfl-newsletter-notice mfl-newsletter-notice--error">
                        <?php esc_html_e('Please enter a valid email address and try again.', 'listdomer-child'); ?>
                    </p>
                <?php endif; ?>

                <form class="mfl-newsletter-form"
                      action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                      method="post"
                      aria-label="<?php esc_attr_e('Newsletter signup', 'listdomer-child'); ?>">
                    <input type="hidden" name="action" value="mfl_newsletter_signup">
                    <?php wp_nonce_field('mfl_newsletter_signup', 'mfl_newsletter_nonce'); ?>
                    <input
                        type="email"
                        name="mfl_email"
                        placeholder="<?php esc_attr_e('Your email address', 'listdomer-child'); ?>"
                        required
                        aria-label="<?php esc_attr_e('Email address', 'listdomer-child'); ?>"
                        autocomplete="email"
                    >
                    <button type="submit">
                        <?php esc_html_e('Subscribe', 'listdomer-child'); ?>
                    </button>
                </form>

                <p class="mfl-newsletter-note">
                    <?php esc_html_e('No spam, ever. Unsubscribe at any time.', 'listdomer-child'); ?>
                </p>
            <?php endif; ?>
        </div>
    </div>

    <!-- ─── Footer main: brand + link columns ──────────────────────────── -->
    <?php
        $footer_listings_url = get_post_type_archive_link('listdom-listing') ?: home_url('/listings/');
        $footer_cities = [
            'miami' => 'Miami', 'orlando' => 'Orlando', 'tampa' => 'Tampa',
            'jacksonville' => 'Jacksonville', 'fort-lauderdale' => 'Fort Lauderdale',
            'st-petersburg' => 'St. Petersburg', 'sarasota' => 'Sarasota', 'naples' => 'Naples',
        ];
        $footer_cats = [
            'Meditation Center', 'Yoga Studio', 'Meditation Retreat',
            'Buddhist Center', 'Wellness Center',
        ];
    ?>
    <div class="mfl-footer-main">
        <div class="container">
            <div class="mfl-footer-grid">

                <div class="mfl-footer-col mfl-footer-col--brand">
                    <div class="mfl-footer-brand">Meditate Florida</div>
                    <p class="mfl-footer-tagline"><?php esc_html_e('Florida\'s Mindfulness Directory', 'listdomer-child'); ?></p>
                    <p class="mfl-footer-blurb">
                        <?php esc_html_e('A free, community-minded directory of meditation centers, yoga studios, and retreats across the Sunshine State — helping you find a place to slow down, wherever you are in Florida.', 'listdomer-child'); ?>
                    </p>
                </div>

                <div class="mfl-footer-col">
                    <h4 class="mfl-footer-heading"><?php esc_html_e('Explore', 'listdomer-child'); ?></h4>
                    <ul class="mfl-footer-links">
                        <li><a href="<?php echo esc_url($footer_listings_url); ?>"><?php esc_html_e('Browse All Locations', 'listdomer-child'); ?></a></li>
                        <?php foreach ($footer_cats as $cat): ?>
                        <li><a href="<?php echo esc_url(add_query_arg('category', $cat, $footer_listings_url)); ?>"><?php echo esc_html($cat); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="mfl-footer-col">
                    <h4 class="mfl-footer-heading"><?php esc_html_e('Popular Cities', 'listdomer-child'); ?></h4>
                    <ul class="mfl-footer-links">
                        <?php foreach ($footer_cities as $slug => $name): ?>
                        <li><a href="<?php echo esc_url(home_url('/meditation/' . $slug . '/')); ?>"><?php echo esc_html($name); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="mfl-footer-col">
                    <h4 class="mfl-footer-heading"><?php esc_html_e('Meditate Florida', 'listdomer-child'); ?></h4>
                    <ul class="mfl-footer-links">
                        <li><a href="<?php echo esc_url(home_url('/about/')); ?>"><?php esc_html_e('About Us', 'listdomer-child'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/blog/')); ?>"><?php esc_html_e('Blog', 'listdomer-child'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'listdomer-child'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php esc_html_e('Privacy Policy', 'listdomer-child'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/terms/')); ?>"><?php esc_html_e('Terms & Conditions', 'listdomer-child'); ?></a></li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <!-- ─── Sub-footer / copyright ──────────────────────────────────────── -->
    <?php
        $subfooter_menu = wp_nav_menu([
            'theme_location' => 'menu-2',
            'menu'           => LSDR_Settings::get('listdomer_footer_menu_select', 'subfooter-menu'),
            'menu_id'        => 'subfooter-menu',
            'echo'           => false,
        ]);
    ?>
    <div class="container">
        <div class="site-subfooter">

            <?php if (trim($subfooter_menu)): ?>
                <nav id="site-subfooter-navigation" class="subfooter-navigation" role="navigation"
                     aria-label="<?php esc_attr_e('Sub Footer', 'listdomer-child'); ?>">
                    <?php echo $subfooter_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </nav>
            <?php endif; ?>

            <p class="mfl-footer-credit">
                &copy; <?php echo esc_html(date('Y')); ?> Meditate Florida &mdash; Florida&#8217;s Mindfulness Directory
            </p>

        </div>
    </div>

</footer>
