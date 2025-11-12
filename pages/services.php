<?php
/* pages/services.php — Premium cards, centered headings, alternating mini image/content */

$services = [
  [
    'category' => 'Pre-Sale & Quality Services',
    'blurb'    => 'We prepare your teas for market with disciplined sampling, rigorous tasting, and clear valuation—so every lot reaches buyers with confidence.',
    'items'    => [
      ['title' => 'Sampling & Grading', 'text' => 'We arrange representative samples for every lot (e.g., 4kg splits) and ensure distribution to buyers is accurate and timely.', 'img' => 'sampling'],
      ['title' => 'Tasting & Quality Evaluation', 'text' => 'Our tasting panel evaluates flavour, aroma, liquor and appearance—translating findings into clear, actionable guidance.', 'img' => 'sample-cup'],
      ['title' => 'Valuation & Recommendation', 'text' => 'We attach pragmatic values based on quality and live market signals, and advise on targeted quality improvements.', 'img' => 'valuation'],
      ['title' => 'Catalogue Preparation', 'text' => 'We compile the official catalogue—origin, grades, weights, packaging, and tasting notes—presented clearly for buyers.', 'img' => 'catalogue'],
      ['title' => 'Lot Segmentation & Packaging', 'text' => 'We guide optimal lot splits (e.g., 20/40/60) and packing to enhance appeal, access and logistics.', 'img' => 'lots'],
      ['title' => 'Factory & Production Advisory', 'text' => 'We visit factories and give practical recommendations on plucking standards, manufacture, handling and warehousing.', 'img' => 'factory'],
    ],
  ],
  [
    'category' => 'Market Access & Sales Services',
    'blurb'    => 'We represent you at auction, drive buyer engagement, and manage the entire sales path—from listing and bidding to documentation and payment.',
    'items'    => [
      ['title' => 'Auction Sales', 'text' => 'We represent producers at the EATTA auction in Mombasa and manage the full selling process.', 'img' => 'lots'],
      ['title' => 'Spot Sales & Forward Contracts', 'text' => 'We structure direct spot deals and forward contracts where they add value to your portfolio.', 'img' => 'sample'],
      ['title' => 'Catalogue Distribution & Buyer Engagement', 'text' => 'We circulate catalogues and samples, stimulate interest, and capture buyer feedback.', 'img' => 'catalogue'],
      ['title' => 'Logistics, Delivery & Payment', 'text' => 'We handle invoicing, confirmations, payment follow-up and coordinate warehouse delivery.', 'img' => 'factory'],
    ],
  ],
  [
    'category' => 'Market Intelligence, Advisory & Reporting',
    'blurb'    => 'We convert data into decisions—tracking trends, forecasting demand, and advising on market fit, quality focus and timing.',
    'items'    => [
      ['title' => 'Market Analysis & Trend Reporting', 'text' => 'We monitor production, exports, pricing and consumer shifts, then brief you with clear recommendations.', 'img' => 'valuation'],
      ['title' => 'Forecasts & Projections', 'text' => 'We combine quality signals with demand outlooks to guide production plans, grade mix and price expectations.', 'img' => 'catalogue'],
      ['title' => 'Market Fit & Buyer Needs', 'text' => 'We align grades and cup profiles to target markets and buyer preferences—including blending needs.', 'img' => 'sample-cup'],
      ['title' => 'Storage, Warehousing & Handling', 'text' => 'We protect value through best-practice storage, handling and delivery coordination.', 'img' => 'factory'],
      ['title' => 'Risk Management', 'text' => 'We navigate disputes, delays and downturns with structured contracts and proactive intelligence.', 'img' => 'tea-house-4'],
    ],
  ],
  [
    'category' => 'Post-Sale Services & Follow-Up',
    'blurb'    => 'We close the loop with transparent reporting, timely remittance, and feedback that drives continuous improvement.',
    'items'    => [
      ['title' => 'Account Sales & Remittance', 'text' => 'We issue detailed account sales and remit proceeds promptly within auction timelines.', 'img' => 'catalogue'],
      ['title' => 'Claims Handling & Disputes', 'text' => 'We manage buyer claims professionally, liaising with warehouses and producers to resolve quickly.', 'img' => 'tea-house-4'],
      ['title' => 'Performance Monitoring & Feedback', 'text' => 'We analyse prices, buyer behaviour and unsolds—and share insights to refine your strategy.', 'img' => 'valuation'],
    ],
  ],
  [
    'category' => 'Value-Added & Consultancy Services',
    'blurb'    => 'We go beyond selling—building long-term capability through audits, training, strategy and benchmarking.',
    'items'    => [
      ['title' => 'Audit & Consultancy', 'text' => 'We support audits, traceability and compliance, and design practical improvement programmes.', 'img' => 'tea-house-3'],
      ['title' => 'Technical Visits & Training', 'text' => 'On-site sessions on plucking, leaf handling and manufacture—often alongside buyer visits.', 'img' => 'factory'],
      ['title' => 'Strategic Planning', 'text' => 'We help prioritise grades, optimise quality-yield balance, and target the right markets.', 'img' => 'sample'],
      ['title' => 'Brand & Positioning', 'text' => 'We advise on differentiation, certification and access to premium and niche segments.', 'img' => 'TEA-HOUSE-2'],
      ['title' => 'Data & Benchmarking', 'text' => 'We benchmark your performance across grades and seasons to show where to push or pivot.', 'img' => 'sample-cup'],
    ],
  ],
];
?>

<link rel="stylesheet" href="/assets/css/services.css">

<section class="services-page">
  <div class="wrap">
    <?php foreach ($services as $section): ?>
      <article class="svc-card reveal">
        <header class="svc-head">
          <h2 class="svc-title"><?= htmlspecialchars($section['category']) ?></h2>
          <p class="svc-sub"><?= htmlspecialchars($section['blurb']) ?></p>
        </header>

        <div class="svc-items">
          <?php foreach ($section['items'] as $i => $item): ?>
            <div class="svc-item <?= $i % 2 ? 'alt' : '' ?>">
              <div class="svc-img ratio ratio-4x3">
                <?php pictureTag($item['img'], $item['title']); ?>
              </div>
              <div class="svc-copy">
                <h3 class="svc-item-title"><?= htmlspecialchars($item['title']) ?></h3>
                <p class="svc-item-text"><?= htmlspecialchars($item['text']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="cta-wide-bleed">
  <div class="wrap">
    <div class="cta-row">
      <div>
        <h3 class="fw-bold mb-1">Want weekly market intelligence?</h3>
        <p class="muted mb-0">We publish concise auction highlights—lots offered vs. sold, average prices, and grade performance.</p>
      </div>
      <a class="btn btn-brand px-4" href="/?p=market-reports">View Market Reports</a>
    </div>
  </div>
</section>

<script defer src="/assets/js/services.js"></script>