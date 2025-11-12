<?php /* pages/contact.php */ ?>
<link rel="stylesheet" href="/assets/css/contact.css" />

<section class="hero-contact">
  <div class="overlay"></div>
  <div class="wrap">
    <h1>Contact Us</h1>
    <p class="sub-title">We’d love to hear from you — send us a message or visit us in Mombasa CBD.</p>
  </div>
</section>

<section class="section-pad">
  <div class="container wrap">
    <div class="row g-4" style="row-gap:24px;">

      <!-- Info + Map -->
      <div class="col-lg-6">
        <div class="card soft contact-card glass p-4 h-100 fade-in">
          <h2 class="h5 mb-3 text-brand">Visit / Reach Us</h2>
          <ul class="list-unstyled mb-4" style="margin:0 0 12px 0;">
            <li><strong>Email:</strong> <?= htmlspecialchars(NOTIFY_TO_EMAIL) ?></li>
            <li><strong>Phone:</strong> +254 722 123 456</li>
            <li><strong>Hours:</strong> Mon–Fri 9:00–17:00</li>
            <li><strong>Address:</strong> P.O. Box 999, Mombasa, Kenya</li>
            <li><strong>Location:</strong> Tea House</li>
          </ul>

          <div class="map-frame ratio ratio-16x9 mb-3" style="border-radius:12px; overflow:hidden; box-shadow:0 10px 28px rgba(0,0,0,.06);">
            <iframe
              title="Map – Tea House, Nyerere Road, Mombasa"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              style="border:0; width:100%; height:100%;"
              allowfullscreen
              src="https://www.google.com/maps?q=Tea%20House%2C%20WMM9%2BHR5%2C%20Next%20To%20Nyerere%20Road%20Car%20Park%2C%20Nyerere%20Road%2C%20Mombasa%20CBD%2C%20Mombasa&output=embed">
            </iframe>
          </div>

          <a class="btn btn-brand" style="display:inline-flex; gap:8px; align-items:center;"
             href="https://share.google/doynRddeAvUMXGVXC" target="_blank" rel="noopener">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
              <path d="M10 20v-6H7l5-8 5 8h-3v6h-4z"/>
            </svg>
            Get Directions
          </a>
        </div>
      </div>

      <!-- Contact Form -->
      <div class="col-lg-6">
        <div class="card soft contact-card glass p-4 fade-in">
          <h2 class="h5 mb-3 text-brand">Send Us a Message</h2>

          <!-- Success/Fail flash -->
          <?php if (!empty($_GET['sent']) && $_GET['sent']==='1'): ?>
            <div id="contactAlert" class="alert alert-success" role="alert">
              Thanks! Your message has been sent.
            </div>
          <?php elseif (!empty($_GET['error'])): ?>
            <div id="contactAlert" class="alert alert-danger" role="alert">
              <?= htmlspecialchars($_GET['error']) ?>
            </div>
          <?php endif; ?>

          <form id="contactForm" method="post" action="/contact_submit.php" class="needs-validation" novalidate>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Name</label>
                <input class="form-control" name="name" required autocomplete="name">
                <div class="invalid-feedback">Please enter your name.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required autocomplete="email">
                <div class="invalid-feedback">Please enter a valid email.</div>
              </div>
              <div class="col-12">
                <label class="form-label">Subject</label>
                <input class="form-control" name="subject" placeholder="General enquiry">
              </div>
              <div class="col-12">
                <label class="form-label">Message</label>
                <textarea class="form-control" name="message" rows="6" required></textarea>
                <div class="invalid-feedback">Please enter a message.</div>
              </div>

              <input type="text" name="hp" class="d-none" tabindex="-1" autocomplete="off">
              <?php if (function_exists('csrf_token')): ?>
                <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
              <?php endif; ?>

              <div class="col-12">
                <div class="g-recaptcha"
                     data-sitekey="<?= htmlspecialchars(RECAPTCHA_SITE_KEY) ?>"
                     data-callback="onRecaptcha"
                     data-expired-callback="onRecaptchaExpired"></div>
                <small class="text-muted d-block mt-1">
                  Protected by reCAPTCHA. See
                  <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Privacy</a> &
                  <a href="https://policies.google.com/terms" target="_blank" rel="noopener">Terms</a>.
                </small>
              </div>

              <div class="col-12 d-flex gap-2">
                <button id="sendBtn" class="btn btn-brand btn-send" type="submit" disabled>
                  <span class="btn-label">Send Message</span>
                  <span class="btn-spinner" aria-hidden="true">
                    <svg viewBox="0 0 50 50" focusable="false" aria-hidden="true">
                      <circle cx="25" cy="25" r="20"/>
                    </svg>
                  </span>
                </button>
                <button class="btn btn-outline-secondary" type="reset">Clear</button>
              </div>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>

<script async defer src="https://www.google.com/recaptcha/api.js"></script>
<script defer src="/assets/js/contact.js"></script>

<!-- Clear query params after showing alert (prevents reappearing on refresh) -->
<script>
  (function() {
    const params = new URLSearchParams(location.search);
    if (params.has('sent') || params.has('error')) {
      // optionally auto-hide after 5s
      const box = document.getElementById('contactAlert');
      if (box) setTimeout(() => box.remove(), 5000);

      // strip the query so refresh doesn't bring it back
      const clean = location.pathname + (location.hash || '');
      history.replaceState({}, '', clean);
    }
  })();
</script>