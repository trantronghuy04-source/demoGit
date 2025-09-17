<?php
require_once "DB.php";

function insertProduct(
    $ma_loaisp, $masp, $tensp, $hinhanh, $gianhap, $giaxuat, $makm, 
    $soluong, $mota_sp, $create_date, $mancc, $mausac,$mausac1,$mausac2, $thong_so_ky_thuat, 
    $xuat_su, $featured, $hinhanh1, $hinhanh2, $dungluong, $dungluong1, $dungluong2
) {
    $db = connect();
    // Kiểm tra sản phẩm đã tồn tại chưa
    $check = "SELECT COUNT(*) FROM ad_product WHERE masp = :masp";
    $stm = $db->prepare($check);
    $stm->bindParam(':masp', $masp);
    $stm->execute();
    $count = $stm->fetchColumn();

    if ($count > 0) {
        echo "Sản phẩm đã tồn tại.";
    } else {
        $sql = "INSERT INTO ad_product 
            (ma_loaisp, masp, tensp, hinhanh, gianhap, giaxuat, makm, soluong, mota_sp, create_date, mancc, mausac,mausac1,mausac2, thong_so_ky_thuat, xuat_su, featured, hinhanh1, hinhanh2, dungluong, dungluong1, dungluong2) 
            VALUES (:ma_loaisp, :masp, :tensp, :hinhanh, :gianhap, :giaxuat, :makm, :soluong, :mota_sp, :create_date, :mancc, :mausac,:mausac1,:mausac2, :thong_so_ky_thuat, :xuat_su, :featured, :hinhanh1, :hinhanh2, :dungluong, :dungluong1, :dungluong2)";
        try {
            $stm = $db->prepare($sql);
            $stm->bindParam(':ma_loaisp', $ma_loaisp);
            $stm->bindParam(':masp', $masp);
            $stm->bindParam(':tensp', $tensp);
            $stm->bindParam(':hinhanh', $hinhanh);
            $stm->bindParam(':gianhap', $gianhap);
            $stm->bindParam(':giaxuat', $giaxuat);
            $stm->bindParam(':makm', $makm);
            $stm->bindParam(':soluong', $soluong);
            $stm->bindParam(':mota_sp', $mota_sp);
            $stm->bindParam(':create_date', $create_date);
            $stm->bindParam(':mancc', $mancc);
            $stm->bindParam(':mausac', $mausac);
            $stm->bindParam(':mausac1', $mausac1);
            $stm->bindParam(':mausac2', $mausac2);
            $stm->bindParam(':thong_so_ky_thuat', $thong_so_ky_thuat);
            $stm->bindParam(':xuat_su', $xuat_su);
            $stm->bindParam(':featured', $featured, PDO::PARAM_INT);
            $stm->bindParam(':hinhanh1', $hinhanh1);
            $stm->bindParam(':hinhanh2', $hinhanh2);
            $stm->bindParam(':dungluong', $dungluong);
            $stm->bindParam(':dungluong1', $dungluong1);
            $stm->bindParam(':dungluong2', $dungluong2);
            $stm->execute();
            echo "Sản phẩm đã được thêm thành công.";
        } catch (PDOException $e) {
            echo "Lỗi khi thêm sản phẩm: " . $e->getMessage();
        }
    }
}

function getProduct() {
    $db = connect();
    // Chọn theo thứ tự cố định để đảm bảo mảng kết quả đúng thứ tự
    $sql = "SELECT ma_loaisp, masp, tensp, hinhanh, gianhap, giaxuat, makm, soluong, mota_sp, create_date, mancc, mausac,mausac1,mausac2, thong_so_ky_thuat, xuat_su, featured, hinhanh1, hinhanh2, dungluong, dungluong1, dungluong2 FROM ad_product ORDER BY masp";
    $stm = $db->prepare($sql);
    $stm->execute();
    return $stm->fetchAll();
}

function deleteProduct($masp) {
    $db = connect();
    $sql = "DELETE FROM ad_product WHERE masp = :masp";
    try {
        $stm = $db->prepare($sql);
        $stm->bindParam(':masp', $masp);
        $stm->execute();
        echo "Sản phẩm đã được xóa thành công.";
    } catch (PDOException $e) {
        echo "Lỗi khi xóa sản phẩm: " . $e->getMessage();
    }
}

