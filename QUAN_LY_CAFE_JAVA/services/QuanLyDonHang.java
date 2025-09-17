package services;

import models.DonHang;
import java.io.*;
import java.util.ArrayList;

public class QuanLyDonHang {
    private ArrayList<DonHang> danhSach;
    private final String filePath = "donhang.dat";
    
    public QuanLyDonHang() {
        danhSach = new ArrayList<>();
        load();
    }
    
    public void addDonHang(DonHang dh) {
        danhSach.add(dh);
        save();
    }
    
    public void updateTrangThai(int maDonHang, String trangThaiMoi) {
        for (DonHang dh : danhSach) {
            if (dh.getMaDonHang() == maDonHang) {
                dh.setTrangThai(trangThaiMoi);
                break;
            }
        }
        save();
    }
    
    public void removeDonHang(int maDonHang) {
        for (int i = 0; i < danhSach.size(); i++) {
            if (danhSach.get(i).getMaDonHang() == maDonHang) {
                danhSach.remove(i);
                break;
            }
        }
        save();
    }
    
    public ArrayList<DonHang> getDanhSach() {
        return danhSach;
    }
    
    public void save() {
        try (ObjectOutputStream oos = new ObjectOutputStream(new FileOutputStream(filePath))) {
            oos.writeObject(danhSach);
        } catch(IOException e) {
            e.printStackTrace();
        }
    }
    
    public void load() {
        File file = new File(filePath);
        if(file.exists()){
            try (ObjectInputStream ois = new ObjectInputStream(new FileInputStream(filePath))) {
                danhSach = (ArrayList<DonHang>) ois.readObject();
            } catch(Exception e) {
                e.printStackTrace();
            }
        }
    }
}
