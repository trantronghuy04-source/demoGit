<?php
session_start(); // Bắt đầu session

// Kiểm tra nếu người dùng đã đăng nhập
if (isset($_SESSION['user_id'])) {
    // Xóa toàn bộ dữ liệu session
    session_unset(); // Xóa các biến session
    session_destroy(); // Hủy session
}

// Chuyển hướng về trang login
header("Location: new.php");
exit();
?>
