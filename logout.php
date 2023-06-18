<?php

require_once __DIR__ . "/function.php";
session_unset();
session_destroy();
header("location:login.php");
