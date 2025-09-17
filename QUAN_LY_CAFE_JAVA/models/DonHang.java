package models;

import java.io.Serializable;

public class DonHang implements Serializable {
    private int maDonHang;
    private String tenDangNhap;
    private String thongTinDoUong;
    private double tongTien;
    private String trangThai;
    
    public DonHang(int maDonHang, String tenDangNhap, String thongTinDoUong, double tongTien, String trangThai) {
        this.maDonHang = maDonHang;
        this.tenDangNhap = tenDangNhap;
        this.thongTinDoUong = thongTinDoUong;
        this.tongTien = tongTien;
        this.trangThai = trangThai;
    }
    
    public int getMaDonHang() { return maDonHang; }
    public String getTenDangNhap() { return tenDangNhap; }
    public String getThongTinDoUong() { return thongTinDoUong; }
    public double getTongTien() { return tongTien; }
    public String getTrangThai() { return trangThai; }
    
    public void setTrangThai(String trangThai) { this.trangThai = trangThai; }
    
    @Override
    public String toString() {
        return "Mã ĐH: " + maDonHang + ", Tên DN: " + tenDangNhap + ", Thông tin: " + thongTinDoUong 
            + ", Tổng tiền: " + tongTien + ", Trạng thái: " + trangThai;
    }
}
