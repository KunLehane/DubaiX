<?php
/**
 * Single Review Template
 *
 * Magazine-style review with full-bleed hero, score badge, structured sections
 * from the review_sections ACF repeater, highlights list, and a sticky sidebar
 * with business details, contact, and Google Maps embed.
 *
 * Outputs Review + LocalBusiness/AggregateRating JSON-LD schema for rich results.
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

get_header();

while (have_posts()) : the_post();
    $score          = get_field('review_score');
    $summary        = get_field('review_summary');
    $sections       = get_field('review_sections');
    $pros           = get_field('review_pros');
    $business_name  = get_field('business_name') ?: get_the_title();
    $business_web   = get_field('business_website');
    $business_loc   = get_field('business_location');
    $maps_embed     = get_field('google_maps_embed');
    $price          = get_field('price_range');
    $phone          = get_field('contact_phone');
    $email          = get_field('contact_email');
    $instagram      = get_field('contact_instagram');
    $hero_url       = has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'dx-hero') : '';
?>

<style>
/* ═══ HERO ═══ */
.dx-review-hero {
  position: relative;
  min-height: 540px;
  background-size: cover;
  background-position: center;
  background-color: var(--dx-navy);
  display: flex;
  align-items: flex-end;
}
.dx-review-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(rgba(26, 26, 46, 0.3), rgba(26, 26, 46, 0.55) 50%, rgba(26, 26, 46, 0.92));
  pointer-events: none;
}
.dx-review-hero-content {
  position: relative;
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 1.5rem;
  width: 100%;
  z-index: 1;
}
.dx-review-hero h1 {
  color: var(--dx-warm-white);
  font-size: 4rem;
  margin: 0.5rem 0 1rem;
  max-width: 900px;
  line-height: 1.05;
}
.dx-review-hero .section-label { color: var(--dx-gold); }
.dx-review-meta-row {
  display: flex;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}
.dx-review-meta-row .dx-card-meta {
  color: rgba(255,255,255,0.75);
  font-size: 0.8rem;
}

/* ═══ LAYOUT ═══ */
.dx-breadcrumbs {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem 1.5rem 0;
  font-size: 0.75rem;
  color: var(--dx-mid-grey);
  letter-spacing: 0.05em;
}
.dx-review-layout {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 3rem;
  max-width: 1200px;
  margin: 0 auto;
  padding: 4rem 1.5rem;
  align-items: flex-start;
}
.dx-review-main { min-width: 0; }
.dx-review-main p,
.dx-review-main li {
  font-size: 1.0625rem;
  line-height: 1.75;
}
.dx-review-summary {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.5rem;
  line-height: 1.5;
  color: var(--dx-navy);
  font-style: italic;
  margin: 0 0 2.5rem;
  padding-left: 1.25rem;
  border-left: 3px solid var(--dx-gold);
}

/* ═══ SECTIONS ═══ */
.dx-review-section { margin-bottom: 3rem; }
.dx-review-section h2 {
  font-size: 2rem;
  margin: 0 0 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid var(--dx-light-grey);
}
.dx-review-section img { max-width: 100%; height: auto; }
.dx-review-gallery {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 0.75rem;
  margin-top: 1.5rem;
}
.dx-review-gallery img {
  width: 100%;
  height: 280px;
  object-fit: cover;
  border-radius: 2px;
}

/* ═══ HIGHLIGHTS ═══ */
.dx-highlights {
  background: var(--dx-warm-white);
  border: 1px solid var(--dx-light-grey);
  padding: 2rem;
  margin: 2rem 0 3rem;
}
.dx-highlights h2 { font-size: 1.5rem; margin: 0 0 1rem; padding: 0; border: none; }
.dx-highlights ul { list-style: none; padding: 0; margin: 0; }
.dx-highlights li {
  padding: 0.6rem 0 0.6rem 2rem;
  position: relative;
  border-bottom: 1px solid var(--dx-light-grey);
}
.dx-highlights li:last-child { border-bottom: none; }
.dx-highlights li::before {
  content: "";
  position: absolute;
  left: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 10px;
  height: 10px;
  background: var(--dx-gold);
  border-radius: 50%;
}

/* ═══ SIDEBAR ═══ */
.dx-review-sidebar {
  position: sticky;
  top: 100px;
  background: var(--dx-white);
  border: 1px solid var(--dx-light-grey);
  border-radius: 2px;
  padding: 2rem;
}
.dx-review-sidebar h2 { font-size: 1.5rem; margin: 0.25rem 0 1rem; }
.dx-review-detail {
  padding: 0.75rem 0;
  border-bottom: 1px solid var(--dx-light-grey);
}
.dx-review-detail:last-of-type { border-bottom: none; }
.dx-detail-label {
  display: block;
  font-size: 0.7rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--dx-mid-grey);
  font-weight: 600;
  margin-bottom: 0.25rem;
}
.dx-detail-value { font-size: 0.95rem; color: var(--dx-text); line-height: 1.5; }
.dx-detail-value a { color: var(--dx-navy); text-decoration: underline; text-decoration-color: var(--dx-gold); text-underline-offset: 3px; }
.dx-detail-value a:hover { color: var(--dx-gold); }
.dx-review-sidebar .dx-btn { display: block; text-align: center; margin-top: 1.5rem; }
.dx-review-map {
  width: 100%;
  height: 240px;
  margin-top: 1.5rem;
  border: 1px solid var(--dx-light-grey);
  overflow: hidden;
}
.dx-review-map iframe { width: 100%; height: 100%; border: 0; display: block; }

