<section class="page-head"><div class="wrap"><h1>Enquiry & Feedback</h1></div></section>
<section class="wrap pad">
  <form class="form card" method="post" action="/enquiry_submit.php" novalidate>
    <div class="row">
      <label>Name<input name="name" required></label>
      <label>Email<input type="email" name="email" required></label>
    </div>
    <div class="row">
      <label>Type
        <select name="etype" required>
          <option value="Enquiry">Enquiry</option>
          <option value="Feedback">Feedback</option>
          <option value="Complaint">Complaint</option>
        </select>
      </label>
      <label>Rating (optional)
        <select name="rating">
          <option value="">—</option><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option>
        </select>
      </label>
    </div>
    <label>Subject<input name="subject" placeholder="Subject" required></label>
    <label>Message<textarea name="message" rows="6" required></textarea></label>
    <label><input type="checkbox" name="consent" required> I consent to be contacted regarding this message.</label>
    <input type="text" name="hp" class="hp">
    <button class="btn">Send</button>
  </form>
</section>