<?php
function insertNews($tieu_de, $noi_dung, $hinh_anh){
    $db = connect();
    $sql = "INSERT INTO ad_tintuc (tieu_de, noi_dung, hinh_anh) VALUES ('$tieu_de', '$noi_dung', '$hinh_anh')";
    try {
        $db->exec($sql);
        echo "Bạn đã lưu tin tức thành công";
    } catch (PDOException $e) {
        echo "Lỗi khi lưu tin tức: " . $e->getMessage();
    }
}

function getNewsByID($id){
    $db = connect();
    $sql = "SELECT * FROM ad_tintuc WHERE id='$id'";
    $stm = $db->prepare($sql);
    $stm->execute();
    return $stm->fetch();
}

function getAllNews(){
    $db = connect();
    $sql = "SELECT * FROM ad_tintuc";
    $stm = $db->prepare($sql);
    $stm->execute();
    return $stm->fetchAll();
}

function deleteNews($id){
    $db = connect();
    $sql = "DELETE FROM ad_tintuc WHERE id = :id";
    $stm = $db->prepare($sql);
    $stm->bindParam(':id', $id, PDO::PARAM_INT);
    
    try {
        $stm->execute();
        if ($stm->rowCount() > 0) {
            echo "Xóa thành công!";
        } else {
            echo "Không tìm thấy tin tức cần xóa.";
        }
    } catch (PDOException $e) {
        echo "Lỗi khi xóa: " . $e->getMessage();
    }
}


function updateNews($id, $tieu_de, $noi_dung, $hinh_anh){
    $db = connect();
    $sql = "UPDATE ad_tintuc SET tieu_de='$tieu_de', noi_dung='$noi_dung', hinh_anh='$hinh_anh' WHERE id='$id'";
    $db->exec($sql);
}
?>
