<?php
/**
 * Dubai Xtra Child Theme
 * GeneratePress Premium child theme for dubaixtra.com
 *
 * @package Dubai_Xtra
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

define('DX_VERSION', '1.0.0');
define('DX_DIR', get_stylesheet_directory());
define('DX_URI', get_stylesheet_directory_uri());

/**
 * ═══ ENQUEUE STYLES & FONTS ═══
 */
add_action('wp_enqueue_scripts', function () {
    // Parent theme
    wp_enqueue_style('generatepress', get_template_directory_uri() . '/style.css');

    // Google Fonts
    wp_enqueue_style(
        'dx-fonts',
        'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500&family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400&display=swap',
        [],
        null
    );

    // Child theme
    wp_enqueue_style('dubai-xtra', get_stylesheet_uri(), ['generatepress', 'dx-fonts'], DX_VERSION);

    // Custom JS
    wp_enqueue_script('dx-main', DX_URI . '/assets/js/main.js', [], DX_VERSION, true);
});

/**
 * ═══ INCLUDE MODULES ═══
 */
require_once DX_DIR . '/inc/custom-post-types.php';
require_once DX_DIR . '/inc/taxonomies.php';
require_once DX_DIR . '/inc/acf-fields.php';
require_once DX_DIR . '/inc/template-functions.php';

if (is_admin()) {
    require_once DX_DIR . '/inc/seed-content.php';
    require_once DX_DIR . '/inc/migrate-best-posts.php';
    require_once DX_DIR . '/inc/seo-meta.php';
    require_once DX_DIR . '/inc/admin-submissions-notice.php';
}

/**
 * ═══ THEME SETUP ═══
 */
add_action('after_setup_theme', function () {
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');

    // Custom image sizes
    add_image_size('dx-hero', 1200, 600, true);
    add_image_size('dx-card', 600, 400, true);
    add_image_size('dx-thumbnail', 400, 300, true);
    add_image_size('dx-listing-logo', 200, 200, false);

    // Register nav menus
    register_nav_menus([
        'dx-primary'  => __('Primary Menu', 'dubai-xtra'),
        'dx-footer-content' => __('Footer Content', 'dubai-xtra'),
        'dx-footer-business' => __('Footer Business', 'dubai-xtra'),
        'dx-footer-social'   => __('Footer Social', 'dubai-xtra'),
    ]);
});

/**
 * ═══ GENERATEPRESS CUSTOMIZATIONS ═══
 */
// Set GP default font family
add_filter('generate_typography_default_fonts', function ($fonts) {
    $fonts[] = 'Cormorant Garamond';
    $fonts[] = 'DM Sans';
    return $fonts;
});

// Force GeneratePress hamburger menu to kick in earlier (default 768 -> 1024)
// so the 6-item nav stops wrapping awkwardly on tablets.
add_filter('generate_mobile_menu_breakpoint', function () {
    return 1024;
});
add_filter('generate_navigation_breakpoint', function () {
    return 1024;
});

/**
 * ═══ MOBILE CTA BAR ═══
 * Renders a full-width "Submit a Listing" bar at the very top of the
 * page on mobile. Above the header. Hidden on desktop via CSS.
 */
add_action('generate_before_header', function () {
    $url = home_url('/submit-listing/');
    echo '<a href="' . esc_url($url) . '" class="dx-mobile-cta-bar">Submit a Listing →</a>';
});

/**
 * ═══ TYPOGRAPHY: NO EM / EN DASHES ═══
 * WordPress's wptexturize() auto-converts " - " in titles and content
 * into en-dashes / em-dashes. The Dubai Xtra style rule is plain hyphens
 * only, so we run after wptexturize and revert the substitution.
 * Smart quotes and ellipses are preserved.
 */
add_filter('wptexturize', function ($text) {
    return str_replace(["\xE2\x80\x93", "\xE2\x80\x94"], '-', $text);
}, 100);