function updateProduct(
    $ma_loaisp, $masp, $tensp, $hinhanh, $gianhap, $giaxuat, $makm, 
    $soluong, $mota_sp, $create_date, $mancc, $mausac, $mausac1, $mausac2, $thong_so_ky_thuat, 
    $xuat_su, $featured, $hinhanh1, $hinhanh2, $dungluong, $dungluong1, $dungluong2
) {
    $db = connect();
    $sql = "UPDATE ad_product 
            SET ma_loaisp = :ma_loaisp, tensp = :tensp, hinhanh = :hinhanh, gianhap = :gianhap, 
                giaxuat = :giaxuat, makm = :makm, soluong = :soluong, mota_sp = :mota_sp, 
                create_date = :create_date, mancc = :mancc, mausac = :mausac, mausac1 = :mausac1, mausac2 = :mausac2, thong_so_ky_thuat = :thong_so_ky_thuat, 
                xuat_su = :xuat_su, featured = :featured, hinhanh1 = :hinhanh1, hinhanh2 = :hinhanh2,
                dungluong = :dungluong, dungluong1 = :dungluong1, dungluong2 = :dungluong2
            WHERE masp = :masp";
    try {
        $stm = $db->prepare($sql);
        $stm->bindParam(':ma_loaisp', $ma_loaisp);
        $stm->bindParam(':masp', $masp);
        $stm->bindParam(':tensp', $tensp);
        $stm->bindParam(':hinhanh', $hinhanh);
        $stm->bindParam(':gianhap', $gianhap);
        $stm->bindParam(':giaxuat', $giaxuat);
        $stm->bindParam(':makm', $makm);
        $stm->bindParam(':soluong', $soluong);
        $stm->bindParam(':mota_sp', $mota_sp);
        $stm->bindParam(':create_date', $create_date);
        $stm->bindParam(':mancc', $mancc);
        $stm->bindParam(':mausac', $mausac);
        $stm->bindParam(':mausac1', $mausac1);
        $stm->bindParam(':mausac2', $mausac2);
        $stm->bindParam(':thong_so_ky_thuat', $thong_so_ky_thuat);
        $stm->bindParam(':xuat_su', $xuat_su);
        $stm->bindParam(':featured', $featured, PDO::PARAM_INT);
        $stm->bindParam(':hinhanh1', $hinhanh1);
        $stm->bindParam(':hinhanh2', $hinhanh2);
        $stm->bindParam(':dungluong', $dungluong);
        $stm->bindParam(':dungluong1', $dungluong1);
        $stm->bindParam(':dungluong2', $dungluong2);
        $stm->execute();
        echo "Sản phẩm đã được cập nhật thành công.";
    } catch (PDOException $e) {
        echo "Lỗi khi cập nhật sản phẩm: " . $e->getMessage();
    }
}

function getProductID1($id) {
    $db = connect();
    $sql = "SELECT ma_loaisp, masp, tensp, hinhanh, gianhap, giaxuat, makm, soluong, mota_sp, create_date, mancc, mausac, mausac1, mausac2, thong_so_ky_thuat, xuat_su, featured, hinhanh1, hinhanh2, dungluong, dungluong1, dungluong2 FROM ad_product WHERE masp = :id";
    $stm = $db->prepare($sql);
    $stm->bindParam(':id', $id);
    $stm->execute();
    return $stm->fetch(PDO::FETCH_ASSOC);
}

function generateProductCode(){
    $db = connect();
    $sql = "SELECT MAX(masp) as max_code FROM ad_product";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $max_code = $row['max_code'];
    if($max_code){
         $num = intval(substr($max_code, 2));
         $new_num = $num + 1;
         $new_code = "SP" . str_pad($new_num, 3, "0", STR_PAD_LEFT);
    } else {
         $new_code = "SP001";
    }
    return $new_code;
}
// Hàm giảm số lượng sản phẩm dựa vào masp
function reduceProductStock($masp, $quantity) {
    $db = connect();
    // Lấy số lượng hiện tại của sản phẩm
    $sql = "SELECT soluong FROM ad_product WHERE masp = :masp";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':masp', $masp);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        return "Sản phẩm với mã $masp không tồn tại.";
    }
    $currentStock = (int)$row['soluong'];
    $newStock = $currentStock - $quantity;
    if ($newStock < 0) {
        $newStock = 0;
    }
    
    // Cập nhật số lượng mới
    $updateSql = "UPDATE ad_product SET soluong = :newStock WHERE masp = :masp";
    $updateStmt = $db->prepare($updateSql);
    $updateStmt->bindParam(':newStock', $newStock, PDO::PARAM_INT);
    $updateStmt->bindParam(':masp', $masp);
    try {
         $updateStmt->execute();
         if ($updateStmt->rowCount() == 0) {
             return "Không cập nhật được số lượng sản phẩm với mã: $masp.";
         }
         return true;
    } catch(PDOException $e) {
         return "Lỗi khi cập nhật số lượng sản phẩm: " . $e->getMessage();
    }
}
?>
