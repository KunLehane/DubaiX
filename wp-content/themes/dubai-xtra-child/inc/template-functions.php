<?php
/**
 * Dubai Xtra — Template Functions
 * Helper functions used across templates.
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

/**
 * Output the score badge
 */
function dx_score_badge($score, $size = '') {
    $class = 'dx-score' . ($size === 'sm' ? ' dx-score-sm' : '');
    echo '<div class="' . esc_attr($class) . '">' . esc_html(number_format((float)$score, 1)) . '</div>';
}

/**
 * Output a section label with gold line
 */
function dx_section_header($label, $heading = '', $link_text = '', $link_url = '') {
    echo '<div class="dx-section-header">';
    echo '<div>';
    echo '<div class="gold-line"></div>';
    echo '<span class="section-label">' . esc_html($label) . '</span>';
    if ($heading) {
        echo '<h2 class="editorial-heading" style="margin-top: 8px;">' . esc_html($heading) . '</h2>';
    }
    echo '</div>';
    if ($link_text && $link_url) {
        echo '<a href="' . esc_url($link_url) . '" class="nav-link" style="font-size: 12px;">' . esc_html($link_text) . ' →</a>';
    }
    echo '</div>';
}

/**
 * Output a tag pill
 */
function dx_tag($text, $style = 'navy') {
    $class = 'dx-tag dx-tag-' . sanitize_html_class($style);
    echo '<span class="' . esc_attr($class) . '">' . esc_html($text) . '</span>';
}

/**
 * Get the primary business category for a post
 */
function dx_get_primary_category($post_id = null) {
    $terms = get_the_terms($post_id ?: get_the_ID(), 'business-category');
    return $terms && !is_wp_error($terms) ? $terms[0] : null;
}

/**
 * Get the primary district for a post
 */
function dx_get_primary_district($post_id = null) {
    $terms = get_the_terms($post_id ?: get_the_ID(), 'dubai-district');
    return $terms && !is_wp_error($terms) ? $terms[0] : null;
}

/**
 * Output district + category meta line
 */
function dx_meta_line($post_id = null) {
    $cat = dx_get_primary_category($post_id);
    $dist = dx_get_primary_district($post_id);
    $parts = [];
    if ($dist) $parts[] = $dist->name;
    if ($cat) $parts[] = $cat->name;
    if ($parts) {
        echo '<span class="dx-card-meta">' . esc_html(implode(' · ', $parts)) . '</span>';
    }
}

/**
 * Output verified badge
 */
function dx_verified_badge() {
    echo '<span class="dx-tag dx-tag-verified">VERIFIED</span>';
}

/**
 * Output feature type badge if sponsored
 */
function dx_feature_badge($post_id = null) {
    $type = get_field('feature_type', $post_id ?: get_the_ID());
    if ($type && $type !== 'Editorial') {
        $style = $type === 'Sponsored Feature' ? 'sponsored' : 'gold';
        dx_tag($type, $style);
    }
}

/**
 * Get review posts for homepage
 */
