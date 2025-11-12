document.addEventListener('DOMContentLoaded', () => {
  // Tabs & panels
  const tabButtons = document.querySelectorAll('.ps-tab');
  const panels = {
    request: document.getElementById('panel-request'),
    offer: document.getElementById('panel-offer'),
  };
  const alertBox = document.getElementById('ps-alert');

  // Forms & buttons
  const reqForm = document.getElementById('ps-request-form');
  const reqBtn  = document.getElementById('ps-request-submit');
  const offForm = document.getElementById('ps-offer-form');
  const offBtn  = document.getElementById('ps-offer-submit');

  // API endpoints
  const API_REQUEST = '/api/api_private_sale_request.php';
  const API_OFFER   = '/api/api_private_sale_offer.php';

  // reCAPTCHA widgets (explicit)
  let reqWidgetId = null;
  let offWidgetId = null;
  window.initRecaptcha = function initRecaptcha(){
    if (!window.RECAPTCHA_SITE_KEY) return;
    const reqNode = document.getElementById('recaptcha-request');
    const offNode = document.getElementById('recaptcha-offer');

    if (reqNode) {
      reqWidgetId = grecaptcha.render(reqNode, {
        sitekey: window.RECAPTCHA_SITE_KEY,
        callback: () => { reqBtn.disabled = false; },
        'expired-callback': () => { reqBtn.disabled = true; }
      });
    }
    if (offNode) {
      offWidgetId = grecaptcha.render(offNode, {
        sitekey: window.RECAPTCHA_SITE_KEY,
        callback: () => { offBtn.disabled = false; },
        'expired-callback': () => { offBtn.disabled = true; }
      });
    }
  };

  // Tabs
  tabButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const tab = btn.dataset.tab;
      tabButtons.forEach(b => b.classList.toggle('active', b === btn));
      Object.keys(panels).forEach(key => {
        const isActive = key === tab;
        panels[key].classList.toggle('active', isActive);
        panels[key].hidden = !isActive;
      });
      hideAlert();
      // disable buttons until valid/solved
      if (tab === 'request') {
        reqBtn.disabled = (reqWidgetId !== null); // if recaptcha present keep disabled until solved
        if (reqWidgetId === null) reqBtn.disabled = !reqForm.checkValidity();
      } else {
        offBtn.disabled = (offWidgetId !== null);
        if (offWidgetId === null) offBtn.disabled = !offForm.checkValidity() || !tableValid();
      }
    });
  });

  // Alerts
  function showAlert(ok, msg) {
    alertBox.className = 'ps-alert ' + (ok ? 'ok' : 'err');
    alertBox.textContent = msg;
    alertBox.classList.remove('hidden');
    alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    if (ok) setTimeout(() => alertBox.classList.add('hidden'), 7000);
  }
  function hideAlert() {
    alertBox.className = 'ps-alert hidden';
    alertBox.textContent = '';
  }

  // Utils
  const setLoading = (btn, on) => {
    btn.classList.toggle('loading', on);
    btn.disabled = on;
  };
  const emailOk = v => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v);

  // ===================== REQUEST FORM =====================
  reqForm.addEventListener('input', () => {
    if (reqWidgetId === null) reqBtn.disabled = !reqForm.checkValidity();
  });
  reqForm.addEventListener('reset', () => {
    hideAlert();
    if (reqWidgetId !== null && window.grecaptcha) {
      grecaptcha.reset(reqWidgetId);
      reqBtn.disabled = true;
    } else {
      reqBtn.disabled = true;
    }
  });

  reqForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideAlert();

    const fd = new FormData(reqForm);
    const data = Object.fromEntries(fd.entries());
    const teaTypes = [];
    reqForm.querySelectorAll('input[name="teaTypes[]"]:checked').forEach(c => teaTypes.push(c.value));
    data.teaTypes = teaTypes;

    // sanitize
    data.name       = (data.name || '').trim();
    data.phone      = (data.phone || '').trim();
    data.email      = (data.email || '').trim();
    data.gardenMark = (data.gardenMark || '').trim();
    data.grade      = (data.grade || '').trim();
    data.packages   = (data.packages || '').trim();
    data.netWeight  = (data.netWeight || '').trim();

    if (!data.name || !data.phone || !emailOk(data.email) || !data.grade || !data.netWeight || !teaTypes.length) {
      showAlert(false, 'Please fill the required fields and select at least one tea type.');
      return;
    }

    // reCAPTCHA token
    if (reqWidgetId !== null && window.grecaptcha) {
      const token = grecaptcha.getResponse(reqWidgetId);
      if (!token) { showAlert(false, 'Please complete the reCAPTCHA.'); return; }
      data['g-recaptcha-response'] = token;
    }

    setLoading(reqBtn, true);
    try {
      const res = await fetch(API_REQUEST, {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify(data)
      });
      const out = await res.json().catch(() => ({}));
      if (!res.ok || !out.success) throw new Error(out.error || 'Failed to submit request.');

      showAlert(true, '✅ Thank you — your private sale request has been submitted!');
      reqForm.reset();
      if (reqWidgetId !== null && window.grecaptcha) {
        grecaptcha.reset(reqWidgetId);
        reqBtn.disabled = true;
      } else {
        reqBtn.disabled = true;
      }
    } catch (err) {
      showAlert(false, err.message || 'Something went wrong.');
    } finally {
      setLoading(reqBtn, false);
    }
  });

  // ===================== OFFER FORM =====================
  const tableBody = document.querySelector('#ps-offer-table tbody');
  const addRowBtn = document.getElementById('ps-add-row');

  function newRow(values = {}) {
    const uid = Math.random().toString(36).slice(2, 8);
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><input id="inv_${uid}" name="invoice[]" required placeholder="Invoice No." aria-labelledby="inv_${uid}"></td>
      <td><input id="grade_${uid}" name="grade[]" required placeholder="Grade" aria-labelledby="grade_${uid}"></td>
      <td><input id="pkgtype_${uid}" name="packageType[]" required placeholder="e.g., Bags" aria-labelledby="pkgtype_${uid}"></td>
      <td><input id="pkgs_${uid}" name="packages[]" type="number" min="1" step="1" required placeholder="Qty" aria-labelledby="pkgs_${uid}"></td>
      <td><input id="gross_${uid}" name="gross[]" type="number" min="0" step="0.01" required placeholder="kg" aria-labelledby="gross_${uid}"></td>
      <td><input id="tare_${uid}"  name="tare[]"  type="number" min="0" step="0.01" required placeholder="kg" aria-labelledby="tare_${uid}"></td>
      <td><input id="net_${uid}"   name="net[]"   type="number" min="0" step="0.01" required placeholder="kg" aria-labelledby="net_${uid}"></td>
      <td><button type="button" class="row-del" aria-label="Remove row">Remove</button></td>
    `;
    // preload values
    const keys = ['invoice','grade','packageType','packages','gross','tare','net'];
    keys.forEach((k,i) => {
      const el = tr.querySelectorAll('input')[i];
      if (values[k] != null) el.value = values[k];
    });

    // auto-calc net
    const gross = tr.querySelector(`input[id^="gross_"]`);
    const tare  = tr.querySelector(`input[id^="tare_"]`);
    const net   = tr.querySelector(`input[id^="net_"]`);
    function recalc() {
      const g = parseFloat(gross.value||'0'), t = parseFloat(tare.value||'0');
      const n = (g - t);
      if (!isNaN(n) && n >= 0) net.value = n.toFixed(2);
    }
    gross.addEventListener('change', recalc);
    tare.addEventListener('change', recalc);

    tr.querySelector('.row-del').addEventListener('click', () => {
      tr.remove();
      if (!tableBody.children.length) addRow();
      if (offWidgetId === null) offBtn.disabled = !offForm.checkValidity() || !tableValid();
    });

    tableBody.appendChild(tr);
  }
  function addRow(v){ newRow(v); }
  addRowBtn.addEventListener('click', () => addRow());
  addRow(); // initial row

  function tableValid() {
    const inputs = tableBody.querySelectorAll('input');
    for (const i of inputs) { if (!i.value.trim()) return false; }
    return tableBody.children.length > 0;
  }

  offForm.addEventListener('input', () => {
    if (offWidgetId === null) offBtn.disabled = !offForm.checkValidity() || !tableValid();
  });

  offForm.addEventListener('reset', () => {
    hideAlert();
    tableBody.innerHTML = '';
    addRow();
    if (offWidgetId !== null && window.grecaptcha) {
      grecaptcha.reset(offWidgetId);
      offBtn.disabled = true;
    } else {
      offBtn.disabled = true;
    }
  });

  offForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideAlert();

    if (!offForm.checkValidity() || !tableValid()) {
      showAlert(false, 'Please complete all required fields and table rows.');
      return;
    }

    // collect rows
    const rows = [];
    tableBody.querySelectorAll('tr').forEach(tr => {
      rows.push({
        invoice:     tr.querySelector('input[name="invoice[]"]').value.trim(),
        grade:       tr.querySelector('input[name="grade[]"]').value.trim(),
        packageType: tr.querySelector('input[name="packageType[]"]').value.trim(),
        packages:    tr.querySelector('input[name="packages[]"]').value.trim(),
        gross:       tr.querySelector('input[name="gross[]"]').value.trim(),
        tare:        tr.querySelector('input[name="tare[]"]').value.trim(),
        net:         tr.querySelector('input[name="net[]"]').value.trim(),
      });
    });

    // base fields
    const fd = new FormData(offForm);
    const base = Object.fromEntries(fd.entries());
    const payload = {
      gardenMark: (base.gardenMark || '').trim(),
      name:       (base.name || '').trim(),
      email:      (base.email || '').trim(),
      phone:      (base.phone || '').trim(),
      contactMe:  base.contactMe === 'yes',
      rows,
      csrf:       base.csrf || '',
      hp:         base.hp || ''
    };

    const emailOkLocal = emailOk(payload.email);
    if (!payload.gardenMark || !payload.name || !emailOkLocal || !payload.phone || !rows.length) {
      showAlert(false, 'Please complete all required fields.');
      return;
    }

    // reCAPTCHA token
    if (offWidgetId !== null && window.grecaptcha) {
      const token = grecaptcha.getResponse(offWidgetId);
      if (!token) { showAlert(false, 'Please complete the reCAPTCHA.'); return; }
      payload['g-recaptcha-response'] = token;
    }

    setLoading(offBtn, true);
    try {
      const res = await fetch(API_OFFER, {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify(payload)
      });
      const out = await res.json().catch(() => ({}));
      if (!res.ok || !out.success) throw new Error(out.error || 'Failed to send offer.');

      showAlert(true, '✅ Thank you — your private sale offer has been sent!');
      offForm.reset();
      tableBody.innerHTML = '';
      addRow();
      if (offWidgetId !== null && window.grecaptcha) {
        grecaptcha.reset(offWidgetId);
        offBtn.disabled = true;
      } else {
        offBtn.disabled = true;
      }
    } catch (err) {
      showAlert(false, err.message || 'Something went wrong.');
    } finally {
      setLoading(offBtn, false);
    }
  });
});