document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('enquiryForm');
  const btn = document.getElementById('submitBtn');
  const alertBox = document.getElementById('formAlert');
  const API_URL = '/api/api_enquiry.php';
  const hasRecaptcha = !!document.querySelector('.g-recaptcha');

  window.onRecaptcha = () => { btn.disabled = false; };
  window.onRecaptchaExpired = () => { btn.disabled = true; };

  function showAlert(ok, msg) {
    alertBox.className = 'alert ' + (ok ? 'alert-success' : 'alert-danger');
    alertBox.textContent = msg;
    alertBox.classList.remove('d-none');
    setTimeout(() => alertBox.classList.add('d-none'), 7000);
  }
  function clearAlert() {
    alertBox.className = 'alert d-none';
    alertBox.textContent = '';
  }

  function setLoading(state) {
    btn.classList.toggle('is-loading', state);
    btn.disabled = state || btn.disabled;
  }

  const emailOk = v => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v);

  form.addEventListener('submit', async e => {
    e.preventDefault();
    clearAlert();

    const fd = new FormData(form);
    const data = Object.fromEntries(fd.entries());

    data.name = (data.name || '').trim();
    data.email = (data.email || '').trim();
    data.tel = (data.tel || '').trim();
    data.topic = (data.topic || 'General').trim();
    data.message = (data.message || '').trim();

    if (!data.name || !emailOk(data.email) || !data.message) {
      showAlert(false, 'Please fill in your name, valid email, and message.');
      return;
    }

    // reCAPTCHA
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

      if (!res.ok || !out.success) {
        throw new Error(out.error || 'Failed to send. Please try again.');
      }

      showAlert(true, '✅ Thank you — your enquiry has been sent!');
      form.reset();
      if (hasRecaptcha && window.grecaptcha) {
        grecaptcha.reset();
        btn.disabled = true;
      }
    } catch (err) {
      showAlert(false, err.message);
    } finally {
      setLoading(false);
    }
  });

  form.addEventListener('input', () => {
    if (!hasRecaptcha) btn.disabled = !form.checkValidity();
  });

  form.addEventListener('reset', () => {
    clearAlert();
    btn.disabled = hasRecaptcha; // keep disabled until recaptcha solved again
  });
});