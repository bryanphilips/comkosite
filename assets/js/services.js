/**
 * SERVICES Slider v4
 * - Per-section slider
 * - next/prev buttons
 * - dots
 * - auto-rotate
 * - swipe/drag (mouse + touch)
 * - responsive (1 / 2 / 3 cards per view based on CSS)
 */
document.addEventListener('DOMContentLoaded', () => {
  const sliders = document.querySelectorAll('[data-svc-slider]');
  if (!sliders.length) return;

  sliders.forEach(initServiceSlider);

  function initServiceSlider(root) {
    const track    = root.querySelector('[data-svc-track]');
    const viewport = root.querySelector('[data-svc-viewport]');
    const slides   = Array.from(root.querySelectorAll('[data-svc-slide]'));
    const btnPrev  = root.querySelector('.svc-nav.prev');
    const btnNext  = root.querySelector('.svc-nav.next');
    const dotsWrap = root.querySelector('[data-svc-dots]');

    if (!track || !viewport || !slides.length) return;

    // --- state
    let index = 0;               // page index (not slide index)
    let pages = 1;               // total pages
    let perView = 1;             // slides per view
    let gap = 14;                // must match CSS .svc-track gap
    let autoTimer = null;
    const AUTO_MS = 6500;

    // --- build dots
    function rebuildDots() {
      if (!dotsWrap) return;
      dotsWrap.innerHTML = '';
      for (let i = 0; i < pages; i++) {
        const b = document.createElement('button');
        b.type = 'button';
        b.className = 'svc-dot' + (i === index ? ' active' : '');
        b.setAttribute('aria-label', `Go to slide group ${i + 1}`);
        b.addEventListener('click', () => {
          goTo(i);
          restartAuto();
        });
        dotsWrap.appendChild(b);
      }
    }

    // --- helpers
    function readGap() {
      const style = window.getComputedStyle(track);
      const g = parseFloat(style.columnGap || style.gap || '0');
      gap = Number.isFinite(g) ? g : 0;
    }

    function computePerView() {
      // determine how many slides fit in the viewport
      // (based on actual rendered widths from CSS breakpoints)
      const vpW = viewport.clientWidth;
      const first = slides[0];
      const slideW = first.getBoundingClientRect().width || vpW;
      // perView ~= (viewport + gap) / (slideWidth + gap)
      const pv = Math.max(1, Math.round((vpW + gap) / (slideW + gap)));
      perView = Math.min(pv, slides.length);
    }

    function computePages() {
      pages = Math.max(1, Math.ceil(slides.length / perView));
      if (index > pages - 1) index = pages - 1;
    }

    function updateButtons() {
      if (!btnPrev || !btnNext) return;
      btnPrev.disabled = index <= 0;
      btnNext.disabled = index >= pages - 1;
    }

    function setTransform() {
      // Move by viewport width (page-by-page)
      const vpW = viewport.clientWidth;
      // translate includes the gaps already within viewport width since slides widths are % based,
      // but using viewport width is the most stable for 1/2/3-per-view layouts.
      const x = index * vpW;
      track.style.transform = `translateX(-${x}px)`;

      // dots active
      if (dotsWrap) {
        dotsWrap.querySelectorAll('.svc-dot').forEach((d, i) => {
          d.classList.toggle('active', i === index);
        });
      }
      updateButtons();
    }

    function goTo(i) {
      index = Math.max(0, Math.min(pages - 1, i));
      setTransform();
    }

    function next() {
      goTo(index + 1);
    }

    function prev() {
      goTo(index - 1);
    }

    // --- auto rotate
    function startAuto() {
      stopAuto();
      autoTimer = setInterval(() => {
        if (index >= pages - 1) goTo(0);
        else next();
      }, AUTO_MS);
    }
    function stopAuto() {
      if (autoTimer) clearInterval(autoTimer);
      autoTimer = null;
    }
    function restartAuto() {
      startAuto();
    }

    // --- init sizing
    function recalc() {
      readGap();
      computePerView();
      computePages();
      rebuildDots();
      setTransform();
    }

    // Hook up buttons
    if (btnPrev) btnPrev.addEventListener('click', () => { prev(); restartAuto(); });
    if (btnNext) btnNext.addEventListener('click', () => { next(); restartAuto(); });

    // Pause auto on hover/focus (desktop)
    root.addEventListener('mouseenter', stopAuto);
    root.addEventListener('mouseleave', startAuto);
    root.addEventListener('focusin', stopAuto);
    root.addEventListener('focusout', startAuto);

    // --- swipe / drag
    let isDown = false;
    let startX = 0;
    let startTransform = 0;
    let dragging = false;

    function getTranslateX() {
      const m = new DOMMatrixReadOnly(window.getComputedStyle(track).transform);
      return Math.abs(m.m41) || 0;
    }

    function onDown(clientX) {
      isDown = true;
      dragging = false;
      startX = clientX;
      startTransform = getTranslateX();
      track.style.transition = 'none';
      stopAuto();
    }

    function onMove(clientX) {
      if (!isDown) return;
      const dx = clientX - startX;
      if (Math.abs(dx) > 6) dragging = true;

      // drag track
      const x = startTransform - dx;
      track.style.transform = `translateX(-${x}px)`;
    }

    function onUp(clientX) {
      if (!isDown) return;
      isDown = false;
      track.style.transition = ''; // restore CSS transition

      const dx = clientX - startX;

      // If not really dragged, snap back
      const threshold = Math.max(40, viewport.clientWidth * 0.12);

      if (dragging && Math.abs(dx) > threshold) {
        if (dx < 0) next(); // swiped left -> next page
        else prev();        // swiped right -> prev page
      } else {
        // snap to current index
        setTransform();
      }

      startAuto();
    }

    // Mouse
    viewport.addEventListener('mousedown', (e) => onDown(e.clientX));
    window.addEventListener('mousemove', (e) => onMove(e.clientX));
    window.addEventListener('mouseup', (e) => onUp(e.clientX));

    // Touch
    viewport.addEventListener('touchstart', (e) => {
      if (!e.touches || !e.touches[0]) return;
      onDown(e.touches[0].clientX);
    }, { passive: true });

    viewport.addEventListener('touchmove', (e) => {
      if (!e.touches || !e.touches[0]) return;
      onMove(e.touches[0].clientX);
    }, { passive: true });

    viewport.addEventListener('touchend', (e) => {
      const t = e.changedTouches && e.changedTouches[0];
      onUp(t ? t.clientX : startX);
    });

    // Prevent accidental link clicks while dragging
    viewport.addEventListener('click', (e) => {
      if (dragging) {
        e.preventDefault();
        e.stopPropagation();
      }
    }, true);

    // resize observer
    const ro = new ResizeObserver(() => recalc());
    ro.observe(viewport);

    // initial
    recalc();
    startAuto();
  }
});