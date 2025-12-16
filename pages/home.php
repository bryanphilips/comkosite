<?php /* /pages/home.php */ ?>
<?php $current = $_GET['p'] ?? 'home'; ?>

<!-- =========================
     HERO (Bootstrap Carousel)
     ========================= -->
<section class="hero position-relative">
  <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3800">
    <!-- Indicators -->
    <div class="carousel-indicators">
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-label="Slide 1"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
      <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="5" aria-label="Slide 6"></button>
    </div>

    <!-- Slides -->
    <div class="carousel-inner">
      <div class="carousel-item active">
        <?php pictureTag('plantation_landscape-2','Combrok Offices'); ?>
      </div>
      <div class="carousel-item">
        <?php pictureTag('plantation-1','Tea Plantation'); ?>
      </div>
      <div class="carousel-item">
        <?php pictureTag('plantation-2','Tea Plantation'); ?>
      </div>
      <div class="carousel-item">
        <?php pictureTag('plantation-3','Tea Plantation'); ?>
      </div>
      <div class="carousel-item">
        <?php pictureTag('plantation-4','Tea Plantation'); ?>
      </div>
      <div class="carousel-item">
        <?php pictureTag('plantation-5','Tea Plantation'); ?>
      </div>
    </div>

    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev" aria-label="Previous">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next" aria-label="Next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>
  </div>

  <!-- Signature trapezium stats (floating) -->
  <aside class="stats-float" id="statsFloat" aria-live="polite">
    <div class="stat-card show">
      <span class="i">🌿</span><strong>50+</strong><small>Years of Brokerage</small>
    </div>
    <div class="stat-card">
      <span class="i">🏷️</span><strong>70+</strong><small>Garden Marks</small>
    </div>
    <div class="stat-card">
      <span class="i">📦</span><strong>25K+</strong><small>Lots / Year</small>
    </div>
    <div class="stat-card">
      <span class="i">⚖️</span><strong>60M+</strong><small>Kilos Per Year</small>
    </div>
    <div class="stat-card">
      <span class="i">🤝</span><strong>75+</strong><small>Buyers</small>
    </div>
    <div class="stat-card">
      <span class="i">🌍</span><strong>8</strong><small>Countries Served</small>
    </div>
    <div class="stat-card">
      <span class="i">👨‍🌾</span><strong>300K+</strong><small>Farmers Impacted</small>
    </div>
  </aside>
</section>

<!-- =========================
     BOXED STATS STRIP
     ========================= -->
<section class="stats-strip section-pad">
  <div class="wrap">
    <div class="row g-3">
      <div class="col-6 col-md-4 col-lg-3">
        <div class="metric cardy">
          <span class="i">🌿</span><div><strong>50+</strong><div>Years of Brokerage</div></div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="metric cardy">
          <span class="i">🏷️</span><div><strong>70+</strong><div>Garden Marks</div></div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="metric cardy">
          <span class="i">📦</span><div><strong>25K+</strong><div>Lots / Year</div></div>
        </div>
      </div>
      <div class="col-6 col-md-4 col-lg-3">
        <div class="metric cardy">
          <span class="i">⚖️</span><div><strong>60M+</strong><div>Kilos Per Year</div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- =========================
     WHY COMBROK
     ========================= -->
<section class="section-pad">
  <div class="container wrap">
    <div class="row g-5 align-items-center">

      <div class="col-lg-6">
        <div class="card soft why-img shadow-sm">
          <div class="ratio ratio-16x9">
            <?php pictureTag('plantation_landscape-3','About image'); ?>
          </div>
        </div>
      </div>

      <div class="col-lg-6">
        <h2 class="display-6 fw-bold mb-3">Why Choose <span class="accent">Combrok</span></h2>
        <p class="lead text-secondary">
          Combrok is a leading tea brokerage firm, commanding close to <strong>five decades</strong> of trusted
expertise in serving stakeholders across the entire tea value chain. As the third
registered Tea Broker in Africa, we have built enduring partnerships with a diverse
network of Producers and Buyers throughout the region.
        </p>
        <p>
          Operating from the heart of the Mombasa Auction Centre, we handle an impressive
average annual volume of over <strong>60 Million Kilograms</strong> of tea.
          Our catalogues consistently
attract more than <strong>75 local and international Buyers</strong>, 
with a geographic footprint spanning <strong>8+ African countries</strong>. Whether you&#39;re a Producer seeking market access or a Buyer looking for quality and
consistency, Combrok is your trusted partner in navigating the dynamic tea trade.
        </p>
      </div>

      <div class="col-lg-6 order-lg-2">
        <div class="card soft why-img shadow-sm">
          <div class="ratio ratio-16x9">
            <?php pictureTag('plantation_landscape','Customer focus image'); ?>
          </div>
        </div>
      </div>

      <div class="col-lg-6 order-lg-1">
        <h3 class="fw-semibold mb-3">Customer Focus</h3>
        <p>At Combrok, our customers are at the heart of everything we do.</p>
        <ul class="list-unstyled premium-bullets">
          <li>We cultivate a deep understanding of our customers, end users, and key influencers — recognizing what drives their decisions and what they value most.</li>
          <li>We innovate with purpose, differentiating around what truly creates value and consistently delivering on our promises.</li>
          <li>We foster seamless collaboration across our organization to respond swiftly and effectively to evolving customer needs.</li>
          <p><strong>Our commitment is simple: listen deeply, act decisively, and earn trust through every interaction</strong></p>
        </ul>
      </div>

    </div>
  </div>
</section>
<!-- === The Auction Process Section === -->
<section class="section-pad auction-process reveal">
  <div class="container text-center">
    <div class="wrap">
      <h2 class="auction-heading">The Auction Process</h2>
      <p class="auction-subtitle">
        <strong>Our streamlined auction process ensures transparency and efficiency from sample reception to the final sale.</strong>
      </p>

      <div class="auction-steps">
        <div class="auction-line"></div>
        <div class="auction-grid">
          <div class="auction-step">
            <div class="auction-circle">1</div>
            <h3>Cataloging</h3>
            
          </div>
          <div class="auction-step">
            <div class="auction-circle">2</div>
            <h3>Sampling</h3>
            
          </div>
          <div class="auction-step">
            <div class="auction-circle">3</div>
            <h3>Tasting & Valuation</h3>
           </div>
          <div class="auction-step">
            <div class="auction-circle">4</div>
            <h3>Auction & Settlement</h3>
            
          </div>
        </div>
      </div>

      <div class="auction-btn mt-4">
        <a href="/?p=auctions" class="btn btn-brand nav-cta d-inline-flex align-items-center gap-2">
          View Upcoming Auction Schedule
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
               class="lucide lucide-arrow-right">
            <path d="M5 12h14"></path>
            <path d="m12 5 7 7-7 7"></path>
          </svg>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- =========================
     CTA WIDE
     ========================= -->
<section class="cta-wide">
  <div class="wrap d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div>
      <h3 class="fw-bold mb-1">Need historical prices or weekly catalogues?</h3>
      <p class="text-secondary mb-0">Request curated, reliable data for your reporting and decision-making.</p>
    </div>
    <a class="btn btn-brand px-4" href="/?p=request">Request Data</a>
  </div>
</section>