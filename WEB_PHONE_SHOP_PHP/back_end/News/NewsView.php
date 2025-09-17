<?php
session_start();
require_once "db_news.php";
$newsList = getAllNews();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách tin tức / Phản hồi</title>
    <link rel="stylesheet" href="path/to/your/style.css">
    <style>
        .container { max-width: 1000px; margin: auto; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 8px; text-align: center; }
        .reply { background: #f9f9f9; }
        .action a { margin: 0 5px; text-decoration: none; color: blue; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Danh sách tin tức / phản hồi</h2>
        <table>
            <tr>
                <th>News ID</th>
                <th>User ID</th>
                <th>Tiêu đề</th>
                <th>Nội dung</th>
                <th>Phản hồi</th>
                <th>Thời gian</th>
                <th>Hành động</th>
            </tr>
            <?php foreach ($newsList as $news): ?>
            <tr <?php if($news['parent_news_id'] != null) echo 'class="reply"'; ?>>
                <td><?php echo htmlspecialchars($news["news_id"]); ?></td>
                <td><?php echo htmlspecialchars($news["user_id"]); ?></td>
                <td><?php echo htmlspecialchars($news["title"]); ?></td>
                <td><?php echo htmlspecialchars($news["content"]); ?></td>
                <td><?php echo htmlspecialchars($news["feedback"]); ?></td>
                <td><?php echo htmlspecialchars($news["created_at"]); ?></td>
                <td class="action">
                <a href="Manager.php?action=UpdateNews&id=<?php echo htmlspecialchars($news["news_id"]); ?>">Sửa</a>
                <a href="Manager.php?action=DeleteNews&id=<?php echo htmlspecialchars($news["news_id"]); ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
