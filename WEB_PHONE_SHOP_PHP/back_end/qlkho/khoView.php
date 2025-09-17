<?php
require_once "./back_end/Adncc/db_AdNccView.php";
require_once "./back_end/AdProduct/db_adProduct.php";
require_once "./back_end/AdProductType/db_AdProductType.php";
// Kết nối CSDL
$db = connect();

// Lấy từ khóa tìm kiếm từ GET (nếu có)
$keyword = "";
if (isset($_GET['keyword'])) {
    $keyword = trim($_GET['keyword']);
}

$sql = "SELECT p.tensp, n.tenncc, t.ten_loaisp, p.hinhanh, p.soluong, p.gianhap, p.giaxuat,
               IF(p.soluong > 0, 'Còn hàng', 'Hết hàng') AS trangthai,
               (p.soluong * p.gianhap) AS tong_gianhap,
               (p.soluong * p.giaxuat) AS tong_giaxuat
        FROM ad_product p
        JOIN ad_nhacc n ON p.mancc = n.mancc
        JOIN ad_producttype t ON p.ma_loaisp = t.ma_loaisp";
        
if (!empty($keyword)) {
    $sql .= " WHERE p.tensp COLLATE utf8mb4_general_ci LIKE :keyword";
}

$sql .= " ORDER BY p.tensp";

$stm = $db->prepare($sql);
if (!empty($keyword)) {
    $searchTerm = "%" . $keyword . "%";
    $stm->bindParam(':keyword', $searchTerm);
}
$stm->execute();
$inventory = $stm->fetchAll(PDO::FETCH_ASSOC);

// Tính tổng tiền nhập và xuất toàn bộ kho
$sql_total = "SELECT SUM(p.soluong * p.gianhap) AS total_gianhap,
                     SUM(p.soluong * p.giaxuat) AS total_giaxuat
              FROM ad_product p";
$stm_total = $db->prepare($sql_total);
$stm_total->execute();
$totalValues = $stm_total->fetch(PDO::FETCH_ASSOC);

// Tính tổng số lượng sản phẩm trong kho
$sql_total_quantity = "SELECT SUM(p.soluong) AS total_quantity FROM ad_product p";
$stm_total_quantity = $db->prepare($sql_total_quantity);
$stm_total_quantity->execute();
$totalQuantity = $stm_total_quantity->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý kho hàng</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .search-box { text-align: center; margin-bottom: 20px; }
        .search-box input[type="text"] { width: 300px; padding: 8px; }
        .search-box button { padding: 8px 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .inventory-img { width: 100px; }
        /* Thanh cuộn cho bảng */
        .scrollable-table {
            overflow-x: auto;  /* Cho phép cuộn ngang nếu bảng rộng */
            overflow-y: auto;  /* Cho phép cuộn dọc nếu cần */
            max-height: 400px; /* Giới hạn chiều cao, vượt quá sẽ xuất hiện thanh cuộn */
        }
    </style>
</head>
<body>
    <h1 align="center">Quản lý kho hàng</h1>
    
    <!-- Thanh tìm kiếm, gửi qua Manager.php với action=qlkho -->
    <div class="search-box">
        <form method="get" action="Manager.php">
            <input type="hidden" name="action" value="qlkho">
            <input type="text" name="keyword" placeholder="Tìm theo tên sản phẩm" value="<?php echo htmlspecialchars($keyword); ?>">
            <button type="submit">Tìm kiếm</button>
        </form>
    </div>
    
    <!-- Bảng danh sách kho hàng với thanh cuộn -->
    <div class="scrollable-table">
        <table>
            <thead>
                <tr>
                    <th>Tên SP</th>
                    <th>Tên NCC</th>
                    <th>Tên Loại SP</th>
                    <th>Hình ảnh</th>
                    <th>Số lượng</th>
                    <th>Giá nhập (VNĐ)</th>
                    <th>Giá xuất (VNĐ)</th>
                    <th>Trạng thái</th>
                    <th>Tổng tiền nhập (VNĐ)</th>
                    <th>Tổng tiền xuất (VNĐ)</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($inventory) > 0): ?>
                    <?php foreach($inventory as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['tensp']); ?></td>
                        <td><?php echo htmlspecialchars($item['tenncc']); ?></td>
                        <td><?php echo htmlspecialchars($item['ten_loaisp']); ?></td>
                        <td>
                            <?php if(!empty($item['hinhanh'])): ?>
                                <img src="./public/<?php echo htmlspecialchars($item['hinhanh']); ?>" class="inventory-img" alt="<?php echo htmlspecialchars($item['tensp']); ?>">
                            <?php else: ?>
                                Không có ảnh
                            <?php endif; ?>
                        </td>
                        <td><?php echo $item['soluong']; ?></td>
                        <td><?php echo number_format($item['gianhap'], 0, ',', '.'); ?></td>
                        <td><?php echo number_format($item['giaxuat'], 0, ',', '.'); ?></td>
                        <td><?php echo $item['trangthai']; ?></td>
                        <td><?php echo number_format($item['tong_gianhap'], 0, ',', '.'); ?></td>
                        <td><?php echo number_format($item['tong_giaxuat'], 0, ',', '.'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="10">Không tìm thấy sản phẩm nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Bảng tổng tiền hàng trong kho -->
    <table>
        <tr>
            <th>Tổng tiền nhập hàng trong kho (VNĐ)</th>
            <th>Tổng tiền xuất hàng trong kho (VNĐ)</th>
        </tr>
        <tr>
            <td><?php echo number_format($totalValues['total_gianhap'], 0, ',', '.'); ?></td>
            <td><?php echo number_format($totalValues['total_giaxuat'], 0, ',', '.'); ?></td>
        </tr>
    </table>
    
    <!-- Hiển thị tổng số lượng sản phẩm trong kho -->
    <table>
        <tr>
            <th>Tổng số lượng sản phẩm trong kho</th>
        </tr>
        <tr>
            <td><?php echo number_format($totalQuantity['total_quantity'], 0, ',', '.'); ?></td>
        </tr>
    </table>
</body>
</html>
