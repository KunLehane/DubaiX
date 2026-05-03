<?php
/**
 * Single Things To Do Template
 *
 * Card-based items list. Each item from the things_items ACF repeater
 * renders as a rich horizontal card with image, description, location,
 * price, date/time, booking link, and an optional internal link to
 * the related listing or review.
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

get_header();

while (have_posts()) : the_post();
    $relevance     = get_field('time_relevance');
    $valid_from    = get_field('valid_from');
    $valid_until   = get_field('valid_until');
    $intro         = get_field('things_intro');
    $items         = get_field('things_items');
    $is_picks      = get_field('is_editors_pick');
    $hero_url      = has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'dx-hero') : '';
    $district      = dx_get_primary_district();
    $valid         = dx_is_ttd_valid();
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
.dx-ttd-hero {
  position: relative;
  min-height: 460px;
  background-size: cover;
  background-position: center;
  background-color: var(--dx-navy);
  display: flex;
  align-items: flex-end;
}
.dx-ttd-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(rgba(26,26,46,0.3), rgba(26,26,46,0.55) 50%, rgba(26,26,46,0.92));
  pointer-events: none;
}
.dx-ttd-hero-inner {
  position: relative;
  z-index: 1;
  max-width: 1200px;
  margin: 0 auto;
  padding: 3rem 1.5rem;
  width: 100%;
}
.dx-ttd-hero h1 {
  color: var(--dx-warm-white);
  font-size: 4rem;
  margin: 1rem 0 0.75rem;
  line-height: 1.05;
  max-width: 900px;
}
.dx-ttd-hero .section-label { color: var(--dx-gold); }
.dx-ttd-hero-tags {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-bottom: 0.5rem;
}
.dx-ttd-hero-meta {
  font-size: 0.85rem;
  color: rgba(255,255,255,0.75);
  letter-spacing: 0.06em;
}
.dx-ttd-expired-banner {
  background: var(--dx-mid-grey);
  color: var(--dx-warm-white);
  text-align: center;
  padding: 0.75rem 1.5rem;
  font-size: 0.8rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  font-weight: 600;
}

/* ═══ INTRO ═══ */
.dx-ttd-intro {
  max-width: 760px;
  margin: 0 auto;
  padding: 4rem 1.5rem 2rem;
}
.dx-ttd-intro p {
  font-size: 1.125rem;
  line-height: 1.8;
}
.dx-ttd-intro p:first-of-type::first-letter {
  font-family: 'Cormorant Garamond', serif;
  font-size: 4.5rem;
  float: left;
  line-height: 0.9;
  margin: 0.4rem 0.6rem 0 0;
  color: var(--dx-gold);
  font-weight: 600;
}

/* ═══ ITEMS LIST ═══ */
.dx-ttd-items {
  max-width: 1000px;
  margin: 0 auto;
  padding: 2rem 1.5rem 4rem;
  display: flex;
  flex-direction: column;
  gap: 2rem;
}
.dx-ttd-item {
  background: var(--dx-white);
  border: 1px solid var(--dx-light-grey);
  border-radius: 2px;
  display: grid;
  grid-template-columns: 5fr 7fr;
  overflow: hidden;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.dx-ttd-item:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 40px rgba(0,0,0,0.06);
}
.dx-ttd-item-img {
  width: 100%;
  height: 100%;
  min-height: 280px;
  object-fit: cover;
}
.dx-ttd-item-img-fallback {
  width: 100%;
  min-height: 280px;
  background: var(--dx-light-grey);
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'Cormorant Garamond', serif;
  font-size: 5rem;
  color: var(--dx-gold);
  font-weight: 600;
}
.dx-ttd-item-body {
  padding: 2rem;
  display: flex;
  flex-direction: column;
}
.dx-ttd-item-number {
  font-family: 'Cormorant Garamond', serif;
  color: var(--dx-gold);
  font-size: 1.625rem;
  font-weight: 700;
  line-height: 1;
  margin-bottom: 0.5rem;
}
.dx-ttd-item-body h3 {
  font-size: 1.75rem;
  margin: 0 0 0.5rem;
  line-height: 1.2;
}
.dx-ttd-item-meta {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem 1.25rem;
  font-size: 0.85rem;
  color: var(--dx-mid-grey);
  margin: 0.5rem 0 1rem;
}
.dx-ttd-item-meta span {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
}
.dx-ttd-item-meta svg { color: var(--dx-gold); flex-shrink: 0; }
.dx-ttd-item-desc {
  color: var(--dx-text);
  font-size: 0.95rem;
  line-height: 1.7;
  margin: 0 0 1.25rem;
  flex: 1;
}
.dx-ttd-item-actions {
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
  align-items: center;
}
.dx-ttd-related-link {
  font-size: 0.8rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--dx-navy);
  font-weight: 600;
  text-decoration: underline;
  text-decoration-color: var(--dx-gold);
  text-underline-offset: 4px;
}
.dx-ttd-related-link:hover { color: var(--dx-gold); }

