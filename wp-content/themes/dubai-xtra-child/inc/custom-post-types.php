<?php
/**
 * Dubai Xtra — Custom Post Types
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

add_action('init', 'dx_register_post_types');

function dx_register_post_types() {

    // ═══ 1. REVIEWS ═══
    register_post_type('review', [
        'labels' => [
            'name'          => 'Reviews',
            'singular_name' => 'Review',
            'add_new_item'  => 'Add New Review',
            'edit_item'     => 'Edit Review',
            'menu_name'     => 'Reviews',
        ],
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'review', 'with_front' => false],
        'menu_icon'    => 'dashicons-star-filled',
        'menu_position' => 5,
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
        'show_in_rest' => true,
    ]);

    // ═══ 2. BEST LISTS ═══
    register_post_type('best-list', [
        'labels' => [
            'name'          => 'Best Lists',
            'singular_name' => 'Best List',
            'add_new_item'  => 'Add New Best List',
            'edit_item'     => 'Edit Best List',
            'menu_name'     => 'Best Lists',
        ],
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'best', 'with_front' => false],
        'menu_icon'    => 'dashicons-awards',
        'menu_position' => 6,
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
        'show_in_rest' => true,
    ]);

    // ═══ 3. OWNER SPOTLIGHTS ═══
    register_post_type('owner-spotlight', [
        'labels' => [
            'name'          => 'Owner Spotlights',
            'singular_name' => 'Owner Spotlight',
            'add_new_item'  => 'Add New Owner Spotlight',
            'edit_item'     => 'Edit Owner Spotlight',
            'menu_name'     => 'Owner Spotlights',
        ],
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'owner-spotlight', 'with_front' => false],
        'menu_icon'    => 'dashicons-businessperson',
        'menu_position' => 7,
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
        'show_in_rest' => true,
    ]);

    // ═══ 4. BUSINESS SPOTLIGHTS ═══
    register_post_type('business-spotlight', [
        'labels' => [
            'name'          => 'Business Spotlights',
            'singular_name' => 'Business Spotlight',
            'add_new_item'  => 'Add New Business Spotlight',
            'edit_item'     => 'Edit Business Spotlight',
            'menu_name'     => 'Business Spotlights',
        ],
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'business-spotlight', 'with_front' => false],
        'menu_icon'    => 'dashicons-building',
        'menu_position' => 8,
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
        'show_in_rest' => true,
    ]);

    // ═══ 5. THINGS TO DO ═══
    register_post_type('things-to-do', [
        'labels' => [
            'name'          => 'Things To Do',
            'singular_name' => 'Things To Do',
            'add_new_item'  => 'Add New Things To Do',
            'edit_item'     => 'Edit Things To Do',
            'menu_name'     => 'Things To Do',
        ],
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'things-to-do', 'with_front' => false],
        'menu_icon'    => 'dashicons-calendar-alt',
        'menu_position' => 9,
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
        'show_in_rest' => true,
    ]);

    // ═══ 6. GUIDES ═══
    register_post_type('guide', [
        'labels' => [
            'name'          => 'Guides',
            'singular_name' => 'Guide',
            'add_new_item'  => 'Add New Guide',
            'edit_item'     => 'Edit Guide',
            'menu_name'     => 'Guides',
        ],
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'guide', 'with_front' => false],
        'menu_icon'    => 'dashicons-book',
        'menu_position' => 10,
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions'],
        'show_in_rest' => true,
    ]);

    // ═══ 7. DIRECTORY LISTINGS ═══
    register_post_type('listing', [
        'labels' => [
            'name'          => 'Directory Listings',
            'singular_name' => 'Listing',
            'add_new_item'  => 'Add New Listing',
            'edit_item'     => 'Edit Listing',
            'menu_name'     => 'Directory',
        ],
        'public'       => true,
        'has_archive'  => true,
        'rewrite'      => ['slug' => 'directory', 'with_front' => false],
        'menu_icon'    => 'dashicons-location',
        'menu_position' => 11,
        'supports'     => ['title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'comments'],
        'show_in_rest' => true,
    ]);
}
