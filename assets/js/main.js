document.addEventListener('DOMContentLoaded', () => {
  // simple fade slider
  document.querySelectorAll('.slides').forEach(slides => {
    const pics = slides.querySelectorAll('picture');
    if (!pics.length) return;
    let i = 0; pics[0].classList.add('active');
    setInterval(() => {
      pics[i].classList.remove('active');
      i = (i + 1) % pics.length;
      pics[i].classList.add('active');
    }, 3500);
  });

  // mobile menu
  const burger = document.querySelector('.burger');
  const nav = document.querySelector('.nav');
  if (burger && nav) burger.addEventListener('click', () => nav.classList.toggle('open'));

  // Market reports rendering
  const rowsEl = document.getElementById('mr-rows');
  if (rowsEl) {
    const yrEl = document.getElementById('mr-year');
    const wkEl = document.getElementById('mr-week');
    const applyBtn = document.getElementById('mr-apply');
    const clearBtn = document.getElementById('mr-clear');
    const summaryEl = document.getElementById('mr-summary');
    let all = [];
    fetch('/assets/data/market-reports.json')
      .then(r => r.json())
      .then(json => { all = json; render(); });

    function render() {
      const y = yrEl.value.trim(), w = wkEl.value.trim();
      let data = all.slice();
      if (y) data = data.filter(d => String(d.year) === y);
      if (w) data = data.filter(d => String(d.week) === w);

      rowsEl.innerHTML = data.map(d => {
        const soldPct = d.qtyOfferedKg ? (d.qtySoldKg/d.qtyOfferedKg*100) : 0;
        return `<tr>
          <td>${d.year}</td><td>${d.week}</td>
          <td>${d.lotsOffered.toLocaleString()}</td>
          <td>${d.lotsSold.toLocaleString()}</td>
          <td>${d.qtyOfferedKg.toLocaleString()}</td>
          <td>${d.qtySoldKg.toLocaleString()}</td>
          <td>${soldPct.toFixed(1)}%</td>
          <td>${(d.avgPriceUsdPerKg??0).toFixed(2)}</td>
        </tr>`;
      }).join('');

      const t = data.reduce((a,d)=>({
        lotsOffered:a.lotsOffered+d.lotsOffered,
        lotsSold:a.lotsSold+d.lotsSold,
        qtyOffered:a.qtyOffered+(d.qtyOfferedKg||0),
        qtySold:a.qtySold+(d.qtySoldKg||0)
      }),{lotsOffered:0,lotsSold:0,qtyOffered:0,qtySold:0});
      const soldPct = t.qtyOffered ? (t.qtySold/t.qtyOffered*100) : 0;

      summaryEl.innerHTML = `<strong>Totals:</strong>
        Lots Offered: ${t.lotsOffered.toLocaleString()} ·
        Lots Sold: ${t.lotsSold.toLocaleString()} ·
        Qty Offered: ${t.qtyOffered.toLocaleString()} kg ·
        Qty Sold: ${t.qtySold.toLocaleString()} kg ·
        Sold %: ${soldPct.toFixed(1)}%`;
    }
    applyBtn.addEventListener('click', render);
    clearBtn.addEventListener('click', () => { yrEl.value=''; wkEl.value=''; render(); });
  }
});