/* ═══ RELATED ═══ */
.dx-related-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

@media (max-width: 768px) {
  .dx-ttd-hero { min-height: 360px; }
  .dx-ttd-hero h1 { font-size: 2.5rem; }
  .dx-ttd-item { grid-template-columns: 1fr; }
  .dx-ttd-item-img,
  .dx-ttd-item-img-fallback { min-height: 220px; height: auto; }
  .dx-ttd-item-body { padding: 1.5rem; }
  .dx-ttd-item-body h3 { font-size: 1.375rem; }
  .dx-related-grid { grid-template-columns: 1fr; }
}
</style>

<main id="primary" class="dx-single-things-to-do">

  <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
    <nav class="dx-breadcrumbs" aria-label="Breadcrumb"><?php rank_math_the_breadcrumbs(); ?></nav>
  <?php endif; ?>

  <article>

    <!-- ═══ HERO ═══ -->
    <header class="dx-ttd-hero" <?php if ($hero_url) : ?>style="background-image: url('<?php echo esc_url($hero_url); ?>');"<?php endif; ?>>
      <div class="dx-ttd-hero-overlay" aria-hidden="true"></div>
      <div class="dx-ttd-hero-inner">
        <span class="section-label">Things To Do</span>
        <div class="dx-ttd-hero-tags" style="margin-top: 0.5rem;">
          <?php if ($relevance) dx_tag($relevance, 'gold'); ?>
          <?php if ($is_picks) dx_tag("Editor's Pick", 'navy'); ?>
        </div>
        <h1><?php the_title(); ?></h1>
        <span class="dx-ttd-hero-meta">
          <?php
            $bits = [];
            if ($district) $bits[] = $district->name;
            if ($valid_from && $valid_until) {
                $bits[] = date_i18n('j M', strtotime($valid_from)) . ' to ' . date_i18n('j M Y', strtotime($valid_until));
            } elseif ($valid_until) {
                $bits[] = 'Valid until ' . date_i18n('j M Y', strtotime($valid_until));
            }
            echo esc_html(implode(' - ', $bits));
          ?>
        </span>
      </div>
    </header>

    <?php if (!$valid) : ?>
      <div class="dx-ttd-expired-banner" role="status">This roundup has expired. The picks below may no longer be available.</div>
    <?php endif; ?>

    <!-- ═══ INTRO ═══ -->
    <?php if ($intro) : ?>
      <section class="dx-ttd-intro">
        <?php echo wp_kses_post($intro); ?>
      </section>
    <?php endif; ?>

    <!-- ═══ ITEMS ═══ -->
    <?php if (is_array($items) && count($items)) : ?>
      <section class="dx-ttd-items" aria-labelledby="dx-items-heading">
        <h2 id="dx-items-heading" class="screen-reader-text">The picks</h2>
        <?php foreach ($items as $i => $item) :
          if (empty($item['item_title'])) continue;
          $img       = $item['item_image'] ?? null;
          $img_url   = $img ? ($img['sizes']['dx-card'] ?? $img['url']) : '';
          $img_alt   = $img['alt'] ?? ($item['item_title'] ?? '');
          $title     = $item['item_title']        ?? '';
          $desc      = $item['item_description']  ?? '';
          $location  = $item['item_location']     ?? '';
          $price     = $item['item_price']        ?? '';
          $datetime  = $item['item_date_time']    ?? '';
          $booking   = $item['item_booking_link'] ?? '';
          $rel_list  = $item['related_listing']   ?? null;
          $rel_rev   = $item['related_review']    ?? null;
          $rel_post  = is_array($rel_list) && count($rel_list) ? $rel_list[0] : (is_array($rel_rev) && count($rel_rev) ? $rel_rev[0] : null);
          $rel_label = is_array($rel_list) && count($rel_list) ? 'View Listing' : 'Read Review';
        ?>
          <article class="dx-ttd-item">
            <?php if ($img_url) : ?>
              <img class="dx-ttd-item-img" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy">
            <?php else : ?>
              <div class="dx-ttd-item-img-fallback" aria-hidden="true"><?php echo esc_html(str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT)); ?></div>
            <?php endif; ?>

            <div class="dx-ttd-item-body">
              <span class="dx-ttd-item-number"><?php echo esc_html(str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
              <h3><?php echo esc_html($title); ?></h3>

              <?php if ($location || $price || $datetime) : ?>
                <div class="dx-ttd-item-meta">
                  <?php if ($location) : ?>
                    <span>
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s7-7 7-13a7 7 0 0 0-14 0c0 6 7 13 7 13z"/><circle cx="12" cy="9" r="2.5"/></svg>
                      <?php echo esc_html($location); ?>
                    </span>
                  <?php endif; ?>
                  <?php if ($datetime) : ?>
                    <span>
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                      <?php echo esc_html($datetime); ?>
                    </span>
                  <?php endif; ?>
                  <?php if ($price) : ?>
                    <span>
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                      <?php echo esc_html($price); ?>
                    </span>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

              <?php if ($desc) : ?>
                <div class="dx-ttd-item-desc"><?php echo wp_kses_post($desc); ?></div>
              <?php endif; ?>

              <?php if ($booking || $rel_post) : ?>
                <div class="dx-ttd-item-actions">
                  <?php if ($booking) : ?>
                    <a href="<?php echo esc_url($booking); ?>" class="dx-btn dx-btn-primary" target="_blank" rel="noopener">Book Now</a>
                  <?php endif; ?>
                  <?php if ($rel_post) : ?>
                    <a href="<?php echo esc_url(get_permalink($rel_post)); ?>" class="dx-ttd-related-link"><?php echo esc_html($rel_label); ?> →</a>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>

    <!-- ═══ POST CONTENT (any extra editor content) ═══ -->
    <?php
    $body = get_the_content();
    if (trim(wp_strip_all_tags($body)) !== '') : ?>
      <section class="dx-ttd-intro">
        <?php the_content(); ?>
      </section>
    <?php endif; ?>

    <!-- ═══ SPONSORED FEATURE ═══ -->
    <section class="dx-sponsored-section"><?php dx_render_sponsored_widget(); ?></section>

    <!-- ═══ RELATED ═══ -->
    <?php
    $related = new WP_Query([
        'post_type'      => 'things-to-do',
        'posts_per_page' => 3,
        'post__not_in'   => [get_the_ID()],
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => [
            'relation' => 'OR',
            ['key' => 'valid_until', 'value' => date('Y-m-d'), 'compare' => '>=', 'type' => 'DATE'],
            ['key' => 'valid_until', 'compare' => 'NOT EXISTS'],
        ],
    ]);
    if ($related->have_posts()) :
    ?>
      <section class="dx-section dx-related-section" aria-labelledby="dx-related-heading">
        <?php dx_section_header('Also On Now', 'More To Do', 'See Everything', get_post_type_archive_link('things-to-do')); ?>
        <h2 id="dx-related-heading" class="screen-reader-text">More things to do</h2>
        <div class="dx-related-grid">
          <?php while ($related->have_posts()) : $related->the_post();
            $r_relevance = get_field('time_relevance');
          ?>
            <article class="dx-card">
              <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                <?php if (has_post_thumbnail()) the_post_thumbnail('dx-card', ['class' => 'dx-card-image', 'alt' => esc_attr(get_the_title())]); ?>
                <div class="dx-card-body">
                  <?php if ($r_relevance) dx_tag($r_relevance, 'gold'); ?>
                  <h3 style="margin: 0.5rem 0; font-size: 1.25rem;"><?php the_title(); ?></h3>
                  <?php dx_meta_line(); ?>
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
   * ItemList of Events. Each item gets schema.org Event with location +
   * date + offer (price). Falls back gracefully when fields are empty.
   */
  $event_items = [];
  if (is_array($items)) {
      foreach ($items as $i => $item) {
          if (empty($item['item_title'])) continue;
          $img = $item['item_image'] ?? null;
          $event = array_filter([
              '@type'      => 'Event',
              'name'       => $item['item_title'],
              'description'=> !empty($item['item_description']) ? wp_strip_all_tags($item['item_description']) : null,
              'image'      => $img ? ($img['sizes']['large'] ?? $img['url']) : null,
              'location'   => !empty($item['item_location']) ? [
                  '@type' => 'Place',
                  'name'  => $item['item_location'],
                  'address' => $item['item_location'],
              ] : null,
              'startDate'  => $valid_from  ?: null,
              'endDate'    => $valid_until ?: null,
              'url'        => !empty($item['item_booking_link']) ? $item['item_booking_link'] : null,
          ]);
          if (!empty($item['item_price'])) {
              $event['offers'] = [
                  '@type'         => 'Offer',
                  'price'         => preg_replace('/[^0-9.]/', '', $item['item_price']) ?: '0',
                  'priceCurrency' => 'AED',
                  'description'   => $item['item_price'],
              ];
          }
          $event_items[] = [
              '@type'    => 'ListItem',
              'position' => $i + 1,
              'item'     => $event,
          ];
      }
  }

  $schema = array_filter([
      '@context'        => 'https://schema.org',
      '@type'           => 'ItemList',
      '@id'             => get_permalink() . '#list',
      'name'            => get_the_title(),
      'description'     => $intro ? wp_trim_words(wp_strip_all_tags($intro), 30) : '',
      'numberOfItems'   => count($event_items),
      'itemListElement' => count($event_items) ? $event_items : null,
  ]);
  ?>
  <script type="application/ld+json"><?php echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>

</main>

<?php endwhile; get_footer();
