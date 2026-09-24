const PUBLIC_HOSTS = new Set(['dubaixtra.com', 'www.dubaixtra.com']);
export default {
  async fetch(request, env) {
    const url=new URL(request.url);
    const origin=env.WORDPRESS_ORIGIN;
    const backend=origin ? new URL(origin) : null;
    if(backend && (backend.protocol!=='https:' || backend.origin===url.origin)) return new Response('Invalid backend configuration',{status:503});
    const isAdmin=/^\/(?:wp-admin(?:\/|$)|wp-login\.php)/.test(url.pathname) && url.pathname!=='/wp-admin/admin-post.php';
    if(isAdmin) return backend ? Response.redirect(new URL(url.pathname+url.search,backend),302) : new Response('Admin connection pending',{status:503});
    const dynamic=url.pathname==='/submit-listing/' || url.pathname==='/wp-admin/admin-post.php' || url.pathname==='/wp-comments-post.php' || url.searchParams.has('comments');
    if(dynamic) {
      if(!backend) return new Response('Submissions are being connected. Please try again shortly.',{status:503});
      if(!['GET','HEAD','POST'].includes(request.method)) return new Response('Method not allowed',{status:405});
      if(request.method==='POST') {
        if(request.headers.get('origin')!==url.origin) return new Response('Invalid form origin',{status:403});
        const size=Number(request.headers.get('content-length')||0);
        if(size>10*1024*1024) return new Response('Upload too large',{status:413});
        if(!['/wp-admin/admin-post.php','/wp-comments-post.php'].includes(url.pathname)) return new Response('Method not allowed',{status:405});
        if(url.pathname==='/wp-admin/admin-post.php') {
          const form=await request.clone().formData();
          if(form.get('action')!=='dx_submit_listing') return new Response('Invalid form action',{status:400});
        }
      }
      const target=new URL(url.pathname+url.search,backend);target.searchParams.delete('comments');
      const headers=new Headers(request.headers);
      // Public forms never forward an admin session or client authentication to WordPress.
      for(const key of ['cookie','authorization','host','cf-connecting-ip','x-forwarded-for']) headers.delete(key);
      headers.set('origin',backend.origin);headers.set('referer',new URL('/submit-listing/',backend).href);
      const upstream=await fetch(target,{method:request.method,headers,body:request.method==='POST'?request.body:undefined,redirect:'manual'});
      const out=new Headers(upstream.headers);
      out.delete('set-cookie');out.delete('content-length');out.set('cache-control','no-store');
      if(!PUBLIC_HOSTS.has(url.host))out.set('x-robots-tag','noindex, nofollow');
      const location=out.get('location');
      if(location) {
        const dest=new URL(location,target);
        if(dest.origin===backend.origin || PUBLIC_HOSTS.has(dest.host)) out.set('location',url.origin+dest.pathname+dest.search+dest.hash);
      }
      if((out.get('content-type')||'').includes('text/html')) {
        let html=await upstream.text();
        for(const host of [backend.origin,'https://dubaixtra.com','http://dubaixtra.com'])html=html.replaceAll(host,url.origin);
        return new Response(html,{status:upstream.status,headers:out});
      }
      return new Response(upstream.body,{status:upstream.status,headers:out});
    }
    if(!['GET','HEAD'].includes(request.method))return new Response('Method not allowed',{status:405});
    if(url.searchParams.has('s')) url.pathname='/search/';
    const response=await env.ASSETS.fetch(new Request(url,request));
    const result=new Response(response.body,response);
    if(!PUBLIC_HOSTS.has(url.host))result.headers.set('x-robots-tag','noindex, nofollow');
    return result;
  }
};
