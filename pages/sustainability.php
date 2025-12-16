<?php
require_once __DIR__ . '/../config.php';
?>
<link rel="stylesheet" href="/assets/css/sustainability.css">

<section class="hero-sust" style="background-image:url('/assets/images/sust-hero.webp')">
  <div class="hero-overlay"></div>
  <div class="wrap hero-inner">

    <div class="hero-layout">
      <!-- LEFT: Copy & Pills -->
      <div class="hero-copy">
        <h1>Tea Sustainability &amp; Shared Value</h1>
        <p class="hero-tagline">
          Building a resilient tea value chain where farmers, producers and buyers
          all benefit — without compromising our climate or communities.
        </p>
        <div class="hero-pills">
          <span class="pill">Fair Pricing</span>
          <span class="pill">Farmer Empowerment</span>
          <span class="pill">Climate Action</span>
          <span class="pill">Transparent Trading</span>
        </div>
      </div>

      <!-- RIGHT: Awards Slider -->
      <aside class="hero-awards" aria-label="Awards and recognition">
        <div class="hero-awards-head">
         
          <h2>Awards &amp; Recognition</h2>
        </div>

        <div class="hero-awards-slider" data-award-slider>
          <!-- Slide 1 -->
          <article class="award-slide active">
            <div class="award-image">
              <?php pictureTag('best_broker_2024', 'Best Priced Broker 2025 trophy'); ?>
            </div>
            <div class="award-body">
              <h3>Best Priced Broker 2025</h3>
              <p>
                Recognised for consistently delivering fair and transparent prices to producers and buyers at the Mombasa Tea Auction.
              </p>
              <p class="award-meta">
                <span>Award:</span> Best Priced Broker 2025
              </p>
              <button type="button" class="award-more" data-award-more>More</button>
            </div>
          </article>

          <!-- Slide 2 -->
          <article class="award-slide">
            <div class="award-image">
              <?php pictureTag('best_broker_2024', 'Farmer empowerment award'); ?>
            </div>
            <div class="award-body">
              <h3>Farmer Empowerment Award 2025</h3>
              <p>
                Commended for empowering smallholder farmer groups with clear market insights, quality feedback and long-term relationship building.
              </p>
              <p class="award-meta">
                <span>Award:</span> Farmer Empowerment 2025
              </p>
              <button type="button" class="award-more" data-award-more>More</button>
            </div>
          </article>

          <!-- Slide 3 -->
          <article class="award-slide">
            <div class="award-image">
              <?php pictureTag('best_broker_2024', 'Climate-smart tea initiative award'); ?>
            </div>
            <div class="award-body">
              <h3>Climate-Smart Tea Initiative 2025</h3>
              <p>
                Highlighted for promoting climate-smart practices and traceable private sales that align with ESG expectations of global buyers.
              </p>
              <p class="award-meta">
                <span>Award:</span> Climate-Smart Tea 2025
              </p>
              <button type="button" class="award-more" data-award-more>More</button>
            </div>
          </article>
        </div>

        <!-- Slider dots -->
        <div class="hero-awards-dots" data-award-dots>
          <button type="button" class="dot active" aria-label="Go to award 1"></button>
          <button type="button" class="dot" aria-label="Go to award 2"></button>
          <button type="button" class="dot" aria-label="Go to award 3"></button>
        </div>
      </aside>
    </div>

  </div>
</section>

<section class="wrap section-pad sust-intro">
  <div class="section-head">
    <h2>Our Sustainability Approach</h2>
    <p>
      Sustainability at Combrok is not a side project — it sits at the heart of how
      we broker tea. From equitable pricing and climate resilience to farmer empowerment
      and responsible data, each auction we support is designed to leave the value chain
      better than we found it.
    </p>
  </div>

  <div class="pillars-grid">
    <article class="pillar-card fade-in">
      <div class="pill-icon">
        <?php pictureTag('catalogue', 'Tea pricing sustainability'); ?>
      </div>
      <h3>Fair &amp; Transparent Pricing</h3>
      <p>
        We promote auction processes that highlight quality, origin and traceability,
        empowering producers to secure competitive prices and helping buyers access
        teas that meet their specification — at a clearly understood value.
      </p>
      <ul>
        <li>Pricing linked to quality, grade and origin.</li>
        <li>Clear view of weekly market movements.</li>
        <li>Support for long-term buyer–producer relationships.</li>
      </ul>
    </article>

    <article class="pillar-card fade-in">
      <div class="pill-icon">
        <?php pictureTag('p_close-up', 'Farmer empowerment'); ?>
      </div>
      <h3>Farmer Empowerment &amp; Growth</h3>
      <p>
        We believe better information leads to better decisions on the farm. We translate
        auction trends into simple insights that support smallholder farmers and estates
        to improve husbandry, quality and income stability.
      </p>
      <ul>
        <li>Explaining market trends in farmer-friendly language.</li>
        <li>Encouraging quality improvements from plucking to factory.</li>
        <li>Highlighting value-add opportunities across the chain.</li>
      </ul>
    </article>

    <article class="pillar-card fade-in">
      <div class="pill-icon">
        <?php pictureTag('plantation_sunrise', 'Climate and environment in tea'); ?>
      </div>
      <h3>Climate &amp; Environment</h3>
      <p>
        Tea is highly climate-sensitive. We collaborate with producers and partners to
        surface climate-smart practices that protect soils, water sources and biodiversity
        while maintaining cup quality and consistency.
      </p>
      <ul>
        <li>Encouraging climate-smart field and factory practices.</li>
        <li>Prioritising sustainably certified and traceable teas.</li>
        <li>Reducing waste in logistics and documentation.</li>
      </ul>
    </article>

    <article class="pillar-card fade-in">
      <div class="pill-icon">
        <?php pictureTag('valuation', 'Responsible tea trading'); ?>
      </div>
      <h3>Responsible &amp; Compliant Trading</h3>
      <p>
        We align with regulatory and ethical standards, supporting traceability,
        digital documentation and responsible private-sale structures that protect
        all parties in the value chain.
      </p>
      <ul>
        <li>Compliance with market and tax regulations.</li>
        <li>Transparent documentation for audits and reviews.</li>
        <li>Support for responsible private sale structures.</li>
      </ul>
    </article>
  </div>
