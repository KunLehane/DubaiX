<?php
/**
 * Dubai Xtra — ACF Field Groups
 * Registers all field groups programmatically.
 * Requires ACF Pro to be active.
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

if (!function_exists('acf_add_local_field_group')) return;

add_action('acf/init', 'dx_register_acf_fields');

function dx_register_acf_fields() {

    // ═══ REVIEW FIELDS ═══
    acf_add_local_field_group([
        'key'      => 'group_dx_review',
        'title'    => 'Review Details',
        'show_in_rest' => 1,
        'location' => [[[
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => 'review',
        ]]],
        'position' => 'normal',
        'fields'   => [
            ['key' => 'field_review_business_name', 'label' => 'Business Name', 'name' => 'business_name', 'type' => 'text', 'required' => 1],
            ['key' => 'field_review_business_website', 'label' => 'Business Website', 'name' => 'business_website', 'type' => 'url'],
            ['key' => 'field_review_business_location', 'label' => 'Business Location', 'name' => 'business_location', 'type' => 'text'],
            ['key' => 'field_review_google_maps', 'label' => 'Google Maps Embed', 'name' => 'google_maps_embed', 'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_review_score', 'label' => 'Review Score', 'name' => 'review_score', 'type' => 'number', 'min' => 1, 'max' => 10, 'step' => 0.1, 'instructions' => 'Score 1-10. Only publish 7+.'],
            ['key' => 'field_review_summary', 'label' => 'Review Summary', 'name' => 'review_summary', 'type' => 'textarea', 'rows' => 3, 'instructions' => '2-3 sentence verdict'],
            [
                'key' => 'field_review_sections', 'label' => 'Review Sections', 'name' => 'review_sections', 'type' => 'repeater',
                'layout' => 'block', 'button_label' => 'Add Section',
                'sub_fields' => [
                    ['key' => 'field_rs_title', 'label' => 'Section Title', 'name' => 'section_title', 'type' => 'text'],
                    ['key' => 'field_rs_content', 'label' => 'Section Content', 'name' => 'section_content', 'type' => 'wysiwyg', 'media_upload' => 1],
                    ['key' => 'field_rs_images', 'label' => 'Section Images', 'name' => 'section_images', 'type' => 'gallery', 'return_format' => 'array'],
                ],
            ],
            [
                'key' => 'field_review_pros', 'label' => 'Highlights', 'name' => 'review_pros', 'type' => 'repeater',
                'layout' => 'table', 'button_label' => 'Add Highlight',
                'sub_fields' => [
                    ['key' => 'field_rp_text', 'label' => 'Highlight', 'name' => 'text', 'type' => 'text'],
                ],
            ],
            ['key' => 'field_review_price_range', 'label' => 'Price Range', 'name' => 'price_range', 'type' => 'select', 'choices' => ['$' => '$', '$$' => '$$', '$$$' => '$$$', '$$$$' => '$$$$']],
            ['key' => 'field_review_phone', 'label' => 'Phone', 'name' => 'contact_phone', 'type' => 'text'],
            ['key' => 'field_review_email', 'label' => 'Email', 'name' => 'contact_email', 'type' => 'email'],
            ['key' => 'field_review_instagram', 'label' => 'Instagram', 'name' => 'contact_instagram', 'type' => 'text'],
            ['key' => 'field_review_featured', 'label' => 'Featured?', 'name' => 'is_featured', 'type' => 'true_false', 'default_value' => 0],
            ['key' => 'field_review_feature_type', 'label' => 'Feature Type', 'name' => 'feature_type', 'type' => 'select', 'choices' => ['Editorial' => 'Editorial', 'Sponsored Feature' => 'Sponsored Feature', 'Partner' => 'Partner'], 'default_value' => 'Editorial'],
        ],
    ]);

    // ═══ BEST LIST FIELDS ═══
    acf_add_local_field_group([
        'key'      => 'group_dx_best_list',
        'title'    => 'Best List Details',
        'show_in_rest' => 1,
        'location' => [[[
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => 'best-list',
        ]]],
        'fields' => [
            ['key' => 'field_bl_intro', 'label' => 'List Introduction', 'name' => 'list_intro', 'type' => 'wysiwyg'],
            ['key' => 'field_bl_methodology', 'label' => 'How We Chose These', 'name' => 'list_methodology', 'type' => 'textarea', 'rows' => 3],
            [
                'key' => 'field_bl_items', 'label' => 'List Items', 'name' => 'list_items', 'type' => 'repeater',
                'layout' => 'block', 'button_label' => 'Add Business',
                'sub_fields' => [
                    ['key' => 'field_bli_name', 'label' => 'Business Name', 'name' => 'business_name', 'type' => 'text', 'required' => 1],
                    ['key' => 'field_bli_link', 'label' => 'Link to Review/Listing', 'name' => 'business_link', 'type' => 'url'],
                    ['key' => 'field_bli_desc', 'label' => 'Short Description', 'name' => 'short_description', 'type' => 'textarea', 'rows' => 3],
                    ['key' => 'field_bli_image', 'label' => 'Featured Image', 'name' => 'featured_image', 'type' => 'image', 'return_format' => 'array'],
                    ['key' => 'field_bli_location', 'label' => 'Location', 'name' => 'location', 'type' => 'text'],
                    ['key' => 'field_bli_price', 'label' => 'Price Range', 'name' => 'price_range', 'type' => 'select', 'choices' => ['$' => '$', '$$' => '$$', '$$$' => '$$$', '$$$$' => '$$$$']],
                    ['key' => 'field_bli_highlight', 'label' => 'Highlight', 'name' => 'highlight', 'type' => 'text', 'instructions' => 'e.g. "Best for: sunset views"'],
                ],
            ],
        ],
    ]);

    // ═══ OWNER SPOTLIGHT FIELDS ═══
    acf_add_local_field_group([
        'key'      => 'group_dx_owner_spotlight',
        'title'    => 'Owner Spotlight Details',
        'show_in_rest' => 1,
        'location' => [[[
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => 'owner-spotlight',
        ]]],
        'fields' => [
            ['key' => 'field_os_owner_name', 'label' => 'Owner Name', 'name' => 'owner_name', 'type' => 'text', 'required' => 1],
            ['key' => 'field_os_business_name', 'label' => 'Business Name', 'name' => 'business_name', 'type' => 'text', 'required' => 1],
            ['key' => 'field_os_owner_photo', 'label' => 'Owner Photo', 'name' => 'owner_photo', 'type' => 'image', 'return_format' => 'array'],
            ['key' => 'field_os_intro', 'label' => 'Spotlight Intro', 'name' => 'spotlight_intro', 'type' => 'wysiwyg'],
            [
                'key' => 'field_os_qa', 'label' => 'Q&A Sections', 'name' => 'qa_sections', 'type' => 'repeater',
                'layout' => 'block', 'button_label' => 'Add Question',
                'sub_fields' => [
                    ['key' => 'field_osqa_q', 'label' => 'Question', 'name' => 'question', 'type' => 'text'],
                    ['key' => 'field_osqa_a', 'label' => 'Answer', 'name' => 'answer', 'type' => 'wysiwyg'],
                ],
            ],
            ['key' => 'field_os_pull_quote', 'label' => 'Pull Quote', 'name' => 'pull_quote', 'type' => 'text', 'instructions' => 'Highlighted quote for social'],
            ['key' => 'field_os_youtube', 'label' => 'YouTube Embed URL', 'name' => 'youtube_embed', 'type' => 'url'],
            ['key' => 'field_os_website', 'label' => 'Business Website', 'name' => 'website', 'type' => 'url'],
            ['key' => 'field_os_instagram', 'label' => 'Instagram Handle', 'name' => 'instagram_handle', 'type' => 'text'],
            ['key' => 'field_os_takeaway', 'label' => 'Key Takeaway', 'name' => 'key_takeaway', 'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_os_featured', 'label' => 'Featured?', 'name' => 'is_featured', 'type' => 'true_false'],
            ['key' => 'field_os_feature_type', 'label' => 'Feature Type', 'name' => 'feature_type', 'type' => 'select', 'choices' => ['Editorial' => 'Editorial', 'Sponsored Feature' => 'Sponsored Feature', 'Partner' => 'Partner']],
        ],
    ]);

    // ═══ BUSINESS SPOTLIGHT FIELDS ═══
    acf_add_local_field_group([
        'key'      => 'group_dx_business_spotlight',
        'title'    => 'Business Spotlight Details',
        'show_in_rest' => 1,
        'location' => [[[
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => 'business-spotlight',
        ]]],
        'fields' => [
            ['key' => 'field_bs_business_name', 'label' => 'Business Name', 'name' => 'business_name', 'type' => 'text', 'required' => 1],
            ['key' => 'field_bs_tagline', 'label' => 'Tagline', 'name' => 'business_tagline', 'type' => 'text', 'instructions' => 'One-line description'],
            ['key' => 'field_bs_logo', 'label' => 'Business Logo', 'name' => 'business_logo', 'type' => 'image', 'return_format' => 'array'],
            ['key' => 'field_bs_gallery', 'label' => 'Gallery', 'name' => 'gallery', 'type' => 'gallery', 'return_format' => 'array'],
            [
                'key' => 'field_bs_sections', 'label' => 'Spotlight Sections', 'name' => 'spotlight_sections', 'type' => 'repeater',
                'layout' => 'block', 'button_label' => 'Add Section',
                'sub_fields' => [
                    ['key' => 'field_bss_title', 'label' => 'Section Title', 'name' => 'section_title', 'type' => 'text'],
                    ['key' => 'field_bss_content', 'label' => 'Section Content', 'name' => 'section_content', 'type' => 'wysiwyg'],
                    ['key' => 'field_bss_image', 'label' => 'Section Image', 'name' => 'section_image', 'type' => 'image', 'return_format' => 'array'],
                ],
            ],
            [
                'key' => 'field_bs_stats', 'label' => 'Key Stats', 'name' => 'key_stats', 'type' => 'repeater',
                'layout' => 'table', 'button_label' => 'Add Stat',
                'sub_fields' => [
                    ['key' => 'field_bsst_label', 'label' => 'Label', 'name' => 'stat_label', 'type' => 'text'],
                    ['key' => 'field_bsst_value', 'label' => 'Value', 'name' => 'stat_value', 'type' => 'text'],
                ],
            ],
            ['key' => 'field_bs_standout', 'label' => 'Standout Feature', 'name' => 'standout_feature', 'type' => 'textarea', 'rows' => 3, 'instructions' => 'The one thing that makes them different'],
            ['key' => 'field_bs_website', 'label' => 'Website', 'name' => 'website', 'type' => 'url'],
            ['key' => 'field_bs_instagram', 'label' => 'Instagram', 'name' => 'instagram', 'type' => 'url'],
            ['key' => 'field_bs_phone', 'label' => 'Phone', 'name' => 'phone', 'type' => 'text'],
            ['key' => 'field_bs_email', 'label' => 'Email', 'name' => 'email', 'type' => 'email'],
            ['key' => 'field_bs_maps', 'label' => 'Google Maps Embed', 'name' => 'google_maps_embed', 'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_bs_related_review', 'label' => 'Related Review', 'name' => 'related_review', 'type' => 'relationship', 'post_type' => ['review'], 'max' => 1],
            ['key' => 'field_bs_related_listing', 'label' => 'Related Listing', 'name' => 'related_listing', 'type' => 'relationship', 'post_type' => ['listing'], 'max' => 1],
            ['key' => 'field_bs_featured', 'label' => 'Featured?', 'name' => 'is_featured', 'type' => 'true_false'],
            ['key' => 'field_bs_feature_type', 'label' => 'Feature Type', 'name' => 'feature_type', 'type' => 'select', 'choices' => ['Editorial' => 'Editorial', 'Sponsored Feature' => 'Sponsored Feature', 'Partner' => 'Partner']],
        ],
    ]);

    // ═══ THINGS TO DO FIELDS ═══
    acf_add_local_field_group([
        'key'      => 'group_dx_things_to_do',
        'title'    => 'Things To Do Details',
        'show_in_rest' => 1,
        'location' => [[[
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => 'things-to-do',
        ]]],
        'fields' => [
            ['key' => 'field_ttd_time_relevance', 'label' => 'Time Relevance', 'name' => 'time_relevance', 'type' => 'select', 'choices' => ['This Weekend' => 'This Weekend', 'This Week' => 'This Week', 'This Month' => 'This Month', 'This Season' => 'This Season', 'Ongoing' => 'Ongoing']],
            ['key' => 'field_ttd_valid_from', 'label' => 'Valid From', 'name' => 'valid_from', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d'],
            ['key' => 'field_ttd_valid_until', 'label' => 'Valid Until', 'name' => 'valid_until', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d'],
            ['key' => 'field_ttd_intro', 'label' => 'Introduction', 'name' => 'things_intro', 'type' => 'wysiwyg'],
            ['key' => 'field_ttd_editors_pick', 'label' => "Editor's Pick?", 'name' => 'is_editors_pick', 'type' => 'true_false'],
            [
                'key' => 'field_ttd_items', 'label' => 'Things To Do Items', 'name' => 'things_items', 'type' => 'repeater',
                'layout' => 'block', 'button_label' => 'Add Item',
                'sub_fields' => [
                    ['key' => 'field_ttdi_title', 'label' => 'Title', 'name' => 'item_title', 'type' => 'text', 'required' => 1],
                    ['key' => 'field_ttdi_desc', 'label' => 'Description', 'name' => 'item_description', 'type' => 'wysiwyg'],
                    ['key' => 'field_ttdi_image', 'label' => 'Image', 'name' => 'item_image', 'type' => 'image', 'return_format' => 'array'],
                    ['key' => 'field_ttdi_location', 'label' => 'Location', 'name' => 'item_location', 'type' => 'text'],
                    ['key' => 'field_ttdi_price', 'label' => 'Price', 'name' => 'item_price', 'type' => 'text', 'instructions' => 'e.g. Free, AED 150, AED 300-500'],
                    ['key' => 'field_ttdi_datetime', 'label' => 'Date/Time', 'name' => 'item_date_time', 'type' => 'text', 'instructions' => 'e.g. Every Friday, 12pm-4pm'],
                    ['key' => 'field_ttdi_booking', 'label' => 'Booking Link', 'name' => 'item_booking_link', 'type' => 'url'],
                    ['key' => 'field_ttdi_listing', 'label' => 'Related Listing', 'name' => 'related_listing', 'type' => 'relationship', 'post_type' => ['listing'], 'max' => 1],
                    ['key' => 'field_ttdi_review', 'label' => 'Related Review', 'name' => 'related_review', 'type' => 'relationship', 'post_type' => ['review'], 'max' => 1],
                ],
            ],
        ],
    ]);

    // ═══ GUIDE FIELDS ═══
    acf_add_local_field_group([
        'key'      => 'group_dx_guide',
        'title'    => 'Guide Details',
        'show_in_rest' => 1,
        'location' => [[[
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => 'guide',
        ]]],
        'fields' => [
            ['key' => 'field_guide_type', 'label' => 'Guide Type', 'name' => 'guide_type', 'type' => 'select', 'choices' => ['Area Guide' => 'Area Guide', 'Topic Guide' => 'Topic Guide', 'Newcomer Guide' => 'Newcomer Guide']],
            [
                'key' => 'field_guide_sections', 'label' => 'Guide Sections', 'name' => 'guide_sections', 'type' => 'repeater',
                'layout' => 'block', 'button_label' => 'Add Section',
                'sub_fields' => [
                    ['key' => 'field_gs_title', 'label' => 'Section Title', 'name' => 'section_title', 'type' => 'text'],
                    ['key' => 'field_gs_content', 'label' => 'Section Content', 'name' => 'section_content', 'type' => 'wysiwyg'],
                    ['key' => 'field_gs_image', 'label' => 'Section Image', 'name' => 'section_image', 'type' => 'image', 'return_format' => 'array'],
                ],
            ],
            ['key' => 'field_guide_listings', 'label' => 'Related Listings', 'name' => 'related_listings', 'type' => 'relationship', 'post_type' => ['listing']],
            ['key' => 'field_guide_reviews', 'label' => 'Related Reviews', 'name' => 'related_reviews', 'type' => 'relationship', 'post_type' => ['review']],
            ['key' => 'field_guide_ttd', 'label' => 'Related Things To Do', 'name' => 'related_things_to_do', 'type' => 'relationship', 'post_type' => ['things-to-do']],
            [
                'key' => 'field_guide_facts', 'label' => 'Key Facts', 'name' => 'key_facts', 'type' => 'repeater',
                'layout' => 'table', 'button_label' => 'Add Fact',
                'sub_fields' => [
                    ['key' => 'field_gf_label', 'label' => 'Label', 'name' => 'label', 'type' => 'text'],
                    ['key' => 'field_gf_value', 'label' => 'Value', 'name' => 'value', 'type' => 'text'],
                ],
            ],
        ],
    ]);

    // ═══ DIRECTORY LISTING FIELDS ═══
    acf_add_local_field_group([
        'key'      => 'group_dx_listing',
        'title'    => 'Listing Details',
        'show_in_rest' => 1,
        'location' => [[[
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => 'listing',
        ]]],
        'fields' => [
            ['key' => 'field_listing_business_name', 'label' => 'Business Name', 'name' => 'business_name', 'type' => 'text', 'required' => 1],
            ['key' => 'field_listing_description', 'label' => 'Business Description', 'name' => 'business_description', 'type' => 'wysiwyg'],
            ['key' => 'field_listing_logo', 'label' => 'Business Logo', 'name' => 'business_logo', 'type' => 'image', 'return_format' => 'array'],
            ['key' => 'field_listing_gallery', 'label' => 'Gallery', 'name' => 'gallery', 'type' => 'gallery', 'return_format' => 'array'],
            ['key' => 'field_listing_address', 'label' => 'Address', 'name' => 'address', 'type' => 'textarea', 'rows' => 2],
            ['key' => 'field_listing_maps', 'label' => 'Google Maps Embed', 'name' => 'google_maps_embed', 'type' => 'textarea', 'rows' => 3],
            ['key' => 'field_listing_phone', 'label' => 'Phone', 'name' => 'phone', 'type' => 'text'],
            ['key' => 'field_listing_email', 'label' => 'Email', 'name' => 'email', 'type' => 'email'],
            ['key' => 'field_listing_website', 'label' => 'Website', 'name' => 'website', 'type' => 'url'],
            ['key' => 'field_listing_instagram', 'label' => 'Instagram', 'name' => 'instagram', 'type' => 'url'],
            [
                'key' => 'field_listing_hours', 'label' => 'Opening Hours', 'name' => 'opening_hours', 'type' => 'repeater',
                'layout' => 'table', 'button_label' => 'Add Day',
                'sub_fields' => [
                    ['key' => 'field_lh_day', 'label' => 'Day', 'name' => 'day', 'type' => 'text'],
                    ['key' => 'field_lh_open', 'label' => 'Open', 'name' => 'open', 'type' => 'text'],
                    ['key' => 'field_lh_close', 'label' => 'Close', 'name' => 'close', 'type' => 'text'],
                ],
            ],
            ['key' => 'field_listing_price_range', 'label' => 'Price Range', 'name' => 'price_range', 'type' => 'select', 'choices' => ['$' => '$', '$$' => '$$', '$$$' => '$$$', '$$$$' => '$$$$']],
            ['key' => 'field_listing_tier', 'label' => 'Listing Tier', 'name' => 'listing_tier', 'type' => 'select', 'choices' => ['Free' => 'Free', 'Premium' => 'Premium (AED 800/yr)'], 'default_value' => 'Free'],
            ['key' => 'field_listing_verified', 'label' => 'Verified?', 'name' => 'is_verified', 'type' => 'true_false'],
            ['key' => 'field_listing_featured', 'label' => 'Featured?', 'name' => 'is_featured', 'type' => 'true_false'],
            ['key' => 'field_listing_expiry', 'label' => 'Listing Expiry', 'name' => 'listing_expiry', 'type' => 'date_picker', 'display_format' => 'd/m/Y', 'return_format' => 'Y-m-d'],
        ],
    ]);
}
