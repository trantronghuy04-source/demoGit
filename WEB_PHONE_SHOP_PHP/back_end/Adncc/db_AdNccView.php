<?php
require_once "DB.php"; 

// Hàm thêm nhà cung cấp
function insertNccType($mancc, $tenncc, $thongtinncc, $hinhanh) {
    $db = connect();
    $sql = "INSERT INTO ad_nhacc (mancc, tenncc, thongtinncc, hinhanh) 
            VALUES (:mancc, :tenncc, :thongtinncc, :hinhanh)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':mancc', $mancc);
    $stmt->bindParam(':tenncc', $tenncc);
    $stmt->bindParam(':thongtinncc', $thongtinncc);
    $stmt->bindParam(':hinhanh', $hinhanh);

    try {
        $stmt->execute();
        echo "Thêm mới nhà cung cấp thành công.";
    } catch (PDOException $e) {
        echo "Lỗi khi thêm mới: " . $e->getMessage();
    }
}

// Hàm lấy thông tin nhà cung cấp theo mã
function getNccTypeID($mancc) {
    $db = connect();
    $sql = "SELECT * FROM ad_nhacc WHERE mancc = :mancc";
    $stm = $db->prepare($sql);
    $stm->bindParam(':mancc', $mancc);
    $stm->execute();
    return $stm->fetch(PDO::FETCH_ASSOC);
}

// Hàm lấy toàn bộ nhà cung cấp
function getNccType() {
    $db = connect();
    $sql = "SELECT * FROM ad_nhacc";
    $stm = $db->prepare($sql);
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_ASSOC);
}

// Hàm xóa nhà cung cấp
function deleteNccType($mancc) {
    $db = connect();
    $sql = "DELETE FROM ad_nhacc WHERE mancc = :mancc";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':mancc', $mancc);
    $stmt->execute();
}

// Hàm cập nhật thông tin nhà cung cấp
function UpdateadNccType($mancc, $tenncc, $thongtinncc, $hinhanh) {
    $db = connect();
    $sql = "UPDATE ad_nhacc SET tenncc = :tenncc, thongtinncc = :thongtinncc, hinhanh = :hinhanh 
            WHERE mancc = :mancc";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':mancc', $mancc);
    $stmt->bindParam(':tenncc', $tenncc);
    $stmt->bindParam(':thongtinncc', $thongtinncc);
    $stmt->bindParam(':hinhanh', $hinhanh);
    $stmt->execute();
}

// Hàm tự sinh mã nhà cung cấp với định dạng NCC001, NCC002, ...
function generateNccCode(){
    $db = connect();
    $sql = "SELECT MAX(mancc) as max_code FROM ad_nhacc";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $max_code = $row['max_code'];
    if($max_code){
         // Giả sử mã có dạng "NCC" + số (3 chữ số)
         $num = intval(substr($max_code, 3));
         $new_num = $num + 1;
         $new_code = "NCC" . str_pad($new_num, 3, "0", STR_PAD_LEFT);
    } else {
         $new_code = "NCC001";
    }
    return $new_code;
}
?>
