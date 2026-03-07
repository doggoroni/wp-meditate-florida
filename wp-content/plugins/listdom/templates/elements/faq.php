<?php
// no direct access
defined('ABSPATH') || die();

/** @var int $post_id */
/** @var int $limit */

$faqs = get_post_meta($post_id, 'lsd_faqs', true);
if (!is_array($faqs)) $faqs = [];

$faqs = array_values(array_filter($faqs, static function ($faq)
{
    $question = isset($faq['question']) ? trim((string) $faq['question']) : '';
    $answer = isset($faq['answer']) ? trim((string) $faq['answer']) : '';

    return $question !== '' && $answer !== '';
}));

if ($limit > 0) $faqs = array_slice($faqs, 0, $limit);

// There are no FAQs
if (!count($faqs)) return '';
?>
<div class="lsd-faqs">
    <div class="lsd-faqs-accordion">
        <?php foreach ($faqs as $faq): ?>
            <details class="lsd-faq-item">
                <summary class="lsd-faq-question"><?php echo esc_html($faq['question']); ?></summary>
                <div class="lsd-faq-answer"><?php echo esc_html($faq['answer']); ?></div>
            </details>
        <?php endforeach; ?>
    </div>
</div>
