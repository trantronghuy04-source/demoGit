-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 17, 2024 lúc 08:56 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `phone_shop`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ad_nhacc`
--

CREATE TABLE `ad_nhacc` (
  `mancc` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tenncc` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `thongtinncc` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hinhanh` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ad_nhacc`
--

INSERT INTO `ad_nhacc` (`mancc`, `tenncc`, `thongtinncc`, `hinhanh`) VALUES
('N1', 'Apple', 'Apple Inc. là một Tập đoàn công nghệ đa quốc gia của Mỹ có trụ sở chính tại Cupertino, California, chuyên Thiết kế, phát triển và bán thiết bị điện tử tiêu dùng, phần mềm máy tính và các dịch vụ trực ', 'download (1).jpg'),
('N2', 'Samsung', 'Samsung là một tập đoàn đa quốc gia của Hàn Quốc có trụ sở chính đặt tại Samsung Town, Seocho, Seoul. Tập đoàn sở hữu rất nhiều công ty con, chuỗi hệ thống bán hàng cùng các văn phòng đại diện trên to', 'images.jpg'),
('N3', 'Nokia Corporation', 'Là tập đoàn viễn thông đa quốc gia có trụ sở tại Keilaniemi, Espoo, Phần Lan. Nokia tập trung vào các sản phẩm viễn thông không dây và cố định, với 129.746 nhân viên chính thức làm việc và bán sản phẩ', 'Nokia_wordmark.svg.png'),
('N4', 'Realme', 'Realme là nhà sản xuất điện thoại thông minh Android Trung Quốc có trụ sở tại Thâm Quyến. Thương hiệu này được chính thức thành lập vào ngày 6 tháng 5 năm 2018, sau khi tách ra khỏi OPPO', 'Realme_logo.png'),
('N5', 'Huawei', 'Huawei được thành lập năm 1987 bởi Nhậm Chính Phi, một cựu kỹ sư của Giải phóng quân Nhân dân Trung Quốc', 'download (2).jpg');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ad_product`
--

CREATE TABLE `ad_product` (
  `ma_loaisp` varchar(50) NOT NULL,
  `masp` varchar(50) NOT NULL,
  `tensp` varchar(50) NOT NULL,
  `hinhanh` varchar(50) NOT NULL,
  `gianhap` int(11) NOT NULL,
  `giaxuat` int(11) NOT NULL,
  `makm` varchar(20) NOT NULL,
  `soluong` int(11) NOT NULL,
  `mota_sp` text NOT NULL,
  `create_date` date NOT NULL,
  `mancc` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ad_product`
--

INSERT INTO `ad_product` (`ma_loaisp`, `masp`, `tensp`, `hinhanh`, `gianhap`, `giaxuat`, `makm`, `soluong`, `mota_sp`, `create_date`, `mancc`) VALUES
('IP', 'IP1', 'Iphone 16 ProMax', 'download.jpg', 34990000, 36990000, 'GIAM10%', 2500, 'Siêu phẩm Apple năm 2024            ', '2024-12-16', 'N1'),
('IP', 'IP2', 'Iphone 12 promax', 'thumb_IP12Pro_1.jpg', 12000000, 13000000, 'GIAM10%', 25, 'Rất tuyệt vời            ', '2024-12-16', 'N1'),
('IP', 'IP3', 'IPhone 14 ProMax', 't_m_18_1_1_3_1.webp', 19990000, 21490000, 'GIAM10%', 350, 'Dynamic Island            ', '2024-12-16', 'N1'),
('NK', 'NK1', 'Nokia 110 4G Pro', 'nokia-110-4g-pro_1__1.webp', 650000, 750000, 'GIAM5%', 250, 'Sản phẩm hỗ trợ 4g của Nokia                                                   ', '2024-12-14', 'N3'),
('NK', 'NK2', 'Nokia 105', 'nokia-105-4g-blue-600x600.jpg', 500000, 650000, 'GIAM5%', 20, 'Siêu phẩm thời xưa            ', '2024-12-16', 'N3'),
('SS', 'SS1', 'Samsung S23 Ultra', '0002550_galaxy-s23-ultra-256gb.jpeg', 18990000, 20990000, 'GIAM7%', 450, 'Ra mắt vào đầu năm 2023                                                                                        ', '2024-12-14', 'N2'),
('SS', 'SS2', 'Samsung S24 Ultra', 'ss-s24-ultra-xam-222.webp', 22390000, 23490000, 'GIAM7%', 200, 'Dòng mới nhất của Samsung            ', '2024-12-16', 'N2');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ad_producttype`
--

CREATE TABLE `ad_producttype` (
  `ma_loaisp` varchar(20) NOT NULL,
  `ten_loaisp` varchar(50) NOT NULL,
  `mota_loaisp` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `ad_producttype`
--

INSERT INTO `ad_producttype` (`ma_loaisp`, `ten_loaisp`, `mota_loaisp`) VALUES
('HW', 'Huawei', 'Chiếc điện thoại đầu tiên của Huawei ra mắt thị trường vào năm 2004 nhưng phải đến năm 2009 công ty mới ra mắt chiếc điện thoại Android đầu tiên.'),
('IP', 'Iphone ', 'Vào tháng 1 năm 2007, Steve Jobs đã giới thiệu chiếc iPhone được mong đợi từ lâu, một sự kết hợp giữa điện thoại thông minh hỗ trợ Internet và iPod.'),
('NK', 'Nokia', 'Nokia ra đời vào năm 1871 tại Phần Lan, nhưng mãi tới tận năm 1967, tập đoàn Nokia mới thực sự được thành lập sau khi 3 công ty con sáp nhập từ năm 1922'),
('RM', 'Realme', 'Realme ra mắt sản phẩm đầu tiên là Realme 1 dành riêng cho thị trường Ấn Độ vào tháng 5 năm 2018. Sau đó định hướng mở rộng đến thị trường Đông Nam Á và các quốc gia khác.'),
('SS', 'Samsung', 'Samsung ra mắt chiếc điện thoại đầu tiên năm 1988, năm 2010 công bố Galaxy S, đến nay hé lộ về Galaxy AI trên smartphone với nhiều khả năng lần đầu xuất hiện.');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `masp` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int(11) NOT NULL,
  `giaxuat` int(11) NOT NULL,
  `soluong` int(11) NOT NULL,
  `hinhanh` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tensp` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`cart_id`, `user_id`, `masp`, `quantity`, `giaxuat`, `soluong`, `hinhanh`, `tensp`) VALUES
