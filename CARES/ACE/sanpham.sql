-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 17, 2025 at 04:36 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sanpham`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `ten_admin` varchar(255) NOT NULL,
  `so_dien_thoai` varchar(15) NOT NULL,
  `mat_khau` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `ten_admin`, `so_dien_thoai`, `mat_khau`) VALUES
(1, 'admin', '147258', '147');

-- --------------------------------------------------------

--
-- Table structure for table `danh_gia`
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
-- Dumping data for table `danh_gia`
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
(25, 32, 8, 5, 'Tốt', '2025-11-10 04:36:23'),
(26, 32, 8, 2, 'Tốt\r\n', '2025-11-10 04:37:57'),
(27, 32, 8, 3, 'Tốt\r\n', '2025-11-10 04:38:28');

-- --------------------------------------------------------

--
-- Table structure for table `don_hang`
--

CREATE TABLE `don_hang` (
  `id_don_hang` int(11) NOT NULL,
  `id_khach_hang` int(11) DEFAULT NULL,
  `id_nguoi_cham_soc` int(11) NOT NULL,
  `id_danh_gia` int(11) NOT NULL,
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
  `trang_thai_nhiem_vu` enum('chờ xác nhận','đã hoàn thành') DEFAULT 'chờ xác nhận'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `don_hang`
--

INSERT INTO `don_hang` (`id_don_hang`, `id_khach_hang`, `id_nguoi_cham_soc`, `id_danh_gia`, `ngay_dat`, `tong_tien`, `dia_chi_giao_hang`, `ten_khach_hang`, `so_dien_thoai`, `trang_thai`, `thoi_gian_bat_dau`, `thoi_gian_ket_thuc`, `hinh_thuc_thanh_toan`, `thanh_toan_status`, `ma_giao_dich_vnpay`, `vnp_ThoiGianThanhToan`, `ten_nhiem_vu`, `trang_thai_nhiem_vu`) VALUES
(127, 31, 1, 0, '2025-11-10', 3500000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'đã hoàn thành', '2025-11-11 06:30:00', '2025-11-11 07:30:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, 'Đi mua thuốc giúp', ''),
(128, 31, 2, 0, '2025-11-10', 2900000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:00:00', '2025-11-11 07:00:00', 'VNPAY (ATM)', 'đã thanh toán', '15248960', '2025-11-10 00:52:57', 'Đi mua thuốc giúp, Đi dạo', ''),
(129, 31, 1, 0, '2025-11-10', 3500000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:00:00', '2025-11-11 07:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"Đi chơi\",\"nấu ăn\"]', ''),
(130, 31, 1, 0, '2025-11-10', 10500000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:30:00', '2025-11-11 09:30:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"Đi chơi\",\"Nấu ăn\"]', ''),
(131, 31, 1, 0, '2025-11-10', 8750000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:00:00', '2025-11-11 08:30:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"Đi chơi\"]', ''),
(132, 31, 1, 0, '2025-11-10', 7000000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:00:00', '2025-11-11 08:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"Đi chơi\"]', ''),
(133, 31, 2, 0, '2025-11-10', 2900000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 07:00:00', '2025-11-11 08:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\"]', ''),
(134, 31, 1, 0, '2025-11-10', 8750000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:30:00', '2025-11-11 09:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"đi chơi\",\"nấu ăn\"]', ''),
(135, 31, 1, 0, '2025-11-10', 3500000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 06:00:00', '2025-11-11 07:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"Đi chơi\"]', ''),
(136, 31, 1, 0, '2025-11-10', 5250000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 05:30:00', '2025-11-11 07:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"Đi chơi\"]', ''),
(137, 31, 1, 0, '2025-11-10', 5250000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', '35.Lê Quốc Trí', '0917702116', 'chờ xác nhận', '2025-11-11 04:30:00', '2025-11-11 06:00:00', 'VNPAY (ATM)', 'đã thanh toán', '15249077', '2025-11-10 02:49:42', '[\"Đi mua thuốc giúp\",\"Đi chơi\",\"Nấu ăn\"]', ''),
(138, 32, 1, 0, '2025-11-10', 700000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', 'Lê Quốc Trí', '0918720115', 'chờ xác nhận', '2025-11-11 08:00:00', '2025-11-11 10:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"Nấu ăn\"]', ''),
(139, 32, 2, 0, '2025-11-10', 290000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', 'Lê Quốc Trí', '0918720115', 'chờ xác nhận', '2025-11-11 04:00:00', '2025-11-11 05:00:00', 'VNPAY (ATM)', 'đã thanh toán', '15249089', '2025-11-10 03:34:15', '[\"Đi mua thuốc giúp\",\"Đi dạo\"]', ''),
(140, 32, 8, 0, '2025-11-10', 290000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', 'Lê Quốc Trí', '0918720115', 'đã hoàn thành', '2025-11-11 09:00:00', '2025-11-11 10:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\"]', 'đã hoàn thành'),
(141, 40, 8, 0, '2025-11-10', 700000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', 'trí quốc', '0918743114', 'đã hoàn thành', '2025-11-11 06:00:00', '2025-11-11 08:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\",\"nấu ăn\"]', 'đã hoàn thành'),
(142, 40, 8, 0, '2025-11-10', 525000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', 'trí quốc', '0918743114', 'đã hoàn thành', '2025-11-11 05:30:00', '2025-11-11 07:00:00', 'VNPAY (ATM)', 'đã thanh toán', '15249409', '2025-11-10 10:37:10', '[\"Đi mua thuốc giúp\",\"Đi dạo\"]', 'đã hoàn thành'),
(143, 32, 8, 0, '2025-11-09', 290000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', 'Lê Quốc Phong', '0918720115', 'đang hoàn thành', '2025-11-22 09:00:00', '2025-11-29 10:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\"]', 'chờ xác nhận'),
(144, 32, 8, 0, '2025-11-09', 222000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', 'Thịnh', '0918720115', 'đã hủy', '2025-11-22 09:00:00', '2025-11-29 10:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\"]', 'chờ xác nhận'),
(145, 32, 8, 0, '2025-11-09', 222000.00, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', 'Thịnh', '0918720115', 'đã hủy', '2025-11-22 09:00:00', '2025-11-29 10:00:00', 'Tiền mặt', 'chưa thanh toán', NULL, NULL, '[\"Đi mua thuốc giúp\"]', 'chờ xác nhận'),
(146, 32, 1, 0, '2025-11-17', 99999999.99, '70 Tô Ký, Phường Tân Chánh Hiệp, Thành Phố Hồ Chí Minh', 'Lê Quốc Trí', '0918720115', 'chờ xác nhận', '2025-11-21 07:00:00', '2025-12-06 08:00:00', 'vnpay', 'chưa thanh toán', NULL, NULL, '[\"sdasd\"]', '');

-- --------------------------------------------------------

--
-- Table structure for table `khach_hang`
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
-- Dumping data for table `khach_hang`
--

INSERT INTO `khach_hang` (`id_khach_hang`, `ten_khach_hang`, `ten_duong`, `phuong_xa`, `tinh_thanh`, `email`, `so_dien_thoai`, `mat_khau`, `hinh_anh`, `tuoi`, `gioi_tinh`, `chieu_cao`, `can_nang`, `role`) VALUES
(4, 'user', NULL, NULL, NULL, NULL, '0334290563', '226655', '', 0, 'Nữ', 0, 0, 0),
(5, 'ACEDD', '80 tô ký', 'Phường Hiệp Thành', 'quận 12', 'phuongtramnganke@gmail.com', '0334290562', '789', 'https://lh3.googleusercontent.com/a/ACg8ocLAIrzGFB-djKTvAN3jVHDZ7_Db8KGnpKg7SlSzfS4E-hp-7w=s96-c', 28, 'Nam', 170, 80, 0),
(6, 'Hoàn Trọng', NULL, NULL, NULL, 'trong123', '985059913', '123', '', 0, 'Nữ', 0, 0, 0),
(7, 'VY', NULL, NULL, NULL, NULL, '123456789', '123456', '', 0, 'Nữ', 0, 0, NULL),
(8, 'GiaThinh', NULL, NULL, NULL, 'khuugiathinh2@gmail.com', '334290589', '123', '', 0, 'Nữ', 0, 0, 0),
(9, 'trọng nguyễn', NULL, NULL, NULL, '', '987658249', '1111', '', 0, 'Nữ', 0, 0, 0),
(10, 'trong phuc', NULL, NULL, NULL, NULL, '789456321', '0123', '', 0, 'Nữ', 0, 0, 0),
(11, '', NULL, NULL, NULL, NULL, '987123', '111', '', 0, 'Nữ', 0, 0, 0),
(12, '', NULL, NULL, NULL, NULL, '11112222', '111', '', 0, 'Nữ', 0, 0, 0),
(13, '', NULL, NULL, NULL, NULL, '98758', '11', '', 0, 'Nữ', 0, 0, 0),
(14, '', NULL, NULL, NULL, NULL, '985', '111', '', 0, 'Nữ', 0, 0, 0),
(32, 'Lê Quốc Trí', '70 Tô Ký', 'Phường Tân Chánh Hiệp', 'Thành Phố Hồ Chí Minh', 'admin@binhbat.ai', '0918720115', '101405', 'uploads/avatars/avatar_691b32aee06c37.27432483.jpg', 20, 'Nam', 170, 62, 0),
(33, '', NULL, NULL, NULL, NULL, '0918720116', '101405', '', 0, 'Nữ', 0, 0, 0),
(34, 'trí quốc', '70 Tô Ký', 'Phường Tân Chánh Hiệp', 'Thành Phố Hồ Chí Minh', 'quoctri101405@gmail.com', '0918743114', 'GOOGLE_LOGIN_KEY', 'https://lh3.googleusercontent.com/a/ACg8ocJYk-R7sdfaDQOVGxXdCJibqXSn2WaQrllw6VqnEsXdlcikew=s96-c', 20, 'Nam', 172, 62, 0),
(38, '35.Lê Quốc Trí', '70 Tô Ký', 'Phường Tân Chánh Hiệp', 'Thành Phố Hồ Chí Minh', 'quoctri1014@gmail.com', '0918720117', 'GOOGLE_LOGIN_KEY', 'https://lh3.googleusercontent.com/a/ACg8ocLsKz8d7lk9v3bS18U_0w92YFtlMmhEiEJMtAKE1yrSjpcqzQ=s96-c', 24, 'Nam', 172, 62, 0),
(39, 'Trí Lê Quốc', '70 Tô Ký', 'Phường Hiệp Thành', 'Thành Phố Hồ Chí Minh', 'trilq1936@ut.edu.vn', '0123456789', 'GOOGLE_LOGIN_KEY', 'https://lh3.googleusercontent.com/a/ACg8ocKFAljaktiNEoCHbn7KUSeFoXmrH4K5HAzcx0iSv8HH6ZC3EQ=s96-c', 19, 'Nữ', 160, 56, 0),
(40, 'Lê Quốc Trí', '70 Tô Ký', 'Phường Hiệp Thành', 'Đà Lạt', '78938439@gmail.com', '0987654321', '123', 'uploads/avatars/avatar_69117cc4aea033.53941660.png', 21, 'Nữ', 160, 57, 0),
(41, 'Vy Nguyễn Thị Anh', NULL, NULL, NULL, 'vynta7822@ut.edu.vn', '', '', 'https://lh3.googleusercontent.com/a/ACg8ocKwsKW2Uq21H96w76S5G0HxqOQRshL0ePkwPVUMAIQeEy1o=s96-c', 0, 'Nữ', 0, 0, NULL),
(42, '', NULL, NULL, NULL, NULL, '0147258369', '123456', '', 0, 'Nữ', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `khieu_nai`
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

--
-- Dumping data for table `khieu_nai`
--

INSERT INTO `khieu_nai` (`id_khieu_nai`, `id_don_hang`, `id_khach_hang`, `noi_dung`, `ngay_gui`, `trang_thai`, `phan_hoi`) VALUES
(18, 127, 4, 'Nhân viên chăm sóc đến muộn, người già phải chờ lâu.', '2025-11-10 09:30:00', 'Chờ xử lý', NULL),
(19, 128, 5, 'Nhân viên không nắm rõ chế độ thuốc, gây khó khăn cho người già.', '2025-11-10 11:15:00', 'Chờ xử lý', NULL),
(20, 129, 6, 'Nhân viên chưa hướng dẫn người già các hoạt động sinh hoạt.', '2025-11-11 08:00:00', 'Chờ xử lý', NULL),
(21, 130, 7, 'Chế độ ăn của người già chưa phù hợp, cần điều chỉnh.', '2025-11-11 09:00:00', 'Chờ xử lý', NULL),
(22, 131, 8, 'Người già than phiền về việc không được đi dạo đúng lịch.', '2025-11-11 08:30:00', 'Đã giải quyết', 'fgdfg'),
(23, 132, 9, 'Nhân viên chưa hỗ trợ đầy đủ các hoạt động chăm sóc.', '2025-11-11 08:00:00', 'Đã giải quyết', 'oke r á');

-- --------------------------------------------------------

--
-- Table structure for table `nguoi_cham_soc`
--

CREATE TABLE `nguoi_cham_soc` (
  `id_cham_soc` int(11) NOT NULL,
  `so_dien_thoai` varchar(255) NOT NULL DEFAULT '0',
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
  `tong_tien_kiem_duoc` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nguoi_cham_soc`
