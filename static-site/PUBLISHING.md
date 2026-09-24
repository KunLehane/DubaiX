# Dubai Xtra publishing connection

## Build
- `npm ci --ignore-scripts`
- `npm run build:live`
- `node verify-live.mjs`
- Cloudflare Pages root: `static-site`; output: `dist`.
- Build and runtime variable `WORDPRESS_ORIGIN`: `https://cms.dubaixtra.com` after DNS and SSL are verified. During preview only, the existing `https://dubaixtra.com` origin is supported.
- `INDEXABLE=true` for the final production build. Previews receive an additional noindex response header.

## WordPress
Install `wp-content/mu-plugins/dx-pages-publishing.php` as a regular plugin or mu-plugin, never both. The live site currently uses a regular plugin installed via a ZIP.
Settings > Dubai Xtra Publishing holds a Cloudflare Pages deploy hook. The URL is secret and is never stored in Git. Published posts, terms, menus and approved comments trigger a build request. Draft saves do not publish drafts. The admin status records whether Cloudflare accepted the request; use Cloudflare to confirm the build actually completed.
The public route feed only returns public URLs: `/wp-json/dx-pages/v1/routes`.

## Runtime
Static HTML and assets are served by Pages. Submission pages, their POST handler, search and comment views go to WordPress without caching. The proxy strips admin cookies and Authorization, restricts form actions and checks POST origin. Admin URLs redirect to the backend. WordPress retains draft storage, logo handling and notification email.
The cms alias uses request-scoped WordPress URL filters; the original database URL settings remain unchanged. Outbound sender domains remain dubaixtra.com.

## Launch gates still required
1. Finish GitHub re-verification and add DubaiX to the Cloudflare app access list.
2. Create Pages project against this branch, build live, verify preview.
3. Compare Blacknight DNS records; Cloudflare zone is prepared but pending. Assigned NS: hazel.ns.cloudflare.com and kaiser.ns.cloudflare.com. Apex and www still point to 206.81.0.252, DNS-only; cms is being added with the same target.
4. Activate DNS and provision a valid cms SSL certificate in Cloudways. Keep main WordPress domain primary.
5. Set WORDPRESS_ORIGIN to cms in build/runtime, connect deploy hook, test a draft submission, email, logo upload and approved publication end-to-end.
6. Merge when checks pass, enable production indexing, attach apex/www Pages domains and verify final public URLs.

Do not switch the public website or remove any Cloudways apps before the launch gates pass.
