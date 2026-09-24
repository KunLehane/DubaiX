const form=document.getElementById('editorial-search');
const input=document.getElementById('search-query');
const results=document.getElementById('search-results');
const status=document.getElementById('search-status');
input.value=new URLSearchParams(location.search).get('s')||'';
let index=[];
function show(){
 const q=input.value.trim().toLowerCase();results.replaceChildren();
 if(!q){status.textContent='Enter a topic, business or place to search.';return;}
 const words=q.split(/\s+/);const matches=index.filter(e=>words.every(w=>(e.title+' '+e.description).toLowerCase().includes(w)));
 status.textContent=matches.length+' results';
 for(const e of matches){const item=document.createElement('article');item.className='dx-card';const a=document.createElement('a');a.href=e.path;const body=document.createElement('div');body.className='dx-card-body';const h=document.createElement('h2');h.textContent=e.title;const p=document.createElement('p');p.textContent=e.description;body.append(h,p);a.append(body);item.append(a);results.append(item);}
}
form.addEventListener('submit',e=>{e.preventDefault();history.replaceState(null,'','?s='+encodeURIComponent(input.value));show();});
fetch('/search-index.json').then(r=>{if(!r.ok)throw Error();return r.json();}).then(data=>{index=data;show();}).catch(()=>{status.textContent='Search is temporarily unavailable. Please browse the directory or try again.';});
