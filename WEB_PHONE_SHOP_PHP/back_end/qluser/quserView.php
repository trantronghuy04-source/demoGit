<?php
require_once "db_quser.php";
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["btn_submit"])) {
    $username = $_POST["username"];
    $email    = $_POST["email"];
    $phone    = $_POST["phone"];
    $address  = $_POST["address"];
    $password = $_POST["password"];
    $result = insertUser($username, $email, $phone, $address, $password);
    if ($result === true) {
        header("Location: Manager.php?action=QUser&message=added");
        exit();
    } else {
        $message = $result;
    }
}
$users = getAllUsers();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý người dùng</title>
    <style>
        table { width: 100%; border-collapse: collapse; margin: 20px auto; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #007bff; color: white; }
        form.add-user { width: 50%; margin: 20px auto; }
        form.add-user input { width: 100%; padding: 8px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Quản lý người dùng</h2>
    <?php if($message != "") echo "<p>$message</p>"; ?>
    <form method="post" class="add-user" action="Manager.php?action=QUser">
        <h3>Thêm mới người dùng</h3>
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Phone:</label>
        <input type="text" name="phone" required>
        <label>Address:</label>
        <input type="text" name="address" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit" name="btn_submit">Thêm mới</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Tùy chọn</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo htmlspecialchars($user['phone']); ?></td>
                <td><?php echo htmlspecialchars($user['address']); ?></td>
                <td>
                    <a href="Manager.php?action=UpdateQUser&id=<?php echo $user['user_id']; ?>">Cập nhật</a> |
                    <a href="Manager.php?action=DeleteQUser&id=<?php echo $user['user_id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa?');">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
