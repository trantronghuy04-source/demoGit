<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../DB.php";
require_once __DIR__ . "/../back_end/Cart/db_cart.php";

$conn = connect();

if (!isset($_GET['cart_id'])) {
    header("Location: cart.php");
    exit();
}

$cart_id = $_GET['cart_id'];
$result = deleteCart($cart_id);

header("Location: cart.php");
exit();
?>
