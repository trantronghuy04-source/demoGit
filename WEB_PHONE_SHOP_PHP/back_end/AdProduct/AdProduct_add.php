<?php
require_once "db_adProduct.php";
require_once "./back_end/AdProductType/db_AdProductType.php";
require_once "./back_end/Adncc/db_AdNccView.php";
require_once "./back_end/km/db_AdKmView.php";

$productType  = getProductType();
$KhuyenMai    = getAllKhuyenMai();
$nccType      = getNccType();

// Tự sinh mã sản phẩm
$txt_masp = isset($_POST["txt_masp"]) ? $_POST["txt_masp"] : generateProductCode();

$txt_maloaisp   = isset($_POST["txt_maloaisp"]) ? $_POST["txt_maloaisp"] : "";
$txt_tensp      = isset($_POST["txt_tensp"]) ? $_POST["txt_tensp"] : "";
$txt_hinhanh    = isset($_FILES["uploadfile"]["name"]) ? $_FILES["uploadfile"]["name"] : "";
$txt_gn         = isset($_POST["txt_gn"]) ? $_POST["txt_gn"] : "";
$txt_giaxuat    = isset($_POST["txt_giaxuat"]) ? $_POST["txt_giaxuat"] : "";
$txt_makm       = isset($_POST["txt_makm"]) ? $_POST["txt_makm"] : "";
$txt_soluong    = isset($_POST["txt_soluong"]) ? $_POST["txt_soluong"] : "";
$txt_motasp     = isset($_POST["txt_motasp"]) ? $_POST["txt_motasp"] : "";
$create_date    = isset($_POST["create_date"]) ? $_POST["create_date"] : "";
$txt_mancc      = isset($_POST["txt_mancc"]) ? $_POST["txt_mancc"] : "";
// Các trường bổ sung
$txt_mausac     = isset($_POST["txt_mausac"]) ? $_POST["txt_mausac"] : "";
$txt_mausac1    = isset($_POST["txt_mausac1"]) ? $_POST["txt_mausac1"] : "";
$txt_mausac2    = isset($_POST["txt_mausac2"]) ? $_POST["txt_mausac2"] : "";
$txt_thongso    = isset($_POST["txt_thongso"]) ? $_POST["txt_thongso"] : "";
$txt_xuatsu     = isset($_POST["txt_xuatsu"]) ? $_POST["txt_xuatsu"] : "";
$txt_featured   = isset($_POST["txt_featured"]) ? $_POST["txt_featured"] : 0;
$txt_hinhanh1   = isset($_FILES["uploadfile1"]["name"]) ? $_FILES["uploadfile1"]["name"] : "";
$txt_hinhanh2   = isset($_FILES["uploadfile2"]["name"]) ? $_FILES["uploadfile2"]["name"] : "";

// Các trường dung lượng
$txt_dungluong  = isset($_POST["txt_dungluong"]) ? $_POST["txt_dungluong"] : "";
$txt_dungluong1 = isset($_POST["txt_dungluong1"]) ? $_POST["txt_dungluong1"] : "";
$txt_dungluong2 = isset($_POST["txt_dungluong2"]) ? $_POST["txt_dungluong2"] : "";
$txt_mausac     = isset($_POST["txt_mausac"]) ? $_POST["txt_mausac"] : "";



