<?php
/**
 * Dubai Xtra — Custom Taxonomies
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

add_action('init', 'dx_register_taxonomies');

function dx_register_taxonomies() {

    // ═══ BUSINESS CATEGORY ═══
    // Note: slug 'categories' (not 'category') to avoid conflict with WP's
    // built-in category taxonomy, whose rewrite rules would otherwise win
    // and 404 on our term URLs.
    register_taxonomy('business-category', ['review', 'listing', 'owner-spotlight', 'business-spotlight', 'best-list', 'things-to-do'], [
        'labels' => [
            'name'          => 'Business Categories',
            'singular_name' => 'Business Category',
            'menu_name'     => 'Categories',
        ],
        'hierarchical' => true,
        'public'       => true,
        'rewrite'      => ['slug' => 'categories', 'with_front' => false],
        'show_in_rest' => true,
        'show_admin_column' => true,
    ]);

    // ═══ DUBAI DISTRICT ═══
    register_taxonomy('dubai-district', ['review', 'listing', 'owner-spotlight', 'business-spotlight', 'guide', 'things-to-do'], [
        'labels' => [
            'name'          => 'Dubai Districts',
            'singular_name' => 'Dubai District',
            'menu_name'     => 'Districts',
        ],
        'hierarchical' => true,
        'public'       => true,
        'rewrite'      => ['slug' => 'district', 'with_front' => false],
        'show_in_rest' => true,
        'show_admin_column' => true,
    ]);

    // ═══ BEST LIST CATEGORY ═══
    register_taxonomy('best-list-category', ['best-list'], [
        'labels' => [
            'name'          => 'Best List Categories',
            'singular_name' => 'Best List Category',
            'menu_name'     => 'List Categories',
        ],
        'hierarchical' => true,
        'public'       => true,
        'rewrite'      => ['slug' => 'best-category', 'with_front' => false],
        'show_in_rest' => true,
        'show_admin_column' => true,
    ]);

    // ═══ THINGS TO DO TYPE ═══
    register_taxonomy('things-to-do-type', ['things-to-do'], [
        'labels' => [
            'name'          => 'Things To Do Types',
            'singular_name' => 'Things To Do Type',
            'menu_name'     => 'TTD Types',
        ],
        'hierarchical' => true,
        'public'       => true,
        'rewrite'      => ['slug' => 'things-type', 'with_front' => false],
        'show_in_rest' => true,
        'show_admin_column' => true,
    ]);

    // ═══ CONTENT TAG ═══
    // Note: slug 'tags' (not 'tag') to avoid conflict with WP's built-in
    // post_tag taxonomy on the same /tag/ URL pattern.
    register_taxonomy('content-tag', ['review', 'listing', 'owner-spotlight', 'business-spotlight', 'best-list', 'things-to-do', 'guide'], [
        'labels' => [
            'name'          => 'Content Tags',
            'singular_name' => 'Content Tag',
            'menu_name'     => 'Tags',
        ],
        'hierarchical' => false,
        'public'       => true,
        'rewrite'      => ['slug' => 'tags', 'with_front' => false],
        'show_in_rest' => true,
        'show_admin_column' => false,
    ]);

    // ═══ SEED DEFAULT TERMS ═══
    dx_seed_terms();
}

function dx_seed_terms() {
    // Only run once
    if (get_option('dx_terms_seeded')) return;

    $categories = [
        'Golf', 'Clinics & Aesthetics', 'Dental', 'Business Setup',
        'Real Estate & Property', 'Luxury Services', 'Restaurants & Hospitality',
        'Reformer Pilates & Fitness', 'Tailors', 'Car Dealerships & Rentals',
        'Interior Design', 'Irish & UK Businesses',
    ];

    $districts = [
        'Dubai Marina', 'JBR', 'Downtown Dubai', 'DIFC', 'Business Bay',
        'JLT', 'Palm Jumeirah', 'Al Quoz', 'Dubai Hills', 'Arabian Ranches',
        'Sports City', 'Motor City', 'Deira', 'Bur Dubai',
    ];

    $ttd_types = [
        'Weekend Picks', 'New Openings', 'Seasonal', 'Events',
        'Family', 'Date Night', 'Free / Budget', 'Nightlife', 'Outdoor',
    ];

    foreach ($categories as $cat) {
        if (!term_exists($cat, 'business-category')) {
            wp_insert_term($cat, 'business-category');
        }
    }

    foreach ($districts as $dist) {
        if (!term_exists($dist, 'dubai-district')) {
            wp_insert_term($dist, 'dubai-district');
        }
    }

    foreach ($ttd_types as $type) {
        if (!term_exists($type, 'things-to-do-type')) {
            wp_insert_term($type, 'things-to-do-type');
        }
    }

    update_option('dx_terms_seeded', true);
}
