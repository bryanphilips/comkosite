<?php
/* pages/about.php — scoped and self-contained */
?>
<!-- Include About-only assets -->
<link rel="stylesheet" href="/assets/css/about.css">
<section class="about-page wrap">

  <!-- Top split (image rotator + background text) -->
  <div class="about-grid pad">
    <div class="card shadow reveal image-rotator" style="--d:120ms">
      <?php
        // iterate between two+ images dynamically
        $bgImages = ['tea_harvest', 'plantation_landscape-2', 'plantation_landscape'];
        foreach ($bgImages as $img): ?>
          <div class="rotating-img">
            <?php pictureTag($img, 'About Combrok'); ?>
          </div>
      <?php endforeach; ?>
    </div>

    <div class="reveal" style="--d:200ms">
      <h2>Background Information</h2>
      <p>
        COMBROK Limited is among the pioneer tea broking firms at the Mombasa Tea Auction.
        Incorporated in 1978, we have delivered uninterrupted brokerage services for more than
        four decades—representing producers across Kenya, Uganda, Rwanda, Burundi, Tanzania,
        Madagascar, Mozambique, Malawi, DRC, and Ethiopia.
      </p>
      <p>
        We handle a substantial weekly portfolio and a broad spectrum of tea grades while
        maintaining our hallmarks: adaptability, integrity, excellence, and consistency.
        Our role spans end-to-end auction representation, professional tasting, technical
        advisory, and timely settlement.
      </p>
    </div>
  </div>

  <!-- Mission / Vision / Values -->
  <section class="pad">
    <div class="cards">
      <article class="card hover reveal" style="--d:80ms">
        <h3 class="with-icon">
          <span class="i">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M12 5c5.5 0 9.5 4.5 10.7 6.2a1.5 1.5 0 0 1 0 1.6C21.5 14.5 17.5 19 12 19S2.5 14.5 1.3 12.8a1.5 1.5 0 0 1 0-1.6C2.5 9.5 6.5 5 12 5zm0 3.5A5.5 5.5 0 1 0 17.5 14 5.5 5.5 0 0 0 12 8.5zm0 2.5a3 3 0 1 1-3 3 3 3 0 0 1 3-3z" fill="currentColor"/>
            </svg>
          </span>
          Vision
        </h3>
        <p>To be the most reliable and trusted tea brokerage firm in Africa, recognized globally
          for our adaptability, excellence, and commitment to advancing the tea trade.</p>
      </article>

      <article class="card hover reveal" style="--d:160ms">
        <h3 class="with-icon">
          <span class="i">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm3.9 6.1-2.8 6.4a1 1 0 0 1-.6.6l-6.4 2.8 2.8-6.4a1 1 0 0 1 .6-.6l6.4-2.8zM11 11l2 2" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
          Mission
        </h3>
        <p>To deliver trusted and innovative tea brokerage services by connecting tea trade
          stakeholders through integrity, quality, and clarity—promoting sustainable value,
          reliable market intelligence, and long-term partnerships across global tea markets.</p>
      </article>

      <article class="card hover reveal" style="--d:240ms">
        <h3 class="with-icon">
          <span class="i">
            <svg viewBox="0 0 24 24" aria-hidden="true">
              <path d="M7 4c3 0 5 2 5 5-3 0-5-2-5-5zm10 0c-3 0-5 2-5 5 3 0 5-2 5-5zM12 13c-4.4 0-8 2.7-8 6h16c0-3.3-3.6-6-8-6z" fill="currentColor"/>
            </svg>
          </span>
          Our Values
        </h3>
        <ul class="bullets values-inline">
          <li>Integrity</li><li>Excellence</li><li>Adaptability</li>
          <li>Sustainability</li><li>Collaboration</li>
        </ul>
      </article>
    </div>
  </section>

  <?php
  function hasOrgChartImage() {
    $base = $_SERVER['DOCUMENT_ROOT'].'/assets/img/';
    foreach (['org-chart.avif','org-chart.webp','org-chart.png','org-chart.jpg'] as $f) {
      if (file_exists($base.$f)) return '/assets/img/'.$f;
    }
    return null;
  }
  $orgImg = hasOrgChartImage();
  ?>

<section class="pad org-section">
  <header class="org-head">
    <h2>COMBROK ORGANIZATIONAL STRUCTURE</h2>
  </header>

  <?php if ($orgImg): ?>
    <div class="card shadow reveal" style="--d:80ms">
      <img src="<?= htmlspecialchars($orgImg) ?>" alt="Combrok Organization Chart"
           style="width:100%;height:auto;border-radius:var(--radius-xl)"/>
    </div>
  <?php endif; ?>

  <div class="org card shadow">

    <!-- Tier 1 -->
    <div class="org-tier">
      <div class="org-node reveal" style="--d:60ms">
        <div class="org-title">Board of Directors</div>
        <p class="org-note">Governance &amp; strategic oversight</p>
      </div>
    </div>

    <!-- Vertical connector with arrow -->
    <div class="org-conn-vert" aria-hidden="true"></div>

    <!-- Tier 2 -->
    <div class="org-tier">
      <div class="org-node reveal" style="--d:120ms">
        <div class="org-title">Management</div>
        <p class="org-note">Execution, performance &amp; stakeholder alignment</p>
      </div>
    </div>

    <!-- Vertical connector with arrow -->
    <div class="org-conn-vert" aria-hidden="true"></div>

    <!-- Tier 3 (three departments on same level) -->
    <div class="org-tier org-tier--trio">
      <div class="org-node reveal" style="--d:180ms">
        <div class="org-chip">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v4H4zM4 12h10v6H4zM16 12h4v6h-4z" fill="currentColor"/></svg>
          Finance &amp; IT
        </div>
      </div>

      <div class="org-conn-horz" aria-hidden="true"></div> <!-- double-ended arrow rail -->

      <div class="org-node reveal" style="--d:220ms">
        <div class="org-chip">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 3h4l1 2h3v3l2 1v4l-2 1v3h-3l-1 2h-4l-1-2H6v-3l-2-1V9l2-1V5h3l1-2zm2 6a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9z" fill="currentColor"/></svg>
          Operations
        </div>
      </div>

      <div class="org-conn-horz" aria-hidden="true"></div> <!-- double-ended arrow rail -->

      <div class="org-node reveal" style="--d:260ms">
        <div class="org-chip">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12l8-5v10l-8-5zm9 2h3l4 3v-8l-4 3h-3v2zm-5 4h2v2H7z" fill="currentColor"/></svg>
          Sales &amp; Marketing
        </div>
      </div>
    </div>
  </div>
</section>

  <section class="pad cta-wide">
    <div class="cta-row reveal" style="--d:120ms">
      <div>
        <h3>Discover our end-to-end brokerage approach</h3>
        <p class="muted">From sampling and tasting to auction representation, settlement, and market intelligence.</p>
      </div>
      <a class="btn" href="/?p=services">Explore Services</a>
    </div>
  </section>

</section>
<script defer src="/assets/js/about.js"></script>