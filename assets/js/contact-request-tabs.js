document.documentElement.classList.add('js');

document.addEventListener('DOMContentLoaded', () => {
    // ----- Fade-in on scroll (for this page)
  const fadeEls = document.querySelectorAll('.fade-in');

  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });

    fadeEls.forEach(el => io.observe(el));
  } else {
    fadeEls.forEach(el => el.classList.add('in-view'));
  }
  const tabs = document.querySelectorAll('.ce-tab');
  const panels = {
    enquiry: document.getElementById('panel-enquiry'),
    request: document.getElementById('panel-request')
  };

  const alertBox = document.getElementById('ceAlert');

  const enquiryForm = document.getElementById('enquiryForm');
  const enquiryBtn  = document.getElementById('enqSubmitBtn');

  const requestForm = document.getElementById('rq-form');
  const requestBtn  = document.getElementById('rq-submit');

  const API_ENQUIRY = '/api/api_enquiry.php';
  const API_REQUEST = '/api/api_request.php';

  const hasRecaptcha = !!document.querySelector('.g-recaptcha') && window.grecaptcha;

  // ----- Alert helpers
  function showAlert(ok, msg) {
    alertBox.classList.remove('d-none', 'alert-success', 'alert-danger');
    alertBox.classList.add(ok ? 'alert-success' : 'alert-danger');
    alertBox.textContent = msg;
    alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
  function hideAlert() {
    alertBox.classList.add('d-none');
    alertBox.textContent = '';
  }

  const emailOk = v => /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v || '');

  // ==========================
  // reCAPTCHA widget IDs
  // ==========================
  let widEnquiry = null;
  let widRequest = null;

  // Render explicitly to avoid getResponse() confusion
  window.ceInitRecaptcha = () => {
    const enqEl = document.querySelector('#panel-enquiry .g-recaptcha');
    const reqEl = document.querySelector('#panel-request .g-recaptcha');

    if (enqEl) {
      widEnquiry = grecaptcha.render(enqEl, {
        sitekey: enqEl.getAttribute('data-sitekey'),
        callback: () => updateButtons(),
        'expired-callback': () => updateButtons()
      });
    }
    if (reqEl) {
      widRequest = grecaptcha.render(reqEl, {
        sitekey: reqEl.getAttribute('data-sitekey'),
        callback: () => updateButtons(),
        'expired-callback': () => updateButtons()
      });
    }

    updateButtons();
  };

  function captchaOk(which) {
    if (!hasRecaptcha) return true;
    const id = which === 'enquiry' ? widEnquiry : widRequest;
    if (id === null) return false;
    return !!grecaptcha.getResponse(id);
  }

  function updateButtons() {
    if (enquiryBtn && enquiryForm) {
      enquiryBtn.disabled = !(captchaOk('enquiry') && enquiryForm.checkValidity());
    }
    if (requestBtn && requestForm) {
      requestBtn.disabled = !(captchaOk('request') && requestForm.checkValidity());
    }
  }

  // Enable/disable when user types
  enquiryForm && enquiryForm.addEventListener('input', updateButtons);
  requestForm && requestForm.addEventListener('input', updateButtons);

  // ----- Tabs
  tabs.forEach(btn => {
    btn.addEventListener('click', () => {
      const tab = btn.dataset.tab;
      tabs.forEach(b => {
        const active = b === btn;
        b.classList.toggle('active', active);
        b.setAttribute('aria-selected', active ? 'true' : 'false');
      });

      Object.keys(panels).forEach(key => {
        const isActive = key === tab;
        panels[key].classList.toggle('active', isActive);
        panels[key].hidden = !isActive;
      });

      hideAlert();
      updateButtons();
    });
  });

  // =====================================================================
  // Enquiry submit
  // =====================================================================
  if (enquiryForm) {
    enquiryForm.addEventListener('reset', () => {
      hideAlert();
      if (hasRecaptcha && widEnquiry !== null) grecaptcha.reset(widEnquiry);
      enquiryForm.classList.remove('was-validated');
      setTimeout(updateButtons, 0);
    });

    enquiryForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      hideAlert();

      if (!enquiryForm.checkValidity()) {
        enquiryForm.classList.add('was-validated');
        showAlert(false, 'Please fill the required fields.');
        updateButtons();
        return;
      }

      const fd = new FormData(enquiryForm);
      const payload = Object.fromEntries(fd.entries());

      payload.name = (payload.name || '').trim();
      payload.email = (payload.email || '').trim();
      payload.message = (payload.message || '').trim();

      if (!payload.name || !emailOk(payload.email) || !payload.message) {
        showAlert(false, 'Please provide a valid name, email and message.');
        updateButtons();
        return;
      }

      if (hasRecaptcha) {
        payload['g-recaptcha-response'] = grecaptcha.getResponse(widEnquiry);
        if (!payload['g-recaptcha-response']) {
          showAlert(false, 'Please complete the reCAPTCHA.');
          updateButtons();
          return;
        }
      }

      enquiryBtn && enquiryBtn.classList.add('loading');
      enquiryBtn && (enquiryBtn.disabled = true);

      try {
        const res = await fetch(API_ENQUIRY, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const out = await res.json().catch(() => ({}));
        if (!res.ok || !out.success) throw new Error(out.error || 'Failed to send enquiry.');

        showAlert(true, '✅ Thank you — your enquiry has been sent!');
        enquiryForm.reset();
      } catch (err) {
        showAlert(false, err.message || 'Something went wrong.');
        // keep captcha state; just refresh button status
        updateButtons();
      } finally {
        enquiryBtn && enquiryBtn.classList.remove('loading');
      }
    });
  }

  // =====================================================================
  // Request submit
  // =====================================================================
  if (requestForm) {
    requestForm.addEventListener('reset', () => {
      hideAlert();
      if (hasRecaptcha && widRequest !== null) grecaptcha.reset(widRequest);
      requestForm.classList.remove('was-validated');
      setTimeout(updateButtons, 0);
    });

    requestForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      hideAlert();

      if (!requestForm.checkValidity()) {
        requestForm.classList.add('was-validated');
        showAlert(false, 'Please fill the required fields.');
        updateButtons();
        return;
      }

      const fd = new FormData(requestForm);
      const payload = Object.fromEntries(fd.entries());

      payload.company = (payload.company || '').trim();
      payload.email = (payload.email || '').trim();
      payload.phone = (payload.phone || '').trim();
      payload.type = (payload.type || '').trim();

      if (!payload.company || !emailOk(payload.email) || !payload.phone || !payload.type) {
        showAlert(false, 'Please complete company, email, phone and dataset type.');
        updateButtons();
        return;
      }

      if (hasRecaptcha) {
        payload['g-recaptcha-response'] = grecaptcha.getResponse(widRequest);
        if (!payload['g-recaptcha-response']) {
          showAlert(false, 'Please complete the reCAPTCHA.');
          updateButtons();
          return;
        }
      }

      requestBtn && requestBtn.classList.add('loading');
      requestBtn && (requestBtn.disabled = true);

      try {
        const res = await fetch(API_REQUEST, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload)
        });
        const out = await res.json().catch(() => ({}));
        if (!res.ok || !out.success) throw new Error(out.error || 'Failed to submit request.');

        showAlert(true, '✅ Thank you — your data request has been submitted!');
        requestForm.reset();
      } catch (err) {
        showAlert(false, err.message || 'Something went wrong.');
        updateButtons();
      } finally {
        requestBtn && requestBtn.classList.remove('loading');
      }
    });
  }

  // Initial button state
  updateButtons();
});