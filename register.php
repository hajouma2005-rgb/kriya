<?php require 'inc.php'; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $role=in_array($_POST['role'],['client','agency','owner'])?$_POST['role']:'client';
  if(strlen($_POST['password'])<6) $err='Password needs at least 6 characters.';
  else try{
    $pdo->prepare("INSERT INTO users(name,email,password,role,phone,city) VALUES(?,?,?,?,?,?)")
      ->execute([trim($_POST['name']),trim($_POST['email']),password_hash($_POST['password'],PASSWORD_DEFAULT),$role,$_POST['phone'],$_POST['city']]);
    $id=$pdo->lastInsertId(); $_SESSION['u']=['id'=>$id,'name'=>$_POST['name'],'role'=>$role];
    header('Location: '.($role==='client'?'dashboard.php':'pricing.php')); exit;
  }catch(PDOException $x){ $err='This email is already registered.'; }
}
head('Sign up'); $r=$_GET['role']??'client'; ?>
<section class="wrap narrow"><h1 class="h1">Create your account</h1>
<form method="post" class="form">
  <div class="roles">
  <?php foreach(['client'=>'I want to rent','owner'=>'I own a car','agency'=>'I run an agency'] as $k=>$l)
    echo "<label><input type='radio' name='role' value='$k' ".($r==$k?'checked':'')."><span>$l</span></label>"; ?></div>
  <?php if($err) echo '<p class="err">'.e($err).'</p>'; ?>
  <input name="name" placeholder="Full name or agency name" required>
  <input type="email" name="email" placeholder="Email" required>
  <input type="password" name="password" placeholder="Password (6+ characters)" required>
  <input name="phone" placeholder="Phone" required><input name="city" placeholder="City" required>
  <button class="btn">Sign up</button><p>Already a member? <a href="login.php">Log in</a></p></form></section>
<?php foot();
