<?php
session_start();
header('Content-Type: application/json');
require_once 'db_connect.php'; 
$pdo = get_pdo_connection();

if (!isset($_SESSION['id_khach_hang'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Lỗi xác thực: Vui lòng đăng nhập lại.']);
    exit;
}
$id_khach_hang = $_SESSION['id_khach_hang'];

try {
    $stmt_kh = $pdo->prepare("SELECT ten_khach_hang FROM khach_hang WHERE id_khach_hang = ?");
    $stmt_kh->execute([$id_khach_hang]);
    $user_info = $stmt_kh->fetch(PDO::FETCH_ASSOC);

    if (!$user_info) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy thông tin khách hàng.']);
        exit;
    }
    $sql_orders = "
        SELECT 
            dh.id_don_hang, dh.ngay_dat, dh.tong_tien, dh.trang_thai,
            ncs.ho_ten AS ten_cham_soc
        FROM don_hang dh
        LEFT JOIN nguoi_cham_soc ncs ON dh.id_nguoi_cham_soc = ncs.id_cham_soc
        WHERE dh.id_khach_hang = ?
        ORDER BY dh.ngay_dat DESC
    ";
    $stmt_orders = $pdo->prepare($sql_orders);
    $stmt_orders->execute([$id_khach_hang]);
    $orders = $stmt_orders->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'customer_name' => $user_info['ten_khach_hang'],
        'data' => $orders
    ]);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
    exit;
}
?>