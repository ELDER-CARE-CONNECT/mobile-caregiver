<?php
include_once("../model/get_products.php");
$conn = connectdb();

session_start(); // Khởi động session

$success_message = "";
$error_messages = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $agree = isset($_POST['agree']);

    // Validate
    if (empty($fullname)) {
        $error_messages[] = "Vui lòng nhập họ tên.";
    }

    if (empty($phone)) {
        $error_messages[] = "Vui lòng nhập số điện thoại.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM khach_hang WHERE so_dien_thoai = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error_messages[] = "Số điện thoại đã được đăng ký.";
        }
    }

    if (empty($password)) {
        $error_messages[] = "Vui lòng nhập mật khẩu.";
    }

    if ($password !== $confirm_password) {
        $error_messages[] = "Mật khẩu xác nhận không khớp.";
    }

   

    if (empty($error_messages)) {
        $stmt = $conn->prepare("INSERT INTO khach_hang (ten_khach_hang, email, so_dien_thoai, mat_khau, role)
                                VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("ssss", $fullname, $email, $phone, $password); // Không mã hoá nếu bạn yêu cầu

        if ($stmt->execute()) {
            $success_message = "Đăng ký thành công! Đang chuyển đến trang đăng nhập...";
            header("refresh:2;url=login.php");
        } else {
            $error_messages[] = "Có lỗi xảy ra khi đăng ký.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="../fontend/css/register.css">
    <link rel="stylesheet" href="../fontend/css/style.css">
    <style>
    
    .form-container {
    text-align: center;
    width: 500px; /* Tăng kích thước form */
    background: #fff;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    margin-top: 40px;
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

    <div class="wrapper">
    <div class="form-container">
        <h2>Đăng ký</h2>

        <?php if (!empty($error_messages)): ?>
            <div class="error-messages" style="color: red;">
                <ul>
                    <?php foreach ($error_messages as $msg): ?>
                        <li><?= $msg ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <p id="success-message" style="color: green;"><?= $success_message ?></p>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <input type="text" name="fullname" placeholder="Nhập họ và tên" required>
            <input type="text" name="email" placeholder="Nhập email">
            <input type="text" name="phone" placeholder="Nhập số điện thoại" required>
            <input type="password" name="password" placeholder="Nhập mật khẩu" required>
            <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>

           

            <button type="submit" class="register-btn">Đăng ký</button>
        </form>

        <p>Bạn đã có tài khoản? <a href="../view/login.php">Đăng nhập ngay</a></p>
        <p id="success-message" style="color: red; display: none;">Đăng ký thành công!</p>
    </div>
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
