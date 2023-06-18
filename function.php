<?php
require_once __DIR__ . "/config.php";
session_start();
date_default_timezone_set($timeZone);

//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require_once __DIR__ . '/vendor/autoload.php';

function getConnection()
{
  global $host;
  global $user;
  global $pass;
  global $dbname;

  $con = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
  return $con;
}

function userRegister()
{
  if (isset($_POST["register"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $retypePassword = $_POST["retypePassword"];

    if ($name == null) {
      echo "Please insert your name";
      return false;
    } elseif ($email == null) {
      echo "Please insert your email";
      return false;
    } elseif ($password == NULL) {
      echo "Please insert your password";
      return false;
    } elseif ($retypePassword == NULL) {
      echo "Please insert your password";
      return false;
    }

    if ($password !== $retypePassword) {
      echo "Password not match";
      return false;
    }

    // check if user is exist
    $result = getBy("user_email", "users", "user_email", $email);
    $result = $result->rowCount;

    if ($result > 0) {
      echo "Email already exist!";
      return false;
    }

    // add user if users is not exist
    InsertData("users", "user_name,user_email,password", "$name,$email,$password");

    // request code verify
    requestCodeVerify(["email" => $email, "name" => $name]);

    // get status verify form database and set session after success register
    $result = getBy("user_email,email_status", "users", "user_email", $email);
    @$email_status = $result->result->email_status;

    $_SESSION["email"] = $email;
    $_SESSION["email_status"] = $email_status;
    header("location:verify.php");
  }
}

function login()
{
  if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    // check if user is exist
    $user = getBy("user_email,email_status,password", "users", "user_email", $email);
    $check = $user->rowCount;
    $user = $user->result;

    // if user exist! 
    if ($check > 0) {
      if (@$user->user_email != $email) {
        echo "Email or Password is wrong";
      } else {
        if ($user->password != $password) {
          echo "Email or Password is wrong";
        } else {
          $_SESSION["email"] = $user->user_email;
          $_SESSION["email_status"] = $user->email_status;
          header("location:index.php");
        }
      }
    } else {
      echo "Email is not exist!";
    }
  }
}

function resetPassword()
{
  if (isset($_POST["reset"])) {
    $email = $_POST["email"];

    $result = getBy("user_email", "users", "user_email", $email);
    $result = $result->rowCount;

    if ($result > 0) {
      requestCodeVerify(["email" => $email], "RESET");
      $_SESSION["email"] = $email;
      header("location:verify.php");
    } else {
      echo "Email is not exist!";
    }
  }
}

function ResetTimer()
{
  $result = getBy("verify_expired,verify_action", "verify", "verify_email", $_SESSION["email"]);
  $expired = $result->result->verify_expired;

  $set = date_create($expired);
  $set = date_add($set, date_interval_create_from_date_string("3 minutes"));
  $set = date_format($set, "H:i:s");

  // var_dump(date("H:i") >= $set);
  if (date("H:i:s") >= $set) {
    delBy("verify", "verify_email", $_SESSION["email"]);
  }
}

function recoverPassword()
{
  if (isset($_POST["recover"])) {
    $password = $_POST["password"];
    $retypePassword = $_POST["retypePassword"];

    if ($password !== $retypePassword) {
      echo "Password not match!";
      return false;
    } else {
      updateData("users", ["password" => $password], "user_email", $_SESSION["email"]);
      header("location:logout.php");
    }
  }
}

function requestCodeVerify($data = [], $verifyAction = "VERIFY")
{
  $userEmail = '';
  if (!isset($_SESSION["email"])) {
    $userEmail = $data["email"];
    $userName = $data["name"];
  } else {
    $userEmail = $_SESSION["email"];
    $result = getBy("*", "users", "user_email", $_SESSION["email"]);
    $userName = $result->result->user_name;
  }
  // check email from database
  $result = getBy("verify_email", "verify", "verify_email", $userEmail);
  $result = $result->rowCount;

  // if email is exist
  if ($result > 0) {
    delBy("verify", "verify_email", $userEmail);
  }

  $email = $userEmail;
  $token = rand(100000, 999999);
  $token_expired = date("Y-m-d H:i:s", time() + (60 * 2));

  InsertData("verify", "verify_email,verify_token,verify_expired,verify_action", "$email,$token,$token_expired,$verifyAction");

  // get code from database
  $getToken = getBy("verify_token", "verify", "verify_email", $userEmail);
  $getToken = $getToken->result;
  // send email 
  SendMail($userName, $userEmail, $getToken->verify_token);
}

function SendMail($userName, $userEmail, $getToken = "")
{
  global $sendEmail;
  if ($sendEmail == true) {
    global $mailHost;
    global $mailUsername;
    global $mailPassword;
    global $mailPort;

    global $mailFromEmail;
    global $mailFromName;
    global $mailSubject;

    //Create an instance; passing `true` enables exceptions
    $mail = new PHPMailer(true);

    try {
      //Server settings
      // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
      $mail->isSMTP();                                            //Send using SMTP
      $mail->Host       = $mailHost;                     //Set the SMTP server to send through
      $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
      $mail->Username   = $mailUsername;                     //SMTP username
      $mail->Password   = $mailPassword;                               //SMTP password
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
      $mail->Port       = $mailPort;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

      //Recipients
      $mail->setFrom($mailFromEmail, $mailFromName);
      $mail->addAddress("$userEmail");               //Name is optional

      //Content
      $mail->isHTML(true);                                  //Set email format to HTML
      $mail->Subject = $mailSubject;
      $mail->Body    = '
          <p>Hai ' . $userName . ' !</p> 
          <p>Your Email: ' . $userEmail . ' </p>  
          <br> 
  
          <p><b>verification Code:</b></p>
          <h3>' . $getToken . '</h3>
          <br>

          <p><b>NOTE: </b><i>if you do not request this verification code please ignore this email, if it is true that you requested this verification code, please do not disclose it to anyone</i></p>
          <br>

          <p>Thanks</p>
          <p>Test Mail</p>';

      $mail->send();

      // echo 'Message has been sent';
      $message = "<p>We have send verify code to your email</p>";
      $message .= "<p>Make sure you insert your code here</p>";
      echo $message;
    } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
  }
}

function Verify()
{
  if (isset($_POST["verify"])) {
    $token = $_POST["code"];

    $verify = getBy("*", "verify", "verify_email", $_SESSION["email"]);
    $verify = $verify->result;

    // get verify status
    $verify_action = $verify->verify_action;

    // if code in database is not same with code user input 
    if ($verify->verify_token != $token) {
      echo "Wrong Token!";
    } else {
      // if code is same with user input
      if ($token > date("H:i:s")) {

        if ($verify_action !== "VERIFY") {
          delBy("verify", "verify_email", $_SESSION["email"]);

          $_SESSION["recover"] = $verify_action;
          header("location:recover.php");
        } else {
          updateData("users", ["email_status" => $_SESSION["email"]], "user_email", $_SESSION["email"]);

          delBy("verify", "verify_email", $_SESSION["email"]);

          header("location:logout.php");
          exit;
        }
      } else {
        echo "Code Expired!";
      }
    }
  }
}

// new function 
function getAll($table, $column = "", $target = "")
{
  if ($column == null || $target == null) {
    $con = getConnection();
    $stat = $con->prepare("SELECT * FROM $table");
    $stat->execute();
    $result = $stat->fetchAll(PDO::FETCH_OBJ);
  } else {
    $con = getConnection();
    $stat = $con->prepare("SELECT * FROM $table WHERE $column = ?");
    $stat->execute([$target]);
    $result = $stat->fetchAll(PDO::FETCH_OBJ);
  }

  return $result;
}

function getBy($columns = [], $table, $column, $target)
{
  $con = getConnection();
  $stat = $con->prepare("SELECT $columns FROM $table WHERE $column = ?");
  $stat->execute([$target]);
  $result = $stat->fetch(PDO::FETCH_OBJ);

  return (object)$result = [
    "rowCount" => $stat->rowCount(),
    "result" => $result
  ];
}

function InsertData($table, $columns, $target)
{
  $targets = explode(",", $target);
  foreach ($targets as $val) {
    $array[] = "'" . $val . "'";
  }
  $target =  implode(",", $array);

  $con = getConnection();
  $stat = $con->prepare("INSERT INTO $table ($columns) values($target)");
  $stat->execute();
}

function updateData($table, $columUpdate = [], $column, $target)
{
  foreach ($columUpdate as $key => $col) {
    $array[] = $key . "=" . "'" . $col . "'";
  }
  $columUpdate = implode(",", $array);

  $con = getConnection();
  $stat = $con->prepare("UPDATE $table SET $columUpdate where $column = ?");
  $stat->execute([$target]);
}

function delBy($table, $column, $target)
{
  $con = getConnection();
  $stat = $con->prepare("DELETE FROM $table WHERE $column = ?");
  $stat->execute([$target]);
}

// end new function 