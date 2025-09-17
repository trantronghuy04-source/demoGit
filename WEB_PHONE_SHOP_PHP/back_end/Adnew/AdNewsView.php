<?php
require_once "db_AdNews.php";

$txt_tieude = isset($_POST["txt_tieude"]) ? $_POST["txt_tieude"] : "";
$txt_noidung = isset($_POST["txt_noidung"]) ? $_POST["txt_noidung"] : "";
$hinh_anh = isset($_FILES["file_hinhanh"]["name"]) ? $_FILES["file_hinhanh"]["name"] : "";

// Xử lý thêm và xóa tin tức
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["btn_submit"])) {
        switch ($_POST["btn_submit"]) {
            case "Thêm mới":
                if (!empty($hinh_anh)) {
                    $target_dir = "uploads/";

                    // Kiểm tra nếu thư mục chưa tồn tại thì tạo mới
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }

                    $target_file = $target_dir . basename($_FILES["file_hinhanh"]["name"]);

                    if (move_uploaded_file($_FILES["file_hinhanh"]["tmp_name"], $target_file)) {
                        insertNews($txt_tieude, $txt_noidung, basename($_FILES["file_hinhanh"]["name"]));
                    } else {
                        echo "<script>alert('Lỗi khi tải ảnh lên!');</script>";
                    }
                } else {
                    insertNews($txt_tieude, $txt_noidung, "");
                }
                break;

            case "delete":
                if (isset($_GET["id"])) {
                    deleteNews($_GET["id"]);
                }
                break;
        }
    }
}

$newsList = getAllNews();
?>

<form method="post" enctype="multipart/form-data">
    <table border="1">
        <tr>
            <th colspan="5">Quản lý Tin Tức</th>
        </tr>
        <tr>
            <td align="center" colspan="5">
                <input type="text" name="txt_tieude" placeholder="Nhập tiêu đề tin tức" required />
                <input type="text" name="txt_noidung" placeholder="Nhập nội dung tin tức" required />
                <input type="file" name="file_hinhanh" accept="image/*" />
                <input type="submit" name="btn_submit" value="Thêm mới" />
            </td>
        </tr>
        <tr>
            <td>ID</td>
            <td>Tiêu đề</td>
            <td>Nội dung</td>
            <td>Hình ảnh</td>
            <td>Hành động</td>
        </tr>
        <?php foreach ($newsList as $news) { ?>
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
                <a href="Manager.php?action=DeleteAdNews&id=<?php echo $news["id"]; ?>"
                    onclick="return confirm('Bạn có muốn xóa không?');">
                    Xóa
                </a>

            </td>
        </tr>
        <?php } ?>
    </table>
</form>
