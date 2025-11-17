<?php
// backend/api_auth.php
// Session đã được start bởi Gateway hoặc đã được gọi trong file này.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

// Lấy action từ GET hoặc POST
$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action === 'logout') {
    // THÊM: Kiểm tra phương thức POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Vui lòng sử dụng phương thức POST để đăng xuất.']);
        exit;
    }

    // 1. Dọn dẹp session trên server
    session_unset(); 
    session_destroy(); 
    
    // 2. XÓA COOKIE SESSION KHỎI TRÌNH DUYỆT (BƯỚC BẮT BUỘC ĐỂ ĐĂNG XUẤT)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    $redirectUrl = 'http://localhost/ELDER-CARE-CONNECT/Admin/login.php';
    
    echo json_encode(['success' => true, 'message' => 'Đã đăng xuất.', 'redirect_url' => $redirectUrl]);
    exit;
}

http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ.']);
?>
