<?php
/* pages/market-reports.php — Premium Market Reports dashboard (single CTA + tabs) */
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

  <!-- TOP: CTA -->
  <div class="mr-topbar card soft">
    <div class="mr-topbar-left">
      <h2 class="mr-title">Reports &amp; Downloads</h2>
      <p class="mr-sub muted">Request the selected report and explore insights below.</p>
    </div>

    <div class="mr-topbar-right">
      <a class="btn ghost dl" id="mr-request" href="/?p=enquiry">
        <span class="ico" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none">
            <path d="M3 11.5l18-8-7 18-2.5-7L3 11.5z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
            <path d="M11.5 14.5L21 3.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
          </svg>
        </span>
        <span id="mr-request-label">Request Market Report</span>
      </a>
    </div>
  </div>

  <!-- FILTER BAR: Year + Sale Range -->
  <div class="filters" style="margin-top:14px;">
    <div class="field">
      <span>Year</span>
      <select id="mr-year" aria-label="Select year">
        <option value="">All</option>
      </select>
    </div>

    <!-- ✅ Sale Range only -->
    <div class="field range" style="min-width:320px;">
      <span>Sale Range</span>
      <div class="range-row" style="gap:10px;">
        <select id="mr-sale-from" aria-label="From sale">
          <option value="">From</option>
        </select>

        <span class="sep" style="font-weight:900;color:#0A4E73;">to</span>

        <select id="mr-sale-to" aria-label="To sale">
          <option value="">To</option>
        </select>
      </div>

      <label class="chk" for="mr-all-sales">
        <input type="checkbox" id="mr-all-sales" checked />
        All sales in selected year
      </label>
    </div>

    <button class="btn" id="mr-apply" type="button">Apply</button>
    <button class="btn ghost" id="mr-clear" type="button">Clear</button>

    <div class="spacer"></div>
    <div class="mr-hint muted small" id="mr-range-label">Loading latest sale…</div>
  </div>

  <!-- Tabs -->
  <div class="mr-filters-row">
    <div class="mr-tabs" role="tablist" aria-label="Market Reports Tabs">
      <button class="mr-tab active"
              id="tab-combrok"
              type="button"
              role="tab"
              aria-selected="true"
              aria-controls="panel-combrok"
              data-mr-tab="combrok">
        Combrok Data
      </button>

      <button class="mr-tab"
              id="tab-auction"
              type="button"
              role="tab"
              aria-selected="false"
              aria-controls="panel-auction"
              data-mr-tab="auction">
        Mombasa Auction Data
      </button>
    </div>

    <div class="spacer"></div>
  </div>
</section>

<!-- ===========================
     TAB 1: COMBROK
=========================== -->
<section id="panel-combrok" class="mr-panel active" role="tabpanel" aria-labelledby="tab-combrok">

  <!-- KPIs -->
  <section class="wrap pad">
    <div class="mr-kpi-block">
      <div class="mr-kpi-head">
        <h2 class="mr-kpi-title">Combrok Data</h2>
        <p class="mr-kpi-sub muted">Broker performance summary for the selected period.</p>
      </div>
      <div class="kpi-grid" id="mr-kpis-combrok" aria-live="polite"></div>
    </div>
  </section>

  <!-- Charts -->
  <section class="wrap pad charts-grid">
    <article class="card hover">
      <header class="chart-head">
        <h3>Average Price (USD/kg) — Trend</h3>
        <span class="sub">By Sale</span>
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

  <section class="wrap pad charts-grid">
    <article class="card hover">
      <header class="chart-head">
        <h3>Sell-Through (%)</h3>
        <span class="sub">Latest / selected range</span>
      </header>
      <div class="chart donut" id="chart-sellthrough-combrok" role="img" aria-label="Combrok sell-through donut chart"></div>
    </article>

    <article class="card hover">
      <header class="chart-head">
        <h3>Lots Sold Trend</h3>
        <span class="sub">By Sale</span>
      </header>
      <div class="chart" id="chart-lots-lines-combrok" role="img" aria-label="Combrok lots sold trend"></div>
    </article>
  </section>

  <!-- Summary + Analytics -->
  <section class="wrap pad">
    <div id="mr-summary-combrok" class="card soft" style="margin-bottom:12px"></div>

    <p class="muted small" style="margin-top:0;">
      This is Combrok Sales Data and is analyzed and prepared by Combrok Limited.
    </p>

    <div class="table-wrap card soft" style="padding:0;margin-top:14px;">
      <table class="table" aria-label="Combrok sales table">
        <thead>
          <tr>
            <th>Year</th>
            <th>Sale</th>
            <th>Lots Offered</th>
            <th>Lots Sold</th>
            <th>Qty Offered (kg)</th>
            <th>Qty Sold (kg)</th>
            <th>Sell-through</th>
            <th>Avg Price (USD/kg)</th>
          </tr>
        </thead>
        <tbody id="mr-rows-combrok"></tbody>
      </table>
    </div>
  </section>

</section>

<!-- ===========================
     TAB 2: AUCTION
=========================== -->
<section id="panel-auction" class="mr-panel" role="tabpanel" aria-labelledby="tab-auction" hidden>

  <!-- KPIs -->
  <section class="wrap pad">
    <div class="mr-kpi-block">
      <div class="mr-kpi-head">
        <h2 class="mr-kpi-title">Mombasa Auction</h2>
        <p class="mr-kpi-sub muted">Market-wide auction totals for the selected period.</p>
      </div>
      <div class="kpi-grid" id="mr-kpis-auction" aria-live="polite"></div>
    </div>
  </section>

  <!-- Charts -->
  <section class="wrap pad charts-grid">
    <article class="card hover">
      <header class="chart-head">
        <h3>Average Price (USD/kg) — Trend</h3>
        <span class="sub">By Sale</span>
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

  <section class="wrap pad charts-grid">
    <article class="card hover">
      <header class="chart-head">
        <h3>Sell-Through (%)</h3>
        <span class="sub">Latest / selected range</span>
      </header>
      <div class="chart donut" id="chart-sellthrough-auction" role="img" aria-label="Auction sell-through donut chart"></div>
    </article>

    <article class="card hover">
      <header class="chart-head">
        <h3>Lots Sold Trend</h3>
        <span class="sub">By Sale</span>
      </header>
      <div class="chart" id="chart-lots-lines-auction" role="img" aria-label="Auction lots sold trend"></div>
    </article>
  </section>

  <section class="wrap pad">
    <div id="mr-summary-auction" class="card soft" style="margin-bottom:12px"></div>
    <p class="muted small">
      This is Auction Data and is analyzed and prepared by Combrok Limited.
    </p>
  </section>

</section>

<script defer src="/assets/js/market-reports.js"></script>