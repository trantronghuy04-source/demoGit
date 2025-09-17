<?php
session_start();
require_once "db_news.php";

// Kiểm tra đăng nhập (có thể thay đổi cách kiểm tra quyền admin nếu cần)
if (!isset($_SESSION['user_id'])) {
    die("Bạn cần đăng nhập để cập nhật tin tức.");
}

$news_id = isset($_GET["id"]) ? $_GET["id"] : '';
if (empty($news_id)) {
    echo "News ID không hợp lệ";
    exit;
}

$newsData = getNewsByID($news_id);
if (!$newsData) {
    echo "Không tìm thấy tin tức";
    exit;
}

$users = getAllUsers();
$error = "";

// Tạo CSRF token nếu chưa có
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token không hợp lệ");
    }
    
    $user_id = $_POST["user_id"];
    $title   = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $feedback = trim($_POST["feedback"]);

    if (empty($user_id) || empty($title) || empty($content)) {
        $error = "Vui lòng điền đầy đủ thông tin.";
    } else {
        $result = updateNews($news_id, $user_id, $title, $content, $feedback);
        if ($result === true) {
            header("Location: Manager.php?action=News");
            exit();
        } else {
            $error = $result;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật tin tức / Phản hồi</title>
    <link rel="stylesheet" href="path/to/your/style.css">
    <style>
        .form-container { max-width: 600px; margin: auto; background: #f7f7f7; padding: 20px; border-radius: 8px; }
        .form-group { margin-bottom: 15px; }
        label { font-weight: bold; }
        input[type="text"], textarea, select { width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
        .submit-btn { padding: 10px 20px; background: orange; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .error { color: red; }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Cập nhật tin tức / Phản hồi</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post" id="update-news-form">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <div class="form-group">
                <label for="news_id">News ID:</label>
                <input type="text" name="news_id" id="news_id" value="<?php echo htmlspecialchars($news_id); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="user_id">Chọn người dùng:</label>
                <select name="user_id" id="user_id" required>
                    <option value="">-- Chọn người dùng --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?php echo htmlspecialchars($user['user_id']); ?>" <?php echo ($user['user_id'] == $newsData["user_id"]) ? "selected" : ""; ?>>
                            <?php echo htmlspecialchars($user['username']) . " (" . htmlspecialchars($user['user_id']) . ")"; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <?php if ($newsData["parent_news_id"] === null): ?>
                    <label for="title">Tiêu đề:</label>
                    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($newsData['title']); ?>" required>
                <?php else: ?>
                    <label for="title">Tiêu đề (Phản hồi):</label>
                    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($newsData['title']); ?>" readonly>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="content">Nội dung:</label>
                <textarea name="content" id="content" rows="5" required><?php echo htmlspecialchars($newsData['content']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="feedback">Phản hồi từ quản trị:</label>
                <textarea name="feedback" id="feedback" rows="3"><?php echo htmlspecialchars($newsData['feedback']); ?></textarea>
            </div>
            <input type="submit" name="btn_submit" value="Cập nhật" class="submit-btn">
        </form>
    </div>
</body>
</html>
