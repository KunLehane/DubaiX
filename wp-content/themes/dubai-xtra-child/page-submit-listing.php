<?php
/**
 * Template Name: Submit Listing
 *
 * The highest-converting page. 15% of traffic, 3m20s avg dwell time.
 * Free vs Premium (AED 800/yr) comparison, social proof, FAQ, conversion CTA.
 *
 * Sections in order:
 *  1. Hero with primary + secondary CTAs
 *  2. Three value props (Reach / Credibility / Visibility)
 *  3. Free vs Premium comparison
 *  4. Premium "what's included" mockup
 *  5. Social proof (1M+ impressions, 12 categories, 14 districts)
 *  6. FAQ (native collapsible <details>)
 *  7. Bottom CTA
 *
 * @package Dubai_Xtra
 */

defined('ABSPATH') || exit;

get_header(); ?>

<style>
/* ═══ HERO ═══ */
.dx-sl-hero {
  background: var(--dx-warm-white);
  text-align: center;
  padding: 6rem 1.5rem 4rem;
  border-bottom: 1px solid var(--dx-light-grey);
}
.dx-sl-hero h1 {
  font-size: 4rem;
  margin: 1rem auto;
  max-width: 900px;
}
.dx-sl-hero .dx-lede {
  font-size: 1.125rem;
  color: var(--dx-mid-grey);
  max-width: 640px;
  margin: 0 auto 2rem;
  line-height: 1.6;
}
.dx-sl-cta-row {
  display: flex;
  gap: 0.75rem;
  justify-content: center;
  flex-wrap: wrap;
}

/* ═══ VALUE PROPS ═══ */
.dx-value-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1.5rem;
}
.dx-value-card {
  background: var(--dx-white);
  border: 1px solid var(--dx-light-grey);
  border-radius: 2px;
  padding: 2.5rem 2rem;
  text-align: center;
}
.dx-value-icon {
  width: 48px;
  height: 48px;
  margin: 0 auto 1rem;
  color: var(--dx-gold);
}
.dx-value-card h3 { font-size: 1.5rem; margin: 0 0 0.5rem; }
.dx-value-card p { color: var(--dx-mid-grey); line-height: 1.6; margin: 0; }

