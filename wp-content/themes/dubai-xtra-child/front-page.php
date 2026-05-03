<?php
/**
 * Dubai Xtra - Front Page Template
 *
 * Sections render only when real content exists. Until each CPT has
 * featured / current entries, the corresponding section is hidden so
 * the homepage never shows empty layouts.
 *
 * Section order (when populated):
 *  1. Hero with brand wordmark + search
 *  2. Featured Reviews (if any featured)
 *  3. Best Of Dubai (always renders when lists exist)
 *  4. Browse By Category (terms exist - always renders)
 *  5. Things To Do (if any current entries)
 *  6. Owner Spotlight (if a featured one exists)
 *  7. Business Spotlight (if a featured one exists)
 *  8. Directory (if any premium listings)
 *  9. Newsletter (always renders)
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

get_header(); ?>

<style>
/* ═══ HERO ═══ */
.dx-hero-home {
  position: relative;
  min-height: 640px;
  background-image: url('/wp-content/uploads/2026/04/Dubai%20Xtra%20Hero.jpg');
  background-size: cover;
  background-position: center;
  background-color: var(--dx-navy);
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  padding: 6rem 1.5rem;
}
.dx-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(rgba(26, 26, 46, 0.45), rgba(26, 26, 46, 0.85));
  pointer-events: none;
}
.dx-hero-content {
  position: relative;
  z-index: 1;
  max-width: 720px;
  color: var(--dx-warm-white);
}
.dx-hero-wordmark {
  font-family: 'Cormorant Garamond', serif;
  font-size: 2.5rem;
  font-weight: 600;
  display: block;
  margin-bottom: 1.5rem;
  letter-spacing: 0.02em;
  color: var(--dx-warm-white);
}
.dx-hero-wordmark em {
  font-style: italic;
  font-weight: 400;
  color: var(--dx-gold);
}
.dx-hero-home h1 {
  color: var(--dx-warm-white);
  font-size: 4.5rem;
  margin: 0 0 1rem;
  line-height: 1.05;
}
.dx-hero-home .dx-lede {
  font-size: 1.125rem;
  color: rgba(255, 255, 255, 0.85);
  margin: 0 auto 2rem;
  line-height: 1.6;
  max-width: 560px;
}
.dx-hero-home .dx-search-bar {
  background: var(--dx-white);
}

/* Best Of Dubai grid (4 cards on dark) */
.dx-best-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1.5rem;
}
.dx-section-dark .dx-card { background: var(--dx-white); }
.dx-section-dark .dx-section-header h2 { color: var(--dx-warm-white); }
.dx-section-dark .dx-section-header a { color: var(--dx-gold); }

/* Featured reviews layout (when populated) */
.dx-featured-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1.5rem;
}
.dx-featured-side {
  display: grid;
  grid-template-rows: 1fr 1fr;
  gap: 1.5rem;
}
.dx-featured-large .dx-card-image { height: 480px; }
.dx-featured-large .dx-card-body { padding: 2rem; }
.dx-featured-large h3 { font-size: 2rem; margin: 0.75rem 0; }
.dx-featured-side .dx-card-image { height: 200px; }
.dx-featured-side h4 { margin: 0.5rem 0; }
.dx-card-title-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  margin: 0.75rem 0;
}
.dx-card-summary {
  color: var(--dx-text);
  line-height: 1.6;
  margin: 0.75rem 0 0;
  font-size: 0.95rem;
}
/* Image-overlay labels + score */
.dx-card-image-wrap {
  position: relative;
  overflow: hidden;
}
.dx-card-image-wrap .dx-score {
  position: absolute;
  top: 1rem;
  right: 1rem;
  z-index: 2;
}
.dx-card-label {
  position: absolute;
  top: 1rem;
  left: 1rem;
  z-index: 2;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  padding: 0.45rem 0.8rem;
  border-radius: 2px;
  line-height: 1;
}
.dx-card-label-pick,
.dx-card-label-sponsored,
.dx-card-label-partner {
  background: var(--dx-gold);
  color: var(--dx-navy);
}
.dx-card-label-featured {
  background: rgba(26, 26, 46, 0.92);
  color: var(--dx-warm-white);
}

