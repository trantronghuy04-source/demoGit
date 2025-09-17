<?php
require_once "./back_end/AdProduct/db_adProduct.php";
require_once "./back_end/AdProductType/db_AdProductType.php";
require_once "./back_end/Adncc/db_AdNccView.php";
require_once "./back_end/km/db_AdKmView.php";

$productType = getProductType();
$KhuyenMai   = getAllKhuyenMai();
$nccType     = getNccType();

$id = $_GET["id"];
$product = getProductID1($id); // Lấy thông tin sản phẩm

// Ưu tiên dữ liệu POST nếu form đã được gửi, ngược lại dùng dữ liệu từ DB
$txt_maloaisp = isset($_POST["txt_maloaisp"]) ? $_POST["txt_maloaisp"] : $product["ma_loaisp"];
$txt_masp     = isset($_POST["txt_masp"]) ? $_POST["txt_masp"] : $product["masp"];
$txt_tensp    = isset($_POST["txt_tensp"]) ? $_POST["txt_tensp"] : $product["tensp"];
$txt_hinhanh  = (isset($_FILES["uploadfile"]["name"]) && !empty($_FILES["uploadfile"]["name"])) ? $_FILES["uploadfile"]["name"] : $product["hinhanh"];
$txt_gn       = isset($_POST["txt_gn"]) ? $_POST["txt_gn"] : $product["gianhap"];
$txt_giaxuat  = isset($_POST["txt_giaxuat"]) ? $_POST["txt_giaxuat"] : $product["giaxuat"];
$txt_makm     = isset($_POST["txt_makm"]) ? $_POST["txt_makm"] : $product["makm"];
$txt_soluong  = isset($_POST["txt_soluong"]) ? $_POST["txt_soluong"] : $product["soluong"];
$txt_mota     = isset($_POST["txt_motasp"]) ? $_POST["txt_motasp"] : $product["mota_sp"];
$create_date  = isset($_POST["create_date"]) ? $_POST["create_date"] : $product["create_date"];
$txt_mancc    = isset($_POST["txt_mancc"]) ? $_POST["txt_mancc"] : $product["mancc"];

// Các trường bổ sung
$txt_mausac   = isset($_POST["txt_mausac"]) ? $_POST["txt_mausac"] : $product["mausac"];
$txt_mausac1   = isset($_POST["txt_mausac1"]) ? $_POST["txt_mausac1"] : $product["mausac1"];
$txt_mausac2   = isset($_POST["txt_mausac2"]) ? $_POST["txt_mausac2"] : $product["mausac2"];
$txt_thongso  = isset($_POST["txt_thongso"]) ? $_POST["txt_thongso"] : $product["thong_so_ky_thuat"];
$txt_xuatsu   = isset($_POST["txt_xuatsu"]) ? $_POST["txt_xuatsu"] : $product["xuat_su"];
$txt_featured = isset($_POST["txt_featured"]) ? 1 : 0; // Sửa lại ở đây
$txt_hinhanh1 = (isset($_FILES["uploadfile1"]["name"]) && !empty($_FILES["uploadfile1"]["name"])) ? $_FILES["uploadfile1"]["name"] : $product["hinhanh1"];
$txt_hinhanh2 = (isset($_FILES["uploadfile2"]["name"]) && !empty($_FILES["uploadfile2"]["name"])) ? $_FILES["uploadfile2"]["name"] : $product["hinhanh2"];

// Các trường dung lượng
$txt_dungluong  = isset($_POST["txt_dungluong"]) ? $_POST["txt_dungluong"] : $product["dungluong"];
$txt_dungluong1 = isset($_POST["txt_dungluong1"]) ? $_POST["txt_dungluong1"] : $product["dungluong1"];
$txt_dungluong2 = isset($_POST["txt_dungluong2"]) ? $_POST["txt_dungluong2"] : $product["dungluong2"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES["uploadfile"]["name"]) && !empty($_FILES["uploadfile"]["name"])) {
        move_uploaded_file($_FILES["uploadfile"]["tmp_name"], "./public/" . $txt_hinhanh);
    }
    if (isset($_FILES["uploadfile1"]["name"]) && !empty($_FILES["uploadfile1"]["name"])) {
        move_uploaded_file($_FILES["uploadfile1"]["tmp_name"], "./public/" . $txt_hinhanh1);
    }
    if (isset($_FILES["uploadfile2"]["name"]) && !empty($_FILES["uploadfile2"]["name"])) {
        move_uploaded_file($_FILES["uploadfile2"]["tmp_name"], "./public/" . $txt_hinhanh2);
    }
    updateProduct(
        $txt_maloaisp,
        $txt_masp,
        $txt_tensp,
        $txt_hinhanh,
        $txt_gn,
        $txt_giaxuat,
        $txt_makm,
        $txt_soluong,
        $txt_mota,
        $create_date,
        $txt_mancc,
        $txt_mausac,
        $txt_mausac1,
        $txt_mausac2,
        $txt_thongso,
        $txt_xuatsu,
        $txt_featured,
        $txt_hinhanh1,
        $txt_hinhanh2,
        $txt_dungluong,
        $txt_dungluong1,
        $txt_dungluong2
    );
    header("location:Manager.php?action=AdProduct");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật sản phẩm</title>
    <link href="public/style1.css" rel="stylesheet" />
