<?php
// api_order_details.php

// Session đã được start bởi Gateway
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once 'db_connect.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'khach_hang' || !isset($_SESSION['id_khach_hang'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Lỗi xác thực: Vui lòng đăng nhập.']);
    exit();
}

try {
    $pdo = get_pdo_connection();
    $id_khach_hang = $_SESSION['id_khach_hang'];
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    // --- XỬ LÝ HỦY ĐƠN HÀNG (Giữ nguyên) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'cancel_order') {
        $id_don_hang_to_cancel = intval($_POST['id_don_hang'] ?? 0);

        if ($id_don_hang_to_cancel <= 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID đơn hàng không hợp lệ.']);
            exit;
        }

        $stmt_check = $pdo->prepare("SELECT trang_thai FROM don_hang WHERE id_don_hang = ? AND id_khach_hang = ?");
        $stmt_check->execute([$id_don_hang_to_cancel, $id_khach_hang]);
        $order_status = $stmt_check->fetchColumn();

        if ($order_status && strtolower(trim($order_status)) === 'chờ xác nhận') {
            $stmt_update = $pdo->prepare("UPDATE don_hang SET trang_thai = 'đã hủy' WHERE id_don_hang = ?");
            $stmt_update->execute([$id_don_hang_to_cancel]);
            echo json_encode(['success' => true, 'message' => 'Đã hủy đơn hàng thành công.']);
        } else {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Không thể hủy đơn hàng (Chỉ hủy được khi "Chờ xác nhận").']);
        }
        exit;
    }

    // --- XỬ LÝ LẤY CHI TIẾT ĐƠN HÀNG ---
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get_details') {
        $id_don_hang = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $stmt = $pdo->prepare("
            SELECT 
                dh.*, 
                kh.ten_khach_hang, kh.so_dien_thoai, 
                TRIM(CONCAT_WS(', ', kh.ten_duong, kh.phuong_xa, kh.tinh_thanh)) AS dia_chi_kh,
                ncs.ho_ten AS ten_cham_soc, ncs.hinh_anh AS hinh_anh_cham_soc, ncs.id_cham_soc AS caregiver_id
            FROM don_hang dh
            LEFT JOIN khach_hang kh ON dh.id_khach_hang = kh.id_khach_hang
            LEFT JOIN nguoi_cham_soc ncs ON dh.id_nguoi_cham_soc = ncs.id_cham_soc
            WHERE dh.id_don_hang = ? AND dh.id_khach_hang = ?
        ");
        $stmt->execute([$id_don_hang, $id_khach_hang]); 
        $order = $stmt->fetch();

        if (!$order) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy đơn hàng hoặc bạn không có quyền xem.']);
            exit;
        }

        $is_rated = false;
        if (strtolower(trim($order['trang_thai'])) === 'đã hoàn thành' && $order['caregiver_id']) {
            $stmt_check_review = $pdo->prepare("SELECT 1 FROM danh_gia WHERE id_khach_hang = ? AND id_cham_soc = ? LIMIT 1");
            $stmt_check_review->execute([$id_khach_hang, $order['caregiver_id']]); 
            if ($stmt_check_review->fetchColumn()) {
                $is_rated = true;
            }
        }
        
        // --- LẤY DANH SÁCH NHIỆM VỤ ---
        $services = [];
        
        // 1. Ưu tiên lấy từ bảng NHIEM_VU
        $stmt_tasks = $pdo->prepare("SELECT ten_nhiem_vu, trang_thai_nhiem_vu FROM nhiem_vu WHERE id_don_hang = ?");
        $stmt_tasks->execute([$id_don_hang]);
        $db_tasks = $stmt_tasks->fetchAll(PDO::FETCH_ASSOC);

        if (count($db_tasks) > 0) {
            // Nếu có dữ liệu trong bảng riêng, dùng nó
            $services = $db_tasks;
        } else {
            // 2. Nếu bảng riêng trống (đơn cũ), lấy từ JSON trong bảng DON_HANG
            if (!empty($order['ten_nhiem_vu'])) {
                $tasks_list = json_decode($order['ten_nhiem_vu'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $tasks_list = preg_split("/\r\n|\n|\r/", $order['ten_nhiem_vu']);
                }
                $tasks_list = array_filter(array_map('trim', $tasks_list));
                
                // Lấy trạng thái chung của đơn hàng làm trạng thái nhiệm vụ giả định
                $common_status = $order['trang_thai_nhiem_vu'] ?? 'chờ xác nhận';
                
                foreach ($tasks_list as $task) {
                    $services[] = [
                        'ten_nhiem_vu' => trim($task, ' "'),
                        'trang_thai_nhiem_vu' => $common_status // Dùng chung trạng thái
                    ];
                }
            }
        }

        echo json_encode([
            'success' => true,
            'order' => $order,
            'services' => $services,
            'is_rated' => $is_rated
        ]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ cho API này.']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Lỗi CSDL: " . $e->getMessage()]);
}
?>