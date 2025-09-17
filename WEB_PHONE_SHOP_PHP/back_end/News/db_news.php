<?php
// db_news.php

// Hàm kết nối cơ sở dữ liệu


// Lấy danh sách người dùng từ bảng users
function getAllUsers() {
    $db = connect();
    $sql = "SELECT * FROM users";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Thêm mới tin tức hoặc phản hồi
function insertNews($user_id, $title, $content, $parent_news_id = NULL) {
    $db = connect();
    $sqlInsert = "INSERT INTO news (user_id, title, content, parent_news_id) 
                  VALUES (:user_id, :title, :content, :parent_news_id)";
    $stmtInsert = $db->prepare($sqlInsert);
    $stmtInsert->bindParam(':user_id', $user_id);
    $stmtInsert->bindParam(':title', $title);
    $stmtInsert->bindParam(':content', $content);
    $stmtInsert->bindParam(':parent_news_id', $parent_news_id);
    try {
        $stmtInsert->execute();
        return true;
    } catch(PDOException $e) {
        error_log("Insert news error: " . $e->getMessage());
        return "Lỗi khi thêm tin tức: " . $e->getMessage();
    }
}

// Lấy danh sách tin tức (bao gồm cả phản hồi) theo thứ tự mới nhất
function getAllNews() {
    $db = connect();
    $sql = "SELECT * FROM news ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Lấy tin tức theo news_id
function getNewsByID($news_id) {
    $db = connect();
    $sql = "SELECT * FROM news WHERE news_id = :news_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':news_id', $news_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Cập nhật tin tức và phản hồi
function updateNews($news_id, $user_id, $title, $content, $feedback = null) {
    $db = connect();
    $sqlUpdate = "UPDATE news 
                  SET user_id = :user_id, title = :title, content = :content, feedback = :feedback 
                  WHERE news_id = :news_id";
    $stmtUpdate = $db->prepare($sqlUpdate);
    $stmtUpdate->bindParam(':user_id', $user_id);
    $stmtUpdate->bindParam(':title', $title);
    $stmtUpdate->bindParam(':content', $content);
    $stmtUpdate->bindParam(':feedback', $feedback);
    $stmtUpdate->bindParam(':news_id', $news_id);
    try {
        $stmtUpdate->execute();
        return true;
    } catch(PDOException $e) {
        error_log("Update news error: " . $e->getMessage());
        return "Lỗi khi cập nhật tin tức: " . $e->getMessage();
    }
}

// Xóa tin tức
function deleteNews($news_id) {
    $db = connect();
    $sql = "DELETE FROM news WHERE news_id = :news_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':news_id', $news_id);
    try {
        $stmt->execute();
        return true;
    } catch(PDOException $e) {
        error_log("Delete news error: " . $e->getMessage());
        return "Lỗi khi xóa tin tức: " . $e->getMessage();
    }
}

// Thêm mới phản hồi (dành cho người dùng gửi phản hồi)
function insertFeedback($user_id, $title, $content) {
    $db = connect();
    $sqlInsert = "INSERT INTO news (user_id, title, content, parent_news_id) 
                  VALUES (:user_id, :title, :content, NULL)";
    $stmtInsert = $db->prepare($sqlInsert);
    $stmtInsert->bindParam(':user_id', $user_id);
    $stmtInsert->bindParam(':title', $title);
    $stmtInsert->bindParam(':content', $content);
    try {
        $stmtInsert->execute();
        return true;
    } catch(PDOException $e) {
        error_log("Insert feedback error: " . $e->getMessage());
        return "Lỗi khi gửi phản hồi: " . $e->getMessage();
    }
}

// Lấy danh sách phản hồi (tin gốc có parent_news_id NULL)
function getAllFeedback() {
    $db = connect();
    $sql = "SELECT * FROM news WHERE parent_news_id IS NULL ORDER BY created_at DESC";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
