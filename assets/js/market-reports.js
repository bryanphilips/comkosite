/* =======================================================
   MARKET REPORTS — data loader + tiny SVG chart kit
   (Week range + All Weeks + axes/labels/legend + scroll animations + pagination 26/page)
   ======================================================= */
(() => {
  'use strict';

  // --- DOM -----------------------------------------------------------------
  const YEAR_IN   = document.getElementById('mr-year');       // <input type=number>
  const WEEK_FROM = document.getElementById('mr-week-from');  // <select>
  const WEEK_TO   = document.getElementById('mr-week-to');    // <select>
  const ALL_WEEKS = document.getElementById('mr-all-weeks');  // <input type=checkbox>

  const BTN_APPLY = document.getElementById('mr-apply');
  const BTN_CLEAR = document.getElementById('mr-clear');

  const KPIS    = document.getElementById('mr-kpis');
  const ROWS    = document.getElementById('mr-rows');
  const SUMMARY = document.getElementById('mr-summary');

  // Chart containers
  const elPriceTrend = document.getElementById('chart-price-trend');
  const elQtyStacked = document.getElementById('chart-qty-stacked');
  const elDonut      = document.getElementById('chart-sellthrough');
  const elLotsLines  = document.getElementById('chart-lots-lines');

  const DATA_URL = '/assets/data/market-reports.json';

  // Global data store
  let ALL = [];

  // Pagination state
  const PAGE_SIZE = 26;
  let currentPage = 1;
  let CURRENT_VIEW = []; // filtered rows (charts/KPIs use this full set; table is paged)

  // --- Utils ---------------------------------------------------------------
  const fmt   = new Intl.NumberFormat();
  const money = new Intl.NumberFormat(undefined, { style:'currency', currency:'USD', maximumFractionDigits:2 });
  const pct   = (num) => (Math.round(num * 10) / 10).toFixed(1) + '%';
  const toNum = (v) => {
    const n = Number(v);
    return Number.isFinite(n) ? n : 0;
  };

  // --- Data layer ----------------------------------------------------------
  async function loadData(){
    try{
      const res = await fetch(DATA_URL, { cache:'no-store' });
      if(!res.ok) throw new Error('No data file');
      const json = await res.json();
      return Array.isArray(json) ? json : (json.data || []);
    }catch(e){
      // Demo data if file missing
      const nowYear = (new Date()).getFullYear();
      const seed = [];
      let price = 2.30;
      for(let w=1; w<=12; w++){
        const lotsOff = 12000 + Math.round(Math.random()*2500);
        const lotsSold= Math.round(lotsOff*(0.86 + Math.random()*0.08));
        const qtyOff  = 6_500_000 + Math.round(Math.random()*800_000);
        const qtySold = Math.round(qtyOff*(0.86 + Math.random()*0.08));
        price += (Math.random()-.5)*0.08;
        seed.push({
          year: nowYear, week: w,
          lotsOffered: lotsOff,
          lotsSold: lotsSold,
          qtyOfferedKg: qtyOff,
          qtySoldKg: qtySold,
          avgPriceUsd: +price.toFixed(2),
          soldPct: +(lotsSold/lotsOff*100).toFixed(2)
        });
      }
      return seed;
    }
  }

  // Populate both week selects based on selected year’s available weeks
  function buildWeekOptionsForYear(allRows, year) {
    const weeks = Array.from(
      new Set(allRows.filter(r => r.year === +year).map(r => r.week))
    ).sort((a,b) => a - b);

    const fill = (sel, label) => {
      sel.innerHTML = `<option value="">${label}</option>`;
      for (const w of weeks) {
        const opt = document.createElement('option');
        opt.value = String(w);
        opt.textContent = `W${w}`;
        sel.appendChild(opt);
      }
    };

    fill(WEEK_FROM, 'From');
    fill(WEEK_TO, 'To');

    if (weeks.length) {
      WEEK_FROM.value = String(weeks[0]);
      WEEK_TO.value   = String(weeks[weeks.length - 1]);
    }
    updateRangeEnabledState();
  }

  function updateRangeEnabledState(){
    const disabled = ALL_WEEKS.checked;
    WEEK_FROM.disabled = disabled;
    WEEK_TO.disabled   = disabled;
  }

  function applyFilters(rows){
    const y     = parseInt(YEAR_IN.value,10);
    const allW  = ALL_WEEKS.checked;
    const fromW = parseInt(WEEK_FROM.value,10);
    const toW   = parseInt(WEEK_TO.value,10);

    let view = rows.filter(r => Number.isFinite(y) ? r.year === y : true);

    if (!allW && Number.isFinite(fromW) && Number.isFinite(toW)) {
      const lo = Math.min(fromW, toW);
      const hi = Math.max(fromW, toW);
      view = view.filter(r => r.week >= lo && r.week <= hi);
    }

    return view.sort((a,b)=> a.week - b.week);
  }

  // --- Render: KPIs --------------------------------------------------------
  function renderKPIs(rowsAll, rowsView){
    if(!rowsView.length){ KPIS.innerHTML = ''; SUMMARY.textContent=''; return; }

    const totalLotsOff = rowsView.reduce((a,b)=>a+b.lotsOffered,0);
    const totalLotsSold= rowsView.reduce((a,b)=>a+b.lotsSold,0);
    const totalQtyOff  = rowsView.reduce((a,b)=>a+b.qtyOfferedKg,0);
    const totalQtySold = rowsView.reduce((a,b)=>a+b.qtySoldKg,0);
    const avgPrice     = rowsView.reduce((a,b)=>a+b.avgPriceUsd,0)/rowsView.length;
    const sellThrough  = totalLotsSold/totalLotsOff*100;

    const lastRow = rowsView[rowsView.length-1];
    const prevRow = rowsAll
      .filter(r => r.year===lastRow.year && r.week===lastRow.week-1)
      .slice(-1)[0];

    const prevSell = prevRow ? (prevRow.lotsSold/prevRow.lotsOffered*100) : null;
    const deltaPct = prevSell==null ? 0 : (sellThrough - prevSell);
    const deltaPrice = prevRow ? (avgPrice - prevRow.avgPriceUsd) : 0;

    KPIS.innerHTML = `
      <div class="kpi">
        <div class="label">Sell-Through</div>
        <div class="value">${pct(sellThrough)}</div>
        <div class="delta ${deltaPct>=0?'up':'down'}">${deltaPct>=0?'+':''}${deltaPct.toFixed(1)} pp vs prev.</div>
      </div>
      <div class="kpi">
        <div class="label">Avg Price</div>
        <div class="value">${money.format(avgPrice)}</div>
        <div class="delta ${deltaPrice>=0?'up':'down'}">${deltaPrice>=0?'+':''}${deltaPrice.toFixed(2)} vs prev.</div>
      </div>
      <div class="kpi">
        <div class="label">Qty Sold</div>
        <div class="value">${fmt.format(totalQtySold)} kg</div>
        <div class="delta">of ${fmt.format(totalQtyOff)} kg offered</div>
      </div>
      <div class="kpi">
        <div class="label">Lots Sold</div>
        <div class="value">${fmt.format(totalLotsSold)}</div>
        <div class="delta">of ${fmt.format(totalLotsOff)} offered</div>
      </div>
    `;

    SUMMARY.innerHTML = `
      <strong>Summary:</strong> ${rowsView.length} week(s) selected ·
      Sell-through ${pct(sellThrough)} ·
      Avg price ${money.format(avgPrice)} ·
      Qty sold ${fmt.format(totalQtySold)} kg.
    `;
  }

  // --- Render: Table (paged) ----------------------------------------------
  function renderTablePage(){
    const total = CURRENT_VIEW.length;
    const startIdx = (currentPage - 1) * PAGE_SIZE;
    const pageRows = CURRENT_VIEW.slice(startIdx, startIdx + PAGE_SIZE);

    if(!pageRows.length){
      ROWS.innerHTML = '<tr><td colspan="8">No data for the selected filter.</td></tr>';
      return;
    }
    ROWS.innerHTML = pageRows.map(r=>{
      const sp = Number.isFinite(r.soldPct) ? r.soldPct : (r.lotsSold/r.lotsOffered*100);
      return `<tr>
        <td>${r.year}</td>
        <td>${r.week}</td>
        <td>${fmt.format(r.lotsOffered)}</td>
        <td>${fmt.format(r.lotsSold)}</td>
        <td>${fmt.format(r.qtyOfferedKg)}</td>
        <td>${fmt.format(r.qtySoldKg)}</td>
        <td>${(sp||0).toFixed(1)}%</td>
        <td>${Number(r.avgPriceUsd||0).toFixed(2)}</td>
      </tr>`;
    }).join('');
  }

  function renderPager(totalCount){
    const tableWrap = document.querySelector('.table-wrap');
    if(!tableWrap) return;

    let pager = document.getElementById('mr-pager');
    if(!pager){
      pager = document.createElement('div');
      pager.id = 'mr-pager';
      pager.style.display = 'flex';
      pager.style.alignItems = 'center';
      pager.style.gap = '8px';
      pager.style.padding = '10px 0';
      tableWrap.insertAdjacentElement('afterend', pager);
    }

    const totalPages = Math.max(1, Math.ceil(totalCount / PAGE_SIZE));
    currentPage = Math.min(Math.max(1, currentPage), totalPages);

    const start = (currentPage - 1) * PAGE_SIZE + 1;
    const end   = Math.min(totalCount, currentPage * PAGE_SIZE);

    pager.innerHTML = `
      <button class="btn ghost" data-act="first" ${currentPage===1?'disabled':''}>&laquo; First</button>
      <button class="btn ghost" data-act="prev"  ${currentPage===1?'disabled':''}>&lsaquo; Prev</button>
      <span class="muted small" style="margin:0 6px;">
        Page <strong>${currentPage}</strong> of <strong>${totalPages}</strong>
      </span>
      <button class="btn ghost" data-act="next" ${currentPage===totalPages?'disabled':''}>Next &rsaquo;</button>
      <button class="btn ghost" data-act="last" ${currentPage===totalPages?'disabled':''}>Last &raquo;</button>
      <span class="muted small" style="margin-left:auto;">Showing ${start}&ndash;${end} of ${totalCount}</span>
    `;

    pager.querySelectorAll('button[data-act]').forEach(btn=>{
      btn.onclick = () => {
        const act = btn.getAttribute('data-act');
        if(act==='first') currentPage = 1;
        if(act==='prev')  currentPage = Math.max(1, currentPage-1);
        if(act==='next')  currentPage = Math.min(totalPages, currentPage+1);
        if(act==='last')  currentPage = totalPages;

        renderTablePage();
        renderPager(totalCount);
        window.dispatchEvent(new CustomEvent('mr:refreshed'));
      };
    });
  }

  // --- Tiny SVG Chart Kit --------------------------------------------------
  const SVG_NS = 'http://www.w3.org/2000/svg';
  const SVG  = (w,h)=>{ const s=document.createElementNS(SVG_NS,'svg'); s.setAttribute('viewBox',`0 0 ${w} ${h}`); s.setAttribute('preserveAspectRatio','xMidYMid meet'); return s; };
  const Path = (d, cls)=>{ const p=document.createElementNS(SVG_NS,'path'); p.setAttribute('d',d); if(cls) p.setAttribute('class',cls); return p; };
  const Rect = (x,y,w,h,cls)=>{ const r=document.createElementNS(SVG_NS,'rect'); r.setAttribute('x',x); r.setAttribute('y',y); r.setAttribute('width',w); r.setAttribute('height',h); if(cls) r.setAttribute('class',cls); r.setAttribute('rx',3); r.setAttribute('ry',3); return r; };
  const Circle = (cx,cy,r,cls)=>{ const c=document.createElementNS(SVG_NS,'circle'); c.setAttribute('cx',cx); c.setAttribute('cy',cy); c.setAttribute('r',r); if(cls) c.setAttribute('class',cls); return c; };
  const Text = (x,y,text,cls,anchor='middle')=>{ const t=document.createElementNS(SVG_NS,'text'); t.setAttribute('x',x); t.setAttribute('y',y); t.setAttribute('text-anchor',anchor); if(cls) t.setAttribute('class',cls); t.textContent=text; return t; };

  const injectOnce = (()=> {
    let done=false;
    return ()=>{
      if(done) return; done=true;
      const css = `
        .mr-gridline{ stroke:#e9eef5; stroke-width:1; }
        .mr-axis{ stroke:#c7d7e6; stroke-width:1; }
        .mr-tick{ fill:#5b6b79; font-size:11px; font-weight:600; }
        .mr-axis-label{ fill:#0A4E73; font-size:12px; font-weight:800; }
        .mr-line-a{ fill:none; stroke:#0A68A0; stroke-width:2.5; }
        .mr-line-b{ fill:none; stroke:#34a853; stroke-width:2.5; }
        .mr-dot{ fill:#0A68A0; }
        .mr-bar-off{ fill:#cfeaf7; }
        .mr-bar-sold{ fill:#8fd2a4; }
        .mr-legend{ font-size:12px; fill:#0a3246; font-weight:700; }
        .mr-donut-sold{ stroke:#0A68A0; fill:none; }
      `;
      const tag = document.createElement('style'); tag.textContent = css;
      document.head.appendChild(tag);
    };
  })();

  function niceTicks(minVal, maxVal, steps=4){
    const span = maxVal - minVal || 1;
    const raw = span/steps;
    const pow10 = Math.pow(10, Math.floor(Math.log10(raw)));
    const mults = [1,2,2.5,5,10];
    const step = mults.find(m=> m*pow10 >= raw) * pow10;
    const start = Math.floor(minVal/step)*step;
    const end   = Math.ceil(maxVal/step)*step;
    const ticks = [];
    for(let v=start; v<=end+1e-9; v+=step) ticks.push(v);
    return {ticks, min:start, max:end};
  }

  // Line chart with axes + legend (robust to invalid points)
  function lineChart(el, series, labels){
    injectOnce();
    el.innerHTML='';
    if(!series?.length || !labels?.length) return;

    const W=860, H=280, Pleft=56, Pright=20, Ptop=24, Pbot=40;

    const vals = series.flat().map(toNum);
    const vmin = Math.min(...vals);
    const vmax = Math.max(...vals);
    const {ticks, min: yMin, max: yMax} = niceTicks(vmin, vmax, 4);

    const xCount = Math.max(labels.length, 1);
    const plotW = W - Pleft - Pright;
    const plotH = H - Ptop - Pbot;

    const sy = v => Ptop + plotH - ( (toNum(v) - yMin) / (yMax - yMin || 1) ) * plotH;
    const sx = i => Pleft + (i * (plotW / Math.max(xCount-1,1)));

    const svg = SVG(W,H);

    // Gridlines + y ticks
    ticks.forEach(v=>{
      const y = sy(v);
      svg.appendChild(Path(`M ${Pleft} ${y} H ${W-Pright}`,'mr-gridline'));
      svg.appendChild(Text(Pleft-8, y+4, String(Math.round(v*100)/100), 'mr-tick','end'));
    });

    // Axes
    svg.appendChild(Path(`M ${Pleft} ${Ptop} V ${H-Pbot}`,'mr-axis'));
    svg.appendChild(Path(`M ${Pleft} ${H-Pbot} H ${W-Pright}`,'mr-axis'));

    // X tick labels (sparse)
    const step = Math.ceil(labels.length / 10);
    labels.forEach((lab,i)=>{
      if(i % step !== 0 && i!==labels.length-1) return;
      const x = sx(i);
      svg.appendChild(Text(x, H-Pbot+16, lab, 'mr-tick','middle'));
    });

    // Axis labels
    const isLots = el.id && el.id.includes('lots-lines');
    const isPrice= el.id && el.id.includes('price-trend');
    const yLabel = isPrice ? 'USD/kg' : 'Count';
    svg.appendChild(Text(Pleft-42, Ptop-6, yLabel, 'mr-axis-label','start'));
    svg.appendChild(Text(W-Pright, H-8, 'Weeks', 'mr-axis-label','end'));

    // Lines + last valid dot
    series.forEach((s,idx)=>{
      let d = '';
      let started = false;
      for (let i=0; i<s.length; i++){
        const x = sx(i);
        const y = sy(s[i]);
        if (Number.isFinite(x) && Number.isFinite(y)) {
          d += (started ? ' L ' : ' M ') + x + ' ' + y;
          started = true;
        }
      }
      const path = Path(d, idx ? 'mr-line-b' : 'mr-line-a');
      // length hint for CSS animations (optional)
      try {
        const len = path.getTotalLength();
        path.style.setProperty('--path-length', len);
      } catch(e){}
      svg.appendChild(path);

      // Last valid point dot
      for (let n=s.length-1; n>=0; n--){
        const x = sx(n), y = sy(s[n]);
        if (Number.isFinite(x) && Number.isFinite(y)) {
          svg.appendChild(Circle(x, y, 3.5, 'mr-dot'));
          break;
        }
      }
    });

    // Legend for two series
    if(series.length===2){
      const legendX = W - Pright - 150, legendY = Ptop + 8, gap=18;
      const names = (isLots)
        ? ['Lots Offered','Lots Sold']
        : ['Series A','Series B'];
      // A
      const swA = document.createElementNS(SVG_NS,'line');
      swA.setAttribute('x1', legendX); swA.setAttribute('y1', legendY);
      swA.setAttribute('x2', legendX+20); swA.setAttribute('y2', legendY);
      swA.setAttribute('class','mr-line-a');
      svg.appendChild(swA);
      svg.appendChild(Text(legendX+26, legendY+4, names[0], 'mr-legend','start'));
      // B
      const y2 = legendY + gap;
      const swB = document.createElementNS(SVG_NS,'line');
      swB.setAttribute('x1', legendX); swB.setAttribute('y1', y2);
      swB.setAttribute('x2', legendX+20); swB.setAttribute('y2', y2);
      swB.setAttribute('class','mr-line-b');
      svg.appendChild(swB);
      svg.appendChild(Text(legendX+26, y2+4, names[1], 'mr-legend','start'));
    }

    el.appendChild(svg);
  }

  // Stacked bars with axes + tick labels
  function stackedBars(el, offered, sold){
    injectOnce();
    el.innerHTML='';
    if(!offered?.length || !sold?.length) return;

    const W=860, H=280, Pleft=56, Pright=16, Ptop=24, Pbot=40;
    const plotW = W - Pleft - Pright;
    const plotH = H - Ptop - Pbot;

    const vmax = Math.max(...offered.map(toNum), ...sold.map(toNum));
    const {ticks, min: yMin, max: yMax} = niceTicks(0, vmax, 4);

    const N = offered.length;
    const sx = i => Pleft + i*(plotW/N) + (plotW/N)/2;
    const sy = v => Ptop + plotH - ( (toNum(v) - yMin) / (yMax - yMin || 1) ) * plotH;
    const bw = (plotW/N) * 0.6;

    const svg = SVG(W,H);

    // Grid + y ticks
    ticks.forEach(v=>{
      const y = sy(v);
      svg.appendChild(Path(`M ${Pleft} ${y} H ${W-Pright}`,'mr-gridline'));
      svg.appendChild(Text(Pleft-8, y+4, String(Math.round(v)), 'mr-tick','end'));
    });

    // Axes
    svg.appendChild(Path(`M ${Pleft} ${Ptop} V ${H-Pbot}`,'mr-axis'));
    svg.appendChild(Path(`M ${Pleft} ${H-Pbot} H ${W-Pright}`,'mr-axis'));

    // Bars
    for(let i=0;i<N;i++){
      const x = sx(i) - bw/2;
      const yOff = sy(offered[i]);
      const hOff = (H-Pbot) - yOff;
      const ySold= sy(sold[i]);
      const hSold= (H-Pbot) - ySold;

      svg.appendChild(Rect(x, yOff, bw, hOff, 'mr-bar-off'));
      svg.appendChild(Rect(x, ySold, bw, hSold, 'mr-bar-sold'));
    }

    // Axis labels
    svg.appendChild(Text(Pleft-42, Ptop-6, 'Kilograms', 'mr-axis-label','start'));
    svg.appendChild(Text(W-Pright, H-8, 'Weeks', 'mr-axis-label','end'));

    el.appendChild(svg);
  }

  // Donut chart (% sell-through)
  function donut(el, soldPct){
    injectOnce();
    el.innerHTML='';
    const W=240,H=240,R=92;
    const svg = SVG(W,H);
    const cx=W/2, cy=H/2;

    // background ring
    const bg = document.createElementNS(SVG_NS,'circle');
    bg.setAttribute('cx',cx); bg.setAttribute('cy',cy); bg.setAttribute('r',R);
    bg.setAttribute('fill','none'); bg.setAttribute('stroke','#eef4f8'); bg.setAttribute('stroke-width',24);
    svg.appendChild(bg);

    // sold arc
    const frac = Math.max(0, Math.min(1, (soldPct||0)/100));
    const theta = frac * Math.PI*2;
    const startX = cx + R*Math.cos(-Math.PI/2);
    const startY = cy + R*Math.sin(-Math.PI/2);
    const endX   = cx + R*Math.cos(theta - Math.PI/2);
    const endY   = cy + R*Math.sin(theta - Math.PI/2);
    const large  = frac > .5 ? 1 : 0;

    const arc = document.createElementNS(SVG_NS,'path');
    arc.setAttribute('d', `M ${startX} ${startY} A ${R} ${R} 0 ${large} 1 ${endX} ${endY}`);
    arc.setAttribute('class','mr-donut-sold');
    arc.setAttribute('stroke-width',24);
    svg.appendChild(arc);

    // center label
    const txt = Text(cx, cy+6, (Math.round((soldPct||0)*10)/10).toFixed(1)+'%', 'mr-axis-label','middle');
    txt.setAttribute('font-size','20');
    svg.appendChild(txt);

    el.appendChild(svg);
  }

  // --- Main ----------------------------------------------------------------
  function refresh(){
    // Sanitize and filter
    const view = applyFilters(
      ALL.map(r => ({
        ...r,
        year:        toNum(r.year),
        week:        toNum(r.week),
        lotsOffered: toNum(r.lotsOffered),
        lotsSold:    toNum(r.lotsSold),
        qtyOfferedKg:toNum(r.qtyOfferedKg),
        qtySoldKg:   toNum(r.qtySoldKg),
        avgPriceUsd: toNum(r.avgPriceUsd),
        soldPct: Number.isFinite(r.soldPct) ? r.soldPct
               : (toNum(r.lotsOffered) ? (toNum(r.lotsSold)/Math.max(1,toNum(r.lotsOffered))*100) : 0)
      }))
    );

    // Update global CURRENT_VIEW and reset to page 1 on filter changes
    CURRENT_VIEW = view;
    currentPage = 1;

    // KPIs + Summary
    renderKPIs(ALL, CURRENT_VIEW);

    // Charts & table
    if(!CURRENT_VIEW.length){
      ROWS.innerHTML = '<tr><td colspan="8">No data for the selected filter.</td></tr>';
      [elPriceTrend, elQtyStacked, elDonut, elLotsLines].forEach(el=> el.innerHTML='');
      renderPager(0);
      window.dispatchEvent(new CustomEvent('mr:refreshed'));
      return;
    }

    // Table page + pager
    renderTablePage();
    renderPager(CURRENT_VIEW.length);

    // Charts (use full filtered data, not paged)
    const labels = CURRENT_VIEW.map(r=>`W${r.week}`);
    const price  = CURRENT_VIEW.map(r=> toNum(r.avgPriceUsd));
    const lotsO  = CURRENT_VIEW.map(r=> toNum(r.lotsOffered));
    const lotsS  = CURRENT_VIEW.map(r=> toNum(r.lotsSold));
    const qtyO   = CURRENT_VIEW.map(r=> toNum(r.qtyOfferedKg));
    const qtyS   = CURRENT_VIEW.map(r=> toNum(r.qtySoldKg));

    lineChart(elPriceTrend, [price], labels);
    stackedBars(elQtyStacked, qtyO, qtyS);
    const soldPctOverall = (lotsS.reduce((a,b)=>a+b,0)/Math.max(1,lotsO.reduce((a,b)=>a+b,0)))*100;
    donut(elDonut, soldPctOverall);
    lineChart(elLotsLines, [lotsO, lotsS], labels);

    // Notify observers to (re)apply in-view animations and row stagger
    window.dispatchEvent(new CustomEvent('mr:refreshed'));
  }

  // Events
  BTN_APPLY?.addEventListener('click', refresh);

  BTN_CLEAR?.addEventListener('click', () => {
    YEAR_IN.value = '';
    ALL_WEEKS.checked = true;
    updateRangeEnabledState();
    WEEK_FROM.innerHTML = '<option value="">From</option>';
    WEEK_TO.innerHTML   = '<option value="">To</option>';
    refresh();
  });

  YEAR_IN?.addEventListener('input', () => {
    if (YEAR_IN.value) buildWeekOptionsForYear(ALL, +YEAR_IN.value);
    refresh();
  });

  ALL_WEEKS?.addEventListener('change', () => {
    updateRangeEnabledState();
    refresh();
  });

  WEEK_FROM?.addEventListener('change', refresh);
  WEEK_TO?.addEventListener('change', refresh);

  // Init
  loadData().then(rows=>{
    const sanitize = r => ({
      year:        toNum(r.year),
      week:        toNum(r.week),
      lotsOffered: toNum(r.lotsOffered),
      lotsSold:    toNum(r.lotsSold),
      qtyOfferedKg:toNum(r.qtyOfferedKg),
      qtySoldKg:   toNum(r.qtySoldKg),
      avgPriceUsd: toNum(r.avgPriceUsd),
      soldPct: Number.isFinite(r.soldPct) ? r.soldPct
              : (toNum(r.lotsOffered) ? (toNum(r.lotsSold)/Math.max(1,toNum(r.lotsOffered))*100) : 0)
    });

    ALL = rows.map(sanitize)
              .sort((a,b)=> a.year===b.year ? a.week-b.week : a.year-b.year);

    // default to latest year and full range
    const last = ALL.slice(-1)[0];
    if (last) {
      YEAR_IN.value = last.year;
      buildWeekOptionsForYear(ALL, last.year);
      ALL_WEEKS.checked = true;
      updateRangeEnabledState();
    }

    refresh();
  });
})();

/* ========= On-scroll animation trigger + table stagger ========= */
(() => {
  const OBS_OPTS = { root:null, rootMargin:'0px', threshold:0.2 };
  const onceObserver = new IntersectionObserver((entries, obs)=>{
    entries.forEach(e=>{
      if(!e.isIntersecting) return;
      e.target.classList.add('in-view');
      obs.unobserve(e.target); // animate once per render
    });
  }, OBS_OPTS);

  const watch = sel => document.querySelectorAll(sel).forEach(el=> onceObserver.observe(el));
  const hook = () => {
    watch('.kpi-grid');
    watch('.chart');
    watch('.donut');
    watch('.table-wrap');
    const summary = document.getElementById('mr-summary');
    if (summary) onceObserver.observe(summary);
  };

  const setRowDelays = () => {
    const rows = document.querySelectorAll('#mr-rows tr');
    rows.forEach((tr, i) => { tr.style.animationDelay = `${Math.min(i * 0.05, 1)}s`; });
  };

  window.addEventListener('mr:refreshed', () => {
    setRowDelays();
    hook();
  });

  document.addEventListener('DOMContentLoaded', hook);
})();