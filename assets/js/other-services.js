/* other-services.js — lazy image loader + crossfade rotator (JS-safe) */
(function(){
  'use strict';

  // Mark document as JS-enabled for CSS gates
  document.documentElement.classList.add('js');

  // === A) Lazy loader ===
  const pictures = document.querySelectorAll('picture');

  if ('IntersectionObserver' in window) {
    pictures.forEach(p => p.classList.add('is-lazy'));

    const io = new IntersectionObserver((entries)=>{
      entries.forEach(entry=>{
        if (!entry.isIntersecting) return;
        const pic = entry.target;

        // Promote data-srcset -> srcset
        pic.querySelectorAll('source[data-srcset]').forEach(s=>{
          s.setAttribute('srcset', s.getAttribute('data-srcset'));
          s.removeAttribute('data-srcset');
        });

        const img = pic.querySelector('img.lazy-img');
        if (img) {
          if (img.complete) img.classList.add('is-loaded');
          else img.addEventListener('load', ()=> img.classList.add('is-loaded'), { once:true });
        }
        io.unobserve(pic);
      });
    }, { rootMargin: '100px 0px', threshold: 0.1 });

    pictures.forEach(p => io.observe(p));
  } else {
    // Fallback: just show the images
    pictures.forEach(pic=>{
      pic.querySelectorAll('source[data-srcset]').forEach(s=>{
        s.setAttribute('srcset', s.getAttribute('data-srcset'));
        s.removeAttribute('data-srcset');
      });
      const img = pic.querySelector('img.lazy-img');
      if (img) img.classList.add('is-loaded');
    });
  }

  // === B) Crossfade Rotators ===
  document.querySelectorAll('.xfade-rotator').forEach(root=>{
    const slides = root.querySelectorAll('.xfade-slide');
    if(!slides.length) return;

    slides.forEach(s=>s.classList.remove('is-active'));
    slides[0].classList.add('is-active');

    const dots = root.querySelectorAll('.xfade-indicators .dot');
    const setDot = i => dots.forEach((d,idx)=>d.classList.toggle('active', idx===i));

    let i = 0, playing = true;
    const HOLD = 5200, FADE = 1800;

    function step(){
      if(!playing) return;
      const cur = i, nxt = (i+1)%slides.length;
      slides[cur].classList.remove('is-active');
      slides[nxt].classList.add('is-active');
      setDot(nxt);
      i = nxt;
    }
    const timer = setInterval(step, HOLD + FADE);

    if ('IntersectionObserver' in window){
      const io = new IntersectionObserver((ents)=>{
        ents.forEach(ent=> playing = ent.isIntersecting);
      }, { threshold: 0.15 });
      io.observe(root);
    }

    window.addEventListener('beforeunload', ()=> clearInterval(timer));
    setDot(0);
  });
})();