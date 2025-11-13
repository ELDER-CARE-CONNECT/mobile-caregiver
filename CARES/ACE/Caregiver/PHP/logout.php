<?php
session_name("CARES_SESSION");
session_start();

// Xóa toàn bộ session
$_SESSION = [];
session_unset();
session_destroy();

// Xóa cookie phiên đăng nhập
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Trở về trang login
header("Location: ../../Admin/login.php");
exit;
?>
