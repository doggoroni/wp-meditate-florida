<?php
/**
 * Simple file logger for the Meditate Florida importer.
 * Writes timestamped lines to wp-content/logs/places-import.log.
 */

defined('ABSPATH') || exit;

class MFL_Logger
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
        $this->ensure_directory();
    }

    // ─── Public API ──────────────────────────────────────────────────────────

    public function info(string $message): void
    {
        $this->write('INFO ', $message);
    }

    public function warning(string $message): void
    {
        $this->write('WARN ', $message);
    }

    public function error(string $message): void
    {
        $this->write('ERROR', $message);
    }

    public function separator(): void
    {
        $this->raw(str_repeat('─', 72));
    }

    // ─── Internals ───────────────────────────────────────────────────────────

    private function write(string $level, string $message): void
    {
        $line = sprintf('[%s] [%s] %s', date('Y-m-d H:i:s'), $level, $message);
        $this->raw($line);
    }

    private function raw(string $line): void
    {
        error_log($line . PHP_EOL, 3, $this->file);
    }

    private function ensure_directory(): void
    {
        $dir = dirname($this->file);

        if (!is_dir($dir)) {
            wp_mkdir_p($dir);
        }
    }
}
