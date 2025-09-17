<?php
require_once "db_AdNccView.php";

// Xử lý thêm mới nếu form được submit với nút "Thêm mới"
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btn_submit"]) && $_POST["btn_submit"] == "Thêm mới") {
    // Lấy mã tự sinh từ hàm (không lấy từ input vì input được readonly)
    $mancc = generateNccCode();
    $tenncc = $_POST["txt_tenncc"];
    $thongtinncc = $_POST["txt_thongtinncc"];
    $hinhanh = $_FILES["uploadfile"]["name"];

    // Lưu file ảnh vào thư mục public/images/
    // Sử dụng đường dẫn tương đối (với file này nằm ở back_end/Adncc/)
    $target_dir = "public/images/";
    $target_file = $target_dir . basename($hinhanh);
    if (move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $target_file)) {
        insertNccType($mancc, $tenncc, $thongtinncc, $hinhanh);
    } else {
        echo "Lỗi khi tải tệp.";
    }
}

// Lấy danh sách nhà cung cấp để hiển thị
$NccType = getNccType();
?>
<form method="post" enctype="multipart/form-data">
    <table border="1">
        <tr>
            <th colspan="5">Quản lý nhà cung cấp</th>
        </tr>
        <tr>
            <td align="center" colspan="5">
                <!-- Mã nhà cung cấp được tự sinh và readonly -->
                <input type="text" name="txt_mancc" placeholder="Mã nhà cung cấp" readonly 
                       value="<?php echo generateNccCode(); ?>" />
                <input type="text" name="txt_tenncc" placeholder="Nhập tên nhà cung cấp" required />
                <input type="text" name="txt_thongtinncc" placeholder="Nhập thông tin nhà cung cấp" required />
                <input type="file" name="uploadfile" required />
                <button type="submit" name="btn_submit" value="Thêm mới">Thêm mới</button>
            </td>
        </tr>
        <tr>
            <td>Mã nhà cung cấp</td>
            <td>Tên nhà cung cấp</td>
            <td>Thông tin nhà cung cấp</td>
            <td>Hình ảnh</td>
            <td>Tùy chọn</td>
        </tr>
        <?php foreach ($NccType as $v): ?>
        <tr>
            <td><?php echo $v['mancc']; ?></td>
            <td><?php echo $v['tenncc']; ?></td>
            <td><?php echo $v['thongtinncc']; ?></td>
            <!-- Ở đây, vì URL trình duyệt đang ở Manager.php (thư mục gốc), nên đường dẫn hiển thị ảnh là "public/images/..." -->
            <td><img src="public/images/<?php echo $v['hinhanh']; ?>" alt="Hình ảnh" width="50"></td>
            <td>
                <a href="Manager.php?action=DeleteAdNcc&id=<?php echo $v['mancc']; ?>" 
                   class="btn btn-delete" 
                   onclick="return confirm('Bạn có chắc chắn muốn xóa nhà cung cấp này?');">Xóa</a>
                <a href="Manager.php?action=UpdateAdNcc&id=<?php echo $v['mancc']; ?>" 
                   class="btn btn-update">Cập nhật</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <!-- (Nếu muốn bao bọc bảng trong div có thanh cuộn, có thể thêm div ở đây) -->
    <div style="max-height: 400px; overflow-y: auto;"></div>
</form>
