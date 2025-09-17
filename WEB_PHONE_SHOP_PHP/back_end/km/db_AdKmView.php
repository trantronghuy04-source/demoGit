<?php
require_once "DB.php";

// Hàm thêm khuyến mãi
function insertKhuyenMai($makm, $phantramkm, $thongtinkm) {
    $db = connect();
    $sql = "INSERT INTO qlkm (makm, phantramkm, thongtinkm) VALUES (:makm, :phantramkm, :thongtinkm)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':makm', $makm);
    $stmt->bindParam(':phantramkm', $phantramkm);
    $stmt->bindParam(':thongtinkm', $thongtinkm);
    try {
        $stmt->execute();
        echo "Thêm mới khuyến mãi thành công.";
    } catch (PDOException $e) {
        echo "Lỗi khi thêm mới: " . $e->getMessage();
    }
}

// Hàm lấy thông tin khuyến mãi theo mã
function getKhuyenMaiByID($makm) {
    $db = connect();
    $sql = "SELECT * FROM qlkm WHERE makm = :makm";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':makm', $makm);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        echo "Không tìm thấy dữ liệu!";
    }
    return $result;
}

// Hàm lấy toàn bộ danh sách khuyến mãi
function getAllKhuyenMai() {
    $db = connect();
    $sql = "SELECT * FROM qlkm";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$results) {
        echo "Không tìm thấy dữ liệu!";
    }
    return $results;
}

// Hàm xóa khuyến mãi theo mã
function deleteKhuyenMai($makm) {
    $db = connect();
    $sql = "DELETE FROM qlkm WHERE makm = :makm";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':makm', $makm);
    try {
        $stmt->execute();
        echo "Xóa khuyến mãi thành công.";
    } catch (PDOException $e) {
        echo "Lỗi khi xóa: " . $e->getMessage();
    }
}

// Hàm cập nhật khuyến mãi
function updateKhuyenMai($makm, $phantramkm, $thongtinkm) {
    $db = connect();
    $sql = "UPDATE qlkm SET phantramkm = :phantramkm, thongtinkm = :thongtinkm WHERE makm = :makm";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':makm', $makm);
    $stmt->bindParam(':phantramkm', $phantramkm);
    $stmt->bindParam(':thongtinkm', $thongtinkm);
    try {
        $stmt->execute();
        echo "Cập nhật khuyến mãi thành công.";
    } catch (PDOException $e) {
        echo "Lỗi khi cập nhật: " . $e->getMessage();
    }
}

// Hàm tự sinh mã khuyến mãi theo định dạng KM001, KM002,...
function generatePromotionCode() {
    $db = connect();
    $sql = "SELECT MAX(makm) as max_code FROM qlkm";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $max_code = $row['max_code'];
    if($max_code){
         $num = intval(substr($max_code, 2)); // loại bỏ "KM"
         $new_num = $num + 1;
         return "KM" . str_pad($new_num, 3, "0", STR_PAD_LEFT);
    } else {
         return "KM001";
    }
}
?>