/* ═══ FREE VS PREMIUM ═══ */
.dx-tier-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
  align-items: stretch;
  max-width: 960px;
  margin: 0 auto;
}
.dx-tier {
  background: var(--dx-white);
  border: 1px solid var(--dx-light-grey);
  border-radius: 2px;
  padding: 2.5rem;
  display: flex;
  flex-direction: column;
}
.dx-tier-premium {
  border: 2px solid var(--dx-gold);
  position: relative;
  box-shadow: 0 12px 40px rgba(201, 168, 76, 0.12);
}
.dx-tier-badge {
  position: absolute;
  top: -14px;
  left: 50%;
  transform: translateX(-50%);
  background: var(--dx-gold);
  color: var(--dx-navy);
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  padding: 0.4rem 0.9rem;
  border-radius: 2px;
}
.dx-tier-name {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.7rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--dx-mid-grey);
  font-weight: 700;
  margin-bottom: 0.5rem;
  display: block;
}
.dx-tier-price {
  font-family: 'Cormorant Garamond', serif;
  font-size: 3rem;
  color: var(--dx-navy);
  font-weight: 600;
  margin: 0;
  line-height: 1;
}
.dx-tier-price-suffix {
  font-size: 1rem;
  color: var(--dx-mid-grey);
  font-family: 'DM Sans', sans-serif;
  font-weight: 400;
}
.dx-tier-tagline {
  color: var(--dx-mid-grey);
  margin: 0.75rem 0 1.5rem;
  font-size: 0.95rem;
}
.dx-tier-features {
  list-style: none;
  padding: 0;
  margin: 0 0 2rem;
  flex: 1;
}
.dx-tier-features li {
  padding: 0.6rem 0;
  display: flex;
  align-items: flex-start;
  gap: 0.6rem;
  font-size: 0.95rem;
  border-bottom: 1px solid var(--dx-light-grey);
}
.dx-tier-features li:last-child { border-bottom: none; }
.dx-tier-features li.dx-tier-disabled { color: var(--dx-mid-grey); }
.dx-tier-check svg { color: var(--dx-gold); }
.dx-tier-cross svg { color: #C7C7C7; }
.dx-tier-icon-wrap { flex-shrink: 0; margin-top: 2px; display: inline-flex; }

/* ═══ PREMIUM MOCKUP ═══ */
.dx-mockup-wrap {
  background: var(--dx-light-grey);
  padding: 3rem 2rem;
  border-radius: 2px;
}
.dx-mockup-label {
  text-align: center;
  font-size: 0.7rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--dx-mid-grey);
  margin: 0 0 1.5rem;
  font-weight: 600;
}
.dx-mockup {
  background: var(--dx-white);
  border: 1px solid var(--dx-light-grey);
  border-radius: 2px;
  max-width: 800px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: 1fr 1.5fr;
  overflow: hidden;
}
.dx-mockup-img {
  width: 100%;
  height: 100%;
  min-height: 280px;
  object-fit: cover;
}
.dx-mockup-body { padding: 1.75rem; }
.dx-mockup-title-row {
  display: flex;
  align-items: center;
  gap: 0.6rem;
  flex-wrap: wrap;
  margin-bottom: 0.4rem;
}
.dx-mockup-title-row h4 { font-size: 1.5rem; margin: 0; }
.dx-mockup-desc {
  color: var(--dx-text);
  font-size: 0.95rem;
  line-height: 1.6;
  margin: 0.75rem 0 1rem;
}
.dx-mockup-features {
  list-style: none;
  padding: 0;
  margin: 0;
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.4rem 1rem;
  font-size: 0.85rem;
  color: var(--dx-text);
}
.dx-mockup-features li::before {
  content: "✓ ";
  color: var(--dx-gold);
  font-weight: 700;
  margin-right: 0.25rem;
}

/* ═══ SOCIAL PROOF ═══ */
.dx-social-proof {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 2rem;
  text-align: center;
}
.dx-stat-value {
  font-family: 'Cormorant Garamond', serif;
  font-size: 4.5rem;
  color: var(--dx-gold);
  font-weight: 600;
  line-height: 1;
}
.dx-stat-label {
  font-size: 0.75rem;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: rgba(255, 255, 255, 0.7);
  margin-top: 0.75rem;
  font-weight: 600;
}

/* ═══ FAQ ═══ */
.dx-faq-list {
  max-width: 800px;
  margin: 0 auto;
}
.dx-faq-item { border-bottom: 1px solid var(--dx-light-grey); }
.dx-faq-item summary {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.375rem;
  color: var(--dx-navy);
  font-weight: 600;
  padding: 1.5rem 2.5rem 1.5rem 0;
  cursor: pointer;
  list-style: none;
  position: relative;
  transition: color 0.2s ease;
  outline: none;
}
.dx-faq-item summary::-webkit-details-marker { display: none; }
.dx-faq-item summary::after {
  content: "+";
  position: absolute;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  font-family: 'DM Sans', sans-serif;
  font-size: 1.5rem;
  color: var(--dx-gold);
  font-weight: 400;
  line-height: 1;
  transition: transform 0.2s ease;
}
.dx-faq-item[open] summary::after { content: "−"; }
.dx-faq-item summary:hover { color: var(--dx-gold); }
.dx-faq-item summary:focus-visible { color: var(--dx-gold); }
.dx-faq-answer {
  padding: 0 2.5rem 1.5rem 0;
  color: var(--dx-text);
  line-height: 1.7;
  font-size: 0.95rem;
}
.dx-faq-answer a { color: var(--dx-navy); text-decoration: underline; text-decoration-color: var(--dx-gold); text-underline-offset: 3px; }
.dx-faq-answer a:hover { color: var(--dx-gold); }

