  /* =========================
     HOMEPAGE AWARD ZOOM (clean)
     ========================= */
  const awardCards = Array.from(document.querySelectorAll('[data-award-card]'));

  const closeAllAwardZoom = () => {
    awardCards.forEach(c => c.classList.remove('is-zoomed'));
  };

  awardCards.forEach(card => {
    const btn = card.querySelector('.award-zoom-btn');
    if (!btn) return;

    btn.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();

      const willOpen = !card.classList.contains('is-zoomed');
      closeAllAwardZoom();               // only one zoom at a time
      if (willOpen) card.classList.add('is-zoomed');
    });

    // clicking on the card closes zoom if open
    card.addEventListener('click', () => {
      if (card.classList.contains('is-zoomed')) card.classList.remove('is-zoomed');
    });
  });

  // ESC closes zoom (single listener)
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeAllAwardZoom();
  });