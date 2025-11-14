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
    const target = parseInt(el.getAttribute('data-target') || '0', 10);
    if (!target || isNaN(target)) return;

    let start = 0;
    const duration = 1200; // ms
    const startTime = performance.now();

    const step = (now) => {
      const progress = Math.min((now - startTime) / duration, 1);
      const value = Math.floor(start + (target - start) * progress);
      el.textContent = value.toLocaleString('en-KE');

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
});