<?php
/**
 * Single Guide Template
 *
 * Long-form editorial guide. Sticky in-page table of contents + key facts
 * box on the left, guide sections from the guide_sections ACF repeater
 * in the main column, then related listings, reviews, and things-to-do
 * as three separate cross-sell strips at the bottom.
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

/**
 * Slugify a section title for in-page anchors.
 */
if (!function_exists('dx_slugify')) {
    function dx_slugify($text) {
        $slug = sanitize_title($text);
        return $slug ?: 'section';
    }
}

get_header();

while (have_posts()) : the_post();
    $guide_type     = get_field('guide_type');
    $sections       = get_field('guide_sections');
    $facts          = get_field('key_facts');
    $rel_listings   = get_field('related_listings');
    $rel_reviews    = get_field('related_reviews');
    $rel_ttd        = get_field('related_things_to_do');
    $hero_url       = has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'dx-hero') : '';
    $district       = dx_get_primary_district();
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

/* ═══ HERO ═══ */
.dx-guide-hero {
  position: relative;
  min-height: 460px;
  background-size: cover;
  background-position: center;
  background-color: var(--dx-navy);
  display: flex;
  align-items: flex-end;
}
.dx-guide-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(rgba(26,26,46,0.3), rgba(26,26,46,0.55) 50%, rgba(26,26,46,0.92));
  pointer-events: none;
}
.dx-guide-hero-inner {
  position: relative;
  z-index: 1;
  max-width: 1200px;
  margin: 0 auto;
  padding: 4rem 1.5rem;
  width: 100%;
}
.dx-guide-hero h1 {
  color: var(--dx-warm-white);
  font-size: 4rem;
  margin: 1rem 0 0.75rem;
  max-width: 900px;
  line-height: 1.05;
}
.dx-guide-hero .section-label { color: var(--dx-gold); }
.dx-guide-hero-meta {
  font-size: 0.85rem;
  color: rgba(255,255,255,0.75);
  letter-spacing: 0.06em;
}

/* ═══ LAYOUT ═══ */
.dx-guide-layout {
  display: grid;
  grid-template-columns: 280px 1fr;
  gap: 3rem;
  max-width: 1200px;
  margin: 0 auto;
  padding: 4rem 1.5rem;
  align-items: flex-start;
}
.dx-guide-aside { position: sticky; top: 100px; }
.dx-guide-main { min-width: 0; max-width: 760px; }

/* ═══ TOC ═══ */
.dx-guide-toc {
  background: var(--dx-white);
  border: 1px solid var(--dx-light-grey);
  padding: 1.5rem;
  border-radius: 2px;
  margin-bottom: 1.5rem;
}
.dx-guide-toc h2 {
  font-size: 0.7rem;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--dx-gold);
  font-family: 'DM Sans', sans-serif;
  font-weight: 700;
  margin: 0 0 1rem;
}
.dx-guide-toc ol {
  list-style: none;
  padding: 0;
  margin: 0;
  counter-reset: dx-toc;
}
.dx-guide-toc li {
  counter-increment: dx-toc;
  padding: 0.5rem 0;
  border-bottom: 1px solid var(--dx-light-grey);
  font-size: 0.9rem;
  line-height: 1.4;
}
.dx-guide-toc li:last-child { border-bottom: none; }
.dx-guide-toc a {
  display: flex;
  gap: 0.6rem;
  color: var(--dx-navy);
}
.dx-guide-toc a::before {
  content: counter(dx-toc, decimal-leading-zero) ".";
  color: var(--dx-gold);
  font-family: 'Cormorant Garamond', serif;
  font-weight: 700;
  flex-shrink: 0;
}
.dx-guide-toc a:hover { color: var(--dx-gold); }

