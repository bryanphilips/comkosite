document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('rq-form');
  const btn = document.getElementById('rq-submit');
  const alertBox = document.getElementById('rq-alert');
  const hasRecaptcha = !!document.querySelector('.g-recaptcha');
  const API_URL = '/api/api_request.php';

  // reCAPTCHA hooks
  window.onRecaptcha = () => { btn.disabled = false; };
  window.onRecaptchaExpired = () => { btn.disabled = true; };

  // helpers
  const emailOk = v => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v);

  function showAlert(ok, msg) {
    alertBox.className = 'rq-alert ' + (ok ? 'ok' : 'err');
    alertBox.textContent = msg;
    alertBox.classList.remove('hidden');
    alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
    if (ok) setTimeout(() => alertBox.classList.add('hidden'), 7000);
  }
  function clearAlert() {
    alertBox.className = 'rq-alert hidden';
    alertBox.textContent = '';
  }
  function setLoading(state) {
    btn.classList.toggle('loading', state);
    btn.disabled = state;
  }

  // submit
  form.addEventListener('submit', async e => {
    e.preventDefault();
    clearAlert();

    // basic validity
    const fd = new FormData(form);
    const data = Object.fromEntries(fd.entries());

    data.company = (data.company || '').trim();
    data.email   = (data.email || '').trim();
    data.phone   = (data.phone || '').trim();
    data.type    = (data.type || '').trim();
    data.notes   = (data.notes || '').trim();

    if (!data.company || !emailOk(data.email) || !data.phone || !data.type) {
      showAlert(false, 'Please fill in all required fields.');
      return;
    }

    // recaptcha check
    if (hasRecaptcha && window.grecaptcha) {
      data['g-recaptcha-response'] = grecaptcha.getResponse();
      if (!data['g-recaptcha-response']) {
        showAlert(false, 'Please verify you’re not a robot.');
        return;
      }
    }

    setLoading(true);
    try {
      const res = await fetch(API_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      const out = await res.json().catch(() => ({}));
      if (!res.ok || !out.success) throw new Error(out.error || 'Failed to send request.');

      // ✅ success
      showAlert(true, '✅ Thank you — your data request has been submitted!');
      form.reset();

      // disable again until recaptcha solved
      if (hasRecaptcha && window.grecaptcha) {
        grecaptcha.reset();
        btn.disabled = true;
      } else {
        btn.disabled = true;
      }
    } catch (err) {
      showAlert(false, err.message || 'Something went wrong.');
    } finally {
      setLoading(false);
    }
  });

  // enable button when valid (if no recaptcha)
  form.addEventListener('input', () => {
    if (!hasRecaptcha) {
      btn.disabled = !form.checkValidity();
    }
  });

  form.addEventListener('reset', () => {
    clearAlert();
    btn.disabled = true;
  });
});