<?php
/**
 * Dubai Xtra - Rank Math SEO Setup
 *
 * One-shot helper that:
 *   1. Writes Rank Math SEO title, meta description, and focus keyword
 *      onto every existing best-list post.
 *   2. Sets default Rank Math title/description templates for all 7 CPTs
 *      so future posts inherit reasonable meta automatically.
 *
 * Triggered manually via the admin notice. Sets the `dx_seo_meta_seeded`
 * option after a successful run so the prompt does not reappear.
 *
 * To re-run: delete the `dx_seo_meta_seeded` option from wp_options.
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

/**
 * ─────────────────────────────────────────────────────────────────
 * CPT TEMPLATE DEFAULTS
 * Used both at one-shot setup time and as the source of truth in case
 * Rank Math options get reset.
 * ─────────────────────────────────────────────────────────────────
 */
function dx_rank_math_cpt_templates() {
    return [
        'review' => [
            'title'       => '%title% - In-Depth Review | Dubai Xtra',
            'description' => 'Read our honest review of %title%. Score, photos, and verdict. Dubai Xtra editorial review.',
        ],
        'best-list' => [
            'title'       => '%title% | Dubai Xtra',
            'description' => 'Discover the %title%. Curated by Dubai Xtra with honest picks and insider tips.',
        ],
        'owner-spotlight' => [
            'title'       => 'Meet %title% | Dubai Xtra Spotlight',
            'description' => 'The story behind %title%. An exclusive Dubai Xtra owner spotlight.',
        ],
        'business-spotlight' => [
            'title'       => '%title% - Business Feature | Dubai Xtra',
            'description' => 'What makes %title% stand out. A Dubai Xtra business spotlight.',
        ],
        'things-to-do' => [
            'title'       => '%title% | Dubai Xtra',
            'description' => 'The best things to do in Dubai right now. Curated picks from Dubai Xtra.',
        ],
        'guide' => [
            'title'       => '%title% | Dubai Xtra Guide',
            'description' => 'Your complete guide to %title%. Tips, recommendations, and insider knowledge.',
        ],
        'listing' => [
            'title'       => '%title% - Dubai Business Directory | Dubai Xtra',
            'description' => 'Find %title% on Dubai Xtra. Verified listing with contact details, reviews, and more.',
        ],
    ];
}

/**
 * ─────────────────────────────────────────────────────────────────
 * PER-POST META GENERATORS (best-list)
 * ─────────────────────────────────────────────────────────────────
 */

/**
 * Build the SEO title. Tries to fit "Title | Dubai Xtra" inside 60 chars.
 * Falls back to the bare title if appending the brand would exceed it.
 */
function dx_seo_title_for_post($title) {
    $title = trim(html_entity_decode($title, ENT_QUOTES));
    $brand = ' | Dubai Xtra';
    $max   = 60;

    if (mb_strlen($title) + mb_strlen($brand) <= $max) {
        return $title . $brand;
    }
    if (mb_strlen($title) <= $max) {
        return $title;
    }
    return mb_substr($title, 0, $max - 1) . '...';
}

/**
 * Generate a focus keyword from the post title.
 * Strips year suffixes (e.g. "2024"), lowercases, normalises whitespace.
 */
function dx_focus_keyword_for_post($title) {
    $kw = mb_strtolower(html_entity_decode($title, ENT_QUOTES));
    $kw = preg_replace('/\s+\d{4}$/', '', $kw);  // strip trailing year
    $kw = preg_replace('/[&]/', 'and', $kw);
    $kw = preg_replace('/\s+/', ' ', $kw);
    return trim($kw);
}

/**
 * Generate a meta description from the title + item count. Picks
 * deterministically from a small template pool keyed on a hash of the
 * title, so descriptions vary across the archive without repeating.
 */
function dx_meta_description_for_best_list($title, $count) {
    $title_clean = html_entity_decode($title, ENT_QUOTES);

    // Strip the leading "Best" so we can drop the noun-phrase into a sentence.
    $topic = preg_replace('/^Best\s+/i', '', $title_clean);

    // Detect location, default to Dubai
    $location = 'Dubai';
    if (preg_match('/abu dhabi/i', $topic)) {
        $location = 'Abu Dhabi';
    }

    // Strip location suffix from the topic itself
    $topic = preg_replace('/\s+(In\s+)?(Dubai|Abu Dhabi|UAE)([,.\s].*)?$/i', '', $topic);
    $topic = trim($topic);
    $topic_lower = mb_strtolower($topic);

    // Replace ampersand for readable prose
    $topic_lower = str_replace('&amp;', 'and', $topic_lower);
    $topic_lower = str_replace('&', 'and', $topic_lower);

    $count_phrase = $count > 0 ? "{$count}" : 'top';

    $templates = [
        "Discover {$location}'s best {$topic_lower}. Our curated guide to {$count_phrase} top picks from Dubai Xtra. See our recommendations.",
        "Looking for {$topic_lower} in {$location}? Our handpicked list of {$count_phrase} from Dubai Xtra. See our top picks.",
        "{$count_phrase} of {$location}'s best {$topic_lower}, reviewed and ranked by Dubai Xtra. Find the best here.",
        "The best {$topic_lower} in {$location}. {$count_phrase} curated venues worth knowing. See our top picks.",
        "{$location}'s best {$topic_lower}: {$count_phrase} editorial picks from Dubai Xtra. Find the best options here.",
    ];

    // Deterministic selection so the same post always gets the same template
    $idx = abs(crc32($title_clean)) % count($templates);
    $desc = $templates[$idx];

    // Hard truncate at 155 chars as a safety net
    if (mb_strlen($desc) > 155) {
        $desc = mb_substr($desc, 0, 152) . '...';
    }
    return $desc;
}

