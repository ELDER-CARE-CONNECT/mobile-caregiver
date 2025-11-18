<?php
// Bắt đầu session (nếu chưa)
session_start();

// Xóa toàn bộ dữ liệu session
$_SESSION = [];
session_unset();
session_destroy();

// Xóa cookie PHPSESSID (nếu có)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ✅ Điều hướng chính xác về trang đăng nhập
header("Location: ../../Admin/login.php"); // 🔸 sửa đường dẫn nếu login ở nơi khác
exit;
?>
