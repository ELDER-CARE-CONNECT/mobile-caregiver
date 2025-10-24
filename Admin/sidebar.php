<link rel="stylesheet" href="fontend/css/dieuhuong.css">
<aside class="sidebar">
    <div class="navbar">
        <div class="logo">
            <img src="fontend/images/logo.png" alt="Logo" class="logo-image" />
            <a href="../Public/view/index1.php">Đăng xuất</a>
        </div>
    </div>
    <ul>
        <li class="<?php echo ($activePage == 'tongquan') ? 'active' : ''; ?>">
            <a href="tongquan.php">🏠 Bảng Điều Khiển</a>
        </li>
        <li class="<?php echo ($activePage == 'nguoi_cham_soc') ? 'active' : ''; ?>">
            <a href="nguoi_cham_soc.php">📦 Quản Lí Người Chăm Sóc</a>
        </li>
        <li class="<?php echo ($activePage == 'quanli') ? 'active' : ''; ?>">
            <a href="quanli.php">📊 Quản Lí Đơn Hàng</a>
        </li>
        <li class="<?php echo ($activePage == 'khachhang') ? 'active' : ''; ?>">
            <a href="khachhang.php">👤 Quản Lí Khách Hàng</a>
        </li>
        <li class="<?php echo ($activePage == 'danhgia') ? 'active' : ''; ?>">
    <a href="danhgia.php">⭐ Quản Lí Đánh Giá</a>
</li>

    </ul>
</aside>
