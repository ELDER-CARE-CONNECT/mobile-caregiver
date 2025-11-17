<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');

include '../../../Admin/connect.php';

// KIỂM TRA ĐĂNG NHẬP NGƯỜI CHĂM SÓC
if (!isset($_SESSION['id_cham_soc'])) {
    http_response_code(403);
    echo json_encode(["error" => "Bạn chưa đăng nhập!"]);
    exit;
}

// Lấy ID từ session
$id = $_SESSION['id_cham_soc'];

// Lấy thông tin người chăm sóc
$stmt = $conn->prepare("SELECT * FROM nguoi_cham_soc WHERE id_cham_soc = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    http_response_code(404);
    echo json_encode(["error" => "Không tìm thấy thông tin người dùng."]);
    exit;
}

// --- TÍNH ĐÁNH GIÁ TRUNG BÌNH ---
$stmt2 = $conn->prepare("SELECT COUNT(*) AS total_votes, SUM(so_sao) AS total_stars FROM danh_gia WHERE id_cham_soc = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$rating_data = $result2->fetch_assoc();

$so_luong_danh_gia = (int)$rating_data['total_votes'];
$tong_sao = (int)$rating_data['total_stars'];

// Tính trung bình, tránh chia cho 0
$danh_gia_tb = $so_luong_danh_gia > 0 ? round($tong_sao / $so_luong_danh_gia, 2) : 0;

// Trả JSON
echo json_encode([
    "user" => $user,
    "danh_gia_tb" => $danh_gia_tb
]);
