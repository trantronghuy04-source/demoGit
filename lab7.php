<?php
trait HeThong {
    protected $username, $password, $soTien;

    public function xacThuc($username, $password) {
        $this->username = $username;
        $this->password = $password;
        if ($username === "admin" && $password === "admin123") {
            echo "<p>🔐 Đăng nhập thành công!</p>";
        } else {
            echo "<p>❌ Sai tên đăng nhập hoặc mật khẩu.</p>";
        }
    }
    public function NapTien($soTien) {
        if ($soTien > 0) {
            $this->soTien += $soTien;
            echo "<p>💰 Nạp tiền thành công trong hệ thống: $soTien VND</p>";
        } else {
            echo "<p>❌ Số tiền nạp không hợp lệ.</p>";
        }
    }
}

trait ThanhToan {
    public function napTien($soTien) {
        if ($soTien > 0) {
            echo "<p>💰 Nạp tiền thành công: $soTien VND</p>";
        } else {
            echo "<p>❌ Số tiền nạp không hợp lệ.</p>";
        }
    }
}

class DangNhap {
    use HeThong, ThanhToan {
        HeThong::NapTien insteadof ThanhToan;
        // Nếu muốn, có thể đổi tên phương thức từ ThanhToan
        ThanhToan::napTien as napTienThanhToan;
    }
}

$obj = new DangNhap();
$obj->xacThuc("admin", "admin123"); // Đăng nhập thành công
$obj->NapTien(50000); // Dùng phương thức từ HeThong
$obj->napTienThanhToan(20000); // Dùng phương thức từ ThanhToan




