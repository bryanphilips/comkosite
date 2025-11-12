<?php
require_once __DIR__ . '/../config.php';
?>
<link rel="stylesheet" href="/assets/css/request.css">

<section class="page-head">
  <div class="wrap">
    <h1 class="form-head">Request Data</h1>
    <p class="sub">Tell us what you need and we’ll prepare the right dataset for you.</p>
  </div>
</section>

<section class="wrap">
  <div id="rq-alert" class="rq-alert hidden" role="alert"></div>

  <form id="rq-form" class="form-card needs-validation" novalidate>
    <div class="form-grid">
      <div class="form-field">
        <label for="rq_company">Company / Organization <span class="req">*</span></label>
        <input id="rq_company" type="text" name="company" required placeholder="e.g., Combrok Limited">
        <div class="invalid-feedback">Please enter your company name.</div>
      </div>

      <div class="form-field">
        <label for="rq_email">Email <span class="req">*</span></label>
        <input id="rq_email" type="email" name="email" required placeholder="e.g., info@combrok.co.ke">
        <div class="invalid-feedback">Please enter a valid email.</div>
      </div>

      <div class="form-field">
        <label for="rq_phone">Phone <span class="req">*</span></label>
        <input id="rq_phone" type="tel" name="phone" required placeholder="e.g., +254 712 345 678">
        <div class="invalid-feedback">Please enter your phone number.</div>
      </div>

      <div class="form-field">
        <label for="rq_type">Dataset Required <span class="req">*</span></label>
        <select id="rq_type" name="type" required>
          <option value="">Select…</option>
          <option value="general-data">General</option>
          <option value="weekly-catalogues">Weekly Catalogues</option>
          <option value="historical-prices">Historical Prices</option>
          <option value="market-reports">Market Reports</option>
        </select>
        <div class="invalid-feedback">Please select a dataset.</div>
      </div>

      <div class="form-field span-2">
        <label for="rq_notes">Notes</label>
        <textarea id="rq_notes" name="notes" rows="5" placeholder="Add details that help us prepare the right dataset…"></textarea>
      </div>
    </div>

    <!-- Honeypot -->
    <input type="text" name="hp" class="hp" tabindex="-1" autocomplete="off">

    <!-- CSRF -->
    <?php if (function_exists('csrf_token')): ?>
      <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <!-- reCAPTCHA -->
    <div class="recaptcha-box">
      <div class="g-recaptcha"
           data-sitekey="<?= htmlspecialchars(RECAPTCHA_SITE_KEY) ?>"
           data-callback="onRecaptcha"
           data-expired-callback="onRecaptchaExpired"></div>
    </div>

    <div class="form-actions">
      <button id="rq-submit" class="btn btn-brand btn-send" type="submit" disabled>
        <span class="lbl">Submit Request</span>
        <span class="spin" aria-hidden="true">
          <svg viewBox="0 0 50 50"><circle cx="25" cy="25" r="20"/></svg>
        </span>
      </button>
      <button class="btn btn-outline-secondary" type="reset">Clear</button>
    </div>
  </form>
</section>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<script defer src="/assets/js/request.js"></script>