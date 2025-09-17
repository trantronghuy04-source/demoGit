<?php
require_once "DB.php";

// Thêm mới loại sản phẩm
function insertAdProductType($ma_loaisp, $ten_loaisp, $mota_loaisp){
    $db = connect();
    $sql = "INSERT INTO ad_producttype(ma_loaisp, ten_loaisp, mota_loaisp) 
            VALUES ('$ma_loaisp', '$ten_loaisp', '$mota_loaisp')";
    try {
         $db->exec($sql);
         echo "Bạn lưu thành công";
    } catch(PDOException $e) {
         echo "Lỗi khi lưu: " . $e->getMessage();
    }
}

// Lấy dữ liệu của một loại sản phẩm theo mã
function getProductTypeID($ma_loaisp){
    $db = connect();
    $sql = "SELECT * FROM ad_producttype WHERE ma_loaisp='$ma_loaisp'";
    $stm = $db->prepare($sql);
    $stm->execute();
    $productTypeID = $stm->fetch();
    return $productTypeID;
}

// Lấy toàn bộ loại sản phẩm
function getProductType(){
    $db = connect();
    $sql = "SELECT * FROM ad_producttype";
    $stm = $db->prepare($sql);
    $stm->execute();
    $productType = $stm->fetchAll();
    return $productType;
}

// Xóa loại sản phẩm theo mã
function deleteProductType($ma_loaisp){
    $db = connect();
    $sql = "DELETE FROM ad_producttype WHERE ma_loaisp='$ma_loaisp'";
    try {
         $db->exec($sql);
         // Bạn có thể hiển thị thông báo thành công nếu cần
    } catch(PDOException $e) {
         echo "Lỗi khi xóa: " . $e->getMessage();
    }
}

// Cập nhật loại sản phẩm
function UpdateadProductType($ma_loaisp, $ten_loaisp, $mota_loaisp){
    $db = connect();
    $sql = "UPDATE ad_producttype SET ten_loaisp='$ten_loaisp', mota_loaisp='$mota_loaisp'
            WHERE ma_loaisp='$ma_loaisp'";
    $db->exec($sql);
}

// Tự sinh mã loại sản phẩm theo định dạng LT001, LT002, ...
function generateProductTypeID(){
    $db = connect();
    $sql = "SELECT MAX(ma_loaisp) as max_id FROM ad_producttype";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $max_id = $row['max_id'];
    if($max_id){
         // Giả sử mã có dạng "LT" + số (3 chữ số)
         $num = intval(substr($max_id, 2));
         $new_num = $num + 1;
         $new_id = "LT" . str_pad($new_num, 3, "0", STR_PAD_LEFT);
    } else {
         $new_id = "LT001";
    }
    return $new_id;
}
?>
