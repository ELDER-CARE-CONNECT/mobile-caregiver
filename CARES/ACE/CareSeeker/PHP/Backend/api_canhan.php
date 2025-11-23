<?php
// File: Backend/api_canhan.php

// Lấy kết nối từ Gateway
global $conn;

// 1. Kiểm tra Auth
if (!isset($_SESSION['id_khach_hang'])) {
    sendResponse(401, ['success' => false, 'message' => 'Vui lòng đăng nhập.']);
}

$id_khach_hang = $_SESSION['id_khach_hang'];
$method = $GLOBALS['api_method'];

if ($method === 'GET') {
    // 2. Lấy thông tin cá nhân
    $stmt = $conn->prepare("SELECT * FROM khach_hang WHERE id_khach_hang = ?");
    $stmt->bind_param("i", $id_khach_hang);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$profile) {
        sendResponse(404, ['success' => false, 'message' => 'Không tìm thấy hồ sơ.']);
    }

    // 3. Lấy danh sách đơn hàng VÀ thông tin phản hồi khiếu nại (NẾU CÓ)
    // SỬA ĐỔI: Lấy thêm k.noi_dung và k.phan_hoi
    $sql_orders = "
        SELECT 
            d.id_don_hang, 
            d.ngay_dat, 
            d.trang_thai,
            k.noi_dung AS noi_dung_kn,        -- Lấy nội dung khách đã khiếu nại
            k.phan_hoi AS loi_phan_hoi_admin, -- Lấy nội dung admin trả lời
            k.trang_thai AS trang_thai_kn     -- Trạng thái khiếu nại
        FROM don_hang d
        LEFT JOIN khieu_nai k ON d.id_don_hang = k.id_don_hang 
        WHERE d.id_khach_hang = ? 
        AND (d.trang_thai = 'đã hoàn thành' OR d.trang_thai = 'đã hủy')
        ORDER BY d.ngay_dat DESC
    ";

    $stmt_orders = $conn->prepare($sql_orders);
    // Chỉ cần bind 1 tham số id_khach_hang vì logic JOIN đã xử lý liên kết
    $stmt_orders->bind_param("i", $id_khach_hang); 
    $stmt_orders->execute();
    $result_orders = $stmt_orders->get_result();
    
    $orders_for_complaint = [];
    while ($row = $result_orders->fetch_assoc()) {
        $orders_for_complaint[] = $row;
    }
    $stmt_orders->close();

    // 4. Trả về kết quả
    sendResponse(200, [
        'success' => true,
        'profile' => $profile,
        'orders_for_complaint' => $orders_for_complaint
    ]);

} else {
    sendResponse(405, ['success' => false, 'message' => 'Phương thức không hỗ trợ.']);
}
?>