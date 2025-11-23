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
    background: #ffffff;
    padding: 20px;
    height: 100vh;
    position: fixed;
    left: 0;
    top: 0;
    border-right: 1px solid #ddd;
    display: flex;
    flex-direction: column;
}

.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 25px;
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

.logo-title {
    font-size: 20px;
    color: #d70018;
    font-weight: bold;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    margin: 12px 0;
}

.sidebar ul li a {
    font-size: 16px;
    display: block;
    padding: 10px 14px;
    text-decoration: none;
    color: #222;
    border-radius: 8px;
    font-weight: 600;
    transition: 0.25s;
}

.sidebar ul li a:hover,
.sidebar ul li.active a {
    background-color: #007bff;
    color: white;
    transform: translateX(5px);
}

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
}
</style>

<aside class="sidebar">

    <div class="navbar">
        <div class="logo">
            <img src="../auth/images/logo.jpg" alt="Logo" class="logo-image">
            <span class="logo-title">Admin</span>
        </div>
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

    <!-- Nút đăng xuất -->
    <a href="../../backend/auth/logout.php" class="logout-btn">
        Đăng xuất
    </a>

</aside>
