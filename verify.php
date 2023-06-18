<?php
require_once __DIR__ . "/function.php";
if (!isset($_SESSION["email"])) {
  header("location:login.php");
} else {
  if ($_SESSION["email_status"] != NULL) {
    header("location:index.php");
  }
}

if (isset($_POST["requestVerify"])) {
  requestCodeVerify();
}

$result = getBy("verify_expired,verify_action", "verify", "verify_email", $_SESSION["email"]);
// if verify_email is exist
$check = $check->rowCount;

// expired result 
$expired = $result->result->verify_expired;
$date = date_create($expired);

// get time from $expired without date
$set = date_create($expired);
$set = date_format($set, "H:i:s");

// verify_name result
$verifyName = $result->result->verify_action;
if ($verifyName == "RESET") {
  ResetTimer();
  $verifyName = "Password";
} else {
  $verifyName = "Account";
}

Verify();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verify</title>
</head>

<body>

  <div>
    <p>Verify Your <?= $verifyName; ?></p>
    <form action="" method="POST">
      <div class="input-group">
        <input type="text" name="code">
      </div>
      <div class="input-group">
        <button type="submit" name="verify">Verify My Email</button>
        <?php if ($set >= date("H:i:s")) : ?>
          <p>Your code expired on <?= date_format($date, " l h:i A"); ?></p>
        <?php endif; ?>
        <?php if ($set < date("H:i:s")) : ?>
          <button type="submit" name="requestVerify">Request New Code</button>
        <?php endif; ?>
      </div>
    </form>
  </div>

</body>

</html>