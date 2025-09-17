<?php
require_once "db_AdProductType.php";

// Nếu có xử lý thêm mới (khi form submit thêm mới)
if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btn_submit"]) && $_POST["btn_submit"] == "Thêm mới"){
    // Tự sinh mã nếu input mã rỗng (input readonly luôn được gán sẵn)
    $ma_loaisp = trim($_POST["txt_maloaisp"]);
    if(empty($ma_loaisp)){
        $ma_loaisp = generateProductTypeID();
    }
    $ten_loaisp = $_POST["txt_tenloaisp"];
    $mota_loaisp = $_POST["txt_motaloaisp"];
    insertAdProductType($ma_loaisp, $ten_loaisp, $mota_loaisp);
}

// Lấy danh sách loại sản phẩm
$productType = getProductType();
?>
<form method="post">
    <table border="1">
        <tr>
            <th colspan="5">Quản lý danh mục loại sản phẩm</th>
        </tr>
        <tr>
            <td align="center" colspan="5">
                <!-- Input mã tự sinh và readonly -->
                <input type="text" name="txt_maloaisp" placeholder="Mã loại sản phẩm" readonly 
                       value="<?php echo generateProductTypeID(); ?>" />
                <input type="text" name="txt_tenloaisp" placeholder="Nhập tên loại sản phẩm" required />
                <input type="text" name="txt_motaloaisp" placeholder="Nhập mô tả loại sản phẩm" required />
                <input type="submit" name="btn_submit" value="Thêm mới" />
            </td>
        </tr>
        <tr>
            <td>Mã loại sản phẩm</td>
            <td>Tên sản phẩm</td>
            <td>Mô tả loại sản phẩm</td>
            <td>Cập nhật</td>
            <td>Xóa</td>
        </tr>
        <?php foreach($productType as $v): ?>
        <tr>
            <td><?php echo $v["ma_loaisp"]; ?></td>
            <td><?php echo $v["ten_loaisp"]; ?></td>
            <td><?php echo $v["mota_loaisp"]; ?></td>
            <td>
                <a href="Manager.php?action=UpdateAdProductType&id=<?php echo $v["ma_loaisp"]; ?>">
                    Cập nhật
                </a>
            </td>
            <td>
                <a href="Manager.php?action=DeleteAdProductType&id=<?php echo $v["ma_loaisp"]; ?>" 
                   onclick="return confirm('Bạn có muốn xóa không?');">
                    Xóa
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</form>
