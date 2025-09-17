<?php
require_once "db_order.php";  // File chứa các hàm insertOrder, getOrderById, updateOrder,...
require_once "../AdProduct/db_adProduct.php"; // Để dùng reduceProductStock()

// Kiểm tra tồn tại biến GET
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Order ID không hợp lệ");
}

$order_id = $_GET['id'];
$orderData = getOrderById($order_id);
if (!$orderData) {
    die("Không tìm thấy đơn hàng");
}

// Xử lý cập nhật đơn hàng khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $result = updateOrder($order_id, $status);
    if ($result === true) {
        // Nếu trạng thái chuyển thành "delivered"
        if ($status === 'delivered') {
            require_once "db_revenue.php";
            $revResult = insertRevenue($orderData);
            if ($revResult !== true) {
                echo $revResult;
                exit();
            }
            
            // Giải mã JSON chứa order_details
            $orderDetails = json_decode($orderData['order_details'], true);
            if (is_array($orderDetails)) {
                foreach ($orderDetails as $item) {
                    if (isset($item['masp']) && isset($item['quantity'])) {
                        $updateResult = reduceProductStock($item['masp'], $item['quantity']);
                        if ($updateResult !== true) {
                            echo $updateResult;
                            exit();
                        }
                    } else {
                        echo "Thông tin sản phẩm trong order_details không đầy đủ.";
                        exit();
                    }
                }
            } else {
                echo "Dữ liệu order_details không hợp lệ.";
                exit();
            }
        }
        header("Location: Manager.php?action=Order");
        exit();
    } else {
        echo $result;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật đơn hàng</title>
</head>
<body>
    <h2>Cập nhật đơn hàng</h2>
    <form method="post">
         <label>Trạng thái đơn hàng:</label>
         <select name="status" required>
             <option value="pending" <?php echo ($orderData['status'] === 'pending') ? 'selected' : ''; ?>>Chờ xử lý</option>
             <option value="confirmed" <?php echo ($orderData['status'] === 'confirmed') ? 'selected' : ''; ?>>Đã xác nhận & Đang giao hàng</option>
             <option value="cancelled" <?php echo ($orderData['status'] === 'cancelled') ? 'selected' : ''; ?>>Bị hủy</option>
             <option value="delivered" <?php echo ($orderData['status'] === 'delivered') ? 'selected' : ''; ?>>Đã nhận hàng thành công</option>
         </select>
         <button type="submit">Cập nhật</button>
    </form>
</body>
</html>
