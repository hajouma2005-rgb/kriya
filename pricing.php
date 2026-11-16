<?php require 'inc.php'; $u=me(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  need(['agency','owner']);
  $p=$_POST['plan']; if(isset(PLANS[$p])){
    $pdo->prepare("REPLACE INTO subscriptions(user_id,plan,status,price) VALUES(?,?,'active',0)")->execute([$u['id'],$p]);
    header('Location: dashboard.php'); exit; }
}
$cur=$u?sub($u['id']):null;
$feat=['starter'=>['For private owners','Up to 3 cars','Photo gallery (6 photos per car)','Booking requests by email and dashboard'],
 'pro'=>['For small agencies','Up to 15 cars','Photo gallery (6 photos per car)','Agency badge on every listing','Priority in search results'],
 'agency'=>['For large agencies','Unlimited cars','Photo gallery (6 photos per car)','Agency badge on every listing','Priority in search results','Dedicated support']];
head('Plans'); ?>
<section class="band center"><h1 class="h1">List your cars. Pay nothing for now.</h1>
<p>Every plan is free during our launch. Prices apply later, and we will tell you before.</p></section>
<section class="wrap"><div class="plans">
<?php foreach(PLANS as $k=>$pl): ?>
  <div class="plan-card <?= $k==='pro'?'hot':'' ?>"><?php if($k==='pro') echo '<span class="flag">Most popular</span>'; ?>
    <h2><?= $pl['name'] ?></h2><p class="for"><?= $feat[$k][0] ?></p>
    <div class="amount"><b>0 MAD</b><s><?= $pl['price'] ?> MAD/month</s><small>free during launch</small></div>
    <ul><?php foreach(array_slice($feat[$k],1) as $f) echo '<li>'.e($f).'</li>'; ?></ul>
    <?php if(!$u): ?><a class="btn" href="register.php?role=<?= $k==='starter'?'owner':'agency' ?>">Sign up to start</a>
    <?php elseif($u['role']==='client'): ?><p class="note">Create an owner or agency account to subscribe.</p>
    <?php elseif($cur && $cur['plan']===$k): ?><span class="btn ghost dark">Your current plan</span>
    <?php else: ?><form method="post"><button class="btn" name="plan" value="<?= $k ?>">Start free</button></form><?php endif; ?>
  </div>
<?php endforeach; ?></div></section>
<?php foot();
