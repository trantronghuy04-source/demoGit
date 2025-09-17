<?php
// --- Đoạn code ban đầu lấy doanh thu ---
ob_start();
require_once "db_revenue.php";
ob_clean();

$search = array();
if(isset($_GET['tennguoidung'])) {
   $search['tennguoidung'] = trim($_GET['tennguoidung']);
}
if(isset($_GET['chitietdonhang'])) {
   $search['chitietdonhang'] = trim($_GET['chitietdonhang']);
}
if(isset($_GET['ngaymua_tu']) && isset($_GET['ngaymua_den'])) {
   $search['ngaymua_tu'] = $_GET['ngaymua_tu'];
   $search['ngaymua_den'] = $_GET['ngaymua_den'];
}

$revenues = getAllRevenues($search);

// Tính tổng doanh thu của tất cả các đơn hàng
$totalRevenue = 0;
foreach($revenues as $rev) {
    $totalRevenue += $rev['tongtiendonhang'];
}

// Tính dữ liệu cho biểu đồ doanh thu theo nhóm (mặc định nhóm theo ngày)
$groupBy = isset($_GET['groupBy']) ? $_GET['groupBy'] : 'day';
$aggData = getRevenueAggregation($groupBy);
$chartLabels = array();
$chartData = array();
foreach($aggData as $row) {
    $chartLabels[] = $row['period'];
    $chartData[] = $row['total_revenue'];
}

// --- Tổng hợp doanh thu theo sản phẩm ---
// Khởi tạo mảng tóm tắt sản phẩm
$productSummary = array();

