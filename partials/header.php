<?php
/* partials/header.php — Fixed navbar + fixed mini-ribbon (premium) */
$current = $_GET['p'] ?? 'home';
$tabs = [
  'about'=>'About','services'=>'Services','other-services'=>'Other Services','auctions'=>'Auctions',
  'products'=>'Products','market-reports'=>'Market Reports','events'=>'Events','contact'=>'Contact','enquiry'=>'Enquiry'
];
?>
<nav class="navbar navbar-expand-lg bg-white navbar-light fixed-top premium-navbar">
  <div class="container wrap">
    <a class="navbar-brand d-flex align-items-center gap-2" href="/" aria-label="Combrok Home" style="margin-left:2px">
      <img src="/assets/img/logo.png" alt="Combrok Logo" class="rounded-10 brand-float"
           onerror="this.src='/assets/img/hero-1.jpg'">
      <span class="fw-bold d-none d-lg-inline"></span>
    </a>

    <button class="navbar-toggler border-0 shadow-none" type="button"
            data-bs-toggle="offcanvas" data-bs-target="#mainNav"
            aria-controls="mainNav" aria-label="Open menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="offcanvas offcanvas-end premium-offcanvas" tabindex="-1" id="mainNav" aria-labelledby="mainNavLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold" id="mainNavLabel">Menu</h5>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="offcanvas" aria-label="Close"></button>
      </div>

      <div class="offcanvas-body">
        <ul class="navbar-nav ms-auto align-items-lg-center">

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

          <!-- CTAs -->
          <li class="nav-item ms-lg-2">
            <a class="btn btn-sm btn-outline-primary nav-cta <?= $current==='request'?'active':'' ?>"
               href="/?p=request">Request Data</a>
          </li>
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
      <div class="ribbon-msg">Empowering East Africa’s Tea Producers &amp; Buyers.</div>
      <div class="ribbon-msg">Trusted auction representation, sampling &amp; market intelligence.</div>
      <div class="ribbon-msg">Delivering excellence through integrity and innovation.</div>
    </div>
  </div>
</div>