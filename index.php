<?php
require_once __DIR__ . "/function.php";

if (!isset($_SESSION["email"])) {
  header("location:login.php");
} else if ($_SESSION["email_status"] == NULL) {
  header("location:verify.php");
}

$user = getAll("users", "user_email", $_SESSION["email"])[0];

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
</head>

<body>

  <ul>
    <li><a href="index.php">Home</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
  <p>Welcome <?= $user->user_name; ?></p>

  <ul>
    <li>Name: <?= $user->user_name; ?></li>
    <li>Email: <?= $user->user_email; ?></li>
  </ul>

</body>

</html>