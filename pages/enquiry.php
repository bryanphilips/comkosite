<?php
require_once __DIR__ . '/../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
$siteKey = RECAPTCHA_SITE_KEY ?? '';
?>
<link rel="stylesheet" href="/assets/css/contact-enquiry.css" />

<section class="hero-contact">
  <div class="overlay"></div>
  <div class="wrap">
    <h1>Contact, Enquiry &amp; Data Requests</h1>
    <p class="sub-title">Send an enquiry or request market datasets — our team will respond promptly.</p>
  </div>
</section>

<section class="section-pad">
  <div class="container wrap">

    <!-- Tabs -->
    <div class="card soft glass p-3 p-md-4 fade-in" style="overflow:hidden;">
      <div class="ce-tabs" role="tablist" aria-label="Enquiry and Request Data tabs">
        <button class="ce-tab active" type="button"
                data-tab="enquiry" role="tab"
                aria-selected="true" aria-controls="panel-enquiry">
          Send an Enquiry
        </button>
        <button class="ce-tab" type="button"
                data-tab="request" role="tab"
                aria-selected="false" aria-controls="panel-request">
          Request Data
        </button>
      </div>

      <!-- Shared alert area -->
      <div id="ceAlert" class="alert d-none mt-3" role="alert"></div>

      <!-- ================= Panel: ENQUIRY ================= -->
      <div id="panel-enquiry" class="ce-panel active" role="tabpanel">
        <div class="contact-card p-0" style="box-shadow:none;border:0;">
          <h2 class="h5 mb-2 text-brand fw-bold mt-3">Send an Enquiry</h2>
          <p class="text-secondary mb-4">Fill in your details and our team will respond promptly.</p>

          <form id="enquiryForm" class="needs-validation" novalidate>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="enq_name" class="form-label fw-semibold">Full Name</label>
                <input id="enq_name" name="name" type="text" class="form-control" required autocomplete="name">
                <div class="invalid-feedback">Please enter your full name.</div>
              </div>

              <div class="col-md-6">
                <label for="enq_email" class="form-label fw-semibold">Email</label>
                <input id="enq_email" name="email" type="email" class="form-control" required autocomplete="email">
                <div class="invalid-feedback">Please enter a valid email address.</div>
              </div>

              <div class="col-md-6">
                <label for="enq_tel" class="form-label fw-semibold">Phone (optional)</label>
                <input id="enq_tel" name="tel" type="tel" class="form-control" autocomplete="tel">
              </div>

              <div class="col-md-6">
                <label for="enq_topic" class="form-label fw-semibold">Topic</label>
                <select id="enq_topic" name="topic" class="form-select">
                  <option>General</option>
                  <option>Auctions</option>
                  <option>Market Reports</option>
                  <option>Private Sale</option>
                  <option>Website / Support</option>
                </select>
              </div>

              <div class="col-12">
                <label for="enq_msg" class="form-label fw-semibold">Message</label>
                <textarea id="enq_msg" name="message" rows="6" class="form-control" required
                          placeholder="How can we help?"></textarea>
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
                     data-callback="onRecaptchaEnquiry"
                     data-expired-callback="onRecaptchaExpiredEnquiry"></div>
                <small class="text-secondary">Protected by reCAPTCHA.</small>
              </div>
            <?php endif; ?>

            <div class="d-flex gap-2 align-items-center mt-4">
              <button id="enqSubmitBtn" type="submit" class="btn btn-brand btn-send px-4" <?= !empty($siteKey) ? 'disabled' : '' ?>>
                <span class="lbl">Send Enquiry</span>
                <span class="btn-spinner" aria-hidden="true">
                  <svg viewBox="0 0 50 50"><circle cx="25" cy="25" r="20"/></svg>
                </span>
              </button>
              <button type="reset" class="btn btn-outline-secondary">Clear</button>
            </div>
          </form>

          <p class="text-secondary small mt-3 mb-0">
            Prefer direct email?
            <a href="mailto:<?= htmlspecialchars(NOTIFY_TO_EMAIL ?? '', ENT_QUOTES, 'UTF-8') ?>">
              <?= htmlspecialchars(NOTIFY_TO_EMAIL ?? '', ENT_QUOTES, 'UTF-8') ?>
            </a>
          </p>
        </div>
      </div>

      <!-- ================= Panel: REQUEST DATA ================= -->
      <div id="panel-request" class="ce-panel" role="tabpanel" hidden>
        <h2 class="h5 mb-2 text-brand fw-bold mt-3">Request Data</h2>
        <p class="text-secondary mb-4">Tell us what you need and we’ll prepare the right dataset for you.</p>

        <!-- Keep request.css styling but avoid nested section wrappers -->
        <form id="rq-form" class="form-card needs-validation" novalidate style="margin-top:0;">
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

          <!-- Honeypot + CSRF -->
          <input type="text" name="hp" class="hp" tabindex="-1" autocomplete="off">
          <input type="hidden" name="csrf" value="<?= htmlspecialchars($_SESSION['csrf'], ENT_QUOTES, 'UTF-8') ?>">

          <?php if (!empty($siteKey)): ?>
            <div class="recaptcha-box">
              <div class="g-recaptcha"
                   data-sitekey="<?= htmlspecialchars($siteKey, ENT_QUOTES, 'UTF-8') ?>"
                   data-callback="onRecaptchaRequest"
                   data-expired-callback="onRecaptchaExpiredRequest"></div>
            </div>
          <?php endif; ?>

          <div class="form-actions">
            <button id="rq-submit" class="btn btn-brand btn-send" type="submit" <?= !empty($siteKey) ? 'disabled' : '' ?>>
              <span class="lbl">Submit Request</span>
              <span class="spin" aria-hidden="true">
                <svg viewBox="0 0 50 50"><circle cx="25" cy="25" r="20"/></svg>
              </span>
            </button>
            <button class="btn btn-outline-secondary" type="reset">Clear</button>
          </div>
        </form>
      </div>

    </div>
  </div>
</section>

<?php if (!empty($siteKey)): ?>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php endif; ?>
<script defer src="/assets/js/contact-request-tabs.js"></script>