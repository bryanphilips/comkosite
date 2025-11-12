<?php
/* pages/products.php — premium, filterable Tea Varieties */
?>
<link rel="stylesheet" href="/assets/css/products.css">

<section class="page-head">
  <div class="wrap">
    <h1>Tea Varieties</h1>
    <p class="muted">We curate and market a broad portfolio—CTC black, Orthodox, green and specialty teas—supported by clear grades, tasting notes, and market positioning.</p>
  </div>
</section>

<!-- ===== Product Cards ===== -->
<section class="wrap pad">
  <div class="tea-grid" id="teaGrid">

    <!-- CTC BLACK -->
    <article class="tea-card" data-tags="ctc">
      <div class="media">
        <div class="ratio ratio-16x9">
          <?php pictureTag('ctc-black-1','CTC Black'); ?>
        </div>
      </div>
      <div class="body">
        <h3>CTC Black</h3>
        <p class="lead">Our core auction workhorse—clean, bright liquors with brisk cup and good color.</p>
        <ul class="bullets">
          <li><strong>Common Grades:</strong> BP1, PF1, PD, Dust</li>
          <li><strong>Typical Profile:</strong> Bright, brisk, colory; blends well; strong with milk.</li>
          <li><strong>Use Cases:</strong> Mainstream blends, retail packs, food-service.</li>
        </ul>
      </div>
    </article>

    <!-- ORTHODOX BLACK -->
    <article class="tea-card" data-tags="orthodox">
      <div class="media">
        <div class="ratio ratio-16x9">
          <?php pictureTag('orthodox-1','Orthodox Black'); ?>
        </div>
      </div>
      <div class="body">
        <h3>Orthodox Black</h3>
        <p class="lead">Leafy teas with distinct origin character and well-made leaf styles.</p>
        <ul class="bullets">
          <li><strong>Common Grades:</strong> OP, BOP, FOP, GFOP, TGFOP</li>
          <li><strong>Typical Profile:</strong> Aromatic, layered, bright liquor; origin-specific nuance.</li>
          <li><strong>Use Cases:</strong> Specialty retail, premium blends, direct market programs.</li>
        </ul>
      </div>
    </article>

    <!-- GREEN -->
    <article class="tea-card" data-tags="green">
      <div class="media">
        <div class="ratio ratio-16x9">
          <?php pictureTag('green-tea-1','Green Tea'); ?>
        </div>
      </div>
      <div class="body">
        <h3>Green Tea</h3>
        <p class="lead">Clean, fresh greens—pan-fired or steamed styles—with grassy to chestnut notes.</p>
        <ul class="bullets">
          <li><strong>Styles:</strong> Gunpowder, Sencha-style, Fannings</li>
          <li><strong>Typical Profile:</strong> Light body, vegetal, sweet finish; low astringency when well-made.</li>
          <li><strong>Use Cases:</strong> Retail packs, health blends, RTD bases.</li>
        </ul>
      </div>
    </article>

    <!-- SPECIALTY -->
    <article class="tea-card" data-tags="specialty">
      <div class="media">
        <div class="ratio ratio-16x9">
          <?php pictureTag('specialty-1','Specialty Tea'); ?>
        </div>
      </div>
      <div class="body">
        <h3>Specialty & Limited Lots</h3>
        <p class="lead">Distinctive offerings—white, oolong, purple, seasonal marks—curated for connoisseurs.</p>
        <ul class="bullets">
          <li><strong>Examples:</strong> White (silver tips), Oolong, Purple, Hand-made orthodox styles</li>
          <li><strong>Typical Profile:</strong> Unique aromatics, delicate liquor, high cup clarity.</li>
          <li><strong>Use Cases:</strong> Micro-lots, direct retail, brand storytelling.</li>
        </ul>
      </div>
    </article>

  </div>
</section>

