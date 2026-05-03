<?php
/**
 * Single Best List Template
 *
 * Editorial numbered list. Big serif numbers, full-width entries with
 * image left + content right, highlight callout per pick, methodology
 * box up top, and an in-page table of contents anchor list.
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

get_header();

while (have_posts()) : the_post();
    $intro       = get_field('list_intro');
    $methodology = get_field('list_methodology');
    $items       = get_field('list_items');
    $hero_url    = has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'dx-hero') : '';
    $district    = dx_get_primary_district();
    $category    = dx_get_primary_category();
    $count       = is_array($items) ? count($items) : 0;
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
.dx-bl-hero {
  position: relative;
  min-height: 480px;
  background-size: cover;
  background-position: center;
  background-color: var(--dx-navy);
  display: flex;
  align-items: flex-end;
  text-align: center;
}
.dx-bl-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(rgba(26,26,46,0.3), rgba(26,26,46,0.55) 50%, rgba(26,26,46,0.92));
  pointer-events: none;
}
.dx-bl-hero-inner {
  position: relative;
  z-index: 1;
  max-width: 900px;
  margin: 0 auto;
  padding: 4rem 1.5rem;
  width: 100%;
}
.dx-bl-hero h1 {
  color: var(--dx-warm-white);
  font-size: 4.5rem;
  margin: 1rem 0 0.75rem;
  line-height: 1.05;
}
.dx-bl-hero .section-label { color: var(--dx-gold); }
.dx-bl-hero-meta {
  font-size: 0.85rem;
  color: rgba(255,255,255,0.75);
  letter-spacing: 0.06em;
}
.dx-bl-count-tag {
  display: inline-block;
  margin-top: 1rem;
  background: var(--dx-gold);
  color: var(--dx-navy);
  padding: 0.5rem 1rem;
  font-size: 0.7rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  font-weight: 700;
  border-radius: 2px;
}

/* ═══ INTRO ═══ */
.dx-bl-intro {
  max-width: 760px;
  margin: 0 auto;
  padding: 4rem 1.5rem 1rem;
}
.dx-bl-intro p {
  font-size: 1.125rem;
  line-height: 1.8;
}
.dx-bl-intro p:first-of-type::first-letter {
  font-family: 'Cormorant Garamond', serif;
  font-size: 4.5rem;
  float: left;
  line-height: 0.9;
  margin: 0.4rem 0.6rem 0 0;
  color: var(--dx-gold);
  font-weight: 600;
}

/* ═══ METHODOLOGY ═══ */
.dx-bl-methodology {
  max-width: 760px;
  margin: 0 auto 3rem;
  padding: 0 1.5rem;
}
.dx-bl-methodology-box {
  background: var(--dx-warm-white);
  border-left: 3px solid var(--dx-gold);
  padding: 1.5rem 1.75rem;
}
.dx-bl-methodology-box h2 {
  font-size: 0.85rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--dx-gold);
  font-family: 'DM Sans', sans-serif;
  font-weight: 700;
  margin: 0 0 0.5rem;
}
.dx-bl-methodology-box p { margin: 0; font-size: 0.95rem; line-height: 1.7; color: var(--dx-text); }

/* ═══ TABLE OF CONTENTS ═══ */
.dx-bl-toc {
  max-width: 760px;
  margin: 0 auto 4rem;
  padding: 0 1.5rem;
}
.dx-bl-toc-inner {
  background: var(--dx-white);
  border: 1px solid var(--dx-light-grey);
  padding: 1.75rem 2rem;
  border-radius: 2px;
}
.dx-bl-toc-inner h2 {
  font-size: 0.85rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--dx-mid-grey);
  font-family: 'DM Sans', sans-serif;
  font-weight: 700;
  margin: 0 0 1rem;
}
.dx-bl-toc-list {
  list-style: none;
  padding: 0;
  margin: 0;
  columns: 2;
  column-gap: 2rem;
}
.dx-bl-toc-list li {
  break-inside: avoid;
  padding: 0.4rem 0;
  font-size: 0.95rem;
}
.dx-bl-toc-list a { color: var(--dx-navy); }
.dx-bl-toc-list a:hover { color: var(--dx-gold); }
.dx-bl-toc-num {
  display: inline-block;
  font-family: 'Cormorant Garamond', serif;
  color: var(--dx-gold);
  font-weight: 700;
  margin-right: 0.5rem;
  min-width: 1.5rem;
}

