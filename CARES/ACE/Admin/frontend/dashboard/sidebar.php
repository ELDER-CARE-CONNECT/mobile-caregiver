<?php
// sidebar.php
// Biến $activePage phải được set trước khi include file này
$activePage = $activePage ?? '';
?>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

.sidebar {
    width: 250px;
<<<<<<< HEAD
    background: #ffffff;
=======
    background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
    padding: 20px;
    height: 100vh;
    position: fixed;
    left: 0;
<<<<<<< HEAD
    top: 0;
    border-right: 1px solid #ddd;
    display: flex;
    flex-direction: column;
=======
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
}

.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
<<<<<<< HEAD
    margin-bottom: 25px;
=======
    margin-bottom: 30px;
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
}

.logo-image {
    width: 45px;
    height: 45px;
    object-fit: contain;
}

<<<<<<< HEAD
.logo-title {
    font-size: 20px;
    color: #d70018;
    font-weight: bold;
=======
.logo a {
    text-decoration: none;
    color: #d9534f;
    font-weight: bold;
    transition: color 0.3s;
    font-size: 24px;
    letter-spacing: 1px;
}

.logo a:hover {
    color: #b52b27;
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
}

.sidebar ul {
    list-style: none;
    padding: 0;
<<<<<<< HEAD
=======
    margin-top: 10px;
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
}

.sidebar ul li {
    margin: 12px 0;
}

.sidebar ul li a {
    font-size: 16px;
<<<<<<< HEAD
    display: block;
    padding: 10px 14px;
    text-decoration: none;
    color: #222;
    border-radius: 8px;
    font-weight: 600;
    transition: 0.25s;
=======
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #000;
    font-weight: 700;
    padding: 10px 12px;
    border-radius: 8px;
    transition: background 0.3s, color 0.3s, transform 0.2s;
    letter-spacing: 1px;
}

.sidebar ul li a span.icon {
    margin-right: 10px;
    font-size: 18px;
    display: inline-block;
    width: 24px;
    text-align: center;
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
}

.sidebar ul li a:hover,
.sidebar ul li.active a {
    background-color: #007bff;
    color: white;
    transform: translateX(5px);
}

<<<<<<< HEAD
/* Nút đăng xuất */
.logout-btn {
    margin-top: auto;
    padding: 12px;
    text-align: center;
    background: #d70018;
    color: #fff !important;
    font-weight: bold;
    border-radius: 8px;
    cursor: pointer;
    text-decoration: none;
    transition: 0.25s;
}

.logout-btn:hover {
    background: #b30013;
}

.main-content {
    margin-left: 250px;
=======
.main-content {
    margin-left: 250px;
    padding: 20px;
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
}
</style>

<aside class="sidebar">
<<<<<<< HEAD

    <div class="navbar">
        <div class="logo">
            <img src="../auth/images/logo.jpg" alt="Logo" class="logo-image">
            <span class="logo-title">Admin</span>
        </div>
=======
    <div class="navbar">
        <div class="logo">
        <img src="../auth/images/logo.jpg" alt="Logo" class="logo-image" />
        <a href="../../logout.php">Đăng xuất</a>
    </div>
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
    </div>

    <ul>
        <li class="<?= $activePage === 'tongquan' ? 'active' : ''; ?>">
            <a href="tongquan.php">Tổng quan</a>
        </li>
        <li class="<?= $activePage === 'nguoi_cham_soc' ? 'active' : ''; ?>">
            <a href="nguoi_cham_soc.php">Quản Lí Người Chăm Sóc</a>
        </li>
        <li class="<?= $activePage === 'quanlidonhang' ? 'active' : ''; ?>">
            <a href="quanlidonhang.php">Quản Lí Đơn Hàng</a>
        </li>
        <li class="<?= $activePage === 'khachhang' ? 'active' : ''; ?>">
            <a href="khachhang.php">Quản Lí Khách Hàng</a>
        </li>
        <li class="<?= $activePage === 'danhgia' ? 'active' : ''; ?>">
            <a href="danhgia.php">Quản Lí Đánh Giá</a>
        </li>
        <li class="<?= $activePage === 'quanli_khieunai' ? 'active' : ''; ?>">
            <a href="quanli_khieunai.php">Quản Lí Khiếu Nại</a>
        </li>
    </ul>
<<<<<<< HEAD

    <!-- Nút đăng xuất -->
    <a href="../../backend/auth/logout.php" class="logout-btn">
        Đăng xuất
    </a>

=======
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
</aside>