--

INSERT INTO `nguoi_cham_soc` (`id_cham_soc`, `so_dien_thoai`, `mat_khau`, `hinh_anh`, `ho_ten`, `dia_chi`, `tuoi`, `gioi_tinh`, `chieu_cao`, `can_nang`, `danh_gia_tb`, `kinh_nghiem`, `tong_tien_kiem_duoc`) VALUES
(1, '', '', 'fontend/img/caregiver1.jpg', 'Nguyễn Thị Hồng', 'TP.HCM', 32, 'Nữ', 158, 50, 4.7, 'Giỏi', 350000.00),
(2, '', '', 'fontend/img/caregiver2.jpg', 'Trần Văn Dũng', 'Đà Nẵng', 35, 'Nam', 172, 70, 4.5, 'Khá', 290000.00),
(3, '', '', 'fontend/img/caregiver3.jpg', 'Lê Mai Anh', 'Hà Nội', 29, 'Nữ', 162, 51, 4.9, 'Giỏi', 320000.00),
(8, '123456789', '741', '', 'vy', '70 tô ký', 20, 'Nữ', 170, 75, 0, '5 năm', 290000.00),
(9, '0987765', '123', 'uploads/1763178683_Ảnh chụp màn hình 2025-11-06 121101.png', 'anhvy', '', 0, 'Nam', 0, 0, 0, '', 0.00),
(10, '0334290562', 'kdspwoldrk123', 'frontend/upload/1763389923_bang-gia-dich-vu-cham-soc-nguoi-gia-tai-nha-Hoan-My.jpg', 'khuugiathinh', '70 tô ký', 53, '', 170, 70, 0, '5 năm', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `nhiem_vu`
--

CREATE TABLE `nhiem_vu` (
  `id_nhiem_vu` int(11) NOT NULL,
  `id_don_hang` int(11) NOT NULL,
  `ten_nhiem_vu` varchar(255) NOT NULL,
  `trang_thai_nhiem_vu` enum('chờ xác nhận','đã hoàn thành') NOT NULL DEFAULT 'chờ xác nhận'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tin_nhan`
--

CREATE TABLE `tin_nhan` (
  `id_tin_nhan` int(11) NOT NULL,
  `id_don_hang` int(11) NOT NULL,
  `id_khach_hang` int(11) NOT NULL,
  `id_cham_soc` int(11) NOT NULL,
  `nguoi_gui` enum('khach_hang','cham_soc') NOT NULL,
  `noi_dung` text NOT NULL,
  `thoi_gian` timestamp NOT NULL DEFAULT current_timestamp(),
  `da_xem` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = chưa xem, 1 = đã xem'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tin_nhan`
--

INSERT INTO `tin_nhan` (`id_tin_nhan`, `id_don_hang`, `id_khach_hang`, `id_cham_soc`, `nguoi_gui`, `noi_dung`, `thoi_gian`, `da_xem`) VALUES
(3, 145, 32, 8, 'cham_soc', 'd', '2025-11-16 08:28:55', 1),
(4, 141, 40, 8, 'cham_soc', 'e', '2025-11-16 09:41:35', 1),
(5, 141, 40, 8, 'khach_hang', 'd', '2025-11-16 09:41:38', 1),
(6, 142, 40, 8, 'khach_hang', 'wqe', '2025-11-16 10:13:50', 1),
(7, 142, 40, 8, 'cham_soc', 'asd', '2025-11-16 10:14:00', 1),
(8, 142, 40, 8, 'cham_soc', 'd', '2025-11-16 10:24:55', 1),
(9, 142, 40, 8, 'cham_soc', 'd', '2025-11-16 10:31:36', 1),
(10, 142, 40, 8, 'khach_hang', 'sasdas', '2025-11-16 10:32:13', 1),
(11, 142, 40, 8, 'khach_hang', 'asdas', '2025-11-16 10:32:14', 1),
(12, 142, 40, 8, 'khach_hang', 'asdasd', '2025-11-16 10:32:15', 1),
(13, 142, 40, 8, 'khach_hang', 'asdasd', '2025-11-16 10:32:25', 1),
(14, 142, 40, 8, 'khach_hang', 'dasdas', '2025-11-16 10:32:26', 1),
(15, 142, 40, 8, 'cham_soc', 'asdasd', '2025-11-16 10:32:39', 1),
(16, 142, 40, 8, 'khach_hang', 'asdasd', '2025-11-16 10:32:44', 1),
(17, 142, 40, 8, 'cham_soc', 'd', '2025-11-16 10:32:49', 1),
(18, 142, 40, 8, 'khach_hang', 'dasda', '2025-11-16 10:32:53', 1),
(19, 141, 40, 8, 'cham_soc', 'd', '2025-11-16 10:35:42', 1),
(20, 142, 40, 8, 'khach_hang', 'sada', '2025-11-16 10:36:17', 1),
(21, 141, 40, 8, 'khach_hang', 'asdas', '2025-11-16 10:36:34', 1),
(22, 141, 40, 8, 'khach_hang', 'sdasd', '2025-11-16 10:36:35', 1),
(23, 141, 40, 8, 'khach_hang', 'dfgdg', '2025-11-16 10:36:42', 1),
(24, 142, 40, 8, 'cham_soc', 'dasdas', '2025-11-16 11:01:49', 1),
(25, 141, 40, 8, 'cham_soc', 'asdasd', '2025-11-16 11:05:13', 1),
(26, 141, 40, 8, 'cham_soc', 'asd', '2025-11-16 11:05:18', 1),
(27, 142, 40, 8, 'khach_hang', 'sadas', '2025-11-16 11:05:43', 1),
(28, 142, 40, 8, 'khach_hang', 'sadasd', '2025-11-16 11:08:47', 1),
(29, 142, 40, 8, 'cham_soc', 'asdasd', '2025-11-16 11:11:53', 1),
(30, 142, 40, 8, 'cham_soc', 'asd', '2025-11-16 11:12:01', 1),
(31, 142, 40, 8, 'khach_hang', 'sdasd', '2025-11-16 11:16:17', 1),
(32, 142, 40, 8, 'khach_hang', 'asdasd', '2025-11-16 11:16:22', 1),
(33, 145, 32, 8, 'cham_soc', 'asdas', '2025-11-16 11:16:36', 1),
(34, 142, 40, 8, 'cham_soc', 'asdasd', '2025-11-16 11:16:41', 1),
(35, 142, 40, 8, 'khach_hang', 'asdasd', '2025-11-16 11:18:53', 1),
(36, 142, 40, 8, 'khach_hang', 'asdasd', '2025-11-16 11:18:56', 1),
(37, 142, 40, 8, 'cham_soc', 'asdasd', '2025-11-16 11:19:04', 1),
(38, 142, 40, 8, 'cham_soc', 'yêu', '2025-11-16 11:23:18', 1),
(39, 142, 40, 8, 'cham_soc', 'yêu', '2025-11-16 11:25:00', 1),
(40, 142, 40, 8, 'cham_soc', 'anh', '2025-11-16 11:25:01', 1),
(41, 142, 40, 8, 'khach_hang', 'asdasd', '2025-11-16 12:11:29', 1),
(42, 142, 40, 8, 'cham_soc', 'asdas', '2025-11-16 12:12:08', 1),
(43, 142, 40, 8, 'khach_hang', 'asdas', '2025-11-16 12:14:37', 1),
(44, 141, 40, 8, 'cham_soc', 'asdas', '2025-11-16 12:14:52', 1),
(45, 141, 40, 8, 'cham_soc', 'asd', '2025-11-16 12:14:54', 1),
(46, 141, 40, 8, 'cham_soc', 'sadas', '2025-11-16 12:18:35', 1),
(47, 141, 40, 8, 'cham_soc', 'asdas', '2025-11-16 12:25:02', 1),
(48, 141, 40, 8, 'khach_hang', 'sadas', '2025-11-16 12:25:51', 1),
(49, 142, 40, 8, 'cham_soc', 'asdas', '2025-11-16 12:31:23', 1),
(50, 142, 40, 8, 'cham_soc', 'asd', '2025-11-16 12:31:25', 1),
(51, 141, 40, 8, 'khach_hang', 'asdasa', '2025-11-16 12:31:33', 1),
(52, 141, 40, 8, 'khach_hang', 'das', '2025-11-16 12:31:34', 1),
(53, 142, 40, 8, 'cham_soc', 'sadas', '2025-11-16 12:36:49', 1),
(54, 141, 40, 8, 'cham_soc', 'asdasd', '2025-11-16 12:37:02', 1),
(55, 142, 40, 8, 'khach_hang', 'asdasd', '2025-11-16 12:40:29', 1),
(56, 142, 40, 8, 'khach_hang', 'dddd', '2025-11-16 12:40:44', 1),
(57, 142, 40, 8, 'khach_hang', 'asd', '2025-11-16 12:42:03', 1),
(58, 142, 40, 8, 'khach_hang', 'asdasd', '2025-11-16 12:44:13', 1),
(59, 141, 40, 8, 'khach_hang', 'd', '2025-11-16 12:44:59', 1),
(60, 141, 40, 8, 'khach_hang', 'zxcv', '2025-11-16 12:48:57', 1),
(61, 142, 40, 8, 'khach_hang', 'pl,', '2025-11-16 12:49:13', 1),
(62, 141, 40, 8, 'khach_hang', 'lm', '2025-11-16 12:49:34', 1),
(63, 141, 40, 8, 'khach_hang', 'sdasd', '2025-11-16 12:53:13', 1),
(64, 145, 32, 8, 'khach_hang', 'd', '2025-11-16 13:00:39', 1),
(65, 141, 40, 8, 'khach_hang', 'anh yêu em', '2025-11-17 05:51:32', 1),
(66, 141, 40, 8, 'khach_hang', 'asdasd', '2025-11-17 05:51:46', 1),
(67, 141, 40, 8, 'khach_hang', 'em yêu anh', '2025-11-17 05:52:03', 1),
(68, 142, 40, 8, 'khach_hang', 'dsad', '2025-11-17 05:59:55', 1),
(69, 142, 40, 8, 'khach_hang', 'asd', '2025-11-17 06:00:28', 1),
(70, 141, 40, 8, 'khach_hang', 'sad', '2025-11-17 06:05:37', 1),
(71, 141, 40, 8, 'khach_hang', 'sadas', '2025-11-17 06:10:56', 1),
(72, 142, 40, 8, 'khach_hang', 'aaa', '2025-11-17 06:16:51', 1),
(73, 142, 40, 8, 'khach_hang', 'a', '2025-11-17 06:17:00', 1),
(74, 142, 40, 8, 'khach_hang', 'asd', '2025-11-17 06:17:45', 1),
(75, 142, 40, 8, 'khach_hang', 'asd', '2025-11-17 06:19:39', 1),
(76, 142, 40, 8, 'khach_hang', 'asd', '2025-11-17 06:30:09', 1),
(77, 142, 40, 8, 'cham_soc', 'sad', '2025-11-17 06:36:15', 1),
(78, 141, 40, 8, 'cham_soc', 'asdasd', '2025-11-17 06:36:33', 1),
(79, 141, 40, 8, 'cham_soc', 'asdas', '2025-11-17 06:36:34', 1),
(80, 141, 40, 8, 'cham_soc', 'asdas', '2025-11-17 06:36:35', 1),
(81, 141, 40, 8, 'cham_soc', 'asdasd', '2025-11-17 06:37:49', 1),
(82, 141, 40, 8, 'cham_soc', 'qqqqqqqqq', '2025-11-17 06:37:51', 1),
(83, 142, 40, 8, 'cham_soc', 'asd', '2025-11-17 06:38:30', 1),
(84, 142, 40, 8, 'cham_soc', 'd', '2025-11-17 06:42:09', 1),
(85, 142, 40, 8, 'cham_soc', 'qqqqqqqqqqqq', '2025-11-17 06:43:16', 1),
(86, 142, 40, 8, 'cham_soc', 'qqqqqqqqqq', '2025-11-17 06:44:01', 1),
(87, 142, 40, 8, 'cham_soc', 'e', '2025-11-17 06:47:16', 1),
(88, 142, 40, 8, 'khach_hang', 'd', '2025-11-17 06:53:46', 1),
(89, 141, 40, 8, 'cham_soc', 'sadas', '2025-11-17 06:54:08', 1),
(90, 142, 40, 8, 'khach_hang', 'asdas', '2025-11-17 06:54:15', 1),
(91, 142, 40, 8, 'cham_soc', 'asdas', '2025-11-17 07:07:30', 1),
(92, 141, 40, 8, 'cham_soc', 'asdas', '2025-11-17 07:07:36', 1),
(93, 141, 40, 8, 'cham_soc', 'aa', '2025-11-17 07:10:00', 1),
(94, 143, 32, 8, 'cham_soc', 'anh yêu vy', '2025-11-17 14:55:08', 1),
(95, 143, 32, 8, 'khach_hang', 'em cũng yêu anh', '2025-11-17 14:55:16', 1),
(96, 145, 32, 8, 'khach_hang', 'qqqqq', '2025-11-17 14:55:37', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `danh_gia`
--
ALTER TABLE `danh_gia`
  ADD PRIMARY KEY (`id_danh_gia`),
  ADD KEY `id_khach_hang` (`id_khach_hang`),
  ADD KEY `id_cham_soc` (`id_cham_soc`);

--
-- Indexes for table `don_hang`
--
ALTER TABLE `don_hang`
  ADD PRIMARY KEY (`id_don_hang`),
  ADD KEY `khach_hang_id` (`id_khach_hang`);

--
-- Indexes for table `khach_hang`
--
ALTER TABLE `khach_hang`
  ADD PRIMARY KEY (`id_khach_hang`),
  ADD UNIQUE KEY `so_dien_thoai` (`so_dien_thoai`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `khieu_nai`
--
ALTER TABLE `khieu_nai`
  ADD PRIMARY KEY (`id_khieu_nai`),
  ADD KEY `id_don_hang` (`id_don_hang`),
  ADD KEY `id_khach_hang` (`id_khach_hang`);

--
-- Indexes for table `nguoi_cham_soc`
--
ALTER TABLE `nguoi_cham_soc`
  ADD PRIMARY KEY (`id_cham_soc`);

--
-- Indexes for table `nhiem_vu`
--
ALTER TABLE `nhiem_vu`
  ADD PRIMARY KEY (`id_nhiem_vu`),
  ADD KEY `fk_don_hang` (`id_don_hang`);

--
-- Indexes for table `tin_nhan`
--
ALTER TABLE `tin_nhan`
  ADD PRIMARY KEY (`id_tin_nhan`),
  ADD KEY `id_don_hang` (`id_don_hang`),
  ADD KEY `id_khach_hang` (`id_khach_hang`),
  ADD KEY `id_cham_soc` (`id_cham_soc`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `danh_gia`
--
ALTER TABLE `danh_gia`
  MODIFY `id_danh_gia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `don_hang`
--
ALTER TABLE `don_hang`
  MODIFY `id_don_hang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `khach_hang`
--
ALTER TABLE `khach_hang`
  MODIFY `id_khach_hang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `khieu_nai`
--
ALTER TABLE `khieu_nai`
  MODIFY `id_khieu_nai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `nguoi_cham_soc`
--
ALTER TABLE `nguoi_cham_soc`
  MODIFY `id_cham_soc` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `nhiem_vu`
--
ALTER TABLE `nhiem_vu`
  MODIFY `id_nhiem_vu` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tin_nhan`
--
ALTER TABLE `tin_nhan`
  MODIFY `id_tin_nhan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `khieu_nai`
--
ALTER TABLE `khieu_nai`
  ADD CONSTRAINT `khieu_nai_ibfk_1` FOREIGN KEY (`id_don_hang`) REFERENCES `don_hang` (`id_don_hang`),
  ADD CONSTRAINT `khieu_nai_ibfk_2` FOREIGN KEY (`id_khach_hang`) REFERENCES `khach_hang` (`id_khach_hang`);

--
-- Constraints for table `nhiem_vu`
--
ALTER TABLE `nhiem_vu`
  ADD CONSTRAINT `fk_don_hang` FOREIGN KEY (`id_don_hang`) REFERENCES `don_hang` (`id_don_hang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tin_nhan`
--
ALTER TABLE `tin_nhan`
  ADD CONSTRAINT `tin_nhan_ibfk_1` FOREIGN KEY (`id_don_hang`) REFERENCES `don_hang` (`id_don_hang`),
  ADD CONSTRAINT `tin_nhan_ibfk_2` FOREIGN KEY (`id_khach_hang`) REFERENCES `khach_hang` (`id_khach_hang`),
  ADD CONSTRAINT `tin_nhan_ibfk_3` FOREIGN KEY (`id_cham_soc`) REFERENCES `nguoi_cham_soc` (`id_cham_soc`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
