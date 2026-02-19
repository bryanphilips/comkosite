/* /assets/js/market-reports.js
   - Supports BOTH JSON formats:
     1) Array: [ {year,sale,...}, ... ]
     2) Object: { broker_sale:[...]} / { auction_sales:[...] }
   - KPI + Summary use Absorption (Qty Sold / Qty Offered)
   - Combrok table reduced columns (6)
   - Charts labeled (X/Y) + readable ticks (K/M/B)
   - Quantity Sold Trend replaces Lots Sold Trend
*/

document.addEventListener('DOMContentLoaded', () => {
  // -----------------------------
  // Elements (filters)
  // -----------------------------
  const yearEl    = document.getElementById('mr-year');
  const fromEl    = document.getElementById('mr-sale-from');
  const toEl      = document.getElementById('mr-sale-to');
  const allSales  = document.getElementById('mr-all-sales');
  const applyBtn  = document.getElementById('mr-apply');
  const clearBtn  = document.getElementById('mr-clear');
  const rangeLbl  = document.getElementById('mr-range-label');

  // KPIs
  const kpiCombrok = document.getElementById('mr-kpis-combrok');
  const kpiAuction = document.getElementById('mr-kpis-auction');

  // Summaries
  const summaryCombrok = document.getElementById('mr-summary-combrok');
  const summaryAuction = document.getElementById('mr-summary-auction');

  // Table rows (Combrok)
  const rowsCombrok = document.getElementById('mr-rows-combrok');

  // Charts (Combrok)
  const chartPriceTrendC  = document.getElementById('chart-price-trend-combrok');
  const chartQtyStackedC  = document.getElementById('chart-qty-stacked-combrok');
  const chartAbsorptionC  = document.getElementById('chart-sellthrough-combrok'); // keep id, label shows Absorption
  const chartQtyTrendC    = document.getElementById('chart-lots-lines-combrok');  // reused id, now Quantity Sold Trend

  // Charts (Auction)
  const chartPriceTrendA  = document.getElementById('chart-price-trend-auction');
  const chartQtyStackedA  = document.getElementById('chart-qty-stacked-auction');
  const chartAbsorptionA  = document.getElementById('chart-sellthrough-auction');
  const chartQtyTrendA    = document.getElementById('chart-lots-lines-auction');

  // Tabs
  const tabs = document.querySelectorAll('.mr-tab');
  const panelCombrok = document.getElementById('panel-combrok');
  const panelAuction = document.getElementById('panel-auction');

  function setActive(tabKey){
    tabs.forEach(btn => {
      const active = btn.dataset.mrTab === tabKey;
      btn.classList.toggle('active', active);
      btn.setAttribute('aria-selected', active ? 'true' : 'false');
      btn.tabIndex = active ? 0 : -1;
    });

    const isCombrok = tabKey === 'combrok';
    if (panelCombrok && panelAuction) {
      panelCombrok.hidden = !isCombrok;
      panelAuction.hidden = isCombrok;
      panelCombrok.classList.toggle('active', isCombrok);
      panelAuction.classList.toggle('active', !isCombrok);
    }
  }
  tabs.forEach(btn => btn.addEventListener('click', () => setActive(btn.dataset.mrTab)));
  setActive('combrok');

  // -----------------------------
  // Data sources
  // -----------------------------
  const DATA_SOURCES = {
    broker:  '/assets/data/broker.json',
    auction: '/assets/data/auction.json'
  };

  // -----------------------------
  // Helpers
  // -----------------------------
  const num = (v, fallback = 0) => {
    const x = parseFloat(String(v ?? '').replace(/,/g,''));
    return Number.isFinite(x) ? x : fallback;
  };

  const fmtInt = (n) => Math.round(Number(n) || 0).toLocaleString('en-KE');
  const fmtUsd = (n) => `${Number(n || 0).toFixed(2)}`;
  const fmtPct = (n) => `${Number(n || 0).toFixed(1)}%`;

  // Short format for big numbers (K/M/B)
  const fmtShort = (n) => {
    const x = Number(n) || 0;
    const abs = Math.abs(x);

    if (abs >= 1_000_000_000) return `${(x / 1_000_000_000).toFixed(2)}B`;
    if (abs >= 1_000_000)     return `${(x / 1_000_000).toFixed(2)}M`;
    if (abs >= 1_000)         return `${(x / 1_000).toFixed(0)}K`;
    return `${Math.round(x)}`;
  };

  const fmtKg = (n) => `${fmtInt(n)} kg`;
  const fmtShortKg = (n) => `${fmtShort(n)} kg`;

  async function fetchJson(url) {
    const res = await fetch(url, { cache: 'no-store' });
    if (!res.ok) throw new Error(`Failed to load ${url} (${res.status})`);
    return res.json();
  }

  function sortByYearSale(a,b){
    return (a.year - b.year) || (a.sale - b.sale);
  }

  function uniqueSorted(arr){
    return [...new Set(arr)].sort((a,b)=>a-b);
  }

  function setSelectOptions(select, values, firstLabel){
    if (!select) return;
    select.innerHTML = '';
    const opt0 = document.createElement('option');
    opt0.value = '';
    opt0.textContent = firstLabel;
    select.appendChild(opt0);

    values.forEach(v => {
      const o = document.createElement('option');
      o.value = String(v);
      o.textContent = String(v);
      select.appendChild(o);
    });
  }

  // Normalize row keys from JSON
  function normalizeRow(r) {
    return {
      year:          num(r.year, 0),
      sale:          num(r.sale, 0),
      lotsOffered:   num(r.offered_lots, 0),
      lotsSold:      num(r.sold_lots, 0),
      qtyOfferedKg:  num(r.qty_offered, 0),
      qtySoldKg:     num(r.qty_sold, 0),
      avgPriceUsdKg: num(r.avg_prc, 0),
    };
  }

  // ✅ Supports:
  // - Array JSON: [ ... ]
  // - Object JSON: { broker_sale:[...] } / { auction_sales:[...] }
  function unwrapArray(json, keys = []) {
    if (Array.isArray(json)) return json;
    for (const k of keys) {
      if (json && Array.isArray(json[k])) return json[k];
    }
    return [];
  }

  function getFilters(){
    const year = num(yearEl?.value, 0);
    const from = num(fromEl?.value, 0);
    const to   = num(toEl?.value, 0);
    const all  = !!allSales?.checked;
    return { year, from, to, all };
  }

  function labelFor(f){
    if (!f.year) return 'All years — All sales';
    if (f.all) return `Year ${f.year} — All sales`;

    if (!f.from && !f.to) return `Year ${f.year} — Select sale range`;
    const a = f.from || f.to;
    const b = f.to || f.from;
    const lo = Math.min(a,b);
    const hi = Math.max(a,b);
    return `Year ${f.year} — Sales ${String(lo).padStart(2,'0')} to ${String(hi).padStart(2,'0')}`;
  }

  function rowsForYear(data, year){
    return data.filter(r => !year || r.year === year).sort(sortByYearSale);
  }

  function clampRange(from, to){
    if (!from && !to) return null;
    const a = from || to;
    const b = to || from;
    return { lo: Math.min(a,b), hi: Math.max(a,b) };
  }

  // -----------------------------
  // Analytics (Absorption)
  // Absorption = Qty Sold / Qty Offered
  // -----------------------------
  function calcKpis(rows){
    if (!rows.length) {
      return {
        marker:'—',
        offeredQty:'—',
        soldQty:'—',
        absorption:'—',
        avg:'—',
        avgType:'—',
        deltaPrice:null,
        deltaSoldQty:null
      };
    }

    const last = rows[rows.length - 1];

    const offeredQty = rows.reduce((s,r)=> s + num(r.qtyOfferedKg), 0);
    const soldQty    = rows.reduce((s,r)=> s + num(r.qtySoldKg), 0);

    const absorption = offeredQty > 0 ? (soldQty / offeredQty) * 100 : 0;

    // Weighted average price by sold qty
    const soldWeight = rows.reduce((s,r)=> s + num(r.qtySoldKg), 0);
    const avg = soldWeight > 0
      ? rows.reduce((s,r)=> s + (num(r.avgPriceUsdKg) * num(r.qtySoldKg)), 0) / soldWeight
      : rows.reduce((s,r)=> s + num(r.avgPriceUsdKg), 0) / rows.length;

    let deltaPrice = null;
    let deltaSoldQty = null;
    if (rows.length >= 2) {
      const prev = rows[rows.length - 2];
      deltaPrice   = last.avgPriceUsdKg - prev.avgPriceUsdKg;
      deltaSoldQty = last.qtySoldKg - prev.qtySoldKg;
    }

    return {
      marker: `${last.year} Sale ${String(last.sale).padStart(2,'0')}`,
      offeredQty: fmtKg(offeredQty),
      soldQty: fmtKg(soldQty),
      absorption: fmtPct(absorption),
      avg: `${fmtUsd(avg)} USD/kg`,
      avgType: soldWeight > 0 ? 'Weighted by sold qty' : 'Simple average',
      deltaPrice,
      deltaSoldQty
    };
  }

  function kpiCard({label, value, hint}) {
    return `
      <div class="kpi-card">
        <div class="kpi-label">${label}</div>
        <div class="kpi-value">${value}</div>
        ${hint ? `<div class="kpi-hint">${hint}</div>` : ``}
      </div>
    `;
  }

  function renderKpis(el, rows){
    if (!el) return;
    const k = calcKpis(rows);

    const priceHint = (k.deltaPrice == null)
      ? k.avgType
      : `${k.avgType} • Δ vs prev: ${k.deltaPrice >= 0 ? '+' : ''}${fmtUsd(k.deltaPrice)} USD/kg`;

    const soldHint = (k.deltaSoldQty == null)
      ? `Absorption: ${k.absorption}`
      : `Absorption: ${k.absorption} • Δ sold qty: ${k.deltaSoldQty >= 0 ? '+' : ''}${fmtShort(k.deltaSoldQty)} kg`;

    el.innerHTML = [
      kpiCard({label:'Period', value: k.marker, hint:'Latest in view'}),
      kpiCard({label:'Qty Offered', value: k.offeredQty}),
      kpiCard({label:'Qty Sold', value: k.soldQty, hint: soldHint}),
      kpiCard({label:'Avg Price', value: k.avg, hint: priceHint}),
    ].join('');
  }

  function renderSummary(el, rows, filters){
    if (!el) return;
    const k = calcKpis(rows);

    el.innerHTML = `
      <div class="mr-summary">
        <div>
          <h3 class="mr-summary-title">Report Summary</h3>
          <p class="mr-summary-sub muted">${labelFor(filters)}</p>
        </div>
        <div class="mr-summary-badges">
          <span class="mr-badge">Qty: ${k.soldQty} / ${k.offeredQty}</span>
          <span class="mr-badge">Absorption: ${k.absorption}</span>
          <span class="mr-badge">Avg: ${k.avg}</span>
        </div>
      </div>
    `;
  }

  // ✅ Reduced columns table (Combrok)
  // Columns: Year | Sale | Qty Offered | Qty Sold | Absorption | Avg Price
  function renderTableCombrok(rows){
    if (!rowsCombrok) return;

    if (!rows.length) {
      rowsCombrok.innerHTML = `
        <tr>
          <td colspan="6" style="padding:14px;color:#64748b;">
            No data for the selected filters.
          </td>
        </tr>
      `;
      return;
    }

    rowsCombrok.innerHTML = rows.map((r, idx) => {
      const absorption = r.qtyOfferedKg > 0 ? (r.qtySoldKg / r.qtyOfferedKg) * 100 : 0;
      const delay = Math.min(idx * 0.05, 1.2);

      return `
        <tr style="animation-delay:${delay}s">
          <td>${r.year}</td>
          <td>${r.sale}</td>
          <td>${fmtInt(r.qtyOfferedKg)}</td>
          <td>${fmtInt(r.qtySoldKg)}</td>
          <td>${fmtPct(absorption)}</td>
          <td>${fmtUsd(r.avgPriceUsdKg)}</td>
        </tr>
      `;
    }).join('');
  }

  // -----------------------------
  // Charts (readable axes + labels)
  // -----------------------------
 function renderLineChart(el, points, opts = {}) {
  if (!el) return;

  const {
    xLabel = 'Sale No.',
    yLabel = '',
    yFmt = (v) => String(v),
    pointFmt = null,    // if null => no point labels
    ticks = 4,
  } = opts;

  el.innerHTML = '';
  if (!points.length) {
    el.innerHTML = `<div class="chart-empty">No chart data</div>`;
    return;
  }

  const W = 680, H = 280;
  const PAD_L = 64;
  const PAD_R = 18;
  const PAD_T = 18;
  const PAD_B = 54; // a bit more space for x-ticks

  const xs = points.map(p => p.x);
  const ys = points.map(p => p.y);

  const minX = Math.min(...xs), maxX = Math.max(...xs);
  let minY = Math.min(...ys), maxY = Math.max(...ys);

  if (minY === maxY) { minY -= 1; maxY += 1; }
  const yPad = (maxY - minY) * 0.12;
  minY -= yPad; maxY += yPad;

  const xScale = (x) =>
    PAD_L + ((x - minX) / (maxX - minX || 1)) * (W - PAD_L - PAD_R);

  const yScale = (y) =>
    (H - PAD_B) - ((y - minY) / (maxY - minY || 1)) * (H - PAD_T - PAD_B);

  const d = points
    .map((p,i) => `${i===0?'M':'L'} ${xScale(p.x).toFixed(1)} ${yScale(p.y).toFixed(1)}`)
    .join(' ');

  // Y ticks
  const tickCount = Math.max(2, ticks);
  const yTickVals = Array.from({ length: tickCount }, (_, i) => {
    const t = i / (tickCount - 1);
    return minY + t * (maxY - minY);
  });

  // ✅ X ticks (Sale numbers)
  // Use each point’s x (sale no) as a tick, but if too many points, skip some
  const maxXTicks = 10;
  const step = Math.ceil(points.length / maxXTicks);
  const xTickPoints = points.filter((_, i) => i % step === 0);

  const svg = document.createElementNS('http://www.w3.org/2000/svg','svg');
  svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
  svg.setAttribute('width','100%');
  svg.setAttribute('height','100%');

  svg.innerHTML = `
    <!-- Axes -->
    <path d="M ${PAD_L} ${H-PAD_B} L ${W-PAD_R} ${H-PAD_B}" stroke="rgba(148,163,184,.55)" stroke-width="1"/>
    <path d="M ${PAD_L} ${PAD_T} L ${PAD_L} ${H-PAD_B}" stroke="rgba(148,163,184,.55)" stroke-width="1"/>

    <!-- Y grid + ticks -->
    ${yTickVals.map(v => {
      const y = yScale(v);
      return `
        <path d="M ${PAD_L} ${y} L ${W-PAD_R} ${y}" stroke="rgba(148,163,184,.18)" stroke-width="1"/>
        <text x="${PAD_L-10}" y="${y+4}" text-anchor="end"
              fill="rgba(100,116,139,.92)" font-size="11" font-weight="800">${yFmt(v)}</text>
      `;
    }).join('')}

    <!-- ✅ X ticks (Sale numbers) -->
    ${xTickPoints.map(p => {
      const x = xScale(p.x);
      return `
        <path d="M ${x} ${H-PAD_B} L ${x} ${H-PAD_B+5}" stroke="rgba(148,163,184,.55)" stroke-width="1"/>
        <text x="${x}" y="${H-PAD_B+20}" text-anchor="middle"
              fill="rgba(100,116,139,.95)" font-size="11" font-weight="900">${p.x}</text>
      `;
    }).join('')}

    <!-- Axis labels -->
    <text x="${(PAD_L + (W-PAD_R))/2}" y="${H-12}" text-anchor="middle"
          fill="rgba(30,41,59,.95)" font-size="12" font-weight="900">${xLabel}</text>

    <text x="16" y="${(PAD_T + (H-PAD_B))/2}" text-anchor="middle"
          fill="rgba(30,41,59,.95)" font-size="12" font-weight="900"
          transform="rotate(-90 16 ${(PAD_T + (H-PAD_B))/2})">${yLabel}</text>

    <!-- Line -->
    <path d="${d}" fill="none" stroke="currentColor" stroke-width="3"/>

    <!-- Points + optional labels -->
    ${points.map(p => {
      const cx = xScale(p.x);
      const cy = yScale(p.y);
      const label = pointFmt ? pointFmt(p.y) : '';
      return `
        <circle cx="${cx}" cy="${cy}" r="4.2" fill="currentColor"></circle>
        ${pointFmt ? `
          <text x="${cx}" y="${cy-10}" text-anchor="middle"
                fill="rgba(15,23,42,.85)" font-size="11" font-weight="900">${label}</text>
        ` : ``}
        <title>Sale ${p.x}: ${yFmt(p.y)}</title>
      `;
    }).join('')}
  `;

  el.style.color = '#218CCF';
  el.appendChild(svg);
}

  function niceTicks(min, max, count = 4) {
    if (min === max) return [min];
    const step = (max - min) / count;
    return Array.from({ length: count + 1 }, (_, i) => min + step * i);
  }

  function renderStackedBars(el, rows, { xLabel='Sale No.', yLabel='Quantity (kg)' } = {}) {
    if (!el) return;
    el.innerHTML = '';
    if (!rows.length) { el.innerHTML = `<div class="chart-empty">No chart data</div>`; return; }

    const W = 720, H = 300;
    const PAD_L = 64, PAD_R = 20, PAD_T = 36, PAD_B = 56;

    const max = Math.max(...rows.map(r => r.offered), 1);
    const gap = 12;
    const barW = Math.max(12, ((W - PAD_L - PAD_R) / rows.length) - gap);

    const yScale = (v) => (H - PAD_B) - ((v / max) * (H - PAD_T - PAD_B));
    const yTicks = niceTicks(0, max, 4);

    const svg = document.createElementNS('http://www.w3.org/2000/svg','svg');
    svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
    svg.setAttribute('width','100%');
    svg.setAttribute('height','100%');

    el.style.color = '#0A4E73';

    svg.innerHTML = `
      <!-- Axes -->
      <path d="M ${PAD_L} ${H-PAD_B} L ${W-PAD_R} ${H-PAD_B}" stroke="rgba(148,163,184,.55)" stroke-width="1"/>
      <path d="M ${PAD_L} ${PAD_T} L ${PAD_L} ${H-PAD_B}" stroke="rgba(148,163,184,.55)" stroke-width="1"/>

      <!-- Y ticks -->
      ${yTicks.map(t => {
        const y = yScale(t);
        return `
          <path d="M ${PAD_L} ${y} L ${W-PAD_R} ${y}" stroke="rgba(148,163,184,.18)" stroke-width="1"/>
          <text x="${PAD_L-10}" y="${y+4}" text-anchor="end"
                fill="rgba(100,116,139,.92)" font-size="11" font-weight="800">${fmtShort(t)}</text>
        `;
      }).join('')}

      <!-- Bars -->
      ${rows.map((r,i) => {
        const x = PAD_L + i * (barW + gap);

        const yOff = yScale(r.offered);
        const ySold = yScale(r.sold);

        const hOff = (H - PAD_B) - yOff;
        const hSold = (H - PAD_B) - ySold;

        return `
          <rect x="${x.toFixed(1)}" y="${yOff.toFixed(1)}" width="${barW.toFixed(1)}" height="${hOff.toFixed(1)}"
                rx="10" fill="currentColor" opacity="0.18"></rect>
          <rect x="${x.toFixed(1)}" y="${ySold.toFixed(1)}" width="${barW.toFixed(1)}" height="${hSold.toFixed(1)}"
                rx="10" fill="currentColor" opacity="0.55"></rect>

          <text x="${(x + barW/2).toFixed(1)}" y="${H-PAD_B+18}" text-anchor="middle"
                fill="rgba(100,116,139,.95)" font-size="11" font-weight="900">${r.x}</text>

          <title>Sale ${r.x}
Offered: ${fmtInt(r.offered)} kg
Sold: ${fmtInt(r.sold)} kg</title>
        `;
      }).join('')}

      <!-- Legend -->
      <g transform="translate(${PAD_L}, ${PAD_T-18})">
        <rect x="0" y="0" width="10" height="10" rx="2" fill="currentColor" opacity="0.18"></rect>
        <text x="14" y="9" font-size="11" font-weight="900" fill="rgba(30,41,59,.90)">Offered</text>

        <rect x="86" y="0" width="10" height="10" rx="2" fill="currentColor" opacity="0.55"></rect>
        <text x="100" y="9" font-size="11" font-weight="900" fill="rgba(30,41,59,.90)">Sold</text>
      </g>

      <!-- Axis labels -->
      <text x="${(PAD_L + (W-PAD_R))/2}" y="${H-16}" text-anchor="middle"
            fill="rgba(30,41,59,.95)" font-size="12" font-weight="900">${xLabel}</text>

      <text x="16" y="${(PAD_T + (H-PAD_B))/2}" text-anchor="middle"
            fill="rgba(30,41,59,.95)" font-size="12" font-weight="900"
            transform="rotate(-90 16 ${(PAD_T + (H-PAD_B))/2})">${yLabel}</text>
    `;

    el.appendChild(svg);
  }

  // Donut = Absorption
  function renderDonut(el, sold, offered) {
    if (!el) return;
    el.innerHTML = '';

    const total = Math.max(offered, 0);
    const s = Math.max(sold, 0);
    const pct = total > 0 ? s / total : 0;

    const size = 260, r = 84, cx = size/2, cy = size/2;
    const circ = 2 * Math.PI * r;

    el.style.color = '#22c55e';

    el.innerHTML = `
      <svg viewBox="0 0 ${size} ${size}" width="100%" height="100%">
        <circle cx="${cx}" cy="${cy}" r="${r}" fill="none" stroke="rgba(148,163,184,.25)" stroke-width="18"></circle>
        <circle cx="${cx}" cy="${cy}" r="${r}"
                fill="none" stroke="currentColor" stroke-width="18"
                stroke-linecap="round"
                transform="rotate(-90 ${cx} ${cy})"
                stroke-dasharray="${circ}" stroke-dashoffset="${circ * (1 - pct)}"></circle>

        <text x="${cx}" y="${cy - 6}" text-anchor="middle" font-size="30" font-weight="950" fill="#0f172a">
          ${(pct * 100).toFixed(1)}%
        </text>
        <text x="${cx}" y="${cy + 18}" text-anchor="middle" font-size="12" font-weight="900" fill="#64748b">
          Absorption
        </text>
        
      </svg>
    `;
  }

  // -----------------------------
  // State
  // -----------------------------
  let DATA_BROKER = [];
  let DATA_AUCTION = [];

  // -----------------------------
  // Main apply
  // -----------------------------
  function applyFilters(){
    const f = getFilters();
    if (rangeLbl) rangeLbl.textContent = labelFor(f);

    const range = f.all ? null : clampRange(f.from, f.to);

    // -------- COMBROK --------
    const brokerYear = rowsForYear(DATA_BROKER, f.year);

    const brokerScope = (!range)
      ? brokerYear
      : brokerYear.filter(r => r.sale >= range.lo && r.sale <= range.hi);

    const brokerUsed = brokerScope.length ? brokerScope : brokerYear;

    renderKpis(kpiCombrok, brokerUsed);
    renderSummary(summaryCombrok, brokerUsed, f);
    renderTableCombrok(brokerYear);

    // Avg Price trend
    renderLineChart(
      chartPriceTrendC,
      brokerYear.map(r => ({ x: r.sale, y: num(r.avgPriceUsdKg) })),
      {
        xLabel: 'Sale No.',
        yLabel: 'Avg Price (USD/kg)',
        yFmt: (v) => Number(v).toFixed(2),
        pointFmt: (v) => Number(v).toFixed(2),
        ticks: 4
      }
    );

    // Qty offered vs sold
    renderStackedBars(
      chartQtyStackedC,
      brokerYear.map(r => ({ x: r.sale, offered: num(r.qtyOfferedKg), sold: num(r.qtySoldKg) })),
      { xLabel: 'Sale No.', yLabel: 'Quantity (kg)' }
    );

    // Absorption donut + Quantity Sold Trend
    const brokerLast = brokerUsed[brokerUsed.length - 1] || null;
    if (brokerLast) {
      renderDonut(chartAbsorptionC, brokerLast.qtySoldKg, brokerLast.qtyOfferedKg);

      renderLineChart(
        chartQtyTrendC,
        brokerYear.map(r => ({ x: r.sale, y: num(r.qtySoldKg) })),
        {
          xLabel: 'Sale No.',
          yLabel: 'Qty Sold (kg)',
          yFmt: (v) => fmtShort(v),
          pointFmt: (v) => fmtShort(v),
          ticks: 4
        }
      );
    } else {
      if (chartAbsorptionC) chartAbsorptionC.innerHTML = `<div class="chart-empty">No chart data</div>`;
      if (chartQtyTrendC) chartQtyTrendC.innerHTML = `<div class="chart-empty">No chart data</div>`;
    }

    // -------- AUCTION --------
    const auctionYear = rowsForYear(DATA_AUCTION, f.year);

    const auctionScope = (!range)
      ? auctionYear
      : auctionYear.filter(r => r.sale >= range.lo && r.sale <= range.hi);

    const auctionUsed = auctionScope.length ? auctionScope : auctionYear;

    renderKpis(kpiAuction, auctionUsed);
    renderSummary(summaryAuction, auctionUsed, f);

    renderLineChart(
      chartPriceTrendA,
      auctionYear.map(r => ({ x: r.sale, y: num(r.avgPriceUsdKg) })),
      {
        xLabel: 'Sale No.',
        yLabel: 'Avg Price (USD/kg)',
        yFmt: (v) => Number(v).toFixed(2),
        pointFmt: (v) => Number(v).toFixed(2),
        ticks: 4
      }
    );

    renderStackedBars(
      chartQtyStackedA,
      auctionYear.map(r => ({ x: r.sale, offered: num(r.qtyOfferedKg), sold: num(r.qtySoldKg) })),
      { xLabel: 'Sale No.', yLabel: 'Quantity (kg)' }
    );

    const auctionLast = auctionUsed[auctionUsed.length - 1] || null;
    if (auctionLast) {
      renderDonut(chartAbsorptionA, auctionLast.qtySoldKg, auctionLast.qtyOfferedKg);

      renderLineChart(
        chartQtyTrendA,
        auctionYear.map(r => ({ x: r.sale, y: num(r.qtySoldKg) })),
        {
          xLabel: 'Sale No.',
          yLabel: 'Qty Sold (kg)',
          yFmt: (v) => fmtShort(v),
          pointFmt: (v) => fmtShort(v),
          ticks: 4
        }
      );
    } else {
      if (chartAbsorptionA) chartAbsorptionA.innerHTML = `<div class="chart-empty">No chart data</div>`;
      if (chartQtyTrendA) chartQtyTrendA.innerHTML = `<div class="chart-empty">No chart data</div>`;
    }
  }

  function setDefaultLatest(){
    const years = uniqueSorted([
      ...DATA_BROKER.map(r => r.year),
      ...DATA_AUCTION.map(r => r.year),
    ]).filter(Boolean);

    const latestYear = years[years.length - 1] || 0;

    const brokerYear = rowsForYear(DATA_BROKER, latestYear);
    const auctionYear = rowsForYear(DATA_AUCTION, latestYear);

    const sales = uniqueSorted([
      ...brokerYear.map(r => r.sale),
      ...auctionYear.map(r => r.sale),
    ]).filter(Boolean);

    const latestSale = sales.length ? sales[sales.length - 1] : 0;

    setSelectOptions(yearEl, years, 'All');
    if (yearEl) yearEl.value = latestYear ? String(latestYear) : '';

    setSelectOptions(fromEl, sales, 'From');
    setSelectOptions(toEl, sales, 'To');

    if (fromEl) fromEl.value = '';
    if (toEl) toEl.value = '';

    if (allSales) allSales.checked = true;

    if (rangeLbl) {
      rangeLbl.textContent = `Year ${latestYear} — All sales (Latest: Sale ${String(latestSale).padStart(2,'0')})`;
    }
  }

  function rebuildSalesForSelectedYear(){
    const year = num(yearEl?.value, 0);
    const brokerYear = rowsForYear(DATA_BROKER, year);
    const auctionYear = rowsForYear(DATA_AUCTION, year);

    const sales = uniqueSorted([
      ...brokerYear.map(r => r.sale),
      ...auctionYear.map(r => r.sale),
    ]).filter(Boolean);

    setSelectOptions(fromEl, sales, 'From');
    setSelectOptions(toEl, sales, 'To');

    if (fromEl) fromEl.value = '';
    if (toEl) toEl.value = '';
  }

  // -----------------------------
  // Load + init
  // -----------------------------
  (async () => {
    try {
      const [brokerJson, auctionJson] = await Promise.all([
        fetchJson(DATA_SOURCES.broker),
        fetchJson(DATA_SOURCES.auction),
      ]);

      // ✅ Supports both new array JSON and older object formats
      DATA_BROKER = unwrapArray(brokerJson, ['broker_sale','broker_sales','data'])
        .map(normalizeRow)
        .filter(r => r.year && r.sale)
        .sort(sortByYearSale);

      DATA_AUCTION = unwrapArray(auctionJson, ['auction_sales','auction_sale','data'])
        .map(normalizeRow)
        .filter(r => r.year && r.sale)
        .sort(sortByYearSale);

      // Debug (open DevTools > Console)
      console.log('[MarketReports] Broker rows:', DATA_BROKER.length, DATA_BROKER);
      console.log('[MarketReports] Auction rows:', DATA_AUCTION.length, DATA_AUCTION);

      setDefaultLatest();
      applyFilters();
    } catch (e) {
      console.error(e);

      if (summaryCombrok) {
        summaryCombrok.innerHTML = `
          <div style="padding:14px;color:#b91c1c;">
            Failed to load report data. Check:
            <code>${DATA_SOURCES.broker}</code> and <code>${DATA_SOURCES.auction}</code>
          </div>
        `;
      }

      if (rowsCombrok) {
        rowsCombrok.innerHTML = `
          <tr><td colspan="6" style="padding:14px;color:#b91c1c;">
            Failed to load broker data.
          </td></tr>
        `;
      }

      if (summaryAuction) {
        summaryAuction.innerHTML = `
          <div style="padding:14px;color:#b91c1c;">
            Failed to load auction data. Check:
            <code>${DATA_SOURCES.auction}</code>
          </div>
        `;
      }
    }
  })();

  // -----------------------------
  // Events
  // -----------------------------
  if (yearEl) {
    yearEl.addEventListener('change', () => {
      rebuildSalesForSelectedYear();
      if (allSales) allSales.checked = true;
      applyFilters();
    });
  }

  // If user picks a range, auto-uncheck "All"
  const onRangePick = () => {
    if (allSales) allSales.checked = false;
    applyFilters();
  };
  if (fromEl) fromEl.addEventListener('change', onRangePick);
  if (toEl) toEl.addEventListener('change', onRangePick);

  if (allSales) {
    allSales.addEventListener('change', () => {
      if (allSales.checked) {
        if (fromEl) fromEl.value = '';
        if (toEl) toEl.value = '';
      }
      applyFilters();
    });
  }

  if (applyBtn) applyBtn.addEventListener('click', applyFilters);

  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      setDefaultLatest();
      applyFilters();
    });
  }
});