function dx_get_featured_reviews($count = 3) {
    return new WP_Query([
        'post_type'      => 'review',
        'posts_per_page' => $count,
        'meta_key'       => 'is_featured',
        'meta_value'     => '1',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
}

/**
 * Get things to do for homepage (current/upcoming)
 */
function dx_get_current_things_to_do($count = 4) {
    return new WP_Query([
        'post_type'      => 'things-to-do',
        'posts_per_page' => $count,
        'meta_query'     => [
            'relation' => 'OR',
            [
                'key'     => 'valid_until',
                'value'   => date('Y-m-d'),
                'compare' => '>=',
                'type'    => 'DATE',
            ],
            [
                'key'     => 'valid_until',
                'compare' => 'NOT EXISTS',
            ],
        ],
        'orderby' => 'date',
        'order'   => 'DESC',
    ]);
}

/**
 * Get featured spotlight (owner or business)
 */
function dx_get_featured_spotlight($type = 'owner-spotlight') {
    return new WP_Query([
        'post_type'      => $type,
        'posts_per_page' => 1,
        'meta_key'       => 'is_featured',
        'meta_value'     => '1',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
}

/**
 * Get premium directory listings
 */
function dx_get_premium_listings($count = 4) {
    return new WP_Query([
        'post_type'      => 'listing',
        'posts_per_page' => $count,
        'meta_key'       => 'listing_tier',
        'meta_value'     => 'Premium',
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
}

/**
 * Get best lists
 */
function dx_get_best_lists($count = 4) {
    return new WP_Query([
        'post_type'      => 'best-list',
        'posts_per_page' => $count,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
}

/**
 * Check if a Things To Do post is still valid
 */
function dx_is_ttd_valid($post_id = null) {
    $until = get_field('valid_until', $post_id ?: get_the_ID());
    if (!$until) return true;
    return strtotime($until) >= strtotime('today');
}

/**
 * Output price range display
 */
function dx_price_range($post_id = null) {
    $range = get_field('price_range', $post_id ?: get_the_ID());
    if ($range) {
        echo '<span class="dx-price-range">' . esc_html($range) . '</span>';
    }
}

/**
 * Render the site-wide Sponsored Feature widget.
 *
 * Pulls the most recent post (review / owner-spotlight / business-spotlight)
 * with feature_type = "Sponsored Feature", excluding the current post. If
 * no such post exists - or the only candidate IS the current post - the
 * widget renders nothing so callers can safely call this anywhere.
 *
 * Caller wraps it in either a sidebar slot (sticky context provided by the
 * sidebar wrapper) or an inline .dx-sponsored-section block.
 */
function dx_render_sponsored_widget() {
    $current_id = get_the_ID();

    $q = new WP_Query([
        'post_type'           => ['review', 'owner-spotlight', 'business-spotlight'],
        'posts_per_page'      => 1,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'post_status'         => 'publish',
        'post__not_in'        => $current_id ? [(int) $current_id] : [],
        'no_found_rows'       => true,
        'ignore_sticky_posts' => true,
        'meta_query'          => [
            ['key' => 'feature_type', 'value' => 'Sponsored Feature'],
        ],
    ]);

    if (!$q->have_posts()) {
        wp_reset_postdata();
        return;
    }

    $q->the_post();

    $type     = get_post_type();
    $business = get_field('business_name') ?: get_the_title();
    $score    = $type === 'review' ? get_field('review_score') : null;

    if ($type === 'review') {
        $excerpt = get_field('review_summary') ?: get_the_excerpt();
    } elseif ($type === 'business-spotlight') {
        $excerpt = get_field('business_tagline') ?: get_the_excerpt();
    } else {
        $excerpt = get_the_excerpt();
    }

    $img_url = has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'dx-card') : '';
    ?>
    <aside class="dx-sponsored-widget" aria-labelledby="dx-sponsored-heading-<?php the_ID(); ?>">
        <span class="dx-sponsored-label">Sponsored</span>
        <a href="<?php the_permalink(); ?>" class="dx-sponsored-card">
            <?php if ($img_url) : ?>
                <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($business); ?>" loading="lazy">
            <?php endif; ?>
            <div class="dx-sponsored-body">
                <div class="dx-sponsored-meta">
                    <h3 id="dx-sponsored-heading-<?php the_ID(); ?>"><?php echo esc_html($business); ?></h3>
                    <?php if ($score) dx_score_badge($score, 'sm'); ?>
                </div>
                <?php if ($excerpt) : ?>
                    <p><?php echo esc_html(wp_trim_words(wp_strip_all_tags($excerpt), 24)); ?></p>
                <?php endif; ?>
                <span class="dx-sponsored-cta">Read More →</span>
            </div>
        </a>
    </aside>
    <?php
    wp_reset_postdata();
}
