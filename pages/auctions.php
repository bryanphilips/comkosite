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
    <a class="btn btn-download" href="/assets/data/Auction Schedule - January 2026 - June 2026.pdf"
       target="_blank" rel="noopener">
      Auction Schedule (2026)
    </a>
    <a class="btn btn-download" href="/assets/data/Orthodox Tea Auction Schedule.pdf"
       target="_blank" rel="noopener">
      Orthodox Schedule (2025)
    </a>
  </div>
</section>

<!-- ===== PRE-AUCTION ===== -->
<section class="wrap pad auction-module pre-auction alt-row">
  <div class="alt-media">
    <div class="card soft why-img shadow-sm">
      <div class="ratio ratio-16x9">
        <?php pictureTag('pre-auction','Pre-auction preparation'); ?>
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
        <?php pictureTag('post-auction','Post-auction settlement'); ?>
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