foreach($revenues as $rev) {
    // Giải mã JSON chứa chi tiết đơn hàng
    $orderDetails = json_decode($rev['chitietdonhang'], true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($orderDetails)) {
        foreach($orderDetails as $item) {
            $masp = $item['masp'];
            // Nếu sản phẩm chưa có trong mảng, khởi tạo dữ liệu
            if (!isset($productSummary[$masp])) {
                $productSummary[$masp] = array(
                    'masp' => $masp,
                    'tensp' => $item['tensp'],
                    'total_quantity' => 0,
                    'total_revenue' => 0,
                    'stock' => $item['stock']  // Lấy giá trị "cung" từ đơn hàng đầu tiên
                );
            }
            // Cộng dồn số lượng bán và doanh thu (số lượng * giá xuất)
            $quantity = $item['quantity'];
            $giaxuat = $item['giaxuat'];
            $productSummary[$masp]['total_quantity'] += $quantity;
            $productSummary[$masp]['total_revenue'] += $quantity * $giaxuat;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý doanh thu</title>
    <!-- Link Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Import Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        .page-title {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .search-form {
            background: #ffffff;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
        }
        .table-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        /* CSS cho thanh cuộn */
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        .chart-container {
            background: #ffffff;
            padding: 20px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            text-align: center;
        }
        .chart-container h3 {
            margin-bottom: 20px;
        }
        .btn-search {
            min-width: 120px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center page-title">Quản lý doanh thu</h2>
        
        <!-- Form tìm kiếm -->
        <form class="row g-3 mb-4 search-form" method="get" action="Manager.php">
            <input type="hidden" name="action" value="Revenue">
            <div class="col-md-3">
                <label class="form-label">Tên người dùng</label>
                <input type="text" class="form-control" name="tennguoidung" value="<?php echo isset($search['tennguoidung']) ? htmlspecialchars($search['tennguoidung']) : ''; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Chi tiết đơn hàng</label>
                <input type="text" class="form-control" name="chitietdonhang" value="<?php echo isset($search['chitietdonhang']) ? htmlspecialchars($search['chitietdonhang']) : ''; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Ngày mua từ</label>
                <input type="date" class="form-control" name="ngaymua_tu" value="<?php echo isset($search['ngaymua_tu']) ? htmlspecialchars($search['ngaymua_tu']) : ''; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Ngày mua đến</label>
                <input type="date" class="form-control" name="ngaymua_den" value="<?php echo isset($search['ngaymua_den']) ? htmlspecialchars($search['ngaymua_den']) : ''; ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Nhóm theo</label>
                <select name="groupBy" class="form-select">
                    <option value="day" <?php echo ($groupBy == 'day') ? 'selected' : ''; ?>>Ngày</option>
                    <option value="month" <?php echo ($groupBy == 'month') ? 'selected' : ''; ?>>Tháng</option>
                    <option value="year" <?php echo ($groupBy == 'year') ? 'selected' : ''; ?>>Năm</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-search">Tìm kiếm</button>
            </div>
        </form>
        
        <div class="row">
            <!-- Bảng hiển thị doanh thu chi tiết -->
            <div class="col-lg-7 mb-4">
                <div class="table-container">
                    <h5 class="mb-3">Danh sách doanh thu</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã doanh thu</th>
                                    <th>Order ID</th>
                                    <th>User ID</th>
                                    <th>Tên người dùng</th>
                                    <th>Chi tiết đơn hàng</th>
                                    <th>Tổng tiền đơn hàng</th>
                                    <th>Ngày mua</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach($revenues as $rev): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($rev['ma_doanhthu']); ?></td>
                                    <td><?php echo htmlspecialchars($rev['order_id']); ?></td>
                                    <td><?php echo htmlspecialchars($rev['user_id']); ?></td>
                                    <td><?php echo htmlspecialchars($rev['tennguoidung']); ?></td>
                                    <td>
                                        <?php
                                        $orderDetails = json_decode($rev['chitietdonhang'], true);
                                        if (json_last_error() === JSON_ERROR_NONE && is_array($orderDetails)) {
                                            foreach ($orderDetails as $item) {
                                                echo '<strong>' . htmlspecialchars($item['tensp']) . '</strong> - ';
                                                echo htmlspecialchars($item['mausac']) . ', ';
                                                echo htmlspecialchars($item['dungluong']) . ' - ';
                                                echo 'Số lượng: ' . htmlspecialchars($item['quantity']) . ', ';
                                                echo 'Giá: ' . number_format($item['giaxuat'], 0, ',', '.') . ' đ';
                                                echo '<br>';
                                            }
                                        } else {
                                            echo htmlspecialchars($rev['chitietdonhang']);
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo number_format($rev['tongtiendonhang'], 0, ',', '.'); ?> đ</td>
                                    <td><?php echo htmlspecialchars($rev['created_at']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Bảng tổng hợp doanh thu theo sản phẩm -->
                <div class="table-container">
                    <h5 class="mb-3">Tổng doanh thu theo sản phẩm</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Mã sản phẩm</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Số lượng bán</th>
                                    <th>Tổng doanh thu</th>
                                    <th>Cung</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($productSummary)): ?>
                                <?php foreach($productSummary as $prod): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($prod['masp']); ?></td>
                                        <td><?php echo htmlspecialchars($prod['tensp']); ?></td>
                                        <td><?php echo htmlspecialchars($prod['total_quantity']); ?></td>
                                        <td><?php echo number_format($prod['total_revenue'], 0, ',', '.'); ?> đ</td>
                                        <td><?php echo htmlspecialchars($prod['stock']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center">Không có dữ liệu</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Biểu đồ doanh thu và tổng doanh thu -->
            <div class="col-lg-5 mb-4">
                <div class="chart-container">
                    <h3>Doanh thu theo <?php echo ($groupBy == 'day') ? 'Ngày' : (($groupBy == 'month') ? 'Tháng' : 'Năm'); ?></h3>
                    <canvas id="revenueChart" width="400" height="300"></canvas>
                    <!-- Hiển thị tổng doanh thu dưới biểu đồ -->
                    <p class="mt-3 fw-bold">Tổng doanh thu: <?php echo number_format($totalRevenue, 0, ',', '.'); ?> đ</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Khởi tạo biểu đồ -->
    <script>
      const ctx = document.getElementById('revenueChart').getContext('2d');
      const revenueChart = new Chart(ctx, {
          type: 'pie', // Bạn có thể chuyển sang 'bar' nếu muốn
          data: {
              labels: <?php echo json_encode($chartLabels); ?>,
              datasets: [{
                  label: 'Doanh thu',
                  data: <?php echo json_encode($chartData); ?>,
                  backgroundColor: [
                      'rgba(255, 99, 132, 0.7)',
                      'rgba(54, 162, 235, 0.7)',
                      'rgba(255, 206, 86, 0.7)',
                      'rgba(75, 192, 192, 0.7)',
                      'rgba(153, 102, 255, 0.7)',
                      'rgba(255, 159, 64, 0.7)'
                  ],
                  borderColor: [
                      'rgba(255, 99, 132, 1)',
                      'rgba(54, 162, 235, 1)',
                      'rgba(255, 206, 86, 1)',
                      'rgba(75, 192, 192, 1)',
                      'rgba(153, 102, 255, 1)',
                      'rgba(255, 159, 64, 1)'
                  ],
                  borderWidth: 1
              }]
          },
          options: {
              responsive: true,
              plugins: {
                  legend: {
                      position: 'top',
                  },
                  title: {
                      display: true,
                      text: 'Doanh thu theo <?php echo ($groupBy == "day") ? "Ngày" : (($groupBy == "month") ? "Tháng" : "Năm"); ?>'
                  }
              }
          }
      });
    </script>
    
    <!-- Bootstrap JS (tùy chọn) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
