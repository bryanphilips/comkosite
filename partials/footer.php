<?php
/* partials/footer.php — Premium Bootstrap footer in brand blue */
?>
<footer class="site-footer py-5 mt-5" style="background:#0A4E73; color:#EAF6FC;">
  <div class="container">
    <div class="row gy-4 justify-content-between align-items-start">

      <!-- Follow / Social -->
      <div class="col-md-6 col-lg-5">
        <h5 class="fw-bold mb-3 text-uppercase" style="color:#69B8E3;">Follow Us</h5>
        <ul class="list-inline social mb-0 d-flex flex-wrap gap-2">
          <?php if (!empty(SOCIAL_X)): ?>
            <li class="list-inline-item">
              <a href="<?= htmlspecialchars(SOCIAL_X, ENT_QUOTES, 'UTF-8') ?>"
                 class="social-link"
                 target="_blank" rel="noopener" aria-label="X (Twitter)" title="X">
                 <i class="bi bi-twitter-x"></i>
              </a>
            </li>
          <?php endif; ?>

          <?php if (!empty(SOCIAL_LINKEDIN)): ?>
            <li class="list-inline-item">
              <a href="<?= htmlspecialchars(SOCIAL_LINKEDIN, ENT_QUOTES, 'UTF-8') ?>"
                 class="social-link"
                 target="_blank" rel="noopener" aria-label="LinkedIn" title="LinkedIn">
                 <i class="bi bi-linkedin"></i>
              </a>
            </li>
          <?php endif; ?>

          <?php if (!empty(SOCIAL_FACEBOOK)): ?>
            <li class="list-inline-item">
              <a href="<?= htmlspecialchars(SOCIAL_FACEBOOK, ENT_QUOTES, 'UTF-8') ?>"
                 class="social-link"
                 target="_blank" rel="noopener" aria-label="Facebook" title="Facebook">
                 <i class="bi bi-facebook"></i>
              </a>
            </li>
          <?php endif; ?>

          <?php if (!empty(SOCIAL_INSTAGRAM)): ?>
            <li class="list-inline-item">
              <a href="<?= htmlspecialchars(SOCIAL_INSTAGRAM, ENT_QUOTES, 'UTF-8') ?>"
                 class="social-link"
                 target="_blank" rel="noopener" aria-label="Instagram" title="Instagram">
                 <i class="bi bi-instagram"></i>
              </a>
            </li>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Contact -->
      <div class="col-md-6 col-lg-5">
        <h5 class="fw-bold mb-3 text-uppercase" style="color:#69B8E3;">Contact</h5>
        <?php $emailSafe = htmlspecialchars(NOTIFY_TO_EMAIL ?? '', ENT_QUOTES, 'UTF-8'); ?>
        <p class="mb-1">
          <i class="bi bi-envelope me-2" style="color:#7FC3E7;"></i>
          <?= $emailSafe !== '' ? "<a href='mailto:$emailSafe' class='text-decoration-none' style='color:#EAF6FC;'>$emailSafe</a>" : '—' ?>
        </p>
        <p class="mb-0"><i class="bi bi-clock me-2" style="color:#7FC3E7;"></i>Mon–Fri 9:00–17:00</p>
      </div>
    </div>

    <hr class="opacity-25 my-4" style="border-color:#69B8E3;">

    <div class="text-center small" style="color:#CFEAF7;">
      <p class="mb-1">© <?= date('Y') ?> <?= htmlspecialchars(APP_NAME ?? 'Combrok Limited', ENT_QUOTES, 'UTF-8') ?> · All rights reserved.</p>
      <a href="/?p=contact" class="text-decoration-none" style="color:#69B8E3;">Contact Us</a>
    </div>
  </div>
</footer>

<!-- Bootstrap Icons -->
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
/>

<!-- Footer Styles -->
<style>
  .social-link {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:42px; height:42px;
    border-radius:50%;
    background:rgba(255,255,255,.1);
    color:#EAF6FC;
    border:1px solid rgba(255,255,255,.2);
    transition:all .25s ease;
  }
  .social-link:hover {
    background:#69B8E3;
    color:#0A4E73;
    transform:translateY(-2px);
  }
</style>

<!-- Bootstrap JS Bundle -->
<script
  src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
  crossorigin="anonymous"
></script>

</body>
</html>