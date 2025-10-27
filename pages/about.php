<?php
/* pages/about.php — animated */
?>
<section class="wrap grid-2 pad">
  <div class="card shadow reveal image-rotator" style="--d:120ms">
    <?php 
      // iterate between two images dynamically
      $bgImages = ['new-tea-house-1', 'new-tea-house-2','tea-house-4']; 
      foreach ($bgImages as $img): ?>
        <div class="rotating-img">
          <?php pictureTag($img, 'About Combrok'); ?>
        </div>
    <?php endforeach; ?>
  </div>

  <div class="reveal" style="--d:200ms">
    <h2>Background Information</h2>
    <p>
      COMBROK Limited is among the pioneer tea broking firms at the Mombasa Tea Auction.
      Incorporated in 1978, we have delivered uninterrupted brokerage services for more than
      four decades—representing producers across Kenya, Uganda, Rwanda, Burundi, Tanzania,
      Madagascar, Mozambique, Malawi, DRC, and Ethiopia.
    </p>
    <p>
      We handle a substantial weekly portfolio and a broad spectrum of tea grades while
      maintaining our hallmarks: adaptability, integrity, excellence, and consistency.
      Our role spans end-to-end auction representation, professional tasting, technical
      advisory, and timely settlement.
    </p>
  </div>
</section>

<!-- Mission / Vision / Values -->
<section class="wrap pad">
  <div class="cards">
    <article class="card hover reveal" style="--d:80ms">
      <h3 class="with-icon">
        <span class="i">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5c5.5 0 9.5 4.5 10.7 6.2a1.5 1.5 0 0 1 0 1.6C21.5 14.5 17.5 19 12 19S2.5 14.5 1.3 12.8a1.5 1.5 0 0 1 0-1.6C2.5 9.5 6.5 5 12 5zm0 3.5A5.5 5.5 0 1 0 17.5 14 5.5 5.5 0 0 0 12 8.5zm0 2.5a3 3 0 1 1-3 3 3 3 0 0 1 3-3z" fill="currentColor"/></svg>
        </span>
        Vision
      </h3>
      <p>To be the most reliable and trusted tea brokerage firm in Africa, recognized globally
        for our adaptability, excellence, and commitment to advancing the tea trade.</p>
    </article>

    <article class="card hover reveal" style="--d:160ms">
      <h3 class="with-icon">
        <span class="i">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 .001 20.001A10 10 0 0 0 12 2zm3.9 6.1-2.8 6.4a1 1 0 0 1-.6.6l-6.4 2.8 2.8-6.4a1 1 0 0 1 .6-.6l6.4-2.8zM11 11l2 2" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </span>
        Mission
      </h3>
      <p>To deliver trusted and innovative tea brokerage services by connecting tea trade
        stakeholders through integrity, quality, and clarity—promoting sustainable value,
        reliable market intelligence, and long-term partnerships across global tea markets.</p>
    </article>

    <article class="card hover reveal" style="--d:240ms">
      <h3 class="with-icon">
        <span class="i">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 4c3 0 5 2 5 5-3 0-5-2-5-5zm10 0c-3 0-5 2-5 5 3 0 5-2 5-5zM12 13c-4.4 0-8 2.7-8 6h16c0-3.3-3.6-6-8-6z" fill="currentColor"/></svg>
        </span>
        Our Values
      </h3>
      <ul class="bullets">
        <li>Integrity</li><li>Excellence</li><li>Adaptability</li>
        <li>Sustainability</li><li>Collaboration</li>
      </ul>
    </article>
  </div>
</section>

<?php
function hasOrgChartImage() {
  $base = $_SERVER['DOCUMENT_ROOT'].'/assets/img/';
  foreach (['org-chart.avif','org-chart.webp','org-chart.png','org-chart.jpg'] as $f) {
    if (file_exists($base.$f)) return '/assets/img/'.$f;
  }
  return null;
}
$orgImg = hasOrgChartImage();
?>

