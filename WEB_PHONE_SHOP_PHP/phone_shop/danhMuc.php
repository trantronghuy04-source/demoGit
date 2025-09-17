<?php
session_start();

require_once __DIR__ . "/../DB.php";
$conn = connect();

// Lấy danh sách danh mục sản phẩm
$sqlCategories = "SELECT * FROM ad_producttype";
$stmtCategories = $conn->query($sqlCategories);
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

// Xử lý lọc theo danh mục
$selectedCategory = isset($_GET['ma_loaisp']) ? $_GET['ma_loaisp'] : '';
$where = [];
$params = [];
if ($selectedCategory != '') {
  $where[] = "ma_loaisp = :ma_loaisp";
  $params[':ma_loaisp'] = $selectedCategory;
}

// Tạo truy vấn sản phẩm
$sqlProducts = "SELECT * FROM ad_product";
if (!empty($where)) {
  $sqlProducts .= " WHERE " . implode(" AND ", $where);
}
$sqlProducts .= " ORDER BY create_date DESC";
$stmtProducts = $conn->prepare($sqlProducts);
$stmtProducts->execute($params);
$products = $stmtProducts->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Menu Danh mục dạng Popup (Chỉ CSDL)</title>
  <!-- Font Awesome để dùng icon -->
  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4+4O8YZHW/r2cycK7CEX1CRmDUyMGKDPk3pTx3Xf2JzZ8v1k9W+2W3eQA=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer"
  />
  <style>
    /* RESET đơn giản */
    * {
      margin: 0; 
      padding: 0; 
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }
    body {
      background-color: #f8f8f8;
      color: #333;
    }

    /* Thanh trên cùng */
    .top-bar {
      display: flex;
      align-items: center;
      background-color: #fff;
      padding: 10px 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    /* Khối chứa cho menu */
    .menu-container {
      position: relative;
      margin-right: 20px;
    }
    /* Nút danh mục có hình tròn */
    .menu-btn {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      background: linear-gradient(45deg, #6a11cb, #2575fc);
      border: none;
      color: #fff;
      font-size: 22px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .menu-btn:hover {
      transform: scale(1.1);
      box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    }
    .menu-btn:active {
      transform: scale(0.95);
    }
    .menu-btn:focus {
      outline: none;
    }

    /* Khối popup menu */
    .popup-menu {
      position: absolute;
      top: 100%;
      left: 0; 
      background-color: #fff;
      color: #333;
      width: 220px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      padding: 10px 0;
      display: none;
      z-index: 999;
    }
    .popup-menu.active {
      display: block;
    }
    .popup-menu .menu-section {
      padding: 0 10px;
      margin-bottom: 8px;
    }
    .popup-menu .menu-section:last-child {
      margin-bottom: 0;
    }
    .popup-menu li {
      list-style: none;
      display: flex;
      align-items: center;
      padding: 8px 0;
      cursor: pointer;
      transition: background-color 0.2s;
      border-radius: 6px;
    }
    .popup-menu li:hover {
      background-color: #f2f2f2;
    }
    .popup-menu li i {
      margin-right: 10px;
      font-size: 18px;
      width: 24px;
      text-align: center;
      color: #333;
    }
    .popup-menu li span {
      font-size: 14px;
      color: #333;
    }

    /* Danh sách sản phẩm */
    .all-products-header {
      text-align: center;
      font-size: 26px;
      font-weight: bold;
      color: #222;
      margin: 30px 0 20px;
    }
    .product-list {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
      margin: 20px;
    }
    .product-container-grid {
      border: 1px solid #ccc;
      border-radius: 10px;
      background-color: white;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      transition: transform 0.2s, box-shadow 0.2s;
      cursor: pointer;
      padding: 16px;
    }
    .product-container-grid:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 12px rgba(0,0,0,0.2);
    }
    .product-container-grid img {
      width: 100%;
      max-height: 180px;
      object-fit: contain;
      margin-bottom: 16px;
    }
    .product-container-grid h3 {
      font-size: 18px;
      color: #333;
      margin: 10px 0;
    }
    .product-price {
      font-size: 14px;
      color: #e63946;
      font-weight: bold;
      margin-top: 5px;
    }
    .original-price {
      color: #999;
      text-decoration: line-through;
    }
    .discount {
      font-size: 13px;
      color: #d0021b;
      margin-left: 5px;
      background: #fff0f0;
      padding: 2px 4px;
      border-radius: 4px;
    }
    .status {
      font-size: 14px;
      margin-top: 5px;
      color: #2e7d32;
    }
    .status.out {
      color: red;
    }
  </style>
</head>
<body>
  <!-- Thanh trên cùng -->
  <div class="top-bar">
    <div class="menu-container">
      <!-- Nút danh mục với thiết kế hình tròn -->
      <button class="menu-btn" id="btnMenu" title="Danh mục">
        <i class="fas fa-bars"></i>
      </button>
      <!-- Menu popup hiển thị danh mục -->
      <div class="popup-menu" id="popupMenu">
        <div class="menu-section">
          <ul>
            <li onclick="window.location.href='?'">
              <i class="fa fa-home"></i>
              <span>Tất cả</span>
            </li>
            <?php foreach ($categories as $cat): ?>
              <li onclick="window.location.href='?ma_loaisp=<?php echo urlencode($cat['ma_loaisp']); ?>'">
                <i class="fa fa-tag"></i>
                <span><?php echo htmlspecialchars($cat['ten_loaisp']); ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
    <!-- (Nếu cần) Thêm ô tìm kiếm, logo, v.v... -->
  </div>

  <!-- NỘI DUNG SẢN PHẨM -->
  <div class="all-products-header">
    <?php 
      if ($selectedCategory != '') {
        foreach ($categories as $cat) {
          if ($cat['ma_loaisp'] == $selectedCategory) {
            echo "Sản phẩm thuộc danh mục: " . htmlspecialchars($cat['ten_loaisp']);
            break;
          }
        }
      } else {
        echo "TẤT CẢ SẢN PHẨM";
      }
    ?>
  </div>
  <div class="product-list">
    <?php foreach ($products as $item): 
            $sqlPromo = "SELECT * FROM qlkm WHERE makm = :makm";
            $stmtPromo = $conn->prepare($sqlPromo);
            $stmtPromo->bindParam(':makm', $item['makm']);
            $stmtPromo->execute();
            $promoItem = $stmtPromo->fetch(PDO::FETCH_ASSOC);
            $discountPercent = $promoItem ? $promoItem['phantramkm'] : 0;
            $giaGoc = $item['giaxuat'];
            $discountAmount = $giaGoc * ($discountPercent / 100);
            $giaSauKM = $giaGoc - $discountAmount;
    ?>
      <div class="product-container-grid"
           onclick="window.location.href='product_detail.php?masp=<?php echo urlencode($item['masp']); ?>'">
        <img src="../public/images/<?php echo htmlspecialchars($item['hinhanh']); ?>" alt="Hình ảnh sản phẩm">
        <h3><?php echo htmlspecialchars($item['tensp']); ?></h3>
        <p class="product-price">
          <?php if ($discountPercent > 0): ?>
            <span class="original-price"><?php echo number_format($giaGoc, 0, ',', '.'); ?> VND</span><br>
            <span class="sale-price"><?php echo number_format($giaSauKM, 0, ',', '.'); ?> VND</span>
            <span class="discount">Giảm <?php echo $discountPercent; ?>%</span>
          <?php else: ?>
            <span class="sale-price"><?php echo number_format($giaGoc, 0, ',', '.'); ?> VND</span>
          <?php endif; ?>
        </p>
        <p class="status">
          <?php echo ($item['soluong'] > 0) ? "Còn hàng" : "<span class='out'>Hết hàng</span>"; ?>
        </p>
      </div>
    <?php endforeach; ?>
  </div>

  <script>
    const btnMenu = document.getElementById('btnMenu');
    const popupMenu = document.getElementById('popupMenu');

    // Toggle menu khi bấm nút danh mục
    btnMenu.addEventListener('click', function(e) {
      e.stopPropagation();
      popupMenu.classList.toggle('active');
    });

    // Đóng menu khi click ra ngoài
    document.addEventListener('click', function(e) {
      if (!btnMenu.contains(e.target) && !popupMenu.contains(e.target)) {
        popupMenu.classList.remove('active');
      }
    });
  </script>
</body>
</html>
<?php include __DIR__ . '/includes/footer.php'; ?>
