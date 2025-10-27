-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 27, 2025 lúc 10:26 AM
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
(3, 3, 3, 5, 'Rất hài lòng với thái độ phục vụ!', '2025-10-18 19:05:38');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `don_hang`
--

CREATE TABLE `don_hang` (
  `id_don_hang` int(11) NOT NULL,
  `id_khach_hang` int(11) DEFAULT NULL,
  `id_cham_soc` int(11) NOT NULL,
  `id_danh_gia` int(11) NOT NULL,
  `ngay_dat` date NOT NULL DEFAULT current_timestamp(),
  `tong_tien` decimal(10,2) DEFAULT NULL,
  `dia_chi_giao_hang` varchar(250) DEFAULT NULL,
  `ten_khach_hang` varchar(255) DEFAULT NULL,
  `so_dien_thoai` varchar(15) NOT NULL,
  `trang_thai` enum('chờ xác nhận','đang hoàn thành','đã hoàn thành','đã hủy') NOT NULL DEFAULT 'chờ xác nhận',
  `thoi_gian_bat_dau` time NOT NULL,
  `thoi_gian_ket_thuc` time NOT NULL,
  `thanh_toan` enum('tiền mặt','chuyển khoản') NOT NULL DEFAULT 'tiền mặt'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `don_hang`
--

INSERT INTO `don_hang` (`id_don_hang`, `id_khach_hang`, `id_cham_soc`, `id_danh_gia`, `ngay_dat`, `tong_tien`, `dia_chi_giao_hang`, `ten_khach_hang`, `so_dien_thoai`, `trang_thai`, `thoi_gian_bat_dau`, `thoi_gian_ket_thuc`, `thanh_toan`) VALUES
(22, 5, 0, 0, '2025-05-04', 999.00, 'q8', 'Khưu Gia Thịnh', '985059913', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(23, 6, 0, 0, '2025-06-01', 99.00, 'q8', 'Hoàn Trọng', '985059913', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(24, 7, 0, 0, '2025-07-01', 200.00, 'q8', 'VY', '123456789', '', '00:00:00', '00:00:00', 'tiền mặt'),
(25, 5, 0, 0, '2025-08-01', 500.00, 'q8', 'Khưu Gia Thịnh', '123456789', '', '00:00:00', '00:00:00', 'tiền mặt'),
(26, 8, 0, 0, '2025-05-05', 340.00, '110', 'Gia An', '334290589', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(27, 8, 0, 0, '2025-05-05', 40.00, '0', 'Gia An', '334290562', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(28, 8, 0, 0, '2025-05-05', 320.00, '0', 'Gia An', '334290589', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(29, 4, 0, 0, '2025-05-05', 20.00, '0', 'Gia An', '334290589', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(30, 4, 0, 0, '2025-05-05', 160.00, '0', 'Gia An', '334290589', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(31, 4, 0, 0, '2025-05-05', 20.00, '0', 'Gia An', '334290589', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(32, 4, 0, 0, '2025-05-05', 20.00, '0', 'Gia An', '334290589', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(33, 4, 0, 0, '2025-05-05', 20.00, '0', 'Gia An', '334290589', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(34, 4, 0, 0, '2025-05-05', 20.00, '0', 'Gia An', '334290589', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(35, 4, 0, 0, '2025-05-05', 20.00, '0', 'Gia An', '334290589', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(36, 4, 0, 0, '2025-05-05', 20.00, '0', 'Gia An', '334290589', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(37, 4, 0, 0, '2025-05-05', 20.00, '0', 'Gia An', '334290589', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(38, 4, 0, 0, '2025-05-05', 160.00, '0', 'Gia An', '334290589', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt'),
(39, 4, 0, 0, '2025-05-05', 160.00, '110', 'Gia An', '334290589', 'chờ xác nhận', '00:00:00', '00:00:00', 'tiền mặt');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khach_hang`
--

CREATE TABLE `khach_hang` (
  `id_khach_hang` int(11) NOT NULL,
  `ten_khach_hang` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `so_dien_thoai` varchar(15) NOT NULL,
  `mat_khau` varchar(100) NOT NULL,
  `hinh_anh` varchar(255) NOT NULL,
  `dia_chi` varchar(255) NOT NULL,
  `tuoi` int(11) NOT NULL,
  `gioi_tinh` enum('Nữ','Nam','','') NOT NULL,
  `chieu_cao` float NOT NULL,
  `can_nang` float NOT NULL,
  `role` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Đang đổ dữ liệu cho bảng `khach_hang`
--

INSERT INTO `khach_hang` (`id_khach_hang`, `ten_khach_hang`, `email`, `so_dien_thoai`, `mat_khau`, `hinh_anh`, `dia_chi`, `tuoi`, `gioi_tinh`, `chieu_cao`, `can_nang`, `role`) VALUES
(4, 'user', NULL, '0334290563', '226655', '', '', 0, 'Nữ', 0, 0, 0),
(5, 'Khưu Gia Thinh', 'phuongtramnganke@gmail.com', '334290564', '789', '', '', 0, 'Nữ', 0, 0, 0),
(6, 'Hoàn Trọng', 'trong123', '985059913', '123', '', '', 0, 'Nữ', 0, 0, 0),
(7, 'VY', NULL, '123456789', '123456', '', '', 0, 'Nữ', 0, 0, NULL),
(8, 'GiaThinh', 'khuugiathinh2@gmail.com', '334290589', '123', '', '', 0, 'Nữ', 0, 0, 0),
(9, 'trọng nguyễn', '', '987658249', '1111', '', '', 0, 'Nữ', 0, 0, 0),
(10, 'trong phuc', NULL, '789456321', '0123', '', '', 0, 'Nữ', 0, 0, 0),
(11, '', NULL, '987123', '111', '', '', 0, 'Nữ', 0, 0, 0),
(12, '', NULL, '11112222', '111', '', '', 0, 'Nữ', 0, 0, 0),
(13, '', NULL, '98758', '11', '', '', 0, 'Nữ', 0, 0, 0),
(14, '', NULL, '985', '111', '', '', 0, 'Nữ', 0, 0, 0);

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
(1, '', '', 'fontend/img/caregiver1.jpg', 'Nguyễn Thị Hồng', 'TP.HCM', 32, 'Nữ', 158, 50, 4.8, 'Giỏi', 12, 3500000.00),
(2, '', '', 'fontend/img/caregiver2.jpg', 'Trần Văn Dũng', 'Đà Nẵng', 35, 'Nam', 172, 70, 4.5, 'Khá', 8, 2900000.00),
(3, '', '', 'fontend/img/caregiver3.jpg', 'Lê Mai Anh', 'Hà Nội', 29, 'Nữ', 162, 51, 4.9, 'Giỏi', 15, 4500000.00),
(8, '123456789', '741', '', 'vy', '', 0, 'Nam', 0, 0, 1, '', 0, 0.00);

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
  ADD KEY `khach_hang_id` (`id_khach_hang`),
  ADD KEY `id_cham_soc` (`id_cham_soc`),
  ADD KEY `id_danh_gia` (`id_danh_gia`);

--
-- Chỉ mục cho bảng `khach_hang`
--
ALTER TABLE `khach_hang`
  ADD PRIMARY KEY (`id_khach_hang`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `so_dien_thoai` (`so_dien_thoai`);

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
  MODIFY `id_danh_gia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `id_don_hang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT cho bảng `khach_hang`
--
ALTER TABLE `khach_hang`
  MODIFY `id_khach_hang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `nguoi_cham_soc`
--
ALTER TABLE `nguoi_cham_soc`
  MODIFY `id_cham_soc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `don_hang`
--
ALTER TABLE `don_hang`
  ADD CONSTRAINT `don_hang_ibfk_1` FOREIGN KEY (`id_khach_hang`) REFERENCES `khach_hang` (`id_khach_hang`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
