<?php
require_once "db_cart.php";

$cart_id = isset($_GET["id"]) ? $_GET["id"] : '';
if(empty($cart_id)) {
    echo "Cart ID không hợp lệ";
    exit;
}

$cartData = getCartByID($cart_id);
if(!$cartData) {
    echo "Không tìm thấy giỏ hàng";
    exit;
}

$users = getAllUsers();
$products = getAllProducts();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user_id   = $_POST["user_id"];
    $masp      = $_POST["masp"];
    $dungluong = $_POST["dungluong"];
    $mausac    = $_POST["mausac"];
    $so_luong  = $_POST["so_luong"];
    $result = updateCart($cart_id, $user_id, $masp, $dungluong, $mausac, $so_luong);
    if($result === true){
        header("Location: Manager.php?action=Cart");
        exit();
    } else {
        echo $result;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật giỏ hàng</title>
    <style>
      body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
      .form-container { background: #fff; padding: 20px; border-radius: 6px; max-width: 800px; margin: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
      .form-group { margin-bottom: 15px; }
      label { display: block; margin-bottom: 5px; font-weight: bold; }
      input[type="text"], input[type="number"], select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
      .option-buttons button { margin-right: 5px; padding: 8px 12px; border: 1px solid #ccc; background: #f9f9f9; cursor: pointer; border-radius: 4px; }
      .option-buttons button.active { background: #cce5ff; color: #004085; border-color: #b8daff; }
      .product-image { max-width: 150px; margin-bottom: 10px; }
      .total-price { font-size: 18px; font-weight: bold; margin-top: 10px; }
      .submit-btn { padding: 10px 20px; background: #28a745; border: none; color: #fff; font-size: 16px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
<div class="form-container">
  <h2>Cập nhật giỏ hàng</h2>
  <form method="post" id="update-cart-form">
      <div class="form-group">
          <label for="cart_id">Cart ID:</label>
          <input type="text" name="cart_id" id="cart_id" value="<?php echo $cart_id; ?>" readonly>
      </div>
      <div class="form-group">
          <label for="user_id">Chọn người dùng:</label>
          <select name="user_id" id="user_id" required>
              <option value="">-- Chọn người dùng --</option>
              <?php foreach($users as $user): ?>
              <option value="<?php echo $user['user_id']; ?>" <?php echo ($user['user_id'] == $cartData["user_id"]) ? "selected" : ""; ?>>
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
              <option value="<?php echo $prod['masp']; ?>" <?php echo ($prod['masp'] == $cartData["masp"]) ? "selected" : ""; ?>>
                  <?php echo $prod['tensp'] . " (" . $prod['masp'] . ")"; ?>
              </option>
              <?php endforeach; ?>
          </select>
      </div>
      <div id="product-details">
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
              <label for="so_luong">Số lượng mua:</label>
              <input type="number" name="so_luong" id="so_luong" value="<?php echo $cartData['so_luong']; ?>" min="1" required>
          </div>
          <div class="form-group">
              <label>Tổng tiền:</label>
              <span id="tong-tien" class="total-price"></span>
          </div>
      </div>
      <input type="submit" name="btn_submit" value="Cập nhật" class="submit-btn">
  </form>
</div>
<script>
  var productsData = <?php echo json_encode($products); ?>;
  function getProductById(masp) {
      return productsData.find(function(prod) {
          return prod.masp === masp;
      });
  }

  function updateProductDetails(selectedProduct, preCapacity, preColor) {
      if(!selectedProduct) return;
      document.getElementById('product-image').src = "../public/images/" + selectedProduct.hinhanh;
      document.getElementById('sale-price').textContent = (selectedProduct.sale_price ? selectedProduct.sale_price : selectedProduct.giaxuat).toLocaleString('vi-VN') + " đ";
      
      // Set thuộc tính max dựa vào số lượng tồn kho
      document.getElementById('so_luong').max = selectedProduct.soluong;

      // Cập nhật dung lượng
      var capacityOptionsDiv = document.getElementById('capacity-options');
      capacityOptionsDiv.innerHTML = "";
      var capacities = [];
      if(selectedProduct.dungluong) capacities.push(selectedProduct.dungluong);
      if(selectedProduct.dungluong1) capacities.push(selectedProduct.dungluong1);
      if(selectedProduct.dungluong2) capacities.push(selectedProduct.dungluong2);
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
              if(cap === preCapacity) {
                  btn.classList.add('active');
                  document.getElementById('selected-capacity').value = cap;
              } else if(!preCapacity && index === 0) {
                  btn.classList.add('active');
                  document.getElementById('selected-capacity').value = cap;
              }
              capacityOptionsDiv.appendChild(btn);
          });
      } else {
          capacityOptionsDiv.innerHTML = "Không có dung lượng";
          document.getElementById('selected-capacity').value = "";
      }

      // Cập nhật màu sắc
      var colorOptionsDiv = document.getElementById('color-options');
      colorOptionsDiv.innerHTML = "";
      var colors = [];
      if(selectedProduct.mausac) colors.push(selectedProduct.mausac);
      if(selectedProduct.mausac1) colors.push(selectedProduct.mausac1);
      if(selectedProduct.mausac2) colors.push(selectedProduct.mausac2);
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
              if(color === preColor) {
                  btn.classList.add('active');
                  document.getElementById('selected-color').value = color;
              } else if(!preColor && index === 0) {
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
  }

  function updateTotal() {
      var masp = document.getElementById('masp').value;
      var product = getProductById(masp);
      if(!product) return;
      var quantity = parseInt(document.getElementById('so_luong').value) || 1;
      var price = product.sale_price ? product.sale_price : product.giaxuat;
      var total = quantity * price;
      document.getElementById('tong-tien').textContent = total.toLocaleString('vi-VN') + " đ";
  }

  document.getElementById('masp').addEventListener('change', function() {
      var masp = this.value;
      var product = getProductById(masp);
      updateProductDetails(product, "", "");
  });

  window.onload = function() {
      var selectedMasp = document.getElementById('masp').value;
      var product = getProductById(selectedMasp);
      var preCapacity = "<?php echo $cartData['dungluong']; ?>";
      var preColor = "<?php echo $cartData['mausac']; ?>";
      updateProductDetails(product, preCapacity, preColor);
  };

  document.getElementById('so_luong').addEventListener('input', updateTotal);
</script>
</body>
</html>
