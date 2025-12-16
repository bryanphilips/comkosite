document.addEventListener('DOMContentLoaded', () => {
  // -------- Fade-in on scroll --------
  const fadeEls = document.querySelectorAll('.fade-in');

  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
          io.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.18
    });

    fadeEls.forEach(el => io.observe(el));
  } else {
    // Fallback: show everything
    fadeEls.forEach(el => el.classList.add('in-view'));
  }

  // -------- Impact counters --------
  const statEls = document.querySelectorAll('.impact-stat');

  const animateStat = (el) => {
    const targetRaw = el.getAttribute('data-target') || '0';
    // Allow "70+" etc. -> parseInt will handle leading number
    const target = parseInt(targetRaw, 10);
    if (!target || isNaN(target)) return;

    let start = 0;
    const duration = 1200; // ms
    const startTime = performance.now();

    const step = (now) => {
      const progress = Math.min((now - startTime) / duration, 1);
      const value = Math.floor(start + (target - start) * progress);
      el.textContent = value.toLocaleString('en-KE') + (targetRaw.endsWith('+') ? '+' : '');

      if (progress < 1) {
        requestAnimationFrame(step);
      }
    };

    requestAnimationFrame(step);
  };

  if ('IntersectionObserver' in window) {
    const statObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateStat(entry.target);
          statObserver.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.4
    });

    statEls.forEach(el => statObserver.observe(el));
  } else {
    statEls.forEach(animateStat);
  }

  // -------- Awards slider (dots + auto-rotate) --------
  const slider = document.querySelector('[data-award-slider]');
  const slides = slider ? slider.querySelectorAll('.award-slide') : [];
  const dotsWrap = document.querySelector('[data-award-dots]');
  const dots = dotsWrap ? dotsWrap.querySelectorAll('.dot') : [];
  const awardsBox = document.querySelector('.hero-awards');

  let currentIndex = 0;
  let autoTimer = null;
  const AUTO_MS = 6000;

  function setSlide(index) {
    if (!slides.length) return;
    currentIndex = (index + slides.length) % slides.length;

    slides.forEach((slide, i) => {
      slide.classList.toggle('active', i === currentIndex);
    });
    if (dots.length) {
      dots.forEach((dot, i) => dot.classList.toggle('active', i === currentIndex));
    }
  }

  function startAuto() {
    if (!slides.length) return;
    if (autoTimer) clearInterval(autoTimer);
    // Don’t auto-rotate when zoomed
    if (awardsBox && awardsBox.classList.contains('image-only')) return;

    autoTimer = setInterval(() => {
      setSlide(currentIndex + 1);
    }, AUTO_MS);
  }

  if (slides.length) {
    setSlide(0);
    startAuto();
  }

  if (dots.length) {
    dots.forEach((dot, idx) => {
      dot.addEventListener('click', () => {
        setSlide(idx);
        startAuto();
      });
    });
  }

  // -------- Image-only zoom mode on award image click --------
  if (awardsBox && slides.length) {
    slides.forEach(slide => {
      const clickable = slide.querySelector('.award-image');
      if (!clickable) return;

      // Toggle image-only mode when clicking the image
      clickable.addEventListener('click', (e) => {
        e.stopPropagation(); // prevent bubbling to awardsBox click

        const nowImageOnly = !awardsBox.classList.contains('image-only');
        awardsBox.classList.toggle('image-only', nowImageOnly);

        if (autoTimer) {
          clearInterval(autoTimer);
          autoTimer = null;
        }

        if (!nowImageOnly) {
          // Back to normal -> restart auto
          startAuto();
        }
      });
    });

    // Clicking anywhere inside hero-awards (but not on the image, which is handled above)
    // will exit image-only mode if active
    awardsBox.addEventListener('click', () => {
      if (!awardsBox.classList.contains('image-only')) return;
      awardsBox.classList.remove('image-only');
      startAuto();
    });

    // Optional: Escape key to exit image-only mode
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && awardsBox.classList.contains('image-only')) {
        awardsBox.classList.remove('image-only');
        startAuto();
      }
    });
  }
});