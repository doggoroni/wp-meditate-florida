<?php
/**
 * Standalone test for MFL_Category_Rules (no WordPress needed).
 *   php tests/category-rules-test.php
 */

PHP_SAPI === "cli" || exit;
define('ABSPATH', __DIR__);
require __DIR__ . '/../includes/class-category-rules.php';

$cases = [
    // Buddhist
    'Palm Beach Dharma Center LLC'                     => 'Buddhist Center',
    'Great Cloud Sangha'                               => 'Buddhist Center',
    'Thubten Kunga Ling'                               => 'Buddhist Center',
    'Wat Buddhamata Sirimahamaya วัดพุทธมารดาสิริมหามายา' => 'Buddhist Center',
    'Tu Viện Quan Âm'                                  => 'Buddhist Center',
    'Chùa Hương Vân'                                   => 'Buddhist Center',
    'Gainesville Karma Thegsum Choling'                => 'Buddhist Center',
    'Shambhala Gainesville'                            => 'Buddhist Center',
    'Soka Gakkai International-usa Miami Buddist Center' => 'Buddhist Center',
    'SGI-USA Orlando Centre'                           => 'Buddhist Center',
    'Sarasota Forest Monastery (SFM)'                  => 'Buddhist Center',
    'Phat Am Pagoda'                                   => 'Buddhist Center',
    'Saccavadi Dhamma Center'                          => 'Buddhist Center',
    'Tallahassee Chan Center'                          => 'Buddhist Center',
    // Spiritual (non-Buddhist traditions)
    'Hindu Mandir Of Daytona Beach'                    => 'Spiritual Center',
    'ISKCON of Tallahassee - Krishna House'            => 'Spiritual Center',
    'Kabbalah Centre'                                  => 'Spiritual Center',
    "Tallahassee Baha'i Center"                        => 'Spiritual Center',
    'Chabad of the Florida Keys'                       => 'Spiritual Center',
    'Center For Spiritual Living'                      => 'Spiritual Center',
    'Unity of Naples'                                  => 'Spiritual Center',
    'Vedanta Center'                                   => 'Spiritual Center',
    'Mother Earth Spiritual Center'                    => 'Spiritual Center',
    // Spa & Wellness
    'Rose Spa Massage'                                 => 'Spa & Wellness',
    'Be Well Holistic Massage Wellness Center, P.A.'   => 'Spa & Wellness',
    'Still Waters Day & Medical Spa'                   => 'Spa & Wellness',
    'The Spa at Four Seasons Resort Orlando at Walt Disney World® Resort' => 'Spa & Wellness',
    'Esthetics813 - The Spa at Saddlebrook Resort'     => 'Spa & Wellness',
    'Revive Salt Lounge'                               => 'Spa & Wellness',
    'Be Still Float Studio'                            => 'Spa & Wellness',
    'Solitude Sauna Studio'                            => 'Spa & Wellness',
    'Restore Hyper Wellness'                           => 'Spa & Wellness',
    'The Kov Cryotherapy'                              => 'Spa & Wellness',
    // Yoga / meditation / mindfulness
    'Casa Vinyasa Miami'                               => 'Yoga Studio',
    'Cocoyogi'                                         => 'Yoga Studio',
    'Maddie Meditates Wellness Center'                 => 'Meditation Center',
    'Mindful Movement Florida'                         => 'Mindfulness Center',
    // Off-topic (to be drafted)
    'HOTWORX - Clearwater, FL - Clearwater Mall'       => MFL_Category_Rules::OFF_TOPIC,
    'Fontainebleau Miami Beach'                        => MFL_Category_Rules::OFF_TOPIC,
    'Sundial Beach Resort & Spa'                       => MFL_Category_Rules::OFF_TOPIC,
    "'Tween Waters Inn & Marina"                       => MFL_Category_Rules::OFF_TOPIC,
    'Delray Center for Brain Science & TMS Therapy'    => MFL_Category_Rules::OFF_TOPIC,
    'Ketamine Treatment: Novel Mind & Wellness Center' => MFL_Category_Rules::OFF_TOPIC,
    'Hartley Chiropractic and Scoliosis Center in St. Augustine FL' => MFL_Category_Rules::OFF_TOPIC,
    'Mindful Behavioral Health PLLC'                   => MFL_Category_Rules::OFF_TOPIC,
    'Jax Wellness Counseling'                          => MFL_Category_Rules::OFF_TOPIC,
    'Jacksonville Arboretum & Botanical Gardens'       => MFL_Category_Rules::OFF_TOPIC,
    'Town Center at Boca Raton'                        => MFL_Category_Rules::OFF_TOPIC,
    'UF Health East'                                   => MFL_Category_Rules::OFF_TOPIC,
    'North Jax Fitness'                                => MFL_Category_Rules::OFF_TOPIC,
    'Mind Body Soul Medical'                           => MFL_Category_Rules::OFF_TOPIC,
    'Mindful Power LLC- Delray Beach, FL - Postpartum, Anxiety, Depression, Trauma, OCD' => MFL_Category_Rules::OFF_TOPIC,
    // No rule → keep current category
    'ZenAF Wellness'                                   => null,
    'Serenity Sounds Vibrational Sound Therapy'        => null,
    'Avalon'                                           => null,
    'Space of Mind'                                    => null,
    'Natural Bodhi Wellness'                           => null,
];

$fail = 0;
foreach ($cases as $name => $want) {
    $got = MFL_Category_Rules::match($name)['category'] ?? null;
    if ($got !== $want) {
        $fail++;
        printf("FAIL  %-60s want %-20s got %s\n", $name, var_export($want, true), var_export($got, true));
    }
}
printf("%d/%d passed\n", count($cases) - $fail, count($cases));
exit($fail ? 1 : 0);
