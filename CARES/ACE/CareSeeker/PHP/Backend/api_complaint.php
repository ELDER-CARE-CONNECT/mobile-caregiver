<?php
// Session đã được start bởi Gateway
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once 'db_connect.php';
$pdo = get_pdo_connection();

if (!isset($_SESSION['id_khach_hang'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Lỗi xác thực: Vui lòng đăng nhập lại.']);
    exit;
}
$id_khach_hang = $_SESSION['id_khach_hang'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

try {
    if ($action === 'submit_complaint' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_don_hang = intval($_POST['id_don_hang'] ?? 0);
        $noi_dung = trim($_POST['noi_dung'] ?? '');

        if (empty($id_don_hang) || empty($noi_dung)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Thiếu ID đơn hàng hoặc nội dung khiếu nại.']);
            exit;
        }
        $stmt_check = $pdo->prepare("
            SELECT id_don_hang FROM don_hang 
            WHERE id_don_hang = ? AND id_khach_hang = ? AND trang_thai IN ('đã hoàn thành', 'Đã hủy', 'đã hủy')
        ");
        $stmt_check->execute([$id_don_hang, $id_khach_hang]);
        if (!$stmt_check->fetch()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền khiếu nại đơn hàng này.']);
            exit;
        }

        $sql_insert = "INSERT INTO khieu_nai (id_don_hang, id_khach_hang, noi_dung, ngay_gui, trang_thai) 
                       VALUES (?, ?, ?, NOW(), 'Chờ xử lý')
                       ON DUPLICATE KEY UPDATE noi_dung = VALUES(noi_dung), trang_thai = 'Chờ xử lý'";
        $pdo->prepare($sql_insert)->execute([$id_don_hang, $id_khach_hang, $noi_dung]);

        echo json_encode(['success' => true, 'message' => 'Gửi khiếu nại thành công.']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ cho Complaint API.']);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
    exit;
}
?>