/* ═══ ITEMS ═══ */
.dx-bl-items { background: var(--dx-warm-white); padding: 2rem 0 4rem; }
.dx-bl-item {
  max-width: 1100px;
  margin: 0 auto;
  padding: 4rem 1.5rem;
  display: grid;
  grid-template-columns: 5fr 7fr;
  gap: 3rem;
  align-items: center;
  border-bottom: 1px solid var(--dx-light-grey);
}
.dx-bl-item:last-child { border-bottom: none; }
.dx-bl-item-img {
  width: 100%;
  height: 420px;
  object-fit: cover;
  border-radius: 2px;
}
.dx-bl-item-img-fallback {
  width: 100%;
  height: 420px;
  background: var(--dx-light-grey);
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 2px;
  font-family: 'Cormorant Garamond', serif;
  font-size: 7rem;
  color: var(--dx-gold);
  font-weight: 600;
}
.dx-bl-item-number {
  font-family: 'Cormorant Garamond', serif;
  font-size: 5rem;
  color: var(--dx-gold);
  font-weight: 700;
  line-height: 0.9;
  margin: 0 0 0.5rem;
}
.dx-bl-item-title {
  font-size: 2.5rem;
  margin: 0 0 0.5rem;
  line-height: 1.15;
}
.dx-bl-item-title a { color: var(--dx-navy); }
.dx-bl-item-title a:hover { color: var(--dx-gold); }
.dx-bl-item-meta {
  font-size: 0.85rem;
  color: var(--dx-mid-grey);
  letter-spacing: 0.05em;
  margin-bottom: 1rem;
}
.dx-bl-item-desc {
  font-size: 1.0625rem;
  line-height: 1.75;
  color: var(--dx-text);
  margin: 1rem 0;
}
.dx-bl-item-highlight {
  background: var(--dx-white);
  border: 1px solid var(--dx-light-grey);
  padding: 0.85rem 1.25rem;
  margin: 1.25rem 0;
  font-size: 0.95rem;
  border-left: 3px solid var(--dx-gold);
}
.dx-bl-item-highlight-label {
  font-size: 0.7rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--dx-gold);
  font-weight: 700;
  margin-right: 0.5rem;
}
.dx-bl-item-cta {
  font-size: 0.8rem;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--dx-navy);
  font-weight: 600;
  text-decoration: underline;
  text-decoration-color: var(--dx-gold);
  text-underline-offset: 4px;
  margin-top: 1rem;
  display: inline-block;
}
.dx-bl-item-cta:hover { color: var(--dx-gold); }

/* ═══ RELATED ═══ */
.dx-related-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

@media (max-width: 768px) {
  .dx-bl-hero { min-height: 380px; }
  .dx-bl-hero h1 { font-size: 2.75rem; }
  .dx-bl-toc-list { columns: 1; }
  .dx-bl-item {
    grid-template-columns: 1fr;
    gap: 1.5rem;
    padding: 3rem 1.5rem;
  }
  .dx-bl-item-img,
  .dx-bl-item-img-fallback { height: 240px; }
  .dx-bl-item-img-fallback { font-size: 4.5rem; }
  .dx-bl-item-number { font-size: 3.5rem; }
  .dx-bl-item-title { font-size: 1.75rem; }
  .dx-related-grid { grid-template-columns: 1fr; }
}
</style>

