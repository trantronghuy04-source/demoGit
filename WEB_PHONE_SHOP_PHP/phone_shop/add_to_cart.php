<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../DB.php";
require_once __DIR__ . "/../back_end/Cart/db_cart.php";

$conn = connect();

if (!isset($_GET['masp'])) {
    echo "Sản phẩm không tồn tại.";
    exit;
}

$masp     = $_GET['masp'];
$so_luong = isset($_GET['quantity']) ? (int)$_GET['quantity'] : 1;
$dungluong = isset($_GET['capacity']) ? $_GET['capacity'] : '';
$mausac    = isset($_GET['color']) ? $_GET['color'] : '';

// Lấy thông tin sản phẩm từ bảng ad_product (để kiểm tra sự tồn tại)
$sql = "SELECT * FROM ad_product WHERE masp = :masp";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':masp', $masp);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    echo "Không tìm thấy sản phẩm.";
    exit;
}

$user_id = $_SESSION['user_id'];
insertCart($user_id, $masp, $so_luong, $dungluong, $mausac);

header("Location: cart.php");
exit();
?>
