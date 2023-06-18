<?php
require_once __DIR__ . "/function.php";

resetPassword();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reset Password</title>
</head>

<body>
  <div>
    <p>Reset Password</p>
    <form action="" method="POST" autocomplete="off">
      <div class="input-group">
        <label>Your Email</label>
        <input type="email" name="email">
      </div>
      <div class="input-group">
        <button type="submit" name="reset">Reset Password</button>
        <a href="index.php">Back</a>
      </div>
    </form>
  </div>
</body>

</html>