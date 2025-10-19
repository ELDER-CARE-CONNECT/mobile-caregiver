<?php
session_start();

// Kiểm tra nếu chưa đăng nhập, chuyển hướng về trang đăng nhập
if (!isset($_SESSION['so_dien_thoai'])) {
    header("Location: login.php");
    exit();
}

// Kiểm tra phân quyền
if ($_SESSION['role'] == 1) {
    // Admin
    echo "Chào Admin, " . $_SESSION['ten_khach_hang'];
} else {
    // User
    echo "Chào User, " . $_SESSION['ten_khach_hang'];
}
?>
