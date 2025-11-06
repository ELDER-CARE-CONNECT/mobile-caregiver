<?php
// Bắt đầu session
session_start();

// Xóa toàn bộ dữ liệu session (đăng xuất thật)
session_unset();
session_destroy();

// Chuyển hướng về trang đăng nhập (sửa lại đường dẫn nếu cần)
header("Location: ../../Admin/login.php");
exit();
?>
