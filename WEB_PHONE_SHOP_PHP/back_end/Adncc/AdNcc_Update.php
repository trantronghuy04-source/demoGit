<?php
require_once "db_AdNccView.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $NccType = getNccTypeID($id); // Lấy thông tin hiện tại của nhà cung cấp

    $txt_mancc = $NccType['mancc'];
    $txt_tenncc = isset($_POST["txt_tenncc"]) ? $_POST["txt_tenncc"] : $NccType['tenncc'];
    $txt_thongtinncc = isset($_POST["txt_thongtinncc"]) ? $_POST["txt_thongtinncc"] : $NccType['thongtinncc'];
    // Nếu người dùng không upload ảnh mới thì dùng ảnh cũ
    $hinhanh = isset($_FILES["uploadfile"]["name"]) && !empty($_FILES["uploadfile"]["name"]) 
        ? $_FILES["uploadfile"]["name"] : $NccType['hinhanh'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!empty($_FILES["uploadfile"]["name"])) {
            // Dùng đường dẫn tương đối từ file này (cũng nằm trong back_end/Adncc)
            $target_dir = "public/images/";
            move_uploaded_file($_FILES["uploadfile"]["tmp_name"], $target_dir . $hinhanh);
        }
        UpdateadNccType($txt_mancc, $txt_tenncc, $txt_thongtinncc, $hinhanh);
        header("Location: Manager.php?action=AdNcc");
        exit();
    }
}
?>
<form method="post" enctype="multipart/form-data">
    <table>
        <tr>
            <th colspan="2">Cập nhật nhà cung cấp</th>
        </tr>
        <tr>
            <td>Mã nhà cung cấp</td>
            <td>
                <input type="text" name="txt_mancc" value="<?php echo $txt_mancc; ?>" readonly>
            </td>
        </tr>
        <tr>
            <td>Tên nhà cung cấp</td>
            <td>
                <input type="text" name="txt_tenncc" value="<?php echo $txt_tenncc; ?>" required>
            </td>
        </tr>
        <tr>
            <td>Thông tin nhà cung cấp</td>
            <td>
                <textarea name="txt_thongtinncc" required><?php echo $txt_thongtinncc; ?></textarea>
            </td>
        </tr>
        <tr>
            <td>Hình ảnh</td>
            <td>
                <input type="file" name="uploadfile">
                <!-- Ở đây, vì Manager.php ở thư mục gốc, nên đường dẫn hiển thị ảnh có thể dùng "public/images/..." -->
                <img src="public/images/<?php echo $hinhanh; ?>" alt="Hình ảnh" width="100">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <button type="submit">Cập nhật</button>
            </td>
        </tr>
    </table>
</form>
