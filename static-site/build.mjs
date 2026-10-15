import fs from 'node:fs/promises';
import path from 'node:path';
const pages=JSON.parse(await fs.readFile('src/pages.json','utf8'));
const escape=s=>String(s).replaceAll('&','&amp;').replaceAll('"','&quot;').replaceAll('<','&lt;');
const header=await fs.readFile('src/header.html','utf8'),footer=await fs.readFile('src/footer.html','utf8');
await fs.mkdir('dist',{recursive:true});await fs.cp('public','dist',{recursive:true});
for(const p of pages){
 let content=await fs.readFile('src/pages/'+p.file,'utf8');
 if(p.kind==='post'){
  const others=pages.filter(other=>other.kind==='post'&&other.path!==p.path);
  if(others.length)content+=`<section class="mft-related" aria-labelledby="other-posts-title"><h2 id="other-posts-title">Other Posts to Read</h2><div class="mft-post-grid">${others.map(other=>`<article class="mft-post-card">${other.image?`<a href="${other.path}" tabindex="-1" aria-hidden="true"><img src="${other.image}" alt="" loading="lazy" width="1000" height="600"></a>`:''}<div><h3><a href="${other.path}">${escape(other.title)}</a></h3><p>${escape(other.excerpt||other.description)}</p><a href="${other.path}">Read the post</a></div></article>`).join('')}</div><p><a href="/blog/">View all blog posts</a></p></section>`;
 }
 const target=p.path==='/'?'dist/index.html':path.join('dist',p.path,'index.html');
 await fs.mkdir(path.dirname(target),{recursive:true});
 const title=p.title.replaceAll('&amp;','&');
 const html=`<!doctype html><html lang="en-IE"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>${escape(title)}</title><meta name="description" content="${escape(p.description)}"><link rel="canonical" href="https://dubaixtra.com${p.path}"><meta property="og:title" content="${escape(title)}"><meta property="og:description" content="${escape(p.description)}"><meta property="og:url" content="https://dubaixtra.com${p.path}">${p.styles}${p.extraHead||''}<meta name="robots" content="noindex,nofollow"><link rel="stylesheet" href="/site.css"><script src="/site.js" defer></script></head><body class="${escape(p.bodyClass)}"><a class="mft-skip" href="#content">Skip to content</a>${header}${content}${footer}</body></html>`;
 const result=p.image?html.replace('</head>',`<meta property="og:image" content="https://dubaixtra.com${escape(p.image)}"><meta name="twitter:card" content="summary_large_image"></head>`):html;
 await fs.writeFile(target,result);
}
await fs.writeFile('dist/sitemap.xml','<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'+pages.map(p=>`<url><loc>https://dubaixtra.com${p.path}</loc></url>`).join('')+'</urlset>');
console.log(`Built ${pages.length} static pages.`);
