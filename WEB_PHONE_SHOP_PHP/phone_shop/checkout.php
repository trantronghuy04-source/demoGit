<?php
session_start();
require_once __DIR__ . "/../DB.php"; // Kết nối đến CSDL
$conn = connect();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Hàm tạo chuỗi ngẫu nhiên (dùng cho mã QR)
function generateRandomString($length = 10) {
    return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
}
$random_code = generateRandomString();

// Lấy thông tin người dùng (username, email, phone, address)
$user_query = "SELECT username, email, phone, address FROM users WHERE user_id = :user_id";
$user_stmt = $conn->prepare($user_query);
$user_stmt->execute([':user_id' => $user_id]);
$user_info = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Lấy thông tin giỏ hàng (các trường cần hiển thị)
// Lấy trường masp từ bảng ad_product thông qua JOIN
$query = "
    SELECT 
      cart.cart_id, 
      cart.so_luong AS quantity, 
      cart.dungluong, 
      cart.mausac,
      ad_product.masp,                -- Lấy masp từ ad_product
      ad_product.tensp, 
      ad_product.hinhanh, 
      ad_product.giaxuat, 
      ad_product.makm,
      ad_product.soluong AS stock, 
      IFNULL(qlkm.phantramkm, 0) AS phantramkm,
      (ad_product.giaxuat * (1 - IFNULL(qlkm.phantramkm, 0) / 100)) AS gia_sau_km
    FROM cart 
    JOIN ad_product ON cart.masp = ad_product.masp 
    LEFT JOIN qlkm ON ad_product.makm = qlkm.makm 
    WHERE cart.user_id = :user_id
