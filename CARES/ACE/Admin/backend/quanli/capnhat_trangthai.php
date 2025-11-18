<?php
header('Content-Type: application/json; charset=utf-8');
include '../config/connect.php';

// Kết nối CSDL
$conn = connectdb();
if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Không kết nối DB'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Lấy dữ liệu POST
$id = isset($_POST['id_don_hang']) ? intval($_POST['id_don_hang']) : 0;
$status = isset($_POST['trang_thai']) ? trim($_POST['trang_thai']) : '';

// Kiểm tra dữ liệu
if ($id <= 0 || $status === '') {
    echo json_encode(['status' => 'error', 'message' => 'Thiếu dữ liệu hoặc dữ liệu không hợp lệ'], JSON_UNESCAPED_UNICODE);
    exit;
}

// Cập nhật trạng thái
$stmt = $conn->prepare("UPDATE don_hang SET trang_thai = ? WHERE id_don_hang = ?");
$stmt->bind_param("si", $status, $id);

if ($stmt->execute()) {
    echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['status' => 'error', 'message' => $conn->error], JSON_UNESCAPED_UNICODE);
}

$stmt->close();
$conn->close();