</section>

<section class="wrap section-pad impact-section">
  <div class="section-head">
    <h2>Measuring Our Impact</h2>
    <p>
      Sustainability must be measurable. We continuously track how our work supports
      farmers, producers and buyers across key indicators.
    </p>
  </div>

  <div class="impact-grid">
    <article class="impact-card fade-in">
      <h3>Farmers</h3>
      <p class="impact-stat" data-target="15000">0</p>
      <p class="impact-label">Smallholder farmers indirectly supported through our brokered teas and insights.</p>
      <ul>
        <li>Better visibility of price trends.</li>
        <li>Encouragement towards quality-focused agronomy.</li>
        <li>Improved predictability of income over time.</li>
      </ul>
    </article>

    <article class="impact-card fade-in">
      <h3>Producers</h3>
      <p class="impact-stat" data-target="85">0</p>
      <p class="impact-label">Factories and producers benefiting from transparent auction representation.</p>
      <ul>
        <li>Weekly performance analytics and feedback.</li>
        <li>Support for certification-linked marketing.</li>
        <li>Better positioning of specialty and orthodox lines.</li>
      </ul>
    </article>

    <article class="impact-card fade-in">
      <h3>Buyers</h3>
      <p class="impact-stat" data-target="70">0</p>
      <p class="impact-label">International and regional buyers engaged on sustainable tea sourcing.</p>
      <ul>
        <li>Curated access to traceable sustainable teas.</li>
        <li>Consistent quality communication and cupping notes.</li>
        <li>Long-term relationships over one-off trades.</li>
      </ul>
    </article>
  </div>
</section>

<section class="wrap section-pad initiatives-section">
  <div class="section-head">
    <h2>Key Sustainability Initiatives</h2>
    <p>
      Our initiatives are built in partnership with producers, farmer organisations
      and buyers to ensure each action has a real-world impact.
    </p>
  </div>

  <div class="initiatives-grid">
    <article class="initiative-card fade-in">
      <div class="initiative-media">
        <?php pictureTag('tea_roasting', 'Field and factory quality trainings'); ?>
      </div>
      <div class="initiative-body">
        <h3>Field &amp; Factory Quality Clinics</h3>
        <p>
          Joint sessions with producers and farmer representatives, translating auction
          feedback into practical improvements in plucking, withering, rolling and firing.
        </p>
        <ul>
          <li>Focus on grades with strong market demand.</li>
          <li>Demonstrating how quality shifts affect pricing.</li>
          <li>Simple dashboards for factory and field teams.</li>
        </ul>
      </div>
    </article>

    <article class="initiative-card fade-in">
      <div class="initiative-media">
        <?php pictureTag('plantation_sunrise', 'Climate-smart tea practices in the field'); ?>
      </div>
      <div class="initiative-body">
        <h3>Climate-Smart Tea Practices</h3>
        <p>
          Working with partners to highlight water conservation, soil health, shade management
          and energy efficiency — aligning premium buyers with climate-resilient producers.
        </p>
        <ul>
          <li>Encouraging sustainable fuel and factory efficiency.</li>
          <li>Supporting water and soil protection conversations.</li>
          <li>Linking climate-smart teas with premium markets.</li>
        </ul>
      </div>
    </article>

    <article class="initiative-card fade-in">
      <div class="initiative-media">
        <?php pictureTag('2leaves-a-bud', 'Traceability and responsible trading'); ?>
      </div>
      <div class="initiative-body">
        <h3>Traceability &amp; Responsible Private Sales</h3>
        <p>
          Supporting structures that keep private sales transparent, traceable and aligned
          with regulatory requirements while protecting the interests of all parties.
        </p>
        <ul>
          <li>Clear documentation from garden mark to cup.</li>
          <li>Support for digital trails and audit readiness.</li>
          <li>Balanced structures for producers and buyers.</li>
        </ul>
      </div>
    </article>
  </div>
