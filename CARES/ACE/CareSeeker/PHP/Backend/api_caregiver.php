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

// 3. Kiểm tra đăng nhập
if (!isset($_SESSION['id_khach_hang'])) {
    if (!isset($_SESSION['so_dien_thoai'])) {
        // Nếu gọi qua Gateway đã check auth thì có thể bỏ qua, 
        // nhưng để an toàn cứ check lại hoặc return 401
        // http_response_code(401);
        // echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập.']);
        // exit;
    }
}

try {
    // Kết nối DB (dùng MySQLi $conn từ db_connect.php)
    global $conn;

    $action = $_GET['action'] ?? '';

    // --- ACTION: LIST ALL ---
    if ($action === 'list_all') {
        // SỬA LỖI QUAN TRỌNG: Dùng sub-query đếm đơn hàng
        $sql = "SELECT 
                    ncs.id_cham_soc, 
                    ncs.ho_ten, 
                    ncs.hinh_anh, 
                    ncs.danh_gia_tb, 
                    ncs.kinh_nghiem, 
                    ncs.tong_tien_kiem_duoc,
                    (SELECT COUNT(*) FROM don_hang dh WHERE dh.id_nguoi_cham_soc = ncs.id_cham_soc) as don_da_nhan
                FROM nguoi_cham_soc ncs";
        
        $result = $conn->query($sql);
        
        if ($result) {
            $caregivers = [];
            while ($row = $result->fetch_assoc()) {
                $caregivers[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $caregivers]);
        } else {
            // Báo lỗi SQL cụ thể để debug (chỉ dùng khi dev)
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn: ' . $conn->error]);
        }
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
        $result = $conn->query($sql);
        
        if ($result) {
            $caregivers = [];
            while ($row = $result->fetch_assoc()) {
                $caregivers[] = $row;
            }
            echo json_encode(['success' => true, 'data' => $caregivers]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn: ' . $conn->error]);
        }
        exit;
    }

    // --- ACTION: GET DETAILS ---
    if ($action === 'get_details' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        
        // Lấy thông tin chi tiết + đếm số đơn
        $stmt_main = $conn->prepare("
            SELECT 
                ncs.*,
                (SELECT COUNT(*) FROM don_hang dh WHERE dh.id_nguoi_cham_soc = ncs.id_cham_soc) as don_da_nhan
            FROM nguoi_cham_soc ncs 
            WHERE ncs.id_cham_soc = ?
        ");
        $stmt_main->bind_param("i", $id);
        $stmt_main->execute();
        $caregiver = $stmt_main->get_result()->fetch_assoc();
        $stmt_main->close();

        if (!$caregiver) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy người chăm sóc.']);
            exit;
        }

        // Lấy đánh giá
        $stmt_reviews = $conn->prepare("
            SELECT dg.*, kh.ten_khach_hang, kh.hinh_anh as avatar_kh
            FROM danh_gia dg 
            LEFT JOIN khach_hang kh ON dg.id_khach_hang = kh.id_khach_hang 
            WHERE dg.id_cham_soc = ?
            ORDER BY dg.ngay_danh_gia DESC
        ");
        $stmt_reviews->bind_param("i", $id);
        $stmt_reviews->execute();
        $res_reviews = $stmt_reviews->get_result();
        $reviews = [];
        while ($row = $res_reviews->fetch_assoc()) {
            $reviews[] = $row;
        }
        $stmt_reviews->close();

        // Lấy người chăm sóc liên quan
        $stmt_related = $conn->prepare("
            SELECT id_cham_soc, ho_ten, hinh_anh, danh_gia_tb, kinh_nghiem, tong_tien_kiem_duoc 
            FROM nguoi_cham_soc 
            WHERE id_cham_soc != ?
            LIMIT 4
        ");
        $stmt_related->bind_param("i", $id);
        $stmt_related->execute();
        $res_related = $stmt_related->get_result();
        $related = [];
        while ($row = $res_related->fetch_assoc()) {
            $related[] = $row;
        }
        $stmt_related->close();

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

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
    exit;
}
?>