<?php
// backend/api_user.php
// Microservice cho Hồ sơ (Hoso.php) và Trang cá nhân (Canhan.php)
// Session đã được start bởi Gateway
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once 'db_connect.php'; // Sử dụng PDO chuẩn

// 1. KIỂM TRA XÁC THỰC (Lấy ID Khách hàng)
if (!isset($_SESSION['id_khach_hang'])) { // Lỗi: Đã sửa lại để kiểm tra biến chuẩn
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Lỗi xác thực: Vui lòng đăng nhập lại.']);
    exit;
}
$id_khach_hang_hien_tai = $_SESSION['id_khach_hang'];

$pdo = get_pdo_connection();
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// =============================================
// GET: LẤY THÔNG TIN HỒ SƠ
// =============================================
if ($action === 'get_profile' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt_profile = $pdo->prepare("SELECT * FROM khach_hang WHERE id_khach_hang = ?");
        $stmt_profile->execute([$id_khach_hang_hien_tai]);
        $profile = $stmt_profile->fetch();

        if (!$profile) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy hồ sơ.']);
            exit;
        }

        // Lấy danh sách đơn hàng để khiếu nại (từ Canhan.php)
        $stmt_orders = $pdo->prepare("
            SELECT d.id_don_hang, d.ngay_dat, d.trang_thai,
                   CASE WHEN k.id_khieu_nai IS NOT NULL THEN 1 ELSE 0 END AS da_khieu_nai
            FROM don_hang d
            LEFT JOIN khieu_nai k ON d.id_don_hang = k.id_don_hang
            WHERE d.id_khach_hang = ? AND d.trang_thai IN ('đã hoàn thành', 'Đã hủy', 'đã hủy')
            ORDER BY d.ngay_dat DESC
        ");
        $stmt_orders->execute([$id_khach_hang_hien_tai]);
        $orders_for_complaint = $stmt_orders->fetchAll();

        echo json_encode([
            'success' => true,
            'profile' => $profile,
            'orders_for_complaint' => $orders_for_complaint
        ]);
        exit;

    } catch (\PDOException $e) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Lỗi CSDL: ' . $e->getMessage()]);
        exit;
    }
}

// =============================================
// POST: CẬP NHẬT HỒ SƠ (Từ Hoso.php)
// =============================================
if ($action === 'update_profile' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // (Logic validation và xử lý upload ảnh từ Hoso.php gốc nên được đặt ở đây)
    // ...
    // Giả định xử lý upload ảnh (nếu có)
    $hinh_anh_path = $_POST['hinh_anh_cu'] ?? ''; // Giữ ảnh cũ
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        // Xử lý upload... (ví dụ: move_uploaded_file)
        // $hinh_anh_path = 'uploads/avatars/' . $_FILES['avatar']['name'];
        // (Cần code move_uploaded_file và kiểm tra bảo mật)
    }

    $data = [
        'ten_khach_hang' => $_POST['ho_ten'] ?? '',
        'so_dien_thoai' => $_POST['so_dt'] ?? '',
        'email' => $_POST['email'] ?? '',
        'ten_duong' => $_POST['ten_duong'] ?? '',
        'phuong_xa' => $_POST['phuong_xa'] ?? '',
        'tinh_thanh' => $_POST['tinh_thanh'] ?? '',
        'tuoi' => empty($_POST['tuoi']) ? null : (int)$_POST['tuoi'],
        'gioi_tinh' => $_POST['gioi_tinh'] ?? '',
        'chieu_cao' => empty($_POST['chieu_cao']) ? null : (float)$_POST['chieu_cao'],
        'can_nang' => empty($_POST['can_nang']) ? null : (float)$_POST['can_nang'],
        'hinh_anh' => $hinh_anh_path,
        'id_khach_hang' => $id_khach_hang_hien_tai
    ];
    
    try {
        $sql = "UPDATE khach_hang SET 
                    ten_khach_hang = :ten_khach_hang, 
                    so_dien_thoai = :so_dien_thoai, 
                    email = :email, 
                    ten_duong = :ten_duong, 
                    phuong_xa = :phuong_xa, 
                    tinh_thanh = :tinh_thanh, 
                    tuoi = :tuoi, 
                    gioi_tinh = :gioi_tinh, 
                    chieu_cao = :chieu_cao, 
                    can_nang = :can_nang, 
                    hinh_anh = :hinh_anh
                WHERE id_khach_hang = :id_khach_hang";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);

        // Cập nhật lại session
        $_SESSION['success_message'] = "Cập nhật hồ sơ thành công! 🎉";
        
        // Sửa lỗi: Đảm bảo chỉ cập nhật các trường liên quan
        $_SESSION['id_khach_hang'] = $data['id_khach_hang']; // Đảm bảo ID vẫn là ID
        $_SESSION['ten_khach_hang'] = $data['ten_khach_hang']; // Cập nhật tên
        $_SESSION['so_dien_thoai'] = $data['so_dien_thoai']; // Cập nhật SĐT

        echo json_encode(['success' => true, 'message' => 'Cập nhật hồ sơ thành công!']);
        exit;

    } catch (\PDOException $e) {
        http_response_code(500);
        if ($e->errorInfo[1] == 1062) { // Lỗi trùng lặp
             echo json_encode(['success' => false, 'message' => 'Lỗi: Email hoặc Số điện thoại này đã tồn tại.']);
        } else {
             echo json_encode(['success' => false, 'message' => 'Lỗi CSDL khi cập nhật: ' . $e->getMessage()]);
        }
        exit;
    }
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ cho User API.']);
?>