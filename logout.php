<?php
include 'error_handling.php';
session_start();
session_destroy();
header("Location: index.php");
exit;
?>
