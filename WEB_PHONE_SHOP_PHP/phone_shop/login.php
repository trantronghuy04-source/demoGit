<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Shop</title>
    <link rel="stylesheet" href="../phone_shop/assets/css/style.css">
</head>
<body>
    <header>
        <!-- Chèn logo vào đây -->
        <a href="index.php">
            <img src="assets/images/logo.png" alt="Phone Shop Logo" class="logo">
        </a>
        <h1>Phone Shop - Đăng nhập</h1>
<form action="login_process.php" method="post">
    <input type="varchar(50)" name="username" placeholder="Tên đăng nhập" required>
    <input type="password" name="password" placeholder="Mật khẩu" required>
    <button type="submit">Đăng nhập</button>
        </form>
    <!-- Thêm dòng thông báo cho người dùng chưa có tài khoản -->
    <p>Nếu bạn chưa có tài khoản, <a href="register.php">Đăng ký ngay</a>.</p>
    </header>
<?php include '../phone_shop/includes/footer.php'; ?>
