<?php
include_once("../model/get_products.php"); // Kết nối CSDL
$conn = connectdb(); // Giả sử bạn có một hàm này để kết nối DB
session_start(); // Khởi động session

// Biến lỗi
$phoneError = $passwordError = $confirmPasswordError = "";

// Kiểm tra khi form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'];
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Kiểm tra số điện thoại có hợp lệ không (10-11 chữ số)
    if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
        $phoneError = "Số điện thoại không hợp lệ.";
    } else {
        // Kiểm tra số điện thoại đã đăng ký chưa trong cơ sở dữ liệu
        $stmt = $conn->prepare("SELECT mat_khau FROM khach_hang WHERE so_dien_thoai = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            // Nếu không tìm thấy người dùng trong DB
            $phoneError = "Số điện thoại này chưa được đăng ký.";
        } else {
            // Kiểm tra mật khẩu cũ có đúng không
            if ($user['mat_khau'] !== $oldPassword) {
                $passwordError = "Mật khẩu cũ không đúng.";
            }

            // Kiểm tra mật khẩu mới và xác nhận mật khẩu có khớp không
            if ($newPassword !== $confirmPassword) {
                $confirmPasswordError = "Mật khẩu mới và xác nhận mật khẩu không khớp.";
            }

            // Nếu không có lỗi, cập nhật mật khẩu
            if (empty($phoneError) && empty($passwordError) && empty($confirmPasswordError)) {
                // Cập nhật mật khẩu mới
                $stmt = $conn->prepare("UPDATE khach_hang SET mat_khau = ? WHERE so_dien_thoai = ?");
                $stmt->bind_param("ss", $newPassword, $phone);
                $stmt->execute();

                // Thông báo thành công
                $successMessage = "Mật khẩu đã được thay đổi thành công.";
                $redirect = true; // Biến này sẽ chỉ ra rằng chúng ta cần chuyển hướng sau khi đổi mật khẩu
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>
    <link rel="stylesheet" href="/fontend/css/quenmk.css">
    <link rel="stylesheet" href="/fontend/css/style.css">
    <style>
        .no-border-iframe {
            border: none;
            outline: none;
            width: 100%;
            height: 100px; /* Tuỳ kích thước taskbar */
        }

       /* RESET CƠ BẢN */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
    }

    /* CONTAINER CHÍNH */
    .boxcenter {
        width: 100%;
        margin: 0 auto;
    }

    /* HEADER CHUẨN HÓA */
    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: rgb(187, 49, 49);
        color: white;
        height: 54.4px;
        padding: 0 20px;
        margin: 0 auto;
        flex-wrap: wrap;
        position: relative;
        width: 100%;
    }

    /* LOGO */
    .logo {
        font-size: 20px;
        font-weight: bold;
        padding-top: 15px;
    }

    /* THANH TÌM KIẾM */
    .search-box {
        flex: 1;
        display: flex;
        justify-content: center;
        
    }
    .search-box input {
        width: 100%;
        max-width: 400px;
        padding: 8px 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 14px;.
    }

    /* NÚT ĐĂNG NHẬP + GIỎ HÀNG */
    .nav-right {
        flex: 0 0 auto;
        display: flex;
        gap: 10px;
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
    .cart:hover, .login-btn:hover {
        background-color: #f0f0f0;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        header {
            flex-direction: column;
            height: auto;
            padding: 10px;
        }

        .search-box {
            margin: 10px 0;
            width: 100%;
        }

        .nav-right {
            justify-content: center;
            width: 100%;
            flex-wrap: wrap;
        }

        .cart, .login-btn {
            margin: 5px;
        }
    }

    .container {
    text-align: center;
    width: 100%;
    max-width: 500px;
    margin-top: 40px;
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

    <div class="container">
        <div class="forgot-password-box">
            <div class="header-row">
                <a href="login.php" class="back-button">←</a> 
                <h2>Quên mật khẩu</h2>
            </div>
            <p>Hãy nhập số điện thoại của bạn vào bên dưới để bắt đầu quá trình khôi phục mật khẩu.</p>

            <!-- Bước 1: Nhập số điện thoại và mật khẩu -->
            <div id="phone-step">
                <form method="POST" action="quenmk.php">
                    <label for="phone">Nhập vào số điện thoại của bạn</label>
                    <input type="text" id="phone" name="phone" placeholder="" required>

                    <label for="old_password">Nhập mật khẩu cũ</label>
                    <input type="password" id="old_password" name="old_password" placeholder="" required>

                    <label for="new_password">Nhập mật khẩu mới</label>
                    <input type="password" id="new_password" name="new_password" placeholder="" required>

                    <label for="confirm_password">Nhập lại mật khẩu mới</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="" required>

                    <p id="phone-error" style="color: red; font-size: 0.9em; margin-top: 4px;">
                        <?php echo $phoneError; ?>
                    </p>
                    <p style="color: red;"><?php echo $passwordError; ?></p>
                    <p style="color: red;"><?php echo $confirmPasswordError; ?></p>

                    <button type="submit" class="continue-btn">Đổi mật khẩu</button>
                </form>

                <?php if (isset($successMessage)): ?>
                    <p style="color: green;"><?php echo $successMessage; ?></p>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <?php if (isset($redirect) && $redirect): ?>
        <script>
            setTimeout(function() {
                window.location.href = "login.php";
            }, 2000); // Chuyển hướng sau 2 giây
        </script>
    <?php endif; ?>

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
