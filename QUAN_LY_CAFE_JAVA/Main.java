import models.NguoiDung;
import models.DonHang;
import models.DoUong;
import services.QuanLyNguoiDung;
import services.QuanLyDonHang;
import services.QuanLyDoUong;
import javax.swing.*;
import javax.swing.table.DefaultTableModel;
import javax.swing.table.TableCellRenderer;
import java.awt.Component;
import java.util.ArrayList;
import java.io.*;

public class Main {
    private static QuanLyNguoiDung qlND = new QuanLyNguoiDung();
    private static QuanLyDonHang qlDH = new QuanLyDonHang();
    private static QuanLyDoUong qlDU = new QuanLyDoUong();

    // Thông tin đăng nhập admin cố định
    private static final String ADMIN_USERNAME = "admin";
    private static final String ADMIN_PASSWORD = "admin123";

    // Biến tự sinh mã cho khách hàng và đồ uống
    private static int autoUserId = 1;
    private static int autoMaDoUong = 1;

    // Giỏ hàng cho khách hàng: mỗi đơn hàng trong giỏ hàng là 1 sản phẩm với số lượng mua
    private static ArrayList<DonHang> cart = new ArrayList<>();

    // Doanh thu tích lũy
    private static double doanhThu = 0.0;

    // Đường dẫn file lưu giỏ hàng và doanh thu
    private static final String CART_FILE = "cart.dat";
    private static final String DOANHTHU_FILE = "doanhthu.dat";

    public static void main(String[] args) {
        loadCart();
        loadDoanhThu();
        updateAutoMaDoUong();
        
        while (true) {
            String input = JOptionPane.showInputDialog(
                "========== CHỌN CHỨC NĂNG ==========\n" +
                "1. Admin\n" +
                "2. Khách hàng\n" +
                "0. Thoát\n" +
                "Nhập lựa chọn:");
            int choice = Integer.parseInt(input);
            switch (choice) {
                case 1:
                    adminFlow();
                    break;
                case 2:
                    userFlow();
                    break;
                case 0:
                    JOptionPane.showMessageDialog(null, "Thoát chương trình.");
                    System.exit(0);
                    break;
                default:
                    JOptionPane.showMessageDialog(null, "Lựa chọn không hợp lệ.");
            }
        }
    }
    
    // Cập nhật autoMaDoUong dựa trên mã đồ uống đã có (định dạng "DUxxx")
    private static void updateAutoMaDoUong() {
        ArrayList<DoUong> list = qlDU.getDanhSach();
        int max = 0;
        for (DoUong du : list) {
            String code = du.getMaDoUong();
            if (code.startsWith("DU")) {
                try {
                    int num = Integer.parseInt(code.substring(2));
                    if (num > max) {
                        max = num;
                    }
                } catch (NumberFormatException e) {
                    // Bỏ qua nếu không parse được
                }
            }
        }
        autoMaDoUong = max + 1;
    }

    // ---------------- ADMIN FLOW ----------------
    private static void adminFlow() {
        String username = JOptionPane.showInputDialog("---------- ĐĂNG NHẬP ADMIN ----------\nNhập tên đăng nhập:");
        String password = JOptionPane.showInputDialog("Nhập mật khẩu:");
        if (username.equals(ADMIN_USERNAME) && password.equals(ADMIN_PASSWORD)) {
            JOptionPane.showMessageDialog(null, "Đăng nhập admin thành công!");
            adminMenu();
        } else {
            JOptionPane.showMessageDialog(null, "Đăng nhập thất bại. Quay lại menu chính.");
        }
    }

    private static void adminMenu() {
        while (true) {
            String input = JOptionPane.showInputDialog(
                "\n---------- MENU ADMIN ----------\n" +
                "1. Quản lý người dùng\n" +
                "2. Quản lý đơn hàng\n" +
                "3. Quản lý đồ uống\n" +
                "0. Đăng xuất\n" +
                "Nhập lựa chọn:");
            int choice = Integer.parseInt(input);
            switch (choice) {
                case 1:
                    menuNguoiDung();
                    break;
                case 2:
                    menuDonHang();
                    break;
                case 3:
                    menuDoUong();
                    break;
                case 0:
                    JOptionPane.showMessageDialog(null, "Đăng xuất admin.");
                    return;
                default:
                    JOptionPane.showMessageDialog(null, "Lựa chọn không hợp lệ.");
            }
        }
    }

    // --------------- MENU NGƯỜI DÙNG (ADMIN) ---------------
    private static void menuNguoiDung() {
        while (true) {
            String input = JOptionPane.showInputDialog(
                "\n-------- MENU QUẢN LÝ NGƯỜI DÙNG --------\n" +
                "1. Thêm người dùng\n" +
                "2. Hiển thị danh sách người dùng\n" +
                "3. Sửa người dùng\n" +
                "4. Xóa người dùng\n" +
                "0. Quay lại\n" +
                "Nhập lựa chọn:");
            int choice = Integer.parseInt(input);
            switch (choice) {
                case 1:
                    addNguoiDungAdmin();
                    break;
                case 2:
                    showNguoiDungTable();
                    break;
                case 3:
                    updateNguoiDungAdmin();
                    break;
                case 4:
                    deleteNguoiDungAdmin();
                    break;
                case 0:
                    return;
                default:
                    JOptionPane.showMessageDialog(null, "Lựa chọn không hợp lệ.");
            }
        }
    }

