package services;

import models.NguoiDung;
import java.io.*;
import java.util.ArrayList;

public class QuanLyNguoiDung {
    private ArrayList<NguoiDung> danhSach;
    private final String filePath = "nguoidung.dat";
    
    public QuanLyNguoiDung() {
        danhSach = new ArrayList<>();
        load();
    }
    
    public void addNguoiDung(NguoiDung nd) {
        danhSach.add(nd);
        save();
    }
    
    public void updateNguoiDung(int id, NguoiDung newND) {
        for (int i = 0; i < danhSach.size(); i++) {
            if (danhSach.get(i).getId() == id) {
                danhSach.set(i, newND);
                save();
                return;
            }
        }
    }
    
    public void removeNguoiDung(int id) {
        for (int i = 0; i < danhSach.size(); i++) {
            if (danhSach.get(i).getId() == id) {
                danhSach.remove(i);
                save();
                return;
            }
        }
    }
    
    public ArrayList<NguoiDung> getDanhSach() {
        return danhSach;
    }
    
    public NguoiDung login(String ten, String matKhau) {
        for (NguoiDung nd : danhSach) {
            if (nd.getTenDangNhap().equals(ten) && nd.getMatKhau().equals(matKhau)) {
                return nd;
            }
        }
        return null;
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
        if (file.exists()) {
            try (ObjectInputStream ois = new ObjectInputStream(new FileInputStream(filePath))) {
                danhSach = (ArrayList<NguoiDung>) ois.readObject();
            } catch(Exception e) {
                e.printStackTrace();
            }
        }
    }
}
