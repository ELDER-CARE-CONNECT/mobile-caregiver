<?php
// File: ACE/Caregiver/Backend/Canhan/api_profile.php

// 1. Xóa bộ đệm và tắt hiển thị lỗi HTML để tránh hỏng JSON
if (ob_get_length()) ob_clean();
ini_set('display_errors', 0);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');

try {
    // 2. Start Session
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params(0, '/');
        session_start();
    }

    // 3. TÌM FILE KẾT NỐI (Quan trọng)
    // Dựa vào hình ảnh: api_profile.php nằm ở Backend/Canhan
    // connect.php nằm ở Fontend/connect.php
    // Đường dẫn: Lùi ra Backend (../) -> Lùi ra Caregiver (../../) -> Vào Fontend -> connect.php
    
    $path_local = __DIR__ . '/../../Fontend/connect.php'; 
    $path_admin = __DIR__ . '/../../../Admin/config/connect.php';

    $conn = null;

    if (file_exists($path_local)) {
        include_once $path_local;
    } elseif (file_exists($path_admin)) {
        include_once $path_admin;
    } else {
        throw new Exception("Không tìm thấy file connect.php (Đã thử: $path_local)");
    }

    if (!function_exists('connectdb')) {
         // Nếu file connect.php không có hàm connectdb, tự tạo kết nối
         $conn = new mysqli("localhost", "root", "", "sanpham");
         $conn->set_charset("utf8");
         if ($conn->connect_error) throw new Exception("Kết nối thất bại: " . $conn->connect_error);
    } else {
        $conn = connectdb();
    }

    // 4. Kiểm tra đăng nhập
    // Fix: Nếu có SĐT mà chưa có ID (do login chỉ lưu SĐT), tự lấy ID
    if (!isset($_SESSION['id_cham_soc']) && isset($_SESSION['so_dien_thoai'])) {
        $stmt = $conn->prepare("SELECT id_cham_soc FROM nguoi_cham_soc WHERE so_dien_thoai = ?");
        $stmt->bind_param("s", $_SESSION['so_dien_thoai']);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $_SESSION['id_cham_soc'] = $row['id_cham_soc'];
        }
    }

    if (!isset($_SESSION['id_cham_soc'])) {
        throw new Exception("Bạn chưa đăng nhập (Session ID trống).");
    }

    $id = $_SESSION['id_cham_soc'];

    // 5. Lấy dữ liệu
    $stmt = $conn->prepare("SELECT * FROM nguoi_cham_soc WHERE id_cham_soc = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) throw new Exception("Không tìm thấy thông tin người dùng.");

    // Tính đánh giá
    $stmt2 = $conn->prepare("SELECT COUNT(*) as sl, SUM(so_sao) as tong FROM danh_gia WHERE id_cham_soc = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $rating = $stmt2->get_result()->fetch_assoc();
    
    $danh_gia_tb = ($rating['sl'] > 0) ? round($rating['tong'] / $rating['sl'], 2) : 5;

    echo json_encode([
        "status" => "success",
        "user" => $user,
        "danh_gia_tb" => $danh_gia_tb
    ]);

} catch (Exception $e) {
    http_response_code(500); // Báo lỗi 500 để JS bắt được
    echo json_encode(["error" => $e->getMessage()]);
}
?>