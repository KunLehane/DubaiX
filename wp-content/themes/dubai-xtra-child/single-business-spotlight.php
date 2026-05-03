<?php
/**
 * Single Business Spotlight Template
 *
 * Brand-focused profile. Cinematic hero with logo + tagline, key stats bar,
 * standout feature callout, alternating spotlight sections, gallery,
 * and prominent CTAs to the related review and directory listing.
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

get_header();

while (have_posts()) : the_post();
    $business        = get_field('business_name') ?: get_the_title();
    $tagline         = get_field('business_tagline');
    $logo            = get_field('business_logo');
    $gallery         = get_field('gallery');
    $sections        = get_field('spotlight_sections');
    $stats           = get_field('key_stats');
    $standout        = get_field('standout_feature');
    $website         = get_field('website');
    $instagram       = get_field('instagram');
    $phone           = get_field('phone');
    $email           = get_field('email');
    $maps_embed      = get_field('google_maps_embed');
    $related_review  = get_field('related_review');
    $related_listing = get_field('related_listing');
    $hero_url        = has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'dx-hero') : '';
    $logo_url        = $logo ? ($logo['sizes']['dx-listing-logo'] ?? $logo['url']) : '';
    $district        = dx_get_primary_district();
    $category        = dx_get_primary_category();
?>

<style>
/* ═══ BREADCRUMBS ═══ */
.dx-breadcrumbs {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem 1.5rem 0;
  font-size: 0.75rem;
  color: var(--dx-mid-grey);
  letter-spacing: 0.05em;
}

/* ═══ HERO (cinematic) ═══ */
.dx-bs-hero {
  position: relative;
  min-height: 600px;
  background-size: cover;
  background-position: center;
  background-color: var(--dx-navy);
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
  color: var(--dx-warm-white);
}
.dx-bs-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(rgba(26,26,46,0.4), rgba(26,26,46,0.85));
  pointer-events: none;
}
.dx-bs-hero-content {
  position: relative;
  z-index: 1;
  max-width: 900px;
  padding: 4rem 1.5rem;
}
.dx-bs-logo {
  width: 100px;
  height: 100px;
  object-fit: contain;
  background: var(--dx-warm-white);
  border-radius: 2px;
  padding: 0.75rem;
  margin: 0 auto 1.5rem;
  display: block;
}
.dx-bs-hero h1 {
  color: var(--dx-warm-white);
  font-size: 4.5rem;
  margin: 0.5rem 0;
  line-height: 1.05;
}
.dx-bs-hero .section-label { color: var(--dx-gold); }
.dx-bs-tagline {
  font-family: 'Cormorant Garamond', serif;
  font-style: italic;
  font-size: 1.625rem;
  color: rgba(255,255,255,0.85);
  margin: 1rem 0 0;
  line-height: 1.4;
}
.dx-bs-hero-meta {
  margin-top: 1.5rem;
  font-size: 0.85rem;
  color: rgba(255,255,255,0.65);
  letter-spacing: 0.08em;
  text-transform: uppercase;
}
.dx-bs-hero-meta a { color: rgba(255,255,255,0.65); }
.dx-bs-hero-meta a:hover { color: var(--dx-gold); }

/* ═══ STATS BAR ═══ */
.dx-bs-stats-bar {
  background: var(--dx-warm-white);
  border-bottom: 1px solid var(--dx-light-grey);
  padding: 3rem 1.5rem;
}
.dx-bs-stats-grid {
  max-width: 960px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
  text-align: center;
}
.dx-bs-stat-value {
  font-family: 'Cormorant Garamond', serif;
  font-size: 4rem;
  color: var(--dx-navy);
  font-weight: 600;
  line-height: 1;
}
.dx-bs-stat-label {
  margin-top: 0.5rem;
  font-size: 0.7rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--dx-mid-grey);
  font-weight: 600;
}

/* ═══ STANDOUT CALLOUT ═══ */
.dx-bs-standout {
  max-width: 800px;
  margin: 0 auto;
  padding: 4rem 1.5rem;
  text-align: center;
}
.dx-bs-standout h2 {
  font-size: 0.9rem;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--dx-gold);
  font-family: 'DM Sans', sans-serif;
  font-weight: 700;
  margin: 0 0 1.5rem;
}
.dx-bs-standout p {
  font-family: 'Cormorant Garamond', serif;
  font-size: 2rem;
  line-height: 1.4;
  color: var(--dx-navy);
  font-weight: 500;
  margin: 0;
}