/* Things to do horizontal cards */
.dx-ttd-grid {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 1.5rem;
}
.dx-ttd-row {
  display: grid;
  grid-template-columns: 160px 1fr;
}
.dx-ttd-row img {
  width: 100%;
  height: 100%;
  min-height: 160px;
  object-fit: cover;
}
.dx-ttd-row .dx-card-body { padding: 1.25rem; }

/* Spotlight 2-col */
.dx-spotlight-2col {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  align-items: center;
}
.dx-spotlight-2col img {
  width: 100%;
  height: 480px;
  object-fit: cover;
  border-radius: 2px;
}

/* Category grid 4x2 */
.dx-category-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
}
.dx-category-card { display: block; }
.dx-category-icon {
  width: 36px;
  height: 36px;
  margin: 0 auto 0.75rem;
  color: var(--dx-gold);
}
.dx-category-name { font-family: 'Cormorant Garamond', serif; font-size: 1.125rem; color: var(--dx-navy); margin: 0; }
.dx-category-count { font-size: 0.7rem; color: var(--dx-mid-grey); letter-spacing: 0.08em; text-transform: uppercase; margin-top: 0.25rem; display: block; }

/* Directory rows */
.dx-directory-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 0;
  border-bottom: 1px solid var(--dx-light-grey);
  transition: padding 0.2s ease, background 0.2s ease;
}
.dx-directory-row:hover {
  background: #FFFDF5;
  padding-left: 0.5rem;
  padding-right: 0.5rem;
}
.dx-directory-info { display: flex; align-items: center; gap: 1rem; min-width: 0; }
.dx-directory-logo {
  width: 56px;
  height: 56px;
  object-fit: contain;
  border: 1px solid var(--dx-light-grey);
  padding: 4px;
  flex-shrink: 0;
}
.dx-directory-titles { min-width: 0; }
.dx-directory-titles h4 { font-size: 1.25rem; margin: 0 0 0.25rem; }
.dx-directory-name-row { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.dx-directory-cta {
  font-size: 0.75rem;
  color: var(--dx-gold);
  letter-spacing: 0.08em;
  text-transform: uppercase;
  font-weight: 600;
  flex-shrink: 0;
}

/* Newsletter */
.dx-newsletter-section {
  background: var(--dx-light-grey);
  text-align: center;
}
.dx-newsletter-section h2 { margin-bottom: 1rem; }
.dx-newsletter-section .dx-lede {
  max-width: 480px;
  margin: 0 auto 2rem;
  color: var(--dx-mid-grey);
}

@media (max-width: 768px) {
  .dx-hero-home { min-height: 480px; padding: 4rem 1.5rem; }
  .dx-hero-home h1 { font-size: 2.75rem; }
  .dx-hero-wordmark { font-size: 1.75rem; margin-bottom: 1rem; }
  .dx-best-grid { grid-template-columns: 1fr; }
  .dx-featured-grid { grid-template-columns: 1fr; }
  .dx-featured-side { grid-template-rows: auto auto; }
  .dx-featured-large .dx-card-image { height: 280px; }
  .dx-featured-large .dx-card-body { padding: 1.5rem; }
  .dx-featured-large h3 { font-size: 1.5rem; }
  .dx-ttd-grid { grid-template-columns: 1fr; }
  .dx-ttd-row { grid-template-columns: 120px 1fr; }
  .dx-spotlight-2col { grid-template-columns: 1fr; gap: 1.5rem; }
  .dx-spotlight-2col img { height: 280px; }
  .dx-category-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>

<main id="primary" class="dx-front-page">

  <!-- ═══ 1. HERO ═══ -->
  <section class="dx-hero-home" aria-labelledby="dx-hero-heading">
    <div class="dx-hero-overlay" aria-hidden="true"></div>
    <div class="dx-hero-content">
      <span class="dx-hero-wordmark">Dubai <em>Xtra</em></span>
      <h1 id="dx-hero-heading">Discover Dubai's Best</h1>
      <p class="dx-lede">Honest reviews, hand-picked things to do, and a directory of the businesses worth knowing.</p>
      <form class="dx-search-bar" action="<?php echo esc_url(home_url('/')); ?>" method="get" role="search">
        <label class="screen-reader-text" for="dx-hero-search">Search Dubai Xtra</label>
        <input id="dx-hero-search" type="search" name="s" placeholder="Search reviews, listings, districts..." value="<?php echo esc_attr(get_search_query()); ?>">
        <button type="submit">Search</button>
      </form>
    </div>
  </section>

  <!-- ═══ 2. LATEST REVIEWS (slotted: Editor's Pick / Sponsored / Featured) ═══ -->
  <?php
  // Three fixed slots so the layout is predictable regardless of post mix:
  //   - Big card: latest Editorial featured review (renders an "Editor's Pick" badge)
  //   - Top right: latest Sponsored Feature ("Sponsored" badge)
  //   - Bottom right: next Editorial featured ("Featured" badge)
  $editorial_q = new WP_Query([
      'post_type'      => 'review',
      'posts_per_page' => 2,
      'orderby'        => 'date',
      'order'          => 'DESC',
      'meta_query'     => [
          'relation' => 'AND',
          ['key' => 'is_featured',  'value' => '1'],
          ['key' => 'feature_type', 'value' => 'Editorial'],
      ],
  ]);
  $sponsored_q = new WP_Query([
      'post_type'      => 'review',
      'posts_per_page' => 1,
      'orderby'        => 'date',
      'order'          => 'DESC',
      'meta_query'     => [
          'relation' => 'AND',
          ['key' => 'is_featured',  'value' => '1'],
          ['key' => 'feature_type', 'value' => 'Sponsored Feature'],
      ],
  ]);
  $main      = $editorial_q->posts[0] ?? null;
  $extra     = $editorial_q->posts[1] ?? null;
  $sponsored = $sponsored_q->posts[0] ?? null;

  // If no editorial featured exists, fall back to any featured review for the big slot
  if (!$main) {
      $any = dx_get_featured_reviews(1);
      if ($any->have_posts()) $main = $any->posts[0];
      wp_reset_postdata();
  }

  if ($main || $sponsored || $extra) :
  ?>
    <section class="dx-section" aria-labelledby="dx-reviews-heading">
      <?php
      dx_section_header(
        'Latest Reviews',
        '',
        'View All',
        get_post_type_archive_link('review')
      );
      ?>
      <h2 id="dx-reviews-heading" class="screen-reader-text">Latest reviews</h2>

      <div class="dx-featured-grid">

        <?php if ($main) :
          $score   = get_field('review_score', $main->ID);
          $summary = get_field('review_summary', $main->ID) ?: get_the_excerpt($main);
          $type    = get_field('feature_type', $main->ID);
          // Big-slot label: "Editor's Pick" for Editorial, otherwise feature_type itself
          $main_label  = $type === 'Sponsored Feature' ? 'Sponsored' : ($type === 'Partner' ? 'Partner' : "Editor's Pick");
          $main_class  = $type === 'Sponsored Feature' ? 'sponsored' : ($type === 'Partner' ? 'partner' : 'pick');
        ?>
          <article class="dx-card dx-featured-large">
            <a href="<?php echo esc_url(get_permalink($main)); ?>" aria-label="<?php echo esc_attr(get_the_title($main)); ?>">
              <?php if (has_post_thumbnail($main)) : ?>
                <div class="dx-card-image-wrap">
                  <?php echo get_the_post_thumbnail($main, 'dx-hero', ['class' => 'dx-card-image', 'alt' => esc_attr(get_the_title($main))]); ?>
                  <span class="dx-card-label dx-card-label-<?php echo esc_attr($main_class); ?>"><?php echo esc_html($main_label); ?></span>
                  <?php if ($score) dx_score_badge($score); ?>
                </div>
              <?php endif; ?>
              <div class="dx-card-body">
                <?php dx_meta_line($main->ID); ?>
                <h3><?php echo esc_html(get_the_title($main)); ?></h3>
                <p class="dx-card-summary"><?php echo esc_html(wp_trim_words($summary, 32)); ?></p>
              </div>
            </a>
          </article>
        <?php endif; ?>

        <?php if ($sponsored || $extra) : ?>
          <div class="dx-featured-side">

            <?php if ($sponsored) :
              $s_score = get_field('review_score', $sponsored->ID);
            ?>
              <article class="dx-card">
                <a href="<?php echo esc_url(get_permalink($sponsored)); ?>" aria-label="<?php echo esc_attr(get_the_title($sponsored)); ?>">
                  <?php if (has_post_thumbnail($sponsored)) : ?>
                    <div class="dx-card-image-wrap">
                      <?php echo get_the_post_thumbnail($sponsored, 'dx-card', ['class' => 'dx-card-image', 'alt' => esc_attr(get_the_title($sponsored))]); ?>
                      <span class="dx-card-label dx-card-label-sponsored">Sponsored</span>
                      <?php if ($s_score) dx_score_badge($s_score, 'sm'); ?>
                    </div>
                  <?php endif; ?>
                  <div class="dx-card-body">
                    <?php dx_meta_line($sponsored->ID); ?>
                    <h4><?php echo esc_html(get_the_title($sponsored)); ?></h4>
                  </div>
                </a>
              </article>
            <?php endif; ?>

            <?php if ($extra) :
              $e_score = get_field('review_score', $extra->ID);
            ?>
              <article class="dx-card">
                <a href="<?php echo esc_url(get_permalink($extra)); ?>" aria-label="<?php echo esc_attr(get_the_title($extra)); ?>">
                  <?php if (has_post_thumbnail($extra)) : ?>
                    <div class="dx-card-image-wrap">
                      <?php echo get_the_post_thumbnail($extra, 'dx-card', ['class' => 'dx-card-image', 'alt' => esc_attr(get_the_title($extra))]); ?>
                      <span class="dx-card-label dx-card-label-featured">Featured</span>
                      <?php if ($e_score) dx_score_badge($e_score, 'sm'); ?>
                    </div>
                  <?php endif; ?>
                  <div class="dx-card-body">
                    <?php dx_meta_line($extra->ID); ?>
                    <h4><?php echo esc_html(get_the_title($extra)); ?></h4>
                  </div>
                </a>
              </article>
            <?php endif; ?>

          </div>
        <?php endif; ?>

      </div>
    </section>
  <?php endif; wp_reset_postdata(); ?>

  <!-- ═══ 3. BEST OF DUBAI ═══ -->
  <?php
  $best = dx_get_best_lists(4);
  if ($best->have_posts()) : ?>
    <section class="dx-section dx-section-dark" aria-labelledby="dx-best-heading">
      <div class="dx-section-header">
        <div>
          <div class="gold-line"></div>
          <span class="section-label">Best Of Dubai</span>
          <h2 id="dx-best-heading" style="margin-top: 0.5rem;">Definitive Lists, Carefully Curated</h2>
        </div>
        <a href="<?php echo esc_url(get_post_type_archive_link('best-list')); ?>" style="font-size: 0.75rem; letter-spacing: 0.08em;">View All Lists →</a>
      </div>

      <div class="dx-best-grid">
        <?php while ($best->have_posts()) : $best->the_post();
          $items = get_field('list_items');
          $count = is_array($items) ? count($items) : 0;
        ?>
          <article class="dx-card">
            <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
              <?php
                if (has_post_thumbnail()) {
                    the_post_thumbnail('dx-card', ['class' => 'dx-card-image', 'alt' => esc_attr(get_the_title())]);
                }
              ?>
              <div class="dx-card-body">
                <?php if ($count) dx_tag($count . ' Picks', 'gold'); ?>
                <h4 style="margin: 0.5rem 0;"><?php the_title(); ?></h4>
                <p style="font-size: 0.875rem; color: var(--dx-mid-grey); margin: 0;">
                  <?php echo esc_html(wp_trim_words(get_the_excerpt(), 14)); ?>
                </p>
              </div>
            </a>
          </article>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- ═══ 4. BROWSE BY CATEGORY ═══ -->
  <?php
  $cats = get_terms([
    'taxonomy'   => 'business-category',
    'hide_empty' => true,
    'number'     => 8,
    'parent'     => 0,
    'orderby'    => 'count',
    'order'      => 'DESC',
  ]);
  if (!empty($cats) && !is_wp_error($cats)) :
    /**
     * Minimal line-art SVG icon set keyed to category names. Defaults to a
     * plain circle for any category without a mapping.
     */
    $svg_icons = [
      'Golf'                          => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 18V4l9 4-9 4"/><circle cx="6" cy="20" r="2"/></svg>',
      'Clinics & Aesthetics'          => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s-7-4.5-7-11a5 5 0 0 1 9-3 5 5 0 0 1 9 3c0 6.5-7 11-7 11z" transform="translate(-2 0)"/></svg>',
      'Dental'                        => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 4c-2 0-3 2-3 4 0 5 2 12 4 12 1 0 1.5-3 3-3s2 3 3 3c2 0 4-7 4-12 0-2-1-4-3-4-1.5 0-2 1-4 1s-2.5-1-4-1z"/></svg>',
      'Business Setup'                => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="1"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2M3 13h18"/></svg>',
      'Real Estate &amp; Property'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11l9-7 9 7v9a1 1 0 0 1-1 1h-5v-7h-6v7H4a1 1 0 0 1-1-1z"/></svg>',
      'Luxury Services'               => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3h12l3 5-9 13L3 8z"/><path d="M6 3l3 5h6l3-5M3 8h18"/></svg>',
      'Restaurants &amp; Hospitality' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 3v8a2 2 0 0 0 4 0V3M9 11v10M17 3c-2 0-3 2-3 5s1 5 3 5v8"/></svg>',
      'Reformer Pilates &amp; Fitness'=> '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="5" r="2"/><path d="M12 7v6m-3 8 3-8 3 8M9 11h6"/></svg>',
      'Tailors'                       => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4 8 16M14 10l6 10"/></svg>',
      'Car Dealerships &amp; Rentals' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 13l2-5h14l2 5v5h-3v-2H6v2H3zM6 13h12"/><circle cx="7" cy="16" r="1.5"/><circle cx="17" cy="16" r="1.5"/></svg>',
      'Interior Design'               => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="1"/><path d="M3 9h18M9 9v12"/></svg>',
      'Combat Sports &amp; Boxing'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M8 6h6v4l3 3v6H6v-6l3-3z"/><path d="M11 14h2"/></svg>',
      'Health &amp; Wellness'         => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12h4l2-6 4 12 2-6h6"/></svg>',
      'Watersports'                   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 18c2 0 2-1 4-1s2 1 4 1 2-1 4-1 2 1 4 1 2-1 4-1M2 14c2 0 2-1 4-1s2 1 4 1 2-1 4-1 2 1 4 1 2-1 4-1M7 10l5-6 5 6"/></svg>',
      'Driving Schools'               => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>',
      'Cigars &amp; Lounges'          => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="3" rx="1"/><path d="M19 12.5c1-0.5 1-2 0-2.5M5 9c0-1 1-2 2-2"/></svg>',
      'Digital Marketing'             => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 12h3l2-6 4 12 2-9 2 6h5"/></svg>',
      'Irish &amp; UK Businesses'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/></svg>',
    ];
    $default_icon = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="12" cy="12" r="8"/></svg>';
  ?>
    <section class="dx-section" aria-labelledby="dx-cat-heading">
      <?php dx_section_header('Browse', 'By Category'); ?>
      <h2 id="dx-cat-heading" class="screen-reader-text">Categories</h2>

      <nav class="dx-category-grid" aria-label="Categories">
        <?php foreach ($cats as $cat) :
          $key = html_entity_decode($cat->name, ENT_QUOTES);
          $icon = $svg_icons[$cat->name] ?? $svg_icons[$key] ?? $default_icon;
        ?>
          <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="dx-category-card">
            <div class="dx-category-icon"><?php echo $icon; // hardcoded SVG ?></div>
            <h4 class="dx-category-name"><?php echo esc_html($key); ?></h4>
            <span class="dx-category-count"><?php echo esc_html($cat->count); ?> entries</span>
          </a>
        <?php endforeach; ?>
      </nav>
    </section>
  <?php endif; ?>

  <!-- ═══ 5. THINGS TO DO (only if current entries exist) ═══ -->
  <?php $ttd = dx_get_current_things_to_do(4); if ($ttd->have_posts()) : ?>
    <section class="dx-section dx-section-white" aria-labelledby="dx-ttd-heading">
      <?php
      dx_section_header(
        'Things To Do',
        'This Weekend in Dubai',
        'See Everything',
        get_post_type_archive_link('things-to-do')
      );
      ?>
      <div class="dx-ttd-grid" id="dx-ttd-heading">
        <?php while ($ttd->have_posts()) : $ttd->the_post();
          $relevance = get_field('time_relevance');
          $district  = dx_get_primary_district();
          $items     = get_field('things_items');
          $first     = is_array($items) && isset($items[0]) ? $items[0] : null;
          $price     = $first['item_price'] ?? '';
        ?>
          <article class="dx-ttd-card dx-ttd-row">
            <a href="<?php the_permalink(); ?>" style="display: contents;" aria-label="<?php echo esc_attr(get_the_title()); ?>">
              <?php
                if (has_post_thumbnail()) {
                    the_post_thumbnail('dx-thumbnail', ['alt' => esc_attr(get_the_title())]);
                } else {
                    echo '<div style="background: var(--dx-light-grey);"></div>';
                }
              ?>
              <div class="dx-card-body">
                <?php if ($relevance) dx_tag($relevance, 'gold'); ?>
                <h4 style="margin: 0.5rem 0;"><?php the_title(); ?></h4>
                <span class="dx-card-meta">
                  <?php
                    $bits = [];
                    if ($district) $bits[] = $district->name;
                    if ($price)    $bits[] = $price;
                    echo esc_html(implode(' - ', $bits));
                  ?>
                </span>
              </div>
            </a>
          </article>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- ═══ 6. OWNER SPOTLIGHT (only if a featured one exists) ═══ -->
  <?php
  $owner = dx_get_featured_spotlight('owner-spotlight');
  if ($owner->have_posts()) : $owner->the_post();
    $photo    = get_field('owner_photo');
    $quote    = get_field('pull_quote');
    $name     = get_field('owner_name') ?: get_the_title();
    $business = get_field('business_name');
    $img_url  = $photo ? ($photo['sizes']['dx-hero'] ?? $photo['url']) : (has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'dx-hero') : '');
  ?>
    <section class="dx-section" aria-labelledby="dx-owner-heading">
      <?php dx_section_header('Owner Spotlight', 'Meet The People Behind The Brand', 'All Owner Spotlights', get_post_type_archive_link('owner-spotlight')); ?>
      <article class="dx-spotlight-2col">
        <?php if ($img_url) : ?>
          <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($name); ?>">
        <?php endif; ?>
        <div>
          <?php if ($business) : ?>
            <span class="section-label"><?php echo esc_html($business); ?></span>
          <?php endif; ?>
          <h3 id="dx-owner-heading" style="margin: 0.5rem 0 1rem;"><?php echo esc_html($name); ?></h3>
          <?php if ($quote) : ?>
            <blockquote class="dx-pull-quote">"<?php echo esc_html($quote); ?>"</blockquote>
          <?php endif; ?>
          <a href="<?php the_permalink(); ?>" class="dx-btn dx-btn-outline">Read The Interview</a>
        </div>
      </article>
    </section>
  <?php wp_reset_postdata(); endif; ?>

  <!-- ═══ 7. BUSINESS SPOTLIGHT (only if a featured one exists) ═══ -->
  <?php
  $biz = dx_get_featured_spotlight('business-spotlight');
  if ($biz->have_posts()) : $biz->the_post();
    $logo     = get_field('business_logo');
    $business = get_field('business_name') ?: get_the_title();
    $tagline  = get_field('business_tagline');
    $stats    = get_field('key_stats');
    $standout = get_field('standout_feature');
    $img_url  = has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'dx-hero') : ($logo ? $logo['url'] : '');
  ?>
    <section class="dx-section dx-section-white" aria-labelledby="dx-biz-heading">
      <?php dx_section_header('Business Spotlight', 'Brands Worth Knowing', 'All Business Spotlights', get_post_type_archive_link('business-spotlight')); ?>
      <article class="dx-spotlight-2col">
        <?php if ($img_url) : ?>
          <img src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($business); ?>">
        <?php endif; ?>
        <div>
          <h3 id="dx-biz-heading"><?php echo esc_html($business); ?></h3>
          <?php if ($tagline) : ?>
            <p style="font-size: 1.125rem; color: var(--dx-mid-grey); margin: 0.5rem 0 1.5rem; line-height: 1.5;"><?php echo esc_html($tagline); ?></p>
          <?php endif; ?>

          <?php if (is_array($stats) && count($stats)) : ?>
            <div class="dx-spotlight-stats" style="margin-bottom: 1.5rem;">
              <?php foreach (array_slice($stats, 0, 3) as $stat) : ?>
                <div>
                  <div class="dx-spotlight-stat-value"><?php echo esc_html($stat['stat_value']); ?></div>
                  <div class="dx-spotlight-stat-label"><?php echo esc_html($stat['stat_label']); ?></div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if ($standout) : ?>
            <div class="dx-standout-box">
              <span class="section-label">Standout Feature</span>
              <p style="margin: 0.5rem 0 0; line-height: 1.6;"><?php echo esc_html($standout); ?></p>
            </div>
          <?php endif; ?>

          <a href="<?php the_permalink(); ?>" class="dx-btn dx-btn-primary" style="margin-top: 1.5rem;">Read Full Spotlight</a>
        </div>
      </article>
    </section>
  <?php wp_reset_postdata(); endif; ?>

  <!-- ═══ 8. FROM THE DIRECTORY (only if any premium listings) ═══ -->
  <?php $listings = dx_get_premium_listings(5); if ($listings->have_posts()) : ?>
    <section class="dx-section dx-section-white" aria-labelledby="dx-dir-heading">
      <?php dx_section_header('From The Directory', 'Premium Listings', 'Browse The Directory', get_post_type_archive_link('listing')); ?>

      <div role="list" id="dx-dir-heading">
        <?php while ($listings->have_posts()) : $listings->the_post();
          $logo     = get_field('business_logo');
          $business = get_field('business_name') ?: get_the_title();
          $verified = get_field('is_verified');
          $district = dx_get_primary_district();
          $price    = get_field('price_range');
          $logo_url = $logo ? ($logo['sizes']['dx-listing-logo'] ?? $logo['url']) : '';
        ?>
          <a href="<?php the_permalink(); ?>" class="dx-directory-row" role="listitem">
            <div class="dx-directory-info">
              <?php if ($logo_url) : ?>
                <img class="dx-directory-logo" src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($business); ?>">
              <?php endif; ?>
              <div class="dx-directory-titles">
                <div class="dx-directory-name-row">
                  <h4><?php echo esc_html($business); ?></h4>
                  <?php if ($verified) dx_verified_badge(); ?>
                </div>
                <span class="dx-card-meta">
                  <?php
                    $bits = [];
                    if ($district) $bits[] = $district->name;
                    if ($price)    $bits[] = $price;
                    echo esc_html(implode(' - ', $bits));
                  ?>
                </span>
              </div>
            </div>
            <span class="dx-directory-cta">View →</span>
          </a>
        <?php endwhile; wp_reset_postdata(); ?>
      </div>
    </section>
  <?php endif; ?>

  <!-- ═══ 9. NEWSLETTER ═══ -->
  <aside class="dx-section dx-newsletter-section" aria-labelledby="dx-news-heading">
    <span class="section-label">Newsletter</span>
    <h2 id="dx-news-heading" style="margin-top: 0.5rem;">Dubai's Best, In Your Inbox</h2>
    <p class="dx-lede">A short weekly edit of openings, reviews and things to do worth your time.</p>
    <form class="dx-newsletter-form" action="#" method="post">
      <label class="screen-reader-text" for="dx-newsletter-email">Your email address</label>
      <input id="dx-newsletter-email" type="email" name="email" required class="dx-newsletter-input" placeholder="your@email.com" autocomplete="email">
      <button type="submit" class="dx-btn dx-btn-primary">Subscribe</button>
    </form>
  </aside>

</main>

<?php get_footer();
