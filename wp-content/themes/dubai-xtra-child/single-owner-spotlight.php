<?php
/**
 * Single Owner Spotlight Template
 *
 * Interview format. Split hero (photo + intro), full-bleed pull quote,
 * Q&A from the qa_sections ACF repeater, optional YouTube embed,
 * key takeaway, and related spotlights.
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

/**
 * Extract a YouTube video ID from any of the common URL formats.
 */
if (!function_exists('dx_youtube_id')) {
    function dx_youtube_id($url) {
        if (!$url) return '';
        if (preg_match('~(?:youtube\.com/(?:watch\?v=|embed/|v/|shorts/)|youtu\.be/)([A-Za-z0-9_-]{11})~', $url, $m)) {
            return $m[1];
        }
        return '';
    }
}

get_header();

while (have_posts()) : the_post();
    $owner_name   = get_field('owner_name') ?: get_the_title();
    $business     = get_field('business_name');
    $photo        = get_field('owner_photo');
    $intro        = get_field('spotlight_intro');
    $qa           = get_field('qa_sections');
    $pull_quote   = get_field('pull_quote');
    $youtube      = get_field('youtube_embed');
    $website      = get_field('website');
    $instagram    = get_field('instagram_handle');
    $takeaway     = get_field('key_takeaway');
    $youtube_id   = dx_youtube_id($youtube);
    $photo_url    = $photo ? ($photo['sizes']['dx-hero'] ?? $photo['url']) : (has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'dx-hero') : '');
    $district     = dx_get_primary_district();
    $category     = dx_get_primary_category();
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

/* ═══ HERO (split: photo + intro) ═══ */
.dx-os-hero {
  background: var(--dx-warm-white);
  padding: 4rem 0;
  border-bottom: 1px solid var(--dx-light-grey);
}
.dx-os-hero-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 4rem;
  align-items: center;
}
.dx-os-photo {
  width: 100%;
  height: 540px;
  object-fit: cover;
  border-radius: 2px;
}
.dx-os-business {
  font-size: 0.75rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--dx-gold);
  font-weight: 600;
}
.dx-os-hero h1 {
  font-size: 4rem;
  margin: 1rem 0;
  line-height: 1.05;
}
.dx-os-tagline {
  font-family: 'Cormorant Garamond', serif;
  font-style: italic;
  font-size: 1.5rem;
  color: var(--dx-mid-grey);
  margin: 0.5rem 0 1.5rem;
  line-height: 1.4;
}
.dx-os-meta {
  font-size: 0.85rem;
  color: var(--dx-mid-grey);
  letter-spacing: 0.05em;
}
.dx-os-meta a { color: var(--dx-mid-grey); }
.dx-os-meta a:hover { color: var(--dx-gold); }
.dx-os-links {
  display: flex;
  gap: 1.25rem;
  flex-wrap: wrap;
  margin-top: 0.75rem;
}
.dx-os-links a {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.9rem;
  color: var(--dx-navy);
  text-decoration: underline;
  text-decoration-color: var(--dx-gold);
  text-underline-offset: 3px;
}
.dx-os-links a:hover { color: var(--dx-gold); }

/* ═══ INTRO BLOCK ═══ */
.dx-os-intro {
  max-width: 760px;
  margin: 0 auto;
  padding: 4rem 1.5rem 2rem;
}
.dx-os-intro p {
  font-size: 1.125rem;
  line-height: 1.8;
  color: var(--dx-text);
}
.dx-os-intro p:first-of-type::first-letter {
  font-family: 'Cormorant Garamond', serif;
  font-size: 4.5rem;
  float: left;
  line-height: 0.9;
  margin: 0.4rem 0.6rem 0 0;
  color: var(--dx-gold);
  font-weight: 600;
}

