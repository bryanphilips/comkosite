<?php
/* pages/other-services.php — premium alternating layout with fade image cards */
?>
<link rel="stylesheet" href="/assets/css/other-services.css">

<?php
/**
 * Responsive Picture with Lazy Data Srcset
 */
function responsivePicture(string $base, string $alt = '', string $class = ''): void {
  $dir = $_SERVER['DOCUMENT_ROOT'].'/assets/img/';
  $url = '/assets/img/';
  $sizes = [1280, 800, 480];
  $fmts  = ['avif'=>'image/avif','webp'=>'image/webp','jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png'];
  $have  = [];

  foreach ($fmts as $ext => $mime) {
    $srcset = [];
    foreach ($sizes as $w) {
      $f = "{$base}-{$w}.{$ext}";
      if (file_exists($dir.$f)) $srcset[] = "{$url}{$f} {$w}w";
    }
    $single = "{$base}.{$ext}";
    if (file_exists($dir.$single)) $srcset[] = "{$url}{$single} 1280w";
    if ($srcset) $have[$ext] = ['mime'=>$mime,'srcset'=>implode(', ', $srcset)];
  }

  // Fallback for <img src>: prefer JPG/PNG → WEBP → AVIF
  $fallback = null;
  foreach (['jpg','jpeg','png','webp','avif'] as $ext) {
    if (isset($have[$ext])) {
      $fallback = explode(' ', $have[$ext]['srcset'])[0];
      break;
    }
  }
  if (!$fallback) {
    echo '<img alt="" width="1" height="1" style="display:none">';
    return;
  }
  ?>
  <picture class="<?= htmlspecialchars($class) ?>">
    <?php foreach (['avif','webp','jpg','jpeg','png'] as $ext): if(isset($have[$ext])): ?>
      <source data-srcset="<?= htmlspecialchars($have[$ext]['srcset']) ?>" type="<?= htmlspecialchars($have[$ext]['mime']) ?>">
    <?php endif; endforeach; ?>
    <img class="lazy-img"
         src="<?= htmlspecialchars($fallback) ?>"
         alt="<?= htmlspecialchars($alt) ?>"
         loading="lazy" decoding="async"
         sizes="(min-width: 1200px) 900px, (min-width: 768px) 700px, 92vw">
    <noscript><img src="<?= htmlspecialchars($fallback) ?>" alt="<?= htmlspecialchars($alt) ?>"></noscript>
  </picture>
  <?php
}
?>

<section class="page-head">
  <div class="wrap">
    <h1>Other Services</h1>
    <p class="muted">Specialized support that complements our core brokerage.</p>
  </div>
</section>

<section class="section-pad os-alt wrap">

  <!-- PROPERTY MANAGEMENT -->
  <div class="alt-row">
    <div class="alt-media">
      <div class="card soft why-img xfade-rotator">
        <div class="ratio ratio-16x9 xfade-stack">
          <div class="xfade-slide"><?php responsivePicture('tea-house-3','Property image A'); ?></div>
          <div class="xfade-slide"><?php responsivePicture('tea-house-4','Property image B'); ?></div>
          <div class="xfade-slide"><?php responsivePicture('tea-house-2','Property image C'); ?></div>
        </div>
        <div class="xfade-indicators">
          <span class="dot"></span><span class="dot"></span><span class="dot"></span>
        </div>
      </div>
    </div>

    <div class="alt-body">
      <h2 class="alt-title">Property Management</h2>
      <p class="alt-sub">
        We own and manage commercial properties designed for productivity and convenience —
        providing modern, serviced office spaces for tea industry stakeholders and general businesses.
      </p>
      <ul class="premium-bullets">
        <li><strong>Prime Locations:</strong> Easily accessible office blocks within key commercial zones.</li>
        <li><strong>Leasing Services:</strong> Flexible tenancy terms, reliable utilities, and full facility maintenance.</li>
        <li><strong>Professional Management:</strong> Security, cleanliness, and responsive tenant support.</li>
      </ul>
    </div>
  </div>

  <!-- CONSULTANCY -->
  <div class="alt-row alt-reverse">
    <div class="alt-media">
      <div class="card soft why-img xfade-rotator">
        <div class="ratio ratio-16x9 xfade-stack">
          <div class="xfade-slide"><?php responsivePicture('p_close-up','Consultancy A'); ?></div>
          <div class="xfade-slide"><?php responsivePicture('factory','Consultancy B'); ?></div>
          <div class="xfade-slide"><?php responsivePicture('tea_roasting','Consultancy C'); ?></div>
        </div>
        <div class="xfade-indicators">
          <span class="dot"></span><span class="dot"></span><span class="dot"></span>
        </div>
      </div>
    </div>

    <div class="alt-body">
      <h2 class="alt-title">Consultancy</h2>
      <p class="alt-sub">
        Our in-house specialists offer analytical, data-driven guidance in tea valuation, data interpretation,
        and market trend forecasting — helping producers, buyers, and investors make smarter decisions.
      </p>
      <ul class="premium-bullets">
        <li><strong>Tea Data Interpretation:</strong> Turning auction datasets into actionable insights.</li>
        <li><strong>Valuation Frameworks:</strong> Using quality-indexed benchmarks tied to market performance.</li>
        <li><strong>Training & Strategy:</strong> Guiding factories on yield optimization and market positioning.</li>
      </ul>
    </div>
  </div>
</section>

<script defer src="/assets/js/other-services.js"></script>