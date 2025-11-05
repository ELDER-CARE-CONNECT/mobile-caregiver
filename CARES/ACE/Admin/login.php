<?php
// =====================================================
// ✅ PHẦN XỬ LÝ PHP ĐĂNG NHẬP
// =====================================================

// 1️⃣ Khởi tạo session thống nhất cho toàn hệ thống
session_name("CARES_SESSION");
if (session_status() === PHP_SESSION_NONE) session_start();
// 2️⃣ Kết nối tới CSDL
include_once("../model/sanpham.php");
include_once("../model/get_products.php");
$conn = connectdb();

// 3️⃣ Xử lý khi người dùng gửi form đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $so_dien_thoai = trim($_POST['phone']);
    $mat_khau = trim($_POST['password']);

    if (empty($so_dien_thoai) || empty($mat_khau)) {
        $error_message = "Số điện thoại và mật khẩu không được để trống!";
    } else {
        // ===== KIỂM TRA TRONG BẢNG NGƯỜI CHĂM SÓC =====
        $sql_ncs = "SELECT * FROM nguoi_cham_soc WHERE ten_tai_khoan = ? AND mat_khau = ?";
        $stmt = $conn->prepare($sql_ncs);
        $stmt->bind_param("ss", $so_dien_thoai, $mat_khau);
        $stmt->execute();
        $result_ncs = $stmt->get_result();

        if ($result_ncs && $result_ncs->num_rows > 0) {
            $user = $result_ncs->fetch_assoc();

            // ✅ Lưu session người chăm sóc
            $_SESSION['role'] = 'nguoi_cham_soc';
            $_SESSION['caregiver_id'] = $user['id_cham_soc'];   // ID người chăm sóc
            $_SESSION['ten_tai_khoan'] = $user['ten_tai_khoan'];
            $_SESSION['ho_ten'] = $user['ho_ten'];

            // ✅ Chuyển đến trang tổng đơn hàng
            header("Location: ../Caregiver/PHP/Tongdonhang.php");
            exit();
        }

        // ===== KIỂM TRA TRONG BẢNG KHÁCH HÀNG =====
$sql_kh = "SELECT * FROM khach_hang WHERE so_dien_thoai = ? AND mat_khau = ?";
$stmt = $conn->prepare($sql_kh);
$stmt->bind_param("ss", $so_dien_thoai, $mat_khau);
$stmt->execute();
$result_kh = $stmt->get_result();

if ($result_kh && $result_kh->num_rows > 0) {
    $user = $result_kh->fetch_assoc();

    // ✅ Lưu session đầy đủ để Canhan.php sử dụng
    $_SESSION['role'] = 'khach_hang';
    $_SESSION['id_khach_hang'] = $user['id_khach_hang'];
    $_SESSION['profile'] = $user; // toàn bộ thông tin khách hàng
    $_SESSION['so_dien_thoai'] = $user['so_dien_thoai'];
    $_SESSION['ten_khach_hang'] = $user['ten_khach_hang'];

    // ✅ Nếu hồ sơ chưa có thông tin -> chuyển đến trang Hồ sơ để bổ sung
    if (empty($user['ten_khach_hang']) || empty($user['dia_chi'])) {
        header("Location: ../CareSeeker/PHP/Hoso.php");
    } else {
        header("Location: ../CareSeeker/PHP/Canhan.php"); // <-- chuyển về trang cá nhân
    }
    exit();
}

        // Nếu không khớp ở bảng nào
        $error_message = "Sai tài khoản hoặc mật khẩu!";
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
  <!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng nhập</title>
<style>
    /* --- NỀN TRANG --- */
   body { 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: url("fontend/images/nen_dang-nhap.jpg") no-repeat center center fixed;
        background-size: cover; /* Ảnh nền phủ toàn bộ màn hình */
        display: flex;
        align-items: center;
        justify-content: flex-start; /* nếu muốn canh giữa, đổi thành center */
        min-height: 100vh;
        margin: 0;
    }

    /* --- LỚP MỜ NỀN PHÍA SAU --- */
    body::before {
        content: "";
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.25); /* lớp phủ đen mờ 25% */
        z-index: -1;
    }

    /* --- KHUNG NGOÀI --- */
    .auth-container {
        margin-left: 65%; /* khoảng cách từ viền trái, có thể chỉnh tùy bạn */
        padding: 20px;
        border-radius: 20px;
        backdrop-filter: blur(10px); /* làm mờ ảnh nền phía sau */
    }

    /* --- KHUNG FORM --- */
    .auth-wrapper {
        width: 100%;
        max-width: 420px;
        background: rgba(255,255,255,0.95);
        padding: 40px 30px;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: all 0.3s ease;
    }

    .auth-wrapper:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    /* --- LOGO --- */
    .auth-logo {
        width: 90px;
        margin-bottom: 20px;
    }

    /* --- TIÊU ĐỀ --- */
    .auth-heading {
        font-size: 26px;
        font-weight: bold;
        color: #d70018;
        margin-bottom: 25px;
    }

    /* --- Ô NHẬP LIỆU --- */
   .auth-input {
    width: 100%;
    padding: 14px 18px;
    margin-bottom: 18px;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 15px;
    transition: 0.2s;
    box-sizing: border-box; /* ✅ giúp tính cả padding & border trong width */
}


    .auth-input:focus {
        outline: none;
        border-color: #d70018;
        box-shadow: 0 0 5px rgba(215, 0, 24, 0.3);
    }

    /* --- LỖI --- */
    .auth-error {
        color: red;
        font-size: 14px;
        margin-top: 5px;
    }

    /* --- QUÊN MẬT KHẨU --- */
    .auth-forgot {
        text-align: right;
        font-size: 14px;
        margin-top: 5px;
        margin-bottom: 20px;
    }

    .auth-forgot a {
        color: #d70018;
        text-decoration: none;
        font-weight: 500;
    }

    .auth-forgot a:hover {
        text-decoration: underline;
    }

    /* --- NÚT ĐĂNG NHẬP --- */
    .auth-submit {
        width: 100%;
        padding: 14px;
        background-color: #d70018;
        color: white;
        border: none;
        border-radius: 10px;
        cursor: pointer;
        font-size: 17px;
        font-weight: bold;
        transition: background 0.3s ease;
    }

    .auth-submit:hover {
        background-color: #b30013;
    }

    /* --- LINK ĐĂNG KÝ --- */
    .auth-register {
        font-size: 14px;
        margin-top: 25px;
    }

    .auth-register a {
        color: #d70018;
        font-weight: bold;
        text-decoration: none;
    }

    .auth-register a:hover {
        text-decoration: underline;
    }
</style>
</head>
<body>

    <div class="auth-container">
        <div class="auth-wrapper">
            <img src="fontend/images/avatar.png" alt="Logo" class="auth-logo">
            <h2 class="auth-heading">Đăng nhập</h2>

            <form method="POST" action="login.php">
                <input type="text" placeholder="Nhập số điện thoại" class="auth-input" name="phone" id="phone" 
                       value="<?php echo isset($so_dien_thoai) ? $so_dien_thoai : ''; ?>">
                <input type="password" placeholder="Nhập mật khẩu" class="auth-input" name="password" id="password">
                
                <?php if (isset($error_message)) { ?>
                    <div id="error-message" class="auth-error"><?php echo $error_message; ?></div>
                <?php } ?>

                <div class="auth-forgot">
                    <a href="quenmk.php">Quên mật khẩu?</a>
                </div>

                <button class="auth-submit" type="submit">Đăng nhập</button>
            </form>

            <p class="auth-register">
                Bạn chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
            </p>
        </div>
    </div>

</body>
</html>

</body>
</html>