/* ═══ KEY FACTS ═══ */
.dx-guide-facts {
  background: var(--dx-warm-white);
  border: 1px solid var(--dx-light-grey);
  padding: 1.5rem;
  border-radius: 2px;
}
.dx-guide-facts h2 {
  font-size: 0.7rem;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--dx-gold);
  font-family: 'DM Sans', sans-serif;
  font-weight: 700;
  margin: 0 0 1rem;
}
.dx-guide-fact {
  padding: 0.6rem 0;
  border-bottom: 1px solid var(--dx-light-grey);
}
.dx-guide-fact:last-child { border-bottom: none; }
.dx-fact-label {
  display: block;
  font-size: 0.7rem;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--dx-mid-grey);
  font-weight: 600;
  margin-bottom: 0.2rem;
}
.dx-fact-value { font-size: 0.95rem; color: var(--dx-text); line-height: 1.5; }

/* ═══ MAIN CONTENT ═══ */
.dx-guide-main p,
.dx-guide-main li { font-size: 1.0625rem; line-height: 1.8; }
.dx-guide-main p:first-of-type::first-letter {
  font-family: 'Cormorant Garamond', serif;
  font-size: 4.5rem;
  float: left;
  line-height: 0.9;
  margin: 0.4rem 0.6rem 0 0;
  color: var(--dx-gold);
  font-weight: 600;
}
.dx-guide-section {
  margin-bottom: 3.5rem;
  scroll-margin-top: 100px;
}
.dx-guide-section h2 {
  font-size: 2rem;
  margin: 0 0 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid var(--dx-light-grey);
}
.dx-guide-section img,
.dx-guide-section-img {
  max-width: 100%;
  height: auto;
  display: block;
  margin: 1.5rem 0;
  border-radius: 2px;
}
.dx-guide-section-img {
  width: 100%;
  height: 380px;
  object-fit: cover;
}

/* ═══ CROSS-SELL STRIPS ═══ */
.dx-guide-related-strip {
  border-top: 1px solid var(--dx-light-grey);
  padding: 4rem 0;
}
.dx-guide-related-strip-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
}
.dx-related-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

@media (max-width: 768px) {
  .dx-guide-hero { min-height: 360px; }
  .dx-guide-hero h1 { font-size: 2.5rem; }
  .dx-guide-layout { grid-template-columns: 1fr; gap: 2rem; padding: 2.5rem 1.5rem; }
  .dx-guide-aside { position: static; }
  .dx-guide-main { max-width: none; }
  .dx-guide-section h2 { font-size: 1.5rem; }
  .dx-guide-section-img { height: 240px; }
  .dx-related-grid { grid-template-columns: 1fr; }
}
</style>

