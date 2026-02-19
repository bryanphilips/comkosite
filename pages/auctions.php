<?php
/* pages/auctions.php — enhanced Auction page */
function pictureTagSolid(string $base, string $alt = '', string $class = ''): void {
  $dir = $_SERVER['DOCUMENT_ROOT'] . '/assets/img/';
  $url = '/assets/img/';
  $fmts = ['avif'=>'image/avif','webp'=>'image/webp','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png'];

  $sources = [];
  foreach ($fmts as $ext => $mime) {
    $path = $dir . $base . '.' . $ext;
    if (file_exists($path)) {
      $sources[] = ['src' => $url . $base . '.' . $ext, 'mime' => $mime];
    }
  }
  $fallback = $sources[0]['src'] ?? 'data:image/gif;base64,R0lGODlhAQABAAAAACw=';

  echo '<picture class="'.htmlspecialchars($class).'">';
  foreach ($sources as $s) {
    echo '<source srcset="'.htmlspecialchars($s['src']).'" type="'.htmlspecialchars($s['mime']).'">';
  }
  echo '<img src="'.htmlspecialchars($fallback).'" alt="'.htmlspecialchars($alt).'" loading="lazy" decoding="async">';
  echo '</picture>';}
?>
<link rel="stylesheet" href="/assets/css/auction.css">

<!-- ===== AUCTION SCHEDULE ===== -->
<section class="wrap pad auction-module schedule-module">
  <div class="schedule-header">
    <div>
      <h2>Auction Schedule</h2>
      <p>Plan your participation with our upcoming auction timetable.</p>
    </div>
    <div class="download-buttons">
      <a class="btn btn-download" href="/assets/data/Auction Schedule - January 2026 - June 2026.pdf"
         target="_blank" rel="noopener">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M12 16a1 1 0 0 1-.71-.29l-5-5a1 1 0 0 1 1.42-1.42L11 12.59V3a1 1 0 0 1 2 0v9.59l3.29-3.3a1 1 0 0 1 1.42 1.42l-5 5a1 1 0 0 1-.71.29zM5 19a1 1 0 0 1 0-2h14a1 1 0 0 1 0 2H5z"/>
        </svg>
        <span>Auction Schedule (2026)</span>
      </a>

      <a class="btn btn-download" href="/assets/data/Orthodox Tea Auction Schedule.pdf"
         target="_blank" rel="noopener">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
          <path d="M12 16a1 1 0 0 1-.71-.29l-5-5a1 1 0 0 1 1.42-1.42L11 12.59V3a1 1 0 0 1 2 0v9.59l3.29-3.3a1 1 0 0 1 1.42 1.42l-5 5a1 1 0 0 1-.71.29zM5 19a1 1 0 0 1 0-2h14a1 1 0 0 1 0 2H5z"/>
        </svg>
        <span>Orthodox Schedule (2025)</span>
      </a>
    </div>
  </div>
</section>

<!-- ===== PRE-AUCTION ===== -->
<section class="wrap pad auction-module pre-auction alt-row">
  <div class="alt-media">
    <div class="card soft why-img shadow-sm">
      <div class="ratio ratio-16x9">
        <?php pictureTag('auction_catalog','Pre-auction preparation'); ?>
      </div>
    </div>
  </div>

  <div class="alt-body">
    <h2 class="alt-title">Pre-Auction Catalogue & Preparation</h2>
    <p class="alt-sub">
      We ensure every lot is market-ready, professionally catalogued, accurately valued, and prepared for buyer engagement.
    </p>
    <ul class="points premium-bullets">
      <li><strong>Sampling & Grading:</strong> Coordinating representative samples, calibrated tasting, and quality assessment.</li>
      <li><strong>Catalogue Preparation:</strong> Each lot is documented with full traceability—factory mark, grade, invoice, and weight.</li>
      <li><strong>Valuation & Recommendation:</strong> Attaching market-driven valuations and advising on process optimization.</li>
      <li><strong>Buyer Engagement & Preview:</strong> Distributing catalogues, hosting sample viewings, and engaging global buyers.</li>
    </ul>
  </div>
</section>

<!-- ===== POST-AUCTION ===== -->
<section class="wrap pad auction-module post-auction alt-row alt-reverse">
  <div class="alt-media">
    <div class="card soft why-img shadow-sm">
      <div class="ratio ratio-16x9">
        <?php pictureTag('sampling%20room%202','Post-auction settlement'); ?>
      </div>
    </div>
  </div>

  <div class="alt-body">
    <h2 class="alt-title">Post-Auction Settlement & Follow-Up</h2>
    <p class="alt-sub">
      Our work continues beyond the sale — we coordinate settlement, documentation, logistics, and market feedback for each client.
    </p>
    <ul class="points premium-bullets">
      <li><strong>Invoice Generation & Remittance:</strong> Issuing sale invoices, tracking payments, and ensuring timely producer settlements.</li>
      <li><strong>Delivery Coordination:</strong> Liaising with warehouses and buyers for accurate dispatch and receipt confirmations.</li>
      <li><strong>Claims & Dispute Resolution:</strong> Managing quality or quantity claims and ensuring fair resolutions.</li>
      <li><strong>Market Feedback:</strong> Providing post-sale reports on buyer trends, grade performance, and market positioning.</li>
    </ul>
  </div>
</section>

<script defer src="/assets/js/auction.js"></script>