<main id="primary" class="dx-single-best-list">

  <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
    <nav class="dx-breadcrumbs" aria-label="Breadcrumb"><?php rank_math_the_breadcrumbs(); ?></nav>
  <?php endif; ?>

  <article>

    <!-- ═══ HERO ═══ -->
    <header class="dx-bl-hero" <?php if ($hero_url) : ?>style="background-image: url('<?php echo esc_url($hero_url); ?>');"<?php endif; ?>>
      <div class="dx-bl-hero-overlay" aria-hidden="true"></div>
      <div class="dx-bl-hero-inner">
        <span class="section-label">Best Of Dubai</span>
        <h1><?php the_title(); ?></h1>
        <span class="dx-bl-hero-meta">
          <?php
            $bits = [];
            if ($district) $bits[] = $district->name;
            if ($category) $bits[] = $category->name;
            echo esc_html(implode(' - ', $bits));
          ?>
        </span>
        <?php if ($count) : ?>
          <div><span class="dx-bl-count-tag"><?php echo esc_html($count); ?> Picks</span></div>
        <?php endif; ?>
      </div>
    </header>

    <!-- ═══ INTRO ═══ -->
    <?php if ($intro) :
      // Imported intros came from posts that wrapped the first item heading
      // in <ol><li><h2>...</h2></li></ol>. The parser kept the opening
      // <ol><li> with the intro and the closer with the first item, leaving
      // a stray empty list item. Strip these orphan tags + any broken images.
      $intro_clean = preg_replace('/<img\b[^>]*>/i',                  '', $intro);
      $intro_clean = preg_replace('/<a\b[^>]*>\s*<\/a>/i',             '', $intro_clean);
      $intro_clean = preg_replace('/<\/?(?:ol|ul|li)\b[^>]*>/i',       '', $intro_clean);
      // Drop the now-empty paragraphs and stray &nbsp; left behind
      $intro_clean = preg_replace('/<p>\s*(&nbsp;)?\s*<\/p>/i',        '', $intro_clean);
    ?>
      <section class="dx-bl-intro">
        <?php echo wp_kses_post($intro_clean); ?>
      </section>
    <?php endif; ?>

    <!-- ═══ METHODOLOGY ═══ -->
    <?php if ($methodology) : ?>
      <aside class="dx-bl-methodology" aria-labelledby="dx-methodology-heading">
        <div class="dx-bl-methodology-box">
          <h2 id="dx-methodology-heading">How We Chose These</h2>
          <p><?php echo esc_html($methodology); ?></p>
        </div>
      </aside>
    <?php endif; ?>

    <!-- ═══ TABLE OF CONTENTS ═══ -->
    <?php if (is_array($items) && count($items) > 3) : ?>
      <nav class="dx-bl-toc" aria-labelledby="dx-toc-heading">
        <div class="dx-bl-toc-inner">
          <h2 id="dx-toc-heading">In This List</h2>
          <ol class="dx-bl-toc-list">
            <?php foreach ($items as $i => $item) :
              if (empty($item['business_name'])) continue;
            ?>
              <li>
                <a href="#pick-<?php echo (int) ($i + 1); ?>">
                  <span class="dx-bl-toc-num"><?php echo esc_html(str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
                  <?php echo esc_html($item['business_name']); ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ol>
        </div>
      </nav>
    <?php endif; ?>

    <!-- ═══ ITEMS ═══ -->
    <?php if (is_array($items) && count($items)) : ?>
      <div class="dx-bl-items">
        <?php foreach ($items as $i => $item) :
          if (empty($item['business_name'])) continue;
          $img       = $item['featured_image'] ?? null;
          $img_url   = $img ? ($img['sizes']['large'] ?? $img['url']) : '';
          $img_alt   = $img['alt'] ?? $item['business_name'];
          $name      = $item['business_name']     ?? '';
          $link      = $item['business_link']     ?? '';
          $desc      = $item['short_description'] ?? '';
          $location  = $item['location']          ?? '';
          $price     = $item['price_range']       ?? '';
          $highlight = $item['highlight']         ?? '';
          $num       = str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT);
        ?>
          <article class="dx-bl-item" id="pick-<?php echo (int) ($i + 1); ?>">
            <?php if ($img_url) : ?>
              <img class="dx-bl-item-img" src="<?php echo esc_url($img_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" loading="lazy">
            <?php else : ?>
              <div class="dx-bl-item-img-fallback" aria-hidden="true"><?php echo esc_html($num); ?></div>
            <?php endif; ?>

            <div class="dx-bl-item-content">
              <div class="dx-bl-item-number" aria-hidden="true"><?php echo esc_html($num); ?></div>
              <h2 class="dx-bl-item-title">
                <?php if ($link) : ?>
                  <a href="<?php echo esc_url($link); ?>"><?php echo esc_html($name); ?></a>
                <?php else : ?>
                  <?php echo esc_html($name); ?>
                <?php endif; ?>
              </h2>

              <?php if ($location || $price) : ?>
                <div class="dx-bl-item-meta">
                  <?php
                    $bits = [];
                    if ($location) $bits[] = $location;
                    if ($price)    $bits[] = $price;
                    echo esc_html(implode(' - ', $bits));
                  ?>
                </div>
              <?php endif; ?>

              <?php if ($desc) : ?>
                <p class="dx-bl-item-desc"><?php echo esc_html($desc); ?></p>
              <?php endif; ?>

              <?php if ($highlight) : ?>
                <div class="dx-bl-item-highlight">
                  <span class="dx-bl-item-highlight-label">Best for</span>
                  <?php echo esc_html($highlight); ?>
                </div>
              <?php endif; ?>

              <?php if ($link) : ?>
                <a href="<?php echo esc_url($link); ?>" class="dx-bl-item-cta">Read More →</a>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <!-- ═══ POST CONTENT (fallback only - hidden when list_items is populated) ═══ -->
    <?php
    $body = get_the_content();
    if (!$count && trim(wp_strip_all_tags($body)) !== '') : ?>
      <section class="dx-bl-intro">
        <?php the_content(); ?>
      </section>
    <?php endif; ?>

    <!-- ═══ SPONSORED FEATURE ═══ -->
    <section class="dx-sponsored-section"><?php dx_render_sponsored_widget(); ?></section>

    <!-- ═══ RELATED LISTS ═══ -->
    <?php
    $related = new WP_Query([
        'post_type'      => 'best-list',
        'posts_per_page' => 3,
        'post__not_in'   => [get_the_ID()],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
    if ($related->have_posts()) :
    ?>
      <section class="dx-section dx-related-section" aria-labelledby="dx-related-heading">
        <?php dx_section_header('More Best Lists', 'You Might Also Like', 'All Best Lists', get_post_type_archive_link('best-list')); ?>
        <h2 id="dx-related-heading" class="screen-reader-text">More best lists</h2>
        <div class="dx-related-grid">
          <?php while ($related->have_posts()) : $related->the_post();
            $r_items = get_field('list_items');
            $r_count = is_array($r_items) ? count($r_items) : 0;
          ?>
            <article class="dx-card">
              <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
                <?php if (has_post_thumbnail()) the_post_thumbnail('dx-card', ['class' => 'dx-card-image', 'alt' => esc_attr(get_the_title())]); ?>
                <div class="dx-card-body">
                  <?php if ($r_count) dx_tag($r_count . ' Picks', 'gold'); ?>
                  <h3 style="margin: 0.5rem 0; font-size: 1.25rem;"><?php the_title(); ?></h3>
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

  </article>

  <?php
  /**
   * ═══ JSON-LD SCHEMA ═══
   * ItemList with each business as a ListItem -> LocalBusiness.
   */
  $list_items = [];
  if (is_array($items)) {
      foreach ($items as $i => $item) {
          if (empty($item['business_name'])) continue;
          $img = $item['featured_image'] ?? null;
          $list_items[] = [
              '@type'    => 'ListItem',
              'position' => $i + 1,
              'item'     => array_filter([
                  '@type'      => 'LocalBusiness',
                  'name'       => $item['business_name'],
                  'description'=> !empty($item['short_description']) ? $item['short_description'] : null,
                  'image'      => $img ? ($img['sizes']['large'] ?? $img['url']) : null,
                  'url'        => !empty($item['business_link']) ? $item['business_link'] : null,
                  'address'    => !empty($item['location']) ? $item['location'] : null,
                  'priceRange' => !empty($item['price_range']) ? $item['price_range'] : null,
              ]),
          ];
      }
  }

  $schema = array_filter([
      '@context'        => 'https://schema.org',
      '@type'           => 'ItemList',
      '@id'             => get_permalink() . '#list',
      'name'            => get_the_title(),
      'description'     => $intro ? wp_trim_words(wp_strip_all_tags($intro), 30) : '',
      'numberOfItems'   => count($list_items),
      'itemListOrder'   => 'https://schema.org/ItemListOrderAscending',
      'itemListElement' => count($list_items) ? $list_items : null,
  ]);
  ?>
  <script type="application/ld+json"><?php echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>

</main>

<?php endwhile; get_footer();
