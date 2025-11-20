<?php
session_start();

// Xóa tất cả session
session_unset();
session_destroy();

// Quay về trang login
header("Location: ../../frontend/auth/login.php");
exit();
?>