/**
 * ═══ TAXONOMY ARCHIVE QUERIES: INCLUDE ALL DX CPTs ═══
 * Our taxonomies (business-category, dubai-district, content-tag) are
 * shared across the editorial CPTs. WordPress's default main query for
 * a taxonomy archive only includes 'post' post_type, so the term page
 * shows zero results even when posts exist. Force the query to include
 * all 7 editorial CPTs on shared-taxonomy archives.
 */
add_action('pre_get_posts', function ($query) {
    if (is_admin() || !$query->is_main_query()) return;

    $shared_taxonomies = [
        'business-category',
        'dubai-district',
        'content-tag',
        'best-list-category',
        'things-to-do-type',
    ];

    if ($query->is_tax($shared_taxonomies)) {
        $query->set('post_type', [
            'review',
            'best-list',
            'owner-spotlight',
            'business-spotlight',
            'things-to-do',
            'guide',
            'listing',
        ]);
    }
});

/**
 * ═══ ADMIN COLUMNS ═══
 */
add_filter('manage_review_posts_columns', function ($columns) {
    $columns['review_score'] = __('Score', 'dubai-xtra');
    $columns['feature_type'] = __('Type', 'dubai-xtra');
    return $columns;
});

add_action('manage_review_posts_custom_column', function ($column, $post_id) {
    if ($column === 'review_score') {
        echo esc_html(get_field('review_score', $post_id) ?: '—');
    }
    if ($column === 'feature_type') {
        echo esc_html(get_field('feature_type', $post_id) ?: 'Editorial');
    }
}, 10, 2);

add_filter('manage_listing_posts_columns', function ($columns) {
    $columns['submitter']    = __('Submitter', 'dubai-xtra');
    $columns['listing_tier'] = __('Tier', 'dubai-xtra');
    $columns['is_verified']  = __('Verified', 'dubai-xtra');
    return $columns;
});

add_action('manage_listing_posts_custom_column', function ($column, $post_id) {
    if ($column === 'submitter') {
        $name  = get_post_meta($post_id, '_dx_submitter_name', true);
        $email = get_field('email', $post_id);
        if ($name) {
            echo esc_html($name);
            if ($email) {
                echo '<br><a href="mailto:' . esc_attr($email) . '" style="color: #C9A84C; font-size: 11px;">' . esc_html($email) . '</a>';
            }
        } else {
            echo '<span style="color: #999;">-</span>';
        }
    }
    if ($column === 'listing_tier') {
        echo esc_html(get_field('listing_tier', $post_id) ?: 'Free');
    }
    if ($column === 'is_verified') {
        echo get_field('is_verified', $post_id) ? '✓' : '-';
    }
}, 10, 2);

/**
 * ═══ LISTING SUBMISSION FORM HANDLER ═══
 * Frontend form on /submit-listing/ posts to admin-post.php with
 * action=dx_submit_listing. We create a draft listing post, populate the
 * ACF fields, optionally sideload the uploaded logo, email the admin,
 * and redirect back to the page with ?dx_submitted=1 (or ?dx_submit_error=...).
 */
define('DX_SUBMISSION_RECIPIENT', 'aidan.lehane@gmail.com');

add_action('admin_post_dx_submit_listing',        'dx_handle_listing_submission');
add_action('admin_post_nopriv_dx_submit_listing', 'dx_handle_listing_submission');

