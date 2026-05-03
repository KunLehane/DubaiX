<?php
/**
 * Dubai Xtra - Migrate "Best" Posts to best-list CPT
 *
 * Imported content from the previous site arrived as standard posts.
 * This migration finds all post_type='post' entries whose title starts
 * with "Best" and switches their post_type to 'best-list', preserving:
 *   - post title
 *   - post slug (post_name)
 *   - post content
 *   - post excerpt
 *   - post date / status / author
 *   - featured image (the _thumbnail_id post_meta is keyed by post ID,
 *     so it follows automatically)
 *   - all post_meta and taxonomies (same reason - separate tables keyed
 *     by post ID)
 *
 * Manual trigger via admin notice + button. Logs migrated entries to
 * the dx_best_migration_log option for review.
 *
 * Re-runnable - already-migrated posts will not match the WHERE clause.
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

/**
 * Show admin notice on Dashboard / Posts list with the candidate count.
 * After a migration run, show the success notice with a collapsible log.
 */
add_action('admin_notices', 'dx_best_migration_notice');
function dx_best_migration_notice() {
    if (!current_user_can('manage_options')) return;

    if (isset($_GET['dx_migrated_best']) && $_GET['dx_migrated_best'] === '1') {
        $log   = get_option('dx_best_migration_log', []);
        $count = is_array($log) ? count($log) : 0;
        echo '<div class="notice notice-success is-dismissible">';
        echo '<p><strong>Dubai Xtra:</strong> Migrated ' . (int) $count . ' "Best" post(s) to the best-list CPT.</p>';
        if ($count) {
            echo '<details><summary style="cursor: pointer; font-weight: 600;">View migration log</summary><ul style="margin-top: 8px;">';
            foreach ($log as $entry) {
                $edit = admin_url('post.php?post=' . (int) $entry['ID'] . '&action=edit');
                echo '<li><a href="' . esc_url($edit) . '">' . esc_html($entry['title']) . '</a> (ID ' . (int) $entry['ID'] . ', slug: <code>' . esc_html($entry['slug']) . '</code>)</li>';
            }
            echo '</ul></details>';
        }
        echo '</div>';
        return;
    }

    // Only show the prompt on Dashboard or Posts list to reduce admin noise.
    global $pagenow;
    if (!in_array($pagenow, ['index.php', 'edit.php'], true)) return;

    $count = dx_count_best_post_candidates();
    if (!$count) return;

    $url = wp_nonce_url(add_query_arg('dx_migrate_best', '1', admin_url('index.php')), 'dx_migrate_best');
    echo '<div class="notice notice-warning">';
    echo '<p><strong>Dubai Xtra:</strong> Found ' . (int) $count . ' standard post(s) with titles starting with "Best". ';
    echo 'These look like best-of lists imported from the old site. Convert them to the best-list CPT? ';
    echo '<a href="' . esc_url($url) . '" class="button button-primary" style="margin-left: 8px;">Migrate Best Posts</a></p>';
    echo '</div>';
}

/**
 * Trigger handler. Verifies nonce, runs migration, redirects with status.
 */
add_action('admin_init', 'dx_maybe_migrate_best_posts');
function dx_maybe_migrate_best_posts() {
    if (!isset($_GET['dx_migrate_best']) || $_GET['dx_migrate_best'] !== '1') return;
    if (!current_user_can('manage_options')) return;
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'dx_migrate_best')) return;

    $log = dx_migrate_best_posts();
    update_option('dx_best_migration_log', $log);

    wp_safe_redirect(admin_url('index.php?dx_migrated_best=1'));
    exit;
}

/**
 * Count candidates without doing the migration (used by the admin notice).
 */
function dx_count_best_post_candidates() {
    global $wpdb;
    return (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->posts}
         WHERE post_type = 'post'
         AND post_title LIKE 'Best%'
         AND post_status NOT IN ('trash', 'auto-draft', 'inherit')"
    );
}

/**
 * Run the migration. Returns array of migrated entries with title/slug/ID.
 */
function dx_migrate_best_posts() {
    global $wpdb;

    $candidates = $wpdb->get_results(
        "SELECT ID, post_title, post_name, post_status FROM {$wpdb->posts}
         WHERE post_type = 'post'
         AND post_title LIKE 'Best%'
         AND post_status NOT IN ('trash', 'auto-draft', 'inherit')
         ORDER BY ID ASC"
    );

    $log = [];
    foreach ($candidates as $row) {
        // Direct DB update so we don't trigger save_post hooks that may
        // not handle a post_type change cleanly (revisions, slugs, etc.).
        $updated = $wpdb->update(
            $wpdb->posts,
            ['post_type' => 'best-list'],
            ['ID'        => $row->ID],
            ['%s'],
            ['%d']
        );

        if ($updated !== false) {
            $log[] = [
                'ID'       => (int) $row->ID,
                'title'    => $row->post_title,
                'slug'     => $row->post_name,
                'status'   => $row->post_status,
                'migrated' => current_time('mysql'),
            ];

            error_log("[Dubai Xtra] Migrated post #{$row->ID} '{$row->post_title}' (slug: {$row->post_name}) -> best-list");
            clean_post_cache($row->ID);
        } else {
            error_log("[Dubai Xtra] FAILED to migrate post #{$row->ID} '{$row->post_title}'");
        }
    }

    if (count($log)) {
        // Flush WP's rewrite rule cache so /best/{slug}/ resolves immediately.
        flush_rewrite_rules(false);
    }

    return $log;
}
