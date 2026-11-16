<?php require 'inc.php'; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $st=$pdo->prepare("SELECT * FROM users WHERE email=?"); $st->execute([trim($_POST['email'])]); $u=$st->fetch();
  if($u && password_verify($_POST['password'],$u['password'])){
    session_regenerate_id(true); $_SESSION['u']=['id'=>$u['id'],'name'=>$u['name'],'role'=>$u['role']];
    header('Location: dashboard.php'); exit; }
  $err='Wrong email or password.';
}
head('Log in'); ?>
<section class="wrap narrow"><h1 class="h1">Welcome back</h1>
<form method="post" class="form"><?php if($err) echo '<p class="err">'.e($err).'</p>'; ?>
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button class="btn">Log in</button><p>New here? <a href="register.php">Create an account</a></p></form></section>
<?php foot();