/* ═══ PULL QUOTE ═══ */
.dx-os-quote-wrap {
  background: var(--dx-navy);
  padding: 5rem 1.5rem;
  text-align: center;
}
.dx-os-quote {
  max-width: 920px;
  margin: 0 auto;
  font-family: 'Cormorant Garamond', serif;
  font-size: 2.5rem;
  font-style: italic;
  color: var(--dx-warm-white);
  line-height: 1.35;
  font-weight: 500;
}
.dx-os-quote::before { content: "\201C"; color: var(--dx-gold); margin-right: 0.25rem; }
.dx-os-quote::after  { content: "\201D"; color: var(--dx-gold); margin-left: 0.25rem; }
.dx-os-quote-attrib {
  margin-top: 1.5rem;
  font-size: 0.7rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--dx-gold);
  font-weight: 600;
}

/* ═══ Q&A ═══ */
.dx-os-qa-section {
  max-width: 800px;
  margin: 0 auto;
  padding: 4rem 1.5rem;
}
.dx-os-qa-heading {
  font-size: 2.25rem;
  margin: 0 0 2.5rem;
  text-align: center;
}
.dx-qa-item {
  margin-bottom: 2.5rem;
  padding-bottom: 2.5rem;
  border-bottom: 1px solid var(--dx-light-grey);
}
.dx-qa-item:last-child { border-bottom: none; padding-bottom: 0; }
.dx-qa-question {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.625rem;
  color: var(--dx-navy);
  font-weight: 600;
  line-height: 1.3;
  margin: 0 0 1rem;
  padding-left: 2.5rem;
  position: relative;
}
.dx-qa-question::before {
  content: "Q.";
  position: absolute;
  left: 0;
  top: 0;
  font-family: 'Cormorant Garamond', serif;
  color: var(--dx-gold);
  font-weight: 700;
  font-size: 1.875rem;
  line-height: 1;
}
.dx-qa-answer {
  padding-left: 2.5rem;
  position: relative;
  color: var(--dx-text);
  font-size: 1.0625rem;
  line-height: 1.8;
}
.dx-qa-answer::before {
  content: "A.";
  position: absolute;
  left: 0;
  top: 0;
  font-family: 'Cormorant Garamond', serif;
  color: var(--dx-mid-grey);
  font-weight: 700;
  font-size: 1.5rem;
  line-height: 1.1;
}
.dx-qa-answer p:first-child { margin-top: 0; }
.dx-qa-answer p:last-child { margin-bottom: 0; }

/* ═══ YOUTUBE EMBED ═══ */
.dx-os-video {
  max-width: 960px;
  margin: 2rem auto 4rem;
  padding: 0 1.5rem;
}
.dx-os-video-wrap {
  position: relative;
  padding-bottom: 56.25%;
  height: 0;
  overflow: hidden;
  background: var(--dx-navy);
  border-radius: 2px;
}
.dx-os-video-wrap iframe {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  border: 0;
}

/* ═══ KEY TAKEAWAY ═══ */
.dx-os-takeaway-section { background: var(--dx-warm-white); border-top: 1px solid var(--dx-light-grey); border-bottom: 1px solid var(--dx-light-grey); }
.dx-os-takeaway {
  max-width: 800px;
  margin: 0 auto;
  padding: 4rem 1.5rem;
  text-align: center;
}
.dx-os-takeaway h2 {
  font-size: 1rem;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--dx-gold);
  font-family: 'DM Sans', sans-serif;
  font-weight: 700;
  margin: 0 0 1rem;
}
.dx-os-takeaway p {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.875rem;
  line-height: 1.4;
  color: var(--dx-navy);
  margin: 0;
  font-weight: 500;
}

/* ═══ RELATED ═══ */
.dx-related-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}

@media (max-width: 768px) {
  .dx-os-hero { padding: 2.5rem 0; }
  .dx-os-hero-inner { grid-template-columns: 1fr; gap: 2rem; }
  .dx-os-photo { height: 320px; }
  .dx-os-hero h1 { font-size: 2.5rem; }
  .dx-os-tagline { font-size: 1.25rem; }
  .dx-os-quote { font-size: 1.5rem; }
  .dx-os-quote-wrap { padding: 3rem 1.5rem; }
  .dx-os-qa-heading { font-size: 1.75rem; }
  .dx-qa-question { font-size: 1.375rem; }
  .dx-os-takeaway p { font-size: 1.5rem; }
  .dx-related-grid { grid-template-columns: 1fr; }
}
</style>

