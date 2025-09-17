package models;

import java.io.Serializable;

public class DoUong implements Serializable {
    private String maDoUong;
    private String tenDoUong;
    private String moTa;
    private double gia;
    private String loaiDoUong;
    
    public DoUong(String maDoUong, String tenDoUong, String moTa, double gia, String loaiDoUong) {
        this.maDoUong = maDoUong;
        this.tenDoUong = tenDoUong;
        this.moTa = moTa;
        this.gia = gia;
        this.loaiDoUong = loaiDoUong;
    }
    
    public String getMaDoUong() { return maDoUong; }
    public String getTenDoUong() { return tenDoUong; }
    public String getMoTa() { return moTa; }
    public double getGia() { return gia; }
    public String getLoaiDoUong() { return loaiDoUong; }
    
    public void setTenDoUong(String tenDoUong) { this.tenDoUong = tenDoUong; }
    public void setMoTa(String moTa) { this.moTa = moTa; }
    public void setGia(double gia) { this.gia = gia; }
    public void setLoaiDoUong(String loaiDoUong) { this.loaiDoUong = loaiDoUong; }
    
    @Override
    public String toString() {
        return "Mã DU: " + maDoUong + ", Tên DU: " + tenDoUong + ", Mô tả: " + moTa 
            + ", Giá: " + gia + ", Loại: " + loaiDoUong;
    }
}