/**
 * ─────────────────────────────────────────────────────────────────
 * RUNNERS
 * ─────────────────────────────────────────────────────────────────
 */

/**
 * Apply Rank Math meta to all best-list posts. Returns count.
 */
function dx_apply_seo_meta_to_best_lists() {
    $posts = get_posts([
        'post_type'      => 'best-list',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'fields'         => 'ids',
    ]);

    foreach ($posts as $post_id) {
        $title = get_the_title($post_id);
        $items = get_field('list_items', $post_id);
        $count = is_array($items) ? count(array_filter($items, fn($i) => !empty($i['business_name']))) : 0;

        update_post_meta($post_id, 'rank_math_title',         dx_seo_title_for_post($title));
        update_post_meta($post_id, 'rank_math_description',   dx_meta_description_for_best_list($title, $count));
        update_post_meta($post_id, 'rank_math_focus_keyword', dx_focus_keyword_for_post($title));
    }

    return count($posts);
}

/**
 * Set default Rank Math title and description templates for all 7 CPTs.
 * Stored in the rank-math-options-titles option as a serialized array,
 * keyed pt_{post_type}_title / pt_{post_type}_description.
 */
function dx_apply_rank_math_cpt_templates() {
    $options = get_option('rank-math-options-titles', []);
    if (!is_array($options)) $options = [];

    foreach (dx_rank_math_cpt_templates() as $cpt => $tmpl) {
        $options['pt_' . $cpt . '_title']       = $tmpl['title'];
        $options['pt_' . $cpt . '_description'] = $tmpl['description'];
    }

    update_option('rank-math-options-titles', $options);
}

/**
 * ─────────────────────────────────────────────────────────────────
 * ADMIN NOTICE + TRIGGER
 * ─────────────────────────────────────────────────────────────────
 */
add_action('admin_notices', 'dx_seo_meta_notice');
function dx_seo_meta_notice() {
    if (!current_user_can('manage_options')) return;

    if (isset($_GET['dx_seo_done']) && $_GET['dx_seo_done'] === '1') {
        $n = isset($_GET['n']) ? (int) $_GET['n'] : 0;
        echo '<div class="notice notice-success is-dismissible"><p><strong>Dubai Xtra:</strong> Rank Math meta applied to ' . $n . ' best-list post(s) and CPT templates set for all 7 post types.</p></div>';
        return;
    }

    if (get_option('dx_seo_meta_seeded')) return;

    $url = wp_nonce_url(add_query_arg('dx_seo_setup', '1', admin_url('index.php')), 'dx_seo_setup');
    echo '<div class="notice notice-info"><p><strong>Dubai Xtra:</strong> Rank Math SEO setup not yet run. ';
    echo '<a href="' . esc_url($url) . '" class="button button-primary" style="margin-left: 8px;">Run SEO Setup</a>';
    echo ' <span style="color: #777; font-size: 12px; margin-left: 8px;">Writes meta to existing best-list posts + sets CPT defaults.</span></p></div>';
}

add_action('admin_init', 'dx_maybe_run_seo_meta_setup');
function dx_maybe_run_seo_meta_setup() {
    if (!isset($_GET['dx_seo_setup']) || $_GET['dx_seo_setup'] !== '1') return;
    if (!current_user_can('manage_options')) return;
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'dx_seo_setup')) return;
    if (get_option('dx_seo_meta_seeded')) {
        wp_safe_redirect(admin_url('index.php'));
        exit;
    }

    @set_time_limit(120);

    $count = dx_apply_seo_meta_to_best_lists();
    dx_apply_rank_math_cpt_templates();

    update_option('dx_seo_meta_seeded', current_time('mysql'));

    wp_safe_redirect(admin_url('index.php?dx_seo_done=1&n=' . (int) $count));
    exit;
}
