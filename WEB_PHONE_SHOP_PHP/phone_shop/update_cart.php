<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../DB.php";
require_once __DIR__ . "/../back_end/Cart/db_cart.php";

$conn = connect();

if (!isset($_GET['cart_id']) || !isset($_GET['quantity'])) {
    header("Location: cart.php");
    exit();
}

$cart_id = $_GET['cart_id'];
$new_quantity = (int)$_GET['quantity'];
if ($new_quantity < 1) {
    $new_quantity = 1;
}

// Lấy thông tin giỏ hàng và số lượng tồn kho của sản phẩm
$stmt = $conn->prepare("
    SELECT cart.*, ad_product.soluong 
    FROM cart 
    JOIN ad_product ON cart.masp = ad_product.masp 
    WHERE cart.cart_id = :cart_id
");
$stmt->execute(['cart_id' => $cart_id]);
$item = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$item) {
    header("Location: cart.php");
    exit();
}

$product_stock = $item['soluong'];
if ($new_quantity > $product_stock) {
    $new_quantity = $product_stock; // không vượt quá tồn kho
}

// Gọi hàm cập nhật giỏ hàng đã có (updateCart) từ db_cart.php
$result = updateCart($cart_id, $item['user_id'], $item['masp'], $item['dungluong'], $item['mausac'], $new_quantity);

header("Location: cart.php");
exit();
?>
