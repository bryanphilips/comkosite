<section class="page-head"><div class="wrap"><h1>Request Data</h1></div></section>
<section class="wrap pad">
  <form class="form card" method="post" action="/request_submit.php" novalidate>
    <div class="row">
      <label>Your name<input name="name" required></label>
      <label>Your email<input type="email" name="email" required></label>
    </div>
    <label>Company<input name="company" required></label>
    <div class="row">
      <label>Data needed
        <select name="data_type" required>
          <option value="">Select…</option>
          <option>Weekly Catalogue</option>
          <option>Market Report</option>
          <option>Historical Prices</option>
          <option>Other</option>
        </select>
      </label>
      <label>Year<input type="number" name="year" min="2015" max="2100" required></label>
      <label>Week<input type="number" name="week" min="1" max="52"></label>
    </div>
    <label>Details<textarea name="details" rows="4" placeholder="Marks, grades, date ranges…"></textarea></label>
    <input type="text" name="hp" class="hp">
    <button class="btn">Send Request</button>
  </form>
</section>