<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

.container {
    display: flex;
    height: 100vh;
}

/* === SIDEBAR === */
.sidebar {
    width: 250px;
    background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    height: 100vh;
    position: fixed;
    left: 0;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
}

/* --- Logo + Đăng xuất --- */
.navbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 30px;
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

/* Nút Đăng xuất */
.logo a {
    text-decoration: none;
    color: #d9534f;
    font-weight: bold;
    transition: color 0.3s;
    font-size: 24px; /* chữ to hơn */
    letter-spacing: 1px;
}

.logo a:hover {
    color: #b52b27;
}

/* --- MENU --- */
.sidebar ul {
    list-style: none;
    padding: 0;
    margin-top: 10px;
}

.sidebar ul li {
    margin: 12px 0;
}

.sidebar ul li a {
    font-size: 16px;
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

/* Icon + chữ cùng hàng */
.sidebar ul li a span.icon {
    margin-right: 10px;
    font-size: 18px;
    display: inline-block;
    width: 24px;
    text-align: center;
}

/* Hover + Active */
.sidebar ul li a:hover,
.sidebar ul li.active a {
    background-color: #007bff;
    color: white;
    transform: translateX(5px);
}

/* === MAIN CONTENT === */
.main-content {
    width: calc(100% - 250px);
    margin-left: 250px;
    padding: 20px;
}
</style>

<aside class="sidebar">
    <div class="navbar">
        <div class="logo">
            <img src="fontend/images/logo.png" alt="Logo" class="logo-image" />
            <a href="../Public/view/index1.php">Đăng xuất</a>
        </div>
    </div>

    <ul>
        <li class="<?php echo ($activePage == 'tongquan') ? 'active' : ''; ?>">
            <a href="tongquan.php">Tổng quan</a>
        </li>

        <li class="<?php echo ($activePage == 'nguoi_cham_soc') ? 'active' : ''; ?>">
            <a href="nguoi_cham_soc.php">Quản Lí Người Chăm Sóc</a>
        </li>

        <li class="<?php echo ($activePage == 'quanli') ? 'active' : ''; ?>">
            <a href="quanli.php">Quản Lí Đơn Hàng</a>
        </li>

        <li class="<?php echo ($activePage == 'khachhang') ? 'active' : ''; ?>">
            <a href="khachhang.php">Quản Lí Khách Hàng</a>
        </li>

        <li class="<?php echo ($activePage == 'danhgia') ? 'active' : ''; ?>">
            <a href="danhgia.php">Quản Lí Đánh Giá</a>
        </li>

        <li class="<?php echo ($activePage == 'quanli_khieunai') ? 'active' : ''; ?>">
            <a href="quanli_khieunai.php">Quản Lí Khiếu Nại</a>
        </li>
    </ul>
</aside>
