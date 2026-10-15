# Dubai Xtra static migration preview

Run `npm run build`, then `node verify.mjs`. Node 22 or newer, no install needed. Cloudflare Pages root: static-site; build: npm run build; output: dist.

158 public routes imported from the 24 September 2026 backup and live public site. Original WordPress theme is untouched at the repository root. Public HTML, media, styles and metadata only; no database, account records, draft submissions or credentials.

## Preview limitations and cutover requirements
This is an intentionally noindex preview, not a production replacement yet.
Listing submissions link to the existing live WordPress form. Search and comments remain on WordPress. Newsletter signup is disabled because no working delivery integration was found in the source template.

Keep WordPress on Cloudways for submissions, draft review, logo uploads and notification email. Before production cutover, configure and test a separate backend origin, fresh published-content synchronisation and frontend rebuild on publishing. Do not point dubaixtra.com at this snapshot before that work is complete.

The migration preserves existing page content and public routes including 13 pagination pages. Broken legacy internal destinations were corrected where a matching page existed; other broken links were unlinked. Existing public review claims were preserved.
