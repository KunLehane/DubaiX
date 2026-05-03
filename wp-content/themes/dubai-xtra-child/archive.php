<?php
/**
 * Generic Archive Template
 *
 * Catches all CPT archives (review, listing, best-list, owner-spotlight,
 * business-spotlight, things-to-do, guide) and taxonomy archives
 * (business-category, dubai-district, etc.).
 *
 * Editorial grid with category + district filter chips. Card markup is
 * post-type-aware so each CPT renders with its appropriate badges.
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

get_header();

// Determine context: CPT archive vs taxonomy archive vs other.
$post_type = '';
$current_term = null;

if (is_post_type_archive()) {
    $post_type = get_query_var('post_type');
    if (is_array($post_type)) $post_type = reset($post_type);
}

if (is_tax() || is_category() || is_tag()) {
    $current_term = get_queried_object();
    // For taxonomy archives, work out the dominant CPT in the queryset.
    global $wp_query;
    if (!empty($wp_query->posts)) {
        $post_type = $wp_query->posts[0]->post_type;
    }
}

if (!$post_type) {
    $post_type = 'post';
}

// Page header content
$page_title = '';
$page_description = '';
$archive_link = '';

if ($current_term) {
    $page_title       = single_term_title('', false);
    $page_description = term_description();
    $archive_link     = get_post_type_archive_link($post_type) ?: home_url('/');
} else {
    $page_title       = post_type_archive_title('', false) ?: get_the_archive_title();
    $page_description = get_the_archive_description();
    $archive_link     = get_post_type_archive_link($post_type) ?: '';
}

$total_posts = isset($GLOBALS['wp_query']->found_posts) ? (int) $GLOBALS['wp_query']->found_posts : 0;

// Categories + districts to render as filter chips. Hide empty so we
// don't surface terms with zero entries for the current post type.
$cats      = get_terms(['taxonomy' => 'business-category', 'hide_empty' => true]);
$districts = get_terms(['taxonomy' => 'dubai-district',    'hide_empty' => true]);
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

/* ═══ HEADER ═══ */
.dx-archive-header {
  max-width: 1200px;
  margin: 0 auto;
  padding: 4rem 1.5rem 2rem;
}
.dx-archive-header h1 {
  font-size: 4rem;
  margin: 0.5rem 0;
  line-height: 1.05;
}
.dx-archive-header .section-label { display: block; }
.dx-archive-count {
  font-size: 0.75rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--dx-mid-grey);
  font-weight: 600;
  margin-top: 0.5rem;
}
.dx-archive-description {
  max-width: 720px;
  font-size: 1.0625rem;
  line-height: 1.7;
  color: var(--dx-text);
  margin: 1rem 0 0;
}

/* ═══ FILTER CHIPS ═══ */
.dx-archive-filters {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem 1rem;
}
.dx-filter-group { margin-bottom: 1rem; }
.dx-filter-label {
  display: inline-block;
  font-size: 0.7rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--dx-mid-grey);
  font-weight: 700;
  margin-right: 0.75rem;
  vertical-align: middle;
}
.dx-filter-chip {
  display: inline-block;
  padding: 0.4rem 0.85rem;
  margin: 0.25rem 0.25rem 0.25rem 0;
  font-size: 0.8rem;
  border: 1px solid var(--dx-light-grey);
  background: var(--dx-white);
  color: var(--dx-navy);
  border-radius: 2px;
  letter-spacing: 0.04em;
  transition: all 0.15s ease;
}
.dx-filter-chip:hover {
  border-color: var(--dx-gold);
  background: #FFFDF5;
  color: var(--dx-navy);
}
.dx-filter-chip.is-active {
  background: var(--dx-navy);
  color: var(--dx-gold);
  border-color: var(--dx-navy);
}
.dx-filter-clear {
  display: inline-block;
  padding: 0.4rem 0.85rem;
  margin: 0.25rem 0.5rem 0.25rem 0;
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--dx-gold);
}

/* ═══ GRID ═══ */
.dx-archive-grid-wrap {
  max-width: 1200px;
  margin: 0 auto;
  padding: 1.5rem 1.5rem 4rem;
}
.dx-archive-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}
.dx-archive-card-title-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  margin: 0.5rem 0;
}
.dx-archive-card-title-row h2,
.dx-archive-card-title-row h3 {
  margin: 0;
  font-size: 1.375rem;
  line-height: 1.25;
}
.dx-archive-card-name-row {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin: 0.5rem 0;
}
.dx-archive-no-results {
  text-align: center;
  padding: 4rem 1.5rem;
  color: var(--dx-mid-grey);
}
.dx-archive-no-results h2 {
  font-size: 2rem;
  margin: 0 0 0.5rem;
}

