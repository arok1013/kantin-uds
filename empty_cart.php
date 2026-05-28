<?php
session_start();
unset($_SESSION['cart']);
unset($_SESSION['current_restaurant']);
header("Location: cart.php");
exit;
?>