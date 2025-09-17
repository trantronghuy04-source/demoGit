<?php
session_start();

require_once __DIR__ . "/../DB.php";
$conn = connect();

// Lấy danh sách tin tức (sắp xếp theo ngày đăng giảm dần)
$sqlNews = "SELECT * FROM ad_tintuc ORDER BY ngay_dang DESC";
$stmtNews = $conn->query($sqlNews);
$news = $stmtNews->fetchAll(PDO::FETCH_ASSOC);

// Lấy danh sách sản phẩm nổi bật (featured = 1) – 10 sản phẩm mới nhất
$sqlFeatured = "SELECT * FROM ad_product WHERE featured = 1 ORDER BY create_date DESC LIMIT 10";
$stmtFeatured = $conn->query($sqlFeatured);
$featuredProducts = $stmtFeatured->fetchAll(PDO::FETCH_ASSOC);

// Xử lý bộ lọc cho "TẤT CẢ SẢN PHẨM"
$where = [];
$params = [];

// Lọc theo tên sản phẩm
if (isset($_GET['search']) && $_GET['search'] !== '') {
  $where[] = "tensp LIKE :search";
  $params[':search'] = '%' . $_GET['search'] . '%';
}

// Tạo câu truy vấn cơ bản
$sqlAll = "SELECT * FROM ad_product";
if (!empty($where)) {
  $sqlAll .= " WHERE " . implode(" AND ", $where);
}

// Sắp xếp theo giá nếu có, nếu không thì sắp xếp theo ngày tạo giảm dần
if (isset($_GET['price_sort']) && $_GET['price_sort'] == 'asc') {
  $sqlAll .= " ORDER BY giaxuat ASC";
} elseif (isset($_GET['price_sort']) && $_GET['price_sort'] == 'desc') {
  $sqlAll .= " ORDER BY giaxuat DESC";
} else {
  $sqlAll .= " ORDER BY create_date DESC";
}

