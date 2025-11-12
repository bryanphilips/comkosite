/* Products page — scoped filters + safe lazy support */
(function(){
  'use strict';

  const root = document.getElementById('teaGrid');
  if(!root) return;

  // Filter chips
  const pills = document.querySelectorAll('[data-pills] .pill');
  pills.forEach(p => p.addEventListener('click', () => {
    pills.forEach(x => x.classList.remove('is-active'));
    p.classList.add('is-active');
    const tag = p.getAttribute('data-filter');

    root.querySelectorAll('.tea-card').forEach(card => {
      if(tag === 'all') {
        card.style.display = '';
      } else {
        const has = (card.getAttribute('data-tags') || '').split(/\s+/).includes(tag);
        card.style.display = has ? '' : 'none';
      }
    });
  }));

  // Minimal “make sure images show” helper in case any global lazy CSS hides them
  document.querySelectorAll('.tea-card picture img').forEach(img => {
    img.addEventListener('load', () => img.classList.add('is-loaded'));
    if (img.complete) img.classList.add('is-loaded');
  });
})();