</head>
<body>
    <form method="post" enctype="multipart/form-data">
        <table>
            <tr>
                <td colspan="2"><strong>Cập nhật sản phẩm</strong></td>
            </tr>
            <tr>
                <td>Mã loại SP</td>
                <td>
                    <select name="txt_maloaisp" required>
                        <?php foreach ($productType as $v){ ?>
                            <option value="<?php echo $v[0]; ?>" <?php echo $v[0] == $txt_maloaisp ? 'selected' : ''; ?>>
                                <?php echo $v[0]; ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Mã SP</td>
                <td><input name="txt_masp" type="text" readonly value="<?php echo $txt_masp; ?>" required /></td>
            </tr>
            <tr>
                <td>Tên SP</td>
                <td><input name="txt_tensp" type="text" required value="<?php echo $txt_tensp; ?>" /></td>
            </tr>
            <tr>
                <td>Hình ảnh chính</td>
                <td>
                    <input name="uploadfile" type="file" />
                    <?php if (!empty($txt_hinhanh)) { ?>
                        <img src="./public/<?php echo $txt_hinhanh; ?>" width="100" />
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td>Hình ảnh phụ 1</td>
                <td>
                    <input name="uploadfile1" type="file" />
                    <?php if (!empty($txt_hinhanh1)) { ?>
                        <img src="./public/<?php echo $txt_hinhanh1; ?>" width="100" />
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td>Hình ảnh phụ 2</td>
                <td>
                    <input name="uploadfile2" type="file" />
                    <?php if (!empty($txt_hinhanh2)) { ?>
                        <img src="./public/<?php echo $txt_hinhanh2; ?>" width="100" />
                    <?php } ?>
                </td>
            </tr>
            <tr>
                <td>Giá nhập</td>
                <td><input name="txt_gn" type="number" required value="<?php echo $txt_gn; ?>" /></td>
            </tr>
            <tr>
                <td>Giá xuất</td>
                <td><input name="txt_giaxuat" type="number" required value="<?php echo $txt_giaxuat; ?>" /></td>
            </tr>
            <tr>
                <td>Mã khuyến mãi</td>
                <td>
                    <select name="txt_makm" required>
                        <?php foreach ($KhuyenMai as $v){ ?>
                            <option value="<?php echo $v['makm']; ?>" <?php echo $v['makm'] == $txt_makm ? 'selected' : ''; ?>>
                                <?php echo $v['makm']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Số lượng</td>
                <td><input name="txt_soluong" type="number" required value="<?php echo $txt_soluong; ?>" /></td>
            </tr>
            <tr>
                <td>Mô tả sản phẩm</td>
                <td><textarea name="txt_motasp" cols="30" rows="7"><?php echo $txt_mota; ?></textarea></td>
            </tr>
            <tr>
                <td>Ngày tạo</td>
                <td><input name="create_date" type="date" required value="<?php echo $create_date; ?>" /></td>
            </tr>
            <tr>
                <td>Mã NCC</td>
                <td>
                    <select name="txt_mancc" required>
                        <?php foreach ($nccType as $v){ ?>
                            <option value="<?php echo $v['mancc']; ?>" <?php echo $v['mancc'] == $txt_mancc ? 'selected' : ''; ?>>
                                <?php echo $v['mancc']; ?>
                            </option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <!-- Các trường bổ sung -->
            <tr>
                <td>Màu sắc</td>
                <td><input type="text" name="txt_mausac" placeholder="VD: đỏ, xanh" value="<?php echo $txt_mausac; ?>" /></td>
            </tr>
            <tr>
                <td>Màu sắc 1</td>
                <td><input type="text" name="txt_mausac1" placeholder="VD: đỏ, xanh" value="<?php echo $txt_mausac1; ?>" /></td>
            </tr><tr>
                <td>Màu sắc 2</td>
                <td><input type="text" name="txt_mausac2" placeholder="VD: đỏ, xanh" value="<?php echo $txt_mausac2; ?>" /></td>
            </tr>
            <tr>
                <td>Thông số kỹ thuật</td>
                <td><textarea name="txt_thongso" cols="30" rows="5" placeholder="Nhập thông số kỹ thuật"><?php echo $txt_thongso; ?></textarea></td>
            </tr>
            <tr>
                <td>Xuất xứ</td>
                <td><input type="text" name="txt_xuatsu" placeholder="Nhập xuất xứ sản phẩm" value="<?php echo $txt_xuatsu; ?>" /></td>
            </tr>
            <tr>
                <td>Nổi bật</td>
                <td><input type="checkbox" name="txt_featured" value="1" <?php echo $txt_featured == 1 ? 'checked' : ''; ?> /> Có</td>
            </tr>
            <!-- Các trường dung lượng -->
            <tr>
                <td>Dung lượng</td>
                <td><input type="text" name="txt_dungluong" placeholder="VD: 128GB" value="<?php echo $txt_dungluong; ?>" /></td>
            </tr>
            <tr>
                <td>Dung lượng phụ 1</td>
                <td><input type="text" name="txt_dungluong1" placeholder="VD: 64GB" value="<?php echo $txt_dungluong1; ?>" /></td>
            </tr>
            <tr>
                <td>Dung lượng phụ 2</td>
                <td><input type="text" name="txt_dungluong2" placeholder="VD: 32GB" value="<?php echo $txt_dungluong2; ?>" /></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <input type="submit" name="btn_submit" value="Cập nhật" />
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
