<?php
session_start();
if ($_SESSION['login'] !== 'yes') {
    header("Location: signin.php");
    exit;
  }
  if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 600)) {
      session_unset();
      session_destroy();
      header("Location: signin.php?session=timeout");
  }
  $_SESSION['LAST_ACTIVITY'] = time();

?>