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
  const chartPriceTrendC = document.getElementById('chart-price-trend-combrok');
  const chartQtyStackedC = document.getElementById('chart-qty-stacked-combrok');
  const chartSellthroughC= document.getElementById('chart-sellthrough-combrok');
  const chartLotsLinesC  = document.getElementById('chart-lots-lines-combrok');

  // Charts (Auction)
  const chartPriceTrendA = document.getElementById('chart-price-trend-auction');
  const chartQtyStackedA = document.getElementById('chart-qty-stacked-auction');
  const chartSellthroughA= document.getElementById('chart-sellthrough-auction');
  const chartLotsLinesA  = document.getElementById('chart-lots-lines-auction');

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
    }
  }
  tabs.forEach(btn => btn.addEventListener('click', () => setActive(btn.dataset.mrTab)));
  setActive('combrok');

  // -----------------------------
  // Data sources
  // -----------------------------
  const DATA_SOURCES = {
    broker: '/assets/data/broker.json',
    auction: '/assets/data/auction.json'
  };

  // -----------------------------
  // Helpers
  // -----------------------------
  const num = (v, fallback = 0) => {
    const x = parseFloat(String(v ?? '').replace(/,/g,''));
    return Number.isFinite(x) ? x : fallback;
  };
  const fmtInt = (n) => Math.round(n).toLocaleString('en-KE');
  const fmtKg  = (n) => `${fmtInt(n)} kg`;
  const fmtUsd = (n) => `${Number(n).toFixed(2)}`;
  const fmtPct = (n) => `${Number(n).toFixed(1)}%`;

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

  function normalizeRow(r) {
    return {
      year: num(r.year, 0),
      sale: num(r.sale, 0),
      lotsOffered: num(r.offered_lots, 0),
      lotsSold: num(r.sold_lots, 0),
      qtyOfferedKg: num(r.qty_offered, 0),
      qtySoldKg: num(r.qty_sold, 0),
      avgPriceUsdKg: num(r.avg_prc, 0),
    };
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

  // -----------------------------
  // Analytics
  // -----------------------------
  function calcKpis(rows){
    if (!rows.length) {
      return {
        marker:'—',
        offeredLots:'—',
        soldLots:'—',
        offeredQty:'—',
        soldQty:'—',
        sellThrough:'—',
        avg:'—',
        avgType:'—',
        deltaPrice:null,
        deltaSoldQty:null
      };
    }

    const last = rows[rows.length - 1];

    const offeredQty = rows.reduce((s,r)=> s + num(r.qtyOfferedKg), 0);
    const soldQty    = rows.reduce((s,r)=> s + num(r.qtySoldKg), 0);
    const offeredLots= rows.reduce((s,r)=> s + num(r.lotsOffered), 0);
    const soldLots   = rows.reduce((s,r)=> s + num(r.lotsSold), 0);

    const sellThrough = offeredQty > 0 ? (soldQty / offeredQty) * 100 : 0;

    const soldWeight = rows.reduce((s,r)=> s + num(r.qtySoldKg), 0);
    const avg = soldWeight > 0
      ? rows.reduce((s,r)=> s + (num(r.avgPriceUsdKg) * num(r.qtySoldKg)), 0) / soldWeight
      : rows.reduce((s,r)=> s + num(r.avgPriceUsdKg), 0) / rows.length;

    let deltaPrice = null;
    let deltaSoldQty = null;
    if (rows.length >= 2) {
      const prev = rows[rows.length - 2];
      deltaPrice = last.avgPriceUsdKg - prev.avgPriceUsdKg;
      deltaSoldQty = last.qtySoldKg - prev.qtySoldKg;
    }

    return {
      marker: `Y${last.year} Sale ${String(last.sale).padStart(2,'0')}`,
      offeredLots: fmtInt(offeredLots),
      soldLots: fmtInt(soldLots),
      offeredQty: fmtKg(offeredQty),
      soldQty: fmtKg(soldQty),
      sellThrough: fmtPct(sellThrough),
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
      ? `Sell-through: ${k.sellThrough}`
      : `Sell-through: ${k.sellThrough} • Δ sold qty: ${k.deltaSoldQty >= 0 ? '+' : ''}${fmtInt(k.deltaSoldQty)} kg`;

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
          <span class="mr-badge">Lots: ${k.soldLots} / ${k.offeredLots}</span>
          <span class="mr-badge">Qty: ${k.soldQty} / ${k.offeredQty}</span>
          <span class="mr-badge">Sell-through: ${k.sellThrough}</span>
          <span class="mr-badge">Avg: ${k.avg}</span>
        </div>
      </div>
    `;
  }

  function renderTableCombrok(rows){
    if (!rowsCombrok) return;

    if (!rows.length) {
      rowsCombrok.innerHTML = `
        <tr>
          <td colspan="8" style="padding:14px;color:#64748b;">
            No data for the selected filters.
          </td>
        </tr>
      `;
      return;
    }

    rowsCombrok.innerHTML = rows.map((r, idx) => {
      const soldPct = r.qtyOfferedKg > 0 ? (r.qtySoldKg / r.qtyOfferedKg) * 100 : 0;
      const delay = Math.min(idx * 0.05, 1.2);

      return `
        <tr style="animation-delay:${delay}s">
          <td>${r.year}</td>
          <td>${r.sale}</td>
          <td>${fmtInt(r.lotsOffered)}</td>
          <td>${fmtInt(r.lotsSold)}</td>
          <td>${fmtInt(r.qtyOfferedKg)}</td>
          <td>${fmtInt(r.qtySoldKg)}</td>
          <td>${fmtPct(soldPct)}</td>
          <td>${fmtUsd(r.avgPriceUsdKg)}</td>
        </tr>
      `;
    }).join('');
  }

  // -----------------------------
  // Charts
  // -----------------------------
  function renderLineChart(el, points, {labelSuffix=''} = {}) {
    if (!el) return;
    el.innerHTML = '';
    if (!points.length) { el.innerHTML = `<div class="chart-empty">No chart data</div>`; return; }

    const W = 640, H = 260, PAD = 30;
    const xs = points.map(p => p.x);
    const ys = points.map(p => p.y);

    const minX = Math.min(...xs), maxX = Math.max(...xs);
    const minY = Math.min(...ys), maxY = Math.max(...ys);

    const xScale = (x) => PAD + ((x - minX) / (maxX - minX || 1)) * (W - PAD*2);
    const yScale = (y) => (H - PAD) - ((y - minY) / (maxY - minY || 1)) * (H - PAD*2);

    const d = points.map((p,i) => `${i===0?'M':'L'} ${xScale(p.x).toFixed(1)} ${yScale(p.y).toFixed(1)}`).join(' ');

    const svg = document.createElementNS('http://www.w3.org/2000/svg','svg');
    svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
    svg.setAttribute('width','100%');
    svg.setAttribute('height','100%');

    svg.innerHTML = `
      <path class="mr-axis" d="M ${PAD} ${H-PAD} L ${W-PAD} ${H-PAD}" stroke="rgba(148,163,184,.55)" stroke-width="1"/>
      <path class="mr-axis" d="M ${PAD} ${PAD} L ${PAD} ${H-PAD}" stroke="rgba(148,163,184,.55)" stroke-width="1"/>
      <path class="mr-line-a" d="${d}" fill="none" stroke="currentColor" stroke-width="3"/>
      <text class="mr-axis-label" x="${PAD}" y="${PAD-10}" fill="rgba(100,116,139,.95)" font-size="12" font-weight="700">
        ${minY.toFixed(2)}${labelSuffix} → ${maxY.toFixed(2)}${labelSuffix}
      </text>
    `;

    el.style.color = '#218CCF';
    el.appendChild(svg);
  }

  function renderStackedBars(el, rows) {
    if (!el) return;
    el.innerHTML = '';
    if (!rows.length) { el.innerHTML = `<div class="chart-empty">No chart data</div>`; return; }

    const W = 640, H = 260, PAD = 26;
    const max = Math.max(...rows.map(r => r.offered), 1);
    const gap = 10;
    const barW = Math.max(10, ((W - PAD*2) / rows.length) - gap);

    const svg = document.createElementNS('http://www.w3.org/2000/svg','svg');
    svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
    svg.setAttribute('width','100%');
    svg.setAttribute('height','100%');

    el.style.color = '#0A4E73';

    svg.innerHTML = `
      <path class="mr-axis" d="M ${PAD} ${H-PAD} L ${W-PAD} ${H-PAD}" stroke="rgba(148,163,184,.55)" stroke-width="1"/>
      <path class="mr-axis" d="M ${PAD} ${PAD} L ${PAD} ${H-PAD}" stroke="rgba(148,163,184,.55)" stroke-width="1"/>
      ${rows.map((r,i) => {
        const x = PAD + i * (barW + gap);
        const offeredH = ((r.offered / max) * (H - PAD*2));
        const soldH = ((r.sold / max) * (H - PAD*2));
        const yOff = (H - PAD) - offeredH;
        const ySold = (H - PAD) - soldH;

        return `
          <rect x="${x.toFixed(1)}" y="${yOff.toFixed(1)}" width="${barW.toFixed(1)}" height="${offeredH.toFixed(1)}"
                rx="8" fill="currentColor" opacity="0.18"></rect>
          <rect x="${x.toFixed(1)}" y="${ySold.toFixed(1)}" width="${barW.toFixed(1)}" height="${soldH.toFixed(1)}"
                rx="8" fill="currentColor" opacity="0.55"></rect>
        `;
      }).join('')}
    `;
    el.appendChild(svg);
  }

  function renderDonut(el, sold, offered) {
    if (!el) return;
    el.innerHTML = '';

    const total = Math.max(offered, 0);
    const s = Math.max(sold, 0);
    const pct = total > 0 ? s / total : 0;

    const size = 240, r = 78, cx = size/2, cy = size/2;
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

        <text x="${cx}" y="${cy-4}" text-anchor="middle" font-size="28" font-weight="950" fill="#0f172a">
          ${(pct*100).toFixed(1)}%
        </text>
        <text x="${cx}" y="${cy+22}" text-anchor="middle" font-size="12" font-weight="800" fill="#64748b">
          Sell-through
        </text>
      </svg>
    `;
  }

  // -----------------------------
  // State
  // -----------------------------
  let DATA_BROKER = [];
  let DATA_AUCTION = [];

  function rowsForYear(data, year){
    return data.filter(r => !year || r.year === year).sort(sortByYearSale);
  }

  function clampRange(from, to){
    if (!from && !to) return null;
    const a = from || to;
    const b = to || from;
    return { lo: Math.min(a,b), hi: Math.max(a,b) };
  }

  function applyFilters(){
    const f = getFilters();
    if (rangeLbl) rangeLbl.textContent = labelFor(f);

    const range = f.all ? null : clampRange(f.from, f.to);

    // ---------- COMBROK ----------
    const brokerYear = rowsForYear(DATA_BROKER, f.year);

    const brokerScope = (!range)
      ? brokerYear
      : brokerYear.filter(r => r.sale >= range.lo && r.sale <= range.hi);

    const brokerUsed = brokerScope.length ? brokerScope : brokerYear;

    renderKpis(kpiCombrok, brokerUsed);
    renderSummary(summaryCombrok, brokerUsed, f);
    renderTableCombrok(brokerYear);

    renderLineChart(chartPriceTrendC, brokerYear.map(r => ({ x: r.sale, y: num(r.avgPriceUsdKg) })), { labelSuffix:' USD/kg' });
    renderStackedBars(chartQtyStackedC, brokerYear.map(r => ({ offered: num(r.qtyOfferedKg), sold: num(r.qtySoldKg) })));

    const brokerLast = (brokerUsed[brokerUsed.length - 1]) || null;
    if (brokerLast) {
      renderDonut(chartSellthroughC, brokerLast.qtySoldKg, brokerLast.qtyOfferedKg);
      renderLineChart(chartLotsLinesC, brokerYear.map(r => ({ x: r.sale, y: num(r.lotsSold) })));
    }

    // ---------- AUCTION ----------
    const auctionYear = rowsForYear(DATA_AUCTION, f.year);

    const auctionScope = (!range)
      ? auctionYear
      : auctionYear.filter(r => r.sale >= range.lo && r.sale <= range.hi);

    const auctionUsed = auctionScope.length ? auctionScope : auctionYear;

    renderKpis(kpiAuction, auctionUsed);
    renderSummary(summaryAuction, auctionUsed, f);

    renderLineChart(chartPriceTrendA, auctionYear.map(r => ({ x: r.sale, y: num(r.avgPriceUsdKg) })), { labelSuffix:' USD/kg' });
    renderStackedBars(chartQtyStackedA, auctionYear.map(r => ({ offered: num(r.qtyOfferedKg), sold: num(r.qtySoldKg) })));

    const auctionLast = (auctionUsed[auctionUsed.length - 1]) || null;
    if (auctionLast) {
      renderDonut(chartSellthroughA, auctionLast.qtySoldKg, auctionLast.qtyOfferedKg);
      renderLineChart(chartLotsLinesA, auctionYear.map(r => ({ x: r.sale, y: num(r.lotsSold) })));
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

    if (fromEl) fromEl.value = ''; // default is ALL sales (checkbox checked)
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

    // keep checkbox as ALL by default when year changes
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

      DATA_BROKER = (brokerJson?.broker_sale || [])
        .map(normalizeRow)
        .filter(r => r.year && r.sale)
        .sort(sortByYearSale);

      DATA_AUCTION = (auctionJson?.auction_sales || [])
        .map(normalizeRow)
        .filter(r => r.year && r.sale)
        .sort(sortByYearSale);

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
          <tr><td colspan="8" style="padding:14px;color:#b91c1c;">
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
      // If they turn ON all sales, clear range selects
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