    private static void addNguoiDungAdmin() {
        int id = Integer.parseInt(JOptionPane.showInputDialog("Nhập ID:"));
        String ten = JOptionPane.showInputDialog("Nhập tên đăng nhập:");
        String mk = JOptionPane.showInputDialog("Nhập mật khẩu:");
        String sdt = JOptionPane.showInputDialog("Nhập số điện thoại:");
        String email = JOptionPane.showInputDialog("Nhập email:");
        String diaChi = JOptionPane.showInputDialog("Nhập địa chỉ:");
        NguoiDung nd = new NguoiDung(id, ten, mk, sdt, email, diaChi);
        qlND.addNguoiDung(nd);
        JOptionPane.showMessageDialog(null, "Thêm người dùng thành công!");
    }

    private static void updateNguoiDungAdmin() {
        showNguoiDungTable();
        int id = Integer.parseInt(JOptionPane.showInputDialog("Nhập ID người dùng cần sửa:"));
        NguoiDung oldND = null;
        for (NguoiDung nd : qlND.getDanhSach()) {
            if (nd.getId() == id) {
                oldND = nd;
                break;
            }
        }
        if (oldND == null) {
            JOptionPane.showMessageDialog(null, "Không tìm thấy người dùng có ID = " + id);
            return;
        }
        String ten = JOptionPane.showInputDialog("Nhập tên đăng nhập mới (Nhấn Enter để giữ nguyên):");
        if (ten.isEmpty()) ten = oldND.getTenDangNhap();
        String mk = JOptionPane.showInputDialog("Nhập mật khẩu mới (Nhấn Enter để giữ nguyên):");
        if (mk.isEmpty()) mk = oldND.getMatKhau();
        String sdt = JOptionPane.showInputDialog("Nhập số điện thoại mới (Nhấn Enter để giữ nguyên):");
        if (sdt.isEmpty()) sdt = oldND.getSoDienThoai();
        String email = JOptionPane.showInputDialog("Nhập email mới (Nhấn Enter để giữ nguyên):");
        if (email.isEmpty()) email = oldND.getEmail();
        String diaChi = JOptionPane.showInputDialog("Nhập địa chỉ mới (Nhấn Enter để giữ nguyên):");
        if (diaChi.isEmpty()) diaChi = oldND.getDiaChi();
        NguoiDung newND = new NguoiDung(id, ten, mk, sdt, email, diaChi);
        qlND.updateNguoiDung(id, newND);
        JOptionPane.showMessageDialog(null, "Cập nhật người dùng thành công!");
    }

    private static void deleteNguoiDungAdmin() {
        showNguoiDungTable();
        int id = Integer.parseInt(JOptionPane.showInputDialog("Nhập ID người dùng cần xóa:"));
        qlND.removeNguoiDung(id);
        JOptionPane.showMessageDialog(null, "Xóa người dùng thành công!");
    }

    private static void showNguoiDungTable() {
    // Yêu cầu người dùng nhập từ khóa tìm kiếm (để trống nếu muốn hiển thị tất cả)
    String searchKeyword = JOptionPane.showInputDialog(null,
            "Nhập từ khóa tìm kiếm (để trống nếu muốn xem tất cả):",
            "Tìm kiếm người dùng", JOptionPane.QUESTION_MESSAGE);
    
    ArrayList<NguoiDung> list = qlND.getDanhSach();
    ArrayList<NguoiDung> filteredList = new ArrayList<>();
    
    // Nếu người dùng nhập từ khóa, tiến hành lọc danh sách
    if (searchKeyword != null && !searchKeyword.trim().isEmpty()) {
        String keywordLower = searchKeyword.toLowerCase();
        for (NguoiDung nd : list) {
            // Lọc theo tên đăng nhập, email, địa chỉ hoặc ID
            if (String.valueOf(nd.getId()).contains(keywordLower)
                    || nd.getTenDangNhap().toLowerCase().contains(keywordLower)
                    || nd.getEmail().toLowerCase().contains(keywordLower)
                    || nd.getDiaChi().toLowerCase().contains(keywordLower)) {
                filteredList.add(nd);
            }
        }
    } else {
        filteredList = list; // Nếu không nhập từ khóa, hiển thị toàn bộ danh sách
    }
    
    String[] columnNames = {"ID", "Tên DN", "Mật khẩu", "SĐT", "Email", "Địa chỉ"};
    Object[][] data = new Object[filteredList.size()][columnNames.length];
    for (int i = 0; i < filteredList.size(); i++) {
        NguoiDung nd = filteredList.get(i);
        data[i][0] = nd.getId();
        data[i][1] = nd.getTenDangNhap();
        data[i][2] = nd.getMatKhau();
        data[i][3] = nd.getSoDienThoai();
        data[i][4] = nd.getEmail();
        data[i][5] = nd.getDiaChi();
    }
    
    JTable table = new JTable(new DefaultTableModel(data, columnNames));
    table.setFillsViewportHeight(true);
    JScrollPane scrollPane = new JScrollPane(table);
    JOptionPane.showMessageDialog(null, scrollPane, "Danh sách người dùng", JOptionPane.INFORMATION_MESSAGE);
}


