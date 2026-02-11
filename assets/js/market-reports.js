document.addEventListener('DOMContentLoaded', () => {
  // -----------------------------
  // Elements
  // -----------------------------
  const yearEl   = document.getElementById('mr-year');
  const fromEl   = document.getElementById('mr-week-from');
  const toEl     = document.getElementById('mr-week-to');
  const allWeeks = document.getElementById('mr-all-weeks');
  const applyBtn = document.getElementById('mr-apply');
  const clearBtn = document.getElementById('mr-clear');

  const kpiCombrok = document.getElementById('mr-kpis-combrok');
  const kpiAuction = document.getElementById('mr-kpis-auction');

  const summaryEl = document.getElementById('mr-summary');
  const rowsEl    = document.getElementById('mr-rows');

  const chartPriceTrend = document.getElementById('chart-price-trend');
  const chartQtyStacked = document.getElementById('chart-qty-stacked');
  const chartSellthrough= document.getElementById('chart-sellthrough');
  const chartLotsLines  = document.getElementById('chart-lots-lines');

  const tableWrap = rowsEl ? rowsEl.closest('.table-wrap') : null;
  const tabs = document.querySelectorAll('.mr-tab');
  const panelCombrok = document.getElementById('panel-combrok');
  const panelAuction = document.getElementById('panel-auction');

  const dlCombrok = document.getElementById('mr-download-combrok');
  const dlAuction = document.getElementById('mr-download-auction');

  function setActive(tabKey){
    // Tabs
    tabs.forEach(btn => {
      const active = btn.dataset.mrTab === tabKey;
      btn.classList.toggle('active', active);
      btn.setAttribute('aria-selected', active ? 'true' : 'false');
      btn.tabIndex = active ? 0 : -1;
    });

    // Panels
    const isCombrok = tabKey === 'combrok';
    panelCombrok.classList.toggle('active', isCombrok);
    panelAuction.classList.toggle('active', !isCombrok);
    panelCombrok.hidden = !isCombrok;
    panelAuction.hidden = isCombrok;

    // Downloads (above tabs)
    if (dlCombrok && dlAuction) {
      dlCombrok.style.display = isCombrok ? '' : 'none';
      dlAuction.style.display = isCombrok ? 'none' : '';
    }
  }

  // Click handlers
  tabs.forEach(btn => {
    btn.addEventListener('click', () => setActive(btn.dataset.mrTab));
  });

  // Keyboard nav (left/right)
  const tabList = document.querySelector('.mr-tabs');
  if (tabList) {
    tabList.addEventListener('keydown', (e) => {
      const keys = ['ArrowLeft','ArrowRight'];
      if (!keys.includes(e.key)) return;

      const currentIndex = [...tabs].findIndex(t => t.classList.contains('active'));
      let nextIndex = currentIndex;

      if (e.key === 'ArrowRight') nextIndex = (currentIndex + 1) % tabs.length;
      if (e.key === 'ArrowLeft') nextIndex = (currentIndex - 1 + tabs.length) % tabs.length;

      tabs[nextIndex].focus();
      setActive(tabs[nextIndex].dataset.mrTab);
    });
  }

  // default
  setActive('combrok');


  // -----------------------------
  // Data sources (EDIT PATHS IF NEEDED)
  // -----------------------------
  const DATA_SOURCES = {
    auction: '/assets/data/auction.json',
    broker:  '/assets/data/broker.json'
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

  function weekOptions(select, labelFirst) {
    const opt0 = document.createElement('option');
    opt0.value = '';
    opt0.textContent = labelFirst;
    select.innerHTML = '';
    select.appendChild(opt0);

    for (let w = 1; w <= 53; w++) {
      const o = document.createElement('option');
      o.value = String(w);
      o.textContent = String(w);
      select.appendChild(o);
    }
  }

  function getRange() {
    const year = num(yearEl?.value, 0);
    const all = !!allWeeks?.checked;
    const from = all ? null : num(fromEl?.value, 0);
    const to   = all ? null : num(toEl?.value, 0);
    return { year, all, from, to };
  }

  function rangeLabel({year, all, from, to}) {
    if (!year) return 'All available data';
    if (all) return `Year ${year} — All weeks`;
    if (!from || !to) return `Year ${year} — Select week range`;
    return `Year ${year} — Weeks ${Math.min(from,to)} to ${Math.max(from,to)}`;
  }

  function enableWeekInputs(enabled) {
    if (fromEl) fromEl.disabled = !enabled;
    if (toEl) toEl.disabled = !enabled;
  }

  // -----------------------------
  // Scroll reveal helpers (.in-view)
  // -----------------------------
  function setupInViewObserver() {
    const targets = [];

    // KPI grids
    document.querySelectorAll('.kpi-grid').forEach(el => targets.push(el));

    // Charts
    document.querySelectorAll('.chart').forEach(el => targets.push(el));

    // Summary
    if (summaryEl) targets.push(summaryEl);

    // Table wrapper
    if (tableWrap) targets.push(tableWrap);

    if (!targets.length) return;

    if (!('IntersectionObserver' in window)) {
      targets.forEach(el => el.classList.add('in-view'));
      return;
    }

    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('in-view');
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.18 });

    targets.forEach(el => io.observe(el));
  }

  // -----------------------------
  // JSON loading + normalizing
  // -----------------------------
  async function fetchJson(url) {
    const res = await fetch(url, { cache: 'no-store' });
    if (!res.ok) throw new Error(`Failed to load ${url} (${res.status})`);
    return res.json();
  }

  // Normalize one row to:
  // { year, week, lotsOffered, lotsSold, qtyOfferedKg, qtySoldKg, avgPriceUsdKg }
  function normalizeRow(r) {
    const year = num(r.year ?? r.Y ?? r.saleYear ?? r.SALE_YEAR, 0);

    let week = num(r.week ?? r.W ?? r.saleWeek ?? r.SALE_WEEK, 0);
    if (!week && typeof r.sale === 'string') {
      const m = r.sale.match(/(\d+)/);
      week = m ? num(m[1], 0) : 0;
    }

    const lotsOffered = num(
      r.lotsOffered ?? r.lots_offered ?? r.offered_lots ?? r.LOTS_OFFERED,
      0
    );

    const lotsSold = num(
      r.lotsSold ?? r.lots_sold ?? r.sold_lots ?? r.LOTS_SOLD,
      0
    );

    const qtyOfferedKg = num(
      r.qtyOfferedKg ?? r.qty_offered_kg ?? r.qty_offered ?? r.QTY_OFFERED_KG,
      0
    );

    const qtySoldKg = num(
      r.qtySoldKg ?? r.qty_sold_kg ?? r.qty_sold ?? r.QTY_SOLD_KG,
      0
    );

    const avgPriceUsdKg = num(
      r.avgPriceUsdKg ?? r.avg_price_usd_kg ?? r.avgPrice ?? r.avg_prc ?? r.AVG_PRICE_USD_KG,
      0
    );

    return { year, week, lotsOffered, lotsSold, qtyOfferedKg, qtySoldKg, avgPriceUsdKg };
  }

  function normalizeDataset(json) {
    const arr = Array.isArray(json)
      ? json
      : (json.data ?? json.rows ?? json.broker_sale ?? []);

    return arr
      .map(normalizeRow)
      .filter(r => r.week); // keep as long as week exists
  }

  let DATA_AUCTION = [];
  let DATA_BROKER  = [];
  let DATA_READY = false;

  async function loadDatasets() {
    try {
      const [auctionJson, brokerJson] = await Promise.all([
        fetchJson(DATA_SOURCES.auction),
        fetchJson(DATA_SOURCES.broker),
      ]);
      DATA_AUCTION = normalizeDataset(auctionJson);
      DATA_BROKER  = normalizeDataset(brokerJson);
      DATA_READY = true;
    } catch (e) {
      console.error(e);
      DATA_READY = false;
      if (rowsEl) {
        rowsEl.innerHTML = `
          <tr>
            <td colspan="8" style="padding:14px;color:#b91c1c;">
              Failed to load report data. Check JSON paths:
              <code>${DATA_SOURCES.auction}</code> and <code>${DATA_SOURCES.broker}</code>
            </td>
          </tr>
        `;
      }
    }
  }

  function filterData(arr, {year, all, from, to}) {
    let out = arr.slice();

    // Allow missing year rows (0) to match selected year
    if (year) out = out.filter(r => (r.year === year) || (r.year === 0));

    if (!all) {
      if (from && to) {
        const a = Math.min(from, to);
        const b = Math.max(from, to);
        out = out.filter(r => r.week >= a && r.week <= b);
      } else {
        out = [];
      }
    }

    if (year) out = out.map(r => (r.year ? r : ({ ...r, year })));

    out.sort((a,b) => (a.year - b.year) || (a.week - b.week));
    return out;
  }

  // -----------------------------
  // KPIs
  // -----------------------------
  function calcKpis(arr) {
    if (!arr.length) {
      return { sale:'—', offered:'—', sold:'—', avg:'—', sellThrough:'—' };
    }

    const year = arr[arr.length - 1].year;
    const lastWeek = arr[arr.length - 1].week;

    const offeredKg = arr.reduce((s,r)=> s + num(r.qtyOfferedKg), 0);
    const soldKg    = arr.reduce((s,r)=> s + num(r.qtySoldKg), 0);

    const soldWeight = arr.reduce((s,r)=> s + num(r.qtySoldKg), 0);
    const weighted = soldWeight > 0
      ? arr.reduce((s,r)=> s + (num(r.avgPriceUsdKg) * num(r.qtySoldKg)), 0) / soldWeight
      : (arr.reduce((s,r)=> s + num(r.avgPriceUsdKg), 0) / arr.length);

    const sellThrough = offeredKg > 0 ? (soldKg / offeredKg) * 100 : 0;

    return {
      sale: `Y${year} W${lastWeek}`,
      offered: fmtKg(offeredKg),
      sold: fmtKg(soldKg),
      avg: `${fmtUsd(weighted)} USD/kg`,
      sellThrough: fmtPct(sellThrough),
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

  function renderKpis(combrokArr, auctionArr) {
    if (!kpiCombrok || !kpiAuction) return;

    const ck = calcKpis(combrokArr);
    const ak = calcKpis(auctionArr);

    kpiCombrok.innerHTML = [
      kpiCard({label:'Sale', value: ck.sale, hint:'Latest period marker'}),
      kpiCard({label:'Offered Qty', value: ck.offered}),
      kpiCard({label:'Sold Qty', value: ck.sold}),
      kpiCard({label:'Avg Price', value: ck.avg, hint:`Sell-through: ${ck.sellThrough}`}),
    ].join('');

    kpiAuction.innerHTML = [
      kpiCard({label:'Sale', value: ak.sale, hint:'Market period marker'}),
      kpiCard({label:'Offered Qty', value: ak.offered}),
      kpiCard({label:'Sold Qty', value: ak.sold}),
      kpiCard({label:'Avg Price', value: ak.avg, hint:`Sell-through: ${ak.sellThrough}`}),
    ].join('');
  }

  // -----------------------------
  // Summary + Table
  // -----------------------------
  function renderSummary(arr, range) {
    if (!summaryEl) return;
    const label = rangeLabel(range);
    const k = calcKpis(arr);

    summaryEl.innerHTML = `
      <div class="mr-summary">
        <div>
          <h3 class="mr-summary-title">Report Summary</h3>
          <p class="mr-summary-sub muted">${label}</p>
        </div>
        <div class="mr-summary-badges">
          <span class="mr-badge">Offered: ${k.offered}</span>
          <span class="mr-badge">Sold: ${k.sold}</span>
          <span class="mr-badge">Avg: ${k.avg}</span>
          <span class="mr-badge">Sell-through: ${k.sellThrough}</span>
        </div>
      </div>
    `;
  }

  function renderTable(arr) {
    if (!rowsEl) return;

    if (!arr.length) {
      rowsEl.innerHTML = `
        <tr>
          <td colspan="8" style="padding:14px;color:#64748b;">
            No data for the selected filters. Try “All weeks” or adjust week range.
          </td>
        </tr>
      `;
      return;
    }

    rowsEl.innerHTML = arr.map((r, idx) => {
      const soldPct = r.qtyOfferedKg > 0 ? (r.qtySoldKg / r.qtyOfferedKg) * 100 : 0;

      // Stagger table animation
      const delay = Math.min(idx * 0.05, 1.2);

      return `
        <tr style="animation-delay:${delay}s">
          <td>${r.year}</td>
          <td>${r.week}</td>
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
  // Minimal charts (SVG)
  // -----------------------------
  function renderLineChart(el, points, {minY, maxY, labelSuffix = ''} = {}) {
    if (!el) return;
    el.innerHTML = '';

    if (!points.length) {
      el.innerHTML = `<div class="chart-empty">No chart data</div>`;
      return;
    }

    const W = 640, H = 260, PAD = 30;
    const xs = points.map(p => p.x);
    const ys = points.map(p => p.y);

    const minX = Math.min(...xs), maxX = Math.max(...xs);
    const _minY = (minY != null) ? minY : Math.min(...ys);
    const _maxY = (maxY != null) ? maxY : Math.max(...ys);

    const xScale = (x) => PAD + ((x - minX) / (maxX - minX || 1)) * (W - PAD*2);
    const yScale = (y) => (H - PAD) - ((y - _minY) / (_maxY - _minY || 1)) * (H - PAD*2);

    const d = points.map((p,i) => `${i===0?'M':'L'} ${xScale(p.x).toFixed(1)} ${yScale(p.y).toFixed(1)}`).join(' ');

    const svg = document.createElementNS('http://www.w3.org/2000/svg','svg');
    svg.setAttribute('viewBox', `0 0 ${W} ${H}`);
    svg.setAttribute('width','100%');
    svg.setAttribute('height','100%');

    svg.innerHTML = `
      <path class="mr-axis" d="M ${PAD} ${H-PAD} L ${W-PAD} ${H-PAD}" stroke="rgba(148,163,184,.55)" stroke-width="1"/>
      <path class="mr-axis" d="M ${PAD} ${PAD} L ${PAD} ${H-PAD}" stroke="rgba(148,163,184,.55)" stroke-width="1"/>

      <path class="mr-line-a" d="${d}" fill="none" stroke="currentColor" stroke-width="3"/>
      ${points.map((p,i) => {
        if (i !== points.length - 1) return '';
        const cx = xScale(p.x), cy = yScale(p.y);
        return `<circle class="mr-dot" cx="${cx.toFixed(1)}" cy="${cy.toFixed(1)}" r="5" fill="currentColor"></circle>`;
      }).join('')}

      <text class="mr-axis-label" x="${PAD}" y="${PAD-10}" fill="rgba(100,116,139,.95)" font-size="12" font-weight="700">
        ${_minY.toFixed(2)}${labelSuffix} → ${_maxY.toFixed(2)}${labelSuffix}
      </text>
    `;

    // animate line drawing
    requestAnimationFrame(() => {
      const path = svg.querySelector('.mr-line-a');
      if (path) {
        const len = path.getTotalLength();
        path.style.setProperty('--path-length', `${len}`);
        path.style.strokeDasharray = `${len}`;
        path.style.strokeDashoffset = `${len}`;
      }
    });

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
          <rect class="mr-bar-off" x="${x.toFixed(1)}" y="${yOff.toFixed(1)}" width="${barW.toFixed(1)}" height="${offeredH.toFixed(1)}"
                rx="8" fill="currentColor" opacity="0.18"></rect>
          <rect class="mr-bar-sold" x="${x.toFixed(1)}" y="${ySold.toFixed(1)}" width="${barW.toFixed(1)}" height="${soldH.toFixed(1)}"
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
        <circle class="mr-donut-sold" cx="${cx}" cy="${cy}" r="${r}"
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
  // Render everything
  // -----------------------------
  function renderAll() {
    if (!DATA_READY) return;

    const range = getRange();

    const combrokArr = filterData(DATA_BROKER, range);
    const auctionArr = filterData(DATA_AUCTION, range);

    renderKpis(combrokArr, auctionArr);
    renderSummary(combrokArr, range);
    renderTable(combrokArr);

    // Charts
    const ptsPrice = auctionArr.map(r => ({ x: r.week, y: num(r.avgPriceUsdKg) }));
    renderLineChart(chartPriceTrend, ptsPrice, { labelSuffix: ' USD/kg' });

    const bars = auctionArr.map(r => ({ offered: num(r.qtyOfferedKg), sold: num(r.qtySoldKg) }));
    renderStackedBars(chartQtyStacked, bars);

    const offeredTotal = auctionArr.reduce((s,r)=> s + num(r.qtyOfferedKg), 0);
    const soldTotal = auctionArr.reduce((s,r)=> s + num(r.qtySoldKg), 0);
    renderDonut(chartSellthrough, soldTotal, offeredTotal);

    // Lots lines (simple: offered series)
    const ptsLotsOff = combrokArr.map(r => ({ x: r.week, y: num(r.lotsOffered) }));
    renderLineChart(chartLotsLines, ptsLotsOff, {});

    // Re-arm in-view animations after HTML updates
    setupInViewObserver();
  }

  // -----------------------------
  // Init
  // -----------------------------
  if (fromEl && toEl) {
    weekOptions(fromEl, 'From');
    weekOptions(toEl, 'To');
  }

  enableWeekInputs(false);

  if (allWeeks) {
    allWeeks.addEventListener('change', () => {
      const enabled = !allWeeks.checked;
      enableWeekInputs(enabled);
      if (!enabled) { if (fromEl) fromEl.value = ''; if (toEl) toEl.value = ''; }
    });
  }

  if (applyBtn) applyBtn.addEventListener('click', () => renderAll());

  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      if (yearEl) yearEl.value = '';
      if (allWeeks) allWeeks.checked = true;
      enableWeekInputs(false);
      if (fromEl) fromEl.value = '';
      if (toEl) toEl.value = '';
      renderAll();
    });
  }

  (async () => {
    await loadDatasets();
    renderAll();
  })();
});