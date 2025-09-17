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
        <a href="new.php">
            <img src="assets/images/logo.png" alt="Phone Shop Logo" class="logo">
        </a>
        <h1>Phone Shop - Đăng ký</h1>
        
<form action="register_process.php" method="post">
    <input type="varchar(50)" name="username" placeholder="Tên đăng nhập" required>
    <input type="password" name="password" placeholder="Mật khẩu" required>
    <input type="varchar(100)" name="email" placeholder="Email" required>
    <input type="varchar(15)" name="phone" placeholder="Số điện thoại" required>
    <input type="varchar(25)" name="address" placeholder="Địa chỉ" required>

    <button type="submit">Đăng ký</button>
</form>
    </header>
<?php include '../phone_shop/includes/footer.php'; ?>
