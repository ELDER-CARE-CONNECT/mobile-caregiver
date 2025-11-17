<?php
// Tệp: Backend/api_canhan.php
// Session đã được start bởi Gateway
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
if (!isset($_SESSION['id_khach_hang'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Lỗi xác thực: Bạn chưa đăng nhập.']);
    exit;
}
$id_khach_hang = $_SESSION['id_khach_hang'];
require_once 'db_connect.php'; 
try {
    $pdo = get_pdo_connection();
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối CSDL.']);
    exit;
}
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    try {
        $stmt_profile = $pdo->prepare("SELECT * FROM khach_hang WHERE id_khach_hang = ?");
        $stmt_profile->execute([$id_khach_hang]);
        $profile = $stmt_profile->fetch();

        if (!$profile) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy hồ sơ.']);
            exit;
        }
        $stmt_orders = $pdo->prepare("
            SELECT 
                d.id_don_hang, d.ngay_dat, d.trang_thai,
                CASE WHEN k.id_khieu_nai IS NOT NULL THEN 1 ELSE 0 END AS da_khieu_nai
            FROM don_hang d
            LEFT JOIN khieu_nai k ON d.id_don_hang = k.id_don_hang AND k.id_khach_hang = ?
            WHERE d.id_khach_hang = ? 
            AND (d.trang_thai = 'đã hoàn thành' OR d.trang_thai = 'đã hủy' OR d.trang_thai = 'Đã hủy')
            ORDER BY d.ngay_dat DESC
        ");
        $stmt_orders->execute([$id_khach_hang, $id_khach_hang]);
        $orders_for_complaint = $stmt_orders->fetchAll();
        echo json_encode([
            'success' => true,
            'profile' => $profile,
            'orders_for_complaint' => $orders_for_complaint
        ]);
        exit;

    } catch (\PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Lỗi truy vấn CSDL: ' . $e->getMessage()]);
        exit;
    }

} elseif ($method === 'POST') {    
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức POST không được hỗ trợ trên API này. Vui lòng dùng api_profile.php để cập nhật.']);
    exit;
}
?>
