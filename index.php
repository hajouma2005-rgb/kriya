<?php require 'inc.php'; head('Rent a car');
$cars = $pdo->query("SELECT c.*,u.role owner_role FROM cars c JOIN users u ON u.id=c.owner_id ORDER BY c.id DESC LIMIT 6")->fetchAll();
$agencies = $pdo->query("SELECT u.id,u.name,u.city,COUNT(c.id) n FROM users u LEFT JOIN cars c ON c.owner_id=u.id WHERE u.role='agency' GROUP BY u.id ORDER BY n DESC LIMIT 4")->fetchAll();
$cities = $pdo->query("SELECT DISTINCT city FROM cars ORDER BY city")->fetchAll(PDO::FETCH_COLUMN);
$nc=$pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn(); $na=$pdo->query("SELECT COUNT(*) FROM users WHERE role<>'client'")->fetchColumn(); ?>
<section class="hero"><div class="hero-in">
  <div><h1>Rent a car from people who love theirs.</h1>
  <p>Trusted agencies and private owners, one search. Pick a city, compare prices and book in minutes.</p>
  <form action="cars.php" class="plate-search">
    <div class="plate"><span class="eu">MA</span>
      <input name="city" list="cities" placeholder="CITY" autocomplete="off" aria-label="City">
      <datalist id="cities"><?php foreach($cities as $c) echo '<option value="'.e($c).'">'; ?></datalist>
      <span class="ar">المغرب</span></div>
    <button class="btn big">Search cars</button></form>
  <div class="stats"><div><b><?= (int)$nc ?></b> cars listed</div><div><b><?= (int)$na ?></b> agencies &amp; owners</div><div><b>0 MAD</b> to list your car</div></div></div>
  <div class="art"><div class="sun"></div><?= carsvg('Kriya') ?><div class="road"><div class="dash"></div></div></div>
</div></section>
<section class="wrap"><div class="row-title"><h2>Fresh on the road</h2><a href="cars.php">See all cars</a></div>
<div class="grid"><?php foreach($cars as $c) card($c); if(!$cars) echo '<p>No cars yet. Be the first to list one!</p>'; ?></div></section>
<section class="wrap how"><h2>How it works</h2><div class="steps">
  <div><i>1</i><h3>Search</h3><p>Choose a city and filter by price, fuel and owner type.</p></div>
  <div><i>2</i><h3>Request</h3><p>Pick your dates and see the exact total before you send.</p></div>
  <div><i>3</i><h3>Drive</h3><p>The owner accepts, you meet, and you hit the road.</p></div></div></section>
<section class="wrap split">
  <div><h2>Agencies you can trust</h2>
  <?php foreach($agencies as $a): ?>
    <a class="agency" href="cars.php?owner=<?= (int)$a['id'] ?>"><b><?= e($a['name']) ?></b><span><?= e($a['city']) ?></span><i><?= (int)$a['n'] ?> cars</i></a>
  <?php endforeach; ?></div>
  <div class="cta"><h2>Your car sleeps 25 days a month.</h2>
  <p>Agencies and private owners can subscribe and list cars with photos. Every plan is free during launch.</p>
  <a class="btn light" href="pricing.php">See plans</a></div>
</section>
<?php foot();
