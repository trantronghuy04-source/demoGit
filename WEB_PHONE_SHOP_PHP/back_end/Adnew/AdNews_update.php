<?php
require_once "db_AdNews.php";

$id = isset($_GET["id"]) ? $_GET["id"] : null;
$news = $id ? getNewsByID($id) : null;

$txt_tieude = $_POST["txt_tieude"] ?? ($news["tieu_de"] ?? "");
$txt_noidung = $_POST["txt_noidung"] ?? ($news["noi_dung"] ?? "");
$hinh_anh_cu = $news["hinh_anh"] ?? "";
$hinh_anh_moi = $_FILES["file_hinhanh"]["name"] ?? "";

// 🛠 XỬ LÝ CẬP NHẬT TIN TỨC
if ($_SERVER["REQUEST_METHOD"] == "POST" && $id) {
    $hinh_anh_moi_path = $hinh_anh_cu; // Mặc định giữ ảnh cũ

    // Nếu có hình ảnh mới, thực hiện upload
    if (!empty($hinh_anh_moi)) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($_FILES["file_hinhanh"]["name"]);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Kiểm tra định dạng file hợp lệ
        $allowed_types = ["jpg", "jpeg", "png", "gif"];
        if (!in_array($file_type, $allowed_types)) {
            echo "<script>alert('Chỉ chấp nhận file JPG, JPEG, PNG, GIF!');</script>";
        } else {
            if (move_uploaded_file($_FILES["file_hinhanh"]["tmp_name"], $target_file)) {
                // Xóa ảnh cũ nếu có
                if (!empty($hinh_anh_cu)) {
                    $file_path = "uploads/" . $hinh_anh_cu;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
                $hinh_anh_moi_path = basename($_FILES["file_hinhanh"]["name"]);
            }
        }
    }

    updateNews($id, $txt_tieude, $txt_noidung, $hinh_anh_moi_path);
    echo "<script>alert('Cập nhật thành công!'); window.location='Manager.php?action=AdNews';</script>";
    exit();
}

// 🛠 XỬ LÝ XÓA TIN TỨC
if (isset($_GET["delete_id"])) {
    deleteNews($_GET["delete_id"]);
    echo "<script>alert('Xóa thành công!'); window.location='Manager.php?action=AdNews';</script>";
    exit();
}
?>

<!-- FORM CẬP NHẬT -->
<?php if ($id) { ?>
<form method="post" enctype="multipart/form-data">
    <table width="700">
        <tr><th colspan="2">Cập nhật tin tức</th></tr>
        <tr>
            <td>Tiêu đề</td>
            <td><input type="text" name="txt_tieude" value="<?php echo htmlspecialchars($txt_tieude); ?>" required /></td>
        </tr>
        <tr>
            <td>Nội dung</td>
            <td><textarea name="txt_noidung" required><?php echo htmlspecialchars($txt_noidung); ?></textarea></td>
        </tr>
        <tr>
            <td>Hình ảnh hiện tại</td>
            <td>
                <?php if (!empty($hinh_anh_cu)) { ?>
                    <img src="uploads/<?php echo htmlspecialchars($hinh_anh_cu); ?>" width="150" />
                <?php } ?>
            </td>
        </tr>
        <tr>
            <td>Chọn hình ảnh mới</td>
            <td><input type="file" name="file_hinhanh" accept="image/*" /></td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" value="Cập nhật" /></td>
        </tr>
    </table>
</form>
<?php } ?>

<!-- DANH SÁCH TIN TỨC -->
<table border="1">
    <tr><th colspan="5">Quản lý Tin Tức</th></tr>
    <tr>
        <td align="center" colspan="5">
            <form method="post" enctype="multipart/form-data">
                <input type="text" name="txt_tieude" placeholder="Nhập tiêu đề tin tức" required />
                <input type="text" name="txt_noidung" placeholder="Nhập nội dung tin tức" required />
                <input type="file" name="file_hinhanh" accept="image/*" />
                <input type="submit" name="btn_submit" value="Thêm mới" />
            </form>
        </td>
    </tr>
    <tr>
        <td>ID</td>
        <td>Tiêu đề</td>
        <td>Nội dung</td>
        <td>Hình ảnh</td>
        <td>Hành động</td>
    </tr>
    <?php foreach (getAllNews() as $news) { ?>
    <tr>
        <td><?php echo $news["id"] ?></td>
        <td><?php echo htmlspecialchars($news["tieu_de"]) ?></td>
        <td><?php echo htmlspecialchars($news["noi_dung"]) ?></td>
        <td>
            <?php if (!empty($news["hinh_anh"])) { ?>
                <img src="uploads/<?php echo htmlspecialchars($news["hinh_anh"]) ?>" width="100">
            <?php } ?>
        </td>
        <td>
            <a href="Manager.php?action=UpdateAdNews&id=<?php echo $news["id"]; ?>">Cập nhật</a> |
            <a href="Manager.php?action=AdNews&delete_id=<?php echo $news["id"]; ?>"
               onclick="return confirm('Bạn có muốn xóa không?');">
                Xóa
            </a>
        </td>
    </tr>
    <?php } ?>
</table>