    // --------------- MENU ĐƠN HÀNG (ADMIN) ---------------
    private static void menuDonHang() {
        while (true) {
            String input = JOptionPane.showInputDialog(
                "\n-------- MENU ĐƠN HÀNG --------\n" +
                "1. Thêm đơn hàng\n" +
                "2. Cập nhật trạng thái đơn hàng\n" +
                "3. Hiển thị danh sách đơn hàng\n" +
                "4. Xác nhận đơn hàng (Chuyển từ 'Chờ xác nhận của admin' sang 'Đang giao hàng')\n" +
                "5. Xem doanh thu\n" +
                "6. Xóa đơn hàng\n" +
                "0. Quay lại\n" +
                "Nhập lựa chọn:");
            int choice = Integer.parseInt(input);
            switch (choice) {
                case 1:
                    themDonHang();
                    break;
                case 2:
                    capNhatTrangThai();
                    break;
                case 3:
                    showDonHangTable();
                    break;
                case 4:
                    xacNhanDonHang();
                    break;
                case 5:
                    showDoanhThuTable();
                    break;
                case 6:
                    showDonHangTable();
                    xoaDonHangAdmin();
                    break;
                case 0:
                    return;
                default:
                    JOptionPane.showMessageDialog(null, "Lựa chọn không hợp lệ.");
            }
        }
    }

    private static void themDonHang() {
        // Hiển thị danh sách sản phẩm để admin chọn
        showDoUongTable();

        String maDU = JOptionPane.showInputDialog("Nhập mã đồ uống muốn thêm vào đơn hàng:");
        DoUong duocChon = null;
        for (DoUong du : qlDU.getDanhSach()) {
            if (du.getMaDoUong().equals(maDU)) {
                duocChon = du;
                break;
            }
        }
        if (duocChon == null) {
            JOptionPane.showMessageDialog(null, "Không tìm thấy đồ uống với mã này.");
            return;
        }

        String tenDN = JOptionPane.showInputDialog("Nhập tên đăng nhập khách hàng:");
        int soLuong = Integer.parseInt(JOptionPane.showInputDialog("Nhập số lượng:"));
        double tongTien = duocChon.getGia() * soLuong;
        String thongTin = duocChon.getTenDoUong() + " - " + soLuong + " ly";

        String[] trangThaiOptions = {"Chờ xác nhận của admin - Thanh toán khi nhận hàng", "Chờ xác nhận của admin - Chuyển khoản"};
        String trangThai = (String) JOptionPane.showInputDialog(null, "Chọn trạng thái thanh toán:", "Trạng thái đơn hàng", JOptionPane.QUESTION_MESSAGE, null, trangThaiOptions, trangThaiOptions[0]);

        DonHang dh = new DonHang((int) (Math.random() * 1000), tenDN, thongTin, tongTien, trangThai);
        qlDH.addDonHang(dh);
        JOptionPane.showMessageDialog(null, "Thêm đơn hàng thành công!");
    }

    private static void capNhatTrangThai() {
        int maDH = Integer.parseInt(JOptionPane.showInputDialog("Nhập mã đơn hàng cần cập nhật:"));
        String trangThaiMoi = JOptionPane.showInputDialog("Nhập trạng thái mới:");
        qlDH.updateTrangThai(maDH, trangThaiMoi);
        JOptionPane.showMessageDialog(null, "Cập nhật trạng thái thành công!");
    }

    private static void showDonHangTable() {
    String[] columnNames = {"Mã ĐH", "Tên DN", "Thông tin", "Tổng tiền", "Trạng thái"};
    ArrayList<DonHang> list = qlDH.getDanhSach();
    Object[][] data = new Object[list.size()][columnNames.length];
    for (int i = 0; i < list.size(); i++) {
        DonHang dh = list.get(i);
        data[i][0] = dh.getMaDonHang();
        data[i][1] = dh.getTenDangNhap();
        data[i][2] = dh.getThongTinDoUong();
        data[i][3] = dh.getTongTien();
        data[i][4] = dh.getTrangThai();
    }
    DefaultTableModel model = new DefaultTableModel(data, columnNames);
    JTable table = new JTable(model);
    table.setFillsViewportHeight(true);
    // Sử dụng cell renderer để hiển thị thông tin đơn hàng dạng đa dòng
    table.getColumnModel().getColumn(2).setCellRenderer(new MultiLineCellRenderer());
    JScrollPane scrollPane = new JScrollPane(table);
    JOptionPane.showMessageDialog(null, scrollPane, "Danh sách đơn hàng", JOptionPane.INFORMATION_MESSAGE);
}


    // Ở admin: xác nhận đơn hàng, chuyển trạng thái sang "Đang giao hàng - ..." dựa trên phương thức thanh toán
    private static void xacNhanDonHang() {
        showDonHangTable();
        int maDH = Integer.parseInt(JOptionPane.showInputDialog("Nhập mã đơn hàng cần xác nhận:"));
        ArrayList<DonHang> ds = qlDH.getDanhSach();
        DonHang donHangCanXacNhan = null;
        for (DonHang dh : ds) {
            if (dh.getMaDonHang() == maDH && dh.getTrangThai().startsWith("Chờ xác nhận của admin")) {
                donHangCanXacNhan = dh;
                break;
            }
        }
        if (donHangCanXacNhan != null) {
            if (donHangCanXacNhan.getTrangThai().contains("Chuyển khoản")) {
                qlDH.updateTrangThai(maDH, "Đang giao hàng - Chuyển khoản");
            } else if (donHangCanXacNhan.getTrangThai().contains("Thanh toán khi nhận hàng")) {
                qlDH.updateTrangThai(maDH, "Đang giao hàng - Thanh toán khi nhận hàng");
            }
            JOptionPane.showMessageDialog(null, "Đơn hàng " + maDH + " đã được xác nhận và đang giao hàng.");
        } else {
            JOptionPane.showMessageDialog(null, "Không tìm thấy đơn hàng cần xác nhận.");
        }
    }
    