<main id="primary" class="dx-single-guide">

  <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
    <nav class="dx-breadcrumbs" aria-label="Breadcrumb"><?php rank_math_the_breadcrumbs(); ?></nav>
  <?php endif; ?>

  <article>

    <!-- ═══ HERO ═══ -->
    <header class="dx-guide-hero" <?php if ($hero_url) : ?>style="background-image: url('<?php echo esc_url($hero_url); ?>');"<?php endif; ?>>
      <div class="dx-guide-hero-overlay" aria-hidden="true"></div>
      <div class="dx-guide-hero-inner">
        <span class="section-label"><?php echo esc_html($guide_type ?: 'Guide'); ?></span>
        <h1><?php the_title(); ?></h1>
        <span class="dx-guide-hero-meta">
          <?php
            $bits = [];
            if ($district) $bits[] = $district->name;
            $bits[] = get_the_modified_date('F Y');
            echo esc_html(implode(' - ', $bits));
          ?>
        </span>
      </div>
    </header>

    <!-- ═══ MAIN + ASIDE ═══ -->
    <div class="dx-guide-layout">

      <aside class="dx-guide-aside" aria-label="Guide navigation">
        <?php if (is_array($sections) && count(array_filter(array_column($sections, 'section_title')))) : ?>
          <nav class="dx-guide-toc" aria-labelledby="dx-guide-toc-heading">
            <h2 id="dx-guide-toc-heading">In This Guide</h2>
            <ol>
              <?php foreach ($sections as $section) :
                if (empty($section['section_title'])) continue;
                $anchor = dx_slugify($section['section_title']);
              ?>
                <li><a href="#<?php echo esc_attr($anchor); ?>"><?php echo esc_html($section['section_title']); ?></a></li>
              <?php endforeach; ?>
            </ol>
          </nav>
        <?php endif; ?>

        <?php if (is_array($facts) && count(array_filter(array_column($facts, 'value')))) : ?>
          <div class="dx-guide-facts">
            <h2>Key Facts</h2>
            <?php foreach ($facts as $fact) :
              if (empty($fact['label']) && empty($fact['value'])) continue;
            ?>
              <div class="dx-guide-fact">
                <?php if (!empty($fact['label'])) : ?>
                  <span class="dx-fact-label"><?php echo esc_html($fact['label']); ?></span>
                <?php endif; ?>
                <?php if (!empty($fact['value'])) : ?>
                  <span class="dx-fact-value"><?php echo esc_html($fact['value']); ?></span>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php dx_render_sponsored_widget(); ?>
      </aside>

      <div class="dx-guide-main">
        <?php
        // Body content (the_content) renders first, before structured sections
        $body = get_the_content();
        if (trim(wp_strip_all_tags($body)) !== '') {
            the_content();
        }
        ?>

        <?php if (is_array($sections) && count($sections)) : ?>
          <?php foreach ($sections as $section) :
            $title   = $section['section_title']   ?? '';
            $content = $section['section_content'] ?? '';
            $img     = $section['section_image']   ?? null;
            $img_url = $img ? ($img['sizes']['large'] ?? $img['url']) : '';
            $img_alt = $img['alt'] ?? '';
            if (!$title && !$content && !$img_url) continue;
            $anchor = $title ? dx_slugify($title) : '';
          ?>
            <section class="dx-guide-section" <?php if ($anchor) : ?>id="<?php echo esc_attr($anchor); ?>"<?php endif; ?>>
              <?php if ($title) : ?>
                <h2><?php echo esc_html($title); ?></h2>
              <?php endif; ?>
              <?php if ($img_url) : ?>
                <img class="dx-guide-section-img" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy">
              <?php endif; ?>
              <?php if ($content) : ?>
                <?php echo wp_kses_post($content); ?>
              <?php endif; ?>
            </section>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

    </div>

    <!-- ═══ RELATED LISTINGS ═══ -->
    <?php if (is_array($rel_listings) && count($rel_listings)) : ?>
      <section class="dx-guide-related-strip" aria-labelledby="dx-rel-listings-heading">
        <div class="dx-guide-related-strip-inner">
          <?php dx_section_header('Featured Businesses', 'Worth Visiting', 'Browse The Directory', get_post_type_archive_link('listing')); ?>
          <h2 id="dx-rel-listings-heading" class="screen-reader-text">Related directory listings</h2>
          <div class="dx-related-grid">
            <?php foreach (array_slice($rel_listings, 0, 3) as $listing) :
              $l_logo  = get_field('business_logo', $listing->ID);
              $l_name  = get_field('business_name', $listing->ID) ?: get_the_title($listing);
              $l_ver   = get_field('is_verified',   $listing->ID);
            ?>
              <article class="dx-card">
                <a href="<?php echo esc_url(get_permalink($listing)); ?>" aria-label="<?php echo esc_attr($l_name); ?>">
                  <?php if (has_post_thumbnail($listing)) echo get_the_post_thumbnail($listing, 'dx-card', ['class' => 'dx-card-image', 'alt' => esc_attr($l_name)]); ?>
                  <div class="dx-card-body">
                    <?php dx_meta_line($listing->ID); ?>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin: 0.5rem 0;">
                      <h3 style="margin: 0; font-size: 1.25rem;"><?php echo esc_html($l_name); ?></h3>
                      <?php if ($l_ver) dx_verified_badge(); ?>
                    </div>
                  </div>
                </a>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <!-- ═══ RELATED REVIEWS ═══ -->
    <?php if (is_array($rel_reviews) && count($rel_reviews)) : ?>
      <section class="dx-guide-related-strip" aria-labelledby="dx-rel-reviews-heading" style="background: var(--dx-warm-white);">
        <div class="dx-guide-related-strip-inner">
          <?php dx_section_header('Read Next', 'Reviews From This Guide', 'All Reviews', get_post_type_archive_link('review')); ?>
          <h2 id="dx-rel-reviews-heading" class="screen-reader-text">Related reviews</h2>
          <div class="dx-related-grid">
            <?php foreach (array_slice($rel_reviews, 0, 3) as $review) :
              $r_score = get_field('review_score', $review->ID);
            ?>
              <article class="dx-card">
                <a href="<?php echo esc_url(get_permalink($review)); ?>" aria-label="<?php echo esc_attr(get_the_title($review)); ?>">
                  <?php if (has_post_thumbnail($review)) echo get_the_post_thumbnail($review, 'dx-card', ['class' => 'dx-card-image', 'alt' => esc_attr(get_the_title($review))]); ?>
                  <div class="dx-card-body">
                    <?php dx_meta_line($review->ID); ?>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; margin: 0.5rem 0;">
                      <h3 style="margin: 0; font-size: 1.25rem;"><?php echo esc_html(get_the_title($review)); ?></h3>
                      <?php if ($r_score) dx_score_badge($r_score, 'sm'); ?>
                    </div>
                  </div>
                </a>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <!-- ═══ RELATED THINGS TO DO ═══ -->
    <?php if (is_array($rel_ttd) && count($rel_ttd)) : ?>
      <section class="dx-guide-related-strip" aria-labelledby="dx-rel-ttd-heading">
        <div class="dx-guide-related-strip-inner">
          <?php dx_section_header('While You Are There', 'Things To Do', 'See Everything', get_post_type_archive_link('things-to-do')); ?>
          <h2 id="dx-rel-ttd-heading" class="screen-reader-text">Related things to do</h2>
          <div class="dx-related-grid">
            <?php foreach (array_slice($rel_ttd, 0, 3) as $ttd) :
              $t_relevance = get_field('time_relevance', $ttd->ID);
            ?>
              <article class="dx-card">
                <a href="<?php echo esc_url(get_permalink($ttd)); ?>" aria-label="<?php echo esc_attr(get_the_title($ttd)); ?>">
                  <?php if (has_post_thumbnail($ttd)) echo get_the_post_thumbnail($ttd, 'dx-card', ['class' => 'dx-card-image', 'alt' => esc_attr(get_the_title($ttd))]); ?>
                  <div class="dx-card-body">
                    <?php if ($t_relevance) dx_tag($t_relevance, 'gold'); ?>
                    <h3 style="margin: 0.5rem 0; font-size: 1.25rem;"><?php echo esc_html(get_the_title($ttd)); ?></h3>
                    <?php dx_meta_line($ttd->ID); ?>
                  </div>
                </a>
              </article>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endif; ?>

  </article>

  <?php
  /**
   * ═══ JSON-LD SCHEMA ═══
   * Article with TableOfContents-friendly headline + image. Search engines
   * use Article schema for guides; the in-page TOC anchors are picked up
   * automatically by Google's rich-result parser.
   */
  $schema = array_filter([
      '@context'      => 'https://schema.org',
      '@type'         => 'Article',
      '@id'           => get_permalink() . '#article',
      'headline'      => get_the_title(),
      'datePublished' => get_the_date('c'),
      'dateModified'  => get_the_modified_date('c'),
      'image'         => $hero_url ?: null,
      'description'   => has_excerpt() ? get_the_excerpt() : '',
      'articleSection'=> $guide_type ?: null,
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
  ]);
  ?>
  <script type="application/ld+json"><?php echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>

</main>

<?php endwhile; get_footer();
