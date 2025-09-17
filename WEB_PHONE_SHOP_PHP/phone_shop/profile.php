<?php
require_once __DIR__ . "/../DB.php"; // Đường dẫn chính xác đến DB.php
include '../phone_shop/includes/header.php';

session_start();

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Chuyển hướng đến trang đăng nhập nếu chưa đăng nhập
    exit();
}

$user_id = $_SESSION['user_id']; // Lấy ID người dùng từ session
$db = connect(); // Kết nối database

// Lấy thông tin hiện tại của người dùng dựa trên cột `id`
$stmt = $db->prepare("SELECT username, email, phone, address FROM users WHERE user_id = :user_id");
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Gán giá trị ID từ session
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $password = $_POST['password'];
    $new_password = $_POST['new_password'];

    // Cập nhật thông tin cá nhân
    $stmt = $db->prepare("UPDATE users SET email = :email, phone = :phone, address = :address WHERE user_id = :user_id");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // Nếu người dùng muốn đổi mật khẩu
    if (!empty($password) && !empty($new_password)) {
        // Kiểm tra mật khẩu hiện tại
        $stmt = $db->prepare("SELECT password FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $stored_password = $stmt->fetchColumn();

        if (password_verify($password, $stored_password)) {
            // Cập nhật mật khẩu mới
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $success_message = "Thông tin đã được cập nhật thành công và mật khẩu đã được thay đổi!";
        } else {
            $error_message = "Mật khẩu hiện tại không đúng!";
        }
    } else {
        $success_message = "Thông tin đã được cập nhật thành công!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chỉnh sửa thông tin cá nhân</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }
        input[type="email"],
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }
        input[type="email"]:focus,
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #007bff;
            outline: none;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            font-size: 16px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            font-size: 14px;
            margin-bottom: 20px;
        }
        .message.success {
            color: green;
        }
        .message.error {
            color: red;
        }
        a {
            display: block;
            text-align: center;
            color: #007bff;
            text-decoration: none;
            margin-top: 20px;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Chỉnh sửa thông tin cá nhân</h2>

        <?php if (isset($success_message)): ?>
            <p class="message success"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <p class="message error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Số điện thoại:</label>
                <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Địa chỉ:</label>
                <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($user['address']); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu hiện tại:</label>
                <input type="password" name="password" id="password">
            </div>

            <div class="form-group">
                <label for="new_password">Mật khẩu mới:</label>
                <input type="password" name="new_password" id="new_password">
            </div>

            <button type="submit">Cập nhật</button>
        </form>

        <a href="logout.php">Đăng xuất</a>
    </div>
</body>
</html>
<?php include __DIR__ . '/includes/footer.php'; ?>