    private static void xoaDonHangAdmin() {
        int maDH = Integer.parseInt(JOptionPane.showInputDialog("Nhập mã đơn hàng cần xóa:"));
        qlDH.removeDonHang(maDH);
        JOptionPane.showMessageDialog(null, "Xóa đơn hàng thành công!");
    }

    // Hiển thị doanh thu: nếu không có đơn hàng hoàn thành, thông báo; nếu có, hiển thị bảng
   private static void showDoanhThuTable() {
    ArrayList<DonHang> list = new ArrayList<>();
    double total = 0.0;
    for (DonHang dh : qlDH.getDanhSach()) {
        if (dh.getTrangThai() != null && dh.getTrangThai().trim().equalsIgnoreCase("Hoàn thành")) {
            list.add(dh);
            total += dh.getTongTien();
        }
    }
    if (list.isEmpty()) {
        JOptionPane.showMessageDialog(null, "Chưa có đơn hàng hoàn thành. Tổng doanh thu: " + total);
        return;
    }
    String[] columnNames = {"Tên người dùng", "Thông tin mua hàng", "Tổng tiền"};
    Object[][] data = new Object[list.size()][columnNames.length];
    for (int i = 0; i < list.size(); i++) {
        DonHang dh = list.get(i);
        data[i][0] = dh.getTenDangNhap();
        data[i][1] = dh.getThongTinDoUong();
        data[i][2] = dh.getTongTien();
    }
    DefaultTableModel model = new DefaultTableModel(data, columnNames);
    JTable table = new JTable(model);
    table.setFillsViewportHeight(true);
    // Sử dụng cell renderer để hiển thị thông tin mua hàng dạng đa dòng
    table.getColumnModel().getColumn(1).setCellRenderer(new MultiLineCellRenderer());
    JScrollPane scrollPane = new JScrollPane(table);
    String message = "Tổng doanh thu: " + total;
    JOptionPane.showMessageDialog(null, new Object[]{scrollPane, message}, "Doanh thu", JOptionPane.INFORMATION_MESSAGE);
}


    // --------------- MENU ĐỒ UỐNG (ADMIN) ---------------
    private static void menuDoUong() {
        while (true) {
            String input = JOptionPane.showInputDialog(
                "\n-------- MENU ĐỒ UỐNG --------\n" +
                "1. Thêm đồ uống\n" +
                "2. Sửa đồ uống\n" +
                "3. Xóa đồ uống\n" +
                "4. Hiển thị danh sách đồ uống\n" +
                "0. Quay lại\n" +
                "Nhập lựa chọn:");
            int choice = Integer.parseInt(input);
            switch (choice) {
                case 1:
                    themDoUong();
                    break;
                case 2:
                    showDoUongTable();
                    suaDoUong();
                    break;
                case 3:
                    showDoUongTable();
                    xoaDoUong();
                    break;
                case 4:
                    showDoUongTable();
                    break;
                case 0:
                    return;
                default:
                    JOptionPane.showMessageDialog(null, "Lựa chọn không hợp lệ.");
            }
        }
    }

    private static void showDoUongTable() {
        String[] columnNames = {"Mã DU", "Tên DU", "Mô tả", "Giá", "Loại DU"};
        ArrayList<DoUong> list = qlDU.getDanhSach();
        Object[][] data = new Object[list.size()][columnNames.length];
        for (int i = 0; i < list.size(); i++) {
            DoUong du = list.get(i);
            data[i][0] = du.getMaDoUong();
            data[i][1] = du.getTenDoUong();
            data[i][2] = du.getMoTa();
            data[i][3] = du.getGia();
            data[i][4] = du.getLoaiDoUong();
        }
        JTable table = new JTable(new DefaultTableModel(data, columnNames));
        table.setFillsViewportHeight(true);
        JScrollPane scrollPane = new JScrollPane(table);
        JOptionPane.showMessageDialog(null, scrollPane, "Danh sách đồ uống", JOptionPane.INFORMATION_MESSAGE);
    }

    private static void themDoUong() {
        String autoMa = String.format("DU%03d", autoMaDoUong++);
        String ten = JOptionPane.showInputDialog("Nhập tên đồ uống:");
        String moTa = JOptionPane.showInputDialog("Nhập mô tả:");
        double gia = Double.parseDouble(JOptionPane.showInputDialog("Nhập giá:"));
        String typeInput = JOptionPane.showInputDialog("Chọn loại đồ uống:\n1. Café\n2. Trà sữa\n3. Trà\n4. Nước trái cây\nNhập lựa chọn:");
        int typeChoice = Integer.parseInt(typeInput);
        String loai = "";
        switch (typeChoice) {
            case 1: loai = "Café"; break;
            case 2: loai = "Trà sữa"; break;
            case 3: loai = "Trà"; break;
            case 4: loai = "Nước trái cây"; break;
            default:
                JOptionPane.showMessageDialog(null, "Lựa chọn không hợp lệ. Mặc định chọn Café.");
                loai = "Café";
                break;
        }
        qlDU.addDoUong(new DoUong(autoMa, ten, moTa, gia, loai));
        JOptionPane.showMessageDialog(null, "Thêm đồ uống thành công! Mã đồ uống: " + autoMa);
    }

