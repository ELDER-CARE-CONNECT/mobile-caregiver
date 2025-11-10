-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 10, 2025 lúc 07:01 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `sanpham`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `ten_admin` varchar(255) NOT NULL,
  `so_dien_thoai` varchar(15) NOT NULL,
  `mat_khau` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `admin`
--

INSERT INTO `admin` (`id_admin`, `ten_admin`, `so_dien_thoai`, `mat_khau`) VALUES
(1, 'admin', '147258', '147');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `danh_gia`
--

CREATE TABLE `danh_gia` (
  `id_danh_gia` int(11) NOT NULL,
  `id_khach_hang` int(11) DEFAULT NULL,
  `id_cham_soc` int(11) DEFAULT NULL,
  `so_sao` int(11) DEFAULT NULL CHECK (`so_sao` between 1 and 5),
  `nhan_xet` text DEFAULT NULL,
  `ngay_danh_gia` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `danh_gia`
--

INSERT INTO `danh_gia` (`id_danh_gia`, `id_khach_hang`, `id_cham_soc`, `so_sao`, `nhan_xet`, `ngay_danh_gia`) VALUES
(1, 1, 1, 3, 'Người chăm sóc rất tận tâm và chu đáo.', '2025-10-18 19:05:38'),
(2, 2, 2, 4, 'Dịch vụ tốt, cần đúng giờ hơn.', '2025-10-18 19:05:38'),
(3, 3, 3, 5, 'Rất hài lòng với thái độ phục vụ!', '2025-10-18 19:05:38'),
(4, 7, 1, 4, 'Cũng cũng oke la ấy \r\n', '2025-11-03 16:00:58'),
(5, 7, 1, 5, 'Cũng cũng oke la ấy \r\n', '2025-11-03 16:02:01'),
(6, 7, 1, 5, 'rất tốt ', '2025-11-03 16:02:19'),
(7, 7, 1, 5, 'tốt ạ ', '2025-11-03 16:06:08'),
(8, 7, 1, 5, 'rất tốt \r\n', '2025-11-03 16:10:02'),
(9, 7, 1, 5, 'siêu tốt nha ạ\r\n', '2025-11-03 16:10:41'),
(10, 7, 1, 5, 'siêu tốt nha ạ\r\n', '2025-11-03 16:11:32'),
(11, 7, 1, 5, 'tốt ạ ', '2025-11-03 16:17:02'),
(12, 7, 1, 5, 'tốt ạ\r\n', '2025-11-03 16:18:22'),
(13, 7, 1, 5, 'kskdll', '2025-11-03 16:30:26'),
(24, 32, 1, 5, 'Hài Lòng ', '2025-11-10 04:35:15'),
(25, 32, 2, 5, 'Tốt\r\n', '2025-11-10 04:36:23'),
(26, 32, 2, 5, 'Tốt\r\n', '2025-11-10 04:37:57'),
(27, 32, 2, 5, 'Tốt\r\n', '2025-11-10 04:38:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

CREATE TABLE `don_hang` (
  `id_don_hang` int(11) NOT NULL,
  `id_khach_hang` int(11) DEFAULT NULL,
  `id_nguoi_cham_soc` int(11) DEFAULT NULL,
  `ngay_dat` date NOT NULL DEFAULT current_timestamp(),
  `tong_tien` decimal(10,2) DEFAULT NULL,
  `dia_chi_giao_hang` varchar(250) DEFAULT NULL,
  `ten_khach_hang` varchar(255) DEFAULT NULL,
  `so_dien_thoai` varchar(15) NOT NULL,
  `trang_thai` enum('chờ xác nhận','đang hoàn thành','đã hoàn thành','đã hủy') NOT NULL DEFAULT 'chờ xác nhận',
  `thoi_gian_bat_dau` datetime DEFAULT NULL,
  `thoi_gian_ket_thuc` datetime DEFAULT NULL,
  `hinh_thuc_thanh_toan` varchar(50) DEFAULT 'Tiền mặt',
  `thanh_toan_status` enum('chưa thanh toán','đã thanh toán','thanh toán thất bại') NOT NULL DEFAULT 'chưa thanh toán',
  `ma_giao_dich_vnpay` varchar(50) DEFAULT NULL,
  `vnp_ThoiGianThanhToan` datetime DEFAULT NULL,
  `ten_nhiem_vu` varchar(255) DEFAULT NULL,
  `trang_thai_nhiem_vu` varchar(500) DEFAULT 'chua_hoan_thanh'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `don_hang`
--

INSERT INTO `don_hang` (`id_don_hang`, `id_khach_hang`, `id_nguoi_cham_soc`, `ngay_dat`, `tong_tien`, `dia_chi_giao_hang`, `ten_khach_hang`, `so_dien_thoai`, `trang_thai`, `thoi_gian_bat_dau`, `thoi_gian_ket_thuc`, `hinh_thuc_thanh_toan`, `thanh_toan_status`, `ma_giao_dich_vnpay`, `vnp_ThoiGianThanhToan`, `ten_nhiem_vu`, `trang_thai_nhiem_vu`) VALUES
(127, 31, 1, '2025-11-10', 3500000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:30:00', '2025-11-11 07:30:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, 'Đi mua thuốc giúp', 'chua_hoan_thanh'),
(128, 31, 2, '2025-11-10', 2900000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:00:00', '2025-11-11 07:00:00', 'VNPAY (ATM)', 'đã thanh toán', '15248960', '2025-11-10 00:52:57', 'Đi mua thuốc giúp, Đi dạo', 'chua_hoan_thanh'),
(129, 31, 1, '2025-11-10', 3500000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:00:00', '2025-11-11 07:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"Đi chơi\",\"nấu ăn\"]', 'chua_hoan_thanh'),
(130, 31, 1, '2025-11-10', 10500000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:30:00', '2025-11-11 09:30:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"Đi chơi\",\"Nấu ăn\"]', 'chua_hoan_thanh'),
(131, 31, 1, '2025-11-10', 8750000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:00:00', '2025-11-11 08:30:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"Đi chơi\"]', 'chua_hoan_thanh'),
(132, 31, 1, '2025-11-10', 7000000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:00:00', '2025-11-11 08:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"Đi chơi\"]', 'chua_hoan_thanh'),
(133, 31, 2, '2025-11-10', 2900000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 07:00:00', '2025-11-11 08:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\"]', 'chua_hoan_thanh'),
(134, 31, 1, '2025-11-10', 8750000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:30:00', '2025-11-11 09:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"đi chơi\",\"nấu ăn\"]', 'chua_hoan_thanh'),
(135, 31, 1, '2025-11-10', 3500000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:00:00', '2025-11-11 07:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"Đi chơi\"]', 'chua_hoan_thanh'),
(136, 31, 1, '2025-11-10', 5250000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 05:30:00', '2025-11-11 07:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"Đi chơi\"]', 'chua_hoan_thanh'),
(137, 31, 1, '2025-11-10', 5250000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 04:30:00', '2025-11-11 06:00:00', 'VNPAY (ATM)', 'đã thanh toán', '15249077', '2025-11-10 02:49:42', '[\"Đi mua thuốc giúp\",\"Đi chơi\",\"Nấu ăn\"]', 'chua_hoan_thanh'),
(138, 32, 1, '2025-11-10', 700000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', 'Lê Quốc Trí', '0918720115', 'chờ xác nhận', '2025-11-11 08:00:00', '2025-11-11 10:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"Nấu ăn\"]', 'chua_hoan_thanh'),
(139, 32, 2, '2025-11-10', 290000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', 'Lê Quốc Trí', '0918720115', 'chờ xác nhận', '2025-11-11 04:00:00', '2025-11-11 05:00:00', 'VNPAY (ATM)', 'đã thanh toán', '15249089', '2025-11-10 03:34:15', '[\"Đi mua thuốc giúp\",\"Đi dạo\"]', 'chua_hoan_thanh'),
(140, 32, 8, '2025-11-10', 290000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', 'Lê Quốc Trí', '0918720115', 'chờ xác nhận', '2025-11-11 09:00:00', '2025-11-11 10:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\"]', 'chua_hoan_thanh'),
(141, 34, 1, '2025-11-10', 700000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', 'trí quốc', '0918743114', 'đã hoàn thành', '2025-11-11 06:00:00', '2025-11-11 08:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"nấu ăn\"]', 'hoan_thanh;hoan_thanh'),
(142, 34, 1, '2025-11-10', 525000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', 'trí quốc', '0918743114', 'đã hủy', '2025-11-11 05:30:00', '2025-11-11 07:00:00', 'VNPAY (ATM)', 'đã thanh toán', '15249409', '2025-11-10 10:37:10', '[\"Đi mua thuốc giúp\",\"Đi dạo\"]', 'chua_hoan_thanh');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khach_hang`
--

CREATE TABLE `khach_hang` (
  `id_khach_hang` int(11) NOT NULL,
  `ten_khach_hang` varchar(100) NOT NULL,
  `ten_duong` varchar(255) DEFAULT NULL,
  `phuong_xa` varchar(100) DEFAULT NULL,
  `tinh_thanh` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `so_dien_thoai` varchar(15) NOT NULL,
  `mat_khau` varchar(100) NOT NULL,
  `hinh_anh` varchar(255) NOT NULL,
  `tuoi` int(11) NOT NULL,
  `gioi_tinh` enum('Nữ','Nam','','') NOT NULL,
  `chieu_cao` float NOT NULL,
  `can_nang` float NOT NULL,
  `role` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khach_hang`
--

INSERT INTO `khach_hang` (`id_khach_hang`, `ten_khach_hang`, `ten_duong`, `phuong_xa`, `tinh_thanh`, `email`, `so_dien_thoai`, `mat_khau`, `hinh_anh`, `tuoi`, `gioi_tinh`, `chieu_cao`, `can_nang`, `role`) VALUES
(4, 'user', NULL, NULL, NULL, NULL, '0334290563', '226655', '', 0, 'Nữ', 0, 0, 0),
(5, 'Khưu Gia Thinh', NULL, NULL, NULL, 'phuongtramnganke@gmail.com', '334290564', '789', '', 0, 'Nữ', 0, 0, 0),
(6, 'Hoàn Trọng', NULL, NULL, NULL, 'trong123', '985059913', '123', '', 0, 'Nữ', 0, 0, 0),
(7, 'VY', NULL, NULL, NULL, NULL, '123456789', '123456', '', 0, 'Nữ', 0, 0, NULL),
(8, 'GiaThinh', NULL, NULL, NULL, 'khuugiathinh2@gmail.com', '334290589', '123', '', 0, 'Nữ', 0, 0, 0),
(9, 'trọng nguyễn', NULL, NULL, NULL, '', '987658249', '1111', '', 0, 'Nữ', 0, 0, 0),
(10, 'trong phuc', NULL, NULL, NULL, NULL, '789456321', '0123', '', 0, 'Nữ', 0, 0, 0),
(11, '', NULL, NULL, NULL, NULL, '987123', '111', '', 0, 'Nữ', 0, 0, 0),
(12, '', NULL, NULL, NULL, NULL, '11112222', '111', '', 0, 'Nữ', 0, 0, 0),
(13, '', NULL, NULL, NULL, NULL, '98758', '11', '', 0, 'Nữ', 0, 0, 0),
(14, '', NULL, NULL, NULL, NULL, '985', '111', '', 0, 'Nữ', 0, 0, 0),
(32, 'Lê Quốc Trí', '70 Tô Ký', 'Phường Tân Chánh Hiệp', 'Thành Phố Hồ Chí Minh', 'admin@binhbat.ai', '0918720115', '101405', '', 20, 'Nam', 170, 62, 0),
(33, '', NULL, NULL, NULL, NULL, '0918720116', '101405', '', 0, 'Nữ', 0, 0, 0),
(34, 'trí quốc', '70 Tô Ký', 'Phường Tân Chánh Hiệp', 'Thành Phố Hồ Chí Minh', 'quoctri101405@gmail.com', '0918743114', 'GOOGLE_LOGIN_KEY', 'https://lh3.googleusercontent.com/a/ACg8ocJYk-R7sdfaDQOVGxXdCJibqXSn2WaQrllw6VqnEsXdlcikew=s96-c', 20, 'Nam', 172, 62, 0),
(38, '35.Lê Quốc Trí', '70 Tô Ký', 'Phường Tân Chánh Hiệp', 'Thành Phố Hồ Chí Minh', 'quoctri1014@gmail.com', '0918720117', 'GOOGLE_LOGIN_KEY', 'https://lh3.googleusercontent.com/a/ACg8ocLsKz8d7lk9v3bS18U_0w92YFtlMmhEiEJMtAKE1yrSjpcqzQ=s96-c', 24, 'Nam', 172, 62, 0),
(39, 'Trí Lê Quốc', '70 Tô Ký', 'Phường Hiệp Thành', 'Thành Phố Hồ Chí Minh', 'trilq1936@ut.edu.vn', '0123456789', 'GOOGLE_LOGIN_KEY', 'https://lh3.googleusercontent.com/a/ACg8ocKFAljaktiNEoCHbn7KUSeFoXmrH4K5HAzcx0iSv8HH6ZC3EQ=s96-c', 19, 'Nữ', 160, 56, 0),
(40, 'Lê Quốc Trí', '70 Tô Ký', 'Phường Hiệp Thành', 'Đà Lạt', '78938439@gmail.com', '0987654321', '123', 'uploads/avatars/avatar_69117cc4aea033.53941660.png', 21, 'Nữ', 160, 57, 0);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khieu_nai`
--

CREATE TABLE `khieu_nai` (
  `id_khieu_nai` int(11) NOT NULL,
  `id_don_hang` int(11) NOT NULL,
  `id_khach_hang` int(11) NOT NULL,
  `noi_dung` text NOT NULL,
  `ngay_gui` datetime DEFAULT current_timestamp(),
  `trang_thai` enum('Chờ xử lý','Đã giải quyết') DEFAULT 'Chờ xử lý',
  `phan_hoi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoi_cham_soc`
--

CREATE TABLE `nguoi_cham_soc` (
  `id_cham_soc` int(11) NOT NULL,
  `ten_tai_khoan` varchar(255) NOT NULL DEFAULT '0',
  `mat_khau` varchar(255) NOT NULL,
  `hinh_anh` varchar(255) DEFAULT NULL,
  `ho_ten` varchar(100) NOT NULL,
  `dia_chi` varchar(255) DEFAULT NULL,
  `tuoi` int(11) DEFAULT NULL,
  `gioi_tinh` enum('Nam','Nữ') NOT NULL DEFAULT 'Nam',
  `chieu_cao` float DEFAULT NULL,
  `can_nang` float DEFAULT NULL,
  `danh_gia_tb` float DEFAULT 0,
  `kinh_nghiem` varchar(50) DEFAULT NULL,
  `don_da_nhan` int(11) DEFAULT 0,
  `tong_tien_kiem_duoc` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoi_cham_soc`
--

INSERT INTO `nguoi_cham_soc` (`id_cham_soc`, `ten_tai_khoan`, `mat_khau`, `hinh_anh`, `ho_ten`, `dia_chi`, `tuoi`, `gioi_tinh`, `chieu_cao`, `can_nang`, `danh_gia_tb`, `kinh_nghiem`, `don_da_nhan`, `tong_tien_kiem_duoc`) VALUES
(1, '', '', 'fontend/img/caregiver1.jpg', 'Nguyễn Thị Hồng', 'TP.HCM', 32, 'Nữ', 158, 50, 4.8, 'Giỏi', 12, 350000.00),
(2, '', '', 'fontend/img/caregiver2.jpg', 'Trần Văn Dũng', 'Đà Nẵng', 35, 'Nam', 172, 70, 4.5, 'Khá', 8, 290000.00),
(3, '', '', 'fontend/img/caregiver3.jpg', 'Lê Mai Anh', 'Hà Nội', 29, 'Nữ', 162, 51, 4.9, 'Giỏi', 15, 320000.00),
(8, '123456789', '741', '', 'vy', '', 0, 'Nam', 0, 0, 1, '', 0, 290000.00);

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Chỉ mục cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD PRIMARY KEY (`id_danh_gia`),
  ADD KEY `id_khach_hang` (`id_khach_hang`),
  ADD KEY `id_cham_soc` (`id_cham_soc`);

--
-- Chỉ mục cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`id_don_hang`),
  ADD KEY `khach_hang_id` (`id_khach_hang`);

--
-- Chỉ mục cho bảng `khach_hang`
--
ALTER TABLE `khach_hang`
  ADD PRIMARY KEY (`id_khach_hang`),
  ADD UNIQUE KEY `so_dien_thoai` (`so_dien_thoai`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Chỉ mục cho bảng `khieu_nai`
--
ALTER TABLE `khieu_nai`
  ADD PRIMARY KEY (`id_khieu_nai`),
  ADD KEY `id_don_hang` (`id_don_hang`),
  ADD KEY `id_khach_hang` (`id_khach_hang`);

--
-- Chỉ mục cho bảng `nguoi_cham_soc`
--
ALTER TABLE `nguoi_cham_soc`
  ADD PRIMARY KEY (`id_cham_soc`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `danh_gia`
--
ALTER TABLE `danh_gia`
  MODIFY `id_danh_gia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `id_don_hang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT cho bảng `khach_hang`
--
ALTER TABLE `khach_hang`
  MODIFY `id_khach_hang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT cho bảng `khieu_nai`
--
ALTER TABLE `khieu_nai`
  MODIFY `id_khieu_nai` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nguoi_cham_soc`
--
ALTER TABLE `nguoi_cham_soc`
  MODIFY `id_cham_soc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `khieu_nai`
--
ALTER TABLE `khieu_nai`
  ADD CONSTRAINT `khieu_nai_ibfk_1` FOREIGN KEY (`id_don_hang`) REFERENCES `don_hang` (`id_don_hang`),
  ADD CONSTRAINT `khieu_nai_ibfk_2` FOREIGN KEY (`id_khach_hang`) REFERENCES `khach_hang` (`id_khach_hang`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
