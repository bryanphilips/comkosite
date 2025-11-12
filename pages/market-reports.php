<?php
/* pages/market-reports.php — Premium Market Reports dashboard */
?>
<link rel="stylesheet" href="/assets/css/market-reports.css">

<section class="page-head">
  <div class="wrap">
    <h1>Market Reports</h1>
    <p class="muted">Weekly auction trends, performance ratios, and price movements. Filter by year and week to drill down.</p>
  </div>
</section>

<section class="wrap pad mr-filters">
  <div class="filters">
    <label class="field">
      <span>Year</span>
      <input id="mr-year" type="number" min="2015" max="2100" placeholder="e.g., 2026" inputmode="numeric">
    </label>

    <div class="field range">
      <span>Week Range</span>
      <div class="range-row">
        <select id="mr-week-from" disabled>
          <option value="">From</option>
        </select>
        <span class="sep">to</span>
        <select id="mr-week-to" disabled>
          <option value="">To</option>
        </select>
      </div>
      <label class="chk">
        <input type="checkbox" id="mr-all-weeks" checked>
        <span>All weeks</span>
      </label>
    </div>

    <button class="btn" id="mr-apply" type="button">Apply</button>
    <button class="btn ghost" id="mr-clear" type="button">Clear</button>

    <div class="spacer"></div>

    <!-- Replaced the CSV button with a direct Excel download -->
    <a class="btn ghost dl"
       id="mr-download-report"
       href="/assets/data/MARKET%20REPORT%20SALE%2041%20EXCEL.xlsx"
       download>
      Download Market Report
    </a>
  </div>
</section>

<!-- KPI CARDS -->
<section class="wrap pad kpi-grid" id="mr-kpis" aria-live="polite">
  <!-- JS will inject KPI cards -->
</section>

<!-- CHARTS ROW 1 -->
<section class="wrap pad charts-grid">
  <article class="card hover">
    <header class="chart-head">
      <h3>Average Price (USD/kg) — Trend</h3>
      <span class="sub">Weekly</span>
    </header>
    <div class="chart" id="chart-price-trend" role="img" aria-label="Average price trend line chart"></div>
  </article>

  <article class="card hover">
    <header class="chart-head">
      <h3>Quantity Offered vs Sold</h3>
      <span class="sub">Stacked bars</span>
    </header>
    <div class="chart" id="chart-qty-stacked" role="img" aria-label="Stacked bars showing offered and sold quantities"></div>
  </article>
</section>

<!-- CHARTS ROW 2 -->
<section class="wrap pad charts-grid">
  <article class="card hover">
    <header class="chart-head">
      <h3>Sell-Through (%)</h3>
      <span class="sub">Sold vs Out-lot</span>
    </header>
    <div class="chart donut" id="chart-sellthrough" role="img" aria-label="Donut chart of sold vs out-lot"></div>
  </article>

  <article class="card hover">
    <header class="chart-head">
      <h3>Lots Sold & Offered</h3>
      <span class="sub">Dual line</span>
    </header>
    <div class="chart" id="chart-lots-lines" role="img" aria-label="Lots sold and offered trend"></div>
  </article>
</section>

<!-- TABLE -->
<section class="wrap pad">
  <div id="mr-summary" class="card soft" style="margin-bottom:12px"></div>

  <div class="table-wrap">
    <table class="table" id="mr-table">
      <thead>
        <tr>
          <th>Year</th><th>Week</th><th>Lots Offered</th><th>Lots Sold</th>
          <th>Qty Offered (kg)</th><th>Qty Sold (kg)</th><th>Sold %</th><th>Avg Price (USD/kg)</th>
        </tr>
      </thead>
      <tbody id="mr-rows"></tbody>
    </table>
  </div>

  <p class="muted small">
    This is Auction Data and is analyzed and prepared by Combrok Limited
  </p>
</section>

<script defer src="/assets/js/market-reports.js"></script>