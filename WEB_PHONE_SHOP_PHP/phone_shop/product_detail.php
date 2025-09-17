<?php 
session_start();

require_once __DIR__ . "/../DB.php";
$conn = connect();

if (!isset($_GET['masp'])) {
    echo "Sản phẩm không tồn tại.";
    exit;
}
$masp = $_GET['masp'];

// Lấy thông tin sản phẩm + nhà cung cấp + loại sp
$sql = "SELECT p.*, 
               n.tenncc, n.thongtinncc, n.hinhanh AS ncc_hinhanh, 
               pt.ten_loaisp, pt.mota_loaisp
        FROM ad_product p
        LEFT JOIN ad_nhacc n ON p.mancc = n.mancc
        LEFT JOIN ad_producttype pt ON p.ma_loaisp = pt.ma_loaisp
        WHERE p.masp = :masp";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':masp', $masp);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    echo "Không tìm thấy sản phẩm.";
    exit;
}

// Lấy thông tin khuyến mại từ bảng qlkm
$sqlKM = "SELECT * FROM qlkm WHERE makm = :makm";
$stmtKM = $conn->prepare($sqlKM);
$stmtKM->bindParam(':makm', $product['makm']);
$stmtKM->execute();
$promo = $stmtKM->fetch(PDO::FETCH_ASSOC);

if ($promo) {
    $discountPercent = (int)$promo['phantramkm'];
} else {
    preg_match('/\d+/', $product['makm'], $match);
    $discountPercent = isset($match[0]) ? (int)$match[0] : 0;
}