/* ═══ SECTIONS (alternating image/content) ═══ */
.dx-bs-sections { background: var(--dx-warm-white); padding: 2rem 0; }
.dx-bs-section {
  max-width: 1200px;
  margin: 0 auto;
  padding: 4rem 1.5rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 3rem;
  align-items: center;
}
.dx-bs-section.dx-bs-section-flip > .dx-bs-section-img { order: 2; }
.dx-bs-section-img {
  width: 100%;
  height: 460px;
  object-fit: cover;
  border-radius: 2px;
}
.dx-bs-section-text h3 {
  font-size: 2rem;
  margin: 0 0 1rem;
}
.dx-bs-section-text p,
.dx-bs-section-text li { font-size: 1.0625rem; line-height: 1.75; }

/* ═══ GALLERY ═══ */
.dx-bs-gallery-section {
  background: var(--dx-light-grey);
  padding: 4rem 0;
}
.dx-bs-gallery-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}
.dx-bs-gallery {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 0.5rem;
  margin-top: 2rem;
}
.dx-bs-gallery img {
  width: 100%;
  height: 220px;
  object-fit: cover;
  border-radius: 2px;
  cursor: zoom-in;
}

/* ═══ RELATED LINKS (review + listing CTAs) ═══ */
.dx-bs-related-cta {
  background: var(--dx-navy);
  padding: 5rem 1.5rem;
}
.dx-bs-related-grid {
  max-width: 960px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}
.dx-bs-related-card {
  background: var(--dx-warm-white);
  border-radius: 2px;
  padding: 2.5rem;
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}
.dx-bs-related-card .section-label { color: var(--dx-gold); }
.dx-bs-related-card h3 { font-size: 2rem; margin: 0; }
.dx-bs-related-card p { color: var(--dx-mid-grey); margin: 0 0 1rem; line-height: 1.6; }
.dx-bs-related-card .dx-btn { align-self: flex-start; }

/* ═══ CONTACT STRIP ═══ */
.dx-bs-contact {
  max-width: 1200px;
  margin: 0 auto;
  padding: 4rem 1.5rem;
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 2rem;
}
.dx-bs-contact-item .dx-detail-label {
  display: block;
  font-size: 0.7rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--dx-mid-grey);
  font-weight: 600;
  margin-bottom: 0.4rem;
}
.dx-bs-contact-item .dx-detail-value {
  font-size: 1rem;
  color: var(--dx-text);
  word-break: break-word;
}
.dx-bs-contact-item a {
  color: var(--dx-navy);
  text-decoration: underline;
  text-decoration-color: var(--dx-gold);
  text-underline-offset: 3px;
}
.dx-bs-contact-item a:hover { color: var(--dx-gold); }

@media (max-width: 768px) {
  .dx-bs-hero { min-height: 480px; }
  .dx-bs-hero h1 { font-size: 2.75rem; }
  .dx-bs-tagline { font-size: 1.25rem; }
  .dx-bs-logo { width: 72px; height: 72px; }
  .dx-bs-stats-grid { grid-template-columns: 1fr; gap: 1.5rem; }
  .dx-bs-stat-value { font-size: 3rem; }
  .dx-bs-standout p { font-size: 1.5rem; }
  .dx-bs-section { grid-template-columns: 1fr; gap: 1.5rem; padding: 2.5rem 1.5rem; }
  .dx-bs-section.dx-bs-section-flip > .dx-bs-section-img { order: 0; }
  .dx-bs-section-img { height: 280px; }
  .dx-bs-section-text h3 { font-size: 1.5rem; }
  .dx-bs-gallery { grid-template-columns: repeat(2, 1fr); }
  .dx-bs-gallery img { height: 160px; }
  .dx-bs-related-grid { grid-template-columns: 1fr; }
  .dx-bs-related-card { padding: 2rem; }
  .dx-bs-related-card h3 { font-size: 1.5rem; }
  .dx-bs-contact { grid-template-columns: 1fr 1fr; gap: 1.5rem; padding: 2.5rem 1.5rem; }
}
</style>

