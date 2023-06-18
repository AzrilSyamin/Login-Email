<?php

require_once __DIR__ . "/function.php";

if (isset($_SESSION["email"])) {
  header("location:index.php");
}

userRegister();

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
</head>

<body>

  <div class="box-default">
    <p>Register</p>
    <form action="" method="POST" autocomplete="off">
      <div class="input-group">
        <label for="name">Name</label>
        <input type="name" name="name" id="name" value="<?= $_POST["name"] ?? null; ?>">
      </div>
      <div class="input-group">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" value="<?= $_POST["email"] ?? null; ?>">
      </div>
      <div class="input-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
      </div>
      <div class="input-group">
        <label for="retypePassword">Retype Password</label>
        <input type="password" name="retypePassword" id="retypePassword">
      </div>
      <div class="input-group">
        <button class="btn-login" name="register">Register</button>
      </div>
      <div class="input-group">
        <p>Have Already Account <a href="login.php">Login Here</a></p>
      </div>
    </form>
  </div>

</body>

</html>