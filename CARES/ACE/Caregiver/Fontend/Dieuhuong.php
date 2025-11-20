<?php
// Dieuhuong.php
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Điều hướng</title>
<!-- Font Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* Reset cơ bản và font */
* { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }

/* Navbar cố định */
.navbar {
    display: flex;
    justify-content: space-between; /* logo trái, link phải */
    align-items: center;
    height: 60px; /* cố định chiều cao */
    padding: 0 20px; /* padding ngang */
    background-color: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 1000;
    font-family: 'Inter', sans-serif; /* đảm bảo font Inter */
}

/* Logo */
.navbar .logo {
    font-size: 24px;
    font-weight: 800;
    color: #FF6B81;
    text-decoration: none;
    font-family: 'Inter', sans-serif;
}

/* Link điều hướng */
.nav-links {
    display: flex;
    gap: 20px;
    margin-right: 5%;
}

.nav-links a {
    text-decoration: none;
    color: #333;
    font-size: 16px;
    font-weight: 700; /* in đậm */
    padding: 6px 12px;
    border-radius: 6px;
    transition: 0.3s;
    font-family: 'Inter', sans-serif;
}

.nav-links a:hover {
    background-color: #FF6B81;
    color: white;
}

/* Khoảng cách từ navbar tới nội dung trang */
body {
    overflow-y: scroll; /* scrollbar luôn hiển thị */
    padding-top: 60px; /* đủ cao để không bị che bởi navbar */
    font-family: 'Inter', sans-serif; /* đảm bảo toàn bộ body dùng Inter */
}

/* Responsive */
@media (max-width: 768px) {
    .nav-links {
        margin-right: 2%;
        gap: 10px;
        font-size: 14px;
    }
}
</style>
</head>
<body>

<div class="navbar">
<<<<<<< HEAD
    <a href="DonHangChuaNhan.php" class="logo">Elder Care Connect</a>
    <div class="nav-links">
        <a href="DonHangChuaNhan.php">Đơn hàng</a>
=======
    <a href="Donhangchuanhan.php" class="logo">Elder Care Connect</a>
    <div class="nav-links">
        <a href="Donhangchuanhan.php">Đơn hàng</a>
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
        <a href="Tongdonhang.php">Lịch sử</a>
        <a href="Canhan.php">Cá nhân</a>
    </div>
</div>

</body>
</html>