    private static void suaDoUong() {
        String ma = JOptionPane.showInputDialog("Nhập mã đồ uống cần sửa:");
        DoUong duocChon = null;
        for (DoUong du : qlDU.getDanhSach()) {
            if (du.getMaDoUong().equals(ma)) {
                duocChon = du;
                break;
            }
        }
        if (duocChon == null) {
            JOptionPane.showMessageDialog(null, "Không tìm thấy đồ uống với mã này.");
            return;
        }
        String ten = JOptionPane.showInputDialog("Nhập tên đồ uống mới (nhấn Enter để giữ nguyên):");
        if (ten.isEmpty()) {
            ten = duocChon.getTenDoUong();
        }
        String moTa = JOptionPane.showInputDialog("Nhập mô tả mới (nhấn Enter để giữ nguyên):");
        if (moTa.isEmpty()) {
            moTa = duocChon.getMoTa();
        }
        String giaInput = JOptionPane.showInputDialog("Nhập giá mới (nhấn Enter để giữ nguyên):");
        double gia = giaInput.isEmpty() ? duocChon.getGia() : Double.parseDouble(giaInput);
        String typeInput = JOptionPane.showInputDialog("Chọn loại đồ uống mới (nhấn Enter để giữ nguyên):\n1. Café\n2. Trà sữa\n3. Trà\n4. Nước trái cây");
        String loai;
        if (typeInput.isEmpty()) {
            loai = duocChon.getLoaiDoUong();
        } else {
            int typeChoice = Integer.parseInt(typeInput);
            switch (typeChoice) {
                case 1: loai = "Café"; break;
                case 2: loai = "Trà sữa"; break;
                case 3: loai = "Trà"; break;
                case 4: loai = "Nước trái cây"; break;
                default:
                    JOptionPane.showMessageDialog(null, "Lựa chọn không hợp lệ. Giữ nguyên loại.");
                    loai = duocChon.getLoaiDoUong();
                    break;
            }
        }
        qlDU.updateDoUong(ma, new DoUong(ma, ten, moTa, gia, loai));
        JOptionPane.showMessageDialog(null, "Cập nhật đồ uống thành công!");
    }

    private static void xoaDoUong() {
        String ma = JOptionPane.showInputDialog("Nhập mã đồ uống cần xóa:");
        qlDU.removeDoUong(ma);
        JOptionPane.showMessageDialog(null, "Xóa đồ uống thành công!");
    }

    // --------------- USER FLOW (KHÁCH HÀNG) ---------------
    private static void userFlow() {
        String input = JOptionPane.showInputDialog(
            "\n---------- CHỨC NĂNG KHÁCH HÀNG ----------\n" +
            "1. Đăng ký tài khoản\n" +
            "2. Đăng nhập tài khoản\n" +
            "Nhập lựa chọn:");
        int choice = Integer.parseInt(input);
        NguoiDung currentUser = null;
        if (choice == 1) {
            currentUser = registerNguoiDungCustomer();
            JOptionPane.showMessageDialog(null, "Vui lòng đăng nhập lại.");
            currentUser = loginFlow();
        } else if (choice == 2) {
            currentUser = loginFlow();
        } else {
            JOptionPane.showMessageDialog(null, "Lựa chọn không hợp lệ.");
            return;
        }
        if (currentUser == null) {
            JOptionPane.showMessageDialog(null, "Đăng nhập thất bại. Quay lại menu chính.");
            return;
        }
        userMenu(currentUser);
    }
    private static int generateUniqueUserId() {
        ArrayList<NguoiDung> danhSach = qlND.getDanhSach();
        int maxId = 0;
        for (NguoiDung nd : danhSach) {
            if (nd.getId() > maxId) {
                maxId = nd.getId();
            }
        }
        return maxId + 1;
    }

    private static NguoiDung registerNguoiDungCustomer() {
    int id = generateUniqueUserId();
    String ten = JOptionPane.showInputDialog("Nhập tên đăng nhập:");
    String mk = JOptionPane.showInputDialog("Nhập mật khẩu:");
    String sdt = JOptionPane.showInputDialog("Nhập số điện thoại:");
    String email = JOptionPane.showInputDialog("Nhập email:");
    NguoiDung nd = new NguoiDung(id, ten, mk, sdt, email, "");
    qlND.addNguoiDung(nd);
    JOptionPane.showMessageDialog(null, "Đăng ký thành công! Mã khách hàng: " + id);
    return nd;
}

    private static NguoiDung loginFlow() {
        String ten = JOptionPane.showInputDialog("Nhập tên đăng nhập:");
        String mk = JOptionPane.showInputDialog("Nhập mật khẩu:");
        NguoiDung nd = qlND.login(ten, mk);
        if (nd != null) {
            JOptionPane.showMessageDialog(null, "Đăng nhập thành công!");
            return nd;
        } else {
            JOptionPane.showMessageDialog(null, "Đăng nhập thất bại!");
            return null;
        }
    }

