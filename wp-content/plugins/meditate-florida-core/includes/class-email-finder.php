<?php
/**
 * MFL_Email_Finder — discovers public contact emails for listings.
 *
 * Google's Places API never returns email addresses, so imported listings
 * have no `lsd_email` and the listing contact form has nowhere to route.
 * This crawls each listing's own website (a field Places *does* provide),
 * looks for a published contact address, and stores it as `lsd_email`.
 *
 * Scope note: addresses are used only to forward inquiries a visitor
 * deliberately sent to that business. They are never added to the
 * newsletter or used for outreach.
 */

defined('ABSPATH') || exit;

class MFL_Email_Finder
{
    /** Paths tried per site, in order, until an email is found. */
    const CANDIDATE_PATHS = ['', '/contact', '/contact-us', '/about'];

    /** Seconds between HTTP requests, so we stay a polite crawler. */
    const REQUEST_DELAY = 1;

    const HTTP_TIMEOUT = 12;

    /** Never store these — platform noise, not the business. */
    const BLOCKED_DOMAINS = [
        'example.com', 'example.org', 'sentry.io', 'sentry-next.wixpress.com',
        'wixpress.com', 'squarespace.com', 'godaddy.com', 'wordpress.com',
        'cloudflare.com', 'schema.org', 'w3.org', 'gstatic.com',
        'googleapis.com', 'jquery.com', 'shopify.com', 'squareup.com',
        'facebook.com', 'instagram.com', 'domain.com', 'yourdomain.com',
        'email.com', 'company.com', 'sitename.com',
    ];

    /** Local-parts that are never a real inbox we should forward to. */
    const BLOCKED_LOCAL_PARTS = [
        'noreply', 'no-reply', 'donotreply', 'do-not-reply', 'nobody',
        'postmaster', 'mailer-daemon', 'user', 'username', 'name',
        'youremail', 'your-email', 'email', 'sentry',
    ];

    /** Asset extensions that look like emails after a `@2x` retina suffix. */
    const ASSET_EXTENSIONS = ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp', 'css', 'js', 'woff', 'woff2', 'ico'];

    private MFL_Logger $log;

    public function __construct(MFL_Logger $logger)
    {
        $this->log = $logger;
    }

    // ─── Backfill entry point ────────────────────────────────────────────────

    /**
     * Find and store emails for published listings that have a website
     * but no email yet.
     *
     * @param  int  $limit   Max listings to process (0 = all).
     * @param  bool $dry_run Report findings without writing meta.
     * @return array{processed:int, found:int, not_found:int, errors:int}
     */
    public function backfill(int $limit = 0, bool $dry_run = false): array
    {
        $this->log->separator();
        $this->log->info('=== Email backfill started' . ($dry_run ? ' (DRY RUN)' : '') . ' ===');

        $stats = ['processed' => 0, 'found' => 0, 'not_found' => 0, 'errors' => 0];

        foreach ($this->target_listing_ids() as $post_id) {
            if ($limit > 0 && $stats['processed'] >= $limit) {
                break;
            }

            $website = trim((string) get_post_meta($post_id, 'lsd_website', true));
            if ($website === '') {
                continue;
            }

            $stats['processed']++;
            $title = get_the_title($post_id);

            try {
                $email = $this->find_email_for_site($website);
            } catch (\Throwable $e) {
                $this->log->warning("  Email lookup errored for #{$post_id} ({$title}): " . $e->getMessage());
                $stats['errors']++;
                continue;
            }

            if ($email === null) {
                $this->log->info("  no email found: {$title}");
                $stats['not_found']++;
                continue;
            }

            if (!$dry_run) {
                update_post_meta($post_id, 'lsd_email', sanitize_email($email));
                update_post_meta($post_id, '_mfl_email_source', 'website');
            }

            $this->log->info("  FOUND {$email} — {$title} (post #{$post_id})");
            $stats['found']++;
        }

        $this->log->info(sprintf(
            '=== Email backfill complete — Processed: %d | Found: %d | Not found: %d | Errors: %d ===',
            $stats['processed'], $stats['found'], $stats['not_found'], $stats['errors']
        ));

        return $stats;
    }

