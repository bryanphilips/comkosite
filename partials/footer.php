<?php
/* partials/footer.php */
?>
<footer class="site-footer">
  <div class="wrap">
    <div class="footer-cols">
      <div>
        <h4>Follow</h4>
        <ul class="social">
          <?php if (!empty(SOCIAL_X)): ?>
            <li>
              <a href="<?= htmlspecialchars(SOCIAL_X, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" aria-label="X (Twitter)" title="X">
                <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3h4.6l5.1 6 5.7-6H21l-7.3 8.1L21 21h-4.6l-5.5-6.5L4.8 21H3l7.6-8.5L3 3z" fill="currentColor"/></svg>
              </a>
            </li>
          <?php endif; ?>

          <?php if (!empty(SOCIAL_LINKEDIN)): ?>
            <li>
              <a href="<?= htmlspecialchars(SOCIAL_LINKEDIN, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" aria-label="LinkedIn" title="LinkedIn">
                <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M4.98 3.5A2.5 2.5 0 1 1 5 8.5a2.5 2.5 0 0 1-.02-5zM3 9h4v12H3zM10 9h3.8v1.7h.1c.5-.9 1.7-1.8 3.4-1.8 3.6 0 4.3 2.3 4.3 5.2V21h-4v-5.3c0-1.3 0-3-1.9-3s-2.1 1.4-2.1 2.9V21h-4z" fill="currentColor"/></svg>
              </a>
            </li>
          <?php endif; ?>

          <?php if (!empty(SOCIAL_FACEBOOK)): ?>
            <li>
              <a href="<?= htmlspecialchars(SOCIAL_FACEBOOK, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" aria-label="Facebook" title="Facebook">
                <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M13 22v-8h3l1-4h-4V8c0-1.2.3-2 2-2h2V2h-3c-3 0-5 1.8-5 5v3H6v4h3v8h4z" fill="currentColor"/></svg>
              </a>
            </li>
          <?php endif; ?>

          <?php if (!empty(SOCIAL_INSTAGRAM)): ?>
            <li>
              <a href="<?= htmlspecialchars(SOCIAL_INSTAGRAM, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" aria-label="Instagram" title="Instagram">
                <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2a3 3 0 0 0-3 3v10a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3V7a3 3 0 0 0-3-3H7zm5 3.5a5.5 5.5 0 1 1 0 11 5.5 5.5 0 0 1 0-11zm0 2A3.5 3.5 0 1 0 12 17a3.5 3.5 0 0 0 0-7zm5.25-2.75a1 1 0 1 1 0 2 1 1 0 0 1 0-2z" fill="currentColor"/>
                </svg>
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>

      <div>
        <h4>Contact</h4>
        <?php
          $emailSafe = htmlspecialchars(NOTIFY_TO_EMAIL ?? '', ENT_QUOTES, 'UTF-8');
        ?>
        <p>Email: <?= $emailSafe !== '' ? $emailSafe : '—' ?><br>Mon–Fri 9:00–17:00</p>
      </div>
    </div>

    <p>© <?= date('Y') ?> <?= htmlspecialchars(APP_NAME ?? 'Combrok Limited', ENT_QUOTES, 'UTF-8') ?> · All rights reserved.</p>
  </div>
</footer>
<script src="/assets/js/main.js" defer></script>
</body>
</html>