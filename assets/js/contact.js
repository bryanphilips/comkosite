// Contact Form Validation + animation on scroll
document.addEventListener("DOMContentLoaded", () => {
  const forms = document.querySelectorAll(".needs-validation");
  forms.forEach(form => {
    form.addEventListener("submit", e => {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      form.classList.add("was-validated");
    }, false);
  });

  // Animate cards on scroll
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => {
      if (e.isIntersecting) e.target.classList.add("in-view");
    });
  }, { threshold:0.2 });
  document.querySelectorAll(".contact-card").forEach(c => obs.observe(c));
});
 function onRecaptcha(){ document.getElementById('sendBtn').disabled = false; }
  function onRecaptchaExpired(){ document.getElementById('sendBtn').disabled = true; }

  // Client-side validation + loading state for the premium send button
  (function () {
    'use strict';
    const form = document.getElementById('contactForm');
    const btn  = document.getElementById('sendBtn');

    form.addEventListener('submit', function (e) {
      if (!form.checkValidity()) {
        e.preventDefault(); e.stopPropagation();
        form.classList.add('was-validated');
        return;
      }
      // Loading state while posting to /contact_submit.php
      btn.classList.add('is-loading');
      btn.setAttribute('aria-busy','true');
      btn.disabled = true;
    }, false);

    // Clear state on reset
    form.addEventListener('reset', function(){
      btn.classList.remove('is-loading');
      btn.removeAttribute('aria-busy');
      // If reCAPTCHA is present, keep disabled until solved again
      if (window.grecaptcha) btn.disabled = true;
    });
  })();