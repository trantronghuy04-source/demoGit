<?php
// OrderView.php
require_once "db_order.php";
$orders = getAllOrders();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý đơn hàng</title>
    <style>
         body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
         table { width: 100%; border-collapse: collapse; margin-top: 20px; }
         table, th, td { border: 1px solid #ccc; }
         th, td { padding: 10px; text-align: center; }
         .order-item { margin-bottom: 10px; }
         .order-item hr { margin: 5px 0; }
         .btn { padding: 5px 10px; background: green; color: #fff; border: none; border-radius: 3px; cursor: pointer; }
    </style>
</head>
<body>
    <h2>Danh sách đơn hàng</h2>
    <table>
       <tr>
         <th>Order ID</th>
         <th>User ID</th>
         <th>Tên</th>
         <th>Email</th>
         <th>SĐT</th>
         <th>Địa chỉ</th>
         <th>Phương thức TT</th>
         <th>Tổng tiền</th>
         <th>Trạng thái</th>
         <th>Chi tiết đơn hàng</th>
         <th>Hành động</th>
       </tr>
       <?php foreach($orders as $order): ?>
         <tr>
           <td><?php echo htmlspecialchars($order['order_id']); ?></td>
           <td><?php echo htmlspecialchars($order['user_id']); ?></td>
           <td><?php echo htmlspecialchars($order['username']); ?></td>
           <td><?php echo htmlspecialchars($order['email']); ?></td>
           <td><?php echo htmlspecialchars($order['phone']); ?></td>
           <td><?php echo htmlspecialchars($order['shipping_address']); ?></td>
           <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
           <td><?php echo number_format($order['total_price'], 0, ',', '.'); ?> đ</td>
           <td>
             <?php 
              if($order['status'] == 'pending'){
                echo "Chờ xử lý";
              } elseif($order['status'] == 'confirmed'){
                echo "Đã xác nhận & Đang giao hàng";
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
            // Giải mã thông tin chi tiết đơn hàng từ JSON
            $orderDetails = json_decode($order['order_details'], true);
            if (is_array($orderDetails)) {
                foreach ($orderDetails as $item) {
                    echo '<div class="order-item">';
                    // Hiển thị tên sản phẩm
                    echo "<strong>Tên sản phẩm:</strong> " . htmlspecialchars($item['tensp'] ?? 'N/A') . "<br>";
                    // Hiển thị màu sắc (nếu có)
                    if (!empty($item['mausac'])) {
                        echo "<strong>Màu sắc:</strong> " . htmlspecialchars($item['mausac']) . "<br>";
                    }
                    // Hiển thị dung lượng (nếu có)
                    if (!empty($item['dungluong'])) {
                        echo "<strong>Dung lượng:</strong> " . htmlspecialchars($item['dungluong']) . "<br>";
                    }
                    // Hiển thị số lượng
                    echo "<strong>Số lượng:</strong> " . htmlspecialchars($item['quantity'] ?? 'N/A') . "<br>";
                    // Hiển thị giá sau khuyến mãi, định dạng số cho đẹp
                    echo "<strong>Giá sau KM:</strong> " . number_format($item['gia_sau_km'] ?? 0, 0, ',', '.') . " đ<br>";
                    echo "<hr>";
                    echo '</div>';
                }
            } else {
                // Nếu không phải JSON hợp lệ, hiển thị nguyên chuỗi
                echo htmlspecialchars($order['order_details']);
            }
            ?>
          </td>

           <td>
              <?php if($order['status'] == 'pending'): ?>
              <!-- Hiển thị nút xác nhận và nút xóa khi đơn hàng đang chờ xử lý -->
              <form method="post" action="Manager.php?action=ConfirmOrder" onsubmit="return confirm('Bạn có chắc muốn xác nhận đơn hàng này không?');">
                  <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['order_id']); ?>">
                  <input type="hidden" name="status" value="confirmed">
                  <button type="submit" class="btn">Xác nhận đơn hàng</button>
              </form>
              <br>
              <a href="Manager.php?action=DeleteOrder&id=<?php echo $order['order_id']; ?>" onclick="return confirm('Bạn có muốn xóa đơn hàng này không?');">Xóa</a>
              <?php else: ?>
              <!-- Sau khi đơn hàng đã được xác nhận (hoặc có trạng thái khác) thì không còn nút xóa và không hiển thị thao tác -->
              -
              <?php endif; ?>
           </td>
         </tr>
       <?php endforeach; ?>
    </table>
</body>
</html>
