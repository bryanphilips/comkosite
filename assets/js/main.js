/* ======================================================================
 * main.js — Combrok front-end (ribbon + nav + hero + stats + reveal)
 * ====================================================================== */
document.addEventListener('DOMContentLoaded', () => {
  const oc = document.getElementById('mainNav');
  if (!oc) return;
  oc.addEventListener('click', (e) => {
    const link = e.target.closest('a.nav-link, a.btn');
    if (!link) return;
    if (window.bootstrap?.Offcanvas) {
      (bootstrap.Offcanvas.getInstance(oc) || new bootstrap.Offcanvas(oc)).hide();
    }
  });
});
(function () {
  'use strict';

  /* ----- Global init guard */
  if (window.__COMBROK_INIT_DONE__) {
    console.warn('[Combrok] main.js already initialized — skipping duplicate run.');
    return;
  }
  window.__COMBROK_INIT_DONE__ = true;

  /* Debug controls: add ?ribbondbg=1 to URL OR localStorage.setItem('ribbondbg','1') */
  const ribbonDebug =
    /(^|[?&])ribbondbg=1(&|$)/.test(location.search) ||
    window.RIBBON_DEBUG === true ||
    localStorage.getItem('ribbondbg') === '1';

  const LOG  = (...a) => ribbonDebug && console.log('[Ribbon]', ...a);
  const WARN = (...a) => ribbonDebug && console.warn('[Ribbon]', ...a);

  /* Helpers */
  const $  = (sel, root = document) => root.querySelector(sel);
  const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

  /* =========================
   * Ribbon slider (RIGHT -> CENTER -> LEFT) with hover pause.
   * Fallback marquee when necessary or forced (?ribbon=marquee)
   * ========================= */
  function initRibbonSimple({ holdMs = 5200, slideGap = 600 } = {}) {
    if (window.__RIBBON_STARTED__) return;
    window.__RIBBON_STARTED__ = true;

    const forceMarquee = /(^|[?&])ribbon=marquee(&|$)/.test(location.search);
    const root = document.getElementById('ribbonRotator');
    if (!root) return;

    if (forceMarquee) return enableRibbonFallback(root);

    const msgs = $$('.ribbon-msg', root);
    LOG('Found messages:', msgs.length);
    if (!msgs.length) return;

    msgs.forEach(m => m.classList.remove('show','exit'));

    let i = 0, alive = true, paused = false;

    /* Pause on hover for desktop */
    if (matchMedia('(hover:hover)').matches) {
      root.addEventListener('mouseenter', () => paused = true);
      root.addEventListener('mouseleave', () => paused = false);
    }

    const showMsg = (idx) => {
      const el = msgs[idx]; if (!el) return;
      void el.offsetWidth;           // reflow
      el.classList.remove('exit');
      el.classList.add('show');
      LOG(`ENTER idx=${idx} (→ center)`);
    };
    const hideMsg = (idx) => {
      const el = msgs[idx]; if (!el) return;
      el.classList.remove('show');
      el.classList.add('exit');
      LOG(`EXIT  idx=${idx} (center → ←)`);
    };

    const enableFallbackIfInvisible = (idx) => {
      const el = msgs[idx];
      if (!el) return enableRibbonFallback(root);
      const cs = getComputedStyle(el);
      const visible = el.offsetParent !== null && parseFloat(cs.opacity) > 0.2;
      if (!visible) enableRibbonFallback(root);
      return visible;
    };

    // pause-aware wait
    const wait = (ms, cb) => {
      const start = performance.now();
      const tick = () => {
        if (!alive) return;
        if (!paused && performance.now() - start >= ms) return cb();
        requestAnimationFrame(tick);
      };
      tick();
    };

    const step = () => {
      if (!alive) return;
      const cur = i;
      wait(holdMs, () => {
        hideMsg(cur);
        wait(slideGap + 200, () => {
          i = (cur + 1) % msgs.length;
          showMsg(i);
          setTimeout(() => { if (enableFallbackIfInvisible(i) && alive) step(); }, 50);
        });
      });
    };

    showMsg(i);
    setTimeout(() => { if (enableFallbackIfInvisible(i)) step(); }, 60);

    // Debug helpers
    window.__ribbon = {
      stop(){ alive = false; WARN('Ribbon stopped'); },
      start(){ if(!alive){ alive = true; step(); WARN('Ribbon started'); } },
      jump(n){ i = ((n|0) % msgs.length + msgs.length) % msgs.length; msgs.forEach(m=>m.classList.remove('show','exit')); showMsg(i); LOG('Jumped to', i); }
    };
  }

  function enableRibbonFallback(rotEl){
    const texts = $$('.ribbon-msg', rotEl)
      .map(m => m.textContent.trim()).filter(Boolean);
    const trackHTML = texts.map(t => `<span class="chunk">${t}</span>`).join('');
    rotEl.innerHTML =
      `<div class="ribbon-fallback">
         <div class="ribbon-track">${trackHTML}${trackHTML}</div>
       </div>`;
    enableRibbonTouchPause(rotEl.querySelector('.ribbon-fallback'));
    WARN('Fallback marquee enabled.');
  }

  function enableRibbonTouchPause(container){
    const track = container.querySelector('.ribbon-track');
    if (!track) return;
    const pause = () => track.style.animationPlayState = 'paused';
    const play  = () => track.style.animationPlayState = 'running';
    container.addEventListener('touchstart', pause, {passive:true});
    container.addEventListener('touchend',   play,  {passive:true});
    container.addEventListener('touchcancel',play,  {passive:true});
  }

  /* ===== Other site glue ===== */

  function initMenus() {
    const offcanvasEl = $('#mainNav');
    if (!offcanvasEl) return;
    // Close drawer when a nav link is clicked
    offcanvasEl.addEventListener('click', (e) => {
      const link = e.target.closest('a.nav-link, a.btn');
      if (!link) return;
      if (typeof bootstrap !== 'undefined' && bootstrap.Offcanvas) {
        (bootstrap.Offcanvas.getInstance(offcanvasEl) || new bootstrap.Offcanvas(offcanvasEl)).hide();
      }
    });
  }

  function initNavbarShadow() {
    const nav = $('.premium-navbar');
    if (!nav) return;
    const onScroll = () => nav.classList.toggle('nav-scrolled', window.scrollY > 8);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  function initHeroCarousel(){
    const el = $('#heroCarousel');
    if (!el || !window.bootstrap) return;
    new bootstrap.Carousel(el, { interval: 5500, pause: 'hover', ride: 'carousel' });
  }

  function initStatsFloat(){
    const wrap = $('#statsFloat');
    if (!wrap) return;
    const cards = $$('.stat-card', wrap);
    if (!cards.length) return;
    let i = 0;
    cards.forEach((c, idx) => c.classList.toggle('show', idx === 0));
    setInterval(() => {
      cards.forEach((c, idx) => c.classList.toggle('show', idx === i));
      i = (i + 1) % cards.length;
    }, 3000);
  }

  function initReveal() {
    const els = $$('.reveal, .metric, .soft');
    if (!els.length) return;
    if ('IntersectionObserver' in window) {
      const io = new IntersectionObserver((es)=>es.forEach(e=>e.isIntersecting&&e.target.classList.add('_in')), {threshold:.18});
      els.forEach(el=>io.observe(el));
    } else {
      els.forEach(el=>el.classList.add('_in'));
    }
  }

  function initSustainabilityAwards() {
  const awardsBox = document.querySelector('.hero-awards');
  const slider = document.querySelector('[data-award-slider]');
  const dotsWrap = document.querySelector('[data-award-dots]');
  if (!awardsBox || !slider) return; // Only run on sustainability page

  const slides = Array.from(slider.querySelectorAll('.award-slide'));
  const dots = dotsWrap ? Array.from(dotsWrap.querySelectorAll('.dot')) : [];

  if (!slides.length) return;

  let currentIndex = slides.findIndex(s => s.classList.contains('active'));
  if (currentIndex < 0) currentIndex = 0;

  let autoTimer = null;
  const AUTO_MS = 6000;

  const setSlide = (idx) => {
    currentIndex = (idx + slides.length) % slides.length;
    slides.forEach((s, i) => s.classList.toggle('active', i === currentIndex));
    dots.forEach((d, i) => d.classList.toggle('active', i === currentIndex));
  };

  const stopAuto = () => {
    if (autoTimer) clearInterval(autoTimer);
    autoTimer = null;
  };

  const startAuto = () => {
    stopAuto();
    // Don’t rotate while zoomed
    if (awardsBox.classList.contains('image-only')) return;

    autoTimer = setInterval(() => {
      setSlide(currentIndex + 1);
    }, AUTO_MS);
  };

  // Init slide
  setSlide(currentIndex);
  startAuto();

  // Dots click
  dots.forEach((dot, idx) => {
    dot.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      setSlide(idx);
      startAuto();
    });
  });

  // Click image => toggle full-image card mode
  slides.forEach((slide) => {
    const imgWrap = slide.querySelector('.award-image');
    if (!imgWrap) return;

    imgWrap.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();

      const goingFull = !awardsBox.classList.contains('image-only');
      awardsBox.classList.toggle('image-only', goingFull);

      if (goingFull) stopAuto();
      else startAuto();
    });
  });

  // Clicking card while zoomed => exit zoom
  awardsBox.addEventListener('click', () => {
    if (!awardsBox.classList.contains('image-only')) return;
    awardsBox.classList.remove('image-only');
    startAuto();
  });

  // ESC closes zoom
  document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    if (!awardsBox.classList.contains('image-only')) return;
    awardsBox.classList.remove('image-only');
    startAuto();
  });
}

  /* Init after DOM */
  document.addEventListener('DOMContentLoaded', () => {
    console.log('[Combrok] DOMContentLoaded');
    initNavbarShadow();
    initMenus();
    initHeroCarousel();
    initRibbonSimple({ holdMs: 5200, slideGap: 600 });
    initStatsFloat();
    initReveal();
    initSustainabilityAwards();
    console.log('[Combrok] Init complete');
  });
})();
