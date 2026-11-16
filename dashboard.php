<?php require 'inc.php'; need(); $u=me(); $err='';
if($u['role']!=='client'){
  $sub=sub($u['id']);
  if($_SERVER['REQUEST_METHOD']==='POST'){
    if(isset($_POST['add'])){
      $cnt=$pdo->prepare("SELECT COUNT(*) FROM cars WHERE owner_id=?"); $cnt->execute([$u['id']]);
      if(!$sub) $err='Activate a plan first to list cars.';
      elseif($cnt->fetchColumn()>=PLANS[$sub['plan']]['cars']) $err='Your plan limit is reached. Upgrade to add more cars.';
      else {
        $files=[]; $F=$_FILES['images']??null;
        if($F) foreach($F['name'] as $i=>$n){ if($F['error'][$i]!==0) continue;
          $ext=strtolower(pathinfo($n,PATHINFO_EXTENSION));
          if(!in_array($ext,['jpg','jpeg','png','webp'])||$F['size'][$i]>3e6){ $err='Photos must be JPG, PNG or WEBP, under 3 MB each.'; continue; }
          $f=bin2hex(random_bytes(8)).'.'.$ext; move_uploaded_file($F['tmp_name'][$i],'uploads/'.$f); $files[]=$f; if(count($files)>=6) break; }
        if(!$err){
          $pdo->prepare("INSERT INTO cars(owner_id,brand,model,year,city,price_per_day,fuel,transmission,seats,description,image) VALUES(?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([$u['id'],$_POST['brand'],$_POST['model'],(int)$_POST['year'],$_POST['city'],(float)$_POST['price'],$_POST['fuel'],$_POST['transmission'],(int)$_POST['seats'],$_POST['description'],$files[0]??null]);
          $cid=$pdo->lastInsertId();
          foreach($files as $f) $pdo->prepare("INSERT INTO car_images(car_id,filename) VALUES(?,?)")->execute([$cid,$f]);
        }
      }
    }
    if(isset($_POST['del'])) $pdo->prepare("DELETE FROM cars WHERE id=? AND owner_id=?")->execute([(int)$_POST['del'],$u['id']]);
    if(isset($_POST['answer'])) $pdo->prepare("UPDATE bookings b JOIN cars c ON c.id=b.car_id SET b.status=? WHERE b.id=? AND c.owner_id=?")
      ->execute([$_POST['answer']==='accepted'?'accepted':'refused',(int)$_POST['bid'],$u['id']]);
  }
  $mine=$pdo->prepare("SELECT * FROM cars WHERE owner_id=? ORDER BY id DESC"); $mine->execute([$u['id']]); $mine=$mine->fetchAll();
  $req=$pdo->prepare("SELECT b.*,c.brand,c.model,cl.name client,cl.phone FROM bookings b JOIN cars c ON c.id=b.car_id JOIN users cl ON cl.id=b.client_id WHERE c.owner_id=? ORDER BY b.id DESC");
  $req->execute([$u['id']]); $req=$req->fetchAll();
} else {
  $trips=$pdo->prepare("SELECT b.*,c.brand,c.model,c.city FROM bookings b JOIN cars c ON c.id=b.car_id WHERE b.client_id=? ORDER BY b.id DESC");
  $trips->execute([$u['id']]); $trips=$trips->fetchAll();
}
head('Dashboard'); ?>
<section class="wrap"><h1 class="h1">Hello, <?= e($u['name']) ?></h1>
<?php if($u['role']!=='client'): ?><div class="plan"><?php if($sub): ?>Plan <b><?= e(PLANS[$sub['plan']]['name']) ?></b> · up to <?= PLANS[$sub['plan']]['cars']>99?'unlimited':PLANS[$sub['plan']]['cars'] ?> cars · <b>free during launch</b> <a href="pricing.php">Change plan</a><?php else: ?>You have no plan yet. <a class="btn small" href="pricing.php">Pick a free plan</a><?php endif; ?></div><?php endif; ?>
<?php if(isset($_GET['sent'])) echo '<p class="ok">Request sent. The owner will answer soon.</p>'; ?>
<?php if($u['role']==='client'): ?>
  <h2>My trips</h2>
  <?php foreach($trips as $t): ?><div class="row"><b><?= e($t['brand'].' '.$t['model']) ?></b> <?= e($t['city']) ?>
    <span><?= e($t['start_date']) ?> → <?= e($t['end_date']) ?></span><span><?= number_format($t['total'],0) ?> MAD</span>
    <i class="tag <?= e($t['status']) ?>"><?= e($t['status']) ?></i></div>
  <?php endforeach; if(!$trips) echo '<p>No trips yet. <a href="cars.php">Find your first car</a>.</p>'; ?>
<?php else: ?>
  <div class="split"><div><h2>Booking requests</h2>
  <?php foreach($req as $r): ?><div class="row"><b><?= e($r['client']) ?></b> <em>wants</em> <?= e($r['brand'].' '.$r['model']) ?>
    <span><?= e($r['start_date']) ?> → <?= e($r['end_date']) ?> · <?= number_format($r['total'],0) ?> MAD · <?= e($r['phone']) ?></span>
    <?php if($r['status']==='pending'): ?><form method="post"><input type="hidden" name="bid" value="<?= (int)$r['id'] ?>">
      <button name="answer" value="accepted" class="btn small">Accept</button>
      <button name="answer" value="refused" class="btn small ghost">Refuse</button></form>
    <?php else: ?><i class="tag <?= e($r['status']) ?>"><?= e($r['status']) ?></i><?php endif; ?></div>
  <?php endforeach; if(!$req) echo '<p>No requests yet.</p>'; ?>
  <h2>My fleet</h2>
  <?php foreach($mine as $c): ?><div class="row"><b><?= e($c['brand'].' '.$c['model']) ?></b> <?= e($c['city']) ?> · <?= number_format($c['price_per_day'],0) ?> MAD/day
    <form method="post" onsubmit="return confirm(<?= e(json_encode(t('Delete this car?'),JSON_UNESCAPED_UNICODE)) ?>)"><button name="del" value="<?= (int)$c['id'] ?>" class="btn small ghost">Delete</button></form></div>
  <?php endforeach; ?></div>
  <form method="post" enctype="multipart/form-data" class="form"><h2>List a car</h2>
    <?php if($err) echo '<p class="err">'.e($err).'</p>'; ?>
    <input name="brand" placeholder="Brand" required><input name="model" placeholder="Model" required>
    <input type="number" name="year" placeholder="Year" min="1990" max="2030" required>
    <input name="city" placeholder="City" value="<?= e($_SESSION['city']??'') ?>" required>
    <input type="number" name="price" placeholder="Price per day (MAD)" min="1" required>
    <select name="fuel"><option value="Petrol">Petrol</option><option value="Diesel">Diesel</option><option value="Hybrid">Hybrid</option><option value="Electric">Electric</option></select>
    <select name="transmission"><option value="Manual">Manual</option><option value="Automatic">Automatic</option></select>
    <input type="number" name="seats" placeholder="Seats" value="5" min="1" max="9">
    <textarea name="description" placeholder="Tell drivers what makes this car great" rows="3"></textarea>
    <label class="file">Photos (up to 6, first one is the cover)<input type="file" name="images[]" accept="image/*" multiple></label><button class="btn" name="add" value="1">Publish car</button></form></div>
<?php endif; ?></section>
<?php foot();
