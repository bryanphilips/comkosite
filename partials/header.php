<?php $current = $_GET['p'] ?? 'home'; ?>

<header class="site-header">
  <div class="wrap">
    <!-- Brand -->
    <a class="brand" href="/">
      <span class="brand-logo">
        <img src="/assets/img/logo.png" alt="Combrok Logo" onerror="this.src='/assets/img/hero-1.jpg'">
      </span>
      <span class="brand-text"></span>
    </a>

    <!-- Navigation -->
    <nav class="nav <?= htmlspecialchars($current) ?>">
      <a href="/?p=about"          class="<?= $current==='about'?'active':'' ?>">About</a>
      <a href="/?p=services"       class="<?= $current==='services'?'active':'' ?>">Services</a>
      <a href="/?p=other-services" class="<?= $current==='other-services'?'active':'' ?>">Other Services</a>
      <a href="/?p=auctions"       class="<?= $current==='auctions'?'active':'' ?>">Auctions</a>
      <a href="/?p=products"       class="<?= $current==='products'?'active':'' ?>">Products</a>
      <a href="/?p=market-reports" class="<?= $current==='market-reports'?'active':'' ?>">Market Reports</a>
      <a href="/?p=events"         class="<?= $current==='events'?'active':'' ?>">Events</a>
      <a href="/?p=contact"        class="<?= $current==='contact'?'active':'' ?>">Contact</a>
      <a href="/?p=enquiry"        class="<?= $current==='enquiry'?'active':'' ?>">Enquiry</a>
      <a href="/?p=request"        class="btn <?= $current==='request'?'active':'' ?>">Request Data</a>
    </nav>

    <!-- Mobile burger toggle -->
    <button class="burger" aria-label="Toggle menu">☰</button>
  </div>

  <!-- Mini ticker ribbon (inside header, full width, below the main row) -->
  <div class="mini-ribbon">
    <div class="mini-ribbon-content wrap">
      <span class="logo-text">Combrok <span class="accent">Limited</span></span>
      <div class="ticker" id="newsTicker" aria-label="Latest update">
        <span class="ticker-track">
          Empowering East Africa’s Tea Producers &amp; Buyers — delivering excellence through integrity and innovation. • Where agility meets adaptability. • Trusted auction representation, sampling, tasting &amp; market intelligence.
        </span>
      </div>
    </div>
  </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const burger = document.querySelector('.burger');
  const nav = document.querySelector('.nav');

  burger?.addEventListener('click', () => {
    nav?.classList.toggle('open');
  });

  // Shrink header on scroll
  const header = document.querySelector('.site-header');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 10) header.classList.add('scrolled');
    else header.classList.remove('scrolled');
  });

  // Right-to-left ticker: pause on hover
  const ticker = document.getElementById('newsTicker');
  if (ticker) {
    const track = ticker.querySelector('.ticker-track');
    if (track) {
      void track.offsetWidth; // kick off CSS animation if needed
      ticker.addEventListener('mouseenter', () => track.style.animationPlayState = 'paused');
      ticker.addEventListener('mouseleave', () => track.style.animationPlayState = 'running');
    }
  }
});
</script>