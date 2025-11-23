<?php
// api_order_details.php

// Session đã được start bởi Gateway
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Lấy kết nối từ Gateway
global $conn;

if (!isset($_SESSION['id_khach_hang'])) {
    sendResponse(401, ['success' => false, 'message' => 'Vui lòng đăng nhập.']);
}

$id_khach_hang = $_SESSION['id_khach_hang'];
$method = $GLOBALS['api_method'];
$input = $GLOBALS['api_input'];

// Lấy action từ GET hoặc POST
$action = $_GET['action'] ?? $input['action'] ?? '';

// --- XỬ LÝ HỦY ĐƠN HÀNG (POST) ---
if ($method === 'POST' && $action === 'cancel_order') {
    $id_don_hang_to_cancel = intval($input['id_don_hang'] ?? 0);

    if ($id_don_hang_to_cancel <= 0) {
        sendResponse(400, ['success' => false, 'message' => 'ID đơn hàng không hợp lệ.']);
    }

    // Kiểm tra trạng thái
    $stmt = $conn->prepare("SELECT trang_thai FROM don_hang WHERE id_don_hang = ? AND id_khach_hang = ?");
    $stmt->bind_param("ii", $id_don_hang_to_cancel, $id_khach_hang);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    $order_status = $row['trang_thai'] ?? '';

    if (strtolower(trim($order_status)) === 'chờ xác nhận') {
        $stmt_update = $conn->prepare("UPDATE don_hang SET trang_thai = 'đã hủy' WHERE id_don_hang = ?");
        $stmt_update->bind_param("i", $id_don_hang_to_cancel);
        
        if ($stmt_update->execute()) {
            sendResponse(200, ['success' => true, 'message' => 'Đã hủy đơn hàng thành công.']);
        } else {
            sendResponse(500, ['success' => false, 'message' => 'Lỗi cập nhật: ' . $conn->error]);
        }
        $stmt_update->close();
    } else {
        sendResponse(403, ['success' => false, 'message' => 'Không thể hủy đơn hàng (Chỉ hủy được khi "Chờ xác nhận").']);
    }
}

// --- XỬ LÝ LẤY CHI TIẾT ĐƠN HÀNG (GET) ---
if ($method === 'GET' && $action === 'get_details') {
    $id_don_hang = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // 1. Lấy thông tin đơn hàng
    $sql = "
        SELECT 
            dh.*, 
            kh.ten_khach_hang, kh.so_dien_thoai, 
            TRIM(CONCAT_WS(', ', kh.ten_duong, kh.phuong_xa, kh.tinh_thanh)) AS dia_chi_kh,
            ncs.ho_ten AS ten_cham_soc, ncs.hinh_anh AS hinh_anh_cham_soc, ncs.id_cham_soc AS caregiver_id
        FROM don_hang dh
        LEFT JOIN khach_hang kh ON dh.id_khach_hang = kh.id_khach_hang
        LEFT JOIN nguoi_cham_soc ncs ON dh.id_nguoi_cham_soc = ncs.id_cham_soc
        WHERE dh.id_don_hang = ? AND dh.id_khach_hang = ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_don_hang, $id_khach_hang);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$order) {
        sendResponse(404, ['success' => false, 'message' => 'Không tìm thấy đơn hàng.']);
    }

    // 2. Kiểm tra đánh giá
    $is_rated = false;
    if (strtolower(trim($order['trang_thai'])) === 'đã hoàn thành' && $order['caregiver_id']) {
        $stmt_check = $conn->prepare("SELECT 1 FROM danh_gia WHERE id_khach_hang = ? AND id_cham_soc = ? LIMIT 1");
        $stmt_check->bind_param("ii", $id_khach_hang, $order['caregiver_id']);
        $stmt_check->execute();
        if ($stmt_check->get_result()->num_rows > 0) {
            $is_rated = true;
        }
        $stmt_check->close();
    }
    
    // 3. Lấy danh sách nhiệm vụ
    $services = [];
    
    // Ưu tiên lấy từ bảng NHIEM_VU
    $stmt_tasks = $conn->prepare("SELECT ten_nhiem_vu, trang_thai_nhiem_vu FROM nhiem_vu WHERE id_don_hang = ?");
    $stmt_tasks->bind_param("i", $id_don_hang);
    $stmt_tasks->execute();
    $res_tasks = $stmt_tasks->get_result();
    
    while ($task = $res_tasks->fetch_assoc()) {
        $services[] = $task;
    }
    $stmt_tasks->close();

    // Nếu bảng nhiệm vụ trống (đơn cũ), lấy từ JSON
    if (empty($services) && !empty($order['ten_nhiem_vu'])) {
        $tasks_list = json_decode($order['ten_nhiem_vu'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $tasks_list = preg_split("/\r\n|\n|\r/", $order['ten_nhiem_vu']);
        }
        
        if (is_array($tasks_list)) {
            $common_status = $order['trang_thai_nhiem_vu'] ?? 'chờ xác nhận';
            foreach ($tasks_list as $t) {
                if (trim($t)) {
                    $services[] = [
                        'ten_nhiem_vu' => trim($t, ' "'),
                        'trang_thai_nhiem_vu' => $common_status
                    ];
                }
            }
        }
    }

    sendResponse(200, [
        'success' => true,
        'order' => $order,
        'services' => $services,
        'is_rated' => $is_rated
    ]);
}

sendResponse(400, ['success' => false, 'message' => 'Hành động không hợp lệ.']);
?>