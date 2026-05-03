<?php
/**
 * Single Listing Template
 *
 * Business profile page with logo, gallery, opening hours, contact details,
 * verified badge, and Google Maps. Outputs LocalBusiness JSON-LD with
 * openingHoursSpecification for rich results.
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

/**
 * Normalise an opening-hours time string to HH:MM. Falls back to the raw
 * input if it cannot be parsed (so the schema is never silently broken,
 * but unusual inputs still render visibly).
 */
if (!function_exists('dx_normalize_time')) {
    function dx_normalize_time($t) {
        if (!$t) return null;
        $t = trim(strtolower($t));
        if (preg_match('/^(\d{1,2})(?::(\d{2}))?\s*(am|pm)?$/', $t, $m)) {
            $h    = (int) $m[1];
            $min  = isset($m[2]) ? (int) $m[2] : 0;
            $ampm = $m[3] ?? '';
            if ($ampm === 'pm' && $h < 12) $h += 12;
            if ($ampm === 'am' && $h === 12) $h = 0;
            return sprintf('%02d:%02d', $h, $min);
        }
        return $t;
    }
}

get_header();

while (have_posts()) : the_post();
    $business_name = get_field('business_name') ?: get_the_title();
    $description   = get_field('business_description');
    $logo          = get_field('business_logo');
    $gallery       = get_field('gallery');
    $address       = get_field('address');
    $maps_embed    = get_field('google_maps_embed');
    $phone         = get_field('phone');
    $email         = get_field('email');
    $website       = get_field('website');
    $instagram     = get_field('instagram');
    $hours         = get_field('opening_hours');
    $price         = get_field('price_range');
    $tier          = get_field('listing_tier');
    $verified      = get_field('is_verified');
    $is_premium    = ($tier === 'Premium');
    $hero_url      = has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'dx-hero') : '';
    $logo_url      = $logo ? ($logo['sizes']['dx-listing-logo'] ?? $logo['url']) : '';
    $district      = dx_get_primary_district();
    $category      = dx_get_primary_category();
?>

<style>
/* ═══ BANNER ═══ */
.dx-listing-banner {
  width: 100%;
  height: 320px;
  background-size: cover;
  background-position: center;
  background-color: var(--dx-light-grey);
  position: relative;
}
.dx-listing-banner-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(rgba(26, 26, 46, 0.2), rgba(26, 26, 46, 0.55));
  pointer-events: none;
}

/* ═══ BREADCRUMBS ═══ */
.dx-breadcrumbs {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1rem 1.5rem 0;
  font-size: 0.75rem;
  color: var(--dx-mid-grey);
  letter-spacing: 0.05em;
}

/* ═══ HEADER ═══ */
.dx-listing-header-wrap {
  background: var(--dx-white);
  border-bottom: 1px solid var(--dx-light-grey);
}
.dx-listing-header-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2.5rem 1.5rem;
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 1.5rem;
  align-items: flex-start;
}
.dx-listing-logo-large {
  width: 96px;
  height: 96px;
  object-fit: contain;
  border: 1px solid var(--dx-light-grey);
  border-radius: 2px;
  padding: 0.5rem;
  background: var(--dx-white);
}
.dx-listing-logo-fallback {
  width: 96px;
  height: 96px;
  background: var(--dx-warm-white);
  border: 1px solid var(--dx-light-grey);
  border-radius: 2px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Cormorant Garamond', serif;
  font-size: 2.5rem;
  color: var(--dx-gold);
  font-weight: 600;
}
.dx-listing-titles h1 {
  font-size: 3rem;
  margin: 0 0 0.5rem;
  line-height: 1.1;
}
.dx-listing-badges {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-bottom: 0.5rem;
}
.dx-listing-meta {
  font-size: 0.85rem;
  color: var(--dx-mid-grey);
  letter-spacing: 0.05em;
  text-transform: uppercase;
}
.dx-listing-meta a { color: var(--dx-mid-grey); text-decoration: none; }
.dx-listing-meta a:hover { color: var(--dx-gold); }

