<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['profile']) || empty($_SESSION['profile']['id_khach_hang'])) {
    http_response_code(401); 
    echo json_encode(['success' => false, 'message' => 'Lỗi xác thực: Bạn chưa đăng nhập.']);
    exit;
}
$id_khach_hang = $_SESSION['profile']['id_khach_hang'];

require_once 'db_connect.php'; 
try {
    $pdo = get_pdo_connection();
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); 
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
    exit;
}

$id_don_hang = $_POST['id_don_hang'] ?? 0;
$noi_dung = trim($_POST['noi_dung'] ?? '');

if (empty($id_don_hang) || empty($noi_dung)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Vui lòng cung cấp ID đơn hàng và nội dung khiếu nại.']);
    exit;
}

try {
    $stmt_check = $pdo->prepare("
        SELECT id_don_hang 
        FROM don_hang 
        WHERE id_don_hang = ? 
        AND id_khach_hang = ? 
        AND (trang_thai = 'đã hoàn thành' OR trang_thai = 'đã hủy' OR trang_thai = 'Đã hủy')
    ");
    $stmt_check->execute([$id_don_hang, $id_khach_hang]);
    
    if ($stmt_check->fetch() === false) {
        http_response_code(403); 
        echo json_encode(['success' => false, 'message' => 'Bạn không có quyền khiếu nại đơn hàng này (có thể đơn hàng không tồn tại, không phải của bạn, hoặc chưa hoàn thành).']);
        exit;
    }
    $sql_insert = "
        INSERT INTO khieu_nai (id_don_hang, id_khach_hang, noi_dung, ngay_gui, trang_thai) 
        VALUES (?, ?, ?, NOW(), 'Chờ xử lý')
        ON DUPLICATE KEY UPDATE 
            noi_dung = VALUES(noi_dung), 
            ngay_gui = NOW(), 
            trang_thai = 'Chờ xử lý'
    ";
    
    $stmt_insert = $pdo->prepare($sql_insert);
    $stmt_insert->execute([$id_don_hang, $id_khach_hang, $noi_dung]);

    echo json_encode(['success' => true, 'message' => 'Gửi khiếu nại thành công!']);
    exit;

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi CSDL khi gửi khiếu nại: ' . $e->getMessage()]);
    exit;
}
?>