<section class="wrap pad">
  <h2>COMBROK ORGANIZATIONAL STRUCTURE</h2>
  <p class="muted" style="margin-top:-6px ">Combrok Limited Organization Chart</p>

  <?php if ($orgImg): ?>
    <div class="card shadow reveal" style="--d:80ms">
      <img src="<?= htmlspecialchars($orgImg) ?>" alt="Combrok Organization Chart" style="width:100%;height:auto;border-radius:var(--radius-xl)"/>
    </div>
  <?php endif; ?>

  <div class="org card shadow">
    <!-- Board -->
    <div class="org-row">
      <div class="org-node reveal" style="--d:60ms">
        <div class="org-title">Board of Directors</div>
        <p class="org-note">Governance &amp; strategic oversight</p>
      </div>
    </div>

    <div class="org-vline line-vert reveal-line" style="--d:140ms"></div>

    <!-- Management -->
    <div class="org-row">
      <div class="org-node reveal" style="--d:200ms">
        <div class="org-title">Management</div>
        <p class="org-note">Execution, performance &amp; stakeholder alignment</p>
      </div>
    </div>

    <div class="org-vline line-vert reveal-line" style="--d:260ms"></div>

    <!-- Departments (same level) -->
    <div class="org-grid">
      <!-- Finance & IT -->
      <div class="dept">
        <div class="org-node reveal" style="--d:320ms">
          <div class="org-chip">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16v4H4zM4 12h10v6H4zM16 12h4v6h-4z" fill="currentColor"/></svg>
            Finance &amp; IT
          </div>
        </div>
        <div class="stub-vert line-vert reveal-line" style="--d:380ms"></div>
        <span class="org-pill reveal-pop" style="--d:440ms">Bank Data Management</span>
      </div>

      <!-- Centered connector between departments -->
      <div class="stub-h line-horz reveal-line" style="--d:360ms"></div>

      <!-- Operations -->
      <div class="dept">
        <div class="org-node reveal" style="--d:400ms">
          <div class="org-chip">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 3h4l1 2h3v3l2 1v4l-2 1v3h-3l-1 2h-4l-1-2H6v-3l-2-1V9l2-1V5h3l1-2zm2 6a3 3 0 1 0 .001 6.001A3 3 0 0 0 12 9z" fill="currentColor"/></svg>
            Operations
          </div>
        </div>
        <div class="stub-vert line-vert reveal-line" style="--d:460ms"></div>
        <span class="org-pill reveal-pop" style="--d:520ms">Sampling &amp; Tasting</span>
      </div>

      <!-- Centered connector between departments -->
      <div class="stub-h line-horz reveal-line" style="--d:420ms"></div>

      <!-- Sales & Marketing -->
      <div class="dept">
        <div class="org-node reveal" style="--d:480ms">
          <div class="org-chip">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12l8-5v10l-8-5zm9 2h3l4 3v-8l-4 3h-3v2zm-5 4h2v2H7z" fill="currentColor"/></svg>
            Sales &amp; Marketing
          </div>
        </div>
        <div class="stub-vert line-vert reveal-line" style="--d:540ms"></div>
        <span class="org-pill reveal-pop" style="--d:600ms">Buyers &amp; Producers</span>
      </div>
    </div>
  </div>
</section>

<section class="wrap pad cta-wide">
  <div class="cta-row reveal" style="--d:120ms">
    <div>
      <h3>Discover our end-to-end brokerage approach</h3>
      <p class="muted">From sampling and tasting to auction representation, settlement, and market intelligence.</p>
    </div>
    <a class="btn" href="/?p=services">Explore Services</a>
  </div>
</section>

