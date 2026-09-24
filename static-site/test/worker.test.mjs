import {test} from 'node:test';
import assert from 'node:assert/strict';
import worker from '../worker.mjs';
const env={WORDPRESS_ORIGIN:'https://cms.dubaixtra.com',ASSETS:{fetch:async()=>new Response('static')}};
test('static previews are noindex; production remains indexable',async()=>{
  const preview=await worker.fetch(new Request('https://preview.pages.dev/directory/'),env);
  assert.equal(preview.headers.get('x-robots-tag'),'noindex, nofollow');
  const production=await worker.fetch(new Request('https://dubaixtra.com/directory/'),env);
  assert.equal(production.headers.get('x-robots-tag'),null);
});
test('admin is sent directly to the backend without forwarding credentials',async()=>{
  const r=await worker.fetch(new Request('https://dubaixtra.com/wp-admin/'),env);
  assert.equal(r.status,302); assert.equal(r.headers.get('location'),'https://cms.dubaixtra.com/wp-admin/');
});
test('foreign form origins and unrelated actions are rejected',async()=>{
  const foreign=await worker.fetch(new Request('https://dubaixtra.com/wp-admin/admin-post.php',{method:'POST',headers:{origin:'https://evil.example'},body:'x'}),env);
  assert.equal(foreign.status,403);
  const invalid=await worker.fetch(new Request('https://dubaixtra.com/wp-admin/admin-post.php',{method:'POST',headers:{origin:'https://dubaixtra.com'},body:new URLSearchParams({action:'delete_user'})}),env);
  assert.equal(invalid.status,400);
});
test('live form nonce is not cached and backend URLs are rewritten',async(t)=>{
  t.mock.method(globalThis,'fetch',async(_url,options)=>{
    assert.equal(options.headers.get('cookie'),null);assert.equal(options.headers.get('authorization'),null);
    return new Response('<form action="https://cms.dubaixtra.com/wp-admin/admin-post.php"><input value="fresh-nonce"></form>',{headers:{'content-type':'text/html','set-cookie':'private=1'}});
  });
  const r=await worker.fetch(new Request('https://dubaixtra.com/submit-listing/',{headers:{cookie:'wordpress_logged_in=secret',authorization:'secret'}}),env);
  assert.equal(r.headers.get('cache-control'),'no-store');assert.equal(r.headers.get('set-cookie'),null);
  assert.match(await r.text(),/https:\/\/dubaixtra.com\/wp-admin\/admin-post.php/);
});
test('successful submission redirects back to public confirmation page',async(t)=>{
  t.mock.method(globalThis,'fetch',async()=>new Response(null,{status:302,headers:{location:'https://cms.dubaixtra.com/submit-listing/?dx_submitted=1#submit-form-section'}}));
  const r=await worker.fetch(new Request('https://dubaixtra.com/wp-admin/admin-post.php',{method:'POST',headers:{origin:'https://dubaixtra.com'},body:new URLSearchParams({action:'dx_submit_listing'})}),env);
  assert.equal(r.headers.get('location'),'https://dubaixtra.com/submit-listing/?dx_submitted=1#submit-form-section');
});

test('search uses the built index so Git-only posts are included',async()=>{
 let target;
 const r=await worker.fetch(new Request('https://dubaixtra.com/?s=Five+Iron'),{...env,ASSETS:{fetch:async(req)=>{target=req.url;return new Response('search');}}});
 assert.equal(new URL(target).pathname,'/search/');assert.equal(new URL(target).searchParams.get('s'),'Five Iron');assert.equal(await r.text(),'search');
});