(1, 11, 'IP2', 9, 13000000, 0, 'thumb_IP12Pro_1.jpg', 'Iphone 12 promax'),
(2, 11, 'IP3', 1, 21490000, 0, 't_m_18_1_1_3_1.webp', 'IPhone 14 ProMax'),
(3, 11, 'SS1', 3, 20990000, 0, '0002550_galaxy-s23-ultra-256gb.jpeg', 'Samsung S23 Ultra'),
(4, 11, 'IP1', 2, 36990000, 0, 'download.jpg', 'Iphone 16 ProMax');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `ten_loaisp` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `hinhanh` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tensp` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `soluong` int(11) NOT NULL,
  `giaxuat` int(11) NOT NULL,
  `thongtinkm` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chat_messages`
--

CREATE TABLE `chat_messages` (
  `message_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `sender` enum('user','staff') NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `order_status` varchar(50) DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `qlkm`
--

CREATE TABLE `qlkm` (
  `makm` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phantramkm` int(11) NOT NULL,
  `thongtinkm` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `qlkm`
--

INSERT INTO `qlkm` (`makm`, `phantramkm`, `thongtinkm`) VALUES
('GIAM10%', 10, 'Giảm 10% giá trị đơn hàng, tặng kèm tai nghe airpods, ốp, sạc, cường lực'),
('GIAM5%', 5, 'Giảm 5% giá trị đơn hàng, tặng kèm tai nghe'),
('GIAM7%', 7, 'Giảm 7% giá trị đơn hàng, tặng kèm tai nghe airpods, ốp, sạc');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(15) DEFAULT NULL,
  `address` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `created_at`, `phone`, `address`) VALUES
(8, 'huy', '$2y$10$U7nufttNsVDhea0g97d/YedVhaexSpZ/wUtzX3mUZW5w/9MmWsyWu', 'huy@gmail.com', '2024-12-13 01:47:59', '123', 'tay mô'),
(9, 'dung', '$2y$10$GHa8m5ahCKNKif5bg4nyG.W7GaH58Z6o.So34t6B6PxnUvNNVFDCC', 'professorr2905@gmail.com', '2024-12-17 03:20:14', '0828389268', 'VN'),
(11, 'cut', '$2y$10$u2HKMbywnSziRtN.iupJEuk0QeFnJDjj.6g16knRwcgkTscuSw5/2', '123@gmail.com', '2024-12-17 11:54:36', '0969', 'HN'),
(12, 'hung', '$2y$10$MBdapPm3ckmDtlyztFxIz.rkgBSLWeuiEaX5i2a37MOFqOFRM3Lq2', 'huy@123', '2024-12-17 19:40:59', '014', 'Tay mỗ');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `ad_nhacc`
--
ALTER TABLE `ad_nhacc`
  ADD PRIMARY KEY (`mancc`);

--
-- Chỉ mục cho bảng `ad_product`
--
ALTER TABLE `ad_product`
  ADD PRIMARY KEY (`masp`);

--
-- Chỉ mục cho bảng `ad_producttype`
--
ALTER TABLE `ad_producttype`
  ADD PRIMARY KEY (`ma_loaisp`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`ten_loaisp`);

--
-- Chỉ mục cho bảng `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Chỉ mục cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Chỉ mục cho bảng `qlkm`
--
ALTER TABLE `qlkm`
  ADD PRIMARY KEY (`makm`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Các ràng buộc cho bảng `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