/* ═══ BOTTOM CTA ═══ */
.dx-bottom-cta {
  text-align: center;
  background: var(--dx-navy);
  color: var(--dx-warm-white);
  padding: 5rem 1.5rem;
}
.dx-bottom-cta h2 {
  color: var(--dx-warm-white);
  font-size: 3rem;
  margin: 0.5rem 0 1rem;
}
.dx-bottom-cta p {
  color: rgba(255, 255, 255, 0.7);
  max-width: 580px;
  margin: 0 auto 2rem;
  line-height: 1.6;
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

/* ═══ SUBMISSION FORM ═══ */
.dx-form-section {
  background: var(--dx-warm-white);
  padding: 5rem 1.5rem;
  border-top: 1px solid var(--dx-light-grey);
}
.dx-form-wrap {
  max-width: 720px;
  margin: 0 auto;
}
.dx-form-wrap > .section-label { display: block; margin-bottom: 0.5rem; }
.dx-form-wrap h2 {
  font-size: 2.5rem;
  margin: 0.25rem 0 1rem;
}
.dx-form-intro {
  color: var(--dx-mid-grey);
  line-height: 1.6;
  margin: 0 0 2.5rem;
  font-size: 1.0625rem;
}
.dx-listing-form { display: block; }
.dx-form-row { margin-bottom: 1.5rem; }
.dx-form-label {
  display: block;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--dx-navy);
  margin-bottom: 0.5rem;
}
.dx-required { color: var(--dx-gold); margin-left: 0.25rem; }
.dx-form-input {
  width: 100%;
  padding: 0.85rem 1rem;
  border: 1px solid var(--dx-light-grey);
  background: var(--dx-white);
  border-radius: 2px;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.95rem;
  color: var(--dx-text);
  line-height: 1.4;
  transition: border-color 0.2s ease, background 0.2s ease;
  box-sizing: border-box;
}
.dx-form-input:focus {
  outline: none;
  border-color: var(--dx-gold);
  background: #FFFDF5;
}
textarea.dx-form-input { resize: vertical; min-height: 120px; }
select.dx-form-input { cursor: pointer; }
.dx-form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1.5rem;
}
.dx-form-grid .dx-form-row { margin-bottom: 0; }
.dx-form-section-title {
  font-family: 'DM Sans', sans-serif;
  font-size: 0.75rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--dx-gold);
  font-weight: 700;
  margin: 2.5rem 0 1.25rem;
  padding-bottom: 0.5rem;
  border-bottom: 1px solid var(--dx-light-grey);
}
.dx-form-file {
  padding: 0.5rem;
  cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  font-size: 0.9rem;
}
.dx-form-hint {
  display: block;
  margin-top: 0.4rem;
  font-size: 0.75rem;
  color: var(--dx-mid-grey);
}
.dx-form-actions {
  margin-top: 2.5rem;
  text-align: center;
}
.dx-form-submit {
  padding: 1rem 2.5rem;
  font-size: 0.85rem;
}
.dx-form-disclaimer {
  margin-top: 1rem;
  font-size: 0.8rem;
  color: var(--dx-mid-grey);
  line-height: 1.5;
}
.dx-form-success {
  background: var(--dx-white);
  border: 2px solid var(--dx-gold);
  padding: 3rem 2rem;
  text-align: center;
  border-radius: 2px;
}
.dx-form-success-icon {
  width: 56px;
  height: 56px;
  margin: 0 auto 1.25rem;
  border-radius: 50%;
  background: var(--dx-gold);
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--dx-navy);
}
.dx-form-success h3 {
  font-size: 2rem;
  margin: 0 0 0.5rem;
  color: var(--dx-navy);
}
.dx-form-success p {
  color: var(--dx-mid-grey);
  margin: 0;
  font-size: 1.0625rem;
  line-height: 1.6;
}
.dx-form-error {
  background: #FFF5F0;
  border-left: 3px solid #C53030;
  padding: 1rem 1.25rem;
  border-radius: 2px;
  color: #742A2A;
  margin-bottom: 1.5rem;
  font-size: 0.95rem;
}

