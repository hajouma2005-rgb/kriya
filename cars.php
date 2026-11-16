<?php require 'inc.php'; head('Browse cars');
$w=['1']; $p=[];
if(($v=trim($_GET['city']??''))!==''){ $w[]='c.city LIKE ?'; $p[]="%$v%"; }
if(($v=trim($_GET['q']??''))!==''){ $w[]='CONCAT(c.brand," ",c.model) LIKE ?'; $p[]="%$v%"; }
if(!empty($_GET['max'])){ $w[]='c.price_per_day<=?'; $p[]=(float)$_GET['max']; }
if(!empty($_GET['fuel'])){ $w[]='c.fuel=?'; $p[]=$_GET['fuel']; }
if(!empty($_GET['type'])){ $w[]='u.role=?'; $p[]=$_GET['type']; }
if(!empty($_GET['owner'])){ $w[]='c.owner_id=?'; $p[]=(int)$_GET['owner']; }
$st=$pdo->prepare("SELECT c.*,u.role owner_role FROM cars c JOIN users u ON u.id=c.owner_id WHERE ".implode(' AND ',$w)." ORDER BY c.price_per_day");
$st->execute($p); $cars=$st->fetchAll(); $g=fn($k)=>e($_GET[$k]??''); ?>
<section class="band"><h1 class="h1">Browse cars</h1><p>Compare prices, photos and owners, then send a request in one click.</p></section>
<section class="wrap">
<form class="filters" method="get">
  <input name="city" placeholder="City" value="<?= $g('city') ?>">
  <input name="q" placeholder="Brand or model" value="<?= $g('q') ?>">
  <select name="fuel"><option value="">Any fuel</option>
  <?php foreach(['Petrol','Diesel','Hybrid','Electric'] as $f) echo '<option value="'.$f.'"'.(($_GET['fuel']??'')==$f?' selected':'').'>'.$f.'</option>'; ?></select>
  <select name="type"><option value="">Agencies &amp; owners</option>
    <option value="agency"<?= ($_GET['type']??'')=='agency'?' selected':'' ?>>Agencies only</option>
    <option value="owner"<?= ($_GET['type']??'')=='owner'?' selected':'' ?>>Private owners only</option></select>
  <label class="range">Max <output id="maxv"><?= $_GET['max']??1000 ?></output> MAD/day
    <input type="range" name="max" min="100" max="1500" step="50" value="<?= e($_GET['max']??1000) ?>" id="max"></label>
  <button class="btn small">Apply</button></form>
<p class="count"><?= count($cars) ?> car<?= count($cars)==1?'':'s' ?> found</p>
<div class="grid"><?php foreach($cars as $c) card($c);
if(!$cars) echo '<p>No car matches. Try another city or raise the price limit.</p>'; ?></div></section>
<?php foot();
