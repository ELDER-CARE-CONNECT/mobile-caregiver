<?php
// backend/api_caregiver.php

// 1. Xóa sạch bộ đệm để tránh lỗi "Unexpected token <"
if (ob_get_length()) ob_clean(); 

// 2. Chỉ start session nếu chưa có (Tránh xung đột với Gateway)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, '/');
    session_start();
}

header('Content-Type: application/json; charset=utf-8');
require_once 'db_connect.php'; 

// 3. Kiểm tra đăng nhập (Dùng id_khach_hang cho chuẩn session)
if (!isset($_SESSION['id_khach_hang'])) {
    // Fallback kiểm tra số điện thoại nếu hệ thống cũ dùng
    if (!isset($_SESSION['so_dien_thoai'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Lỗi xác thực: Vui lòng đăng nhập lại.']);
        exit;
    }
}

try {
    $pdo = get_pdo_connection();
    $action = $_GET['action'] ?? '';

    // --- ACTION: LIST ALL ---
    if ($action === 'list_all') {
        // SỬA LỖI DATABASE: Dùng sub-query để đếm đơn hàng thay vì gọi cột don_da_nhan không tồn tại
        $sql = "SELECT 
                    ncs.id_cham_soc, 
                    ncs.ho_ten, 
                    ncs.hinh_anh, 
                    ncs.danh_gia_tb, 
                    ncs.kinh_nghiem, 
                    ncs.tong_tien_kiem_duoc,
                    (SELECT COUNT(*) FROM don_hang dh WHERE dh.id_nguoi_cham_soc = ncs.id_cham_soc) as don_da_nhan
                FROM nguoi_cham_soc ncs";
        
        $stmt = $pdo->query($sql);
        $caregivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['success' => true, 'data' => $caregivers]);
        exit;
    }

    // --- ACTION: LIST FEATURED ---
    if ($action === 'list_featured') {
        $sql = "SELECT 
                    ncs.id_cham_soc, 
                    ncs.ho_ten, 
                    ncs.hinh_anh, 
                    ncs.danh_gia_tb, 
                    ncs.kinh_nghiem, 
                    ncs.tong_tien_kiem_duoc,
                    (SELECT COUNT(*) FROM don_hang dh WHERE dh.id_nguoi_cham_soc = ncs.id_cham_soc) as don_da_nhan
                FROM nguoi_cham_soc ncs
                ORDER BY ncs.danh_gia_tb DESC 
                LIMIT 3";
        $stmt = $pdo->query($sql);
        $caregivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $caregivers]);
        exit;
    }

    // --- ACTION: GET DETAILS ---
    if ($action === 'get_details' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        
        // Lấy thông tin chi tiết + đếm số đơn
        $stmt_main = $pdo->prepare("
            SELECT 
                ncs.*,
                (SELECT COUNT(*) FROM don_hang dh WHERE dh.id_nguoi_cham_soc = ncs.id_cham_soc) as don_da_nhan
            FROM nguoi_cham_soc ncs 
            WHERE ncs.id_cham_soc = ?
        ");
        $stmt_main->execute([$id]);
        $caregiver = $stmt_main->fetch(PDO::FETCH_ASSOC);

        if (!$caregiver) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy người chăm sóc.']);
            exit;
        }

        // Lấy đánh giá
        $stmt_reviews = $pdo->prepare("
            SELECT dg.*, kh.ten_khach_hang 
            FROM danh_gia dg 
            LEFT JOIN khach_hang kh ON dg.id_khach_hang = kh.id_khach_hang 
            WHERE dg.id_cham_soc = ?
            ORDER BY dg.ngay_danh_gia DESC
        ");
        $stmt_reviews->execute([$id]);
        $reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

        // Lấy người chăm sóc liên quan
        $stmt_related = $pdo->prepare("
            SELECT id_cham_soc, ho_ten, hinh_anh, danh_gia_tb, kinh_nghiem, tong_tien_kiem_duoc 
            FROM nguoi_cham_soc 
            WHERE id_cham_soc != ?
            LIMIT 4
        ");
        $stmt_related->execute([$id]);
        $related = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'caregiver' => $caregiver,
            'reviews' => $reviews,
            'related' => $related
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ.']);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
    exit;
}
?>