/* ═══ RELATED ═══ */
.dx-related-section { background: var(--dx-light-grey); }
.dx-related-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}
.dx-related-title-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  margin: 0.5rem 0;
}
.dx-related-title-row h3 { margin: 0; font-size: 1.25rem; }

@media (max-width: 768px) {
  .dx-review-hero { min-height: 380px; }
  .dx-review-hero-content { padding: 2rem 1.5rem; }
  .dx-review-hero h1 { font-size: 2.5rem; }
  .dx-review-layout { grid-template-columns: 1fr; gap: 2rem; padding: 2.5rem 1.5rem; }
  .dx-review-sidebar { position: static; padding: 1.5rem; }
  .dx-review-summary { font-size: 1.25rem; }
  .dx-review-section h2 { font-size: 1.5rem; }
  .dx-review-gallery { grid-template-columns: 1fr; }
  .dx-review-gallery img { height: 220px; }
  .dx-related-grid { grid-template-columns: 1fr; }
}
</style>

<main id="primary" class="dx-single-review">

  <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
    <nav class="dx-breadcrumbs" aria-label="Breadcrumb"><?php rank_math_the_breadcrumbs(); ?></nav>
  <?php endif; ?>

  <article>

    <!-- ═══ HERO ═══ -->
    <header class="dx-review-hero" <?php if ($hero_url) : ?>style="background-image: url('<?php echo esc_url($hero_url); ?>');"<?php endif; ?>>
      <div class="dx-review-hero-overlay" aria-hidden="true"></div>
      <div class="dx-review-hero-content">
        <div class="dx-review-meta-row">
          <span class="section-label">Review</span>
          <?php dx_feature_badge(); ?>
        </div>
        <h1><?php the_title(); ?></h1>
        <div class="dx-review-meta-row">
          <?php if ($score) dx_score_badge($score); ?>
          <span class="dx-card-meta">
            <?php
              $bits = [];
              $cat  = dx_get_primary_category();
              $dist = dx_get_primary_district();
              if ($dist)  $bits[] = $dist->name;
              if ($cat)   $bits[] = $cat->name;
              if ($price) $bits[] = $price;
              echo esc_html(implode(' - ', $bits));
            ?>
          </span>
        </div>
      </div>
    </header>

    <!-- ═══ MAIN + SIDEBAR ═══ -->
    <div class="dx-review-layout">

      <div class="dx-review-main">
        <?php if ($summary) : ?>
          <p class="dx-review-summary"><?php echo esc_html($summary); ?></p>
        <?php endif; ?>

        <?php if (is_array($sections) && count($sections)) : ?>
          <?php foreach ($sections as $section) : ?>
            <section class="dx-review-section">
              <?php if (!empty($section['section_title'])) : ?>
                <h2><?php echo esc_html($section['section_title']); ?></h2>
              <?php endif; ?>

              <?php if (!empty($section['section_content'])) : ?>
                <?php echo wp_kses_post($section['section_content']); ?>
              <?php endif; ?>

              <?php if (!empty($section['section_images']) && is_array($section['section_images'])) : ?>
                <div class="dx-review-gallery">
                  <?php foreach ($section['section_images'] as $img) :
                    $img_src = $img['sizes']['large'] ?? $img['url'];
                    $img_alt = $img['alt'] ?? '';
                  ?>
                    <img src="<?php echo esc_url($img_src); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy">
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </section>
          <?php endforeach; ?>
        <?php endif; ?>

        <?php if (is_array($pros) && count(array_filter(array_column($pros, 'text')))) : ?>
          <aside class="dx-highlights">
            <h2>Highlights</h2>
            <ul>
              <?php foreach ($pros as $pro) : ?>
                <?php if (!empty($pro['text'])) : ?>
                  <li><?php echo esc_html($pro['text']); ?></li>
                <?php endif; ?>
              <?php endforeach; ?>
            </ul>
          </aside>
        <?php endif; ?>

        <?php
        $body = get_the_content();
        if (trim(wp_strip_all_tags($body)) !== '') : ?>
          <section class="dx-review-section">
            <?php the_content(); ?>
          </section>
        <?php endif; ?>
      </div>

      <aside class="dx-review-sidebar" aria-label="Business details">
        <span class="section-label">At A Glance</span>
        <h2><?php echo esc_html($business_name); ?></h2>

        <?php if ($business_loc) : ?>
          <div class="dx-review-detail">
            <span class="dx-detail-label">Location</span>
            <span class="dx-detail-value"><?php echo esc_html($business_loc); ?></span>
          </div>
        <?php endif; ?>

        <?php if ($price) : ?>
          <div class="dx-review-detail">
            <span class="dx-detail-label">Price Range</span>
            <span class="dx-detail-value"><?php echo esc_html($price); ?></span>
          </div>
        <?php endif; ?>

        <?php if ($phone) : ?>
          <div class="dx-review-detail">
            <span class="dx-detail-label">Phone</span>
            <span class="dx-detail-value">
              <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($email) : ?>
          <div class="dx-review-detail">
            <span class="dx-detail-label">Email</span>
            <span class="dx-detail-value">
              <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($instagram) :
          $handle = ltrim($instagram, '@');
        ?>
          <div class="dx-review-detail">
            <span class="dx-detail-label">Instagram</span>
            <span class="dx-detail-value">
              <a href="https://instagram.com/<?php echo esc_attr($handle); ?>" target="_blank" rel="noopener">@<?php echo esc_html($handle); ?></a>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($business_web) : ?>
          <a href="<?php echo esc_url($business_web); ?>" class="dx-btn dx-btn-primary" target="_blank" rel="noopener">Visit Website →</a>
        <?php endif; ?>

        <?php if ($maps_embed) : ?>
          <div class="dx-review-map">
            <?php echo wp_kses($maps_embed, [
                'iframe' => [
                    'src'             => true,
                    'width'           => true,
                    'height'          => true,
                    'frameborder'     => true,
                    'allowfullscreen' => true,
                    'loading'         => true,
                    'referrerpolicy'  => true,
                    'style'           => true,
                    'title'           => true,
                ],
            ]); ?>
          </div>
        <?php endif; ?>

        <?php dx_render_sponsored_widget(); ?>
      </aside>
    </div>

    <!-- ═══ RELATED REVIEWS ═══ -->
    <?php
    $related_args = [
        'post_type'      => 'review',
        'posts_per_page' => 3,
        'post__not_in'   => [get_the_ID()],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    $primary_cat = dx_get_primary_category();
    if ($primary_cat) {
        $related_args['tax_query'] = [[
            'taxonomy' => 'business-category',
            'field'    => 'term_id',
            'terms'    => [$primary_cat->term_id],
        ]];
    }
    $related = new WP_Query($related_args);
    if ($related->have_posts()) :
    ?>
      <section class="dx-section dx-related-section" aria-labelledby="dx-related-heading">
        <?php dx_section_header('More Reviews', 'You Might Also Like', 'All Reviews', get_post_type_archive_link('review')); ?>
        <h2 id="dx-related-heading" class="screen-reader-text">More reviews</h2>
        <div class="dx-related-grid">
          <?php while ($related->have_posts()) : $related->the_post();
            $r_score = get_field('review_score');
          ?>
            <article class="dx-card">
              <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                <?php if (has_post_thumbnail()) the_post_thumbnail('dx-card', ['class' => 'dx-card-image', 'alt' => esc_attr(get_the_title())]); ?>
                <div class="dx-card-body">
                  <?php dx_meta_line(); ?>
                  <div class="dx-related-title-row">
                    <h3><?php the_title(); ?></h3>
                    <?php if ($r_score) dx_score_badge($r_score, 'sm'); ?>
                  </div>
                </div>
              </a>
            </article>
          <?php endwhile; wp_reset_postdata(); ?>
        </div>
      </section>
    <?php endif; ?>

  </article>

  <?php
  /**
   * ═══ JSON-LD SCHEMA ═══
   * Review with itemReviewed -> LocalBusiness (with aggregateRating wrapping
   * the score). Covers both Review and AggregateRating rich-result patterns.
   */
  $local_business = array_filter([
      '@type'       => 'LocalBusiness',
      'name'        => $business_name,
      'address'     => $business_loc,
      'telephone'   => $phone,
      'url'         => $business_web,
      'image'       => $hero_url,
      'priceRange'  => $price,
  ]);
  if ($score) {
      $local_business['aggregateRating'] = [
          '@type'       => 'AggregateRating',
          'ratingValue' => (float) $score,
          'bestRating'  => 10,
          'worstRating' => 1,
          'ratingCount' => 1,
          'reviewCount' => 1,
      ];
  }

  $schema = [
      '@context'      => 'https://schema.org',
      '@type'         => 'Review',
      '@id'           => get_permalink() . '#review',
      'name'          => get_the_title(),
      'datePublished' => get_the_date('c'),
      'dateModified'  => get_the_modified_date('c'),
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
      'itemReviewed'  => $local_business,
  ];
  if ($score) {
      $schema['reviewRating'] = [
          '@type'       => 'Rating',
          'ratingValue' => (float) $score,
          'bestRating'  => 10,
          'worstRating' => 1,
      ];
  }
  if ($summary) {
      $schema['reviewBody'] = wp_strip_all_tags($summary);
  }
  ?>
  <script type="application/ld+json"><?php echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>

</main>

<?php endwhile; get_footer();
