<?php
/**
 * Plugin Name: Dubai Xtra Instagram Links
 * Description: Accept Instagram handles or profile URLs and store full profile links on listings.
 * Version: 1.0.0
 */
if (!defined('ABSPATH')) { exit; }

/** Return a canonical profile URL, or an empty string for invalid input. */
function dx_instagram_profile_url($value) {
    if (!is_string($value)) { return ''; }
    $value = trim($value);
    if ($value === '') { return ''; }
    if (preg_match('~^(?:(?:https?:)?//)?(?:www\.|m\.)?instagram\.com/~i', $value)) {
        $value = preg_replace('~^(?:(?:https?:)?//)?(?:www\.|m\.)?instagram\.com/~i', '', $value);
        $value = preg_split('/[?#]/', $value)[0];
        $value = rtrim($value, '/');
    }
    $handle = ltrim($value, '@');
    if (!preg_match('/^[A-Za-z0-9_](?:[A-Za-z0-9_.]{0,28}[A-Za-z0-9_])?$/D', $handle)
        || strpos($handle, '..') !== false
        || in_array(strtolower($handle), ['p', 'reel', 'reels', 'stories', 'explore', 'accounts', 'direct'], true)) {
        return '';
    }
    return 'https://www.instagram.com/' . $handle . '/';
}

function dx_instagram_prepare_field($field) {
    // A URL-only browser field rejects @handles before ACF can normalize them.
    $field['type'] = 'text';
    $field['placeholder'] = '@yourhandle or https://www.instagram.com/yourhandle/';
    $field['instructions'] = 'Enter a handle or Instagram profile URL. It is saved as a full profile link.';
    return $field;
}

function dx_instagram_validate_field($valid, $value) {
    if ($valid !== true) { return $valid; }
    if (is_string($value) && trim($value) === '') { return true; }
    return dx_instagram_profile_url($value) !== '' ? true : 'Enter a valid Instagram handle or profile URL.';
}

function dx_instagram_store_field($value) {
    $url = dx_instagram_profile_url($value);
    // Preserve invalid legacy data for correction rather than silently deleting it.
    return $url !== '' ? $url : $value;
}

foreach (['field_listing_instagram', 'field_bs_instagram'] as $key) {
    add_filter('acf/prepare_field/key=' . $key, 'dx_instagram_prepare_field');
    add_filter('acf/validate_value/key=' . $key, 'dx_instagram_validate_field', 20, 2);
    add_filter('acf/update_value/key=' . $key, 'dx_instagram_store_field');
    add_filter('acf/load_value/key=' . $key, 'dx_instagram_store_field');
    add_filter('acf/format_value/key=' . $key, 'dx_instagram_profile_url');
}

// The public form is a text field; normalize before the theme builds its draft and email.
function dx_instagram_normalize_submission() {
    if (isset($_POST['instagram']) && is_string($_POST['instagram'])) {
        $value = wp_unslash($_POST['instagram']);
        $url = dx_instagram_profile_url($value);
        if ($url !== '') { $_POST['instagram'] = wp_slash($url); }
    }
}
add_action('admin_post_dx_submit_listing', 'dx_instagram_normalize_submission', 1);
add_action('admin_post_nopriv_dx_submit_listing', 'dx_instagram_normalize_submission', 1);
