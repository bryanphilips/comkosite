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

    <div class="ticker" id="newsTicker" aria-label="Latest updates">
      <div class="ticker-track">
        <span class="tick">Where agility meets adaptability.</span>
        <span class="tick">Empowering East Africa’s Tea Producers &amp; Buyers.</span>
        <span class="tick">Delivering excellence through integrity and innovation.</span>
        <span class="tick">Trusted auction representation, sampling &amp; Market intelligence.</span>
      </div>
    </div>
  </div>
</div>
</header>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Burger (unchanged)
  const burger = document.querySelector('.burger');
  const nav = document.querySelector('.nav');
  burger?.addEventListener('click', () => {
    const opened = nav?.classList.toggle('open');
    burger.setAttribute('aria-expanded', opened ? 'true' : 'false');
  });

  // Shrink header on scroll (unchanged)
  const header = document.querySelector('.site-header');
  window.addEventListener('scroll', () => {
    header.classList.toggle('scrolled', window.scrollY > 10);
  });

  // === Sequential R→L ticker (one line at a time) ===
  const ticker = document.getElementById('newsTicker');
  if (!ticker) return;
  const ticks = ticker.querySelectorAll('.tick');
  if (!ticks.length) return;

  let i = 0;
  const enterMs = 900;   // slide in
  const holdMs  = 2600;  // pause fully visible
  const exitMs  = 800;   // slide out
  const totalMs = enterMs + holdMs + exitMs;

  let timer;

  function show(index){
    // reset all
    ticks.forEach(t => t.classList.remove('active','exit'));
    const el = ticks[index];
    // enter
    el.classList.add('active');

    // schedule exit
    setTimeout(() => {
      el.classList.add('exit');
    }, enterMs + holdMs);
  }

  function cycle(){
    show(i);
    i = (i + 1) % ticks.length;
  }

  function start(){
    stop();
    // kick off immediately, then interval
    cycle();
    timer = setInterval(cycle, totalMs);
  }
  function stop(){
    if (timer) clearInterval(timer);
    timer = null;
  }

  // Start rotation
  start();

  // Pause on hover / resume on leave
  ticker.addEventListener('mouseenter', stop);
  ticker.addEventListener('mouseleave', start);

  // Save resources when not visible
  document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()));
  window.addEventListener('blur', stop);
  window.addEventListener('focus', start);
});
</script>