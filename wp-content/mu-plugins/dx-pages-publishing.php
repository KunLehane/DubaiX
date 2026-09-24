<?php
/**
 * Plugin Name: Dubai Xtra Pages Publishing
 * Description: Rebuild the public Cloudflare site when published WordPress content changes.
 * Version: 1.0.1
 */
defined('ABSPATH') || exit;

// A separate Cloudways domain alias keeps WordPress available after the public
// domain moves. Do not rewrite the stored site URLs or the existing database.
if (strtolower($_SERVER['HTTP_HOST'] ?? '') === 'cms.dubaixtra.com') {
    add_filter('option_home', function () { return 'https://cms.dubaixtra.com'; });
    add_filter('option_siteurl', function () { return 'https://cms.dubaixtra.com'; });
    // Plugin/content constants may be set before this regular plugin loads.
    // Keep editor assets on WordPress instead of the public static frontend.
    $cms_asset_url = function ($url) {
        return preg_replace('~^https?://(?:www\.)?dubaixtra\.com(?=/|$)~i', 'https://cms.dubaixtra.com', $url);
    };
    foreach (['content_url', 'plugins_url', 'script_loader_src', 'style_loader_src', 'theme_file_uri'] as $hook) {
        add_filter($hook, $cms_asset_url);
    }
    add_filter('upload_dir', function ($uploads) use ($cms_asset_url) {
        $uploads['url'] = $cms_asset_url($uploads['url']);
        $uploads['baseurl'] = $cms_asset_url($uploads['baseurl']);
        return $uploads;
    });
    add_action('send_headers', function () { header('X-Robots-Tag: noindex, nofollow'); });
    add_filter('wp_mail_from', function ($from) {
        return str_replace('@cms.dubaixtra.com', '@dubaixtra.com', $from);
    });
}

add_action('rest_api_init', function () {
    register_rest_route('dx-pages/v1', '/routes', [
        'methods' => 'GET', 'permission_callback' => '__return_true',
        'callback' => function () {
            $routes = [home_url('/')];
            $types = array_values(array_diff(get_post_types(['public' => true]), ['attachment']));
            $posts = get_posts(['post_type' => $types, 'post_status' => 'publish', 'has_password' => false, 'numberposts' => -1, 'fields' => 'ids']);
            foreach ($posts as $id) $routes[] = get_permalink($id);
            foreach ($types as $type) { $archive = get_post_type_archive_link($type); if ($archive) $routes[] = $archive; }
            foreach (get_taxonomies(['public' => true]) as $taxonomy) {
                $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => true]);
                if (is_wp_error($terms)) continue;
                foreach ($terms as $term) { $link = get_term_link($term); if (!is_wp_error($link)) $routes[] = $link; }
            }
            $response = new WP_REST_Response(array_values(array_unique($routes)));
            $response->header('Cache-Control', 'no-store');
            return $response;
        }
    ]);
});

function dx_pages_queue_rebuild() {
    static $queued = false;
    if ($queued || !get_option('dx_pages_deploy_hook')) return;
    $queued = true;
    // ACF and taxonomy updates have completed by the time shutdown runs.
    add_action('shutdown', 'dx_pages_send_rebuild');
}
function dx_pages_send_rebuild() {
    $hook = get_option('dx_pages_deploy_hook');
    if (!$hook) return;
    $response = wp_remote_post($hook, ['timeout' => 10, 'redirection' => 0, 'body' => '']);
    $ok = !is_wp_error($response) && wp_remote_retrieve_response_code($response) >= 200 && wp_remote_retrieve_response_code($response) < 300;
    update_option('dx_pages_last_request', ['time' => time(), 'accepted' => $ok], false);
}
add_action('transition_post_status', function ($new, $old, $post) {
    if (wp_is_post_revision($post->ID) || wp_is_post_autosave($post->ID)) return;
    if ($new === 'publish' || $old === 'publish') dx_pages_queue_rebuild();
}, 20, 3);
add_action('before_delete_post', function ($id, $post) {
    if ($post->post_status === 'publish') dx_pages_queue_rebuild();
}, 10, 2);
add_action('edited_term', 'dx_pages_queue_rebuild');
add_action('delete_term', 'dx_pages_queue_rebuild');
add_action('wp_update_nav_menu', 'dx_pages_queue_rebuild');
add_action('transition_comment_status', function ($new, $old) {
    if ($new === 'approved' || $old === 'approved') dx_pages_queue_rebuild();
}, 10, 2);

add_action('admin_menu', function () {
    add_options_page('Dubai Xtra Publishing', 'Dubai Xtra Publishing', 'manage_options', 'dx-pages', 'dx_pages_settings');
});
function dx_pages_settings() {
    if (!current_user_can('manage_options')) return;
    $notice = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_admin_referer('dx_pages_settings');
        $hook = trim(wp_unslash($_POST['deploy_hook'] ?? ''));
        if ($hook) {
            if (!preg_match('~^https://api\.cloudflare\.com/client/v4/pages/webhooks/deploy_hooks/[a-zA-Z0-9-]+$~D', $hook)) {
                $notice = 'Invalid Cloudflare Pages deploy hook. No changes saved.';
            } else {
                update_option('dx_pages_deploy_hook', $hook, false);
                $notice = 'Deploy hook saved.';
            }
        }
        if (isset($_POST['rebuild']) && !$notice) {
            dx_pages_send_rebuild();
            $notice = 'Rebuild request sent. Check the request status below and deployment status in Cloudflare.';
        }
    }
    $status = get_option('dx_pages_last_request');
    echo '<div class="wrap"><h1>Dubai Xtra Publishing</h1>';
    if ($notice) echo '<div class="notice notice-info"><p>' . esc_html($notice) . '</p></div>';
    echo '<p>Published changes request a new Cloudflare build. Drafts remain private. A successful request means the build was queued, not that deployment is complete.</p>';
    echo '<p>Deploy hook: <strong>' . (get_option('dx_pages_deploy_hook') ? 'Configured' : 'Not configured') . '</strong></p>';
    if ($status) echo '<p>Last request: ' . esc_html(wp_date('Y-m-d H:i:s', $status['time'])) . ' - ' . ($status['accepted'] ? 'Accepted' : 'Failed - check Cloudflare and retry') . '</p>';
    echo '<form method="post">';
    wp_nonce_field('dx_pages_settings');
    echo '<p><label for="deploy_hook">Cloudflare deploy hook (leave blank to keep existing)</label><br><input type="password" id="deploy_hook" name="deploy_hook" class="large-text" autocomplete="new-password"></p>';
    submit_button('Save hook');
    submit_button('Rebuild public site', 'secondary', 'rebuild');
    echo '</form></div>';
}
