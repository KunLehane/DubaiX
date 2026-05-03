<?php
/**
 * Dubai Xtra - Pending submission notice
 *
 * Site-wide admin notice showing the number of user-submitted listing
 * drafts awaiting review. Only counts drafts that have the private
 * _dx_submitter_name meta (set by the frontend submission handler) so
 * the admin's own in-progress drafts don't inflate the count.
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

add_action('admin_notices', 'dx_pending_listings_notice');
function dx_pending_listings_notice() {
    if (!current_user_can('edit_posts')) return;

    // Hide on the listings edit screen - redundant since the page IS the list
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if ($screen && in_array($screen->id, ['edit-listing', 'listing'], true)) return;

    global $wpdb;
    $count = (int) $wpdb->get_var(
        "SELECT COUNT(DISTINCT p.ID)
         FROM $wpdb->posts p
         INNER JOIN $wpdb->postmeta pm ON p.ID = pm.post_id
         WHERE p.post_type = 'listing'
         AND p.post_status = 'draft'
         AND pm.meta_key = '_dx_submitter_name'"
    );

    if ($count <= 0) return;

    $url   = admin_url('edit.php?post_type=listing&post_status=draft');
    $label = $count === 1
        ? '1 listing submission'
        : number_format_i18n($count) . ' listing submissions';

    echo '<div class="notice notice-warning" style="border-left-color: #C9A84C;">';
    echo '<p style="font-size: 14px;"><strong>Dubai Xtra:</strong> ';
    echo '<span style="color: #1A1A2E;">' . esc_html($label) . '</span> awaiting review. ';
    echo '<a href="' . esc_url($url) . '" class="button button-primary" style="margin-left: 8px; background: #1A1A2E; border-color: #1A1A2E; color: #C9A84C;">Review Submissions →</a>';
    echo '</p></div>';
}