";
$stmt = $conn->prepare($query);
$stmt->execute([':user_id' => $user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tính tổng tiền và tổng giá gốc
$total_price = 0;
$total_origin = 0;
foreach ($cart_items as $item) {
    $origin = $item['giaxuat'] * $item['quantity'];
    $price = $item['gia_sau_km'] * $item['quantity'];
    $total_origin += $origin;
    $total_price += $price;
}
$total_discount = $total_origin - $total_price;

// Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Xử lý xóa sản phẩm khỏi giỏ hàng
    if (isset($_POST['remove_item'])) {
        $cart_id = $_POST['cart_id'];
        $delete_query = "DELETE FROM cart WHERE cart_id = :cart_id AND user_id = :user_id";
        $stmtDel = $conn->prepare($delete_query);
        if ($stmtDel->execute([':cart_id' => $cart_id, ':user_id' => $user_id])) {
            echo "<script>alert('Sản phẩm đã được xóa khỏi giỏ hàng!'); window.location.href = 'checkout.php';</script>";
            exit();
        } else {
            echo "<script>alert('Lỗi khi xóa sản phẩm.');</script>";
        }
    }
    // Xử lý đặt đơn hàng
    elseif (isset($_POST['confirm_checkout'])) {
        $payment_method = $_POST['payment_method'];
        $shipping_address = trim($_POST['shipping_address']);
        if (empty($shipping_address)) {
            $shipping_address = $user_info['address'];
        }
        $confirm_bank_transfer = isset($_POST['confirm_bank_transfer']) ? 1 : 0;
        
        // Lưu thông tin giỏ hàng dưới dạng JSON (order_details sẽ chứa masp)
        $order_details = json_encode($cart_items);
        
        // Bao gồm file db_order.php để sử dụng hàm insertOrder
        require_once __DIR__ . "/../back_end/Order/db_order.php";
        
        if ($payment_method === 'bank_transfer' && !$confirm_bank_transfer) {
            echo "<script>alert('Vui lòng xác nhận thanh toán chuyển khoản sau khi quét mã QR.');</script>";
        } else {
            $result = insertOrder(
                $user_id,
                $user_info['username'],
                $user_info['email'],
                $user_info['phone'],
                $shipping_address,
                $payment_method,
                $confirm_bank_transfer,
                $total_origin,
                $total_discount,
                $total_price,
                $order_details,
                $random_code
            );
            if ($result === true) {
                // Xóa giỏ hàng sau khi đặt đơn hàng thành công
                $stmtDelAll = $conn->prepare("DELETE FROM cart WHERE user_id = :user_id");
                $stmtDelAll->execute([':user_id' => $user_id]);
                echo "<script>alert('Đơn hàng đã được đặt thành công!'); window.location.href = 'new.php';</script>";
                exit();
            } else {
                echo "<script>alert('Có lỗi xảy ra: $result');</script>";
            }
        }
    }
}
?>
<?php include __DIR__ . '/../phone_shop/includes/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thanh toán - Checkout</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 20px; color: #333; }
        .container { max-width: 1200px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
        h2 { margin-bottom: 20px; }
        .user-info, .shipping-info { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="tel"], textarea, select {
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 10px;
        }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 10px; text-align: center; }
        .summary { text-align: right; margin-bottom: 20px; }
        .summary strong { font-size: 18px; color: #d0021b; }
        .btn { padding: 10px 20px; background: orange; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { opacity: 0.9; }
        /* Phần QR code */
        #bank_transfer_section {
            display: none;
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        #qrcode {
            display: inline-block;
            margin: 10px auto;
        }
    </style>
    <!-- Thư viện QRCode -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>
<body>
<div class="container">
    <h2>Thanh toán</h2>
    
    <!-- Thông tin người dùng -->
    <div class="user-info">
        <h3>Thông tin người dùng</h3>
        <label>Họ và tên:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user_info['username']); ?>" readonly>
        
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user_info['email']); ?>" readonly>
        
        <label>Số điện thoại:</label>
        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user_info['phone']); ?>" readonly>
    </div>
    
    <!-- Bảng giỏ hàng -->
    <?php if (!empty($cart_items)): ?>
        <table>
            <thead>
                <tr>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Đơn giá (VNĐ)</th>
                    <th>Thành tiền (VNĐ)</th>
                    <th>Thông tin</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart_items as $item): 
                    $item_total = $item['gia_sau_km'] * $item['quantity'];
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['tensp']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo number_format($item['gia_sau_km'], 0, ',', '.'); ?></td>
                        <td><?php echo number_format($item_total, 0, ',', '.'); ?></td>
                        <td>
                            <?php if (!empty($item['dungluong'])): ?>
                                <div><strong>Dung lượng:</strong> <?php echo htmlspecialchars($item['dungluong']); ?></div>
                            <?php endif; ?>
                            <?php if (!empty($item['mausac'])): ?>
                                <div><strong>Màu sắc:</strong> <?php echo htmlspecialchars($item['mausac']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" action="checkout.php" style="margin:0;" onsubmit="return confirm('Bạn có muốn xóa sản phẩm này?');">
                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                <button type="submit" name="remove_item" class="btn">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3"><strong>Tổng cộng:</strong></td>
                    <td colspan="3"><strong><?php echo number_format($total_price, 0, ',', '.'); ?> VNĐ</strong></td>
                </tr>
            </tbody>
        </table>
    <?php else: ?>
        <p>Giỏ hàng của bạn trống.</p>
    <?php endif; ?>
    
    <!-- Form thanh toán -->
    <form method="POST" action="checkout.php" id="checkout_form">
        <div class="shipping-info">
            <h3>Địa chỉ nhận hàng</h3>
            <label>Địa chỉ:</label>
            <textarea name="shipping_address" id="shipping_address" rows="3"><?php echo htmlspecialchars($user_info['address']); ?></textarea>
        </div>
        
        <label for="payment_method">Chọn hình thức thanh toán:</label>
        <select name="payment_method" id="payment_method" required>
            <option value="">-- Chọn --</option>
            <option value="cash">Thanh toán khi nhận hàng</option>
            <option value="bank_transfer">Chuyển khoản</option>
        </select>
        
        <div id="bank_transfer_section">
            <h3>Thanh toán chuyển khoản</h3>
            <div id="qrcode"></div>
            <p>Quét mã QR trên để thanh toán.</p>
            <button type="submit" name="confirm_checkout" value="bank_transfer" class="btn">Xác nhận thanh toán chuyển khoản</button>
            <input type="hidden" name="confirm_bank_transfer" value="1">
        </div>
        
        <div id="cod_section">
            <button type="submit" name="confirm_checkout" value="cash" class="btn" style="margin-top:20px;">Xác nhận đơn hàng</button>
        </div>
    </form>
    
    <div class="summary">
        <p>Tổng giá gốc: <?php echo number_format($total_origin, 0, ',', '.'); ?> VNĐ</p>
        <p>Tổng khuyến mãi: <?php echo number_format($total_discount, 0, ',', '.'); ?> VNĐ</p>
        <p>Phải thanh toán: <strong><?php echo number_format($total_price, 0, ',', '.'); ?> VNĐ</strong></p>
    </div>
</div>

<script>
    document.getElementById('payment_method').addEventListener('change', function() {
        var method = this.value;
        if (method === 'bank_transfer') {
            document.getElementById('bank_transfer_section').style.display = 'block';
            document.getElementById('cod_section').style.display = 'none';
            if (!document.getElementById('qrcode').hasChildNodes()) {
                new QRCode(document.getElementById("qrcode"), {
                    text: "<?php echo $random_code; ?>",
                    width: 200,
                    height: 200
                });
            }
        } else if (method === 'cash') {
            document.getElementById('bank_transfer_section').style.display = 'none';
            document.getElementById('cod_section').style.display = 'block';
        } else {
            document.getElementById('bank_transfer_section').style.display = 'none';
            document.getElementById('cod_section').style.display = 'block';
        }
    });
</script>
</body>
<?php include '../phone_shop/includes/footer.php'; ?>
</html>
