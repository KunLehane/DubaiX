# Publish through GitHub

Posts and best lists now live here. Edit these files, commit to main, and Cloudflare publishes automatically. No WordPress editor, upload or matching WordPress post is required.

## Current Git-owned pages

- `pages/golf-networking-breakfast-dubai.html`
- `pages/best-golf-networking-in-dubai-uae.html`

`entries.json` owns their URL, title, description, sharing image and archive membership. `assets/` owns the supplied event images (served at `/editorial-assets/`). These pages take precedence over WordPress during every build, including builds triggered by WordPress. The old CMS copies remain as recoverable historical copies; editing them will not change the public pages.

## New post or best list

1. Create `pages/your-slug.html` with one `<main id="primary">` and one `<h1>`. Copy the matching article or best-list structure to retain the site styling, then replace the content. HTML is trusted editorial source, not public submission input.
2. Add an entry to `entries.json`: unique `slug` and trailing-slash `path`, `kind` (`article` or `best-list`), `title`, `description`, `image`, ISO `published` date, `content` filename and `archives` array (`path` and `title` for each).
3. Place images in `assets/` and refer to `/editorial-assets/filename.jpg`. Add new events to the relevant existing best-list HTML instead of creating a duplicate list.
4. Run `npm test`, then `WORDPRESS_ORIGIN=https://cms.dubaixtra.com INDEXABLE=true npm run build:live` and `node verify-live.mjs` (PowerShell: set `$env:WORDPRESS_ORIGIN` and `$env:INDEXABLE` separately).
5. Push and check the Cloudflare preview before merging. Main deploys the public domain automatically.

The build inserts new cards into their archives and the homepage, updates existing cards, generates new tag archives when necessary, and includes Git-owned routes in the sitemap. The content files are used even if the matching WordPress page is absent or changed.

## Existing legacy pages

Legacy pages still import from WordPress until moved here. To take ownership of another page, preserve its URL, copy its public `<main>` into a content file and add its metadata to `entries.json`. Do not change business submission handling: WordPress remains responsible for draft listings, notification email and approvals.

The shared `shell.html` preserves the current header, footer and theme styles. It has `{{MAIN}}` and `{{META}}` slots. Build-time WordPress access remains necessary for legacy pages and current approved business listings; publishing Git content takes the normal Cloudflare build time, not an instant update.