<!-- ===== GRADE GLOSSARY (CTC + ORTHODOX) ===== -->
<section class="wrap pad">
  <div class="card shadow-soft">
    <h2 class="center">Tea Grade Glossary</h2>
    <p class="muted center">
      A comprehensive guide to the tea grades marketed and traded through Combrok — reflecting both CTC and Orthodox manufacture.
    </p>

    <div class="grade-table-wrap">
      <table class="grade-table">
        <thead>
          <tr>
            <th class="w-cat">Category</th>
            <th class="w-grade">Grade (Abbr.)</th>
            <th>Full Name & Description</th>
          </tr>
        </thead>
        <tbody>

          <!-- ==================== -->
          <!-- CTC MAIN GRADES -->
          <!-- ==================== -->
          <tr class="section"><td colspan="3">CTC Black — Main Grades</td></tr>
          <tr>
            <td><span class="badge ctc">CTC</span> Main</td>
            <td><strong>BP1</strong></td>
            <td><strong>Broken Pekoe 1</strong> — large even leaf particles producing bright, brisk, full-bodied liquor with clean aroma.</td>
          </tr>
          <tr>
            <td><span class="badge ctc">CTC</span> Main</td>
            <td><strong>PF1</strong></td>
            <td><strong>Pekoe Fanning 1</strong> — granular leaf size, bright color, brisk liquor, and balanced flavor; most common CTC auction grade.</td>
          </tr>
          <tr>
            <td><span class="badge ctc">CTC</span> Main</td>
            <td><strong>PD</strong></td>
            <td><strong>Pekoe Dust</strong> — fine broken particles giving deep color, strong flavor; ideal for milk teas.</td>
          </tr>
          <tr>
            <td><span class="badge ctc">CTC</span> Main</td>
            <td><strong>Dust 1</strong></td>
            <td><strong>Dust 1</strong> — very fine particles offering quick extraction, thick cup, and dark liquor; widely used in tea bags.</td>
          </tr>

          <!-- ==================== -->
          <!-- CTC SECONDARY GRADES -->
          <!-- ==================== -->
          <tr class="section"><td colspan="3">CTC Black — Secondary Grades</td></tr>
          <tr>
            <td><span class="badge ctc">CTC</span> Secondary</td>
            <td><strong>BMF / BMF1 / BMFD</strong></td>
            <td><strong>Broken Mixed Fannings / Broken Mixed Fannings 1 / Broken Mixed Fannings Dust</strong> — dusty by-products adding body and color to blends.</td>
          </tr>
          <tr>
            <td><span class="badge ctc">CTC</span> Secondary</td>
            <td><strong>BP / BP2</strong></td>
            <td><strong>Broken Pekoe / Broken Pekoe 2</strong> — slightly larger broken leaf producing medium body and soft liquor.</td>
          </tr>
          <tr>
            <td><span class="badge ctc">CTC</span> Secondary</td>
            <td><strong>DUST / DUST2</strong></td>
            <td><strong>Dust / Dust 2</strong> — extremely fine cut giving brisk, colory cup; ideal for fast infusion blends.</td>
          </tr>
          <tr>
            <td><span class="badge ctc">CTC</span> Secondary</td>
            <td><strong>FNGS / FNGS1 / FNGS2</strong></td>
            <td><strong>Fine Netted Grainy Secondary</strong> — fine granular grades producing thick, strong liquor and fast brew.</td>
          </tr>
          <tr>
            <td><span class="badge ctc">CTC</span> Secondary</td>
            <td><strong>PD2</strong></td>
            <td><strong>Pekoe Dust 2</strong> — finer version of PD with brisk, intense liquor suited to high-speed infusion.</td>
          </tr>
          <tr>
            <td><span class="badge ctc">CTC</span> Secondary</td>
            <td><strong>PF / PF2</strong></td>
            <td><strong>Pekoe Fanning / Pekoe Fanning 2</strong> — slightly coarser fannings giving balanced cup brightness and color.</td>
          </tr>

          <!-- ==================== -->
          <!-- ORTHODOX MAIN GRADES -->
          <!-- ==================== -->
          <tr class="section"><td colspan="3">Orthodox Black — Main Grades</td></tr>
          <tr>
            <td><span class="badge ortho">Orthodox</span> Main</td>
            <td><strong>OP / OP1</strong></td>
            <td><strong>Orange Pekoe / Orange Pekoe 1</strong> — long, wiry leaf style yielding bright liquor and brisk, classic flavor.</td>
          </tr>
          <tr>
            <td><span class="badge ortho">Orthodox</span> Main</td>
            <td><strong>FOP</strong></td>
            <td><strong>Flowery Orange Pekoe</strong> — long twisted leaf with tip; aromatic and well-balanced liquor.</td>
          </tr>
          <tr>
            <td><span class="badge ortho">Orthodox</span> Main</td>
            <td><strong>GFOP</strong></td>
            <td><strong>Golden Flowery Orange Pekoe</strong> — tippy leaf giving light golden liquor with floral fragrance.</td>
          </tr>
          <tr>
            <td><span class="badge ortho">Orthodox</span> Main</td>
            <td><strong>TGFOP / TGFOP1</strong></td>
            <td><strong>Tippy Golden Flowery Orange Pekoe / No. 1</strong> — high tip content; bright, refined liquor with excellent clarity.</td>
          </tr>
          <tr>
            <td><span class="badge ortho">Orthodox</span> Main</td>
            <td><strong>OPA</strong></td>
            <td><strong>Orange Pekoe A</strong> — bold, open leaf grade producing mellow, soft liquor with mild astringency.</td>
          </tr>
          <tr>
            <td><span class="badge ortho">Orthodox</span> Main</td>
            <td><strong>PEKOE / PEKOE1</strong></td>
            <td><strong>Pekoe / Pekoe 1</strong> — tightly rolled, curly leaf; strong, full-bodied cup with pleasant grip.</td>
          </tr>
          <tr>
            <td><span class="badge ortho">Orthodox</span> Main</td>
            <td><strong>BOP / BOP1</strong></td>
            <td><strong>Broken Orange Pekoe / No. 1</strong> — even broken leaf; bright liquor and medium strength, excellent for blending.</td>
          </tr>
          <tr>
            <td><span class="badge ortho">Orthodox</span> Main</td>
            <td><strong>FBOP</strong></td>
            <td><strong>Flowery Broken Orange Pekoe</strong> — broken leaf with golden tips; rich aroma and bright, brisk liquor.</td>
          </tr>
          <tr>
            <td><span class="badge ortho">Orthodox</span> Main</td>
            <td><strong>FBOPF</strong></td>
            <td><strong>Flowery Broken Orange Pekoe Fanning</strong> — finer tippy particles; aromatic, smooth, strong liquor.</td>
          </tr>
          <tr>
            <td><span class="badge ortho">Orthodox</span> Main</td>
            <td><strong>FBOPFSP</strong></td>
            <td><strong>Flowery Broken Orange Pekoe Fanning Special</strong> — highest tippy content; complex aroma, golden cup, premium lots.</td>
          </tr>
          <tr>
            <td><span class="badge ortho">Orthodox</span> Main</td>
            <td><strong>GFBOP</strong></td>
            <td><strong>Golden Flowery Broken Orange Pekoe</strong> — bright tippy leaf; flavorful liquor with lively character.</td>
          </tr>
          <tr>
            <td><span class="badge ortho">Orthodox</span> Main</td>
            <td><strong>GBOP</strong></td>
            <td><strong>Golden Broken Orange Pekoe</strong> — larger broken leaf; colory, full-bodied cup with depth.</td>
          </tr>
          <tr>
            <td><span class="badge ortho">Orthodox</span> Main</td>
            <td><strong>FP</strong></td>
            <td><strong>Flowery Pekoe</strong> — rolled leaf with some tip; smooth liquor and light floral aroma.</td>
          </tr>

          <!-- ==================== -->
          <!-- ORTHODOX SECONDARY -->
          <!-- ==================== -->
          <tr class="section"><td colspan="3">Orthodox Black — Secondary Grades</td></tr>
          <tr>
            <td><span class="badge ortho">Orthodox</span> Secondary</td>
            <td><strong>OR / ORL</strong></td>
            <td><strong>Ordinary / Ordinary Leaf</strong> — factory-specific secondary orthodox grades, typically made from late infusions or off-sizes; liquor remains bright and drinkable.</td>
          </tr>

        </tbody>
      </table>
    </div>
  </div>
</section>
<!-- ===== CTA ===== -->
<section class="wrap pad cta-wide">
  <div class="cta-row">
    <div>
      <h3>Looking for specific grades or marks?</h3>
      <p class="muted">Tell us your target cup, blend spec, or price band—we’ll propose lots from current catalogues.</p>
    </div>
    <a class="btn" href="/?p=contact">Talk to Our Tea Desk</a>
  </div>
</section>

<script defer src="/assets/js/products.js"></script>