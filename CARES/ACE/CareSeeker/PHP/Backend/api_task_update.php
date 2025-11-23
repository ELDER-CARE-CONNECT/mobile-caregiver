<?php
// File: api_task_update.php

// 1. Lấy biến kết nối từ biến toàn cục (Cách an toàn nhất khi include)
$conn = isset($GLOBALS['conn']) ? $GLOBALS['conn'] : null;

// Nếu vẫn không có kết nối, thử include lại (phòng hờ)
if (!$conn) {
    require_once 'db_connect.php';
    $conn = $GLOBALS['conn'];
}

if (!$conn) {
    sendResponse(500, ['success' => false, 'message' => 'Lỗi: Mất kết nối CSDL (Conn is null)']);
}

// 2. Lấy phương thức và dữ liệu
$method = $GLOBALS['api_method'];
$input = $GLOBALS['api_input'];

// 3. Kiểm tra phương thức POST
if ($method !== 'POST') {
    sendResponse(405, ['success' => false, 'message' => 'Phương thức không hợp lệ']);
}

// 4. Lấy tham số ID và Action
$id_nhiem_vu = isset($input['id_nhiem_vu']) ? intval($input['id_nhiem_vu']) : 0;
$action = $input['action'] ?? '';

// 5. Validate dữ liệu
if ($id_nhiem_vu <= 0) {
    sendResponse(400, ['success' => false, 'message' => 'ID nhiệm vụ không hợp lệ']);
}

if ($action !== 'hoan_thanh_nhiem_vu') {
    sendResponse(400, ['success' => false, 'message' => 'Hành động không hợp lệ']);
}

// 6. Thực hiện cập nhật SQL
$stmt = $conn->prepare("UPDATE nhiem_vu SET trang_thai_nhiem_vu = 'đã hoàn thành' WHERE id_nhiem_vu = ?");

if (!$stmt) {
    sendResponse(500, ['success' => false, 'message' => 'Lỗi SQL Prepare: ' . $conn->error]);
}

$stmt->bind_param("i", $id_nhiem_vu);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        sendResponse(200, ['success' => true, 'message' => 'Đã cập nhật thành công']);
    } else {
        // Trường hợp ID đúng nhưng trạng thái đã là "đã hoàn thành" từ trước
        sendResponse(200, ['success' => true, 'message' => 'Nhiệm vụ đã được cập nhật trước đó']);
    }
} else {
    sendResponse(500, ['success' => false, 'message' => 'Lỗi thực thi SQL: ' . $stmt->error]);
}

$stmt->close();
?>