    private static void userMenu(NguoiDung user) {
        while (true) {
            String input = JOptionPane.showInputDialog(
                "\n---------- MENU KHÁCH HÀNG ----------\n" +
                "1. Xem danh sách đồ uống\n" +
                "2. Thêm sản phẩm vào giỏ hàng\n" +
                "3. Xem giỏ hàng\n" +
                "4. Sửa giỏ hàng (Sửa số lượng mua)\n" +
                "5. Xóa sản phẩm khỏi giỏ hàng\n" +
                "6. Thanh toán đơn hàng\n" +
                "7. Xem trạng thái đơn hàng\n" +
                "8. Cập nhập thông tin cá nhân\n" +               
                "0. Đăng xuất\n" +
                "Nhập lựa chọn:");
            int choice = Integer.parseInt(input);
            switch (choice) {
                case 1:
                    showDoUongTable();
                    break;
                case 2:
                    showDoUongTable();
                    addToCart(user);
                    break;
                case 3:
                    showCart();
                    break;
                case 4:
                    updateCartItem();
                    break;
                case 5:
                    removeCartItem();
                    break;
                case 6:
                    thanhToan(user);
                    break;
                case 7:
                    showDonHangTableForUser(user);
                    xemTrangThaiDonHang(user);
                    break;
                case 8:
                
                    suaThongTinCaNhan(user);
                    break;                
                case 0:
                    JOptionPane.showMessageDialog(null, "Đăng xuất tài khoản.");
                    return;
                default:
                    JOptionPane.showMessageDialog(null, "Lựa chọn không hợp lệ.");
            }
        }
    }
    private static void suaThongTinCaNhan(NguoiDung user) {
        // Hiển thị thông tin cá nhân hiện tại
        String thongTinHienTai = "Thông tin cá nhân hiện tại:\n" +
                                 "Tên đăng nhập: " + user.getTenDangNhap() + "\n" +
                                 "Mật khẩu: " + user.getMatKhau() + "\n" +
                                 "Số điện thoại: " + user.getSoDienThoai() + "\n" +
                                 "Email: " + user.getEmail() + "\n" +
                                 "Địa chỉ: " + user.getDiaChi();
        JOptionPane.showMessageDialog(null, thongTinHienTai, "Thông tin cá nhân", JOptionPane.INFORMATION_MESSAGE);

        // Sau khi hiển thị, tiến hành nhập thông tin mới
        String ten = JOptionPane.showInputDialog("Nhập tên đăng nhập mới (Nhấn Enter để giữ nguyên):");
        if (ten.isEmpty()) {
            ten = user.getTenDangNhap();
        }
        String mk = JOptionPane.showInputDialog("Nhập mật khẩu mới (Nhấn Enter để giữ nguyên):");
        if (mk.isEmpty()) {
            mk = user.getMatKhau();
        }
        String sdt = JOptionPane.showInputDialog("Nhập số điện thoại mới (Nhấn Enter để giữ nguyên):");
        if (sdt.isEmpty()) {
            sdt = user.getSoDienThoai();
        }
        String email = JOptionPane.showInputDialog("Nhập email mới (Nhấn Enter để giữ nguyên):");
        if (email.isEmpty()) {
            email = user.getEmail();
        }
        String diaChi = JOptionPane.showInputDialog("Nhập địa chỉ mới (Nhấn Enter để giữ nguyên):");
        if (diaChi.isEmpty()) {
            diaChi = user.getDiaChi();
        }

        // Cập nhật thông tin cho đối tượng hiện tại
        user.setTenDangNhap(ten);
        user.setMatKhau(mk);
        user.setSoDienThoai(sdt);
        user.setEmail(email);
        user.setDiaChi(diaChi);

        // Nếu có phương thức cập nhật trong QuanLyNguoiDung cho user hiện tại thì gọi ở đây
        qlND.updateNguoiDung(user.getId(), user);
        JOptionPane.showMessageDialog(null, "Cập nhật thông tin cá nhân thành công!");
    }

    // Giỏ hàng: mỗi đơn hàng chứa thông tin sản phẩm và số lượng mua
    private static void addToCart(NguoiDung user) {
        String ma = JOptionPane.showInputDialog("Nhập mã đồ uống cần mua:");
        int soLuong = Integer.parseInt(JOptionPane.showInputDialog("Nhập số lượng:"));
        DoUong duocChon = null;
        for (DoUong du : qlDU.getDanhSach()) {
            if (du.getMaDoUong().equals(ma)) {
                duocChon = du;
                break;
            }
        }
        if (duocChon == null) {
            JOptionPane.showMessageDialog(null, "Không tìm thấy đồ uống với mã này.");
            return;
        }
        double tongTien = duocChon.getGia() * soLuong;
        String thongTin = duocChon.getTenDoUong() + " - " + soLuong + " ly";
        DonHang dh = new DonHang((int)(Math.random() * 1000), user.getTenDangNhap(), thongTin, tongTien, "Chưa thanh toán");
        cart.add(dh);
        JOptionPane.showMessageDialog(null, "Đã thêm vào giỏ hàng:\n" + dh);
        saveCart();
    }

    private static void showCart() {
        if (cart.isEmpty()) {
            JOptionPane.showMessageDialog(null, "Giỏ hàng trống.");
            return;
        }
        String[] columnNames = {"Mã ĐH", "Thông tin", "Tổng tiền", "Trạng thái"};
        Object[][] data = new Object[cart.size()][columnNames.length];
        for (int i = 0; i < cart.size(); i++) {
            DonHang dh = cart.get(i);
            data[i][0] = dh.getMaDonHang();
            data[i][1] = dh.getThongTinDoUong();
            data[i][2] = dh.getTongTien();
            data[i][3] = dh.getTrangThai();
        }
        JTable table = new JTable(new DefaultTableModel(data, columnNames));
        table.setFillsViewportHeight(true);
        JScrollPane scrollPane = new JScrollPane(table);
        JOptionPane.showMessageDialog(null, scrollPane, "Giỏ hàng của bạn", JOptionPane.INFORMATION_MESSAGE);
    }

