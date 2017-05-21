<?php
session_start();
if(!empty($_SESSION['logged_in'])) {
    header('Location: welcome.php');
} else {
    header('Location: login.php');
}
?>