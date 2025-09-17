package services;

import models.DoUong;
import java.io.*;
import java.util.ArrayList;

public class QuanLyDoUong {
    private ArrayList<DoUong> danhSach;
    private final String filePath = "douong.dat";
    
    public QuanLyDoUong() {
        danhSach = new ArrayList<>();
        load();
    }
    
    public void addDoUong(DoUong du) {
        danhSach.add(du);
        save();
    }
    
    public void updateDoUong(String maDoUong, DoUong duMoi) {
        for (int i = 0; i < danhSach.size(); i++) {
            if (danhSach.get(i).getMaDoUong().equals(maDoUong)) {
                danhSach.set(i, duMoi);
                break;
            }
        }
        save();
    }
    
    public void removeDoUong(String maDoUong) {
        for (int i = 0; i < danhSach.size(); i++) {
            if (danhSach.get(i).getMaDoUong().equals(maDoUong)) {
                danhSach.remove(i);
                break;
            }
        }
        save();
    }
    
    public ArrayList<DoUong> getDanhSach() {
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
                danhSach = (ArrayList<DoUong>) ois.readObject();
            } catch(Exception e) {
                e.printStackTrace();
            }
        }
    }
}