    private static void updateCartItem() {
        if (cart.isEmpty()) {
            JOptionPane.showMessageDialog(null, "Giỏ hàng trống.");
            return;
        }
        showCart();
        int maDH = Integer.parseInt(JOptionPane.showInputDialog("Nhập mã đơn hàng cần sửa số lượng:"));
        DonHang itemToUpdate = null;
        for (DonHang dh : cart) {
            if (dh.getMaDonHang() == maDH) {
                itemToUpdate = dh;
                break;
            }
        }
        if (itemToUpdate == null) {
            JOptionPane.showMessageDialog(null, "Không tìm thấy sản phẩm trong giỏ hàng với mã này.");
            return;
        }
        int newQuantity = Integer.parseInt(JOptionPane.showInputDialog("Nhập số lượng mới:"));
        String[] parts = itemToUpdate.getThongTinDoUong().split(" - ");
        String tenDoUong = parts[0];
        int currentQuantity = Integer.parseInt(parts[1].split(" ")[0]);
        double donGia = itemToUpdate.getTongTien() / currentQuantity;
        double newTotal = donGia * newQuantity;
        String newThongTin = tenDoUong + " - " + newQuantity + " ly";
        DonHang newItem = new DonHang(itemToUpdate.getMaDonHang(), itemToUpdate.getTenDangNhap(), newThongTin, newTotal, "Chưa thanh toán");
        for (int i = 0; i < cart.size(); i++) {
            if (cart.get(i).getMaDonHang() == maDH) {
                cart.set(i, newItem);
                break;
            }
        }
        JOptionPane.showMessageDialog(null, "Cập nhật giỏ hàng thành công!");
        saveCart();
    }

    private static void removeCartItem() {
        if (cart.isEmpty()) {
            JOptionPane.showMessageDialog(null, "Giỏ hàng trống.");
            return;
        }
        showCart();
        int maDH = Integer.parseInt(JOptionPane.showInputDialog("Nhập mã đơn hàng cần xóa khỏi giỏ hàng:"));
        for (int i = 0; i < cart.size(); i++) {
            if (cart.get(i).getMaDonHang() == maDH) {
                cart.remove(i);
                JOptionPane.showMessageDialog(null, "Xóa sản phẩm khỏi giỏ hàng thành công!");
                saveCart();
                return;
            }
        }
        JOptionPane.showMessageDialog(null, "Không tìm thấy sản phẩm với mã này trong giỏ hàng.");
    }

    // Thanh toán giỏ hàng: gộp tất cả sản phẩm trong giỏ thành 1 đơn hàng
    private static void thanhToan(NguoiDung user) {
        if (cart.isEmpty()) {
            JOptionPane.showMessageDialog(null, "Giỏ hàng trống, vui lòng chọn hàng mua trước.");
            return;
        }
        String diaChiGiaoHang = JOptionPane.showInputDialog("Nhập địa chỉ giao hàng:");
        user.setDiaChi(diaChiGiaoHang);
        JOptionPane.showMessageDialog(null, "Thông tin khách hàng:\n" + user);
        String phuongThucInput = JOptionPane.showInputDialog("Chọn phương thức thanh toán cho giỏ hàng:\n1. Thanh toán khi nhận hàng\n2. Chuyển khoản\nNhập lựa chọn:");
        int method = Integer.parseInt(phuongThucInput);
        String phuongThuc = (method == 2) ? "Chuyển khoản" : "Thanh toán khi nhận hàng";
        
        // Gộp thông tin các sản phẩm trong giỏ
        StringBuilder thongTinGop = new StringBuilder();
        double tongTienGop = 0.0;
        for (DonHang dh : cart) {
            // Lưu ý: mỗi sản phẩm được nối vào với ký tự xuống dòng để hiển thị nhiều dòng trong 1 cell
            thongTinGop.append(dh.getThongTinDoUong()).append("\n");
            tongTienGop += dh.getTongTien();
        }

        // Tạo mã đơn hàng duy nhất
        int maDHMoi = (int)(Math.random() * 1000);

        // Tạo đơn hàng gộp
        DonHang donHangGop = new DonHang(maDHMoi, user.getTenDangNhap(), thongTinGop.toString(), tongTienGop, "");
        if (phuongThuc.equals("Chuyển khoản")) {
            doanhThu += tongTienGop;
            donHangGop.setTrangThai("Chờ xác nhận của admin - Chuyển khoản");
        } else {
            donHangGop.setTrangThai("Chờ xác nhận của admin - Thanh toán khi nhận hàng");
        }
        qlDH.addDonHang(donHangGop);

        // Xóa giỏ hàng sau khi thanh toán
        cart.clear();
        saveCart();
        JOptionPane.showMessageDialog(null, "Thanh toán giỏ hàng thành công! Vui lòng chờ admin xác nhận đơn hàng.");
    }

