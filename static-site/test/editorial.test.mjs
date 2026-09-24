import {test} from 'node:test';
import assert from 'node:assert/strict';
import {parse,renderSync} from 'ultrahtml';
import {loadEditorial,renderEditorial,renderArchive,updateEditorialCards} from '../editorial.mjs';

test('Git-owned article and list render without a WordPress copy',async()=>{
  const {entries,shell}=await loadEditorial();
  for(const e of entries) {
    const html=renderEditorial(e,shell);
    assert.match(html,/<meta name="dx-content-source" content="github">/);
    assert.ok(html.includes('https://dubaixtra.com'+e.path));
    assert.ok(!html.includes('{{MAIN}}')&&!html.includes('{{META}}'));
    assert.ok(!html.includes('cms.dubaixtra.com'));
  }
  const article=renderEditorial(entries[0],shell);
  assert.match(article,/background-image: url\('\/editorial-assets\/five-iron-breakfast-network.png'\)/);
  assert.ok(article.indexOf('five-iron-yas-bay.png')>article.indexOf('New Five Iron Golf opening in Yas Bay'));
  assert.ok(renderEditorial(entries[1],shell).includes('Monthly Event'));
});
test('new Git-only post is discoverable and CMS cards cannot overwrite its metadata',async()=>{
  const {entries,shell}=await loadEditorial();
  const e={...entries[0],path:'/things-to-do/git-only-test/',title:'Git-only & new',archives:[{path:'/tags/new-topic/',title:'New topic'}]};
  const doc=parse(renderArchive(e.archives[0],shell));
  updateEditorialCards(doc,'/tags/new-topic/',[e]);
  updateEditorialCards(doc,'/tags/new-topic/',[e]);
  const html=renderSync(doc);
  assert.equal((html.match(/data-editorial-path=/g)||[]).length,1);
  assert.match(html,/Git-only &amp; new/);
  const old=parse(`<main><div class="dx-archive-grid"><article class="dx-card"><a href="https://cms.dubaixtra.com${e.path}">Old WordPress title</a></article></div></main>`);
  updateEditorialCards(old,'/tags/new-topic/',[e]);
  assert.ok(!renderSync(old).includes('Old WordPress title'));
  assert.ok(renderSync(old).includes('Git-only &amp; new'));
});
