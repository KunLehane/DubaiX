<?php
/**
 * Dubai Xtra - One-shot Content Seeder
 *
 * Creates 10 demo posts (1 review, 1 best-list, 1 owner-spotlight,
 * 1 business-spotlight, 1 things-to-do, 1 guide, 3 directory listings)
 * so all templates can be previewed against realistic data.
 *
 * Triggered manually via the admin notice. Sets the `dx_content_seeded`
 * option after a successful run so the prompt does not reappear.
 *
 * To re-run: delete the `dx_content_seeded` option from wp_options
 * (or run: wp option delete dx_content_seeded).
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

/**
 * Show admin notice with the seed trigger when content has not been seeded.
 */
add_action('admin_notices', 'dx_seed_admin_notice');
function dx_seed_admin_notice() {
    if (!current_user_can('manage_options')) return;

    if (isset($_GET['dx_seeded']) && $_GET['dx_seeded'] === '1') {
        echo '<div class="notice notice-success is-dismissible"><p><strong>Dubai Xtra:</strong> Demo content seeded. Visit the homepage to see the templates in action.</p></div>';
        return;
    }
    if (isset($_GET['dx_seeded']) && $_GET['dx_seeded'] === 'partial') {
        echo '<div class="notice notice-warning is-dismissible"><p><strong>Dubai Xtra:</strong> Seeding completed with some failures. Check Posts/CPTs to see what landed.</p></div>';
        return;
    }

    if (!get_option('dx_content_seeded')) {
        $url = wp_nonce_url(add_query_arg('dx_seed', '1', admin_url('index.php')), 'dx_seed_content');
        echo '<div class="notice notice-info"><p><strong>Dubai Xtra:</strong> Demo content not yet seeded. ';
        echo '<a href="' . esc_url($url) . '" class="button button-primary" style="margin-left: 8px;">Seed Demo Content</a>';
        echo ' <span style="color: #777; font-size: 12px; margin-left: 8px;">(One-time. Creates 10 sample posts. Takes ~30 seconds while images download.)</span></p></div>';
    }
}

/**
 * Trigger handler. Verifies nonce, runs seeder, redirects with status.
 */
add_action('admin_init', 'dx_maybe_seed_content');
function dx_maybe_seed_content() {
    if (!isset($_GET['dx_seed']) || $_GET['dx_seed'] !== '1') return;
    if (!current_user_can('manage_options')) return;
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce($_GET['_wpnonce'], 'dx_seed_content')) return;
    if (get_option('dx_content_seeded')) {
        wp_safe_redirect(admin_url('index.php'));
        exit;
    }
    if (!function_exists('update_field')) {
        wp_die('ACF Pro is required for the content seeder. Activate ACF and try again.');
    }

    @set_time_limit(180);
    @ini_set('memory_limit', '256M');

    $result = dx_seed_content();
    update_option('dx_content_seeded', current_time('mysql'));

    $status = $result['failures'] > 0 ? 'partial' : '1';
    wp_safe_redirect(admin_url('index.php?dx_seeded=' . $status));
    exit;
}

/**
 * Seed all 10 demo posts. Returns an array with counts.
 */
