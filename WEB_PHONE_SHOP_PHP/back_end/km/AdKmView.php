<?php
require_once "db_AdKmView.php";

// Tự sinh mã khuyến mãi mới
$auto_makm = generatePromotionCode();

// Khi form được submit để thêm mới
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btn_submit"]) && $_POST["btn_submit"] == "Thêm mới") {
    // Sử dụng mã tự sinh
    $makm = $auto_makm;
    $phantramkm = $_POST["txt_phantramkm"];
    $thongtinkm = $_POST["txt_thongtinkm"];

    insertKhuyenMai($makm, $phantramkm, $thongtinkm);
}

$KhuyenMaiList = getAllKhuyenMai();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý khuyến mãi</title>
    <link href="public/style1.css" rel="stylesheet" />
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f2f2f2; }
    </style>
</head>
<body>
    <form method="post">
        <table>
            <tr>
                <th colspan="4">Quản lý khuyến mãi</th>
            </tr>
            <tr>
                <td align="center" colspan="4">
                    <!-- Hiển thị mã khuyến mãi tự sinh (read-only) -->
                    <input type="text" name="txt_makm" value="<?php echo $auto_makm; ?>" readonly style="width:120px;" />
                    <input type="number" name="txt_phantramkm" placeholder="Nhập % khuyến mãi" min="0" max="100" required style="width:120px;" />
                    <input type="text" name="txt_thongtinkm" placeholder="Nhập thông tin khuyến mãi" required style="width:300px;" />
                    <button type="submit" name="btn_submit" value="Thêm mới">Thêm mới</button>
                </td>
            </tr>
            <tr>
                <th>Mã khuyến mãi</th>
                <th>Phần trăm khuyến mãi</th>
                <th>Thông tin khuyến mãi</th>
                <th>Thao tác</th>
            </tr>
            <?php foreach ($KhuyenMaiList as $v): ?>
            <tr>
                <td><?php echo $v['makm']; ?></td>
                <td><?php echo $v['phantramkm']; ?>%</td>
                <td><?php echo $v['thongtinkm']; ?></td>
                <td>
                    <a href="Manager.php?action=DeleteAdKm&id=<?php echo $v['makm']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa khuyến mãi này?');">Xóa</a> |
                    <a href="Manager.php?action=UpdateAdKm&id=<?php echo $v['makm']; ?>">Cập nhật</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </form>
</body>
</html>