@media (max-width: 768px) {
  .dx-sl-hero { padding: 4rem 1.5rem 3rem; }
  .dx-sl-hero h1 { font-size: 2.5rem; }
  .dx-value-grid { grid-template-columns: 1fr; }
  .dx-tier-grid { grid-template-columns: 1fr; gap: 2rem; }
  .dx-tier { padding: 2rem 1.5rem; }
  .dx-tier-price { font-size: 2.5rem; }
  .dx-mockup { grid-template-columns: 1fr; }
  .dx-mockup-img { min-height: 220px; }
  .dx-mockup-features { grid-template-columns: 1fr; }
  .dx-social-proof { grid-template-columns: 1fr; gap: 2.5rem; }
  .dx-stat-value { font-size: 3rem; }
  .dx-bottom-cta h2 { font-size: 2rem; }
  .dx-bottom-cta { padding: 3.5rem 1.5rem; }
  .dx-form-section { padding: 3rem 1.5rem; }
  .dx-form-wrap h2 { font-size: 2rem; }
  .dx-form-grid { grid-template-columns: 1fr; gap: 1.5rem; }
}
</style>

<?php
/**
 * Inline check / cross icons used in the comparison table.
 */
$dx_check_icon = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 13l4 4L19 7"/></svg>';
$dx_cross_icon = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>';
?>

