# Dubai Xtra - Project Context & Rules

## Stack
- WordPress + GeneratePress Premium (parent theme) + `dubai-xtra-child` (this child theme)
- ACF Pro for all custom fields
- Rank Math for SEO (breadcrumbs, meta tags)
- Local by Flywheel for development, Cloudways for production

## Repo Structure
This repo's root **is** the WordPress install root (`public_html/` on Cloudways).
- Tracked: `wp-content/themes/dubai-xtra-child/` (the entire custom theme), `wp-content/mu-plugins/` (if added), `CLAUDE.md`, root project config (`composer.json` / `package.json` if added).
- Ignored: WordPress core, third-party plugins, GeneratePress parent theme, default themes, uploads, caches, logs, LocalWP runtime, editor junk. See `.gitignore`.

## Theme Path
`wp-content/themes/dubai-xtra-child/`

## File Layout
- `style.css` - design tokens (CSS variables) + all component CSS
- `functions.php` - enqueues, theme setup, admin columns, image sizes, query filters, mail handler
- `inc/custom-post-types.php` - 7 CPTs
- `inc/taxonomies.php` - 5 taxonomies + seeded terms
- `inc/acf-fields.php` - all ACF field groups (programmatic)
- `inc/template-functions.php` - helper functions used in templates
- `inc/seed-content.php` - one-shot demo content seeder (admin trigger)
- `inc/migrate-best-posts.php` - one-shot migration: post -> best-list
- `inc/seo-meta.php` - one-shot Rank Math meta + CPT template setter
- `inc/admin-submissions-notice.php` - dashboard pending-submission notice
- `assets/js/main.js` - vanilla JS (sticky header, smooth scroll, category active state)

## Design System
Colors are defined as CSS variables in `style.css` - never hardcode hex values, always use the variable.

| Variable | Value | Use |
|---|---|---|
| `--dx-navy` | `#1A1A2E` | primary brand, headings, dark sections |
| `--dx-gold` | `#C9A84C` | accent, CTAs, section labels |
| `--dx-warm-white` | `#FAFAF8` | page background |
| `--dx-text` | `#2D2D2D` | body text |
| `--dx-light-grey` | `#F0EFEB` | borders, dividers |
| `--dx-mid-grey` | `#888888` | secondary text |
| `--dx-verified` | `#4A7C59` | verified badge green |
| `--dx-white` | `#FFFFFF` | card backgrounds |
| `--dx-gold-hover` | `#B8933F` | gold hover state |
| `--dx-navy-light` | `#2A2A4E` | navy hover state |

## Fonts
- Cormorant Garamond - all headings, score badges, blockquotes
- DM Sans - body, navigation, buttons, tags, labels

## Aesthetic
Editorial, clean, TimeOut / Conde Nast. White space, strong typography, restrained colour, minimal decoration.

## 7 Custom Post Types and URL Slugs
| CPT | Slug | URL pattern |
|---|---|---|
| `review` | review | `/review/{slug}/` |
| `best-list` | best | `/best/{slug}/` |
| `owner-spotlight` | owner-spotlight | `/owner-spotlight/{slug}/` |
| `business-spotlight` | business-spotlight | `/business-spotlight/{slug}/` |
| `things-to-do` | things-to-do | `/things-to-do/{slug}/` |
| `guide` | guide | `/guide/{slug}/` |
| `listing` | directory | `/directory/{slug}/` |

## Taxonomies
| Taxonomy | URL pattern | Shared across |
|---|---|---|
| `business-category` | `/categories/{slug}/` | review, listing, owner-spotlight, business-spotlight, best-list, things-to-do |
| `dubai-district` | `/district/{slug}/` | review, listing, owner-spotlight, business-spotlight, guide, things-to-do |
| `content-tag` | `/tags/{slug}/` | all 7 CPTs |
| `best-list-category` | `/best-category/{slug}/` | best-list only |
| `things-to-do-type` | `/things-type/{slug}/` | things-to-do only |

Note: `business-category` uses `/categories/` (not `/category/`) to avoid conflict with WP's built-in `category` taxonomy. `content-tag` uses `/tags/` for the same reason vs `post_tag`. Taxonomy archive queries are forced to all 7 editorial CPTs via a `pre_get_posts` filter in functions.php.

