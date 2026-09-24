import fs from 'node:fs/promises';
import path from 'node:path';
import {parse, renderSync} from 'ultrahtml';
import {querySelector, querySelectorAll} from 'ultrahtml/selector';

export const escape = value => String(value).replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;').replaceAll('"','&quot;').replaceAll("'",'&#39;');
const origin = 'https://dubaixtra.com';
const validRoute = p => typeof p === 'string' && /^\/(?:[a-z0-9-]+\/)+$/.test(p) && !/^\/(?:wp-|submit-listing|listing|search)/.test(p);
export async function loadEditorial(root='content') {
  const entries=JSON.parse(await fs.readFile(path.join(root,'entries.json'),'utf8'));
  const shell=await fs.readFile(path.join(root,'shell.html'),'utf8');
  const seen=new Set();
  for(const e of entries) {
    if(!validRoute(e.path)||seen.has(e.path)) throw Error('Invalid or duplicate editorial path: '+e.path);
    seen.add(e.path);
    if(!e.title||!e.description||!['article','best-list'].includes(e.kind)||!/^pages\/[a-z0-9-]+\.html$/.test(e.content)) throw Error('Invalid editorial metadata: '+e.path);
    if(!Array.isArray(e.archives)||e.archives.some(a=>!validRoute(a.path)||!a.title)) throw Error('Invalid archives: '+e.path);
    if(!e.image?.startsWith('/')||e.image.startsWith('//')||/["'<>]/.test(e.image)) throw Error('Invalid editorial image');
    e.html=await fs.readFile(path.join(root,e.content),'utf8');
    const doc=parse(e.html);
    if(querySelectorAll(doc,'main').length!==1||querySelectorAll(doc,'h1').length!==1) throw Error('Editorial content needs one main and one h1: '+e.path);
    const h1=querySelector(doc,'h1'); h1.children=parse(escape(e.title)).children;
    e.html=renderSync(doc);
  }
  return {entries,shell};
}
export function renderEditorial(e,shell) {
  const schema={ '@context':'https://schema.org','@type':e.kind==='best-list'?'WebPage':'Article',headline:e.title,description:e.description,url:origin+e.path,image:origin+e.image,datePublished:e.published,publisher:{'@type':'Organization',name:'Dubai Xtra'}};
  const meta=`<title>${escape(e.title)} | Dubai Xtra</title><meta name="description" content="${escape(e.description)}"><link rel="canonical" href="${origin+e.path}"><meta property="og:title" content="${escape(e.title)}"><meta property="og:description" content="${escape(e.description)}"><meta property="og:url" content="${origin+e.path}"><meta property="og:image" content="${origin+escape(e.image)}"><meta name="twitter:card" content="summary_large_image"><meta name="dx-content-source" content="github"><script type="application/ld+json">${JSON.stringify(schema).replaceAll('<','\\u003c')}</script>`;
  return shell.replace('{{MAIN}}',e.html).replace('{{META}}',meta);
}
export function card(e) {
  return `<article class="dx-card" data-editorial-path="${e.path}"><a href="${e.path}"><img class="dx-card-image" src="${escape(e.image)}" alt="${escape(e.title)}" loading="lazy"><div class="dx-card-body"><span class="dx-card-meta">${e.kind==='best-list'?'Best Of Dubai':'Things To Do'}</span><h3>${escape(e.title)}</h3><p class="dx-card-summary">${escape(e.description)}</p></div></a></article>`;
}
function replace(node,html) { node.parent.children=node.parent.children.flatMap(n=>n===node?parse(html).children:[n]); }
function routeOf(href) {try {const u=new URL(href,origin);return ['dubaixtra.com','cms.dubaixtra.com','www.dubaixtra.com'].includes(u.host)?u.pathname:null;}catch{return null;}}
// Updates cards wherever they already appear; inserts new content into its archives.
// Native pages always win over the CMS, including after a WordPress deploy hook.
export function updateEditorialCards(doc,route,entries) {
  const present=new Set();
  for(const c of querySelectorAll(doc,'.dx-card')) {
    const a=querySelector(c,'a');const e=entries.find(e=>e.path===routeOf(a?.attributes.href));
    if(e){present.add(e.path);replace(c,card(e));}
  }
  const additions=entries.filter(e=>!present.has(e.path)&&(route==='/'||e.archives.some(a=>a.path===route)));
  if(!additions.length)return;
  const grid=querySelector(doc,'.dx-archive-grid');
  if(grid) {
    grid.children.unshift(...parse(additions.map(card).join('')).children);
    const count=querySelector(doc,'.dx-archive-count');
    if(count) {const old=renderSync(count).match(/>(\d+)/)?.[1];if(old)count.children=parse(`${Number(old)+additions.length} entries`).children;}
  } else if(route==='/') {
    const main=querySelector(doc,'main');
    if(main)main.children.push(...parse(`<section class="dx-section"><div class="dx-section-header"><h2>Latest Articles &amp; Best Lists</h2></div><div class="dx-archive-grid">${additions.map(card).join('')}</div></section>`).children);
  } else throw Error('Archive grid missing: '+route);
}
export function renderArchive(archive,shell) {
  return shell.replace('{{META}}',`<title>${escape(archive.title)} | Dubai Xtra</title><link rel="canonical" href="${origin+archive.path}">`).replace('{{MAIN}}',`<main id="primary" class="dx-archive"><header class="dx-archive-header"><h1>${escape(archive.title)}</h1><span class="dx-archive-count">0 entries</span></header><section class="dx-archive-grid-wrap"><div class="dx-archive-grid"></div></section></main>`);
}
