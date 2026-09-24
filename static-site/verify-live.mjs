import fs from 'node:fs/promises';
import path from 'node:path';
import {parse} from 'ultrahtml';
import {querySelectorAll} from 'ultrahtml/selector';
const missing=[];
async function scan(dir){for(const e of await fs.readdir(dir,{withFileTypes:true})){const file=path.join(dir,e.name);if(e.isDirectory())await scan(file);else if(e.name==='index.html'){
 const html=await fs.readFile(file,'utf8'); const doc=parse(html);
 for(const node of querySelectorAll(doc,'a,link,img,script')){
  const ref=node.attributes.href||node.attributes.src;if(!ref?.startsWith('/')||ref.startsWith('//'))continue;
  const u=new URL(ref,'https://dubaixtra.com');if(u.pathname==='/submit-listing/'||u.pathname.startsWith('/wp-admin')||u.pathname==='/wp-login.php'||u.search)continue;
  const target=path.join('dist',decodeURIComponent(u.pathname),path.extname(u.pathname)?'':'index.html');
  try{await fs.access(target);}catch{missing.push({file,ref});}
 }
}}}
await scan('dist'); console.log(JSON.stringify({missing},null,2));if(missing.length)process.exitCode=1;