// Thực thi truy vấn lọc sản phẩm
$stmtAll = $conn->prepare($sqlAll);
$stmtAll->execute($params);
$allProducts = $stmtAll->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/includes/header.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tin tức & Sản phẩm</title>
  <link rel="stylesheet" href="../phone_shop/assets/css/style.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f8f8f8;
      margin: 0;
      padding: 0;
    }
    /* Tin tức */
    .news-header {
      text-align: center;
      font-size: 28px;
      font-weight: bold;
      color: #222;
      margin: 40px 0 20px;
    }
    .news-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 30px;
      padding: 20px;
    }
    .news-item {
      width: 80%;
      display: flex;
      align-items: center;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .news-item:hover {
      transform: translateY(-10px);
      box-shadow: 0 10px 20px rgba(0,0,0,0.2);
    }
    .news-image {
      width: 50%;
    }
    .news-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }
    .news-content {
      width: 50%;
      padding: 20px;
      text-align: center;
    }
    .news-title {
      font-size: 20px;
      font-weight: bold;
      color: #111;
      margin-bottom: 10px;
    }
    .news-desc {
      font-size: 14px;
      color: #555;
      line-height: 1.5;
    }

    /* Sản phẩm nổi bật (slider) */
    .product-header {
      text-align: center;
      font-size: 28px;
      font-weight: bold;
      color: #222;
      margin: 40px 0 20px;
    }
    .product-wrapper {
      display: flex;
      align-items: center;
      position: relative;
      width: 90%;
      margin: auto;
    }
    .product-container {
      display: flex;
      flex-wrap: nowrap;
      overflow-x: auto;
      scroll-behavior: smooth;
      gap: 15px;
      padding: 15px;
      width: 100%;
      scrollbar-width: none;
    }
    .product-container::-webkit-scrollbar {
      display: none;
    }
    .product-item {
      flex: 0 0 250px;
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      text-align: center;
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .product-item:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 12px rgba(0,0,0,0.2);
    }
    .product-container .product-image {
      height: 180px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
    }
    .product-container .product-image img {
      width: 100%;
      max-height: 180px;
      object-fit: contain;
    }
    .product-content {
      padding: 10px;
    }
    .product-title {
      font-size: 16px;
      font-weight: bold;
      color: #111;
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

    /* Nút cuộn trái/phải cho slider */
    button.scroll-left, button.scroll-right {
      background-color: rgba(0, 0, 0, 0.7);
      color: white;
      border: none;
      padding: 10px;
      font-size: 20px;
      cursor: pointer;
      position: absolute;
      top: 50%;
      transform: translateY(-50%);
      z-index: 10;
      border-radius: 50%;
    }
    button.scroll-left { left: -10px; }
    button.scroll-right { right: -10px; }
    button.scroll-left:hover, button.scroll-right:hover {
      background-color: rgba(0, 0, 0, 0.9);
    }

    /* Tất cả sản phẩm (grid) */
    .all-products-header {
      text-align: center;
      font-size: 28px;
      font-weight: bold;
      color: #222;
      margin: 40px 0 20px;
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
      padding: 16px;
      background-color: white;
      text-align: center;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      transition: transform 0.2s, box-shadow 0.2s;
      cursor: pointer;
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
      font-size: 20px;
      color: #333;
      margin: 10px 0;
    }

    /* Thanh tìm kiếm + sắp xếp */
    .search-sort-bar {
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 20px auto;
  width: 100%;       /* Dùng 100% để phủ toàn bộ chiều ngang */
  max-width: none;   /* Hoặc bỏ giới hạn */
  gap: 10px;
}

    /* Ô tìm kiếm dạng bo tròn với icon kính lúp bên trái */
    .search-box input[type="text"] {
  width: 200px; /* Tăng lên tuỳ ý */
  max-width: 100%;
  padding: 12px 50px 12px 40px;
  border: none;
  border-radius: 50px;
  font-size: 16px;
  color: #333;
  background: #fff;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  outline: none;
}



    /* Select sắp xếp */
    .sort-select {
      padding: 12px 16px;
      font-size: 16px;
      border: none;
      outline: none;
      border-radius: 50px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      background: #fff;
      cursor: pointer;
    }
    /* Nút submit */
    .search-submit {
      padding: 12px 20px;
      font-size: 16px;
      border: none;
      outline: none;
      border-radius: 50px;
      background: linear-gradient(135deg, #e63946, #d62839);
      color: #fff;
      cursor: pointer;
      transition: background 0.3s;
      margin-left: 5px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .search-submit:hover {
      background: linear-gradient(135deg, #d62839, #e63946);
    }
  </style>
  <script>
    // Xử lý nút cuộn slider
    document.addEventListener("DOMContentLoaded", function() {
      const container = document.querySelector(".product-container");
      const btnLeft = document.querySelector(".scroll-left");
      const btnRight = document.querySelector(".scroll-right");

      btnLeft.addEventListener("click", function() {
        container.scrollBy({ left: -250, behavior: "smooth" });
      });
      btnRight.addEventListener("click", function() {
        container.scrollBy({ left: 250, behavior: "smooth" });
      });
    });
  </script>
</head>
<body>
  <!-- Tin tức -->
  <div class="news-header">TIN TỨC MỚI NHẤT</div>
  <div class="news-container">
    <?php foreach ($news as $item): ?>
      <div class="news-item">
        <div class="news-image">
          <img src="../public/images/<?php echo htmlspecialchars($item['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($item['tieu_de']); ?>">
        </div>
        <div class="news-content">
          <div class="news-title"><?php echo htmlspecialchars($item['tieu_de']); ?></div>
          <div class="news-desc"><?php echo htmlspecialchars($item['noi_dung']); ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Sản phẩm nổi bật (slider) -->
  <div class="product-header">SẢN PHẨM NỔI BẬT</div>
  <div class="product-wrapper">
    <button class="scroll-left">&#10094;</button>
    <div class="product-container">
      <?php foreach ($featuredProducts as $product): 
            // Tính khuyến mãi
            $sqlPromo = "SELECT * FROM qlkm WHERE makm = :makm";
            $stmtPromo = $conn->prepare($sqlPromo);
            $stmtPromo->bindParam(':makm', $product['makm']);
            $stmtPromo->execute();
            $promoItem = $stmtPromo->fetch(PDO::FETCH_ASSOC);
            $discountPercent = $promoItem ? $promoItem['phantramkm'] : 0;
            $giaGoc = $product['giaxuat'];
            $discountAmount = $giaGoc * ($discountPercent / 100);
            $giaSauKM = $giaGoc - $discountAmount;
      ?>
        <div class="product-item" onclick="window.location.href='product_detail.php?masp=<?php echo urlencode($product['masp']); ?>'">
          <div class="product-image">
            <img src="../public/images/<?php echo htmlspecialchars($product['hinhanh']); ?>" alt="<?php echo htmlspecialchars($product['tensp']); ?>">
          </div>
          <div class="product-content">
            <div class="product-title"><?php echo htmlspecialchars($product['tensp']); ?></div>
            <div class="product-price">
              <?php if ($discountPercent > 0): ?>
                <span class="original-price"><?php echo number_format($giaGoc, 0, ',', '.'); ?> VND</span><br>
                <span class="sale-price"><?php echo number_format($giaSauKM, 0, ',', '.'); ?> VND</span>
                <span class="discount">Giảm <?php echo $promoItem['phantramkm']; ?>%</span>
              <?php else: ?>
                <span class="sale-price"><?php echo number_format($giaGoc, 0, ',', '.'); ?> VND</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <button class="scroll-right">&#10095;</button>
  </div>

  <!-- Tất cả sản phẩm (grid) -->
  <div class="all-products-header">TẤT CẢ SẢN PHẨM</div>
  <!-- Thanh tìm kiếm + sắp xếp -->
  <div class="search-sort-bar">
    <form method="GET" action="" style="display: flex; width: 100%; gap: 10px;">
      <!-- Ô tìm kiếm -->
      <div class="search-box">
        <input 
          type="text" 
          name="search" 
          placeholder="Bạn cần tìm gì?" 
          value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
        >
      </div>
      <!-- Chọn sắp xếp -->
      <select name="price_sort" class="sort-select">
        <option value="">Sắp xếp theo giá</option>
        <option value="asc" <?php echo (isset($_GET['price_sort']) && $_GET['price_sort'] == 'asc') ? 'selected' : ''; ?>>Từ thấp đến cao</option>
        <option value="desc" <?php echo (isset($_GET['price_sort']) && $_GET['price_sort'] == 'desc') ? 'selected' : ''; ?>>Từ cao đến thấp</option>
      </select>
      <!-- Nút submit -->
      <button type="submit" class="search-submit">Lọc</button>
    </form>
  </div>

  <div class="product-list">
    <?php foreach ($allProducts as $item): 
            // Tính khuyến mãi
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
      <div class="product-container-grid" onclick="window.location.href='product_detail.php?masp=<?php echo urlencode($item['masp']); ?>'">
        <img src="../public/images/<?php echo htmlspecialchars($item['hinhanh']); ?>" alt="Hình ảnh sản phẩm">
        <h3><?php echo htmlspecialchars($item['tensp']); ?></h3>
        <p class="product-price">
          <?php if ($discountPercent > 0): ?>
            <span class="original-price"><?php echo number_format($giaGoc, 0, ',', '.'); ?> VND</span><br>
            <span class="sale-price"><?php echo number_format($giaSauKM, 0, ',', '.'); ?> VND</span>
            <span class="discount">Giảm <?php echo $promoItem['phantramkm']; ?>%</span>
          <?php else: ?>
            <span class="sale-price"><?php echo number_format($giaGoc, 0, ',', '.'); ?> VND</span>
          <?php endif; ?>
        </p>
        <p class="status">
          <?php echo ($item['soluong'] > 0) ? "Còn hàng" : "<span class='out' style='color:red;'>Hết hàng</span>"; ?>
        </p>
      </div>
    <?php endforeach; ?>
  </div>
</body>
</html>
<?php include __DIR__ . '/includes/footer.php'; ?>