function dx_handle_listing_submission() {
    // Resolve the page we redirect back to (so we never depend on referer)
    $page          = get_page_by_path('submit-listing');
    $redirect_base = $page ? get_permalink($page) : home_url('/');

    // Nonce check
    if (
        empty($_POST['dx_listing_nonce']) ||
        !wp_verify_nonce($_POST['dx_listing_nonce'], 'dx_submit_listing')
    ) {
        wp_safe_redirect(add_query_arg('dx_submit_error', 'security', $redirect_base) . '#submit-form-section');
        exit;
    }

    // Sanitise
    $business_name  = sanitize_text_field($_POST['business_name']        ?? '');
    $description    = sanitize_textarea_field($_POST['business_description'] ?? '');
    $contact_name   = sanitize_text_field($_POST['contact_name']         ?? '');
    $email          = sanitize_email($_POST['contact_email']             ?? '');
    $phone          = sanitize_text_field($_POST['contact_phone']        ?? '');
    $website        = esc_url_raw($_POST['website']                       ?? '');
    $instagram      = sanitize_text_field($_POST['instagram']             ?? '');
    $cat_id         = (int) ($_POST['business_category']                  ?? 0);
    $district_id    = (int) ($_POST['dubai_district']                     ?? 0);

    // Required-field validation
    if (!$business_name || !$contact_name || !$email || !is_email($email)) {
        wp_safe_redirect(add_query_arg('dx_submit_error', 'required', $redirect_base) . '#submit-form-section');
        exit;
    }

    // Create draft listing
    $post_id = wp_insert_post([
        'post_type'    => 'listing',
        'post_status'  => 'draft',
        'post_title'   => $business_name,
        'post_content' => $description,
        'post_excerpt' => wp_trim_words($description, 30),
    ], true);

    if (is_wp_error($post_id)) {
        wp_safe_redirect(add_query_arg('dx_submit_error', 'create_failed', $redirect_base) . '#submit-form-section');
        exit;
    }

    // ACF fields
    if (function_exists('update_field')) {
        update_field('business_name',        $business_name, $post_id);
        update_field('business_description', $description,   $post_id);
        update_field('phone',                $phone,         $post_id);
        update_field('email',                $email,         $post_id);
        update_field('website',              $website,       $post_id);
        update_field('instagram',            $instagram,     $post_id);
        update_field('listing_tier',         'Free',         $post_id);
        update_field('is_verified',          0,              $post_id);
        update_field('is_featured',          0,              $post_id);
    }

    // Submitter contact name as private post meta - not an ACF field, but
    // the admin will want it when reviewing the draft.
    update_post_meta($post_id, '_dx_submitter_name', $contact_name);

    // Taxonomies
    if ($cat_id)      wp_set_post_terms($post_id, [$cat_id],      'business-category');
    if ($district_id) wp_set_post_terms($post_id, [$district_id], 'dubai-district');

    // Logo upload (optional)
    $logo_id = 0;
    if (!empty($_FILES['business_logo']['tmp_name']) && empty($_FILES['business_logo']['error'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $logo_id = media_handle_upload('business_logo', $post_id);
        if (!is_wp_error($logo_id) && $logo_id) {
            if (function_exists('update_field')) {
                update_field('business_logo', $logo_id, $post_id);
            }
            set_post_thumbnail($post_id, $logo_id);
        } else {
            $logo_id = 0;
        }
    }

    // Notification email
    $cat_name      = $cat_id      ? get_term($cat_id)->name      : '(not specified)';
    $district_name = $district_id ? get_term($district_id)->name : '(not specified)';
    $edit_url      = admin_url('post.php?post=' . $post_id . '&action=edit');

    $subject = 'New Dubai Xtra Listing Submission: ' . $business_name;

    $body  = '<div style="font-family: Arial, sans-serif; max-width: 640px;">';
    $body .= '<h2 style="color:#1A1A2E; margin:0 0 16px;">New Listing Submission</h2>';
    $body .= '<p style="color:#555;">A new listing has been submitted on Dubai Xtra. It is saved as a <strong>draft</strong> for your review.</p>';
    $body .= '<h3 style="color:#C9A84C; font-size:14px; letter-spacing:0.1em; text-transform:uppercase; margin:24px 0 8px;">Business</h3>';
    $body .= '<table style="width:100%; border-collapse:collapse;">';
    $body .= '<tr><td style="padding:6px 12px 6px 0; color:#888; width:140px;">Business Name</td><td style="padding:6px 0;"><strong>' . esc_html($business_name) . '</strong></td></tr>';
    $body .= '<tr><td style="padding:6px 12px 6px 0; color:#888; vertical-align:top;">Description</td><td style="padding:6px 0;">' . nl2br(esc_html($description ?: '(none provided)')) . '</td></tr>';
    $body .= '<tr><td style="padding:6px 12px 6px 0; color:#888;">Category</td><td style="padding:6px 0;">' . esc_html($cat_name) . '</td></tr>';
    $body .= '<tr><td style="padding:6px 12px 6px 0; color:#888;">District</td><td style="padding:6px 0;">' . esc_html($district_name) . '</td></tr>';
    $body .= '<tr><td style="padding:6px 12px 6px 0; color:#888;">Website</td><td style="padding:6px 0;">' . ($website ? '<a href="' . esc_url($website) . '">' . esc_html($website) . '</a>' : '(not provided)') . '</td></tr>';
    $body .= '<tr><td style="padding:6px 12px 6px 0; color:#888;">Instagram</td><td style="padding:6px 0;">' . esc_html($instagram ?: '(not provided)') . '</td></tr>';
    $body .= '<tr><td style="padding:6px 12px 6px 0; color:#888;">Logo uploaded</td><td style="padding:6px 0;">' . ($logo_id ? 'Yes (attached to draft)' : 'No') . '</td></tr>';
    $body .= '</table>';
    $body .= '<h3 style="color:#C9A84C; font-size:14px; letter-spacing:0.1em; text-transform:uppercase; margin:24px 0 8px;">Contact</h3>';
    $body .= '<table style="width:100%; border-collapse:collapse;">';
    $body .= '<tr><td style="padding:6px 12px 6px 0; color:#888; width:140px;">Submitted by</td><td style="padding:6px 0;">' . esc_html($contact_name) . '</td></tr>';
    $body .= '<tr><td style="padding:6px 12px 6px 0; color:#888;">Email</td><td style="padding:6px 0;"><a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a></td></tr>';
    $body .= '<tr><td style="padding:6px 12px 6px 0; color:#888;">Phone</td><td style="padding:6px 0;">' . esc_html($phone ?: '(not provided)') . '</td></tr>';
    $body .= '</table>';
    $body .= '<p style="margin:32px 0 0;"><a href="' . esc_url($edit_url) . '" style="display:inline-block; background:#1A1A2E; color:#C9A84C; padding:12px 24px; text-decoration:none; font-weight:700; letter-spacing:0.1em; text-transform:uppercase; font-size:12px;">Review in WP Admin →</a></p>';
    $body .= '</div>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: Dubai Xtra <noreply@' . parse_url(home_url(), PHP_URL_HOST) . '>',
        'Reply-To: ' . sanitize_text_field($contact_name) . ' <' . $email . '>',
    ];

    wp_mail(DX_SUBMISSION_RECIPIENT, $subject, $body, $headers);

    wp_safe_redirect(add_query_arg('dx_submitted', '1', $redirect_base) . '#submit-form-section');
    exit;
}

/**
 * ═══ FULL-WIDTH TEMPLATES ═══
 * Front page and our custom singles render edge-to-edge. The .dx-section
 * wrapper inside each template handles its own 1200px max-width, so we
 * just need to disable GP's outer container constraint and its auto title.
 */
function dx_is_full_width_template() {
    return is_front_page()
        || is_page_template('page-submit-listing.php')
        || is_singular(['review', 'best-list', 'owner-spotlight', 'business-spotlight', 'things-to-do', 'guide', 'listing'])
        || is_post_type_archive(['review', 'best-list', 'owner-spotlight', 'business-spotlight', 'things-to-do', 'guide', 'listing']);
}

add_filter('body_class', function ($classes) {
    if (dx_is_full_width_template()) {
        $classes[] = 'dx-full-width';
    }
    return $classes;
});

// Hide GP's auto page title - our templates render their own H1
add_filter('generate_show_title', function ($show) {
    if (dx_is_full_width_template()) {
        return false;
    }
    return $show;
});

/**
 * ═══ FLUSH REWRITE ON ACTIVATION ═══
 */
add_action('after_switch_theme', function () {
    dx_register_post_types();
    dx_register_taxonomies();
    flush_rewrite_rules();
});
