<section class="page-head"><div class="wrap"><h1>Contact</h1></div></section>
<section class="wrap grid-2 pad">
  <div class="card">
    <h2>Reach Us</h2>
    <ul class="bullets">
      <li>Email: <?=htmlspecialchars(NOTIFY_TO_EMAIL)?></li>
      <li>Hours: Mon–Fri 9:00–17:00</li>
    </ul>
  </div>
  <div class="card">
    <form class="form" method="post" action="/contact_submit.php" novalidate>
      <div class="row">
        <label>Name<input name="name" required></label>
        <label>Email<input type="email" name="email" required></label>
      </div>
      <label>Subject<input name="subject" placeholder="General enquiry"></label>
      <label>Message<textarea name="message" rows="6" required></textarea></label>
      <input type="text" name="hp" class="hp">
      <button class="btn">Send Message</button>
    </form>
  </div>
</section>