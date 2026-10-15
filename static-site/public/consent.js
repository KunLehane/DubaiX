(() => {
  'use strict';
  const settings = document.getElementById('gm-consent').dataset;
  const KEY = 'site-consent-v1';
  const AGE = 180 * 864e5;
  const GA = settings.analytics || '';
  const read = () => {
    try {
      const value = JSON.parse(localStorage.getItem(KEY));
      return value?.version === 1 && typeof value.analytics === 'boolean' && typeof value.embeds === 'boolean' &&
        Number.isFinite(value.savedAt) && value.savedAt <= Date.now() && Date.now() - value.savedAt < AGE ? value : null;
    } catch { return null; }
  };
  let consent = read();
  let analyticsLoaded = false;
  let embedsLoaded = false;
  window['ga-disable-' + GA] = !consent?.analytics;
  function clearAnalytics() {
    const parts = location.hostname.split('.');
    const domains = ['', ...parts.map((_, i) => parts.slice(i).join('.')).filter(d => d.includes('.'))];
    const paths = ['/', ...location.pathname.split('/').filter(Boolean).map((_, i, all) => '/' + all.slice(0, i + 1).join('/'))];
    document.cookie.split(';').forEach(cookie => {
      const name = cookie.trim().split('=')[0];
      if (!/^(_ga(?:_|$)|_gid$|_gat(?:_|$))/.test(name)) return;
      for (const domain of domains) for (const path of paths) {
        document.cookie = `${name}=; Max-Age=0; Path=${path}${domain ? '; Domain=' + domain : ''}; SameSite=Lax`;
      }
    });
  }
  function apply() {
    window['ga-disable-' + GA] = !consent?.analytics;
    if (!consent?.analytics) clearAnalytics();
    if (GA && consent?.analytics && !analyticsLoaded) {
      analyticsLoaded = true;
      window.dataLayer = window.dataLayer || [];
      window.gtag = function () { window.dataLayer.push(arguments); };
      window.gtag('js', new Date());
      window.gtag('config', GA);
      const script = document.createElement('script');
      script.async = true;
      script.src = 'https://www.googletagmanager.com/gtag/js?id=' + GA;
      document.head.append(script);
    }
    if (consent?.embeds && !embedsLoaded) {
      embedsLoaded = true;
      document.querySelectorAll('iframe[data-consent-src]').forEach(frame => { frame.src = frame.dataset.consentSrc; });

    }
  }
  const dialog = document.getElementById('gm-consent');
  const options = document.getElementById('gm-consent-options');
  const preferences = document.getElementById('gm-preferences');
  const analytics = document.getElementById('gm-analytics');
  const embeds = document.getElementById('gm-embeds');
  let opener;
  function show(detailed = false) {
    opener = document.activeElement;
    analytics.checked = consent?.analytics === true;
    embeds.checked = consent?.embeds === true;
    options.hidden = !detailed;
    preferences.setAttribute('aria-expanded', String(detailed));
    if (!dialog.open) dialog.showModal();
  }
  function save(allowAnalytics, allowEmbeds) {
    const revoke = (analyticsLoaded && !allowAnalytics) || (embedsLoaded && !allowEmbeds);
    consent = { version: 1, analytics: allowAnalytics, embeds: allowEmbeds, savedAt: Date.now() };
    try { localStorage.setItem(KEY, JSON.stringify(consent)); } catch { /* This visit still honours the selection. */ }
    window['ga-disable-' + GA] = !allowAnalytics;
    if (!allowAnalytics) clearAnalytics();
    dialog.close();
    opener?.focus();
    // Unload already-running third-party scripts when consent is withdrawn.
    // If storage is unavailable, the reloaded page safely defaults to no consent.
    if (revoke) { location.reload(); return; }
    apply();
  }
  document.addEventListener('click', event => {
    if (event.target.closest('[data-consent-open]')) { event.preventDefault(); show(true); }
  });
  preferences.addEventListener('click', () => {
    options.hidden = !options.hidden;
    preferences.setAttribute('aria-expanded', String(!options.hidden));
  });
  document.getElementById('gm-accept').addEventListener('click', () => save(Boolean(GA), settings.embeds === 'true'));
  document.getElementById('gm-reject').addEventListener('click', () => save(false, false));
  document.getElementById('gm-save').addEventListener('click', () => save(analytics.checked, embeds.checked));
  dialog.addEventListener('cancel', event => { if (!consent) { event.preventDefault(); save(false, false); } });
  window.addEventListener('storage', event => {
    if (event.key === KEY || event.key === null) location.reload();
  });
  apply();
  // Keep the policy readable before making a choice.
  if (!consent && !['/cookie-policy/', '/cookie-policy', settings.privacy].includes(location.pathname)) show();
})();
