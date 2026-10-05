<?php
/**
 * MFL_Category_Rules — name-based listing categorisation.
 *
 * The business name is a stronger signal than the search term that found a
 * place ("Palm Beach Dharma Center" surfaced via a "wellness" search). Rules
 * are ordered; the first match wins. OFF_TOPIC means the place doesn't belong
 * in the directory at all (importer skips it, recategorize drafts it).
 *
 * Pure PHP, no WordPress calls — tested by tests/category-rules-test.php.
 */

defined('ABSPATH') || exit;

class MFL_Category_Rules
{
    const OFF_TOPIC = 'Off-topic';

    /**
     * [category, rule label, pattern, optional "unless" pattern].
     * Patterns run against the entity-decoded name, case-insensitive, UTF-8.
     */
    const RULES = [
        // ── Off-topic ── hotels/venues, clinical care, gyms
        [self::OFF_TOPIC, 'hospitality',
            '/\b(resort|hotel|inn|cottages)\b|fontainebleau|eden roc|little palm island|pritikin|margaritaville/',
            '/\bspa (at|by)\b/'],
        [self::OFF_TOPIC, 'venue',
            '/botanical|arboretum|key west gardens|town center|ocean center|community center|council on aging|resource center|imports\b|\bpages\b/'],
        [self::OFF_TOPIC, 'clinical',
            '/\btms\b|ketamine|anxiety|depression|\bocd\b|chiropract|behavioral health|mental health|counsel|psychotherap|\bclinic\b|adventhealth|uf health|hospital|\bmedical\b(?!\s+spa)/'],
        [self::OFF_TOPIC, 'fitness',
            '/hotworx|fitness|\bgym\b|\bfyt\b/'],

        // ── Traditions
        ['Buddhist Center', 'buddhist',
            '/dharma|dhamma|sangha|\bzen\b|buddh|budd?ist|\bwat\b|vihara|kadampa|tibet|thegsum|shambhala|soka gakkai|\bsgi\b|pagoda|monaster|\bling\b|chùa|\bchua\b|tu vi[eệ]n|ni vien|phap vien|\bchan center/u'],
        ['Spiritual Center', 'spiritual',
            '/spiritual|mandir|hindu|iskcon|krishna|kabbalah|chabad|baha.?i\b|islamic|mosque|\bchurch\b|ministr|\bunity of\b|vedanta|franciscan|espiritualidad|our lady|gnosis/u'],

        // ── Spa-type wellness (noindexed category)
        ['Spa & Wellness', 'spa',
            '/\bspa\b|massage|med ?spa|esthetic|skincare|cryo|hyper wellness|biostation|liquivida|salt (room|lounge|cave|spa)|\bfloat\b|sauna|cold plunge|soakhouse|hammam/'],

        // ── Practice
        ['Yoga Studio',        'yoga',        '/yog[ai]|vinyasa/'],
        ['Meditation Center',  'meditation',  '/meditat/'],
        ['Mindfulness Center', 'mindfulness', '/mindful/'],
    ];

    /**
     * @return array{category:string,rule:string}|null  null = no opinion.
     */
    public static function match(string $name): ?array
    {
        $name = html_entity_decode($name, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        foreach (self::RULES as $rule) {
            [$category, $label, $pattern] = $rule;
            $unless = $rule[3] ?? null;

            if (preg_match($pattern . 'i', $name) && !($unless && preg_match($unless . 'i', $name))) {
                return ['category' => $category, 'rule' => $label];
            }
        }
        return null;
    }

    public static function is_off_topic(string $name): bool
    {
        return (self::match($name)['category'] ?? null) === self::OFF_TOPIC;
    }
}
