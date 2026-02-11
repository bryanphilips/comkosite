<?php
/* pages/market-reports.php — Premium Market Reports dashboard (single download + tabs) */
?>
<link rel="stylesheet" href="/assets/css/market-reports.css">

<section class="page-head">
  <div class="wrap">
    <h1>Market Reports</h1>
    <p class="muted">
      Weekly auction trends, performance ratios, and price movements. Switch tabs to compare broker vs market.
    </p>
  </div>
</section>

<section class="wrap pad mr-filters">

  <!-- TOP: single download + selectors (optional) -->
  <div class="mr-topbar card soft">
    <div class="mr-topbar-left">
      <h2 class="mr-title">Reports & Downloads</h2>
      <p class="mr-sub muted">Download the selected report and explore insights below.</p>
    </div>

    <div class="mr-topbar-right">
      <!-- Single download button -->
      <a class="btn ghost dl"
         id="mr-download"
         href="/assets/data/MARKET%20REPORT%20SALE%2041%20EXCEL.xlsx"
         download>
        <span class="ico" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
            <path d="M12 3v10" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M8 11l4 4 4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M4 21h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </span>
        <span id="mr-download-label">Download Combrok Report</span>
      </a>
    </div>
  </div>

  <!-- Tabs (below download) -->
  <div class="mr-filters-row">
    <div class="mr-tabs" role="tablist" aria-label="Market Reports Tabs">
      <button class="mr-tab active"
              id="tab-combrok"
              type="button"
              role="tab"
              aria-selected="true"
              aria-controls="panel-combrok"
              data-mr-tab="combrok">
        Combrok Sales Data
      </button>

      <button class="mr-tab"
              id="tab-auction"
              type="button"
              role="tab"
              aria-selected="false"
              aria-controls="panel-auction"
              data-mr-tab="auction">
        Auction Sales Data
      </button>
    </div>

    <div class="spacer"></div>

    <div class="mr-hint muted small">
      Tip: switch tabs to compare broker vs market.
    </div>
  </div>
</section>


<!-- ===========================
     TAB 1: COMBROK
=========================== -->
<section id="panel-combrok" class="mr-panel active" role="tabpanel" aria-labelledby="tab-combrok">
  <!-- KPI SECTION -->
  <section class="wrap pad">
    <div class="mr-kpi-block">
      <div class="mr-kpi-head">
        <h2 class="mr-kpi-title">Combrok Sales</h2>
        <p class="mr-kpi-sub muted">Broker performance summary for the selected period.</p>
      </div>

      <div class="kpi-grid" id="mr-kpis-combrok" aria-live="polite">
        <!-- JS injects KPIs -->
      </div>
    </div>
  </section>

  <!-- CHARTS ROW 1 -->
  <section class="wrap pad charts-grid">
    <article class="card hover">
      <header class="chart-head">
        <h3>Average Price (USD/kg) — Trend</h3>
        <span class="sub">Weekly</span>
      </header>
      <div class="chart" id="chart-price-trend-combrok" role="img" aria-label="Combrok average price trend line chart"></div>
    </article>

    <article class="card hover">
      <header class="chart-head">
        <h3>Quantity Offered vs Sold</h3>
        <span class="sub">Stacked bars</span>
      </header>
      <div class="chart" id="chart-qty-stacked-combrok" role="img" aria-label="Combrok offered and sold quantities"></div>
    </article>
  </section>

  <!-- CHARTS ROW 2 -->
  <section class="wrap pad charts-grid">
    <article class="card hover">
      <header class="chart-head">
        <h3>Sell-Through (%)</h3>
        <span class="sub">Sold vs Out-lot</span>
      </header>
      <div class="chart donut" id="chart-sellthrough-combrok" role="img" aria-label="Combrok sell-through donut chart"></div>
    </article>

    <article class="card hover">
      <header class="chart-head">
        <h3>Lots Sold & Offered</h3>
        <span class="sub">Dual line</span>
      </header>
      <div class="chart" id="chart-lots-lines-combrok" role="img" aria-label="Combrok lots sold and offered trend"></div>
    </article>
  </section>

  <!-- SUMMARY + NOTE -->
  <section class="wrap pad">
    <div id="mr-summary-combrok" class="card soft" style="margin-bottom:12px"></div>
    <p class="muted small">
      This is Combrok Sales Data and is analyzed and prepared by Combrok Limited.
    </p>
  </section>
</section>


<!-- ===========================
     TAB 2: AUCTION
=========================== -->
<section id="panel-auction" class="mr-panel" role="tabpanel" aria-labelledby="tab-auction" hidden>
  <!-- KPI SECTION -->
  <section class="wrap pad">
    <div class="mr-kpi-block">
      <div class="mr-kpi-head">
        <h2 class="mr-kpi-title">Auction Sales</h2>
        <p class="mr-kpi-sub muted">Market-wide auction totals for the selected period.</p>
      </div>

      <div class="kpi-grid" id="mr-kpis-auction" aria-live="polite">
        <!-- JS injects KPIs -->
      </div>
    </div>
  </section>

  <!-- CHARTS ROW 1 -->
  <section class="wrap pad charts-grid">
    <article class="card hover">
      <header class="chart-head">
        <h3>Average Price (USD/kg) — Trend</h3>
        <span class="sub">Weekly</span>
      </header>
      <div class="chart" id="chart-price-trend-auction" role="img" aria-label="Auction average price trend line chart"></div>
    </article>

    <article class="card hover">
      <header class="chart-head">
        <h3>Quantity Offered vs Sold</h3>
        <span class="sub">Stacked bars</span>
      </header>
      <div class="chart" id="chart-qty-stacked-auction" role="img" aria-label="Auction offered and sold quantities"></div>
    </article>
  </section>

  <!-- CHARTS ROW 2 -->
  <section class="wrap pad charts-grid">
    <article class="card hover">
      <header class="chart-head">
        <h3>Sell-Through (%)</h3>
        <span class="sub">Sold vs Out-lot</span>
      </header>
      <div class="chart donut" id="chart-sellthrough-auction" role="img" aria-label="Auction sell-through donut chart"></div>
    </article>

    <article class="card hover">
      <header class="chart-head">
        <h3>Lots Sold & Offered</h3>
        <span class="sub">Dual line</span>
      </header>
      <div class="chart" id="chart-lots-lines-auction" role="img" aria-label="Auction lots sold and offered trend"></div>
    </article>
  </section>

  <!-- SUMMARY + NOTE -->
  <section class="wrap pad">
    <div id="mr-summary-auction" class="card soft" style="margin-bottom:12px"></div>
    <p class="muted small">
      This is Auction Data and is analyzed and prepared by Combrok Limited.
    </p>
  </section>
</section>

<script defer src="/assets/js/market-reports.js"></script>