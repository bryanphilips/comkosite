<section class="page-head hero-banner">
  <div class="overlay"></div>
  <div class="wrap">
    <h1>Calendar of Events</h1>
    <p class="sub-title">Stay Updated on Trade, Auction & Networking Highlights</p>
  </div>
</section>

<section class="wrap pad events-section">
  <!-- The calendar will be auto-rendered here -->
  <div id="events-root" class="events-container glassy"></div>
</section>

<!-- CSS + JS -->
<link rel="stylesheet" href="/assets/css/events.css">
<script defer src="/assets/js/events.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
  EventsCalendar.init({
    mount: '#events-root',
    dataUrl: '/assets/data/events.json'
  });
});
</script>