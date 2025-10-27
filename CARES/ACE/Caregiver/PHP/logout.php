<?php
session_start();           // Bắt đầu session
session_unset();           // Xóa toàn bộ biến session
session_destroy();         // Hủy phiên đăng nhập

// Quay lại trang đăng nhập caregiver
header("Location: login.php");
exit;
?>