function dx_seed_content() {
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $created  = 0;
    $failures = 0;

    /**
     * Sideload an image from a URL. Returns attachment ID or 0 on failure.
     * Wrapped so a network blip never aborts the whole seed run.
     */
    $sideload = function ($url, $title) use (&$failures) {
        if (!$url) return 0;
        $tmp = download_url($url, 20);
        if (is_wp_error($tmp)) { $failures++; return 0; }
        $name = sanitize_file_name(basename(parse_url($url, PHP_URL_PATH)) ?: $title . '.jpg');
        if (!preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $name)) $name .= '.jpg';
        $file_array = ['name' => $name, 'tmp_name' => $tmp];
        $id = media_handle_sideload($file_array, 0, $title);
        if (is_wp_error($id)) {
            @unlink($tmp);
            $failures++;
            return 0;
        }
        return (int) $id;
    };

    /**
     * Upsert a post by slug + post_type. Returns post ID. Skips creation
     * if a post with the same slug already exists in that CPT.
     */
    $upsert_post = function ($slug, $args) use (&$created) {
        $existing = get_posts([
            'post_type'      => $args['post_type'],
            'name'           => $slug,
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ]);
        if (!empty($existing)) return (int) $existing[0];

        $args['post_name']   = $slug;
        $args['post_status'] = 'publish';
        $id = wp_insert_post($args, true);
        if (is_wp_error($id)) return 0;
        $created++;
        return (int) $id;
    };

    /**
     * Ensure a taxonomy term exists. Returns term_id.
     */
    $ensure_term = function ($name, $taxonomy) {
        $term = term_exists($name, $taxonomy);
        if ($term) return is_array($term) ? (int) $term['term_id'] : (int) $term;
        $created = wp_insert_term($name, $taxonomy);
        return is_wp_error($created) ? 0 : (int) $created['term_id'];
    };

    // ═══ 1. REVIEW: Emirates Golf Club ═══
    $review_id = $upsert_post('emirates-golf-club-the-majlis', [
        'post_title'   => 'Emirates Golf Club - The Majlis Course',
        'post_type'    => 'review',
        'post_excerpt' => "The desert's most storied 18 holes still sets the standard for Dubai golf.",
    ]);
    if ($review_id) {
        $hero_img = $sideload('https://picsum.photos/seed/emirates-golf-majlis/1600/900', 'Emirates Golf Club Majlis Course');
        if ($hero_img) set_post_thumbnail($review_id, $hero_img);

        update_field('business_name',     'Emirates Golf Club',                                  $review_id);
        update_field('business_website',  'https://dubaigolf.com/emirates-golf-club',            $review_id);
        update_field('business_location', 'Sheikh Zayed Road, Emirates Hills',                   $review_id);
        update_field('review_score',      9.2,                                                   $review_id);
        update_field('review_summary',    "The desert's most storied 18 holes still sets the standard for Dubai golf. The Majlis is the course every visiting player asks about, and 35 years on it earns the attention.", $review_id);
        update_field('review_sections', [
            [
                'section_title'   => 'The Experience',
                'section_content' => '<p>The drive in past the dhow-shaped clubhouse remains one of the great pre-round arrivals in golf. The Majlis itself is mature in a way few Dubai courses are: the bermuda is dense, the bunkering is genuinely strategic, and the par-3 7th is the kind of hole you remember on the flight home.</p><p>What separates The Majlis from its newer neighbours is restraint. The routing trusts the landscape rather than fighting it, and the green complex attention is obvious in the flat-stick work. You play the course; you do not just survive it.</p>',
            ],
            [
                'section_title'   => 'Value for Money',
                'section_content' => '<p>Green fees during high season run AED 1,295 for a non-member 18 - not cheap, but priced fairly against international peers. The buggy, range balls, and an arrival drink are all included. Twilight rates from 2pm bring it down to AED 695, which is the smarter move if you are not chasing a sunrise tee time.</p>',
            ],
            [
                'section_title'   => "Who It's For",
                'section_content' => '<p>Anyone serious about their golf, and anyone who wants the Dubai golf experience done properly. Beginners will find it punishing in the desert, so book a lesson on the academy course first. The European Tour players who win the DP World Tour Championship here every November tell you everything you need to know about the standard.</p>',
            ],
        ], $review_id);
        update_field('review_pros', [
            ['text' => 'Mature bermuda fairways and properly fast greens'],
            ['text' => 'Iconic clubhouse and arrival experience'],
            ['text' => 'Twilight rates make weekday rounds genuinely good value'],
            ['text' => 'Caddies who know the lines on every hole'],
        ], $review_id);
        update_field('price_range',       '$$$$',              $review_id);
        update_field('contact_phone',     '+971 4 380 2222',   $review_id);
        update_field('contact_email',     'info@emiratesgolfclub.ae', $review_id);
        update_field('contact_instagram', 'emiratesgolfclub',  $review_id);
        update_field('is_featured',       1,                   $review_id);
        update_field('feature_type',      'Editorial',         $review_id);

        wp_set_post_terms($review_id, [$ensure_term('Golf', 'business-category')],         'business-category');
        wp_set_post_terms($review_id, [$ensure_term('Emirates Hills', 'dubai-district')], 'dubai-district');
    }

    // ═══ 2. BEST LIST: Best Golf Courses in Dubai ═══
    $best_id = $upsert_post('best-golf-courses-dubai', [
        'post_title'   => 'Best Golf Courses in Dubai',
        'post_type'    => 'best-list',
        'post_excerpt' => 'Five courses worth the green fee, ranked from best to start-here.',
    ]);
    if ($best_id) {
        $hero_img = $sideload('https://picsum.photos/seed/dubai-golf-courses-list/1600/900', 'Best Golf Courses in Dubai');
        if ($hero_img) set_post_thumbnail($best_id, $hero_img);

        update_field('list_intro', '<p>Dubai has more golf than any other city in the Gulf and most of the courses are excellent. These are the five we send visitors to first, in the order we would play them ourselves.</p>', $best_id);
        update_field('list_methodology', 'We played each course at full price as anonymous visitors, scored on conditioning, design, value, and atmosphere, and only included courses we would book again.', $best_id);

        $list_items = [
            [
                'business_name'     => 'Emirates Golf Club - Majlis',
                'business_link'     => $review_id ? get_permalink($review_id) : '',
                'short_description' => "The grand-old-man of Dubai golf. Mature bermuda, strategic bunkering, and the dhow-shaped clubhouse to round it off.",
                'location'          => 'Emirates Hills',
                'price_range'       => '$$$$',
                'highlight'         => 'Iconic course, properly fast greens',
            ],
            [
                'business_name'     => 'Jumeirah Golf Estates - Earth',
                'business_link'     => '',
                'short_description' => "Greg Norman's tournament course and home of the DP World Tour Championship. Long, strategic, and best played in winter.",
                'location'          => 'Jumeirah Golf Estates',
                'price_range'       => '$$$$',
                'highlight'         => 'Tournament-grade conditioning',
            ],
            [
                'business_name'     => 'Dubai Hills Golf Club',
                'business_link'     => '',
                'short_description' => 'A park-style design winding through the Dubai Hills development. Gentle on the eye, harder than it looks.',
                'location'          => 'Dubai Hills',
                'price_range'       => '$$$',
                'highlight'         => 'Best for visitors who want a real round in a beautiful setting',
            ],
            [
                'business_name'     => 'Trump International Golf Club',
                'business_link'     => '',
                'short_description' => "Gil Hanse design at DAMAC Hills. The most demanding course on this list and the one members talk about most.",
                'location'          => 'DAMAC Hills',
                'price_range'       => '$$$$',
                'highlight'         => 'Best for low handicaps who want to be tested',
            ],
            [
                'business_name'     => 'Arabian Ranches Golf Club',
                'business_link'     => '',
                'short_description' => 'A heath-style desert course with bermuda fairways and rugged native areas. Underrated and well priced.',
                'location'          => 'Arabian Ranches',
                'price_range'       => '$$$',
                'highlight'         => 'Best value for money in this list',
            ],
        ];
        foreach ($list_items as $i => &$item) {
            $img_id = $sideload('https://picsum.photos/seed/golf-course-' . $i . '/800/600', $item['business_name']);
            if ($img_id) $item['featured_image'] = $img_id;
        }
        unset($item);
        update_field('list_items', $list_items, $best_id);

        wp_set_post_terms($best_id, [$ensure_term('Golf', 'business-category')], 'business-category');
    }

    // ═══ 3. OWNER SPOTLIGHT: Jude Hobbs ═══
    $owner_id = $upsert_post('jude-hobbs-putting-coach', [
        'post_title'   => 'Jude Hobbs - Putting Coach',
        'post_type'    => 'owner-spotlight',
        'post_excerpt' => "The PGA pro turning Dubai's golfers into stat-driven putters.",
    ]);
    if ($owner_id) {
        $photo_id = $sideload('https://picsum.photos/seed/jude-hobbs-coach/1200/1500', 'Jude Hobbs');
        if ($photo_id) {
            update_field('owner_photo', $photo_id, $owner_id);
            set_post_thumbnail($owner_id, $photo_id);
        }

        update_field('owner_name',       'Jude Hobbs',                            $owner_id);
        update_field('business_name',    'Hobbs Putting Performance',             $owner_id);
        update_field('spotlight_intro',  '<p>Jude Hobbs has been a PGA professional for 14 years. He spent the first nine of them coaching full-swing in the UK, then moved to Dubai, ran some numbers on his lesson book, and quietly specialised in putting. He now coaches out of Address Montgomerie and a small studio in Business Bay, where his SAM PuttLab is rarely off.</p><p>He is the rare coach who will tell you to stop taking lessons if you are not going to put in the work, and the rare putting coach who has the data to back up what he asks you to change.</p>', $owner_id);

        update_field('qa_sections', [
            [
                'question' => 'How did you end up specialising in putting?',
                'answer'   => '<p>Honestly, I looked at my own scoring and realised I was leaking 4-5 shots a round on the greens. I assumed I was the exception, but when I started measuring my pupils with the PuttLab, almost everyone was the same. The full swing gets all the attention because it looks impressive, but putting is where rounds are actually decided.</p>',
            ],
            [
                'question' => 'What is the most common mistake you see?',
                'answer'   => '<p>Aim. Maybe 70% of the amateurs who come in are aiming offline at five feet. They are not missing the putts because of the stroke, they are missing because they are pointed at the wrong piece of grass. We can fix that in one session.</p>',
            ],
            [
                'question' => 'How long does it take to see real improvement?',
                'answer'   => '<p>If someone is willing to put 20 minutes a day on the practice green for six weeks, I would expect them to drop two shots a round. That is conservative. If they are willing to do drills - not just hit putts, do drills - that number goes up.</p>',
            ],
            [
                'question' => 'Where should a Dubai golfer practise their putting?',
                'answer'   => '<p>Address Montgomerie has the best practice green in the city, hands down. Emirates Golf Club is also excellent if you are a member or playing. The hotel courses tend to have flat greens that are not actually useful practice. Find a green with slope.</p>',
            ],
            [
                'question' => 'What is the lesson you wish more golfers booked?',
                'answer'   => '<p>The on-course playing lesson. People do range lessons, putting lessons, short-game lessons, but they never let me watch them on the actual course. That is where you see what they are really doing under pressure. It is the most useful 90 minutes I can spend with a pupil.</p>',
            ],
        ], $owner_id);

        update_field('pull_quote',       'Most golfers lose 5 shots a round on the green. I fix that.',                $owner_id);
        update_field('youtube_embed',    '',                                                                            $owner_id);
        update_field('instagram_handle', 'hobbsputtingdubai',                                                            $owner_id);
        update_field('key_takeaway',     'Putting is the most measurable, fixable, and ignored part of the amateur game. Find an hour a week to work on it and you will save more shots than another driver lesson ever will.', $owner_id);
        update_field('is_featured',      1,                                                                              $owner_id);
        update_field('feature_type',     'Editorial',                                                                    $owner_id);

        wp_set_post_terms($owner_id, [$ensure_term('Golf', 'business-category')],         'business-category');
        wp_set_post_terms($owner_id, [$ensure_term('Business Bay', 'dubai-district')],   'dubai-district');
    }

    // ═══ 4. BUSINESS SPOTLIGHT: Grafton Tailors ═══
    $biz_id = $upsert_post('grafton-tailors-dubai', [
        'post_title'   => 'Grafton Tailors Dubai',
        'post_type'    => 'business-spotlight',
        'post_excerpt' => "The DIFC tailor that quietly dresses half of Dubai's law firms.",
    ]);
    if ($biz_id) {
        $hero_img = $sideload('https://picsum.photos/seed/grafton-tailors-shop/1600/900', 'Grafton Tailors');
        if ($hero_img) set_post_thumbnail($biz_id, $hero_img);

        $logo_id = $sideload('https://picsum.photos/seed/grafton-tailors-logo/400/400', 'Grafton Tailors logo');
        if ($logo_id) update_field('business_logo', $logo_id, $biz_id);

        $gallery_ids = [];
        for ($i = 1; $i <= 4; $i++) {
            $g_id = $sideload('https://picsum.photos/seed/grafton-gallery-' . $i . '/1200/800', 'Grafton ' . $i);
            if ($g_id) $gallery_ids[] = $g_id;
        }
        if ($gallery_ids) update_field('gallery', $gallery_ids, $biz_id);

        update_field('business_name',    'Grafton Tailors',                                                $biz_id);
        update_field('business_tagline', "Bespoke tailoring for Dubai's business community.",              $biz_id);

        $sections = [
            [
                'section_title'   => 'The House Style',
                'section_content' => '<p>Grafton\'s cut sits somewhere between Savile Row and Naples - a clean, slightly suppressed silhouette with a soft shoulder, designed to wear comfortably in the Dubai climate without losing its line. Most of the cloth is woven in Huddersfield or by Loro Piana, with a small selection of Drago and Holland & Sherry for the warm-weather fabrics.</p>',
            ],
            [
                'section_title'   => 'The Process',
                'section_content' => '<p>A first appointment is unhurried. The team will measure 24 points, take a posture analysis, and walk you through cloth options without rushing the conversation. The first fitting comes at four to six weeks, and most commissions deliver in eight to ten. Alterations on a finished suit are free for the lifetime of the garment.</p>',
            ],
            [
                'section_title'   => 'Who Wears Grafton',
                'section_content' => '<p>The client list skews to senior professionals - law firm partners, family office principals, banking and consulting MDs - who want a Dubai-made suit without the volume-tailor feel that dominates the market. Repeat business is high, and the average client commissions three to four pieces a year once they have a workable house pattern on file.</p>',
            ],
        ];
        foreach ($sections as $i => &$section) {
            $sec_img = $sideload('https://picsum.photos/seed/grafton-section-' . $i . '/1200/800', 'Grafton Section ' . $i);
            if ($sec_img) $section['section_image'] = $sec_img;
        }
        unset($section);
        update_field('spotlight_sections', $sections, $biz_id);

        update_field('key_stats', [
            ['stat_label' => 'Years in Dubai', 'stat_value' => '11'],
            ['stat_label' => 'Master Tailors', 'stat_value' => '4'],
            ['stat_label' => 'Starting Suit',  'stat_value' => 'AED 6,500'],
        ], $biz_id);
        update_field('standout_feature', 'A bespoke house pattern stored on file means second and third commissions skip the fitting cycle entirely - a rare service in Dubai and the reason most regulars stay regulars.', $biz_id);
        update_field('website',     'https://graftontailors.ae',               $biz_id);
        update_field('instagram',   'https://instagram.com/graftontailors',    $biz_id);
        update_field('phone',       '+971 4 555 0123',                          $biz_id);
        update_field('email',       'enquiries@graftontailors.ae',              $biz_id);
        update_field('is_featured', 1,                                          $biz_id);
        update_field('feature_type','Editorial',                                $biz_id);

        wp_set_post_terms($biz_id, [$ensure_term('Tailors', 'business-category')], 'business-category');
        wp_set_post_terms($biz_id, [$ensure_term('DIFC', 'dubai-district')],       'dubai-district');
    }

    // ═══ 5. THINGS TO DO: This Weekend May 2026 ═══
    $ttd_id = $upsert_post('weekend-dubai-may-2026', [
        'post_title'   => 'This Weekend in Dubai - May 2026',
        'post_type'    => 'things-to-do',
        'post_excerpt' => 'Four things worth getting off the sofa for this weekend.',
    ]);
    if ($ttd_id) {
        $hero_img = $sideload('https://picsum.photos/seed/dubai-weekend-may-2026/1600/900', 'Dubai Weekend May 2026');
        if ($hero_img) set_post_thumbnail($ttd_id, $hero_img);

        update_field('time_relevance', 'This Weekend',          $ttd_id);
        update_field('valid_from',     '2026-05-01',            $ttd_id);
        update_field('valid_until',    '2026-12-31',            $ttd_id); // Long validity so it stays visible during demo
        update_field('things_intro',   '<p>Four picks for the weekend - one brunch, one new opening, one outdoor market, and one for the golf calendar. All are bookable in under five minutes from the links below.</p>', $ttd_id);
        update_field('is_editors_pick', 1,                       $ttd_id);

        $items = [
            [
                'item_title'        => 'Saturday Brunch at COYA Dubai',
                'item_description'  => '<p>The Peruvian brunch at COYA Four Seasons remains one of the best of its kind in the city. Live cumbia, ceviche made to order, and a Pisco list that is genuinely good rather than gimmicky. Book the pool terrace if it is available.</p>',
                'item_location'     => 'Four Seasons Resort, Jumeirah',
                'item_price'        => 'AED 695',
                'item_date_time'    => 'Saturday 1pm-4pm',
                'item_booking_link' => 'https://coyarestaurant.com/dubai',
            ],
            [
                'item_title'        => 'Friday at Emirates Golf Club',
                'item_description'  => '<p>The summer twilight rate kicks in at 2pm on the Majlis - AED 695 including buggy and range balls. The course is properly walkable in May before the heat lands, and you will be on the 18th green for sunset.</p>',
                'item_location'     => 'Emirates Hills',
                'item_price'        => 'AED 695',
                'item_date_time'    => 'Friday from 2pm',
                'item_booking_link' => 'https://dubaigolf.com',
            ],
            [
                'item_title'        => 'New Opening: Sazerac on the Marina',
                'item_description'  => '<p>A New Orleans-leaning whisky bar that opened this month at the Marina end of JBR. Solid bar food, a serious bourbon list, and live music on Friday and Saturday nights. Reservations recommended for a corner booth.</p>',
                'item_location'     => 'JBR Walk, The Beach',
                'item_price'        => 'AED 200-400',
                'item_date_time'    => 'Friday & Saturday from 6pm',
                'item_booking_link' => 'https://example.com/sazerac',
            ],
            [
                'item_title'        => 'Ripe Market at Academy Park',
                'item_description'  => '<p>The Saturday outdoor market at Dubai Police Academy Park - small-batch food producers, growers, and a decent coffee tent. Worth a Saturday morning before the day gets warm.</p>',
                'item_location'     => 'Dubai Police Academy Park, Umm Suqeim',
                'item_price'        => 'Free entry',
                'item_date_time'    => 'Saturday 9am-1pm',
                'item_booking_link' => 'https://ripeme.com',
            ],
        ];
        foreach ($items as $i => &$item) {
            $img_id = $sideload('https://picsum.photos/seed/ttd-may-2026-' . $i . '/800/600', $item['item_title']);
            if ($img_id) $item['item_image'] = $img_id;
        }
        unset($item);
        update_field('things_items', $items, $ttd_id);

        wp_set_post_terms($ttd_id, [$ensure_term('Weekend Picks', 'things-to-do-type')], 'things-to-do-type');
        wp_set_post_terms($ttd_id, [$ensure_term('Dubai Marina', 'dubai-district')],     'dubai-district');
    }

    // ═══ 6. GUIDE: Dubai Marina Complete Guide ═══
    $guide_id = $upsert_post('dubai-marina-complete-guide', [
        'post_title'   => 'Dubai Marina - The Complete Guide',
        'post_type'    => 'guide',
        'post_excerpt' => 'Where to eat, where to stay, what to skip, and how to get around the most famous waterfront in the city.',
    ]);
    if ($guide_id) {
        $hero_img = $sideload('https://picsum.photos/seed/dubai-marina-complete-guide/1600/900', 'Dubai Marina');
        if ($hero_img) set_post_thumbnail($guide_id, $hero_img);

        update_field('guide_type', 'Area Guide', $guide_id);

        $sections = [
            [
                'section_title'   => 'The Lay of the Land',
                'section_content' => '<p>Dubai Marina is a 3km man-made canal lined with high-rise residential and a public walkway (the Marina Walk) that runs almost the full length on both sides. The Walk is the centre of gravity - most of the dining, the bars, the boat charters, and the actually pleasant places to spend an evening sit on or just off it. North of the canal is JBR (The Walk) which has a beach. South is the Bluewaters island, which has the wheel and a small but well-edited dining mix.</p><p>The metro stops at both ends of the Marina (DMCC and Sobha Realty) and a tram runs through the middle. Driving is easy in winter, less easy on weekend evenings.</p>',
            ],
            [
                'section_title'   => 'Where to Eat',
                'section_content' => '<p>The Marina has more restaurants than any single Dubai district, and the average is mediocre. The exceptions worth booking: Pier 7 (the seven-restaurant tower at the Marina end - book Atelier M for the terrace, Asia Asia for the food); the W terrace at Bluewaters; the COYA Saturday brunch up the road at the Four Seasons. Skip everything on the JBR strip unless someone you trust has named a specific place.</p>',
            ],
            [
                'section_title'   => 'Where to Stay',
                'section_content' => '<p>The Address Dubai Marina sits at the centre of the Walk and is the easiest hotel to use as a base - you can walk to most things and the metro is two minutes away. The Westin and Le Royal Meridien on JBR are larger, beach-adjacent, and better for families. The Grand Plaza Mövenpick is the value pick if you do not need a beach.</p>',
            ],
            [
                'section_title'   => 'How to Get Around',
                'section_content' => '<p>For visitors: the Metro is the easiest way in and out from the wider city, and the Marina Walk is best done on foot. The water taxi between the Marina and Bluewaters costs AED 50 and is faster than driving on a busy night. Avoid taxis on the Walk during peak evenings - the JBR-to-Marina ride can sit in traffic for 25 minutes for what is a 12-minute walk.</p>',
            ],
        ];
        foreach ($sections as $i => &$section) {
            $sec_img = $sideload('https://picsum.photos/seed/marina-guide-' . $i . '/1200/800', 'Marina Section ' . $i);
            if ($sec_img) $section['section_image'] = $sec_img;
        }
        unset($section);
        update_field('guide_sections', $sections, $guide_id);

        update_field('key_facts', [
            ['label' => 'Length',                       'value' => '3km canal'],
            ['label' => 'Metro Stations',               'value' => 'DMCC, Sobha Realty'],
            ['label' => 'Best Time to Visit',           'value' => 'November to March'],
            ['label' => 'Average Dinner',               'value' => 'AED 250-500 per person'],
            ['label' => 'Walking End-to-End',           'value' => '40 minutes'],
        ], $guide_id);

        wp_set_post_terms($guide_id, [$ensure_term('Dubai Marina', 'dubai-district')], 'dubai-district');
    }

    // ═══ 7a. LISTING: Emirates Golf Club ═══
    $list1_id = $upsert_post('emirates-golf-club-listing', [
        'post_title'   => 'Emirates Golf Club',
        'post_type'    => 'listing',
        'post_excerpt' => 'The original Dubai championship golf club. Two 18-hole courses (Majlis and Faldo), Address-branded clubhouse, full academy.',
    ]);
    if ($list1_id) {
        $hero_img = $sideload('https://picsum.photos/seed/emirates-golf-listing/1600/900', 'Emirates Golf Club');
        if ($hero_img) set_post_thumbnail($list1_id, $hero_img);
        $logo_id = $sideload('https://picsum.photos/seed/emirates-golf-logo/400/400', 'Emirates Golf Club logo');
        if ($logo_id) update_field('business_logo', $logo_id, $list1_id);

        $gallery_ids = [];
        for ($i = 1; $i <= 4; $i++) {
            $g_id = $sideload('https://picsum.photos/seed/egc-gallery-' . $i . '/1200/800', 'EGC ' . $i);
            if ($g_id) $gallery_ids[] = $g_id;
        }
        if ($gallery_ids) update_field('gallery', $gallery_ids, $list1_id);

        update_field('business_name',        'Emirates Golf Club', $list1_id);
        update_field('business_description', '<p>The first grass course in the Middle East, opened in 1988. Home to the Hero Dubai Desert Classic on the DP World Tour and one of two championship 18s (Majlis, Faldo). Visitor green fees range AED 695-1,295. Members and visitors share the same clubhouse and academy.</p>', $list1_id);
        update_field('address',              "Sheikh Zayed Road\nEmirates Hills, Dubai", $list1_id);
        update_field('phone',                '+971 4 380 2222',                          $list1_id);
        update_field('email',                'info@emiratesgolfclub.ae',                  $list1_id);
        update_field('website',              'https://dubaigolf.com/emirates-golf-club',  $list1_id);
        update_field('instagram',            'https://instagram.com/emiratesgolfclub',    $list1_id);
        update_field('opening_hours', [
            ['day' => 'Monday',    'open' => '06:00', 'close' => '21:00'],
            ['day' => 'Tuesday',   'open' => '06:00', 'close' => '21:00'],
            ['day' => 'Wednesday', 'open' => '06:00', 'close' => '21:00'],
            ['day' => 'Thursday',  'open' => '06:00', 'close' => '21:00'],
            ['day' => 'Friday',    'open' => '06:00', 'close' => '21:00'],
            ['day' => 'Saturday',  'open' => '06:00', 'close' => '21:00'],
            ['day' => 'Sunday',    'open' => '06:00', 'close' => '21:00'],
        ], $list1_id);
        update_field('price_range',  '$$$$',     $list1_id);
        update_field('listing_tier', 'Premium',  $list1_id);
        update_field('is_verified',  1,          $list1_id);
        update_field('is_featured',  1,          $list1_id);

        wp_set_post_terms($list1_id, [$ensure_term('Golf', 'business-category')],         'business-category');
        wp_set_post_terms($list1_id, [$ensure_term('Emirates Hills', 'dubai-district')], 'dubai-district');
    }

    // ═══ 7b. LISTING: Grafton Tailors ═══
    $list2_id = $upsert_post('grafton-tailors-listing', [
        'post_title'   => 'Grafton Tailors',
        'post_type'    => 'listing',
        'post_excerpt' => 'Bespoke tailoring in DIFC. Houses Loro Piana and Holland & Sherry, with a four-master atelier.',
    ]);
    if ($list2_id) {
        $hero_img = $sideload('https://picsum.photos/seed/grafton-tailors-store/1600/900', 'Grafton Tailors store');
        if ($hero_img) set_post_thumbnail($list2_id, $hero_img);
        $logo_id = $sideload('https://picsum.photos/seed/grafton-tailors-listing-logo/400/400', 'Grafton Tailors logo');
        if ($logo_id) update_field('business_logo', $logo_id, $list2_id);

        update_field('business_name',        'Grafton Tailors', $list2_id);
        update_field('business_description', '<p>Bespoke tailoring atelier in the DIFC. Cloth from Huddersfield, Loro Piana, Drago, and Holland & Sherry. House pattern stored on file for repeat commissions. Suits start at AED 6,500. By appointment.</p>', $list2_id);
        update_field('address',              "Gate Village 4, Level 2\nDIFC, Dubai",   $list2_id);
        update_field('phone',                '+971 4 555 0123',                         $list2_id);
        update_field('email',                'enquiries@graftontailors.ae',             $list2_id);
        update_field('website',              'https://graftontailors.ae',               $list2_id);
        update_field('instagram',            'https://instagram.com/graftontailors',    $list2_id);
        update_field('opening_hours', [
            ['day' => 'Monday',    'open' => '10:00',  'close' => '19:00'],
            ['day' => 'Tuesday',   'open' => '10:00',  'close' => '19:00'],
            ['day' => 'Wednesday', 'open' => '10:00',  'close' => '19:00'],
            ['day' => 'Thursday',  'open' => '10:00',  'close' => '19:00'],
            ['day' => 'Friday',    'open' => '10:00',  'close' => '17:00'],
            ['day' => 'Saturday',  'open' => '11:00',  'close' => '17:00'],
            ['day' => 'Sunday',    'open' => 'Closed', 'close' => 'Closed'],
        ], $list2_id);
        update_field('price_range',  '$$$$',     $list2_id);
        update_field('listing_tier', 'Premium',  $list2_id);
        update_field('is_verified',  1,          $list2_id);
        update_field('is_featured',  0,          $list2_id);

        wp_set_post_terms($list2_id, [$ensure_term('Tailors', 'business-category')], 'business-category');
        wp_set_post_terms($list2_id, [$ensure_term('DIFC', 'dubai-district')],       'dubai-district');
    }

    // ═══ 7c. LISTING: The Reform Studio (Free tier) ═══
    $list3_id = $upsert_post('the-reform-studio-listing', [
        'post_title'   => 'The Reform Studio',
        'post_type'    => 'listing',
        'post_excerpt' => 'Reformer pilates studio in Business Bay. Small-group classes, certified instructors, no contracts.',
    ]);
    if ($list3_id) {
        update_field('business_name',        'The Reform Studio', $list3_id);
        update_field('business_description', '<p>A boutique reformer pilates studio in Business Bay. Classes capped at six. Drop-in AED 150, packs from AED 1,200 / 10 sessions. Lead instructor is a former STOTT-certified clinical pilates trainer.</p>', $list3_id);
        update_field('address',              "Bay Square Building 3\nBusiness Bay, Dubai", $list3_id);
        update_field('phone',                '+971 4 555 0145',                            $list3_id);
        update_field('email',                'hello@thereformstudio.ae',                    $list3_id);
        update_field('website',              'https://thereformstudio.ae',                  $list3_id);
        update_field('opening_hours', [
            ['day' => 'Monday',    'open' => '06:30',  'close' => '20:00'],
            ['day' => 'Tuesday',   'open' => '06:30',  'close' => '20:00'],
            ['day' => 'Wednesday', 'open' => '06:30',  'close' => '20:00'],
            ['day' => 'Thursday',  'open' => '06:30',  'close' => '20:00'],
            ['day' => 'Friday',    'open' => '07:00',  'close' => '13:00'],
            ['day' => 'Saturday',  'open' => '08:00',  'close' => '13:00'],
            ['day' => 'Sunday',    'open' => 'Closed', 'close' => 'Closed'],
        ], $list3_id);
        update_field('price_range',  '$$',  $list3_id);
        update_field('listing_tier', 'Free', $list3_id);
        update_field('is_verified',  0,     $list3_id);
        update_field('is_featured',  0,     $list3_id);

        wp_set_post_terms($list3_id, [$ensure_term('Reformer Pilates & Fitness', 'business-category')], 'business-category');
        wp_set_post_terms($list3_id, [$ensure_term('Business Bay', 'dubai-district')],                  'dubai-district');
    }

    // Cross-link the business spotlight to its listing + review for richer related-CTA section
    if ($biz_id && isset($list2_id) && $list2_id) {
        update_field('related_listing', [$list2_id], $biz_id);
    }

    return ['created' => $created, 'failures' => $failures];
}