</section>

<section class="wrap section-pad visuals-section">
  <div class="visuals-grid">
    <figure class="visual-card fade-in">
      <?php pictureTag('plantation_landscape-2', 'Tea factory sustainability'); ?>
      <figcaption>
        Efficient factories, cleaner energy and smarter process controls reduce waste and emissions.
      </figcaption>
    </figure>

    <figure class="visual-card fade-in">
      <?php pictureTag('plantation_landscape-3', 'Farmer group meeting'); ?>
      <figcaption>
        Farmer groups receive market updates in simple, actionable language they can apply season-by-season.
      </figcaption>
    </figure>

    <figure class="visual-card fade-in">
      <?php pictureTag('plantation_landscape', 'Auction and buyers'); ?>
      <figcaption>
        Buyers and producers aligned on traceability and quality, with sustainability at the centre.
      </figcaption>
    </figure>
  </div>
</section>

<section class="wrap section-pad monitoring-section">
  <div class="section-head">
    <h2>Effect &amp; Monitoring</h2>
    <p>
      We track the effect of our work over time and refine our approach based on real data.
      Some of the indicators we monitor include:
    </p>
  </div>

  <div class="monitoring-grid">
    <div class="monitoring-block fade-in">
      <h3>Farmer-Level</h3>
      <ul>
        <li>Consistency and improvement in quality grades offered.</li>
        <li>Adoption of climate-smart and good agricultural practices.</li>
        <li>Stability of delivered volumes across seasons.</li>
      </ul>
    </div>
    <div class="monitoring-block fade-in">
      <h3>Producer-Level</h3>
      <ul>
        <li>Performance of factories in weekly auctions and private sales.</li>
        <li>Premiums achieved for sustainably produced or specialty lines.</li>
        <li>Readiness for audits through better documentation and traceability.</li>
      </ul>
    </div>
    <div class="monitoring-block fade-in">
      <h3>Buyer-Level</h3>
      <ul>
        <li>Access to consistent quality and traceable teas across seasons.</li>
        <li>Confidence to commit to long-term contracts and origin programs.</li>
        <li>Alignment with global sustainability and ESG requirements.</li>
      </ul>
    </div>
  </div>
</section>

<section class="wrap section-pad testimonials-section">
  <div class="section-head">
    <h2>What Our Partners Say</h2>
    <p>
      Voices from farmers, producers and buyers who experience the impact of sustainable tea ecosystems every day.
    </p>
  </div>

  <div class="testimonials-grid">
    <article class="testimonial-card fade-in">
      <div class="testimonial-photo">
        <?php pictureTag('sampled-heart', 'Farmer testimonial'); ?>
      </div>
      <blockquote>
        “The quality insights and sustainable practices introduced to our field teams
        have transformed both yield and income. Today our farmers understand how
        climate-smart practices directly improve cup quality.”
      </blockquote>
      <div class="testimonial-meta">
        <strong>Mary Wanjiru</strong>
        <span>Smallholder Farmer</span>
      </div>
    </article>

    <article class="testimonial-card fade-in">
      <div class="testimonial-photo">
        <?php pictureTag('sampled-heart', 'Producer testimonial'); ?>
      </div>
      <blockquote>
        “The weekly market intelligence and transparent reporting give our factory a
        stronger position at the auction. Sustainability is no longer a slogan — it’s
        measurable and visible.”
      </blockquote>
      <div class="testimonial-meta">
        <strong>Daniel Kiprotich</strong>
        <span>Factory Manager</span>
      </div>
    </article>

    <article class="testimonial-card fade-in">
      <div class="testimonial-photo">
        <?php pictureTag('sampled-heart', 'Buyer testimonial'); ?>
      </div>
      <blockquote>
        “Sustainable teas backed with origin traceability and consistent quality give us
        confidence when sourcing for global markets. The transparency is unmatched.”
      </blockquote>
      <div class="testimonial-meta">
        <strong>Rebecca Mills</strong>
        <span>Chairman Tea Buyers</span>
      </div>
    </article>
  </div>
</section>

<section class="wrap section-pad closing-section">
  <div class="closing-card fade-in">
    <h2>Let’s Build a More Sustainable Tea Future</h2>
    <p>
      Whether you are a farmer organisation, producer, buyer or allied partner, we’re ready
      to explore how we can strengthen sustainability across your tea value chain.
    </p>
    <div class="closing-actions">
      <a href="/?p=enquiry" class="btn btn-brand">Talk to Our Team</a>
      <a href="/?p=request" class="btn btn-outline">Request Market Insights</a>
    </div>
  </div>
</section>

<script defer src="/assets/js/sustainability.js"></script>