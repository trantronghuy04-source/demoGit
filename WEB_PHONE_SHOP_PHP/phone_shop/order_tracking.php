<?php
session_start();
require_once __DIR__ . "/../DB.php";
require_once __DIR__ . "/../back_end/Order/db_order.php";
require_once __DIR__ . "/../back_end/Revenue/db_revenue.php"; // Thêm require_once để sử dụng hàm insertRevenue

$conn = connect();

// Kiểm tra đăng nhập (nếu chưa đăng nhập chuyển đến trang login)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Xử lý khi khách hàng xác nhận đã nhận hàng thành công
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receive_order'])) {
    $order_id = $_POST['order_id'] ?? '';
    if (empty($order_id)) {
        echo "<script>alert('Order ID không hợp lệ.'); window.location.href='order_tracking.php';</script>";
        exit();
    }
    // Lấy thông tin đơn hàng cần chuyển sang doanh thu
    $order = getOrderById($order_id);
    if (!$order) {
        echo "<script>alert('Không tìm thấy đơn hàng.'); window.location.href='order_tracking.php';</script>";
        exit();
    }
    // Cập nhật trạng thái đơn hàng thành 'delivered'
    $result = updateOrder($order_id, 'delivered');
    if ($result === true) {
       // Nếu cập nhật thành công, chèn dữ liệu vào bảng doanh thu
       $revResult = insertRevenue($order);
       if ($revResult !== true) {
           echo "<script>alert('Lỗi khi chuyển đơn hàng sang doanh thu: " . addslashes($revResult) . "'); window.location.href='order_tracking.php';</script>";
           exit();
       }
       echo "<script>alert('Cảm ơn bạn đã xác nhận nhận hàng thành công.'); window.location.href='order_tracking.php';</script>";
       exit();
    } else {
       echo "<script>alert('Có lỗi xảy ra: " . addslashes($result) . "'); window.location.href='order_tracking.php';</script>";
       exit();
    }
}

// Lấy danh sách đơn hàng của user đang đăng nhập
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$orders = $stmt->fetchAll();
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Thông tin đơn hàng của tôi</title>
  <style>
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    table, th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    .btn { padding: 5px 10px; background: green; color: #fff; border: none; border-radius: 3px; cursor: pointer; }
    .order-details { margin-top: 5px; }
  </style>
</head>
<body>
  <h2>Thông tin đơn hàng của tôi</h2>
  <?php if(count($orders) > 0): ?>
  <table>
    <thead>
      <tr>
        <th>Order ID</th>
        <th>Ngày đặt</th>
        <th>Phương thức TT</th>
        <th>Tổng tiền</th>
        <th>Trạng thái</th>
        <th>Chi tiết đơn hàng</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($orders as $order): ?>
      <tr>
        <td><?php echo htmlspecialchars($order['order_id']); ?></td>
        <td><?php echo htmlspecialchars($order['created_at']); ?></td>
        <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
        <td><?php echo number_format($order['total_price'], 0, ',', '.'); ?> đ</td>
        <td>
          <?php 
            if($order['status'] == 'pending'){
                echo "Chờ xử lý";
            } elseif($order['status'] == 'confirmed'){
                echo "Đã xác nhận";
            } elseif($order['status'] == 'delivered'){
                echo "Đã nhận hàng thành công";
            } elseif($order['status'] == 'cancelled'){
                echo "Bị hủy";
            } else {
                echo htmlspecialchars($order['status']);
            }
          ?>
        </td>
        <td>
          <?php 
          // Giải mã thông tin chi tiết đơn hàng từ JSON để hiển thị thông tin sản phẩm
          $orderDetails = json_decode($order['order_details'], true);
          if(is_array($orderDetails)):
              foreach($orderDetails as $item):
          ?>
          <div class="order-details">
            <strong>Sản phẩm:</strong> <?php echo htmlspecialchars($item['tensp'] ?? 'N/A'); ?><br>
            <strong>Số lượng:</strong> <?php echo htmlspecialchars($item['quantity'] ?? 'N/A'); ?><br>
            <?php if(!empty($item['dungluong'])): ?>
              <strong>Dung lượng:</strong> <?php echo htmlspecialchars($item['dungluong']); ?><br>
            <?php endif; ?>
            <?php if(!empty($item['mausac'])): ?>
              <strong>Màu sắc:</strong> <?php echo htmlspecialchars($item['mausac']); ?><br>
            <?php endif; ?>
          </div>
          <?php 
              endforeach;
          else:
              echo htmlspecialchars($order['order_details']);
          endif;
          ?>
        </td>
        <td>
          <?php if($order['status'] == 'confirmed'): ?>
          <form method="POST" action="order_tracking.php" onsubmit="return confirm('Xác nhận đã nhận hàng thành công?');">
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
            <button type="submit" name="receive_order" class="btn">Đã nhận hàng thành công</button>
          </form>
          <?php else: ?>
          -
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php else: ?>
  <p>Bạn chưa có đơn hàng nào.</p>
  <?php endif; ?>
</body>
</html>
<?php include __DIR__ . '/includes/footer.php'; ?>
