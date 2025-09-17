<?php
require_once "db_quser.php";
if (!isset($_GET['id'])) {
    echo "Người dùng không tồn tại.";
    exit();
}
$user_id = $_GET['id'];
$user = getUserById($user_id);
if (!$user) {
    echo "Không tìm thấy người dùng.";
    exit();
}
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email    = $_POST["email"];
    $phone    = $_POST["phone"];
    $address  = $_POST["address"];
    $password = isset($_POST["password"]) ? trim($_POST["password"]) : "";
    $result = updateUser($user_id, $username, $email, $phone, $address, $password);
    if ($result === true) {
        header("Location: Manager.php?action=QUser&message=update_success");
        exit();
    } else {
        $message = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật người dùng</title>
    <style>
        form { margin: 0 auto; width: 50%; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 5px; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">Cập nhật thông tin người dùng</h2>
    <?php if($message != "") echo "<p>$message</p>"; ?>
    <form method="post" action="Manager.php?action=UpdateQUser&id=<?php echo $user_id; ?>">
        <label>User ID:</label>
        <input type="text" name="user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>" readonly>
        <label>Username:</label>
        <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        <label>Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <label>Phone:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        <label>Address:</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
        <label>Mật khẩu (để trống nếu không thay đổi):</label>
        <input type="password" name="password">
        <button type="submit">Cập nhật</button>
    </form>
</body>
</html>
