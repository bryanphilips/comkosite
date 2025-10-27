<?php $current = $_GET['p'] ?? 'home'; ?>

<section class="hero">
  <!-- Background slides -->
  <div class="slides fade-rotator">
    <?php pictureTag('tea-house-4','Tea estates'); ?>
    <?php pictureTag('new-tea-house-1','Auction hall'); ?>
    <?php pictureTag('new-tea-house-2','Tasting lab'); ?>
  </div>

  <!-- Trapezium floating stats (bottom-left, translucent, independent cycle) -->
  <aside class="stats-float" id="statsFloat" aria-live="polite">
    <div class="stat-card show">
      <span class="i">🌿</span><strong>50+</strong><small>Years of Brokerage</small>
    </div>
    <div class="stat-card">
      <span class="i">🏷️</span><strong>85+</strong><small>Garden Marks</small>
    </div>
    <div class="stat-card">
      <span class="i">📦</span><strong>25K+</strong><small>Lots / Year</small>
    </div>
    <div class="stat-card">
      <span class="i">⚖️</span><strong>60M+</strong><small>Kilos Per Year</small>
    </div>
    <div class="stat-card">
      <span class="i">🤝</span><strong>70+</strong><small>Buyers</small>
    </div>
    <div class="stat-card">
      <span class="i">🌍</span><strong>8</strong><small>Countries Served</small>
    </div>
    <div class="stat-card">
      <span class="i">👨‍🌾</span><strong>300K+</strong><small>Farmers Impacted</small>
    </div>
  </aside>
</section>

<!-- ========== WHY COMBROK SECTION ========== -->
<section class="wrap grid-2 pad why-combrok">
  <div class="card shadow reveal">
    <?php pictureTag('about','About image'); ?>
  </div>
  <div class="reveal">
    <h2>Why Choose <span class="accent">Combrok</span></h2>
    <p>At Combrok, we believe in partnership, transparency, and sustainable value creation across Africa’s tea supply chain.</p>
    <ul class="bullets">
      <li><strong>End-to-end Auction Representation</strong> — from catalogue to settlement.</li>
      <li><strong>Professional Sampling & Tasting</strong> — ensuring quality and consistency.</li>
      <li><strong>Actionable Market Intelligence</strong> — insights that drive smarter trading and better returns.</li>
    </ul>
  </div>
</section>

<!-- ========== CALL TO ACTION ========== -->
<section class="cta-wide reveal">
  <div class="wrap">
    <div class="cta-row">
      <div>
        <h3>Need historical prices or weekly catalogues?</h3>
        <p class="muted">Request curated, reliable data for your reports and business decisions.</p>
      </div>
      <a class="btn" href="/?p=request">Request Data</a>
    </div>
  </div>
</section>

<!-- ========== JS (slides + reveal + stats) ========== -->
<script>
// Reveal on scroll
const observer = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('_in'); });
}, { threshold: 0.2 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// Hero image rotator
const slideEls = document.querySelectorAll('.slides picture');
let slideIdx = 0;
function rotateSlides() {
  slideEls.forEach((s, idx) => s.classList.toggle('active', idx === slideIdx));
  slideIdx = (slideIdx + 1) % slideEls.length;
}
if (slideEls.length) {
  rotateSlides();
  setInterval(rotateSlides, 5000);
}

// Stats rotator (independent)
const statEls = document.querySelectorAll('.stats-float .stat-card');
let statIdx = 0;
function rotateStats() {
  statEls.forEach((s, idx) => s.classList.toggle('show', idx === statIdx));
  statIdx = (statIdx + 1) % statEls.length;
}
if (statEls.length) {
  statEls.forEach((s, i) => s.classList.toggle('show', i === 0));
  setInterval(rotateStats, 3000);
}
</script>