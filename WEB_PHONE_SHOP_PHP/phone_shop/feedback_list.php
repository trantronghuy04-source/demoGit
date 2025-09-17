<?php
require_once __DIR__ . "/../back_end/News/db_news.php";
$conn = connect();
$newsList = getAllNews();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách Phản hồi</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 20px; color: #333; }
        .feedback-item { background: #fff; border: 1px solid #ccc; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
        .feedback-item strong { color: #555; }
        .feedback-item small { color: #999; }
        .reply { margin-left: 20px; border: 1px dashed #ccc; padding: 8px; margin-top: 8px; }
        .admin-feedback { margin-top: 8px; background: #e7f4e4; padding: 8px; border: 1px solid #9fd89f; }
    </style>
</head>
<body>
    <h2>Danh sách Phản hồi</h2>
    <?php 
    foreach ($newsList as $feedback) {
        // Chỉ hiển thị tin gốc (parent_news_id NULL)
        if ($feedback['parent_news_id'] === NULL) {
            echo '<div class="feedback-item">';
            echo '<strong>Người gửi:</strong> ' . htmlspecialchars($feedback['user_id']) . ' - ' . htmlspecialchars($feedback['title']) . '<br>';
            echo '<strong>Nội dung:</strong> ' . nl2br(htmlspecialchars($feedback['content'])) . '<br>';
            echo '<small>Thời gian: ' . htmlspecialchars($feedback['created_at']) . '</small><br>';
            
            // Hiển thị phản hồi từ quản trị nếu có
            if (!empty($feedback['feedback'])) {
                echo '<div class="admin-feedback">';
                echo '<strong>Phản hồi từ quản trị:</strong> ' . nl2br(htmlspecialchars($feedback['feedback'])) . '<br>';
                echo '</div>';
            }
            
            // Lấy các phản hồi trả lời (reply) cho tin gốc này
            $conn2 = connect();
            $sqlReplies = "SELECT * FROM news WHERE parent_news_id = :parent_id ORDER BY created_at ASC";
            $stmtReplies = $conn2->prepare($sqlReplies);
            $stmtReplies->execute([':parent_id' => $feedback['news_id']]);
            $replies = $stmtReplies->fetchAll(PDO::FETCH_ASSOC);
            if ($replies) {
                foreach ($replies as $reply) {
                    echo '<div class="reply">';
                    echo '<strong>Phản hồi:</strong> ' . nl2br(htmlspecialchars($reply['content'])) . '<br>';
                    echo '<small>Thời gian: ' . htmlspecialchars($reply['created_at']) . '</small>';
                    // Hiển thị phản hồi của quản trị đối với reply nếu có
                    if (!empty($reply['feedback'])) {
                        echo '<div class="admin-feedback">';
                        echo '<strong>Phản hồi từ quản trị:</strong> ' . nl2br(htmlspecialchars($reply['feedback'])) . '<br>';
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }
            echo '</div>';
        }
    }
    ?>
</body>
</html>
