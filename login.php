<?php
require_once __DIR__ . "/function.php";

if (isset($_SESSION["email"])) {
  header("location:index.php");
}
login();





?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
</head>

<body>

  <div class="box-default">
    <p>Login</p>
    <form action="" method="POST" autocomplete="off">
      <div class="input-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?= $_POST["email"] ?? null; ?>">
      </div>
      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
      </div>
      <div class="input-group">
        <button class="btn-login" name="login">Login</button>
      </div>
      <div class="input-group">
        <p>Dont't Have Account? <a href="register.php">Register Here</a></p>
        <p>Forget your Password? <a href="reset.php">Click Here</a></p>
      </div>
    </form>
  </div>

</body>

</html>