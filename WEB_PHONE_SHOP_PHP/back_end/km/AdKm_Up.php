<?php
require_once "db_AdKmView.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $KhuyenMai = getKhuyenMaiByID($id); // Lấy thông tin khuyến mãi hiện tại

    // Lấy giá trị mặc định cho form
    $txt_makm = $KhuyenMai['makm'];
    $txt_phantramkm = isset($_POST["txt_phantramkm"]) ? $_POST["txt_phantramkm"] : $KhuyenMai['phantramkm'];
    $txt_thongtinkm = isset($_POST["txt_thongtinkm"]) ? $_POST["txt_thongtinkm"] : $KhuyenMai['thongtinkm'];

    // Kiểm tra khi form được submit
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        updateKhuyenMai($txt_makm, $txt_phantramkm, $txt_thongtinkm);
        header("Location: Manager.php?action=AdKm"); // Sửa lại đường dẫn chuyển hướng
        exit();
    }
    
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập Nhật Khuyến Mãi</title>
</head>
<body>
    <form method="post">
        <table>
            <tr>
                <th colspan="2">Cập Nhật Khuyến Mãi</th>
            </tr>
            <tr>
                <td>Mã Khuyến Mãi</td>
                <td>
                    <input type="text" name="txt_makm" value="<?php echo $txt_makm; ?>" readonly>
                </td>
            </tr>
            <tr>
                <td>Phần Trăm KM (%)</td>
                <td>
                    <input type="number" name="txt_phantramkm" value="<?php echo $txt_phantramkm; ?>" min="0" max="100" required>
                </td>
            </tr>
            <tr>
                <td>Thông Tin Khuyến Mãi</td>
                <td>
                    <textarea name="txt_thongtinkm"><?php echo $txt_thongtinkm; ?></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <button type="submit">Cập nhật</button>
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
