<?php
session_start();  // Bắt đầu phiên làm việc để lưu trữ thông tin người dùng

require_once __DIR__ . "/../DB.php"; // Đường dẫn chính xác đến DB.php
$conn = connect(); // Kết nối đến CSDL

// Kiểm tra nếu form đã được gửi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'], $_POST['password'])) {
        // Lấy thông tin từ form
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        // Câu lệnh SQL để kiểm tra thông tin đăng nhập
        $sql = "SELECT user_id, username, password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra nếu tìm thấy người dùng
        if ($user) {
            // Kiểm tra mật khẩu có khớp không
            if (password_verify($password, $user['password'])) {
                // Mật khẩu đúng, lưu thông tin người dùng vào session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];

                // Chuyển hướng người dùng về trang chủ
                header("Location: new.php");
                exit;
            } else {
                echo "Mật khẩu không đúng!";
            }
        } else {
            echo "Tên đăng nhập không tồn tại!";
        }
    } else {
        echo "Vui lòng nhập tên đăng nhập và mật khẩu!";
    }
} else {
    echo "Yêu cầu không hợp lệ.";
}
?>
