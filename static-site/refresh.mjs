import fs from 'node:fs/promises';
import path from 'node:path';
import { parse, renderSync, walkSync } from 'ultrahtml';
import { querySelector, querySelectorAll } from 'ultrahtml/selector';
import {loadEditorial,renderEditorial,renderArchive,updateEditorialCards} from './editorial.mjs';
const editorial=await loadEditorial();
const nativePages=new Map(editorial.entries.map(e=>[e.path,renderEditorial(e,editorial.shell)]));
const nativeArchives=new Map(editorial.entries.flatMap(e=>e.archives).map(a=>[a.path,a]));

// This exporter uses anonymous public HTML, never the WordPress database or admin API.
const source = new URL(process.env.WORDPRESS_ORIGIN || 'https://dubaixtra.com');
const publicOrigin = 'https://dubaixtra.com';
if (source.protocol !== 'https:' || source.username || source.password) throw Error('HTTPS origin required');
const allowedHosts = new Set([source.host, 'dubaixtra.com', 'www.dubaixtra.com']);
const pages = new Map(), assets = new Map(), pending = new Set(['/']);
for(const p of [...nativePages.keys(),...nativeArchives.keys()]) pending.add(p);
const done = new Set();
const buildRevision = Date.now().toString();
const remove = n => { if(n.parent) n.parent.children = n.parent.children.filter(c => c !== n); };
function localURL(value, base = source) {
  try {
    const u = new URL(value.replaceAll('&amp;', '&'), base);
    if (!allowedHosts.has(u.host)) return null;
    // WordPress carries query arguments into archive pagination links.
    u.searchParams.delete('_dx_build');
    return u;
  } catch { return null; }
}
function pagePath(value) {
  const u = localURL(value);
  if (!u || u.search || /\.(?:php|xml|json|txt|css|js|jpe?g|png|webp|pdf|svg)$/i.test(u.pathname) || /^\/(?:wp-|feed\/)/.test(u.pathname)) return null;
  return u.pathname.endsWith('/') ? u.pathname : u.pathname + '/';
}
function asset(value, base = source) {
  const u = localURL(value, base);
  if (!u) return value;
  if (/^\/(?:wp-content|wp-includes)\//.test(u.pathname) && /\.(?:css|js|png|jpe?g|webp|gif|svg|ico|woff2?|ttf|eot|mp4|webm|json)$/i.test(u.pathname)) {
    assets.set(u.pathname, u.pathname + u.search);
    return u.pathname;
  }
  return u.pathname + u.search + u.hash;
}
function css(text, base) { return text.replace(/url\(\s*(['"]?)([^)'"\s]+)\1\s*\)/g, (_,q,u) => `url('${asset(u,base).replaceAll("'", '%27')}')`); }
async function get(route, attempt=0) {
  let url = new URL(route, source);
  // Cloudways may still cache the previous published HTML after a WP save.
  // Fetch fresh anonymous pages for this build while retaining stable asset URLs.
  if (!/^\/(?:wp-content|wp-includes)\//.test(url.pathname)) url.searchParams.set('_dx_build', buildRevision+'-'+attempt);
  for(let redirects=0; redirects<5; redirects++) {
    if(url.origin !== source.origin) throw Error('Unexpected origin redirect: '+url.origin);
    const r = await fetch(url, { redirect:'manual', signal:AbortSignal.timeout(45000), headers:{'User-Agent':'DubaiXtra-PublicBuild/1.0'} });
    if(r.status >= 300 && r.status < 400 && r.headers.has('location')) { url=new URL(r.headers.get('location'),url); continue; }
    return r;
  }
  throw Error('Too many redirects');
}
const routeResponse=await get('/wp-json/dx-pages/v1/routes');
if(!routeResponse.ok) throw Error('Public route feed unavailable: '+routeResponse.status);
const routeFeed=await routeResponse.json();
if(!Array.isArray(routeFeed))throw Error('Invalid public route feed');
for(const route of routeFeed){const p=pagePath(route);if(p)pending.add(p);}
while(pending.size) {
  if(done.size>1000) throw Error('Unexpected route count');
  const batch=[...pending].slice(0,4); batch.forEach(p=>{pending.delete(p);done.add(p);});
  await Promise.all(batch.map(async route=>{
    // Forms and search are always rendered live; no cached nonces are published.
    if(route==='/submit-listing/') return;
    let input=nativePages.get(route);
    if(!input) {
      const r=await get(route);
      if(r.status===404 && nativeArchives.has(route)) input=renderArchive(nativeArchives.get(route),editorial.shell);
      else if(r.status===404) return;
      else if(!r.ok) throw Error(route+': HTTP '+r.status);
      else input=await r.text();
    }
    let doc=parse(input);
    // The origin occasionally returns incomplete archive HTML with HTTP 200.
    // Retry fresh reads; never publish an empty replacement archive.
    if(nativeArchives.has(route)&&!nativePages.has(route)) {
      for(let attempt=1;attempt<=3&&!querySelector(doc,'.dx-archive-grid');attempt++) {
        await new Promise(resolve=>setTimeout(resolve,1000*attempt));
        const retry=await get(route,attempt);
        if(retry.ok)doc=parse(await retry.text());
      }
    }
    const head=querySelector(doc,'head'),body=querySelector(doc,'body');
    updateEditorialCards(doc,route,editorial.entries);
    if(!head||!body) throw Error('Invalid HTML: '+route);
    if((body.attributes.class||'').includes('logged-in') || querySelector(doc,'#wpadminbar')) throw Error('Authenticated HTML refused');
    for(const a of querySelectorAll(doc,'a')) { const p=pagePath(a.attributes.href||''); if(p&&!done.has(p)) pending.add(p); }
    for(const link of querySelectorAll(head,'link')) if(['https://api.w.org/','EditURI','shortlink','pingback'].includes(link.attributes.rel) || /(?:rss|atom|json)/.test(link.attributes.type||'')) remove(link);
    for(const f of querySelectorAll(body,'form')) {
      if(f.attributes.id==='commentform') {
        const replacement=parse('<p><a href="'+publicOrigin+route+'?comments=1#respond">Read or leave a comment</a></p>').children;
        f.parent.children=f.parent.children.flatMap(n=>n===f?replacement:[n]);
      }
    }
    // GeneratePress's logo heading must not compete with each page's main heading.
    for(const h of querySelectorAll(body,'h1')) if((h.attributes.class||'').includes('main-title')) h.name='p';
    walkSync(doc,n=>{
      if(n.attributes) for(const key of ['href','src','poster','action','srcset','style']) {
        const value=n.attributes[key]; if(!value) continue;
        if(key==='srcset') n.attributes[key]=value.split(',').map(v=>v.trim().split(/\s+/).map((x,i)=>i?x:asset(x)).join(' ')).join(', ');
        else if(key==='style') n.attributes[key]=css(value,source);
        else n.attributes[key]=asset(value);
      }
      if(n.name==='style') for(const c of n.children) if(c.value)c.value=css(c.value,source);
    });
    for(const a of querySelectorAll(body,'a')) {
      if(a.attributes.href==='/author/')a.attributes.href='/about/';
      if(['/listing/365-golf-events-dubai','/Xtreme%20Jet%20Ski%20Dubai'].includes(a.attributes.href))delete a.attributes.href;
    }
    for(const canonical of querySelectorAll(head,'link')) if(canonical.attributes.rel==='canonical')canonical.attributes.href=publicOrigin+route;
    let html=renderSync(doc).replaceAll(source.origin,publicOrigin);
    if(process.env.INDEXABLE!=='true') html=html.replace('</head>','<meta name="robots" content="noindex,nofollow"></head>');
    pages.set(route,html);
  }));
  console.log('Public pages refreshed:',pages.size);
}
const searchIndex=[...pages].filter(([p])=>p!=='/').map(([p,h])=>{const d=parse(h);const title=querySelector(d,'h1');const desc=querySelectorAll(d,'meta').find(n=>n.attributes.name==='description');return {path:p,title:title?renderSync(title).replace(/<[^>]*>/g,''):p,description:desc?.attributes.content||''};});
const searchHtml=editorial.shell.replace('{{META}}','<title>Search | Dubai Xtra</title><meta name="robots" content="noindex,follow">').replace('{{MAIN}}','<main id="primary" class="dx-archive"><header class="dx-archive-header"><h1>Search Dubai Xtra</h1><form id="editorial-search" action="/search/" method="get"><label for="search-query">Search articles, places and businesses</label><input id="search-query" type="search" name="s"><button type="submit">Search</button></form><p id="search-status" role="status">Loading search…</p></header><section class="dx-archive-grid-wrap"><div id="search-results" class="dx-archive-grid"></div></section></main><script src="/editorial-assets/search.js" defer></script>');
pages.set('/search/',searchHtml);
if(pages.size<100) throw Error('Incomplete export: refusing to replace the site');
const assetDone=new Set();
const outputRoot=path.resolve('dist');
const projectRoot=await fs.realpath('.');
if(path.dirname(outputRoot)!==projectRoot)throw Error('Unsafe build directory');
try{if(await fs.realpath(outputRoot)!==outputRoot)throw Error('Build directory must not be a symlink');}catch(e){if(e.code!=='ENOENT')throw e;}
await fs.rm(outputRoot,{recursive:true,force:true});
await fs.mkdir(outputRoot,{recursive:true});
while([...assets.keys()].some(k=>!assetDone.has(k))) {
  const batch=[...assets].filter(([k])=>!assetDone.has(k)).slice(0,6);
  await Promise.all(batch.map(async([name,route])=>{
    assetDone.add(name); const r=await get(route); if(!r.ok) throw Error('Asset '+name+': '+r.status);
    let buffer=Buffer.from(await r.arrayBuffer());
    if(name.endsWith('.css')) buffer=Buffer.from(css(buffer.toString(),new URL(route,source)));
    const target=path.resolve('dist','.'+decodeURIComponent(name));
    if(!target.startsWith(path.resolve('dist')+path.sep)) throw Error('Unsafe asset path');
    await fs.mkdir(path.dirname(target),{recursive:true});await fs.writeFile(target,buffer);
  }));
}
await fs.cp('content/assets','dist/editorial-assets',{recursive:true});
await fs.writeFile('dist/search-index.json',JSON.stringify(searchIndex));
for(const [route,html] of pages) { const file=path.join('dist',route,'index.html');await fs.mkdir(path.dirname(file),{recursive:true});await fs.writeFile(file,html); }
await fs.writeFile('dist/build-manifest.json',JSON.stringify({builtAt:new Date().toISOString(),routes:[...pages.keys()],editorialRoutes:[...nativePages.keys()],assets:[...assetDone]}));
await fs.writeFile('dist/sitemap.xml','<?xml version="1.0"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'+[...pages.keys(),'/submit-listing/'].filter(p=>p!=='/search/').map(p=>`<url><loc>${publicOrigin}${p}</loc></url>`).join('')+'</urlset>');
await fs.writeFile('dist/robots.txt',process.env.INDEXABLE==='true'?`User-agent: *\nAllow: /\nSitemap: ${publicOrigin}/sitemap.xml\n`:'User-agent: *\nDisallow: /\n');
await fs.writeFile('dist/404.html','<!doctype html><html lang="en"><title>Page not found | Dubai Xtra</title><h1>Page not found</h1><p><a href="/">Return to Dubai Xtra</a></p></html>');
await fs.copyFile('worker.mjs','dist/_worker.js');
console.log(JSON.stringify({pages:pages.size,assets:assetDone.size}));
