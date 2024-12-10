<?php
session_start(); 

$_SESSION = [];

session_destroy();

header("Location: home-page.php");
exit();
?>
