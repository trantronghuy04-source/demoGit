<?php
require_once __DIR__ . "/../DB.php";
$conn = connect(); // Gọi hàm kết nối từ DB.php


// Kiểm tra nếu form đã được gửi và các trường không trống
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username'], $_POST['password'], $_POST['email'], $_POST['phone'], $_POST['address'])) {
        // Lấy thông tin từ form
        $username = trim($_POST['username']);
        $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Mã hóa mật khẩu
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);

        // Kiểm tra nếu các trường quan trọng không bị bỏ trống
        if (empty($username) || empty($password) || empty($email) || empty($phone) || empty($address)) {
            echo "Vui lòng điền đầy đủ thông tin!";
            exit;
        }

        // Câu lệnh SQL để chèn dữ liệu vào bảng `users`
        $sql = "INSERT INTO users (username, password, email, phone, address) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        // Thực thi câu lệnh với dữ liệu
        try {
            $stmt->execute([$username, $password, $email, $phone, $address]);
            // Chuyển hướng đến trang đăng nhập sau khi đăng ký thành công
            header("Location: login.php");
            exit;
        } catch (Exception $e) {
            echo "Lỗi khi thực thi câu lệnh: " . $e->getMessage();
        }
    } else {
        echo "Dữ liệu không hợp lệ!";
    }
} else {
    echo "Yêu cầu không hợp lệ.";
}
?>