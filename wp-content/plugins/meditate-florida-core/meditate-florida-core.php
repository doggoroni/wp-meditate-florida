<?php
/**
 * Plugin Name:  Meditate Florida Core
 * Description:  Automated Google Places importer for Florida meditation businesses.
 *               Runs weekly via WP-Cron, creates/updates Listdom listings,
 *               and logs all activity to wp-content/logs/places-import.log.
 * Version:      1.0.0
 * Requires PHP: 8.0
 */

defined('ABSPATH') || exit;

// ─── Constants ────────────────────────────────────────────────────────────────

define('MFL_VERSION',   '1.0.0');
define('MFL_DIR',       plugin_dir_path(__FILE__));
define('MFL_CRON_HOOK', 'mfl_weekly_places_import');
define('MFL_LOG_FILE',  WP_CONTENT_DIR . '/logs/places-import.log');
define('MFL_ENV_FILE',  ABSPATH . '.env');

// ─── Autoload ────────────────────────────────────────────────────────────────

require_once MFL_DIR . 'includes/class-logger.php';
require_once MFL_DIR . 'includes/class-places-importer.php';

// ─── Activation / Deactivation ───────────────────────────────────────────────

register_activation_hook(__FILE__, 'mfl_activate');
register_deactivation_hook(__FILE__, 'mfl_deactivate');

function mfl_activate(): void
{
    // Create the logs directory if it doesn't exist yet.
    $log_dir = dirname(MFL_LOG_FILE);
    if (!is_dir($log_dir)) {
        wp_mkdir_p($log_dir);
    }

    // Write a .htaccess so the log file is not publicly accessible.
    $htaccess = $log_dir . '/.htaccess';
    if (!file_exists($htaccess)) {
        file_put_contents($htaccess, "Deny from all\n");
    }

    // Schedule the first run at the next Sunday 03:00 in the site's timezone.
    if (!wp_next_scheduled(MFL_CRON_HOOK)) {
        wp_schedule_event(mfl_next_sunday_3am(), 'weekly', MFL_CRON_HOOK);
    }
}

function mfl_deactivate(): void
{
    $timestamp = wp_next_scheduled(MFL_CRON_HOOK);
    if ($timestamp) {
        wp_unschedule_event($timestamp, MFL_CRON_HOOK);
    }
}

// ─── Cron callback ───────────────────────────────────────────────────────────

add_action(MFL_CRON_HOOK, 'mfl_run_import');

function mfl_run_import(): void
{
    // Prevent PHP from timing out on large imports.
    set_time_limit(0);

    $logger   = new MFL_Logger(MFL_LOG_FILE);
    $importer = new MFL_Places_Importer($logger);
    $importer->run_all_cities();
}

// ─── Helpers ─────────────────────────────────────────────────────────────────

/**
 * Returns the Unix timestamp of the next Sunday at 03:00 in the site timezone.
 * If today is Sunday and it's before 3am, returns today at 3am.
 */
function mfl_next_sunday_3am(): int
{
    $tz  = new DateTimeZone(wp_timezone_string() ?: 'America/New_York');
    $now = new DateTime('now', $tz);

    $days_until_sunday = (7 - (int) $now->format('w')) % 7;

    // If it's already Sunday but 3am hasn't passed, run today.
    if ($days_until_sunday === 0 && (int) $now->format('H') < 3) {
        $days_until_sunday = 0;
    } elseif ($days_until_sunday === 0) {
        // It's Sunday but past 3am — schedule for next Sunday.
        $days_until_sunday = 7;
    }

    $target = clone $now;
    if ($days_until_sunday > 0) {
        $target->modify("+{$days_until_sunday} days");
    }
    $target->setTime(3, 0, 0);

    return $target->getTimestamp();
}
