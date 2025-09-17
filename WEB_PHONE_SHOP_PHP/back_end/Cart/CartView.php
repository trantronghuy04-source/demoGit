<?php
require_once "db_cart.php";
$users = getAllUsers();
$products = getAllProducts();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btn_submit"]) && $_POST["btn_submit"] == "Thêm mới") {
    $user_id  = $_POST["user_id"];
    $masp     = $_POST["masp"];
    $so_luong = $_POST["so_luong"];
    $dungluong = isset($_POST["dungluong"]) ? $_POST["dungluong"] : null;
    $mausac = isset($_POST["mausac"]) ? $_POST["mausac"] : null;
    $result = insertCart($user_id, $masp, $so_luong, $dungluong, $mausac);
    if($result === true){
        header("Location: Manager.php?action=Cart");
        exit();
    } else {
        echo $result;
    }
}

$carts = getCart();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý giỏ hàng</title>
    <style>
      body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
      .form-container { background: #fff; padding: 20px; border-radius: 6px; max-width: 900px; margin: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); margin-bottom: 30px; }
      .form-group { margin-bottom: 15px; }
      label { display: block; margin-bottom: 5px; font-weight: bold; }
      input[type="text"], input[type="number"], select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
      .option-buttons button {
          margin-right: 5px;
          padding: 8px 12px;
          border: 1px solid #ddd;
          background: #f0f0f0;
          cursor: pointer;
          border-radius: 4px;
          transition: background-color 0.3s, color 0.3s;
      }
      .option-buttons button:hover {
          background: #e0e0e0;
      }
      .option-buttons button.active {
          background: #FF6B6B;
          color: #fff;
          border-color: #FF6B6B;
      }
      .product-image { max-width: 150px; margin-bottom: 10px; }
      .total-price { font-size: 18px; font-weight: bold; margin-top: 10px; }
      .submit-btn { padding: 10px 20px; background: #28a745; border: none; color: #fff; font-size: 16px; border-radius: 4px; cursor: pointer; }
      table { width: 100%; border-collapse: collapse; margin-top: 20px; }
      table, th, td { border: 1px solid #ccc; }
      th, td { padding: 10px; text-align: center; }
    </style>
</head>
<body>
<div class="form-container">
  <h2>Quản lý giỏ hàng - Thêm mới</h2>
  <form method="post" id="cart-form">
      <div class="form-group">
          <label for="user_id">Chọn người dùng:</label>
          <select name="user_id" id="user_id" required>
              <option value="">-- Chọn người dùng --</option>
              <?php foreach ($users as $user): ?>
                  <option value="<?php echo $user['user_id']; ?>">
                      <?php echo $user['username'] . " (" . $user['user_id'] . ")"; ?>
                  </option>
              <?php endforeach; ?>
          </select>
      </div>
      <div class="form-group">
          <label for="masp">Chọn sản phẩm:</label>
          <select name="masp" id="masp" required>
              <option value="">-- Chọn sản phẩm --</option>
              <?php foreach($products as $prod): ?>
              <option value="<?php echo $prod['masp']; ?>">
                  <?php echo $prod['tensp'] . " (" . $prod['masp'] . ")"; ?>
              </option>
              <?php endforeach; ?>
          </select>
      </div>
      <div id="product-details" style="display:none;">
          <div class="form-group">
              <label>Hình ảnh:</label>
              <img id="product-image" class="product-image" src="" alt="Hình sản phẩm">
          </div>
          <div class="form-group">
              <label>Chọn dung lượng:</label>
              <div id="capacity-options" class="option-buttons"></div>
              <input type="hidden" name="dungluong" id="selected-capacity">
          </div>
          <div class="form-group">
              <label>Chọn màu sắc:</label>
              <div id="color-options" class="option-buttons"></div>
              <input type="hidden" name="mausac" id="selected-color">
          </div>
          <div class="form-group">
              <label>Giá sau khuyến mãi:</label>
              <span id="sale-price"></span>
          </div>
          <div class="form-group">
              <label for="so_luong">Số lượng:</label>
              <input type="number" name="so_luong" id="so_luong" value="1" min="1" required>
          </div>
          <div class="form-group">
              <label>Tổng tiền:</label>
              <span id="tong-tien" class="total-price"></span>
          </div>
      </div>
      <input type="submit" name="btn_submit" value="Thêm mới" class="submit-btn">
  </form>
</div>

<!-- Danh sách giỏ hàng -->
<div class="form-container">
  <h2>Danh sách giỏ hàng</h2>
  <table>
    <tr>
      <th>Cart ID</th>
      <th>User ID</th>
      <th>Mã sản phẩm</th>
      <th>Tên sản phẩm</th>
      <th>Hình ảnh</th>
      <th>Dung lượng</th>
      <th>Màu sắc</th>
      <th>Số lượng</th>
      <th>Giá sau KM</th>
      <th>Tổng tiền</th>
      <th>Hành động</th>
    </tr>
    <?php foreach ($carts as $cart): ?>
    <tr>
      <td><?php echo $cart["cart_id"]; ?></td>
      <td><?php echo $cart["user_id"]; ?></td>
      <td><?php echo $cart["masp"]; ?></td>
      <td><?php echo $cart["tensp"]; ?></td>
      <td><img src="../public/images/<?php echo $cart["hinhanh"]; ?>" width="50" /></td>
      <td><?php echo $cart["dungluong"]; ?></td>
      <td><?php echo $cart["mausac"]; ?></td>
      <td><?php echo $cart["so_luong"]; ?></td>
      <td><?php echo number_format($cart["gia_sau_km"], 0, ',', '.'); ?> đ</td>
      <td><?php echo number_format($cart["tong_tien"], 0, ',', '.'); ?> đ</td>
      <td>
          <a href="Manager.php?action=UpdateCart&id=<?php echo $cart["cart_id"]; ?>">Cập nhật</a> | 
          <a href="Manager.php?action=DeleteCart&id=<?php echo $cart["cart_id"]; ?>" onclick="return confirm('Bạn có muốn xóa không?');">Xóa</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </table>
</div>

<script>
  var productsData = <?php echo json_encode($products); ?>;
  function getProductById(masp) {
      return productsData.find(function(prod) {
          return prod.masp === masp;
      });
  }

  document.getElementById('masp').addEventListener('change', function() {
      var masp = this.value;
      if(masp === "") {
          document.getElementById('product-details').style.display = "none";
          return;
      }
      var product = getProductById(masp);
      if(!product) return;

      document.getElementById('product-details').style.display = "block";
      document.getElementById('product-image').src = "../public/images/" + product.hinhanh;
      document.getElementById('sale-price').textContent = (product.sale_price ? product.sale_price : product.giaxuat).toLocaleString('vi-VN') + " đ";
      
      // Set thuộc tính max dựa vào số lượng tồn kho
      document.getElementById('so_luong').max = product.soluong;

      // Cập nhật lựa chọn dung lượng
      var capacityOptionsDiv = document.getElementById('capacity-options');
      capacityOptionsDiv.innerHTML = "";
      var capacities = [];
      if(product.dungluong) capacities.push(product.dungluong);
      if(product.dungluong1) capacities.push(product.dungluong1);
      if(product.dungluong2) capacities.push(product.dungluong2);
      if(capacities.length > 0) {
          capacities.forEach(function(cap, index) {
              var btn = document.createElement('button');
              btn.type = "button";
              btn.textContent = cap;
              btn.addEventListener('click', function() {
                  var btns = capacityOptionsDiv.querySelectorAll('button');
                  btns.forEach(function(b) { b.classList.remove('active'); });
                  btn.classList.add('active');
                  document.getElementById('selected-capacity').value = cap;
                  updateTotal();
              });
              if(index === 0) {
                  btn.classList.add('active');
                  document.getElementById('selected-capacity').value = cap;
              }
              capacityOptionsDiv.appendChild(btn);
          });
      } else {
          capacityOptionsDiv.innerHTML = "Không có dung lượng";
          document.getElementById('selected-capacity').value = "";
      }

      // Cập nhật lựa chọn màu sắc
      var colorOptionsDiv = document.getElementById('color-options');
      colorOptionsDiv.innerHTML = "";
      var colors = [];
      if(product.mausac) colors.push(product.mausac);
      if(product.mausac1) colors.push(product.mausac1);
      if(product.mausac2) colors.push(product.mausac2);
      if(colors.length > 0) {
          colors.forEach(function(color, index) {
              var btn = document.createElement('button');
              btn.type = "button";
              btn.textContent = color;
              btn.addEventListener('click', function() {
                  var btns = colorOptionsDiv.querySelectorAll('button');
                  btns.forEach(function(b) { b.classList.remove('active'); });
                  btn.classList.add('active');
                  document.getElementById('selected-color').value = color;
                  updateTotal();
              });
              if(index === 0) {
                  btn.classList.add('active');
                  document.getElementById('selected-color').value = color;
              }
              colorOptionsDiv.appendChild(btn);
          });
      } else {
          colorOptionsDiv.innerHTML = "Không có màu sắc";
          document.getElementById('selected-color').value = "";
      }
      updateTotal();
  });

  document.getElementById('so_luong').addEventListener('input', updateTotal);
  function updateTotal() {
      var masp = document.getElementById('masp').value;
      var product = getProductById(masp);
      if(!product) return;
      var quantity = parseInt(document.getElementById('so_luong').value) || 1;
      var price = product.sale_price ? product.sale_price : product.giaxuat;
      var total = quantity * price;
      document.getElementById('tong-tien').textContent = total.toLocaleString('vi-VN') + " đ";
  }
</script>
</body>
</html>
