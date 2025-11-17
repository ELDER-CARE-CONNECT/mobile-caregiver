<?php
// Session đã được start bởi Gateway
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once 'db_connect.php'; 

if (!isset($_SESSION['so_dien_thoai'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Lỗi xác thực: Vui lòng đăng nhập lại.']);
    exit;
}

try {
    $pdo = get_pdo_connection();
    $action = $_GET['action'] ?? '';
    if ($action === 'list_all') {
        $sql = "SELECT id_cham_soc, ho_ten, hinh_anh, danh_gia_tb, kinh_nghiem, don_da_nhan, tong_tien_kiem_duoc 
                FROM nguoi_cham_soc";
        $stmt = $pdo->query($sql);
        $caregivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $caregivers]);
        exit;
    }

    if ($action === 'list_featured') {
        $sql = "SELECT id_cham_soc, ho_ten, hinh_anh, danh_gia_tb, kinh_nghiem, tong_tien_kiem_duoc 
                FROM nguoi_cham_soc 
                LIMIT 3";
        $stmt = $pdo->query($sql);
        $caregivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => $caregivers]);
        exit;
    }
    if ($action === 'get_details' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt_main = $pdo->prepare("SELECT * FROM nguoi_cham_soc WHERE id_cham_soc = ?");
        $stmt_main->execute([$id]);
        $caregiver = $stmt_main->fetch(PDO::FETCH_ASSOC);

        if (!$caregiver) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy người chăm sóc.']);
            exit;
        }
        $stmt_reviews = $pdo->prepare("
            SELECT dg.*, kh.ten_khach_hang 
            FROM danh_gia dg 
            LEFT JOIN khach_hang kh ON dg.id_khach_hang = kh.id_khach_hang 
            WHERE dg.id_cham_soc = ?
            ORDER BY dg.ngay_danh_gia DESC
        ");
        $stmt_reviews->execute([$id]);
        $reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);
        $stmt_related = $pdo->prepare("
            SELECT id_cham_soc, ho_ten, hinh_anh, danh_gia_tb, kinh_nghiem, tong_tien_kiem_duoc 
            FROM nguoi_cham_soc 
            WHERE id_cham_soc != ?
            LIMIT 4
        ");
        $stmt_related->execute([$id]);
        $related = $stmt_related->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'caregiver' => $caregiver,
            'reviews' => $reviews,
            'related' => $related
        ]);
        exit;
    }
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ cho Caregiver API.']);

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
    exit;
}
?>
