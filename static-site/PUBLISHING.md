# Dubai Xtra publishing

## Editorial: GitHub first

Use `static-site/content/` for new articles, Things To Do posts and best lists. Follow [content/README.md](content/README.md). The breakfast networking article and existing Best Golf Networking list are migrated; their HTML, metadata and event images are owned by Git. No matching WordPress entry is required. Git-owned routes take precedence on every build, including a WordPress-triggered build. Preserve existing URLs when migrating another legacy page.

Cards, archives, sitemap and the public search index include Git-native content. Legacy pages and approved business listings continue to refresh from the anonymous WordPress source. The shared HTML shell retains the current design. Content does not deploy instantly: allow the normal Cloudflare build to finish.

## Build and deploy

- `npm ci --ignore-scripts`
- `npm test`
- Set `WORDPRESS_ORIGIN=https://cms.dubaixtra.com`; set `INDEXABLE=true` only for production.
- `npm run build:live` then `node verify-live.mjs`.
- Cloudflare Pages project `dubaixtra`, root `static-site`, output `dist`, production branch `main`.
- Branch previews must pass before merging. Main deploys to `https://dubaixtra.com`.
- `dist/build-manifest.json` includes `editorialRoutes`; Git-owned pages also have `meta[name=dx-content-source]` set to `github`.

## Business submissions and approvals

WordPress remains at `https://cms.dubaixtra.com/wp-admin/`. Submission pages, their POST handler and comment views are proxied without caching. The proxy strips admin credentials, restricts form actions and validates POST origin. WordPress retains draft listings, image upload handling and notification email. Publishing an approved listing triggers the Cloudflare deploy hook. Public search uses the generated index, including Git content and current WordPress pages.

The live publishing plugin is installed as a regular plugin. Source: `wp-content/mu-plugins/dx-pages-publishing.php`. Never install both copies. Settings > Dubai Xtra Publishing stores the private deploy-hook URL, which must never enter Git. Existing WordPress editorial copies remain as history; the public build ignores them where Git owns the same path.

CMS responses are noindex. Production is indexable; branch previews are noindex. The public domain and www use Cloudflare Pages; the CMS stays on Cloudways. Do not delete the backend or any hosting applications as part of editorial publishing.
