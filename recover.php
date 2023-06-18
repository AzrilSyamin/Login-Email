<?php
require_once __DIR__ . "/function.php";
if (!isset($_SESSION["recover"])) {
  header("location:index.php");
}

recoverPassword();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recover Password</title>
</head>

<body>
  <div>
    <p>Recover Password</p>
    <form action="" method="POST" autocomplete="off">
      <div class="input-group">
        <label>New Password</label>
        <input type="password" name="password">
      </div>
      <div class="input-group">
        <label>Re-type Password</label>
        <input type="password" name="retypePassword">
      </div>
      <div class="input-group">
        <button type="submit" name="recover">Recover Password</button>
      </div>
    </form>
  </div>
</body>

</html>