/* ═══ LAYOUT ═══ */
.dx-listing-layout {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 3rem;
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 1.5rem;
  align-items: flex-start;
}
.dx-listing-main { min-width: 0; }
.dx-listing-main h2 {
  font-size: 1.75rem;
  margin: 0 0 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid var(--dx-light-grey);
}
.dx-listing-section { margin-bottom: 3rem; }
.dx-listing-description p,
.dx-listing-description li { font-size: 1.0625rem; line-height: 1.75; }

/* ═══ GALLERY ═══ */
.dx-listing-gallery {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 0.5rem;
}
.dx-listing-gallery img {
  width: 100%;
  height: 200px;
  object-fit: cover;
  border-radius: 2px;
  cursor: zoom-in;
}

/* ═══ HOURS TABLE ═══ */
.dx-hours-table {
  width: 100%;
  border-collapse: collapse;
}
.dx-hours-table tr { border-bottom: 1px solid var(--dx-light-grey); }
.dx-hours-table tr:last-child { border-bottom: none; }
.dx-hours-table td {
  padding: 0.75rem 0;
  font-size: 0.95rem;
}
.dx-hours-table td:first-child {
  font-weight: 600;
  color: var(--dx-navy);
  width: 35%;
}
.dx-hours-table td:last-child {
  color: var(--dx-text);
  text-align: right;
}

/* ═══ MAP ═══ */
.dx-listing-map-large {
  width: 100%;
  height: 380px;
  border: 1px solid var(--dx-light-grey);
  overflow: hidden;
  border-radius: 2px;
}
.dx-listing-map-large iframe { width: 100%; height: 100%; border: 0; display: block; }

/* ═══ SIDEBAR ═══ */
.dx-listing-sidebar {
  position: sticky;
  top: 100px;
  background: var(--dx-white);
  border: 1px solid var(--dx-light-grey);
  border-radius: 2px;
  padding: 2rem;
}
.dx-listing-sidebar h2 {
  font-size: 1.5rem;
  margin: 0.25rem 0 1.25rem;
}
.dx-contact-detail {
  padding: 0.75rem 0;
  border-bottom: 1px solid var(--dx-light-grey);
}
.dx-contact-detail:last-of-type { border-bottom: none; }
.dx-detail-label {
  display: block;
  font-size: 0.7rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--dx-mid-grey);
  font-weight: 600;
  margin-bottom: 0.25rem;
}
.dx-detail-value { font-size: 0.95rem; color: var(--dx-text); line-height: 1.5; word-break: break-word; }
.dx-detail-value a { color: var(--dx-navy); text-decoration: underline; text-decoration-color: var(--dx-gold); text-underline-offset: 3px; }
.dx-detail-value a:hover { color: var(--dx-gold); }
.dx-listing-sidebar .dx-btn { display: block; text-align: center; margin-top: 1.5rem; }
.dx-sidebar-map {
  width: 100%;
  height: 200px;
  margin-top: 1.5rem;
  border: 1px solid var(--dx-light-grey);
  overflow: hidden;
}
.dx-sidebar-map iframe { width: 100%; height: 100%; border: 0; display: block; }

/* ═══ RELATED ═══ */
.dx-related-section { background: var(--dx-light-grey); }
.dx-related-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}
.dx-related-name-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin: 0.5rem 0;
}

@media (max-width: 768px) {
  .dx-listing-banner { height: 200px; }
  .dx-listing-header-inner { grid-template-columns: 1fr; padding: 2rem 1.5rem; }
  .dx-listing-logo-large,
  .dx-listing-logo-fallback { width: 72px; height: 72px; }
  .dx-listing-titles h1 { font-size: 2rem; }
  .dx-listing-layout { grid-template-columns: 1fr; gap: 2rem; padding: 2rem 1.5rem; }
  .dx-listing-sidebar { position: static; padding: 1.5rem; }
  .dx-listing-gallery { grid-template-columns: repeat(2, 1fr); }
  .dx-listing-gallery img { height: 140px; }
  .dx-listing-main h2 { font-size: 1.5rem; }
  .dx-listing-map-large { height: 280px; }
  .dx-related-grid { grid-template-columns: 1fr; }
}
</style>