$giaGoc = (int)$product['giaxuat'];
$discountAmount = $giaGoc * ($discountPercent / 100);
$giaSauKM = $giaGoc - $discountAmount;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <title>Chi tiết sản phẩm</title>
  <style>
    /* Reset CSS */
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      color: #333;
      line-height: 1.6;
    }
    a { text-decoration: none; color: inherit; }
    img { max-width: 100%; display: block; }
    .container {
      width: 90%;
      max-width: 1200px;
      margin: 30px auto;
      background-color: #fff;
      padding: 20px;
      border-radius: 6px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    /* Layout sản phẩm: 2 cột */
    .product-detail {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }
    /* Gallery ảnh sản phẩm */
    .product-gallery {
      border-right: 1px solid #ddd;
      padding-right: 20px;
    }
    .main-image {
      margin-bottom: 15px;
    }
    .main-image img {
      border-radius: 6px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      width: 100%;
      height: auto;
      object-fit: cover;
    }
    .thumbnail-list {
      display: flex;
      gap: 10px;
    }
    .thumbnail-list img {
      width: 60px;
      height: 60px;
      object-fit: cover;
      cursor: pointer;
      border: 2px solid transparent;
      border-radius: 4px;
      transition: border-color 0.3s;
    }
    .thumbnail-list img:hover,
    .thumbnail-list img.active {
      border-color: #ff424e;
    }
    /* Thông tin sản phẩm */
    .product-info h1 {
      font-size: 24px;
      margin-bottom: 10px;
    }
    .product-variants {
      margin: 15px 0;
    }
    .variant-group {
      margin-bottom: 10px;
    }
    .variant-group label {
      font-weight: bold;
      margin-right: 10px;
    }
    .variant-group button {
      margin-right: 5px;
      padding: 8px 12px;
      border: 1px solid #ccc;
      background-color: #f9f9f9;
      cursor: pointer;
      border-radius: 4px;
      transition: background-color 0.3s, color 0.3s;
    }
    .variant-group button.active {
      background-color: #007bff;
      color: #fff;
      border-color: #007bff;
    }
    /* Giá sản phẩm */
    .product-price {
      margin: 15px 0;
    }
    .original-price {
      font-size: 14px;
      color: #999;
      text-decoration: line-through;
      margin-bottom: 5px;
    }
    .sale-price {
      font-size: 20px;
      color: #d0021b;
      font-weight: bold;
    }
    .discount {
      margin-left: 10px;
      background-color: #fff0f0;
      color: #d0021b;
      padding: 3px 5px;
      border-radius: 4px;
      font-size: 14px;
    }
    /* Khuyến mại nổi bật */
    .promo-highlight {
      background-color: #fff8e1;
      border: 1px solid #ffd54f;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 15px;
    }
    .promo-highlight h3 {
      margin-bottom: 5px;
      font-size: 16px;
      color: #bf7b00;
    }
    .promo-highlight ul {
      list-style: disc;
      margin-left: 20px;
    }
    /* Nút thêm vào giỏ hàng & số lượng */
    .cta-buttons {
      margin-top: 20px;
      display: flex;
      align-items: center;
    }
    .add-cart {
      padding: 12px 20px;
      margin-left: 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 15px;
      transition: opacity 0.3s;
      background-color: #ff424e;
      color: #fff;
    }
    .add-cart:hover { opacity: 0.9; }
    /* Custom quantity control */
    .quantity-control {
      display: inline-flex;
      align-items: center;
      border: 1px solid #ccc;
      border-radius: 4px;
      overflow: hidden;
    }
    .quantity-control button {
      background-color: #f9f9f9;
      border: none;
      padding: 10px 14px;
      font-size: 18px;
      cursor: pointer;
      color: #333;
      transition: background-color 0.3s;
    }
    .quantity-control button:hover {
      background-color: #e0e0e0;
    }
    .quantity-control input {
      width: 50px;
      text-align: center;
      border: none;
      outline: none;
      font-size: 16px;
      /* Không cho phép nhập trực tiếp */
      pointer-events: none;
      background-color: #fff;
    }
    /* Thông tin bổ sung */
    .extra-info {
      margin-top: 20px;
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
    }
    .info-box {
      background-color: #fafafa;
      border: 1px solid #eee;
      border-radius: 6px;
      padding: 15px;
      text-align: center;
    }
    .info-box h2 {
      font-size: 16px;
      margin-bottom: 8px;
    }
    .info-box ul {
      list-style: disc;
      margin-left: 20px;
      text-align: left;
    }
    /* Ảnh nhà cung cấp */
    .supplier-img {
      display: block;
      margin: 10px auto 0;
      max-width: 120px;
      border-radius: 50%;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      border: 1px solid #ccc;
    }
    /* Tabs */
    .detail-tabs {
      margin-top: 30px;
    }
    .tab-content {
    max-height: 300px; /* Đặt chiều cao tối đa */
    overflow-y: auto;  /* Bật thanh cuộn dọc khi nội dung vượt quá chiều cao này */
    }
    .tab-menu {
      display: flex;
      border-bottom: 1px solid #ddd;
      margin-bottom: 15px;
    }
    .tab-menu button {
      flex: 1;
      padding: 10px 0;
      background: none;
      border: none;
      cursor: pointer;
      font-weight: 600;
      font-size: 16px;
      transition: color 0.3s, border-bottom 0.3s;
    }
    .tab-menu button.active {
      border-bottom: 3px solid #ff424e;
      color: #ff424e;
    }
    .tab-content { min-height: 150px; }
    .tab-pane { display: none; }
    .tab-pane.active { display: block; }
    .description-box p { margin-bottom: 10px; line-height: 1.5; }
    /* Responsive */
    @media (max-width: 768px) {
      .product-detail { grid-template-columns: 1fr; }
      .product-gallery { border-right: none; padding-right: 0; margin-bottom: 20px; }
      .extra-info { grid-template-columns: 1fr; }
    }
    /* Footer */
    footer {
      margin-top: 30px;
      text-align: center;
      font-size: 14px;
      color: #666;
    }
  </style>
  <script>
    // Thay đổi ảnh chính khi click vào thumbnail
    function changeMainImage(src, thumb) {
      document.getElementById('main-image').src = src;
      let thumbs = document.querySelectorAll('.thumbnail-list img');
      thumbs.forEach(img => img.classList.remove('active'));
      thumb.classList.add('active');
    }
    // Xử lý chọn option (dung lượng, màu sắc)
    function selectOption(btn) {
      let group = btn.parentElement;
      let buttons = group.querySelectorAll('button');
      buttons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    }
    // Hàm mở tab cho Thông số kỹ thuật và Mô tả sản phẩm
    function openTab(tabName) {
      var tabPanes = document.querySelectorAll('.tab-pane');
      tabPanes.forEach(function(pane) {
        pane.classList.remove('active');
      });
      document.getElementById(tabName).classList.add('active');

      var tabButtons = document.querySelectorAll('.tab-menu button');
      tabButtons.forEach(function(btn) {
        btn.classList.remove('active');
      });
      if (tabName === 'specs') {
        document.getElementById('btn-specs').classList.add('active');
      } else if (tabName === 'desc') {
        document.getElementById('btn-desc').classList.add('active');
      }
    }
    // Hàm xử lý thêm sản phẩm vào giỏ hàng với số lượng và option đã chọn
    function addToCart(masp, available) {
      var qty = document.getElementById('quantity').value;
      if (parseInt(qty) > available) {
        alert("Số lượng vượt quá số lượng có sẵn (" + available + ")!");
        return;
      }
      // Lấy giá trị dung lượng và màu sắc được chọn
      var capacityBtn = document.querySelector('.product-variants .variant-group:nth-of-type(1) button.active');
      var colorBtn = document.querySelector('.product-variants .variant-group:nth-of-type(2) button.active');
      var capacity = capacityBtn ? capacityBtn.textContent.trim() : '';
      var color = colorBtn ? colorBtn.textContent.trim() : '';
      
      // Chuyển hướng tới add_to_cart.php với các tham số cần thiết
      window.location.href = 'add_to_cart.php?masp=' + encodeURIComponent(masp) + 
                               '&quantity=' + encodeURIComponent(qty) +
                               '&capacity=' + encodeURIComponent(capacity) +
                               '&color=' + encodeURIComponent(color);
    }
    // Xử lý nút tăng giảm số lượng
    document.addEventListener("DOMContentLoaded", function() {
      const decrementBtn = document.querySelector('.quantity-control .decrement');
      const incrementBtn = document.querySelector('.quantity-control .increment');
      const qtyInput = document.getElementById('quantity');

      decrementBtn.addEventListener('click', function() {
        let currentVal = parseInt(qtyInput.value);
        let minVal = parseInt(qtyInput.getAttribute('min'));
        if (currentVal > minVal) {
          qtyInput.value = currentVal - 1;
        }
      });

      incrementBtn.addEventListener('click', function() {
        let currentVal = parseInt(qtyInput.value);
        let maxVal = parseInt(qtyInput.getAttribute('max'));
        if (currentVal < maxVal) {
          qtyInput.value = currentVal + 1;
        }
      });
    });
  </script>
</head>
<body>
  <div class="container">
    <div class="product-detail">
      <!-- Cột trái: Gallery ảnh sản phẩm -->
      <div class="product-gallery">
        <div class="main-image">
          <img id="main-image" src="../public/images/<?php echo htmlspecialchars($product['hinhanh']); ?>" alt="Hình ảnh chính">
        </div>
        <div class="thumbnail-list">
          <?php
            $thumbs = [
              $product['hinhanh'],
              $product['hinhanh1'],
              $product['hinhanh2']
            ];
            foreach ($thumbs as $thumb) {
              if (!empty($thumb)) {
                echo '<img src="../public/images/'.htmlspecialchars($thumb).'" onclick="changeMainImage(this.src, this)" alt="thumb">';
              }
            }
          ?>
        </div>
      </div>
      <!-- Cột phải: Thông tin sản phẩm -->
      <div class="product-info">
        <h1><?php echo htmlspecialchars($product['tensp']); ?></h1>
        <div class="product-variants">
          <div class="variant-group">
            <label>Dung lượng:</label>
            <?php
              $dlFields = ['dungluong', 'dungluong1', 'dungluong2'];
              foreach ($dlFields as $field) {
                if (!empty($product[$field])) {
                  echo '<button onclick="selectOption(this)">'.htmlspecialchars($product[$field]).'</button>';
                }
              }
            ?>
          </div>
          <div class="variant-group">
            <label>Màu sắc:</label>
            <?php
              $msFields = ['mausac', 'mausac1', 'mausac2'];
              foreach ($msFields as $field) {
                if (!empty($product[$field])) {
                  echo '<button onclick="selectOption(this)">'.htmlspecialchars($product[$field]).'</button>';
                }
              }
            ?>
          </div>
        </div>
        <div class="product-price">
          <?php if ($giaGoc > 0): ?>
            <p class="original-price"><?php echo number_format($giaGoc, 0, ',', '.'); ?> đ</p>
          <?php endif; ?>
          <p class="sale-price">
            <?php echo number_format($giaSauKM, 0, ',', '.'); ?> đ
            <?php if ($discountPercent > 0): ?>
              <span class="discount">Giảm <?php echo $discountPercent; ?>%</span>
            <?php endif; ?>
          </p>
        </div>
        <?php if ($promo): ?>
        <div class="promo-highlight">
          <h3>Khuyến mãi nổi bật</h3>
          <ul>
            <li><?php echo htmlspecialchars($promo['thongtinkm']); ?> (Giảm <?php echo $promo['phantramkm']; ?>%)</li>
          </ul>
        </div>
        <?php endif; ?>
        <div class="cta-buttons">
          <!-- Khối hiển thị số lượng với nút trừ & cộng (readonly) -->
          <div class="quantity-control">
            <button type="button" class="decrement">-</button>
            <input type="number" id="quantity" value="1" min="1" max="<?php echo $product['soluong']; ?>" readonly>
            <button type="button" class="increment">+</button>
          </div>
          <button class="add-cart" onclick="addToCart('<?php echo urlencode($product['masp']); ?>', <?php echo $product['soluong']; ?>)">
            Thêm vào giỏ hàng
          </button>
        </div>
      </div>
    </div> <!-- end .product-detail -->

    <!-- Khối thông tin bổ sung -->
    <div class="extra-info">
      <div class="info-box">
        <h2>Chính sách dành cho sản phẩm</h2>
        <ul>
          <li>Hàng chính hãng - Bảo hành 12 tháng</li>
          <li>Giao hàng toàn quốc</li>
          <li>Hỗ trợ kỹ thuật trực tuyến</li>
        </ul>
      </div>
      <div class="info-box">
        <h2>Nhà cung cấp: <?php echo htmlspecialchars($product['tenncc']); ?></h2>
        <p><?php echo htmlspecialchars($product['thongtinncc']); ?></p>
        <?php if(!empty($product['ncc_hinhanh'])): ?>
          <img class="supplier-img" src="../public/images/<?php echo htmlspecialchars($product['ncc_hinhanh']); ?>" alt="Nhà cung cấp">
        <?php endif; ?>
      </div>
      <div class="info-box">
        <h2>Loại sản phẩm: <?php echo htmlspecialchars($product['ten_loaisp']); ?></h2>
        <p><?php echo htmlspecialchars($product['mota_loaisp']); ?></p>
      </div>
    </div>

    <!-- Tabs: Thông số kỹ thuật & Mô tả sản phẩm -->
    <div class="detail-tabs">
      <div class="tab-menu">
        <button id="btn-specs" class="active" onclick="openTab('specs')">Thông số kỹ thuật</button>
        <button id="btn-desc" onclick="openTab('desc')">Mô tả sản phẩm</button>
      </div>
      <div class="tab-content">
        <div class="tab-pane active" id="specs">
          <div class="description-box">
            <p><strong>Xuất xứ:</strong> <?php echo htmlspecialchars($product['xuat_su']); ?></p>
            <p><strong>Thông số kỹ thuật:</strong></p>
            <p><?php echo nl2br(htmlspecialchars($product['thong_so_ky_thuat'])); ?></p>
          </div>
        </div>
        <div class="tab-pane" id="desc">
          <div class="description-box">
            <p><?php echo nl2br(htmlspecialchars($product['mota_sp'])); ?></p>
          </div>
        </div>
      </div>
    </div>
    
    <div style="margin-top: 20px;">
      <a href="new.php" style="color: #007bff;">&laquo; Quay lại trang chủ</a>
    </div>
  </div> <!-- end .container -->

  <footer>
    <p>© 2025 - Bản quyền thuộc về [Tên cửa hàng của bạn]</p>
  </footer>
</body>
</html>
<?php include __DIR__ . '/includes/footer.php'; ?>
