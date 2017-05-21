<?php
session_start();
if($_SESSION['logged_in']) {
    $_SESSION['logged_in'] = 0;
}
header('Location: login.php');
?>