<main id="primary" class="dx-single-owner-spotlight">

  <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
    <nav class="dx-breadcrumbs" aria-label="Breadcrumb"><?php rank_math_the_breadcrumbs(); ?></nav>
  <?php endif; ?>

  <article>

    <!-- ═══ HERO (split layout) ═══ -->
    <header class="dx-os-hero">
      <div class="dx-os-hero-inner">
        <?php if ($photo_url) : ?>
          <img class="dx-os-photo" src="<?php echo esc_url($photo_url); ?>" alt="<?php echo esc_attr($owner_name); ?>">
        <?php endif; ?>
        <div>
          <span class="section-label">Owner Spotlight</span>
          <?php if ($business) : ?>
            <div class="dx-os-business" style="margin-top: 0.5rem;"><?php echo esc_html($business); ?></div>
          <?php endif; ?>
          <h1><?php echo esc_html($owner_name); ?></h1>
          <?php if (has_excerpt()) : ?>
            <p class="dx-os-tagline"><?php echo esc_html(get_the_excerpt()); ?></p>
          <?php endif; ?>
          <span class="dx-os-meta">
            <?php
              $bits = [];
              if ($district) $bits[] = '<a href="' . esc_url(get_term_link($district)) . '">' . esc_html($district->name) . '</a>';
              if ($category) $bits[] = '<a href="' . esc_url(get_term_link($category)) . '">' . esc_html($category->name) . '</a>';
              echo implode(' &middot; ', $bits);
            ?>
          </span>
          <?php if ($website || $instagram) : ?>
            <div class="dx-os-links">
              <?php if ($website) :
                $web_label = preg_replace('~^https?://(www\.)?~', '', rtrim($website, '/'));
              ?>
                <a href="<?php echo esc_url($website); ?>" target="_blank" rel="noopener">
                  <?php echo esc_html($web_label); ?> →
                </a>
              <?php endif; ?>
              <?php if ($instagram) :
                $handle = ltrim($instagram, '@');
              ?>
                <a href="https://instagram.com/<?php echo esc_attr($handle); ?>" target="_blank" rel="noopener">
                  @<?php echo esc_html($handle); ?> on Instagram
                </a>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <!-- ═══ INTRO ═══ -->
    <?php if ($intro) : ?>
      <section class="dx-os-intro">
        <?php echo wp_kses_post($intro); ?>
      </section>
    <?php endif; ?>

    <!-- ═══ PULL QUOTE ═══ -->
    <?php if ($pull_quote) : ?>
      <aside class="dx-os-quote-wrap" aria-label="Pull quote">
        <p class="dx-os-quote"><?php echo esc_html($pull_quote); ?></p>
        <div class="dx-os-quote-attrib"><?php echo esc_html($owner_name); ?><?php echo $business ? ' &middot; ' . esc_html($business) : ''; ?></div>
      </aside>
    <?php endif; ?>

    <!-- ═══ Q&A ═══ -->
    <?php if (is_array($qa) && count($qa)) : ?>
      <section class="dx-os-qa-section" aria-labelledby="dx-qa-heading">
        <h2 id="dx-qa-heading" class="dx-os-qa-heading">The Conversation</h2>
        <?php foreach ($qa as $item) : ?>
          <?php if (empty($item['question']) && empty($item['answer'])) continue; ?>
          <article class="dx-qa-item">
            <?php if (!empty($item['question'])) : ?>
              <h3 class="dx-qa-question"><?php echo esc_html($item['question']); ?></h3>
            <?php endif; ?>
            <?php if (!empty($item['answer'])) : ?>
              <div class="dx-qa-answer"><?php echo wp_kses_post($item['answer']); ?></div>
            <?php endif; ?>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>

    <!-- ═══ YOUTUBE ═══ -->
    <?php if ($youtube_id) : ?>
      <section class="dx-os-video" aria-label="Video interview">
        <div class="dx-os-video-wrap">
          <iframe
            src="https://www.youtube.com/embed/<?php echo esc_attr($youtube_id); ?>"
            title="Interview with <?php echo esc_attr($owner_name); ?>"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen
            loading="lazy"
            referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
      </section>
    <?php endif; ?>

    <!-- ═══ KEY TAKEAWAY ═══ -->
    <?php if ($takeaway) : ?>
      <section class="dx-os-takeaway-section">
        <div class="dx-os-takeaway">
          <h2>The Takeaway</h2>
          <p><?php echo esc_html($takeaway); ?></p>
        </div>
      </section>
    <?php endif; ?>

    <!-- ═══ POST CONTENT (any extra editor content) ═══ -->
    <?php
    $body = get_the_content();
    if (trim(wp_strip_all_tags($body)) !== '') : ?>
      <section class="dx-os-intro">
        <?php the_content(); ?>
      </section>
    <?php endif; ?>

    <!-- ═══ SPONSORED FEATURE ═══ -->
    <section class="dx-sponsored-section"><?php dx_render_sponsored_widget(); ?></section>

    <!-- ═══ RELATED ═══ -->
    <?php
    $related = new WP_Query([
        'post_type'      => 'owner-spotlight',
        'posts_per_page' => 3,
        'post__not_in'   => [get_the_ID()],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
    if ($related->have_posts()) :
    ?>
      <section class="dx-section" aria-labelledby="dx-related-heading">
        <?php dx_section_header('More Owner Spotlights', 'Other Founders Worth Knowing', 'All Spotlights', get_post_type_archive_link('owner-spotlight')); ?>
        <h2 id="dx-related-heading" class="screen-reader-text">More owner spotlights</h2>
        <div class="dx-related-grid">
          <?php while ($related->have_posts()) : $related->the_post();
            $r_photo    = get_field('owner_photo');
            $r_name     = get_field('owner_name') ?: get_the_title();
            $r_biz      = get_field('business_name');
            $r_img      = $r_photo ? ($r_photo['sizes']['dx-card'] ?? $r_photo['url']) : (has_post_thumbnail() ? get_the_post_thumbnail_url(null, 'dx-card') : '');
          ?>
            <article class="dx-card">
              <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr($r_name); ?>">
                <?php if ($r_img) : ?>
                  <img class="dx-card-image" src="<?php echo esc_url($r_img); ?>" alt="<?php echo esc_attr($r_name); ?>">
                <?php endif; ?>
                <div class="dx-card-body">
                  <?php if ($r_biz) : ?>
                    <span class="dx-card-meta"><?php echo esc_html($r_biz); ?></span>
                  <?php endif; ?>
                  <h3 style="margin: 0.5rem 0 0; font-size: 1.25rem;"><?php echo esc_html($r_name); ?></h3>
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
   * Article (interview) with the owner as the subject Person and the
   * business as the relevant Organization.
   */
  $schema = array_filter([
      '@context'      => 'https://schema.org',
      '@type'         => 'Article',
      '@id'           => get_permalink() . '#article',
      'headline'      => get_the_title(),
      'datePublished' => get_the_date('c'),
      'dateModified'  => get_the_modified_date('c'),
      'image'         => $photo_url ?: null,
      'description'   => $takeaway ? wp_strip_all_tags($takeaway) : ($intro ? wp_trim_words(wp_strip_all_tags($intro), 30) : ''),
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
      'about'         => array_filter([
          '@type' => 'Person',
          'name'  => $owner_name,
          'image' => $photo_url ?: null,
          'worksFor' => $business ? [
              '@type' => 'Organization',
              'name'  => $business,
          ] : null,
      ]),
  ]);
  ?>
  <script type="application/ld+json"><?php echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>

</main>

<?php endwhile; get_footer();
