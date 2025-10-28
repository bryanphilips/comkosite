<?php $current = $_GET['p'] ?? 'home'; ?>

<section class="hero">
  <!-- Background slides -->
  <div class="slides fade-rotator">
    <?php pictureTag('plantation-1','Tea Plantation'); ?>
    <?php pictureTag('plantation-2','Tea Plantation'); ?>
    <?php pictureTag('plantation-3','Tea Plantation'); ?>
    <?php pictureTag('plantation-4','Tea Plantation'); ?>
    <?php pictureTag('plantation-5','Tea Plantation'); ?>
  
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
    <?php pictureTag('Tea-house-4','About image'); ?>
  </div>
  <div class="reveal">
    <h2>Why Choose <span class="accent">Combrok</span></h2>
    <p>Combrok is a trusted partner that endeavors to meet the needs of stakeholders within the tea industry.

Our journey started almost 50 years ago where we were privileged to be licensed as the third registered Tea Broker in Africa.

Since then, we have grown and partnered with a wide spectrum of Producers across the region who in turn process a variety of bulk teas.

We operate within the Mombasa Auction Centre where over the last 5 years our average annual volume turnaround has been over 60Mkgs.

Combrok catalogue attracts and is supported by over 75 local and international Buyers.

our geographic coverage spans more than 8 African countries.</p>
</div>
<div class="card shadow reveal">
    <?php pictureTag('Tea-house-4','About image'); ?>
  </div>
  <div class="reveal">
    <h3>Customer Focus</h3>
    <ul class="bullets">
      <li>We prioritize  to know who our customers, end users and influencers are – and understand what’s important to them, what drives their business and what they value most.</li>
      <li>We are clear on what creates value for our customers – and then innovate, differentiate and deliver on our promises.</li>
      <li>We work cooperatively with others across the organization to meet our customers’ needs.</li>
    </ul>
  
</section>
<!-- • We prioritize  to know who our customers, end users and influencers are – and understand what’s important to them, what drives their business and what they value most.
• We are clear on what creates value for our customers – and then innovate, differentiate and deliver on our promises.
• We work cooperatively with others across the organization to meet our customers’ needs. -->
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