<style>
  /* icons next to headings */
  .with-icon{display:flex;align-items:center;gap:.55rem}
  .with-icon .i{width:28px;height:28px;border-radius:999px;display:flex;align-items:center;justify-content:center;background:var(--brand-50,#eaf6ff);color:var(--brand-600,#0a68a0);box-shadow:0 6px 18px rgba(0,0,0,.06)}
  .with-icon .i svg{width:16px;height:16px}

  /* base org styles */
  .org{border-radius:18px;padding:22px 14px;background:linear-gradient(180deg,#fff,rgba(255,255,255,.96))}
  .org-row{display:flex;justify-content:center;align-items:center;margin:10px 0}
  .org-node{background:#fff;border-radius:18px;padding:14px 16px;text-align:center;min-width:230px;box-shadow:0 6px 18px rgba(0,0,0,.06)}
  .org-title{font-weight:700;margin-bottom:6px}
  .org-note{margin:0;color:#5b6b7a;font-size:.95rem}
  .org-vline{width:2px;height:22px;margin:0 auto;background:linear-gradient(to bottom,#9dd5f3,#0a68a0);border-radius:2px}

  /* department grid with centered tiny connectors */
  .org-grid{
    --stub-w:42px;
    display:grid;
    grid-template-columns:1fr var(--stub-w) 1fr var(--stub-w) 1fr;
    align-items:start; justify-items:center; gap:16px; margin-top:8px
  }
  .dept{display:flex;flex-direction:column;align-items:center}
  .stub-h{width:100%;height:2px;border-radius:2px;align-self:center; margin-top:75px}
  .stub-vert{width:2px;height:18px;margin:8px 0;border-radius:2px}
  .org-chip{display:inline-flex;gap:8px;align-items:center;justify-content:center;padding:6px 12px;border-radius:999px;background:#eaf6ff;color:#084b72;font-weight:600}
  .org-chip svg{width:18px;height:18px}
  .org-pill{display:inline-block;padding:6px 12px;border-radius:999px;background:#f6f9fb;color:#334b5f;border:1px solid #e6edf3;white-space:nowrap}

  /* line color tokens */
  .line-vert{background:linear-gradient(to bottom,#9dd5f3,#0a68a0)}
  .line-horz{background:linear-gradient(to right,#7ec0e3,#0a68a0)}

  /* ===== Animations ===== */
  .reveal{opacity:0;transform:translateY(14px);transition:opacity .6s ease, transform .6s ease; transition-delay: var(--d,0ms);}
  .reveal._in{opacity:1;transform:none}
  .reveal-line{transform-origin:center center;transition:transform .6s ease;opacity:0;transition-delay: var(--d,0ms)}
  .line-horz{transform:scaleX(0)}
  .line-vert{transform-origin:top center; transform:scaleY(0)}
  .reveal-line._in{opacity:1}
  .reveal-line._in.line-horz{transform:scaleX(1)}
  .reveal-line._in.line-vert{transform:scaleY(1)}
  .reveal-pop{opacity:0;transform:scale(.96);transition:opacity .4s ease, transform .4s ease; transition-delay: var(--d,0ms)}
  .reveal-pop._in{opacity:1;transform:scale(1)}

  @media(max-width:900px){
    .org-grid{grid-template-columns:1fr;gap:18px}
    .stub-h{display:none}
  }

  /* ── Background image rotator (single visible) ─────────── */
  .image-rotator{position:relative;overflow:hidden;border-radius:var(--radius-xl);min-height:360px;height:100%}
  .image-rotator .rotating-img{position:absolute;inset:0;opacity:0;visibility:hidden;transition:opacity 1s ease-in-out,visibility 0s linear 1s}
  .image-rotator .rotating-img.active{opacity:1;visibility:visible;transition:opacity 1s ease-in-out}
  .image-rotator img{width:100%;height:100%;object-fit:cover;border-radius:var(--radius-xl)}

  @media (prefers-reduced-motion: reduce){
    .image-rotator .rotating-img{transition:none !important}
  }
</style>

<script>
  // One-time animations for non-org items
  (function(){
    const onceIO = new IntersectionObserver((entries)=>{
      entries.forEach(ent=>{
        if(ent.isIntersecting){
          ent.target.classList.add('_in');
          onceIO.unobserve(ent.target);
        }
      });
    }, { threshold: 0.18 });

    document.querySelectorAll(':not(.org) > .reveal, :not(.org) > .reveal-line, :not(.org) > .reveal-pop')
      .forEach(el => onceIO.observe(el));
  })();

  // Re-trigger animations for ORG CHART on every enter/exit
  (function(){
    const org = document.querySelector('.org');
    if(!org) return;

    const repeatIO = new IntersectionObserver((entries)=>{
      entries.forEach(ent=>{
        const el = ent.target;
        if(ent.isIntersecting){ el.classList.add('_in'); }
        else{
          el.classList.remove('_in');
          if(el.classList.contains('reveal-line') || el.classList.contains('reveal') || el.classList.contains('reveal-pop')){
            void el.offsetWidth; // reflow to reset transition
          }
        }
      });
    }, { threshold: 0.35 });

    org.querySelectorAll('.reveal, .reveal-line, .reveal-pop, .stub-h, .stub-vert')
      .forEach(el => repeatIO.observe(el));
  })();

  // Background image rotator: 2s fade, single visible slide
  (function(){
    const rotator = document.querySelector('.image-rotator');
    if(!rotator) return;

    const slides = rotator.querySelectorAll('.rotating-img');
    if(slides.length < 2) { slides[0]?.classList.add('active'); return; }

    let index = 0;
    let playing = true;
    const INTERVAL = 2000; // 2 seconds

    slides[index].classList.add('active');

    function next(){
      if(!playing) return;
      slides[index].classList.remove('active');
      index = (index + 1) % slides.length;
      slides[index].classList.add('active');
    }

    setInterval(next, INTERVAL);

    // Pause when off-screen
    const io = new IntersectionObserver((entries)=>{
      entries.forEach(ent => playing = ent.isIntersecting);
    }, { threshold: 0.15 });
    io.observe(rotator);
  })();
</script>