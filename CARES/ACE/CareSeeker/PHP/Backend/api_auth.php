<?php
// backend/api_auth.php

// 1. Xóa bộ đệm để tránh các ký tự lạ
if (ob_get_length()) ob_clean(); 

// 2. CHỈ Cài đặt Session khi chưa có Session nào chạy
// (Tránh xung đột với Gateway)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(0, '/');
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// 3. Lấy tham số action (Hỗ trợ cả GET và POST)
$gatewayInput = $GLOBALS['api_input'] ?? [];
$action = $_GET['action'] ?? $_POST['action'] ?? $gatewayInput['action'] ?? '';

if ($action === 'logout') {
    
    // Xóa Session PHP
    session_unset(); 
    session_destroy(); 
    
    // Xóa Cookie Trình duyệt
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            '/', 
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    
    // Đường dẫn về trang Login (Đã chỉnh đúng)
<<<<<<< HEAD
    $redirectUrl = 'http://localhost:8080/ACE/Admin/frontend/auth/login.php';
=======
    $redirectUrl = 'http://localhost/CARES/ACE/Admin/frontend/auth/login.php';
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
    
    echo json_encode([
        'success' => true, 
        'message' => 'Đã đăng xuất thành công.', 
        'redirect_url' => $redirectUrl
    ]);
    exit;
}

http_response_code(400);
echo json_encode([
    'success' => false, 
    'message' => 'Hành động không hợp lệ.',
    'received_action' => $action
]);
?>