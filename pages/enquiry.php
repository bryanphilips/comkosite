<?php
require_once __DIR__ . '/../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
$siteKey = RECAPTCHA_SITE_KEY ?? '';
?>
<link rel="stylesheet" href="/assets/css/enquiry.css">

<section class="hero-contact" style="background-image:url('/assets/images/enquiry-hero.webp')">
  <div class="overlay"></div>
  <div class="wrap">
    <h1>Send an Enquiry</h1>
    <p class="sub-title">We’re happy to assist — share your details below and our team will respond promptly.</p>
  </div>
</section>

<section class="section-pad">
  <div class="container wrap" style="max-width:900px;">
    <div class="card soft contact-card glass p-4 p-md-5 fade-in">
      <h2 class="h5 mb-3 text-brand fw-bold">Let’s Get in Touch</h2>
      <p class="text-secondary mb-4">Fill in your enquiry details below.</p>

      <div id="formAlert" class="alert d-none" role="alert"></div>

      <form id="enquiryForm" class="needs-validation" novalidate>
        <div class="row g-3">
          <div class="col-md-6">
            <label for="name" class="form-label fw-semibold">Full Name</label>
            <input id="name" name="name" type="text" class="form-control" required autocomplete="name">
            <div class="invalid-feedback">Please enter your full name.</div>
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input id="email" name="email" type="email" class="form-control" required autocomplete="email">
            <div class="invalid-feedback">Please enter a valid email address.</div>
          </div>
          <div class="col-md-6">
            <label for="tel" class="form-label fw-semibold">Phone (optional)</label>
            <input id="tel" name="tel" type="tel" class="form-control" autocomplete="tel">
          </div>
          <div class="col-md-6">
            <label for="topic" class="form-label fw-semibold">Topic</label>
            <select id="topic" name="topic" class="form-select">
              <option>General</option>
              <option>Auctions</option>
              <option>Market Reports</option>
              <option>Private Sale</option>
            </select>
          </div>
          <div class="col-12">
            <label for="msg" class="form-label fw-semibold">Message</label>
            <!-- id is msg (for label), name is message (what API expects) -->
            <textarea id="msg" name="message" rows="5" class="form-control" required placeholder="How can we help?"></textarea>
            <div class="invalid-feedback">Please write a short message.</div>
          </div>
        </div>

        <!-- Honeypot + CSRF -->
        <input type="text" name="hp" class="hp" tabindex="-1" autocomplete="off">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">

        <?php if (!empty($siteKey)): ?>
          <div class="mt-3">
            <div class="g-recaptcha"
                 data-sitekey="<?= htmlspecialchars($siteKey, ENT_QUOTES, 'UTF-8') ?>"
                 data-callback="onRecaptcha"
                 data-expired-callback="onRecaptchaExpired"></div>
            <small class="text-secondary">Protected by reCAPTCHA.</small>
          </div>
          <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <?php endif; ?>

        <div class="d-flex gap-2 align-items-center mt-4">
          <button id="submitBtn" type="submit" class="btn btn-brand btn-send px-4" disabled>
            <span class="btn-label">Submit Enquiry</span>
            <span class="btn-spinner" aria-hidden="true">
              <svg viewBox="0 0 50 50"><circle cx="25" cy="25" r="20"/></svg>
            </span>
          </button>
          <button type="reset" class="btn btn-outline-secondary">Clear</button>
        </div>
      </form>

      <p class="text-secondary small mt-3 mb-0">
        Prefer direct email? <a href="mailto:<?= htmlspecialchars(NOTIFY_TO_EMAIL) ?>"><?= htmlspecialchars(NOTIFY_TO_EMAIL) ?></a>
      </p>
    </div>
  </div>
</section>

<!-- IMPORTANT: absolute path so it actually loads -->
<script defer src="/assets/js/enquiry.js"></script>