<main id="primary" class="dx-single-listing">

  <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
    <nav class="dx-breadcrumbs" aria-label="Breadcrumb"><?php rank_math_the_breadcrumbs(); ?></nav>
  <?php endif; ?>

  <article>

    <!-- ═══ BANNER (featured image, premium only) ═══ -->
    <?php if ($hero_url && $is_premium) : ?>
      <div class="dx-listing-banner" style="background-image: url('<?php echo esc_url($hero_url); ?>');" aria-hidden="true">
        <div class="dx-listing-banner-overlay"></div>
      </div>
    <?php endif; ?>

    <!-- ═══ HEADER ═══ -->
    <header class="dx-listing-header-wrap">
      <div class="dx-listing-header-inner">
        <?php if ($logo_url) : ?>
          <img class="dx-listing-logo-large" src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($business_name); ?> logo">
        <?php else : ?>
          <div class="dx-listing-logo-fallback" aria-hidden="true"><?php echo esc_html(mb_substr($business_name, 0, 1)); ?></div>
        <?php endif; ?>
        <div class="dx-listing-titles">
          <div class="dx-listing-badges">
            <?php if ($verified) dx_verified_badge(); ?>
            <?php if ($is_premium) dx_tag('Premium', 'gold'); ?>
            <?php if (!$is_premium) dx_tag('Directory', 'navy'); ?>
          </div>
          <h1><?php echo esc_html($business_name); ?></h1>
          <span class="dx-listing-meta">
            <?php
              $bits = [];
              if ($district)  $bits[] = '<a href="' . esc_url(get_term_link($district)) . '">' . esc_html($district->name) . '</a>';
              if ($category)  $bits[] = '<a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a>';
              if ($price)     $bits[] = esc_html($price);
              echo implode(' &middot; ', $bits); // intentionally unescaped: bits already escaped above
            ?>
          </span>
        </div>
      </div>
    </header>

    <!-- ═══ MAIN + SIDEBAR ═══ -->
    <div class="dx-listing-layout">

      <div class="dx-listing-main">

        <?php if ($description) : ?>
          <section class="dx-listing-section dx-listing-description">
            <h2>About <?php echo esc_html($business_name); ?></h2>
            <?php echo wp_kses_post($description); ?>
          </section>
        <?php endif; ?>

        <?php if (is_array($gallery) && count($gallery)) : ?>
          <section class="dx-listing-section">
            <h2>Gallery</h2>
            <div class="dx-listing-gallery">
              <?php foreach (array_slice($gallery, 0, 9) as $img) :
                $src = $img['sizes']['large'] ?? $img['url'];
                $alt = $img['alt'] ?? $business_name;
              ?>
                <a href="<?php echo esc_url($img['url']); ?>" target="_blank" rel="noopener">
                  <img src="<?php echo esc_url($src); ?>" alt="<?php echo esc_attr($alt); ?>" loading="lazy">
                </a>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>

        <?php if (is_array($hours) && count($hours)) : ?>
          <section class="dx-listing-section">
            <h2>Opening Hours</h2>
            <table class="dx-hours-table">
              <tbody>
                <?php foreach ($hours as $row) :
                  $day   = $row['day']   ?? '';
                  $open  = $row['open']  ?? '';
                  $close = $row['close'] ?? '';
                  if (!$day) continue;
                  $time = ($open && $close) ? "{$open} - {$close}" : ($open ?: 'Closed');
                ?>
                  <tr>
                    <td><?php echo esc_html($day); ?></td>
                    <td><?php echo esc_html($time); ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </section>
        <?php endif; ?>

        <?php if ($maps_embed) : ?>
          <section class="dx-listing-section">
            <h2>Find <?php echo esc_html($business_name); ?></h2>
            <?php if ($address) : ?>
              <p style="color: var(--dx-mid-grey); margin: 0 0 1rem;"><?php echo nl2br(esc_html($address)); ?></p>
            <?php endif; ?>
            <div class="dx-listing-map-large">
              <?php echo wp_kses($maps_embed, [
                  'iframe' => [
                      'src' => true, 'width' => true, 'height' => true,
                      'frameborder' => true, 'allowfullscreen' => true,
                      'loading' => true, 'referrerpolicy' => true,
                      'style' => true, 'title' => true,
                  ],
              ]); ?>
            </div>
          </section>
        <?php endif; ?>

        <?php
        $body = get_the_content();
        if (trim(wp_strip_all_tags($body)) !== '') : ?>
          <section class="dx-listing-section dx-listing-description">
            <?php the_content(); ?>
          </section>
        <?php endif; ?>
      </div>

      <aside class="dx-listing-sidebar" aria-label="Contact details">
        <span class="section-label">Contact</span>
        <h2><?php echo esc_html($business_name); ?></h2>

        <?php if ($phone) : ?>
          <div class="dx-contact-detail">
            <span class="dx-detail-label">Phone</span>
            <span class="dx-detail-value">
              <a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $phone)); ?>"><?php echo esc_html($phone); ?></a>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($email) : ?>
          <div class="dx-contact-detail">
            <span class="dx-detail-label">Email</span>
            <span class="dx-detail-value">
              <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($address) : ?>
          <div class="dx-contact-detail">
            <span class="dx-detail-label">Address</span>
            <span class="dx-detail-value"><?php echo nl2br(esc_html($address)); ?></span>
          </div>
        <?php endif; ?>

        <?php if ($instagram) :
          $insta_url = filter_var($instagram, FILTER_VALIDATE_URL) ? $instagram : 'https://instagram.com/' . ltrim($instagram, '@');
          $insta_label = filter_var($instagram, FILTER_VALIDATE_URL) ? '@' . trim(parse_url($instagram, PHP_URL_PATH), '/') : $instagram;
        ?>
          <div class="dx-contact-detail">
            <span class="dx-detail-label">Instagram</span>
            <span class="dx-detail-value">
              <a href="<?php echo esc_url($insta_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($insta_label); ?></a>
            </span>
          </div>
        <?php endif; ?>

        <?php if ($website) : ?>
          <a href="<?php echo esc_url($website); ?>" class="dx-btn dx-btn-primary" target="_blank" rel="noopener">Visit Website →</a>
        <?php endif; ?>

        <?php if ($maps_embed && !$is_premium) : ?>
          <div class="dx-sidebar-map">
            <?php echo wp_kses($maps_embed, [
                'iframe' => [
                    'src' => true, 'width' => true, 'height' => true,
                    'frameborder' => true, 'allowfullscreen' => true,
                    'loading' => true, 'referrerpolicy' => true,
                    'style' => true, 'title' => true,
                ],
            ]); ?>
          </div>
        <?php endif; ?>

        <?php dx_render_sponsored_widget(); ?>
      </aside>
    </div>

    <!-- ═══ RELATED LISTINGS ═══ -->
    <?php
    $related_args = [
        'post_type'      => 'listing',
        'posts_per_page' => 3,
        'post__not_in'   => [get_the_ID()],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    if ($category) {
        $related_args['tax_query'] = [[
            'taxonomy' => 'business-category',
            'field'    => 'term_id',
            'terms'    => [$category->term_id],
        ]];
    }
    $related = new WP_Query($related_args);
    if ($related->have_posts()) :
    ?>
      <section class="dx-section dx-related-section" aria-labelledby="dx-related-heading">
        <?php dx_section_header('More From The Directory', 'Other ' . ($category ? $category->name . ' Businesses' : 'Listings'), 'Browse The Directory', get_post_type_archive_link('listing')); ?>
        <h2 id="dx-related-heading" class="screen-reader-text">More listings</h2>
        <div class="dx-related-grid">
          <?php while ($related->have_posts()) : $related->the_post();
            $r_logo  = get_field('business_logo');
            $r_name  = get_field('business_name') ?: get_the_title();
            $r_ver   = get_field('is_verified');
            $r_logo_url = $r_logo ? ($r_logo['sizes']['dx-listing-logo'] ?? $r_logo['url']) : '';
          ?>
            <article class="dx-card">
              <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr($r_name); ?>">
                <?php if (has_post_thumbnail()) the_post_thumbnail('dx-card', ['class' => 'dx-card-image', 'alt' => esc_attr($r_name)]); ?>
                <div class="dx-card-body">
                  <?php dx_meta_line(); ?>
                  <div class="dx-related-name-row">
                    <h3 style="margin: 0; font-size: 1.25rem;"><?php echo esc_html($r_name); ?></h3>
                    <?php if ($r_ver) dx_verified_badge(); ?>
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
   * LocalBusiness with PostalAddress, openingHoursSpecification, and sameAs.
   */
  $images = [];
  if ($logo_url)        $images[] = $logo_url;
  if ($hero_url)        $images[] = $hero_url;
  if (is_array($gallery)) {
      foreach (array_slice($gallery, 0, 6) as $g) {
          $images[] = $g['sizes']['large'] ?? $g['url'];
      }
  }
  $images = array_values(array_unique(array_filter($images)));

  $hours_spec = [];
  $day_map = [
      'mon' => 'Monday',    'monday'    => 'Monday',
      'tue' => 'Tuesday',   'tuesday'   => 'Tuesday',
      'wed' => 'Wednesday', 'wednesday' => 'Wednesday',
      'thu' => 'Thursday',  'thursday'  => 'Thursday',
      'fri' => 'Friday',    'friday'    => 'Friday',
      'sat' => 'Saturday',  'saturday'  => 'Saturday',
      'sun' => 'Sunday',    'sunday'    => 'Sunday',
  ];
  if (is_array($hours)) {
      foreach ($hours as $row) {
          $day_raw = strtolower(trim($row['day'] ?? ''));
          $day     = $day_map[$day_raw] ?? null;
          $opens   = dx_normalize_time($row['open']  ?? '');
          $closes  = dx_normalize_time($row['close'] ?? '');
          if ($day && $opens && $closes) {
              $hours_spec[] = [
                  '@type'     => 'OpeningHoursSpecification',
                  'dayOfWeek' => $day,
                  'opens'     => $opens,
                  'closes'    => $closes,
              ];
          }
      }
  }

  $same_as = [];
  if ($instagram) {
      $same_as[] = filter_var($instagram, FILTER_VALIDATE_URL)
          ? $instagram
          : 'https://instagram.com/' . ltrim($instagram, '@');
  }
  if ($website) $same_as[] = $website;

  $schema = array_filter([
      '@context'    => 'https://schema.org',
      '@type'       => 'LocalBusiness',
      '@id'         => get_permalink() . '#business',
      'name'        => $business_name,
      'description' => $description ? wp_strip_all_tags($description) : '',
      'url'         => $website ?: get_permalink(),
      'telephone'   => $phone,
      'email'       => $email,
      'priceRange'  => $price,
      'image'       => count($images) ? $images : null,
      'address'     => $address ? array_filter([
          '@type'           => 'PostalAddress',
          'streetAddress'   => trim(preg_replace('/\s+/', ' ', $address)),
          'addressLocality' => $district ? $district->name : null,
          'addressCountry'  => 'AE',
      ]) : null,
      'openingHoursSpecification' => count($hours_spec) ? $hours_spec : null,
      'sameAs'      => count($same_as) ? $same_as : null,
  ]);
  ?>
  <script type="application/ld+json"><?php echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>

</main>

<?php endwhile; get_footer();
