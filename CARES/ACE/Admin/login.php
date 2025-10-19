<?php
// Bước 1: Kết nối với CSDL
include_once("../model/sanpham.php");
include_once('../model/get_products.php'); // Đảm bảo rằng bạn đã kết nối với CSDL đúng cách
$conn = connectdb();
// Bước 2: Khởi tạo session
session_start();

// Bước 3: Xử lý form khi người dùng nhấn đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $so_dien_thoai = $_POST['phone'];
    $mat_khau = $_POST['password'];

    // Bước 4: Kiểm tra dữ liệu có hợp lệ không
    if (empty($so_dien_thoai) || empty($mat_khau)) {
        $error_message = "Số điện thoại và mật khẩu không được để trống!";
    } else {
        // Bước 5: Kết nối tới CSDL và kiểm tra thông tin đăng nhập
        $sql = "SELECT * FROM khach_hang WHERE so_dien_thoai = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $so_dien_thoai);  // Ràng buộc tham số

        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Bước 6: Kiểm tra xem có người dùng hợp lệ không và so sánh mật khẩu trực tiếp
        if ($user) {
            // Nếu mật khẩu khớp
            if ($user['mat_khau'] === $mat_khau) {
                // Đăng nhập thành công, lưu thông tin vào session
                $_SESSION['so_dien_thoai'] = $user['so_dien_thoai'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['ten_khach_hang'] = $user['ten_khach_hang'];

                // Chuyển hướng tới trang index1.php
                header("Location: tongquan.php");
                exit();
            } else {
                // Mật khẩu không khớp
                $error_message = "Sai số điện thoại hoặc mật khẩu!";
            }
        } else {
            // Người dùng không tồn tại trong cơ sở dữ liệu
            $error_message = "Sai số điện thoại hoặc mật khẩu!";
        }
    }
}
?>

<!-- Giao diện đăng nhập -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="../style/style.css">
    <style>
    .auth-wrapper {
        max-width: 500px;
        width: 100%;
        margin: 0 auto;
        margin-top: 40px;
        text-align: center;
    }

    .auth-logo {
        width: 80px;
        margin-bottom: 20px;
    }

    .auth-heading {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .auth-social {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 15px;
    }

    .btn-google, .btn-zalo {
        border: none;
        padding: 12px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
    }

    .btn-google {
        background-color: #fff;
        border: 1px solid #ccc;
    }

    .btn-zalo {
        background-color: #fff;
        border: 1px solid #ccc;
    }

    .auth-divider {
        width: 100%;
        border: none;
        border-top: 2px solid #ccc;
        margin: 20px 0;
    }

    .auth-input {
        width: 100%;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 16px;
    }

    .auth-forgot {
        text-align: center;
        font-size: 14px;
        margin-top: 15px;
        margin-bottom: 15px;
        color: #d70018;
    }

    .auth-submit {
        width: 100%;
        padding: 15px;
        background-color: #d70018;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 18px;
        font-weight: bold;
        margin-top: 15px;
    }

    .auth-register, .auth-policy {
        font-size: 14px;
        margin-top: 15px;
    }

    .auth-register a, .auth-policy a {
        color: #d70018;
        text-decoration: none;
        font-weight: bold;
    }

    .auth-error {
        color: red;
        font-size: 14px;
        margin-top: 5px;
    }
    .cart, .login-btn {
        background: white;
        color: #d32f2f;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        height: 34.4px;
        font-weight: bold;
        font-size: 13px;
        width: 118.06px;
    }
    </style>
    
</head>
<body>
<header>
    <div class="logo">Apple.Acsr</div>
   
    <div class="nav-right">
        <button class="cart">🛒 Giỏ hàng</button>
        <div class="dropdown">
            <?php if (isset($_SESSION['ten_khach_hang'])): ?>
                <button id="loginBtn" class="login-btn">
                    👤 <?php echo htmlspecialchars($_SESSION['ten_khach_hang']); ?>
                </button>
                <div class="dropdown-menu" style="display: none;">
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 1): ?>
                        <a href="http://localhost/WEB_PhuKien/Admin/tongquan.php" id="ThongTinTaiKhoan">Trang cá nhân</a>
                    <?php endif; ?>
                    <a href="../model/logout.php" id="logoutBtn">Đăng xuất</a>
                </div>
            <?php else: ?>
                <button id="loginBtn" class="login-btn" onclick="window.location.href='../view/login.php'">👤 Đăng nhập</button>
            <?php endif; ?>
        </div>
    </div>
</header>

        <div class="auth-wrapper">
            <img src="../../Admin/Images/avatar.png" alt="Logo" class="auth-logo">
            <h2 class="auth-heading">Đăng nhập</h2>

            <form method="POST" action="login.php">
                <input type="text" placeholder="Nhập số điện thoại" class="auth-input" name="phone" id="phone" value="<?php echo isset($so_dien_thoai) ? $so_dien_thoai : ''; ?>">
                <input type="password" placeholder="Nhập mật khẩu" class="auth-input" name="password" id="password">
                
                <?php if (isset($error_message)) { ?>
                    <div id="error-message" class="auth-error"><?php echo $error_message; ?></div>
                <?php } ?>
                
                <div class="auth-forgot">
                    <a href="quenmk.php">Quên mật khẩu?</a>
                </div>
                <button class="auth-submit" type="submit">Đăng nhập</button>
            </form>

            <p class="auth-register">Bạn chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
        </div>


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