Seeded terms exist for categories (Golf, Dental, Real Estate, etc.), districts (Dubai Marina, DIFC, Palm Jumeirah, etc.), and TTD types (Weekend Picks, Family, Date Night, etc.). See [wp-content/themes/dubai-xtra-child/inc/taxonomies.php](wp-content/themes/dubai-xtra-child/inc/taxonomies.php).

## Helper Functions
All in [wp-content/themes/dubai-xtra-child/inc/template-functions.php](wp-content/themes/dubai-xtra-child/inc/template-functions.php). Use these instead of rolling your own markup.

**Output helpers (echo directly):**
- `dx_section_header($label, $heading, $link_text, $link_url)` - editorial section header with gold rule line
- `dx_score_badge($score, $size)` - circular navy/gold score badge. `$size = 'sm'` for smaller version
- `dx_meta_line($post_id)` - district + category meta line, e.g. "Dubai Marina - Restaurants"
- `dx_tag($text, $style)` - styled tag pill. Styles: `navy`, `gold`, `sponsored`, `verified`
- `dx_verified_badge()` - green VERIFIED badge for premium listings
- `dx_feature_badge($post_id)` - shows Sponsored Feature / Partner if not Editorial
- `dx_price_range($post_id)` - outputs $/$$/$$$/$$$$
- `dx_render_sponsored_widget()` - site-wide Sponsored Feature sidebar widget

**Query helpers (return WP_Query):**
- `dx_get_featured_reviews($count)`
- `dx_get_current_things_to_do($count)` - filtered to non-expired only
- `dx_get_best_lists($count)`
- `dx_get_featured_spotlight($type)` - $type is `owner-spotlight` or `business-spotlight`
- `dx_get_premium_listings($count)`

**Term helpers:**
- `dx_get_primary_category($post_id)` - returns first business-category term
- `dx_get_primary_district($post_id)` - returns first dubai-district term
- `dx_is_ttd_valid($post_id)` - true if things-to-do not expired

## Coding Rules
- **Never use em dashes.** Use a regular hyphen `-` or rewrite the sentence.
- **All styling uses CSS variables** from `style.css`. Never hardcode `#1A1A2E` - use `var(--dx-navy)`.
- **Use the helper functions.** Don't reimplement section headers, score badges, meta lines, etc.
- **Mobile-first responsive.** Existing breakpoint is `max-width: 768px`. Match that pattern.
- **Max-width 1200px** content areas (use the existing `.dx-section` class).
- **Units:** rem for font sizes and spacing, % and fr for grid widths, px for max-width containers and media queries.
- **Semantic HTML5 always:** `<article>`, `<section>`, `<nav>`, `<aside>`, `<header>`, `<footer>`, `<main>`.
- **One H1 per page.** Section headings use H2, sub-sections H3, etc.
- **Escape all output:** `esc_html()`, `esc_url()`, `esc_attr()`, `wp_kses_post()` for ACF wysiwyg.

## SEO Requirements (every template)
- Rank Math breadcrumbs at the top of single templates: `if (function_exists('rank_math_the_breadcrumbs')) rank_math_the_breadcrumbs();`
- Proper heading hierarchy (one H1 only)
- Internal links between related content
- Semantic HTML5 throughout
- Good fallback `og:title` / `og:description` / `og:image` (Rank Math handles most, but write meaningful titles and excerpts)

## SEO Schema Requirements (specific templates)
- `single-review.php` outputs Review + AggregateRating JSON-LD using the `review_score` ACF field
- `single-listing.php` outputs LocalBusiness JSON-LD using ACF fields: business_name, address, phone, opening_hours, website, geo (if available)

## Highest Converting Page
`page-submit-listing.php` (Template Name: Submit Listing). Analytics show 15% of traffic hits this page with 3m20s dwell time. It must convert: clear value props, free vs premium (AED 800/yr) comparison, social proof (1M+ impressions, 12 categories, 14 districts), FAQ, strong CTA, working frontend submission form.

## Build Order
1. CLAUDE.md (this file)
2. `front-page.php` - homepage
3. `page-submit-listing.php` - submit listing page (Template Name: Submit Listing)
4. `single-review.php`
5. `single-listing.php`
6. `single-owner-spotlight.php`
7. `single-business-spotlight.php`
8. `single-things-to-do.php`
9. `single-best-list.php`
10. `single-guide.php`
11. `archive.php`
