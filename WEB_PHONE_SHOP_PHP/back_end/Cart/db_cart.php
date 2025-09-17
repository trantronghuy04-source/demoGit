<?php
// Giả sử bạn đã có hàm connect() để kết nối CSDL

// Lấy danh sách sản phẩm kèm thông tin khuyến mãi (phantramkm) và tính sale_price
function getAllProducts() {
    $db = connect();
    $sql = "SELECT p.*, IFNULL(q.phantramkm, 0) AS phantramkm 
            FROM ad_product p 
            LEFT JOIN qlkm q ON p.makm = q.makm";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($products as &$prod) {
        $giaGoc = (int)$prod['giaxuat'];
        $discountPercent = (int)$prod['phantramkm'];
        $prod['sale_price'] = $giaGoc - ($giaGoc * $discountPercent / 100);
    }
    return $products;
}

// Lấy danh sách người dùng từ bảng users
function getAllUsers() {
    $db = connect();
    $sql = "SELECT * FROM users";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Thêm mới giỏ hàng
function insertCart($user_id, $masp, $so_luong, $dungluong = null, $mausac = null) {
    $db = connect();
    // Lấy thông tin sản phẩm từ bảng ad_product (có tích hợp khuyến mãi)
    $sql = "SELECT p.*, IFNULL(q.phantramkm, 0) AS phantramkm 
            FROM ad_product p 
            LEFT JOIN qlkm q ON p.makm = q.makm 
            WHERE p.masp = :masp";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':masp', $masp);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        return "Sản phẩm không tồn tại.";
    }
    
    // Kiểm tra số lượng tồn kho (giả sử cột tồn kho là 'soluong')
    if ($so_luong > $product['soluong']) {
        return "Số lượng đặt vượt quá số lượng sản phẩm hiện có.";
    }
    
    $giaGoc = (int)$product['giaxuat'];
    $discountPercent = (int)$product['phantramkm'];
    $giaSauKM = $giaGoc - ($giaGoc * $discountPercent / 100);
    $tong_tien = $so_luong * $giaSauKM;
    // Nếu dungluong, mausac không có thì lấy mặc định từ sản phẩm
    if (!$dungluong) {
        $dungluong = $product['dungluong'];
    }
    if (!$mausac) {
        $mausac = $product['mausac'];
    }
    $sqlInsert = "INSERT INTO cart (user_id, masp, tensp, hinhanh, dungluong, mausac, so_luong, gia_sau_km, tong_tien)
                  VALUES (:user_id, :masp, :tensp, :hinhanh, :dungluong, :mausac, :so_luong, :gia_sau_km, :tong_tien)";
    $stmtInsert = $db->prepare($sqlInsert);
    $stmtInsert->bindParam(':user_id', $user_id);
    $stmtInsert->bindParam(':masp', $masp);
    $stmtInsert->bindParam(':tensp', $product['tensp']);
    $stmtInsert->bindParam(':hinhanh', $product['hinhanh']);
    $stmtInsert->bindParam(':dungluong', $dungluong);
    $stmtInsert->bindParam(':mausac', $mausac);
    $stmtInsert->bindParam(':so_luong', $so_luong);
    $stmtInsert->bindParam(':gia_sau_km', $giaSauKM);
    $stmtInsert->bindParam(':tong_tien', $tong_tien);
    try {
        $stmtInsert->execute();
        return true;
    } catch(PDOException $e) {
        return "Lỗi khi thêm giỏ hàng: " . $e->getMessage();
    }
}

// Lấy toàn bộ giỏ hàng
function getCart() {
    $db = connect();
    $sql = "SELECT * FROM cart";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Lấy thông tin 1 giỏ hàng theo cart_id
function getCartByID($cart_id) {
    $db = connect();
    $sql = "SELECT * FROM cart WHERE cart_id = :cart_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':cart_id', $cart_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Cập nhật giỏ hàng
function updateCart($cart_id, $user_id, $masp, $dungluong, $mausac, $so_luong) {
    $db = connect();
    // Lấy thông tin sản phẩm (có khuyến mãi)
    $sql = "SELECT p.*, IFNULL(q.phantramkm, 0) AS phantramkm 
            FROM ad_product p 
            LEFT JOIN qlkm q ON p.makm = q.makm 
            WHERE p.masp = :masp";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':masp', $masp);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        return "Sản phẩm không tồn tại.";
    }
    
    // Kiểm tra số lượng tồn kho
    if ($so_luong > $product['soluong']) {
        return "Số lượng đặt vượt quá số lượng sản phẩm hiện có.";
    }
    
    $giaGoc = (int)$product['giaxuat'];
    $discountPercent = (int)$product['phantramkm'];
    $giaSauKM = $giaGoc - ($giaGoc * $discountPercent / 100);
    $tong_tien = $so_luong * $giaSauKM;
    $sqlUpdate = "UPDATE cart 
                  SET user_id = :user_id, masp = :masp, dungluong = :dungluong, mausac = :mausac, 
                      so_luong = :so_luong, gia_sau_km = :gia_sau_km, tong_tien = :tong_tien 
                  WHERE cart_id = :cart_id";
    $stmtUpdate = $db->prepare($sqlUpdate);
    $stmtUpdate->bindParam(':user_id', $user_id);
    $stmtUpdate->bindParam(':masp', $masp);
    $stmtUpdate->bindParam(':dungluong', $dungluong);
    $stmtUpdate->bindParam(':mausac', $mausac);
    $stmtUpdate->bindParam(':so_luong', $so_luong);
    $stmtUpdate->bindParam(':gia_sau_km', $giaSauKM);
    $stmtUpdate->bindParam(':tong_tien', $tong_tien);
    $stmtUpdate->bindParam(':cart_id', $cart_id);
    try {
        $stmtUpdate->execute();
        return true;
    } catch(PDOException $e) {
        return "Lỗi khi cập nhật giỏ hàng: " . $e->getMessage();
    }
}

// Xóa giỏ hàng
function deleteCart($cart_id) {
    $db = connect();
    $sql = "DELETE FROM cart WHERE cart_id = :cart_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':cart_id', $cart_id);
    try {
        $stmt->execute();
        return true;
    } catch(PDOException $e) {
        return "Lỗi khi xóa giỏ hàng: " . $e->getMessage();
    }
}
?>
