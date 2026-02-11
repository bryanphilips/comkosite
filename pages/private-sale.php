<?php
// pages/private-sale.php
require_once __DIR__ . '/../config.php';
if (session_status() === PHP_SESSION_NONE) session_start();
if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
$siteKey = RECAPTCHA_SITE_KEY ?? '';
?>
<link rel="stylesheet" href="/assets/css/private-sale.css">

<section class="page-head">
  <div class="wrap">
    <h1 class="form-head">Private Sale</h1>
    <p class="sub">Request a private sale or offer teas for private sale — quick, secure & efficient.</p>
  </div>
</section>

<section class="wrap">
  <div class="ps-tabs card soft">
    <div class="ps-tabbar" role="tablist" aria-label="Private Sale Tabs">
      <button class="ps-tab active" data-tab="request" role="tab" aria-selected="true" aria-controls="panel-request">Buyer Request for Private Sale</button>
      <button class="ps-tab" data-tab="offer" role="tab" aria-selected="false" aria-controls="panel-offer">Producer Offer Tea for Private Sale</button>
    </div>

    <!-- shared alert slot -->
    <div id="ps-alert" class="ps-alert hidden" role="alert" aria-live="polite"></div>

    <!-- PANEL: REQUEST -->
    <div id="panel-request" class="ps-panel active" role="tabpanel" aria-labelledby="tab-request">
      <form id="ps-request-form" class="form-card needs-validation" novalidate>
        <div class="form-grid">
          <div class="form-field">
            <label for="ps_req_name">Name <span class="req">*</span></label>
            <input id="ps_req_name" name="name" type="text" required placeholder="Your full name" autocomplete="name">
            <div class="invalid-feedback">Please enter your name.</div>
          </div>

          <div class="form-field">
            <label for="ps_req_phone">Phone Number <span class="req">*</span></label>
            <input id="ps_req_phone" name="phone" type="tel" required placeholder="+254 7xx xxx xxx" autocomplete="tel">
            <div class="invalid-feedback">Please enter your phone.</div>
          </div>

          <div class="form-field">
            <label for="ps_req_email">Email <span class="req">*</span></label>
            <input id="ps_req_email" name="email" type="email" required placeholder="you@company.com" autocomplete="email">
            <div class="invalid-feedback">Please enter a valid email.</div>
          </div>

          <div class="form-field span-2">
            <span class="label-block">Tea Type(s) <span class="req">*</span></span>
            <div class="check-row" role="group" aria-labelledby="tea-types-label">
              <label for="ps_req_type_ctc"><input id="ps_req_type_ctc" type="checkbox" name="teaTypes[]" value="CTC"> CTC Tea</label>
              <label for="ps_req_type_orth"><input id="ps_req_type_orth" type="checkbox" name="teaTypes[]" value="Orthodox"> Orthodox Tea</label>
              <label for="ps_req_type_spec"><input id="ps_req_type_spec" type="checkbox" name="teaTypes[]" value="Specialty"> Specialty Tea</label>
            </div>
            <div class="hint">Select one or more.</div>
          </div>

          <div class="form-field">
            <label for="ps_req_gmark">Garden Mark <span class="muted">(optional)</span></label>
            <input id="ps_req_gmark" name="gardenMark" type="text" placeholder="e.g., KERICHO AA">
          </div>

          <div class="form-field">
            <label for="ps_req_grade">Grade Required <span class="req">*</span></label>
            <input id="ps_req_grade" name="grade" type="text" required placeholder="e.g., BP1 / PF1 / OP">
            <div class="invalid-feedback">Please specify the grade.</div>
          </div>

          <div class="form-field">
            <label for="ps_req_packages">Packages <span class="muted">(optional)</span></label>
            <input id="ps_req_packages" name="packages" type="text" placeholder="e.g., 50 bags @ 50kg">
          </div>

          <div class="form-field">
            <label for="ps_req_net">Net Weight (kg) <span class="req">*</span></label>
            <input id="ps_req_net" name="netWeight" type="number" min="1" step="0.01" required placeholder="e.g., 2500">
            <div class="invalid-feedback">Please enter net weight.</div>
          </div>
        </div>

        <!-- honeypot + csrf -->
        <input type="text" name="hp" class="hp" tabindex="-1" autocomplete="off">
        <?php if (function_exists('csrf_token')): ?>
          <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <!-- reCAPTCHA (explicit render target) -->
        <div class="recaptcha-box">
          <div id="recaptcha-request"></div>
        </div>

        <div class="form-actions">
          <button id="ps-request-submit" class="btn btn-brand btn-send" type="submit" disabled>
            <span class="lbl">Submit Request</span>
            <span class="spin" aria-hidden="true">
              <svg viewBox="0 0 50 50"><circle cx="25" cy="25" r="20"/></svg>
            </span>
          </button>
          <button type="reset" class="btn btn-outline-secondary">Clear</button>
        </div>
      </form>
    </div>

    <!-- PANEL: OFFER -->
    <div id="panel-offer" class="ps-panel" role="tabpanel" aria-labelledby="tab-offer" hidden>
      <form id="ps-offer-form" class="form-card needs-validation" novalidate>
        <div class="form-grid">
          <div class="form-field">
            <label for="ps_off_gmark">Garden Mark <span class="req">*</span></label>
            <input id="ps_off_gmark" name="gardenMark" type="text" required placeholder="e.g., KERICHO AA">
            <div class="invalid-feedback">Please enter garden mark.</div>
          </div>
          <div class="form-field">
            <label for="ps_off_name">Name <span class="req">*</span></label>
            <input id="ps_off_name" name="name" type="text" required placeholder="Contact person">
            <div class="invalid-feedback">Please enter your name.</div>
          </div>
          <div class="form-field">
            <label for="ps_off_email">Email <span class="req">*</span></label>
            <input id="ps_off_email" name="email" type="email" required placeholder="you@company.com">
            <div class="invalid-feedback">Please enter a valid email.</div>
          </div>
          <div class="form-field">
            <label for="ps_off_phone">Phone <span class="req">*</span></label>
            <input id="ps_off_phone" name="phone" type="tel" required placeholder="+254 7xx xxx xxx">
            <div class="invalid-feedback">Please enter your phone.</div>
          </div>
        </div>

        <!-- Dynamic table -->
        <div class="table-card">
          <div class="table-head">
            <h3 class="h6">Lots to Offer</h3>
            <button type="button" class="btn small add-row" id="ps-add-row">+ Add Line</button>
          </div>
          <div class="table-scroll">
            <table class="ps-table" id="ps-offer-table">
              <thead>
                <tr>
                  <th scope="col">Invoice No.</th>
                  <th scope="col">Grade</th>
                  <th scope="col">Package Type</th>
                  <th scope="col">Packages</th>
                  <th scope="col">Gross Wt</th>
                  <th scope="col">Tare Wt</th>
                  <th scope="col">Net Wt</th>
                  <th scope="col"></th>
                </tr>
              </thead>
              <tbody><!-- rows injected by JS --></tbody>
            </table>
          </div>
          <p class="muted small">All fields are mandatory. You can add multiple lines.</p>
        </div>

        <div class="contact-opt">
          <label class="check" for="ps_off_contact">
            <input id="ps_off_contact" type="checkbox" name="contactMe" value="yes" checked>
            Please contact me about this offer.
          </label>
        </div>

        <!-- honeypot + csrf -->
        <input type="text" name="hp" class="hp" tabindex="-1" autocomplete="off">
        <?php if (function_exists('csrf_token')): ?>
          <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">
        <?php endif; ?>

        <!-- reCAPTCHA (explicit render target) -->
        <div class="recaptcha-box">
          <div id="recaptcha-offer"></div>
        </div>

        <div class="form-actions">
          <button id="ps-offer-submit" class="btn btn-brand btn-send" type="submit" disabled>
            <span class="lbl">Send Offer</span>
            <span class="spin" aria-hidden="true">
              <svg viewBox="0 0 50 50"><circle cx="25" cy="25" r="20"/></svg>
            </span>
          </button>
          <button type="reset" class="btn btn-outline-secondary">Clear</button>
        </div>
      </form>
    </div>
  </div>
</section>

<!-- reCAPTCHA explicit render -->
<script>
  window.RECAPTCHA_SITE_KEY = "<?= htmlspecialchars($siteKey, ENT_QUOTES, 'UTF-8') ?>";
</script>
<script src="https://www.google.com/recaptcha/api.js?onload=initRecaptcha&render=explicit" async defer></script>
<script defer src="/assets/js/private-sale.js"></script>