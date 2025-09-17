package models;

import java.io.Serializable;

public class NguoiDung implements Serializable {
    private int id;
    private String tenDangNhap;
    private String matKhau;
    private String soDienThoai;
    private String email;
    private String diaChi;
    
    public NguoiDung(int id, String tenDangNhap, String matKhau, String soDienThoai, String email, String diaChi) {
        this.id = id;
        this.tenDangNhap = tenDangNhap;
        this.matKhau = matKhau;
        this.soDienThoai = soDienThoai;
        this.email = email;
        this.diaChi = diaChi;
    }
    
    public int getId() { return id; }
    public String getTenDangNhap() { return tenDangNhap; }
    public String getMatKhau() { return matKhau; }
    public String getSoDienThoai() { return soDienThoai; }
    public String getEmail() { return email; }
    public String getDiaChi() { return diaChi; }
    
    public void setTenDangNhap(String tenDangNhap) { this.tenDangNhap = tenDangNhap; }
    public void setMatKhau(String matKhau) { this.matKhau = matKhau; }
    public void setSoDienThoai(String soDienThoai) { this.soDienThoai = soDienThoai; }
    public void setEmail(String email) { this.email = email; }
    public void setDiaChi(String diaChi) { this.diaChi = diaChi; }
    
    @Override
    public String toString() {
        return "ID: " + id + ", Tên đăng nhập: " + tenDangNhap + ", Mật khẩu: " + matKhau 
            + ", SĐT: " + soDienThoai + ", Email: " + email + ", Địa chỉ: " + diaChi;
    }
}
