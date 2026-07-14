<?php

class LSDR_Base
{
    /**
     * Trims a description to a given character limit and indicates if truncation is needed.
     *
     * @param string $description The full HTML description.
     * @param int $max_chars The character limit for truncation.
     *
     * @return array {
     *  @type string $short The truncated or original description.
     *  @type string $full The original full description.
     *  @type bool $is_show_more Whether "Show More" is needed.
     * }
     */
    public static function lsd_get_trimmed_description(string $description, int $max_chars = 300): array
    {
        $plain_text = wp_strip_all_tags($description);
        $needs_truncation = mb_strlen($plain_text) > $max_chars;

        return [
            'short' => $needs_truncation
                ? mb_substr($plain_text, 0, $max_chars) . '...'
                : $description,
            'full' => $description,
            'is_show_more' => $needs_truncation,
        ];
    }
}