<main id="primary" class="dx-submit-listing">

  <!-- ═══ BREADCRUMBS ═══ -->
  <?php if (function_exists('rank_math_the_breadcrumbs')) : ?>
    <nav class="dx-breadcrumbs" aria-label="Breadcrumb">
      <?php rank_math_the_breadcrumbs(); ?>
    </nav>
  <?php endif; ?>

  <!-- ═══ 1. HERO ═══ -->
  <section class="dx-sl-hero" aria-labelledby="dx-sl-hero-heading">
    <span class="section-label">For Business Owners</span>
    <h1 id="dx-sl-hero-heading">Get Your Business Featured on Dubai Xtra</h1>
    <p class="dx-lede">Reach Dubai's most engaged audience of residents, expats and visitors actively looking for the next place to book, eat, or call.</p>
    <div class="dx-sl-cta-row">
      <a href="#submit-form" class="dx-btn dx-btn-primary">Submit Your Listing</a>
      <a href="#example" class="dx-btn dx-btn-outline">See A Premium Example</a>
    </div>
  </section>

  <!-- ═══ 2. VALUE PROPS ═══ -->
  <section class="dx-section" aria-labelledby="dx-sl-value-heading">
    <?php dx_section_header('Why Dubai Xtra', 'Three Reasons To List With Us'); ?>
    <h2 id="dx-sl-value-heading" class="screen-reader-text">Why list on Dubai Xtra</h2>

    <div class="dx-value-grid">
      <article class="dx-value-card">
        <svg class="dx-value-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="24" cy="24" r="20"/>
          <path d="M4 24h40M24 4a30 30 0 0 1 0 40M24 4a30 30 0 0 0 0 40"/>
        </svg>
        <h3>Reach</h3>
        <p>Over 1 million impressions a year from a Dubai-focused, high-intent audience. Editorial coverage that ranks in search and gets shared.</p>
      </article>
      <article class="dx-value-card">
        <svg class="dx-value-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M24 6l16 8v12c0 8-7 14-16 16-9-2-16-8-16-16V14z"/>
          <path d="M16 24l6 6 12-12"/>
        </svg>
        <h3>Credibility</h3>
        <p>Every listing is reviewed by an editor before going live. The verified badge tells customers they can trust who they are dealing with.</p>
      </article>
      <article class="dx-value-card">
        <svg class="dx-value-icon" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="24" cy="24" r="16"/>
          <circle cx="24" cy="24" r="6"/>
          <path d="M24 4v6M24 38v6M4 24h6M38 24h6"/>
        </svg>
        <h3>Visibility</h3>
        <p>Premium listings rotate on the homepage, in best-of lists, and in our weekly newsletter. You stay top of mind, not buried five pages deep.</p>
      </article>
    </div>
  </section>

  <!-- ═══ 3. FREE VS PREMIUM ═══ -->
  <section class="dx-section dx-section-white" aria-labelledby="dx-sl-tier-heading">
    <?php dx_section_header('Pricing', 'Free Listing or Premium'); ?>
    <h2 id="dx-sl-tier-heading" class="screen-reader-text">Free vs Premium comparison</h2>

    <div class="dx-tier-grid">
      <!-- Free -->
      <article class="dx-tier" aria-labelledby="dx-tier-free-name">
        <span id="dx-tier-free-name" class="dx-tier-name">Free</span>
        <div class="dx-tier-price">$0<span class="dx-tier-price-suffix"> / forever</span></div>
        <p class="dx-tier-tagline">A basic presence in the Dubai Xtra directory.</p>
        <ul class="dx-tier-features">
          <li><span class="dx-tier-icon-wrap dx-tier-check"><?php echo $dx_check_icon; // phpcs:ignore ?></span> Business name and category</li>
          <li><span class="dx-tier-icon-wrap dx-tier-check"><?php echo $dx_check_icon; // phpcs:ignore ?></span> District and contact details</li>
          <li><span class="dx-tier-icon-wrap dx-tier-check"><?php echo $dx_check_icon; // phpcs:ignore ?></span> 200-character description</li>
          <li class="dx-tier-disabled"><span class="dx-tier-icon-wrap dx-tier-cross"><?php echo $dx_cross_icon; // phpcs:ignore ?></span> No verified badge</li>
          <li class="dx-tier-disabled"><span class="dx-tier-icon-wrap dx-tier-cross"><?php echo $dx_cross_icon; // phpcs:ignore ?></span> No image gallery or logo</li>
          <li class="dx-tier-disabled"><span class="dx-tier-icon-wrap dx-tier-cross"><?php echo $dx_cross_icon; // phpcs:ignore ?></span> No homepage rotation</li>
          <li class="dx-tier-disabled"><span class="dx-tier-icon-wrap dx-tier-cross"><?php echo $dx_cross_icon; // phpcs:ignore ?></span> No newsletter inclusion</li>
        </ul>
        <a href="#submit-form" class="dx-btn dx-btn-outline">Get Started Free</a>
      </article>

      <!-- Premium -->
      <article class="dx-tier dx-tier-premium" aria-labelledby="dx-tier-premium-name">
        <span class="dx-tier-badge">Recommended</span>
        <span id="dx-tier-premium-name" class="dx-tier-name">Premium</span>
        <div class="dx-tier-price">AED 800<span class="dx-tier-price-suffix"> / year</span></div>
        <p class="dx-tier-tagline">The full Dubai Xtra treatment. Billed annually, no auto-renewal.</p>
        <ul class="dx-tier-features">
          <li><span class="dx-tier-icon-wrap dx-tier-check"><?php echo $dx_check_icon; // phpcs:ignore ?></span> Everything in Free, plus:</li>
          <li><span class="dx-tier-icon-wrap dx-tier-check"><?php echo $dx_check_icon; // phpcs:ignore ?></span> Verified badge on your listing</li>
          <li><span class="dx-tier-icon-wrap dx-tier-check"><?php echo $dx_check_icon; // phpcs:ignore ?></span> Logo and gallery (up to 8 images)</li>
          <li><span class="dx-tier-icon-wrap dx-tier-check"><?php echo $dx_check_icon; // phpcs:ignore ?></span> Full opening hours and map embed</li>
          <li><span class="dx-tier-icon-wrap dx-tier-check"><?php echo $dx_check_icon; // phpcs:ignore ?></span> Homepage and best-of rotation</li>
          <li><span class="dx-tier-icon-wrap dx-tier-check"><?php echo $dx_check_icon; // phpcs:ignore ?></span> Weekly newsletter mentions</li>
          <li><span class="dx-tier-icon-wrap dx-tier-check"><?php echo $dx_check_icon; // phpcs:ignore ?></span> Priority editorial coverage</li>
        </ul>
        <a href="#submit-form" class="dx-btn dx-btn-primary">Go Premium</a>
      </article>
    </div>
  </section>

  <!-- ═══ 4. PREMIUM MOCKUP ═══ -->
  <section class="dx-section" id="example" aria-labelledby="dx-sl-mockup-heading">
    <?php dx_section_header("See It In Action", "Here Is What A Premium Listing Looks Like"); ?>
    <h2 id="dx-sl-mockup-heading" class="screen-reader-text">Premium listing example</h2>

    <div class="dx-mockup-wrap">
      <p class="dx-mockup-label">Sample Premium Listing</p>
      <article class="dx-mockup">
        <img class="dx-mockup-img" src="https://picsum.photos/seed/trump-golf-dubai-mockup/800/600" alt="Sample premium listing photo">
        <div class="dx-mockup-body">
          <div class="dx-mockup-title-row">
            <h4>Trump International Golf Club</h4>
            <?php dx_verified_badge(); ?>
          </div>
          <span class="dx-card-meta">DAMAC Hills - Golf - $$$$</span>
          <p class="dx-mockup-desc">Championship 18-hole course designed by Gil Hanse, the only Trump-branded course in the Middle East. Open daily, members and guests welcome.</p>
          <ul class="dx-mockup-features">
            <li>18-hole championship course</li>
            <li>Pro shop and dining</li>
            <li>Booking and directions</li>
            <li>Full image gallery</li>
            <li>Opening hours and contact</li>
            <li>Social and website links</li>
          </ul>
        </div>
      </article>
    </div>
  </section>

  <!-- ═══ 5. SOCIAL PROOF ═══ -->
  <section class="dx-section dx-section-dark" aria-labelledby="dx-sl-proof-heading">
    <h2 id="dx-sl-proof-heading" class="screen-reader-text">Reach in numbers</h2>
    <div class="dx-social-proof">
      <div>
        <div class="dx-stat-value">1M+</div>
        <div class="dx-stat-label">Impressions per year</div>
      </div>
      <div>
        <div class="dx-stat-value">12</div>
        <div class="dx-stat-label">Editorial categories</div>
      </div>
      <div>
        <div class="dx-stat-value">14</div>
        <div class="dx-stat-label">Dubai districts covered</div>
      </div>
    </div>
  </section>

  <!-- ═══ 6. FAQ ═══ -->
  <section class="dx-section" aria-labelledby="dx-sl-faq-heading">
    <?php dx_section_header('FAQ', 'Common Questions'); ?>
    <h2 id="dx-sl-faq-heading" class="screen-reader-text">Frequently asked questions</h2>

    <div class="dx-faq-list">
      <details class="dx-faq-item">
        <summary>How long does it take to get listed?</summary>
        <div class="dx-faq-answer">Most listings go live within 48 hours of submission. Every listing is reviewed by an editor before publishing. If anything is missing or unclear we will email you before going live.</div>
      </details>

      <details class="dx-faq-item">
        <summary>What is the difference between Free and Premium?</summary>
        <div class="dx-faq-answer">Free is a basic directory entry: name, category, district, contact details, and a short description. Premium adds the verified badge, image gallery, opening hours, map embed, homepage rotation, newsletter mentions, and priority editorial coverage. Full comparison is above.</div>
      </details>

      <details class="dx-faq-item">
        <summary>Can I upgrade from Free to Premium later?</summary>
        <div class="dx-faq-answer">Yes, any time. Existing free listings can be upgraded with no disruption to your URL or content. Just email us or submit the upgrade form and we will switch you over.</div>
      </details>

      <details class="dx-faq-item">
        <summary>What if my listing isn't approved?</summary>
        <div class="dx-faq-answer">If we cannot publish your listing as submitted, you get a full refund and an email explaining why. We are a curated directory, not a pay-to-play one. That is part of what makes the verified badge worth having.</div>
      </details>

      <details class="dx-faq-item">
        <summary>How is Dubai Xtra different from other Dubai directories?</summary>
        <div class="dx-faq-answer">We are smaller and more curated. Every business in our directory is reviewed by an editor. We focus on businesses worth knowing, not exhaustive coverage. Our audience is residents and visitors who already trust the recommendation when they arrive.</div>
      </details>

      <details class="dx-faq-item">
        <summary>Can I edit my listing after publishing?</summary>
        <div class="dx-faq-answer">Premium customers get a dashboard to edit their listing details directly. Free listings can be updated by emailing us. Either way we typically turn around requests within one business day.</div>
      </details>

      <details class="dx-faq-item">
        <summary>Do you accept payment in AED?</summary>
        <div class="dx-faq-answer">Yes. We accept AED, USD, GBP, and EUR. Premium is billed annually at AED 800 (or the equivalent in your currency). We invoice on submission and never auto-renew without your written authorisation.</div>
      </details>

      <details class="dx-faq-item">
        <summary>Will my listing show up in search engines?</summary>
        <div class="dx-faq-answer">Yes. Every listing has a clean URL, structured data, and is indexed by Google. Premium listings tend to rank higher because they include images, full contact details, and structured opening hours.</div>
      </details>
    </div>
  </section>

  <!-- ═══ 7. BOTTOM CTA ═══ -->
  <section class="dx-bottom-cta" id="submit-form" aria-labelledby="dx-sl-cta-heading">
    <span class="section-label" style="color: var(--dx-gold);">Ready When You Are</span>
    <h2 id="dx-sl-cta-heading">Submit Your Listing</h2>
    <p>Tell us about your business in the form below. We will review your submission and get back to you within 48 hours. Free listings are free, forever. Premium is AED 800 a year, billed once, no auto-renewal.</p>
    <a href="#submit-form-section" class="dx-btn dx-btn-gold">Start Submission ↓</a>
  </section>

  <!-- ═══ 8. SUBMISSION FORM ═══ -->
  <section class="dx-form-section" id="submit-form-section" aria-labelledby="dx-form-heading">
    <div class="dx-form-wrap">

      <?php if (isset($_GET['dx_submitted']) && $_GET['dx_submitted'] === '1') : ?>

        <div class="dx-form-success" role="status">
          <div class="dx-form-success-icon" aria-hidden="true">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>
          </div>
          <h3>Thanks for submitting!</h3>
          <p>We will review your listing and get it live within 48 hours. Watch your inbox for next steps.</p>
        </div>

      <?php else : ?>

        <span class="section-label">Submit Your Listing</span>
        <h2 id="dx-form-heading">Tell Us About Your Business</h2>
        <p class="dx-form-intro">Fill out the form below. Required fields marked with a gold asterisk. Your submission lands in our editorial inbox and a draft listing in our admin for review.</p>

        <?php if (isset($_GET['dx_submit_error'])) :
          $err = $_GET['dx_submit_error'];
          $msg = match ($err) {
              'required'      => 'Please fill in the required fields (Business Name, Your Name, Email).',
              'security'      => 'Your session expired. Please refresh the page and try again.',
              'create_failed' => 'Something went wrong on our end. Try again or email us directly.',
              default         => 'Something went wrong. Please try again.',
          };
        ?>
          <div class="dx-form-error" role="alert"><?php echo esc_html($msg); ?></div>
        <?php endif; ?>

        <form class="dx-listing-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post" enctype="multipart/form-data">
          <?php wp_nonce_field('dx_submit_listing', 'dx_listing_nonce'); ?>
          <input type="hidden" name="action" value="dx_submit_listing">

          <div class="dx-form-row">
            <label class="dx-form-label" for="dx-bn">Business Name <span class="dx-required" aria-label="required">*</span></label>
            <input id="dx-bn" type="text" name="business_name" class="dx-form-input" required maxlength="200">
          </div>

          <div class="dx-form-row">
            <label class="dx-form-label" for="dx-bd">Business Description</label>
            <textarea id="dx-bd" name="business_description" class="dx-form-input" rows="5" maxlength="2000" placeholder="What do you do? Who is it for? What makes you worth listing?"></textarea>
          </div>

          <div class="dx-form-grid">
            <div class="dx-form-row">
              <label class="dx-form-label" for="dx-cat">Business Category</label>
              <select id="dx-cat" name="business_category" class="dx-form-input">
                <option value="">Choose a category</option>
                <?php
                $cats = get_terms([
                    'taxonomy'   => 'business-category',
                    'hide_empty' => false,
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                ]);
                if (!is_wp_error($cats)) {
                    foreach ($cats as $cat) {
                        echo '<option value="' . esc_attr($cat->term_id) . '">' . esc_html(html_entity_decode($cat->name, ENT_QUOTES)) . '</option>';
                    }
                }
                ?>
              </select>
            </div>

            <div class="dx-form-row">
              <label class="dx-form-label" for="dx-dist">Dubai District</label>
              <select id="dx-dist" name="dubai_district" class="dx-form-input">
                <option value="">Choose a district</option>
                <?php
                $districts = get_terms([
                    'taxonomy'   => 'dubai-district',
                    'hide_empty' => false,
                    'orderby'    => 'name',
                    'order'      => 'ASC',
                ]);
                if (!is_wp_error($districts)) {
                    foreach ($districts as $d) {
                        echo '<option value="' . esc_attr($d->term_id) . '">' . esc_html($d->name) . '</option>';
                    }
                }
                ?>
              </select>
            </div>
          </div>

          <h3 class="dx-form-section-title">Your Contact Details</h3>

          <div class="dx-form-grid">
            <div class="dx-form-row">
              <label class="dx-form-label" for="dx-cn">Your Name <span class="dx-required" aria-label="required">*</span></label>
              <input id="dx-cn" type="text" name="contact_name" class="dx-form-input" required maxlength="120">
            </div>

            <div class="dx-form-row">
              <label class="dx-form-label" for="dx-ce">Email <span class="dx-required" aria-label="required">*</span></label>
              <input id="dx-ce" type="email" name="contact_email" class="dx-form-input" required maxlength="160">
            </div>
          </div>

          <h3 class="dx-form-section-title">Optional Details</h3>

          <div class="dx-form-grid">
            <div class="dx-form-row">
              <label class="dx-form-label" for="dx-ph">Phone</label>
              <input id="dx-ph" type="tel" name="contact_phone" class="dx-form-input" placeholder="+971..." maxlength="40">
            </div>

            <div class="dx-form-row">
              <label class="dx-form-label" for="dx-web">Website URL</label>
              <input id="dx-web" type="url" name="website" class="dx-form-input" placeholder="https://" maxlength="200">
            </div>
          </div>

          <div class="dx-form-row">
            <label class="dx-form-label" for="dx-ig">Instagram Handle</label>
            <input id="dx-ig" type="text" name="instagram" class="dx-form-input" placeholder="@yourhandle" maxlength="60">
          </div>

          <div class="dx-form-row">
            <label class="dx-form-label" for="dx-logo">Business Logo</label>
            <input id="dx-logo" type="file" name="business_logo" class="dx-form-input dx-form-file" accept="image/jpeg,image/png,image/webp">
            <span class="dx-form-hint">JPG, PNG, or WebP. Square or near-square works best.</span>
          </div>

          <div class="dx-form-actions">
            <button type="submit" class="dx-btn dx-btn-gold dx-form-submit">Submit Listing →</button>
            <p class="dx-form-disclaimer">By submitting, you agree to our editorial review. Listings go live within 48 hours, free of charge. We will email you with next steps.</p>
          </div>
        </form>

      <?php endif; ?>

    </div>
  </section>

</main>

<?php get_footer();
