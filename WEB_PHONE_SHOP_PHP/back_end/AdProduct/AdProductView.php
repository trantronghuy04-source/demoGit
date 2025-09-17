<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Quản lý sản phẩm</title>
  <link href="public/style1.css" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      padding: 0;
      overflow-y: auto;
      scroll-behavior: smooth;
    }
    .table-container {
      width: 100%;
      max-height: 80vh;
      overflow-y: auto;
      overflow-x: auto;
      margin: 10px 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 8px;
      border: 1px solid #ddd;
      text-align: left;
      vertical-align: top;
    }
    th {
      background-color: #f2f2f2;
      font-weight: bold;
      white-space: nowrap;
    }
    .scroll-cell {
      max-height: 120px;
      overflow-y: auto;
      scroll-behavior: smooth;
      white-space: normal;
      word-wrap: break-word;
      padding: 5px;
    }
  </style>
</head>
<body>
  <?php 
    require_once "./back_end/AdProductType/db_AdProductType.php";
    require_once "db_adProduct.php";
    require_once "./back_end/km/db_AdKmView.php";
    require_once "./back_end/Adncc/db_AdNccView.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btn_submit"]) && $_POST["btn_submit"] == "delete") {
          $id = $_GET["id"];
          deleteProduct($id);
    }
    $ProductTypeID = getProduct();  
  ?>

  <form method="post">
    <div class="table-container">
      <table>
        <tr>
          <th colspan="25">Quản lý sản phẩm</th>
        </tr>
        <tr>
          <td colspan="25">
            <a href="Manager.php?action=AdProduct_add">Thêm mới</a>
          </td>
        </tr>
        <tr>
          <th>Mã loại SP</th>
          <th>Mã SP</th>
          <th>Tên SP</th>
          <th>Hình ảnh chính</th>
          <th>Giá nhập</th>
          <th>Giá xuất</th>
          <th>Mã khuyến mãi</th>
          <th>Số lượng</th>
          <th>Mô tả sản phẩm</th>
          <th>Ngày tạo</th>
          <th>Mã NCC</th>
          <th>Màu sắc</th>
          <th>Màu sắc 1</th>
          <th>Màu sắc 2</th>
          <th>Thông số kỹ thuật</th>
          <th>Xuất xứ</th>
          <th>Nổi bật</th>
          <th>Hình ảnh phụ 1</th>
          <th>Hình ảnh phụ 2</th>
          <th>Dung lượng</th>
          <th>Dung lượng phụ 1</th>
          <th>Dung lượng phụ 2</th>
          <th>Xóa</th>
          <th>Update</th>
        </tr>
        <?php foreach ($ProductTypeID as $v) { ?>
        <tr>
          <td><?php echo $v[0]; ?></td>
          <td><?php echo $v[1]; ?></td>
          <td><?php echo $v[2]; ?></td>
          <td><img src="./public/<?php echo $v[3]; ?>" width="100"></td>
          <td><?php echo $v[4]; ?></td>
          <td><?php echo $v[5]; ?></td>
          <td><?php echo $v[6]; ?></td>
          <td><?php echo $v[7]; ?></td>
          <td><div class="scroll-cell"><?php echo $v[8]; ?></div></td>
          <td><?php echo $v[9]; ?></td>
          <td><?php echo $v[10]; ?></td>
          <td><?php echo $v[11]; ?></td>
          <td><?php echo $v[12]; ?></td>
          <td><?php echo $v[13]; ?></td>
          <td><div class="scroll-cell"><?php echo $v[14]; ?></div></td>
          <td><?php echo $v[15]; ?></td>
          <td><?php echo ($v[16] == 1) ? 'Có' : 'Không'; ?></td>
          <td><img src="./public/<?php echo $v[17]; ?>" width="100"></td>
          <td><img src="./public/<?php echo $v[18]; ?>" width="100"></td>
          <td><?php echo $v[19]; ?></td>
          <td><?php echo $v[20]; ?></td>
          <td><?php echo $v[21]; ?></td>
          <td>
            <button type="submit" name="btn_submit" value="delete"
                    formaction="Manager.php?action=AdProduct&id=<?php echo $v[1]; ?>"
                    onclick="return confirm('Bạn có chắc chắn xóa không?')">
              Xóa
            </button>
          </td>
          <td>
            <a href="Manager.php?action=UpdateAddProduct1&id=<?php echo $v[1]; ?>">Update</a>
          </td>
        </tr>
        <?php } ?>
      </table>
    </div>
  </form>
</body>
</html>
