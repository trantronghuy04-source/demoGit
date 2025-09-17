package models;

import java.io.Serializable;

public class NhanVien implements Serializable {
    private int maNhanVien;
    private String tenDangNhap;
    private String matKhau;
    private String sdt;
    private String email;
    private String diaChi;

    public NhanVien(int maNhanVien, String tenDangNhap, String matKhau, String sdt, String email, String diaChi) {
        this.maNhanVien = maNhanVien;
        this.tenDangNhap = tenDangNhap;
        this.matKhau = matKhau;
        this.sdt = sdt;
        this.email = email;
        this.diaChi = diaChi;
    }

    public int getMaNhanVien() { return maNhanVien; }
    public String getTenDangNhap() { return tenDangNhap; }
    public String getMatKhau() { return matKhau; }
    public String getSdt() { return sdt; }
    public String getEmail() { return email; }
    public String getDiaChi() { return diaChi; }

    public void setTenDangNhap(String tenDangNhap) { this.tenDangNhap = tenDangNhap; }
    public void setMatKhau(String matKhau) { this.matKhau = matKhau; }
    public void setSdt(String sdt) { this.sdt = sdt; }
    public void setEmail(String email) { this.email = email; }
    public void setDiaChi(String diaChi) { this.diaChi = diaChi; }

    @Override
    public String toString() {
        return "MaNhanVien: " + maNhanVien + ", TenDangNhap: " + tenDangNhap +
               ", SDT: " + sdt + ", Email: " + email + ", DiaChi: " + diaChi;
    }
}
