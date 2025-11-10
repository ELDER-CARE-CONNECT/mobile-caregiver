<?php
// Admin/login.php
include_once("connect.php"); // kết nối DB (file connect.php nằm cùng thư mục Admin/)

// ✅ THÊM LOGIC HỦY SESSION CŨ TRƯỚC KHI BẮT ĐẦU SESSION MỚI
// 1. Kiểm tra trạng thái session và hủy
if (session_status() === PHP_SESSION_ACTIVE) {
    session_unset();
    session_destroy();
}

// 2. Bắt đầu session mới
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $so_dien_thoai = trim($_POST['phone']);
    $mat_khau = trim($_POST['password']);

    if (empty($so_dien_thoai) || empty($mat_khau)) {
        $error_message = "Số điện thoại và mật khẩu không được để trống!";
    } else {
        // === Kiểm tra bảng admin ===
        $sql_admin = "SELECT * FROM admin WHERE so_dien_thoai = ? AND mat_khau = ?";
        $stmt = $conn->prepare($sql_admin);
        // ✅ Cần kiểm tra $stmt->prepare thành công trước khi bind
        if ($stmt) {
            $stmt->bind_param("ss", $so_dien_thoai, $mat_khau);
            $stmt->execute();
            $result_admin = $stmt->get_result();

            if ($result_admin->num_rows > 0) {
                $_SESSION['role'] = 'admin';
                $_SESSION['so_dien_thoai'] = $so_dien_thoai;
                header("Location: tongquan.php");
                exit();
            }
        }

        // === Kiểm tra bảng khách hàng ===
        // ✅ SỬA: Lấy thêm tuổi và giới tính để kiểm tra hồ sơ đầy đủ
        $sql_kh = "SELECT id_khach_hang, ten_khach_hang, ten_duong, phuong_xa, tinh_thanh, email, tuoi, gioi_tinh FROM khach_hang WHERE so_dien_thoai = ? AND mat_khau = ?";
        $stmt = $conn->prepare($sql_kh);
        
        // ✅ Kiểm tra $stmt->prepare thành công
        if ($stmt) {
            $stmt->bind_param("ss", $so_dien_thoai, $mat_khau);
            $stmt->execute();
            $result_kh = $stmt->get_result();

            if ($result_kh->num_rows > 0) {
                $user = $result_kh->fetch_assoc();
                
                // ⭐ Lưu thông tin vào Session
                $_SESSION['id_khach_hang'] = $user['id_khach_hang']; 
                $_SESSION['email'] = $user['email']; 
                
                $_SESSION['role'] = 'khach_hang';
                $_SESSION['so_dien_thoai'] = $so_dien_thoai;
                $_SESSION['ten_khach_hang'] = $user['ten_khach_hang'];
                
                $ten_khach_hang = trim($user['ten_khach_hang'] ?? '');
                $email = trim($user['email'] ?? '');
                $ten_duong = trim($user['ten_duong'] ?? '');
                $phuong_xa = trim($user['phuong_xa'] ?? '');
                $tinh_thanh = trim($user['tinh_thanh'] ?? '');
                $tuoi = intval($user['tuoi'] ?? 0);
                $gioi_tinh = trim($user['gioi_tinh'] ?? '');

                // ✅ LOGIC KIỂM TRA HỒ SƠ ĐẦY ĐỦ (Đảm bảo tất cả các trường bắt buộc đã được điền)
                if (empty($ten_khach_hang) || empty($email) || empty($ten_duong) || empty($phuong_xa) || empty($tinh_thanh) || $tuoi <= 0 || empty($gioi_tinh)) {
                    // Nếu thiếu bất kỳ thông tin nào, chuyển đến trang Hồ sơ để điền
                    header("Location: ../CareSeeker/PHP/Hoso.php"); 
                } else {
                    // Nếu đầy đủ, chuyển đến trang chủ
                    header("Location: ../CareSeeker/PHP/index.php"); 
                }
                exit();
            }
        }

        // === Kiểm tra bảng người chăm sóc ===
        $sql_ncs = "SELECT * FROM nguoi_cham_soc WHERE ten_tai_khoan = ? AND mat_khau = ?";
        $stmt = $conn->prepare($sql_ncs);
        
        // ✅ Kiểm tra $stmt->prepare thành công
        if ($stmt) {
            $stmt->bind_param("ss", $so_dien_thoai, $mat_khau);
            $stmt->execute();
            $result_ncs = $stmt->get_result();

            if ($result_ncs->num_rows > 0) {
                $_SESSION['role'] = 'nguoi_cham_soc';
                $_SESSION['ten_tai_khoan'] = $so_dien_thoai;
                header("Location: ../Caregiver/PHP/Donhangchuanhan.php"); 
                exit();
            }
        }

        // Không tìm thấy tài khoản
        $error_message = "Sai số điện thoại hoặc mật khẩu!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Đăng nhập</title>
<style>
    /* CSS giữ nguyên */
    body { 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: url("fontend/images/nen_dang-nhap.jpg") no-repeat center center fixed;
        background-size: cover;
        min-height: 100vh;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        box-sizing: border-box;
    }
    .auth-container {
        display: flex;
        gap: 50px;
        background: rgba(255,255,255,0.85);
        border-radius: 20px;
        padding: 40px;
        max-width: 940px;
        width: 100%;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        position: relative; 
        z-index: 1; 
    }
    .auth-image {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .auth-image img {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: -1;
        object-fit: cover;
        border-radius: 0; 
    }
    .auth-wrapper {
        flex: 0 0 420px;
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
    .auth-logo {
        width: 90px;
        margin-bottom: 20px;
    }
    .auth-heading {
        font-size: 26px;
        font-weight: bold;
        color: #d70018;
        margin-bottom: 25px;
    }
   .auth-input {
        width: 100%;
        padding: 14px 18px;
        margin-bottom: 18px;
        border: 1px solid #ddd;
        border-radius: 10px;
        font-size: 15px;
        box-sizing: border-box;
    }
    .auth-input:focus {
        outline: none;
        border-color: #d70018;
        box-shadow: 0 0 5px rgba(215, 0, 24, 0.3);
    }
    .auth-error {
        color: red;
        font-size: 14px;
        margin-top: 5px;
    }
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
    .google-btn {
        width: 100%;
        background-color: #db4437;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px;
        margin-top: 15px;
        font-size: 16px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        box-sizing: border-box;
    }
    .google-btn img {
        width: 20px;
        height: 20px;
    }
    @media (max-width: 900px) {
        body {
            padding: 10px;
        }
        .auth-container {
            flex-direction: column;
            padding: 20px 15px;
            max-width: 100%;
            box-shadow: none;
            background: transparent;
        }
        .auth-image {
            display: none;
        }
        .auth-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 30px 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            background: rgba(255,255,255,0.95);
            border-radius: 16px;
        }
    }
</style>
</head>
<body>

<div class="auth-container">
    <div class="auth-image">
        <img src="images/nguoi-cao-tuoi-2.jpg" alt="Ảnh minh họa">
    </div>
    <div class="auth-wrapper">
        <img src="images/logo.jpg" alt="Logo" class="auth-logo">
        <h2 class="auth-heading">Đăng nhập</h2>

        <form method="POST" action="">
            <input type="text" placeholder="Nhập số điện thoại" class="auth-input" name="phone" id="phone" 
                   value="<?php echo isset($so_dien_thoai) ? htmlspecialchars($so_dien_thoai, ENT_QUOTES, 'UTF-8') : ''; ?>">
            <input type="password" placeholder="Nhập mật khẩu" class="auth-input" name="password" id="password">
            
            <?php if (isset($error_message)) { ?>
                <div id="error-message" class="auth-error"><?php echo $error_message; ?></div>
            <?php } ?>

            <div class="auth-forgot">
                <a href="quenmk.php">Quên mật khẩu?</a>
            </div>

            <button class="auth-submit" type="submit">Đăng nhập</button>
        </form>

        <button class="google-btn" type="button" onclick="loginWithGoogle()">
            <img src="https://img.icons8.com/color/48/000000/google-logo.png" alt="Google logo"/> Đăng nhập bằng Gmail
        </button>

        <p class="auth-register">
            Bạn chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
        </p>
    </div>
</div>

<script type="module">
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-app.js";
  import { getAuth, GoogleAuthProvider, signInWithPopup } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

  const firebaseConfig = {
    apiKey: "AIzaSyCmpKMonh3_RM8CtJ_5JZ2VLB71Hcd8Kn8",
    authDomain: "demoxd-5ecba.firebaseapp.com",
    projectId: "demoxd-5ecba",
    storageBucket: "demoxd-5ecba.firebasestorage.app",
    messagingSenderId: "347326750071",
    appId: "1:347326750071:web:ab3d6813900af8cae39572",
    measurementId: "G-ZJNZ8770QQ"
  };

  const app = initializeApp(firebaseConfig);
  const auth = getAuth(app);
  const provider = new GoogleAuthProvider();

  window.loginWithGoogle = function() {
    signInWithPopup(auth, provider)
      .then((result) => {
        const user = result.user;
        return fetch('save_google_user.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            ten_khach_hang: user.displayName,
            email: user.email,
            hinh_anh: user.photoURL
          })
        });
      })
      .then(async (res) => {
        if (!res.ok) throw new Error('Network response was not ok');
        const data = await res.json();
        if (data.success) {
          // SỬA: Chuyển hướng đến Hoso.php để khách hàng hoàn thành thông tin sau khi đăng nhập Google lần đầu
          window.location.href = "../CareSeeker/PHP/Hoso.php";
        } else {
          alert("Không thể lưu người dùng: " + (data.message || "Lỗi không xác định"));
        }
      })
      .catch((error) => {
        console.error("Đăng nhập Gmail thất bại:", error);
        alert("Không thể đăng nhập bằng Gmail!");
      });
  }
</script>

</body>
</html>