/* ═══ PAGINATION ═══ */
.dx-archive-pagination {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 1.5rem 4rem;
  text-align: center;
}
.dx-archive-pagination .nav-links {
  display: inline-flex;
  gap: 0.5rem;
  flex-wrap: wrap;
  justify-content: center;
}
.dx-archive-pagination .page-numbers {
  display: inline-block;
  min-width: 40px;
  padding: 0.5rem 0.85rem;
  border: 1px solid var(--dx-light-grey);
  background: var(--dx-white);
  color: var(--dx-navy);
  font-size: 0.9rem;
  letter-spacing: 0.04em;
  text-decoration: none;
}
.dx-archive-pagination .page-numbers:hover {
  border-color: var(--dx-gold);
  background: #FFFDF5;
}
.dx-archive-pagination .page-numbers.current {
  background: var(--dx-navy);
  color: var(--dx-gold);
  border-color: var(--dx-navy);
}

@media (max-width: 768px) {
  .dx-archive-header h1 { font-size: 2.5rem; }
  .dx-archive-grid { grid-template-columns: 1fr; }
}
</style>

<main id="primary" class="dx-archive">

  <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
    <nav class="dx-breadcrumbs" aria-label="Breadcrumb"><?php rank_math_the_breadcrumbs(); ?></nav>
  <?php endif; ?>

  <header class="dx-archive-header">
    <span class="section-label"><?php
      $label_map = [
          'review'              => 'Reviews',
          'listing'             => 'Directory',
          'best-list'           => 'Best Of Dubai',
          'owner-spotlight'     => 'Owner Spotlights',
          'business-spotlight'  => 'Business Spotlights',
          'things-to-do'        => 'Things To Do',
          'guide'               => 'Guides',
      ];
      echo esc_html($label_map[$post_type] ?? 'Browse');
    ?></span>
    <h1><?php echo esc_html($page_title); ?></h1>
    <?php if ($total_posts) : ?>
      <span class="dx-archive-count"><?php echo esc_html($total_posts); ?> <?php echo esc_html(_n('entry', 'entries', $total_posts, 'dubai-xtra')); ?></span>
    <?php endif; ?>
    <?php if ($page_description) : ?>
      <div class="dx-archive-description"><?php echo wp_kses_post($page_description); ?></div>
    <?php endif; ?>
  </header>

  <!-- ═══ FILTERS ═══ -->
  <?php if (!empty($cats) || !empty($districts)) : ?>
    <nav class="dx-archive-filters" aria-label="Filter">

      <?php if (!empty($cats) && !is_wp_error($cats)) : ?>
        <div class="dx-filter-group">
          <span class="dx-filter-label">Categories</span>
          <?php if ($archive_link) : ?>
            <a href="<?php echo esc_url($archive_link); ?>" class="dx-filter-chip <?php echo (!$current_term || $current_term->taxonomy !== 'business-category') ? 'is-active' : ''; ?>">All</a>
          <?php endif; ?>
          <?php foreach ($cats as $cat) :
            $is_active = $current_term && $current_term->term_id === $cat->term_id ? ' is-active' : '';
          ?>
            <a href="<?php echo esc_url(get_term_link($cat)); ?>" class="dx-filter-chip<?php echo esc_attr($is_active); ?>">
              <?php echo esc_html($cat->name); ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($districts) && !is_wp_error($districts)) : ?>
        <div class="dx-filter-group">
          <span class="dx-filter-label">Districts</span>
          <?php if ($archive_link && $current_term && $current_term->taxonomy === 'dubai-district') : ?>
            <a href="<?php echo esc_url($archive_link); ?>" class="dx-filter-clear">Clear ×</a>
          <?php endif; ?>
          <?php foreach ($districts as $dist) :
            $is_active = $current_term && $current_term->term_id === $dist->term_id ? ' is-active' : '';
          ?>
            <a href="<?php echo esc_url(get_term_link($dist)); ?>" class="dx-filter-chip<?php echo esc_attr($is_active); ?>">
              <?php echo esc_html($dist->name); ?>
            </a>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

    </nav>
  <?php endif; ?>

  <!-- ═══ SPONSORED FEATURE ═══ -->
  <section class="dx-sponsored-section"><?php dx_render_sponsored_widget(); ?></section>

  <!-- ═══ GRID ═══ -->
  <section class="dx-archive-grid-wrap" aria-label="Results">
    <?php if (have_posts()) : ?>
      <div class="dx-archive-grid">
        <?php while (have_posts()) : the_post();
          $current_pt = get_post_type();
        ?>
          <article class="dx-card">
            <a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(get_the_title()); ?>">
              <?php if (has_post_thumbnail()) the_post_thumbnail('dx-card', ['class' => 'dx-card-image', 'alt' => esc_attr(get_the_title())]); ?>
              <div class="dx-card-body">

                <?php
                /**
                 * Card body adapts to post type so each archive feels native.
                 */
                switch ($current_pt) :
                  case 'review':
                    $score = get_field('review_score');
                ?>
                    <?php dx_meta_line(); ?>
                    <div class="dx-archive-card-title-row">
                      <h2><?php the_title(); ?></h2>
                      <?php if ($score) dx_score_badge($score, 'sm'); ?>
                    </div>
                    <p style="color: var(--dx-mid-grey); font-size: 0.875rem; margin: 0; line-height: 1.6;">
                      <?php echo esc_html(wp_trim_words(get_field('review_summary') ?: get_the_excerpt(), 18)); ?>
                    </p>
                <?php
                    break;

                  case 'listing':
                    $verified = get_field('is_verified');
                    $tier     = get_field('listing_tier');
                ?>
                    <?php dx_meta_line(); ?>
                    <div class="dx-archive-card-name-row">
                      <h2 style="margin: 0; font-size: 1.375rem;"><?php the_title(); ?></h2>
                      <?php if ($verified) dx_verified_badge(); ?>
                      <?php if ($tier === 'Premium') dx_tag('Premium', 'gold'); ?>
                    </div>
                <?php
                    break;

                  case 'best-list':
                    $items = get_field('list_items');
                    $count = is_array($items) ? count($items) : 0;
                ?>
                    <?php if ($count) dx_tag($count . ' Picks', 'gold'); ?>
                    <h2 style="margin: 0.5rem 0; font-size: 1.375rem;"><?php the_title(); ?></h2>
                    <p style="color: var(--dx-mid-grey); font-size: 0.875rem; margin: 0; line-height: 1.6;">
                      <?php echo esc_html(wp_trim_words(get_the_excerpt(), 16)); ?>
                    </p>
                <?php
                    break;

                  case 'things-to-do':
                    $relevance = get_field('time_relevance');
                ?>
                    <?php if ($relevance) dx_tag($relevance, 'gold'); ?>
                    <h2 style="margin: 0.5rem 0; font-size: 1.375rem;"><?php the_title(); ?></h2>
                    <?php dx_meta_line(); ?>
                <?php
                    break;

                  case 'owner-spotlight':
                    $owner_name = get_field('owner_name') ?: get_the_title();
                    $biz_name   = get_field('business_name');
                ?>
                    <?php if ($biz_name) : ?>
                      <span class="dx-card-meta"><?php echo esc_html($biz_name); ?></span>
                    <?php endif; ?>
                    <h2 style="margin: 0.5rem 0; font-size: 1.375rem;"><?php echo esc_html($owner_name); ?></h2>
                <?php
                    break;

                  case 'business-spotlight':
                    $biz_name = get_field('business_name') ?: get_the_title();
                    $tagline  = get_field('business_tagline');
                ?>
                    <span class="dx-card-meta">Business Spotlight</span>
                    <h2 style="margin: 0.5rem 0; font-size: 1.375rem;"><?php echo esc_html($biz_name); ?></h2>
                    <?php if ($tagline) : ?>
                      <p style="color: var(--dx-mid-grey); font-size: 0.875rem; margin: 0; line-height: 1.6;"><?php echo esc_html(wp_trim_words($tagline, 18)); ?></p>
                    <?php endif; ?>
                <?php
                    break;

                  case 'guide':
                    $guide_type = get_field('guide_type');
                ?>
                    <?php if ($guide_type) dx_tag($guide_type, 'gold'); ?>
                    <h2 style="margin: 0.5rem 0; font-size: 1.375rem;"><?php the_title(); ?></h2>
                    <p style="color: var(--dx-mid-grey); font-size: 0.875rem; margin: 0; line-height: 1.6;">
                      <?php echo esc_html(wp_trim_words(get_the_excerpt(), 16)); ?>
                    </p>
                <?php
                    break;

                  default:
                ?>
                    <?php dx_meta_line(); ?>
                    <h2 style="margin: 0.5rem 0; font-size: 1.375rem;"><?php the_title(); ?></h2>
                    <p style="color: var(--dx-mid-grey); font-size: 0.875rem; margin: 0; line-height: 1.6;">
                      <?php echo esc_html(wp_trim_words(get_the_excerpt(), 18)); ?>
                    </p>
                <?php
                endswitch;
                ?>

              </div>
            </a>
          </article>
        <?php endwhile; ?>
      </div>

    <?php else : ?>
      <div class="dx-archive-no-results">
        <h2>Nothing here yet</h2>
        <p>We could not find anything matching your filter. Try clearing the filter or browsing a different section.</p>
        <?php if ($archive_link) : ?>
          <a href="<?php echo esc_url($archive_link); ?>" class="dx-btn dx-btn-outline" style="margin-top: 1.5rem;">Browse All</a>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </section>

  <!-- ═══ PAGINATION ═══ -->
  <?php if (have_posts()) : ?>
    <nav class="dx-archive-pagination" aria-label="Pagination">
      <?php
      the_posts_pagination([
          'mid_size'  => 2,
          'prev_text' => '← Previous',
          'next_text' => 'Next →',
          'screen_reader_text' => ' ',
      ]);
      ?>
    </nav>
  <?php endif; ?>

</main>

<?php get_footer();
