# Dubai Xtra — Child Theme

## Quick Setup (LocalWP)

1. Create a new site in LocalWP (PHP 8.1+, WordPress 6.x)
2. Install & activate **GeneratePress** theme
3. Install & activate **GeneratePress Premium** plugin
4. Install & activate **ACF Pro** plugin
5. Drop this `dubai-xtra-child` folder into `wp-content/themes/`
6. Activate the **Dubai Xtra** child theme in Appearance → Themes
7. Visit any CPT page to trigger taxonomy seeding (one-time)
8. Go to Settings → Permalinks and click Save (flushes rewrite rules)

## What's Included

```
dubai-xtra-child/
├── style.css                    # Theme identity + full CSS design system
├── functions.php                # Enqueues, includes, GP customizations
├── inc/
│   ├── custom-post-types.php    # 7 CPTs (review, best-list, owner-spotlight, etc.)
│   ├── taxonomies.php           # 5 taxonomies + auto-seeding
│   ├── acf-fields.php           # All ACF field groups registered via PHP
│   └── template-functions.php   # Helper functions for templates
├── assets/
│   ├── css/                     # Additional CSS (if needed)
│   ├── js/
│   │   └── main.js              # Frontend interactions
│   └── images/                  # Theme images
└── template-parts/              # Reusable template partials (build next)
```

## Content Types

| CPT | Slug | Archive URL |
|-----|------|-------------|
| Reviews | `review` | `/review/` |
| Best Lists | `best-list` | `/best/` |
| Owner Spotlights | `owner-spotlight` | `/spotlight/owner/` |
| Business Spotlights | `business-spotlight` | `/spotlight/business/` |
| Things To Do | `things-to-do` | `/things-to-do/` |
| Guides | `guide` | `/guide/` |
| Directory Listings | `listing` | `/directory/` |

## Taxonomies

| Taxonomy | Slug |
|----------|------|
| Business Category | `business-category` |
| Dubai District | `dubai-district` |
| Best List Category | `best-list-category` |
| Things To Do Type | `things-to-do-type` |
| Content Tag | `content-tag` |

## Design System

- **Navy:** #1A1A2E
- **Gold:** #C9A84C
- **Background:** #FAFAF8
- **Headings:** Cormorant Garamond
- **Body:** DM Sans

## Next Steps (VS Code / Claude Code)

1. Build page templates (homepage, archive pages, single templates)
2. Create template-parts for reusable components (review card, listing row, etc.)
3. Add frontend listing submission form + Stripe integration
4. Configure GeneratePress Premium settings (header, footer, sidebars)
5. Create screenshot.png (1200×900) for WP admin
6. Seed initial content
7. Deploy to live hosting
