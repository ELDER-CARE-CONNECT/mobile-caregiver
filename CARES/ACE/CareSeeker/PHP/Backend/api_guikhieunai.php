<?php
// File: Backend/api_guikhieunai.php

// Lấy kết nối từ Gateway
global $conn;

if (!isset($_SESSION['id_khach_hang'])) {
    sendResponse(401, ['success' => false, 'message' => 'Vui lòng đăng nhập.']);
}

$id_khach_hang = $_SESSION['id_khach_hang'];
$input = $GLOBALS['api_input']; // Lấy dữ liệu từ Gateway (hỗ trợ cả JSON và Form)

// Lấy dữ liệu (Ưu tiên từ input json/post, nếu không có thì lấy từ $_POST thuần)
$id_don_hang = $input['id_don_hang'] ?? $_POST['id_don_hang'] ?? 0;
$noi_dung = trim($input['noi_dung'] ?? $_POST['noi_dung'] ?? '');

if (empty($id_don_hang) || empty($noi_dung)) {
    sendResponse(400, ['success' => false, 'message' => 'Thiếu thông tin khiếu nại.']);
}

// 1. Kiểm tra quyền khiếu nại (Đơn hàng phải của khách này và đã hoàn thành/hủy)
$stmt_check = $conn->prepare("
    SELECT id_don_hang 
    FROM don_hang 
    WHERE id_don_hang = ? 
    AND id_khach_hang = ? 
    AND (trang_thai = 'đã hoàn thành' OR trang_thai = 'đã hủy')
");
$stmt_check->bind_param("ii", $id_don_hang, $id_khach_hang);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows === 0) {
    $stmt_check->close();
    sendResponse(403, ['success' => false, 'message' => 'Bạn không thể khiếu nại đơn hàng này.']);
}
$stmt_check->close();

// 2. Thực hiện Insert hoặc Update khiếu nại
// Sử dụng INSERT ... ON DUPLICATE KEY UPDATE để tránh lỗi trùng lặp nếu gửi lại
$sql_insert = "
    INSERT INTO khieu_nai (id_don_hang, id_khach_hang, noi_dung, ngay_gui, trang_thai) 
    VALUES (?, ?, ?, NOW(), 'Chờ xử lý')
    ON DUPLICATE KEY UPDATE 
        noi_dung = VALUES(noi_dung), 
        ngay_gui = NOW(), 
        trang_thai = 'Chờ xử lý'
";

$stmt_insert = $conn->prepare($sql_insert);
$stmt_insert->bind_param("iis", $id_don_hang, $id_khach_hang, $noi_dung);

if ($stmt_insert->execute()) {
    sendResponse(200, ['success' => true, 'message' => 'Gửi khiếu nại thành công!']);
} else {
    sendResponse(500, ['success' => false, 'message' => 'Lỗi hệ thống: ' . $conn->error]);
}

$stmt_insert->close();
?>