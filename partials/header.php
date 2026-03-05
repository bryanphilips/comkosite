<?php
/* partials/header.php — UPDATED (better spacing: menu closer to logo, CTA right) */
$current = $_GET['p'] ?? 'home';

$tabs = [
  'about'          => 'About',
  'services'       => 'Services',
  'auctions'       => 'Auctions',
  'products'       => 'Products',
  'market-reports' => 'Market Reports',
  'events'         => 'Events',
  'sustainability' => 'Sustainability',
  'enquiry'        => 'Enquiry'
];
?>
<nav class="navbar navbar-expand-lg bg-white navbar-light fixed-top premium-navbar">
  <div class="container wrap">

    <!-- Brand -->
    <a class="navbar-brand d-flex align-items-center gap-2" href="/?p=home" aria-label="Combrok Home">
      <img src="/assets/img/logo.png" alt="Combrok Logo" class="rounded-10 brand-float"
           onerror="this.src='/assets/img/hero-1.jpg'">
      <span class="fw-bold d-none d-lg-inline"></span>
    </a>

    <!-- Toggler -->
    <button class="navbar-toggler border-0 shadow-none" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-label="Open menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Offcanvas / Desktop Nav -->
    <div class="offcanvas offcanvas-end premium-offcanvas" tabindex="-1" id="mainNav" aria-labelledby="mainNavLabel">

      <!-- Mobile header -->
      <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold" id="mainNavLabel">Menu</h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>

      <div class="offcanvas-body">
        <!-- ✅ NOTE: removed ms-auto so CSS can position menu close to logo (desktop) -->
        <ul class="navbar-nav align-items-lg-center">

          <!-- Home link (burger only) -->
          <li class="nav-item d-lg-none">
            <a class="nav-link px-3 py-2 premium-link <?= $current==='home'?'active':'' ?>"
               href="/?p=home" aria-label="Home">Home</a>
          </li>

          <?php foreach ($tabs as $slug => $label):
            $active = ($current === $slug) ? 'active' : '';
            $aria   = ($current === $slug) ? 'aria-current="page"' : '';
          ?>
            <li class="nav-item">
              <a class="nav-link px-3 py-2 premium-link <?= $active ?>"
                 href="/?p=<?= $slug ?>" <?= $aria ?>><?= $label ?></a>
            </li>
          <?php endforeach; ?>

          <!-- CTA -->
          <li class="nav-item">
            <a class="btn btn-sm btn-brand nav-cta <?= $current==='private-sale'?'active':'' ?>"
               href="/?p=private-sale">Private Sale</a>
          </li>

        </ul>
      </div>
    </div>

  </div>
</nav>

<!-- Fixed Mini Ribbon -->
<div class="mini-ribbon">
  <div class="wrap d-flex align-items-center gap-2 w-100">
    <strong class="text-white" style="margin-left:2px">Combrok Limited</strong>
    <div id="ribbonRotator" class="ribbon-rotator flex-grow-1">
      <div class="ribbon-msg">Where agility meets adaptability.</div>
      <div class="ribbon-msg">Empowering Tea Producers &amp; Buyers.</div>
      <div class="ribbon-msg">Trusted auction representation, sampling &amp; market intelligence.</div>
      <div class="ribbon-msg">Delivering excellence through integrity and innovation.</div>
      <div class="ribbon-msg">🏆 Winner: Best Broker (Highest Priced) — 2024</div>
    </div>
  </div>
</div>