<main id="primary" class="dx-single-business-spotlight">

  <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
    <nav class="dx-breadcrumbs" aria-label="Breadcrumb"><?php rank_math_the_breadcrumbs(); ?></nav>
  <?php endif; ?>

  <article>

    <!-- ═══ HERO ═══ -->
    <header class="dx-bs-hero" <?php if ($hero_url) : ?>style="background-image: url('<?php echo esc_url($hero_url); ?>');"<?php endif; ?>>
      <div class="dx-bs-hero-overlay" aria-hidden="true"></div>
      <div class="dx-bs-hero-content">
        <?php if ($logo_url) : ?>
          <img class="dx-bs-logo" src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($business); ?> logo">
        <?php endif; ?>
        <span class="section-label">Business Spotlight</span>
        <h1><?php echo esc_html($business); ?></h1>
        <?php if ($tagline) : ?>
          <p class="dx-bs-tagline"><?php echo esc_html($tagline); ?></p>
        <?php endif; ?>
        <div class="dx-bs-hero-meta">
          <?php
            $bits = [];
            if ($district) $bits[] = '<a href="' . esc_url(get_term_link($district)) . '">' . esc_html($district->name) . '</a>';
            if ($category) $bits[] = '<a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a>';
            echo implode(' &middot; ', $bits);
          ?>
        </div>
      </div>
    </header>

    <!-- ═══ STATS BAR ═══ -->
    <?php if (is_array($stats) && count($stats)) : ?>
      <section class="dx-bs-stats-bar" aria-label="Key stats">
        <div class="dx-bs-stats-grid">
          <?php foreach (array_slice($stats, 0, 3) as $stat) : ?>
            <div>
              <div class="dx-bs-stat-value"><?php echo esc_html($stat['stat_value']); ?></div>
              <div class="dx-bs-stat-label"><?php echo esc_html($stat['stat_label']); ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>

    <!-- ═══ STANDOUT FEATURE ═══ -->
    <?php if ($standout) : ?>
      <section class="dx-bs-standout" aria-labelledby="dx-standout-heading">
        <h2 id="dx-standout-heading">What Makes Them Different</h2>
        <p><?php echo esc_html($standout); ?></p>
      </section>
    <?php endif; ?>

    <!-- ═══ SECTIONS (alternating image/content) ═══ -->
    <?php if (is_array($sections) && count($sections)) : ?>
      <div class="dx-bs-sections">
        <?php foreach ($sections as $i => $section) :
          $section_img = $section['section_image'] ?? null;
          $img_url = $section_img ? ($section_img['sizes']['large'] ?? $section_img['url']) : '';
          $img_alt = $section_img['alt'] ?? '';
          $flip    = ($i % 2 === 1) ? ' dx-bs-section-flip' : '';
        ?>
          <section class="dx-bs-section<?php echo esc_attr($flip); ?>">
            <?php if ($img_url) : ?>
              <img class="dx-bs-section-img" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy">
            <?php endif; ?>
            <div class="dx-bs-section-text">
              <?php if (!empty($section['section_title'])) : ?>
                <h3><?php echo esc_html($section['section_title']); ?></h3>
              <?php endif; ?>
              <?php if (!empty($section['section_content'])) : ?>
                <?php echo wp_kses_post($section['section_content']); ?>
              <?php endif; ?>
            </div>
          </section>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- ═══ GALLERY ═══ -->
    <?php if (is_array($gallery) && count($gallery)) : ?>
      <section class="dx-bs-gallery-section" aria-labelledby="dx-gallery-heading">
        <div class="dx-bs-gallery-inner">
          <?php dx_section_header('Gallery', 'A Closer Look'); ?>
          <h2 id="dx-gallery-heading" class="screen-reader-text">Gallery</h2>
          <div class="dx-bs-gallery">
            <?php foreach (array_slice($gallery, 0, 8) as $img) :
              $src = $img['sizes']['large'] ?? $img['url'];
              $alt = $img['alt'] ?? $business;
            ?>
              <a href="<?php echo esc_url($img['url']); ?>" target="_blank" rel="noopener">
                <img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy">
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <!-- ═══ POST CONTENT (any extra editor content) ═══ -->
    <?php
    $body = get_the_content();
    if (trim(wp_strip_all_tags($body)) !== '') : ?>
      <section class="dx-section" style="max-width: 800px;">
        <?php the_content(); ?>
      </section>
    <?php endif; ?>

    <!-- ═══ SPONSORED FEATURE ═══ -->
    <section class="dx-sponsored-section"><?php dx_render_sponsored_widget(); ?></section>

    <!-- ═══ RELATED REVIEW + LISTING CTAs ═══ -->
    <?php
    $review_post  = is_array($related_review)  && count($related_review)  ? $related_review[0]  : null;
    $listing_post = is_array($related_listing) && count($related_listing) ? $related_listing[0] : null;
    if ($review_post || $listing_post) :
    ?>
      <section class="dx-bs-related-cta" aria-labelledby="dx-related-cta-heading">
        <h2 id="dx-related-cta-heading" class="screen-reader-text">Read more about <?php echo esc_html($business); ?></h2>
        <div class="dx-bs-related-grid">
          <?php if ($review_post) :
            $r_score = get_field('review_score', $review_post->ID);
          ?>
            <article class="dx-bs-related-card">
              <span class="section-label">The Review</span>
              <h3><?php echo esc_html(get_the_title($review_post)); ?></h3>
              <?php if ($r_score) : ?>
                <div style="margin: 0.25rem 0 0.5rem;"><?php dx_score_badge($r_score, 'sm'); ?></div>
              <?php endif; ?>
              <p>Read our full editorial review of <?php echo esc_html($business); ?>.</p>
              <a href="<?php echo esc_url(get_permalink($review_post)); ?>" class="dx-btn dx-btn-primary">Read The Review →</a>
            </article>
          <?php endif; ?>

          <?php if ($listing_post) : ?>
            <article class="dx-bs-related-card">
              <span class="section-label">Directory</span>
              <h3><?php echo esc_html(get_the_title($listing_post)); ?></h3>
              <p>Hours, contact details, and how to find them.</p>
              <a href="<?php echo esc_url(get_permalink($listing_post)); ?>" class="dx-btn dx-btn-primary">View The Listing →</a>
            </article>
          <?php endif; ?>
        </div>
      </section>
    <?php endif; ?>

    <!-- ═══ CONTACT STRIP ═══ -->
    <?php if ($website || $phone || $email || $instagram) : ?>
      <section class="dx-section-white" aria-labelledby="dx-contact-heading">
        <div class="dx-bs-contact">
          <h2 id="dx-contact-heading" class="screen-reader-text">Contact <?php echo esc_html($business); ?></h2>

          <?php if ($website) : ?>
            <div class="dx-bs-contact-item">
              <span class="dx-detail-label">Website</span>
              <span class="dx-detail-value">
                <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener">
                  <?php echo esc_html(preg_replace('~^https?://(www\.)?~', '', rtrim($website, '/'))); ?>
                </a>
              </span>
            </div>
          <?php endif; ?>

          <?php if ($phone) : ?>
            <div class="dx-bs-contact-item">
              <span class="dx-detail-label">Phone</span>
              <span class="dx-detail-value">
                <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a>
              </span>
            </div>
          <?php endif; ?>

          <?php if ($email) : ?>
            <div class="dx-bs-contact-item">
              <span class="dx-detail-label">Email</span>
              <span class="dx-detail-value">
                <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
              </span>
            </div>
          <?php endif; ?>

          <?php if ($instagram) :
            $insta_url   = filter_var($instagram, FILTER_VALIDATE_URL) ? $instagram : 'https://instagram.com/' . ltrim($instagram, '@');
            $insta_label = filter_var($instagram, FILTER_VALIDATE_URL) ? '@' . trim(parse_url($instagram, PHP_URL_PATH), '/') : $instagram;
          ?>
            <div class="dx-bs-contact-item">
              <span class="dx-detail-label">Instagram</span>
              <span class="dx-detail-value">
                <a href="<?php echo esc_url($insta_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($insta_label); ?></a>
              </span>
            </div>
          <?php endif; ?>
        </div>
      </section>
    <?php endif; ?>

  </article>

  <?php
  /**
   * ═══ JSON-LD SCHEMA ═══
   * Article that's about an Organization. Includes the business as the
   * subject so search engines can connect this content to the brand.
   */
  $images = [];
  if ($hero_url) $images[] = $hero_url;
  if ($logo_url) $images[] = $logo_url;
  if (is_array($gallery)) {
      foreach (array_slice($gallery, 0, 4) as $g) {
          $images[] = $g['sizes']['large'] ?? $g['url'];
      }
  }
  $images = array_values(array_unique(array_filter($images)));

  $same_as = [];
  if ($website)   $same_as[] = $website;
  if ($instagram) {
      $same_as[] = filter_var($instagram, FILTER_VALIDATE_URL)
          ? $instagram
          : 'https://instagram.com/' . ltrim($instagram, '@');
  }

  $org = array_filter([
      '@type'     => 'Organization',
      'name'      => $business,
      'logo'      => $logo_url ?: null,
      'image'     => $hero_url ?: null,
      'url'       => $website ?: null,
      'telephone' => $phone ?: null,
      'email'     => $email ?: null,
      'sameAs'    => count($same_as) ? $same_as : null,
  ]);

  $schema = array_filter([
      '@context'      => 'https://schema.org',
      '@type'         => 'Article',
      '@id'           => get_permalink() . '#article',
      'headline'      => get_the_title(),
      'datePublished' => get_the_date('c'),
      'dateModified'  => get_the_modified_date('c'),
      'image'         => count($images) ? $images : null,
      'description'   => $tagline ?: ($standout ? wp_strip_all_tags($standout) : ''),
      'author'        => [
          '@type' => 'Organization',
          'name'  => get_bloginfo('name'),
          'url'   => home_url('/'),
      ],
      'publisher'     => [
          '@type' => 'Organization',
          'name'  => get_bloginfo('name'),
          'url'   => home_url('/'),
      ],
      'about'         => $org,
  ]);
  ?>
  <script type="application/ld+json"><?php echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>

</main>

<?php endwhile; get_footer();
