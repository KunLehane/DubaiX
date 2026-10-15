import fs from 'node:fs/promises';
import path from 'node:path';
import { Script } from 'node:vm';
new Script(await fs.readFile('public/consent.js','utf8'),{filename:'consent.js'});
const config=JSON.parse(await fs.readFile('consent.config.json','utf8'));
const esc=s=>String(s).replaceAll('&','&amp;').replaceAll('"','&quot;').replaceAll('<','&lt;');
async function files(dir){const result=[];for(const f of await fs.readdir(dir,{withFileTypes:true})){const p=path.join(dir,f.name);if(f.isDirectory())result.push(...await files(p));else if(p.endsWith('.html'))result.push(p);}return result;}
await fs.rm('dist/cookie-policy.html',{force:true});
const htmlFiles=(await files('dist')).filter(file=>file!==path.join('dist','cookie-policy','index.html'));
const providers=new Set();
const pages=[];
let analytics=Boolean(config.analytics);
for(const file of htmlFiles){
 let html=await fs.readFile(file,'utf8');
 // Remove the existing GA loader and initialisation before browser parsing.
 html=html.replace(/<script\b[^>]*>[\s\S]*?<\/script>/gi,script=>{
   if(/googletagmanager\.com\/gtag\/js|gtag\(['"](?:config|js)['"]/.test(script)){analytics=true;return '';}
   return script;
 });
 html=html.replace(/<iframe\b[^>]*>[\s\S]*?<\/iframe>/gi,frame=>{
   const src=frame.match(/\ssrc=["'](https?:\/\/[^"']+)["']/i);
   if(!src)return frame;
   const host=new URL(src[1].replaceAll('&amp;','&')).hostname;
   providers.add(host);
   return `<div class="gm-embed"><div class="gm-embed-notice"><p>Allow embedded content to load this service from ${esc(host)}.</p><button type="button" data-consent-open>Manage preferences</button></div>${frame.replace(/\ssrc=/i,' data-consent-src=')}</div>`;
 });
 pages.push({file,html});
}
if(analytics&&!config.analytics)throw Error('Analytics detected without a configured measurement ID');
const embeds=providers.size>0;
const purpose=analytics&&embeds?'We use optional analytics to understand visits. With your permission, we also load embedded maps or videos.':analytics?'With your permission, we use Google Analytics to understand visits to this website.':embeds?'With your permission, we load embedded maps or videos from other providers.':'This site currently has no optional analytics or embedded services enabled. We only remember the privacy choice you make here.';
const privacyLink=config.privacy?`<a href="${esc(config.privacy)}">Privacy Policy</a>`:`<a href="${esc(config.contact)}">Privacy enquiries</a>`;
const dialog=`<dialog id="gm-consent" data-analytics="${analytics?esc(config.analytics):''}" data-embeds="${embeds}" data-privacy="${esc(config.privacy||config.contact)}" aria-labelledby="gm-consent-title" aria-describedby="gm-consent-description">
<div class="gm-consent-header"><strong class="gm-site-name">${esc(config.name)}</strong><h2 id="gm-consent-title">Your privacy choices</h2></div>
<p id="gm-consent-description">${purpose} ${analytics||embeds?'These services may use cookies and receive device information. Accept to allow them, or manage your preferences to choose or reject.':''}</p>
<div id="gm-consent-options" hidden><p><strong>Essential - always enabled</strong><br>Remembers your privacy choices and supports basic website functions.</p>
<label ${analytics?'':'hidden'}><input type="checkbox" id="gm-analytics"><span><strong>Analytics</strong><br>Allow Google Analytics to measure website visits.</span></label>
<label ${embeds?'':'hidden'}><input type="checkbox" id="gm-embeds"><span><strong>Embedded content</strong><br>Allow maps or videos from ${esc([...providers].join(', '))}. Providers may use cookies for their own purposes.</span></label>
<div class="gm-consent-actions"><button type="button" id="gm-save">Save preferences</button><button type="button" id="gm-reject">Reject non-essential</button></div></div>
<div class="gm-consent-actions"><button type="button" id="gm-accept">Accept</button><button type="button" id="gm-preferences" aria-expanded="false" aria-controls="gm-consent-options">Manage preferences</button></div>
<p class="gm-consent-links"><a href="/cookie-policy/">Cookie Policy</a>${privacyLink}</p></dialog>`;
const footer=`<div class="gm-cookie-footer"><a href="/cookie-policy/">Cookie Policy</a><button type="button" data-consent-open>Cookie settings</button></div>`;
const assets='<link rel="stylesheet" href="/consent.css"><script src="/consent.js" defer></script>';
for(let {file,html} of pages){
 if(html.includes('id="gm-consent"'))throw Error('Duplicate consent injection');
 html=html.includes('</head>')?html.replace('</head>',assets+'</head>'):html.replace('</html>',assets+'</html>');
 html=html.includes('</body>')?html.replace('</body>',footer+dialog+'</body>'):html.replace('</html>',footer+dialog+'</html>');
 await fs.writeFile(file,html);
}
const breadcrumb=JSON.stringify({'@context':'https://schema.org','@type':'BreadcrumbList',itemListElement:[{'@type':'ListItem',position:1,name:'Home',item:config.url+'/'},{'@type':'ListItem',position:2,name:'Cookie Policy',item:config.url+'/cookie-policy/'}]});
const policy=`<!doctype html><html lang="en-IE"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Cookie Policy | ${esc(config.name)}</title><meta name="description" content="Manage cookie preferences and learn about optional services on ${esc(config.name)}."><link rel="canonical" href="${config.url}/cookie-policy/">${assets}<script type="application/ld+json">${breadcrumb}</script><style>body{margin:0;color:#27312e;background:#fafaf8;font:17px/1.7 Arial,sans-serif}main{max-width:800px;margin:auto;padding:36px 24px}h1,h2{line-height:1.3}h2{font-size:23px;margin-top:32px}a{color:#284b62}button{padding:12px;cursor:pointer}</style></head><body><main id="content"><a href="/">${esc(config.name)} - Home</a><h1>Cookie Policy</h1><p>Updated 15 October 2026.</p>
<h2>Your choices</h2><p>Use Accept to allow the optional services described below, or Manage preferences to choose categories or reject non-essential services. Change your choices at any time using <button type="button" data-consent-open>Cookie settings</button>.</p>
<h2>Remembering your choice</h2><p>Your choice is stored on this device in local storage as <code>site-consent-v1</code> for up to 180 days. It records permitted categories and the time of your choice. Clearing storage or using another browser prompts you again. When storage is unavailable, the choice applies to the current visit.</p>
<h2>Analytics</h2><p>${analytics?`Google Analytics (${esc(config.analytics)}) loads only after Analytics permission. It may set _ga and _ga_* cookies to measure visits. Rejecting prevents its script from loading; withdrawing permission disables it and removes accessible analytics cookies. See <a href="https://policies.google.com/privacy" target="_blank" rel="nofollow noopener">Google's privacy information</a>.`:'No optional visitor analytics is enabled by this consent setup.'}</p>
<h2>Embedded content</h2><p>${embeds?`Maps or videos from ${esc([...providers].join(', '))} stay as placeholders until Embedded content is allowed. Loading them sends information such as your IP address and browser details to those providers, which may use cookies or similar storage under their own policies.`:'No external map or video embeds were detected in the built pages for this release.'}</p>
<h2>Withdrawing permission</h2><p>Withdrawal reloads the page to unload previously enabled services. It cannot undo information already sent or remove cookies on other providers' domains. Use your browser settings to manage those cookies.</p>
<h2>Other resources</h2><p>Essential website features continue without optional consent. Fonts, images and other resources may be requested from external providers independently of optional analytics or embeds. Following an external link takes you to that provider's website.</p>
<h2>Questions</h2><p>Contact us through our <a href="${esc(config.contact)}">contact page</a>. ${privacyLink}</p></main>${footer}${dialog}</body></html>`;
await fs.mkdir('dist/cookie-policy',{recursive:true});
await fs.writeFile('dist/cookie-policy/index.html',policy);
await fs.writeFile('dist/consent-inventory.json',JSON.stringify({analytics:analytics?config.analytics:null,providers:[...providers],pages:pages.length+1},null,2));
// Fail the build if any gated resource remains eager or a page missed controls.
for(const file of await files('dist')){const html=await fs.readFile(file,'utf8');if(!html.includes('id="gm-consent"')||/<iframe\b[^>]*\ssrc=["']https?:/i.test(html)||/<script[^>]*src=["']https?:\/\/www.googletagmanager.com/i.test(html))throw Error('Consent gate failed: '+file);}
console.log(`Consent enabled on ${pages.length+1} pages; analytics=${analytics}; embed providers=${[...providers].join(', ')||'none'}`);