    // Hiển thị danh sách đơn hàng cho khách hàng: mỗi đơn hàng xuất hiện 1 hàng duy nhất với thông tin sản phẩm được hiển thị dưới dạng nhiều dòng
    private static void showDonHangTableForUser(NguoiDung user) {
        String[] columnNames = {"Mã ĐH", "Thông tin sản phẩm", "Tổng tiền", "Trạng thái"};
        ArrayList<DonHang> list = new ArrayList<>();
        for (DonHang dh : qlDH.getDanhSach()) {
            if (dh.getTenDangNhap().equals(user.getTenDangNhap())) {
                list.add(dh);
            }
        }
        Object[][] data = new Object[list.size()][columnNames.length];
        for (int i = 0; i < list.size(); i++) {
            DonHang dh = list.get(i);
            data[i][0] = dh.getMaDonHang();
            // Dữ liệu thông tin sản phẩm chứa "\n" để ngắt dòng
            data[i][1] = dh.getThongTinDoUong();
            data[i][2] = dh.getTongTien();
            data[i][3] = dh.getTrangThai();
        }
        DefaultTableModel model = new DefaultTableModel(data, columnNames);
        JTable table = new JTable(model);
        table.setFillsViewportHeight(true);
        // Sử dụng cell renderer cho cột "Thông tin sản phẩm" để hiển thị text đa dòng
        table.getColumnModel().getColumn(1).setCellRenderer(new MultiLineCellRenderer());
        JScrollPane scrollPane = new JScrollPane(table);
        JOptionPane.showMessageDialog(null, scrollPane, "Danh sách đơn hàng của bạn", JOptionPane.INFORMATION_MESSAGE);
    }

    // Phía khách hàng: xem trạng thái đơn hàng và xác nhận nhận hàng thành công
    private static void xemTrangThaiDonHang(NguoiDung user) {
        showDonHangTableForUser(user);
        ArrayList<DonHang> ds = qlDH.getDanhSach();
        for (DonHang dh : ds) {
            if (dh.getTenDangNhap().equals(user.getTenDangNhap()) && dh.getTrangThai() != null) {
                String state = dh.getTrangThai().trim();
                // Với "Thanh toán khi nhận hàng": doanh thu được cộng khi khách xác nhận đã nhận hàng
                if (state.equalsIgnoreCase("Đang giao hàng - Thanh toán khi nhận hàng")) {
                    String confirm = JOptionPane.showInputDialog("Nhập 'R' nếu bạn đã nhận được hàng cho đơn hàng " + dh.getMaDonHang() + ":");
                    if (confirm != null && confirm.trim().equalsIgnoreCase("R")) {
                        doanhThu += dh.getTongTien();
                        qlDH.updateTrangThai(dh.getMaDonHang(), "Hoàn thành");
                        JOptionPane.showMessageDialog(null, "Cảm ơn bạn đã xác nhận nhận hàng. Đơn hàng đã hoàn thành.");
                        saveDoanhThu();
                    }
                }
                // Với "Chuyển khoản": doanh thu đã được cộng lúc thanh toán, chỉ cập nhật trạng thái
                else if (state.equalsIgnoreCase("Đang giao hàng - Chuyển khoản")) {
                    String confirm = JOptionPane.showInputDialog("Nhập 'R' nếu bạn đã nhận được hàng cho đơn hàng " + dh.getMaDonHang() + ":");
                    if (confirm != null && confirm.trim().equalsIgnoreCase("R")) {
                        qlDH.updateTrangThai(dh.getMaDonHang(), "Hoàn thành");
                        JOptionPane.showMessageDialog(null, "Cảm ơn bạn đã xác nhận nhận hàng. Đơn hàng đã hoàn thành.");
                    }
                }
            }
        }
    }

    // ---------------- Lưu/Tải giỏ hàng và doanh thu ----------------
    private static void saveCart() {
        try (ObjectOutputStream oos = new ObjectOutputStream(new FileOutputStream(CART_FILE))) {
            oos.writeObject(cart);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    private static void loadCart() {
        File file = new File(CART_FILE);
        if (file.exists()) {
            try (ObjectInputStream ois = new ObjectInputStream(new FileInputStream(file))) {
                cart = (ArrayList<DonHang>) ois.readObject();
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

    private static void saveDoanhThu() {
        try (ObjectOutputStream oos = new ObjectOutputStream(new FileOutputStream(DOANHTHU_FILE))) {
            oos.writeDouble(doanhThu);
        } catch (IOException e) {
            e.printStackTrace();
        }
    }

    private static void loadDoanhThu() {
        File file = new File(DOANHTHU_FILE);
        if (file.exists()) {
            try (ObjectInputStream ois = new ObjectInputStream(new FileInputStream(file))) {
                doanhThu = ois.readDouble();
            } catch (Exception e) {
                e.printStackTrace();
            }
        }
    }

    // Cell renderer hỗ trợ hiển thị text đa dòng trong JTable
    static class MultiLineCellRenderer extends JTextArea implements TableCellRenderer {
        public MultiLineCellRenderer() {
            setLineWrap(true);
            setWrapStyleWord(true);
        }
        @Override
        public Component getTableCellRendererComponent(JTable table, Object value,
                boolean isSelected, boolean hasFocus, int row, int column) {
            setText(value == null ? "" : value.toString());
            setSize(table.getColumnModel().getColumn(column).getWidth(), getPreferredSize().height);
            if (table.getRowHeight(row) != getPreferredSize().height) {
                table.setRowHeight(row, getPreferredSize().height);
            }
            return this;
        }
    }
}
