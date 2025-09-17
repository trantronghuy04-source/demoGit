<?php
session_start();
require_once __DIR__ . "/../DB.php"; // File DB.php chứa hàm kết nối cho người dùng
require_once __DIR__ . "/../back_end/News/db_news.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = connect();

// Lấy thông tin người dùng: username, email,...
$user_query = "SELECT username, email FROM users WHERE user_id = :user_id";
$stmt = $conn->prepare($user_query);
$stmt->execute([':user_id' => $user_id]);
$user_info = $stmt->fetch(PDO::FETCH_ASSOC);

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $feedback_title   = trim($_POST['feedback_title']);
    $feedback_message = trim($_POST['feedback_message']);
    
    if (empty($feedback_message)) {
        $error = "Vui lòng nhập nội dung phản hồi.";
    } else {
        // Sử dụng hàm insertFeedback để thêm phản hồi (tin gốc có parent_news_id NULL)
        $result = insertFeedback($user_id, $feedback_title, $feedback_message);
        if ($result === true) {
            $success = "Phản hồi của bạn đã được gửi thành công!";
        } else {
            $error = $result;
        }
    }
}

// Lấy danh sách tin tức (bao gồm cả phản hồi và các reply) để hiển thị cho người dùng
$allNews = getAllNews();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Phản hồi của người dùng</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 20px; color: #333; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; }
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .btn { padding: 10px 20px; background: orange; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .feedback-item { border: 1px solid #ccc; margin-bottom: 15px; padding: 10px; }
        .replies { margin-left: 20px; }
        .reply-item { border: 1px dashed #ccc; margin-bottom: 5px; padding: 5px; }
        .admin-feedback { margin-top: 8px; background: #e7f4e4; padding: 8px; border: 1px solid #9fd89f; }
        .user-info p { margin: 5px 0; }
    </style>
</head>
<body>
    <?php include '../phone_shop/includes/header.php'; ?>
    <div class="container">
        <h2>Phản hồi của bạn</h2>
        <?php if ($error !== ""): ?>
            <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <?php if ($success !== ""): ?>
            <p style="color:green;"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        
        <!-- Thông tin người dùng -->
        <div class="user-info">
            <p><strong>Mã người dùng:</strong> <?php echo htmlspecialchars($user_id); ?></p>
            <p><strong>Họ và tên:</strong> <?php echo htmlspecialchars($user_info['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_info['email']); ?></p>
        </div>
        
        <!-- Form gửi phản hồi -->
        <form method="POST" action="FeedbackView.php">
            <div class="form-group">
                <label for="feedback_title">Tiêu đề phản hồi:</label>
                <input type="text" name="feedback_title" id="feedback_title" placeholder="Nhập tiêu đề phản hồi" required>
            </div>
            <div class="form-group">
                <label for="feedback_message">Nội dung phản hồi:</label>
                <textarea name="feedback_message" id="feedback_message" rows="5" placeholder="Nhập nội dung phản hồi" required></textarea>
            </div>
            <button type="submit" class="btn">Gửi phản hồi</button>
        </form>
        
        <!-- Hiển thị danh sách phản hồi của người dùng -->
        <h3>Danh sách phản hồi và trả lời</h3>
        <?php 
        // Lặp qua danh sách tin tức, chỉ hiển thị tin gốc của người dùng (parent_news_id NULL)
        foreach ($allNews as $news) {
            if ($news['parent_news_id'] === null && $news['user_id'] == $user_id) {
                echo "<div class='feedback-item'>";
                echo "<p><strong>Tiêu đề:</strong> " . htmlspecialchars($news['title']) . "</p>";
                echo "<p><strong>Nội dung:</strong> " . htmlspecialchars($news['content']) . "</p>";
                echo "<p><small><strong>Thời gian:</strong> " . htmlspecialchars($news['created_at']) . "</small></p>";
                
                // Hiển thị phản hồi từ quản trị nếu có
                if (!empty($news['feedback'])) {
                    echo "<p><strong>Phản hồi từ quản trị:</strong> " . nl2br(htmlspecialchars($news['feedback'])) . "</p>";
                }
                
                // Lấy các reply của tin gốc này
                $replies = array_filter($allNews, function($item) use ($news) {
                    return $item['parent_news_id'] == $news['news_id'];
                });
                if (!empty($replies)) {
                    echo "<div class='replies'><h4>Trả lời:</h4>";
                    foreach ($replies as $reply) {
                        echo "<div class='reply-item'>";
                        echo "<p><strong>Tiêu đề:</strong> " . htmlspecialchars($reply['title']) . "</p>";
                        echo "<p><strong>Nội dung:</strong> " . htmlspecialchars($reply['content']) . "</p>";
                        echo "<p><small><strong>Thời gian:</strong> " . htmlspecialchars($reply['created_at']) . "</small></p>";
                        // Hiển thị phản hồi từ quản trị đối với reply nếu có
                        if (!empty($reply['feedback'])) {
                            echo "<p><strong>Phản hồi từ quản trị:</strong> " . nl2br(htmlspecialchars($reply['feedback'])) . "</p>";
                        }
                        echo "</div>";
                    }
                    echo "</div>";
                }
                echo "</div>";
            }
        }
        ?>
    </div>
    <?php include '../phone_shop/includes/footer.php'; ?>
</body>
</html>
