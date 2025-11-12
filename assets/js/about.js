/* =========================================================
   ABOUT PAGE — scoped JS (initializes only inside .about-page)
   No globals, no dependency on main.js
   ========================================================= */
(function(){
  'use strict';

  const root = document.querySelector('.about-page');
  if(!root) return; // run only on About

  const $  = (sel, r = root) => r.querySelector(sel);
  const $$ = (sel, r = root) => Array.from(r.querySelectorAll(sel));

  // Helper: return elements not inside .org
  const notInOrg = (nodes) => nodes.filter(el => !el.closest('.org'));

  // One-shot reveal for non-org elements
  function initRevealOnce(){
    const all = [
      ...$$('.reveal'),
      ...$$('.reveal-line'),
      ...$$('.reveal-pop')
    ];
    const els = notInOrg(all);
    if(!els.length){
      return;
    }
    if (!('IntersectionObserver' in window)) {
      els.forEach(el => el.classList.add('_in'));
      return;
    }
    const onceIO = new IntersectionObserver((entries, obs)=>{
      entries.forEach(ent=>{
        if(ent.isIntersecting){
          ent.target.classList.add('_in');
          obs.unobserve(ent.target);
        }
      });
    }, { threshold: 0.18, root: null, rootMargin: '0px 0px -5% 0px' });
    els.forEach(el => onceIO.observe(el));
  }

  // Re-trigger animations for ORG CHART on enter/exit
  function initOrgReplay(){
    const org = $('.org');
    if(!org || !('IntersectionObserver' in window)) return;

    const repeatIO = new IntersectionObserver((entries)=>{
      entries.forEach(ent=>{
        const el = ent.target;
        if(ent.isIntersecting){
          el.classList.add('_in');
        }else{
          el.classList.remove('_in');
          // reset transition state
          void el.offsetWidth;
        }
      });
    }, { threshold: 0.35 });

    org.querySelectorAll('.reveal, .reveal-line, .reveal-pop, .stub-h, .stub-vert')
      .forEach(el => repeatIO.observe(el));
  }

  // Background image rotator: 2s fade, single visible slide
  function initImageRotator(){
    const rotator = $('.image-rotator');
    if(!rotator) return;

    const slides = $$('.rotating-img', rotator);
    if(!slides.length) return;

    // Show first slide immediately (covers slow JS load)
    slides.forEach(s => s.classList.remove('active'));
    slides[0].classList.add('active');

    // Respect reduced motion: no autoplay
    const reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduceMotion || slides.length < 2) return;

    let index = 0;
    let playing = true;
    let disposed = false;
    const INTERVAL = 2000; // 2 seconds

    const next = () => {
      if(disposed || !playing) return;
      slides[index].classList.remove('active');
      index = (index + 1) % slides.length;
      slides[index].classList.add('active');
    };

    const timer = setInterval(next, INTERVAL);

    // Pause when off-screen
    if('IntersectionObserver' in window){
      const io = new IntersectionObserver((entries)=>{
        entries.forEach(ent => playing = ent.isIntersecting);
      }, { threshold: 0.15 });
      io.observe(rotator);
    }

    // Pause on hover for pointer devices
    if (matchMedia && matchMedia('(hover:hover)').matches) {
      rotator.addEventListener('mouseenter', () => { playing = false; });
      rotator.addEventListener('mouseleave', () => { playing = true; });
    }

    // Safety: cleanup if element is removed or page unloads
    const cleanup = () => {
      if(disposed) return;
      disposed = true;
      clearInterval(timer);
    };
    const mo = new MutationObserver(() => {
      if (!document.body.contains(rotator)) cleanup();
    });
    mo.observe(document.body, { childList: true, subtree: true });
    window.addEventListener('beforeunload', cleanup);
  }

  // Init
  const run = () => {
    initRevealOnce();
    initOrgReplay();
    initImageRotator();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', run, { once: true });
  } else {
    run();
  }
})();