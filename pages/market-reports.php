<section class="page-head"><div class="wrap"><h1>Market Reports</h1></div></section>
<section class="wrap pad">
  <div class="filters">
    <label>Year <input id="mr-year" type="number" min="2015" max="2100" placeholder="e.g., 2025"></label>
    <label>Week <input id="mr-week" type="number" min="1" max="52" placeholder="(optional)"></label>
    <button class="btn" id="mr-apply" type="button">Apply</button>
    <button class="btn ghost" id="mr-clear" type="button">Clear</button>
  </div>
  <div id="mr-summary" class="card" style="margin:12px 0"></div>
  <table class="table" id="mr-table">
    <thead>
      <tr>
        <th>Year</th><th>Week</th><th>Lots Offered</th><th>Lots Sold</th>
        <th>Qty Offered (kg)</th><th>Qty Sold (kg)</th><th>Sold %</th><th>Avg Price (USD/kg)</th>
      </tr>
    </thead>
    <tbody id="mr-rows"></tbody>
  </table>
  <p class="muted">Update weekly by editing <code>assets/data/market-reports.json</code>.</p>
</section>