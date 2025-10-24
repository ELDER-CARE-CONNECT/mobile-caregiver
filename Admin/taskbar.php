<?php
session_start(); // Khởi động session

include_once("../model/get_products.php");
include_once("../model/sanpham.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
<header>
        <div class="logo">Apple.Acsr</div>
        <input type="text" placeholder="Bạn cần tìm gì?">
        <div class="nav-right">
            <button class="cart">🛒 Giỏ hàng</button>
            <div class="dropdown">
                <?php if (isset($_SESSION['ten_khach_hang'])): ?>
                    <button id="loginBtn" class="login-btn">
                        👤 
                        <?php
                            echo $_SESSION['role'] == 1 ? "Admin" : "User";
                            echo " - " . $_SESSION['ten_khach_hang'];
                        ?>
                    </button>
                    <div class="dropdown-menu" style="display: none;">
                        <a href="<?php echo $_SESSION['role'] == 1 ? '../view/admin.php' : '../view/index.php'; ?>" id="ThongTinTaiKhoan">Trang cá nhân</a>
                        <a href="../model/logout.php" id="logoutBtn">Đăng xuất</a>
                    </div>
                    <?php else: ?>
                        <button id="loginBtn" class="login-btn" onclick="window.location.href='../view/login.php'">👤 Đăng nhập</button>
                    <?php endif; ?>
            </div>
        </div>
    </header>
    <script src="../script/script1.js"></script>.
    <script>
document.addEventListener("DOMContentLoaded", function () {
    const loginBtn = document.getElementById("loginBtn");
    const dropdownMenu = document.querySelector(".dropdown-menu");

    if (loginBtn && dropdownMenu) {
        loginBtn.addEventListener("click", function (e) {
            e.stopPropagation(); // Ngăn việc click lan ra ngoài
            dropdownMenu.style.display = dropdownMenu.style.display === "none" ? "block" : "none";
        });

        // Ẩn dropdown khi click ra ngoài
        document.addEventListener("click", function () {
            dropdownMenu.style.display = "none";
        });

        dropdownMenu.addEventListener("click", function (e) {
            e.stopPropagation(); // Click trong menu không ẩn nó
        });
    }
});
</script>

</body>
</html>