if($_SERVER["REQUEST_METHOD"]=="POST"){
    if(isset($_FILES["uploadfile"]) && !empty($txt_hinhanh)){
        move_uploaded_file($_FILES["uploadfile"]["tmp_name"], "./public/" . $txt_hinhanh);
    }
    if(isset($_FILES["uploadfile1"]) && !empty($txt_hinhanh1)){
        move_uploaded_file($_FILES["uploadfile1"]["tmp_name"], "./public/" . $txt_hinhanh1);
    }
    if(isset($_FILES["uploadfile2"]) && !empty($txt_hinhanh2)){
        move_uploaded_file($_FILES["uploadfile2"]["tmp_name"], "./public/" . $txt_hinhanh2);
    }
    insertProduct(
        $txt_maloaisp,
        $txt_masp,
        $txt_tensp,
        $txt_hinhanh,
        $txt_gn,
        $txt_giaxuat,
        $txt_makm,
        $txt_soluong,
        $txt_motasp,
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
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm mới sản phẩm</title>
    <link href="public/style1.css" rel="stylesheet" />
</head>
<body>
    <form method="post" enctype="multipart/form-data">
        <table>
            <tr>
                <td colspan="2"><strong>Thêm mới sản phẩm</strong></td>
            </tr>
            <tr>
                <td>Mã loại SP</td>
                <td>
                    <select name="txt_maloaisp" required>
                        <?php foreach ($productType as $v){ ?>
                            <option value="<?php echo $v[0]; ?>"><?php echo $v[0]; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Mã SP</td>
                <td>
                    <input name="txt_masp" type="text" readonly value="<?php echo $txt_masp; ?>" required />
                </td>
            </tr>
            <tr>
                <td>Tên SP</td>
                <td>
                    <input name="txt_tensp" type="text" required />
                </td>
            </tr>
            <tr>
                <td>Hình ảnh chính</td>
                <td><input name="uploadfile" type="file" /></td>
            </tr>
            <tr>
                <td>Hình ảnh phụ 1</td>
                <td><input name="uploadfile1" type="file" /></td>
            </tr>
            <tr>
                <td>Hình ảnh phụ 2</td>
                <td><input name="uploadfile2" type="file" /></td>
            </tr>
            <tr>
                <td>Giá nhập</td>
                <td><input name="txt_gn" type="number" required /></td>
            </tr>
            <tr>
                <td>Giá xuất</td>
                <td><input name="txt_giaxuat" type="number" required /></td>
            </tr>
            <tr>
                <td>Mã khuyến mãi</td>
                <td>
                    <select name="txt_makm" required>
                        <?php foreach ($KhuyenMai as $v){ ?>
                            <option value="<?php echo $v['makm']; ?>"><?php echo $v['makm']; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Số lượng</td>
                <td><input name="txt_soluong" type="number" required /></td>
            </tr>
            <tr>
                <td>Mô tả sản phẩm</td>
                <td><textarea name="txt_motasp" cols="30" rows="7"></textarea></td>
            </tr>
            <tr>
                <td>Ngày tạo</td>
                <td><input name="create_date" type="date" required /></td>
            </tr>
            <tr>
                <td>Mã NCC</td>
                <td>
                    <select name="txt_mancc" required>
                        <?php foreach ($nccType as $v){ ?>
                            <option value="<?php echo $v['mancc']; ?>"><?php echo $v['mancc']; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <!-- Các trường bổ sung -->
            <tr>
                <td>Màu sắc</td>
                <td><input type="text" name="txt_mausac" placeholder="VD: đỏ, xanh" /></td>
            </tr>
            <tr>
                <td>Màu sắc 1</td>
                <td><input type="text" name="txt_mausac1" placeholder="VD: đỏ, xanh" /></td>
            </tr><tr>
                <td>Màu sắc 2 </td>
                <td><input type="text" name="txt_mausac2" placeholder="VD: đỏ, xanh" /></td>
            </tr>
            <tr>
                <td>Thông số kỹ thuật</td>
                <td><textarea name="txt_thongso" cols="30" rows="5" placeholder="Nhập thông số kỹ thuật"></textarea></td>
            </tr>
            <tr>
                <td>Xuất xứ</td>
                <td><input type="text" name="txt_xuatsu" placeholder="Nhập xuất xứ sản phẩm" /></td>
            </tr>
            <tr>
                <td>Nổi bật</td>
                <td><input type="checkbox" name="txt_featured" value="1" /> Có</td>
            </tr>
            <!-- Các trường dung lượng -->
            <tr>
                <td>Dung lượng</td>
                <td><input type="text" name="txt_dungluong" placeholder="VD: 128GB" /></td>
            </tr>
            <tr>
                <td>Dung lượng phụ 1</td>
                <td><input type="text" name="txt_dungluong1" placeholder="VD: 64GB" /></td>
            </tr>
            <tr>
                <td>Dung lượng phụ 2</td>
                <td><input type="text" name="txt_dungluong2" placeholder="VD: 32GB" /></td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: center;">
                    <input type="submit" name="btn_submit" value="Thêm mới" />
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
