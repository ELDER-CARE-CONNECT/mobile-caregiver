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

    if ($action === 'submit_rating' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_cham_soc = intval($_POST['id_cs'] ?? 0);
        $id_don_hang = intval($_POST['id_dh'] ?? 0);
        $rating = intval($_POST['rating'] ?? 0);
        $comment = trim($_POST['comment'] ?? '');

        if ($rating < 1 || $rating > 5) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Vui lòng chọn số sao.']);
            exit;
        }

        $stmt_check = $pdo->prepare("
            SELECT id_don_hang FROM don_hang 
            WHERE id_don_hang = ? AND id_khach_hang = ? AND id_nguoi_cham_soc = ? AND trang_thai = 'đã hoàn thành'
        ");
        $stmt_check->execute([$id_don_hang, $id_khach_hang, $id_cham_soc]);
        if (!$stmt_check->fetch()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền đánh giá đơn hàng này.']);
            exit;
        }
        $sql_insert = "INSERT INTO danh_gia (id_khach_hang, id_cham_soc, so_sao, nhan_xet, ngay_danh_gia) 
                       VALUES (?, ?, ?, ?, NOW())";
        $pdo->prepare($sql_insert)->execute([$id_khach_hang, $id_cham_soc, $rating, $comment]);
        
        $sql_avg = "UPDATE nguoi_cham_soc SET danh_gia_tb = 
                        (SELECT AVG(so_sao) FROM danh_gia WHERE id_cham_soc = ?)
                    WHERE id_cham_soc = ?";
        $pdo->prepare($sql_avg)->execute([$id_cham_soc, $id_cham_soc]);

        echo json_encode(['success' => true, 'message' => 'Đánh giá của bạn đã được ghi nhận. Cảm ơn bạn!']);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ cho Rating API.']);

} catch (\PDOException $e) {
    http_response_code(500);
    if ($e->errorInfo[1] == 1062) { 
         echo json_encode(['success' => false, 'message' => 'Bạn đã đánh giá đơn hàng này rồi.']);
    } else {
         echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
    }
    exit;
}
?>
