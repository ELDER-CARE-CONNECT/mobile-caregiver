<?php
// File: ACE/Admin/logout.php

session_start();

// 1. Xóa toàn bộ dữ liệu Session
$_SESSION = [];
session_unset();
session_destroy();

// 2. Xóa Cookie Session trên trình duyệt (để đăng xuất triệt để)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        '/', // Quan trọng: set path là '/' để xóa cookie toàn cục
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// 3. Chuyển hướng về trang Login (Đường dẫn tuyệt đối chính xác)
header("Location: /CARES/ACE/Admin/frontend/auth/login.php");
exit;
?>