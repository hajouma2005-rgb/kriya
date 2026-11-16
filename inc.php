<?php
session_start();
require __DIR__.'/lang.php';
ob_start('i18n');
$pdo = new PDO('mysql:host=localhost;dbname=kriya;charset=utf8mb4','root','',
  [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function me(){ return $_SESSION['u'] ?? null; }
function need($roles=null){
  $u = me();
  if(!$u || ($roles && !in_array($u['role'],$roles))){ header('Location: login.php'); exit; }
}
const PLANS=['starter'=>['name'=>'Starter','cars'=>3,'price'=>99],'pro'=>['name'=>'Pro','cars'=>15,'price'=>299],'agency'=>['name'=>'Agency','cars'=>999,'price'=>599]];
function sub($uid){ global $pdo; $s=$pdo->prepare("SELECT * FROM subscriptions WHERE user_id=? AND status='active'"); $s->execute([$uid]); return $s->fetch(); }
function carsvg($brand='K'){ $h=abs(crc32($brand))%360; $c="hsl($h,55%,52%)"; return '<svg class="carsvg" viewBox="0 0 320 130" aria-hidden="true"><ellipse cx="160" cy="124" rx="140" ry="6" fill="#000" opacity=".18"/><path d="M14 98Q16 74 48 68L88 42Q100 34 120 34H202Q222 34 236 48L264 66Q304 70 306 98V106H14Z" fill="'.$c.'"/><path d="M98 48H150V66H80ZM160 48H200Q213 48 222 58L236 66H160Z" fill="#d6eef2" opacity=".9"/><rect x="286" y="80" width="18" height="8" rx="3" fill="#ffd36b"/><circle cx="82" cy="104" r="21" fill="#151515"/><circle cx="82" cy="104" r="9" fill="#bbb"/><circle cx="244" cy="104" r="21" fill="#151515"/><circle cx="244" cy="104" r="9" fill="#bbb"/></svg>'; }
function head($title='Kriya'){
  $u = me(); ?>
<!DOCTYPE html><html lang="<?= $GLOBALS['L'] ?>" dir="<?= $GLOBALS['L']==='ar'?'rtl':'ltr' ?>"><head><meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e(t($title)) ?> · Kriya</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:wght@500;800&family=Figtree:wght@400;600;700&family=Cairo:wght@500;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/style.css?v=<?= filemtime(__DIR__.'/assets/style.css') ?>"></head><body>
<header class="nav"><a class="logo" href="index.php">kriya<i></i></a>
<nav><a href="cars.php">Browse cars</a><a href="pricing.php">For owners &amp; agencies</a>
<?php if($u): ?>
  <a href="dashboard.php"><?= $u['role']==='client' ? 'My trips' : 'My fleet' ?></a>
  <a class="btn small ghost" href="logout.php">Log out (<?= e(explode(' ',$u['name'])[0]) ?>)</a>
<?php else: ?>
  <a href="login.php">Log in</a><a class="btn small" href="register.php">Sign up</a>
<?php endif; ?>
<div class="lang"><?php foreach($GLOBALS['LANGS'] as $k=>$n) echo '<a'.($GLOBALS['L']===$k?' class="on"':'').' href="?'.e(http_build_query(array_merge($_GET,['lang'=>$k]))).'">'.$n.'</a>'; ?></div></nav></header><main>
<?php }
function foot(){ ?>
</main><footer><b>kriya</b> – car rental between agencies, owners and drivers.
<span>Made with HTML · CSS · JS · PHP · MySQL</span></footer>
<script>window.T=<?= json_encode(['day'=>t('day'),'days'=>t('days'),'bad'=>t('End date must be after start date.')],JSON_UNESCAPED_UNICODE) ?></script>
<script src="assets/app.js?v=<?= filemtime(__DIR__.'/assets/app.js') ?>"></script></body></html>
<?php }
function card($c){
  $bg = $c['image'] ? "background-image:url('uploads/".e($c['image'])."')" : '';
  ?>
<a class="car" href="car.php?id=<?= (int)$c['id'] ?>">
  <div class="pic" style="<?= $bg ?>"><?= $c['image'] ? '' : carsvg($c['brand']) ?>
  <em><?= e($c['owner_role']==='agency' ? 'Agency' : 'Private owner') ?></em>
  <b class="price"><?= number_format($c['price_per_day'],0) ?> MAD<small>/day</small></b></div>
  <div class="info"><h3><?= e($c['brand'].' '.$c['model']) ?> <small><?= (int)$c['year'] ?></small></h3>
  <ul class="chips"><li><?= e($c['city']) ?></li><li><?= e($c['transmission']) ?></li><li><?= e($c['fuel']) ?></li><li><?= (int)$c['seats'] ?> seats</li></ul></div></a>
<?php }