    /** Published listings with a website and no stored email. */
    private function target_listing_ids(): array
    {
        global $wpdb;

        return array_map('intval', (array) $wpdb->get_col(
            "SELECT p.ID
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} w
                     ON w.post_id = p.ID AND w.meta_key = 'lsd_website' AND w.meta_value != ''
             LEFT JOIN {$wpdb->postmeta} e
                    ON e.post_id = p.ID AND e.meta_key = 'lsd_email'
             WHERE p.post_type   = 'listdom-listing'
               AND p.post_status = 'publish'
               AND (e.meta_value IS NULL OR e.meta_value = '')
             ORDER BY p.ID ASC"
        ));
    }

    // ─── Discovery ───────────────────────────────────────────────────────────

    /**
     * Try the site's homepage and common contact paths.
     * Returns the best candidate email, or null.
     */
    public function find_email_for_site(string $website): ?string
    {
        $base = $this->normalize_base_url($website);
        if ($base === null) {
            return null;
        }

        $site_host = $this->registrable_host($base);

        foreach (self::CANDIDATE_PATHS as $path) {
            $html = $this->fetch($base . $path);
            if ($html === null) {
                continue;
            }

            $email = self::extract_best_email($html, $site_host);
            if ($email !== null) {
                return $email;
            }
        }

        return null;
    }

    /**
     * Pull the best email out of a page's HTML.
     * `mailto:` links win over loose text matches; addresses on the site's
     * own domain win over free webmail found in the page.
     *
     * Public + static so it can be unit-tested without HTTP.
     */
    public static function extract_best_email(string $html, string $site_host = ''): ?string
    {
        $mailto = [];
        if (preg_match_all('/mailto:([^"\'?>\s]+)/i', $html, $m)) {
            $mailto = $m[1];
        }

        $loose = [];
        if (preg_match_all('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $html, $m2)) {
            $loose = $m2[0];
        }

        // mailto: first — an address the site deliberately published.
        foreach ([$mailto, $loose] as $group) {
            $valid = [];
            foreach ($group as $raw) {
                $email = self::clean_candidate($raw);
                if ($email !== null && !in_array($email, $valid, true)) {
                    $valid[] = $email;
                }
            }

            if (!$valid) {
                continue;
            }

            // Prefer an address on the business's own domain.
            if ($site_host !== '') {
                foreach ($valid as $email) {
                    if (str_ends_with(strtolower(substr($email, strrpos($email, '@') + 1)), $site_host)) {
                        return $email;
                    }
                }
            }

            return $valid[0];
        }

        return null;
    }

    /** Normalize + reject a candidate address. Returns null if unusable. */
    private static function clean_candidate(string $raw): ?string
    {
        $email = strtolower(trim(rawurldecode($raw), " \t\n\r\0\x0B.,;:<>()[]\"'"));

        if ($email === '' || !is_email($email)) {
            return null;
        }

        [$local, $domain] = explode('@', $email, 2);

        // `logo@2x.png` and friends parse as emails — drop asset filenames.
        $ext = strtolower((string) pathinfo($domain, PATHINFO_EXTENSION));
        if (in_array($ext, self::ASSET_EXTENSIONS, true)) {
            return null;
        }

        foreach (self::BLOCKED_LOCAL_PARTS as $blocked) {
            if ($local === $blocked || str_starts_with($local, $blocked . '+')) {
                return null;
            }
        }

        foreach (self::BLOCKED_DOMAINS as $blocked) {
            if ($domain === $blocked || str_ends_with($domain, '.' . $blocked)) {
                return null;
            }
        }

        // Hashed/tracking pixels occasionally match the email pattern.
        if (strlen($local) > 40 || preg_match('/^[0-9a-f]{24,}$/', $local)) {
            return null;
        }

        return $email;
    }

    // ─── HTTP helpers ────────────────────────────────────────────────────────

    /** Scheme-normalized origin (no trailing slash), or null if unusable. */
    private function normalize_base_url(string $website): ?string
    {
        $website = trim($website);
        if ($website === '') {
            return null;
        }

        if (!preg_match('#^https?://#i', $website)) {
            $website = 'https://' . $website;
        }

        $parts = wp_parse_url($website);
        if (empty($parts['host'])) {
            return null;
        }

        return ($parts['scheme'] ?? 'https') . '://' . $parts['host'];
    }

    /** Host without a leading www., for own-domain preference. */
    private function registrable_host(string $url): string
    {
        $host = (string) (wp_parse_url($url, PHP_URL_HOST) ?: '');
        return strtolower(preg_replace('/^www\./i', '', $host));
    }

    /** GET a URL, returning HTML or null. Non-HTML and errors return null. */
    private function fetch(string $url): ?string
    {
        sleep(self::REQUEST_DELAY);

        $response = wp_remote_get($url, [
            'timeout'     => self::HTTP_TIMEOUT,
            'redirection' => 3,
            'user-agent'  => 'MeditateFL-DirectoryBot/1.0 (+https://meditateflorida.com)',
            'headers'     => ['Accept' => 'text/html,application/xhtml+xml'],
        ]);

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
            return null;
        }

        $type = (string) wp_remote_retrieve_header($response, 'content-type');
        if ($type !== '' && !str_contains(strtolower($type), 'html')) {
            return null;
        }

        $body = (string) wp_remote_retrieve_body($response);

        // Cap the parse size — contact details live near the top or in the footer.
        return strlen($body) > 600000 ? substr($body, 0, 600000) : $body;
    }
}
