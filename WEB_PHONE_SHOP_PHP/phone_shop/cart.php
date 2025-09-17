<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once __DIR__ . "/../DB.php";
$conn = connect();

$user_id = $_SESSION['user_id'];

// Lấy dữ liệu giỏ hàng (JOIN với bảng ad_product và qlkm để lấy thêm thông tin khuyến mãi)
$stmt = $conn->prepare("
    SELECT cart.*, 
           ad_product.tensp, 
           ad_product.hinhanh, 
           ad_product.giaxuat, 
           ad_product.makm,
           ad_product.soluong,
           qlkm.thongtinkm,
           (ad_product.giaxuat * (1 - IFNULL(qlkm.phantramkm, 0) / 100)) AS gia_sau_km
    FROM cart
    JOIN ad_product ON cart.masp = ad_product.masp
    LEFT JOIN qlkm ON ad_product.makm = qlkm.makm
    WHERE cart.user_id = :user_id
");
$stmt->execute(['user_id' => $user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tính tổng tiền và tổng giá gốc dựa trên số lượng mua
$tong_tien = 0;
$tong_gia_goc = 0;
foreach ($cart_items as $item) {
    $tong_gia_goc += $item['giaxuat'] * $item['so_luong'];
    $tong_tien += $item['gia_sau_km'] * $item['so_luong'];
}
$tong_khuyen_mai = $tong_gia_goc - $tong_tien;
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng</title>
    <style>
        /* Reset CSS */
        * {
            margin: 0; 
            padding: 0; 
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            color: #333;
        }
        .cart-container {
            display: flex;
            max-width: 1200px;
            margin: 20px auto;
            gap: 20px;
        }
        /* Danh sách sản phẩm */
        .cart-left {
            flex: 2;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
        }
        .cart-item {
            border-bottom: 1px solid #ddd;
            padding: 20px 0;
            display: flex;
            gap: 15px;
            align-items: flex-start;
        }
        .cart-item:last-child {
            border-bottom: none;
        }
        .item-img img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 6px;
        }
        .item-info {
            flex: 1;
        }
        .item-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .item-detail {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .item-options {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }
        .item-price {
            font-size: 15px;
            margin: 10px 0;
        }
        .price-current {
            color: #d0021b;
            font-weight: 600;
            margin-right: 10px;
        }
        .price-old {
            text-decoration: line-through;
            color: #999;
            font-size: 14px;
        }
        .quantity-box {
            display: inline-flex;
            align-items: center;
            margin-right: 10px;
        }
        .quantity-box a {
            display: inline-block;
            width: 30px;
            height: 30px;
            background: #f1f1f1;
            text-align: center;
            line-height: 30px;
            text-decoration: none;
            color: #333;
            border-radius: 4px;
            margin: 0 5px;
        }
        .quantity-box input {
            width: 40px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            height: 30px;
        }
        .item-delete {
            color: #999;
            cursor: pointer;
            font-size: 14px;
            text-decoration: underline;
        }
        /* Tóm tắt đơn hàng */
        .cart-right {
            flex: 1;
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            height: fit-content;
        }
        .summary-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .summary-row span {
            font-size: 14px;
        }
        .summary-total {
            font-size: 18px;
            font-weight: 600;
            color: #d0021b;
            margin-top: 10px;
        }
        .summary-button {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 12px 0;
            background: #ff424e;
            color: #fff;
            text-align: center;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
        }
        .summary-button:hover {
            opacity: 0.9;
        }
        @media (max-width: 768px) {
            .cart-container {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
<div class="cart-container">
    <!-- Danh sách sản phẩm -->
    <div class="cart-left">
        <h3 style="font-size:18px; margin-bottom:20px;">Giỏ hàng (<?php echo count($cart_items); ?>)</h3>
        <?php if (empty($cart_items)): ?>
            <p>Giỏ hàng của bạn hiện tại không có sản phẩm nào.</p>
        <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
            <div class="cart-item">
                <div class="item-img">
                    <img src="../public/images/<?php echo htmlspecialchars($item['hinhanh']); ?>" alt="Sản phẩm">
                </div>
                <div class="item-info">
                    <div class="item-title">
                        <?php echo htmlspecialchars($item['tensp']) . " (" . htmlspecialchars($item['masp']) . ")"; ?>
                    </div>
                    <div class="item-detail">
                        <?php echo !empty($item['thongtinkm']) ? "Khuyến mãi: " . htmlspecialchars($item['thongtinkm']) : "Không có khuyến mãi"; ?>
                    </div>
                    <div class="item-options">
                        <?php if (!empty($item['dungluong'])): ?>
                            <span>Dung lượng: <?php echo htmlspecialchars($item['dungluong']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($item['mausac'])): ?>
                            <span style="margin-left:10px;">Màu sắc: <?php echo htmlspecialchars($item['mausac']); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="item-price">
                        <span class="price-current">
                            <?php echo number_format($item['gia_sau_km'], 0, ',', '.'); ?> đ
                        </span>
                        <span class="price-old">
                            <?php echo number_format($item['giaxuat'], 0, ',', '.'); ?> đ
                        </span>
                    </div>
                    <div class="quantity-box">
                        <!-- Nút giảm: chuyển sang update_cart.php với số lượng giảm 1 -->
                        <a href="update_cart.php?cart_id=<?php echo $item['cart_id']; ?>&quantity=<?php echo max($item['so_luong'] - 1, 1); ?>">-</a>
                        <input type="text" value="<?php echo $item['so_luong']; ?>" readonly />
                        <!-- Nút tăng: chuyển sang update_cart.php với số lượng tăng 1 (không vượt qua tồn kho) -->
                        <a href="update_cart.php?cart_id=<?php echo $item['cart_id']; ?>&quantity=<?php echo ($item['so_luong'] + 1 > $item['soluong'] ? $item['soluong'] : $item['so_luong'] + 1); ?>">+</a>
                    </div>
                    <span class="item-delete">
                        <a href="delete_cart.php?cart_id=<?php echo $item['cart_id']; ?>" onclick="return confirm('Bạn có muốn xóa không?');">
                            Xóa
                        </a>
                    </span>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <!-- Tóm tắt đơn hàng -->
    <div class="cart-right">
        <div class="summary-title">Thông tin đơn hàng</div>
        <div class="summary-row">
            <span>Tổng tiền</span>
            <span><?php echo number_format($tong_gia_goc, 0, ',', '.'); ?> đ</span>
        </div>
        <div class="summary-row">
            <span>Tổng khuyến mãi</span>
            <span><?php echo number_format($tong_khuyen_mai, 0, ',', '.'); ?> đ</span>
        </div>
        <div class="summary-row">
            <span>Cần thanh toán</span>
            <span class="summary-total"><?php echo number_format($tong_tien, 0, ',', '.'); ?> đ</span>
        </div>
        <!-- Nút chuyển hướng đến trang thanh toán (checkout.php) -->
        <a href="checkout.php" class="summary-button">Xác nhận đơn</a>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
