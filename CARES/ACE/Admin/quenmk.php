<?php
include_once("../model/get_products.php"); 
$conn = connectdb();
session_start();

$phoneError = $passwordError = $confirmPasswordError = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Kiểm tra số điện thoại hợp lệ
    if (!preg_match('/^[0-9]{9,11}$/', $phone)) {
        $phoneError = "Số điện thoại không hợp lệ.";
    } else {
        // Kiểm tra số điện thoại có tồn tại
        $stmt = $conn->prepare("SELECT * FROM khach_hang WHERE so_dien_thoai = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            $phoneError = "Số điện thoại này chưa được đăng ký.";
        } else {
            // Kiểm tra mật khẩu xác nhận
            if ($newPassword !== $confirmPassword) {
                $confirmPasswordError = "Mật khẩu xác nhận không khớp.";
            } elseif (strlen($newPassword) < 6) {
                $passwordError = "Mật khẩu phải có ít nhất 6 ký tự.";
            } else {
                // Cập nhật mật khẩu mới
                $stmt = $conn->prepare("UPDATE khach_hang SET mat_khau = ? WHERE so_dien_thoai = ?");
                $stmt->bind_param("ss", $newPassword, $phone);
                if ($stmt->execute()) {
                    $successMessage = "✅ Đổi mật khẩu thành công! Đang chuyển hướng đến trang đăng nhập...";
                    header("refresh:2;url=login.php");
                } else {
                    $passwordError = "Đã có lỗi xảy ra. Vui lòng thử lại.";
                }
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
    <style>
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> Phong
     /* --- RESET --- */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url("fontend/images/nen_dang-nhap.jpg") no-repeat center center fixed;
    background-size: cover;
    display: flex;
    align-items: center;
    justify-content: flex-start; /* canh phải giống login */
    min-height: 100vh;
    margin: 0;
    color: #333;
}

/* --- LỚP MỜ PHỦ LÊN NỀN --- */
body::before {
    content: "";
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.25);
    z-index: -1;
}

/* --- CONTAINER CHÍNH --- */
.container {
    margin-left: 65%; /* cùng vị trí với login */
    width: 100%;
    max-width: 420px;
    padding: 20px;
}

/* --- KHUNG NGOÀI --- */
.forgot-password-box {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    padding: 40px 30px;
    text-align: center;
    transition: all 0.3s ease;
}

.forgot-password-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

/* --- HEADER TRÊN CÙNG --- */
.header-row {
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    margin-bottom: 15px;
}

.header-row h2 {
    font-size: 26px;
    font-weight: bold;
    color: #d70018;
}

.back-button {
    position: absolute;
    left: 0;
    text-decoration: none;
    font-size: 24px;
    color: #d70018;
    font-weight: bold;
    transition: 0.2s;
}

.back-button:hover {
    transform: translateX(-4px);
}

/* --- MÔ TẢ NGẮN --- */
.forgot-password-box p {
    font-size: 14px;
    color: #444;
    margin-bottom: 20px;
}

/* --- INPUT FORM --- */
form label {
    display: block;
    text-align: left;
    font-weight: 500;
    font-size: 14px;
    margin-bottom: 6px;
    color: #222;
}

form input {
    width: 100%;
    padding: 14px 16px;
    margin-bottom: 18px;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 15px;
    transition: 0.2s;
}

form input:focus {
    outline: none;
    border-color: #d70018;
    box-shadow: 0 0 5px rgba(215, 0, 24, 0.3);
}

/* --- NÚT SUBMIT --- */
.continue-btn {
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

.continue-btn:hover {
    background-color: #b30013;
}

/* --- THÔNG BÁO LỖI / THÀNH CÔNG --- */
#phone-error,
form p {
    color: red;
    font-size: 14px;
    margin-top: -10px;
    margin-bottom: 10px;
}

form p[style*="green"] {
    color: green;
}

/* --- RESPONSIVE --- */
@media (max-width: 768px) {
    body {
        justify-content: center;
        padding: 20px;
    }

    .container {
        margin-left: 0;
        max-width: 90%;
    }

    .forgot-password-box {
        padding: 30px 20px;
    }
}
.forgot-password-box a {
    display: inline-block;
    margin-top: 20px;
    color: #d70018;
    text-decoration: none;
    font-weight: 500;
    font-size: 15px;
    transition: all 0.3s ease;
}
<<<<<<< HEAD
=======

.forgot-password-box a:hover {
    color: #d70018;
    text-decoration: underline;
    text-underline-offset: 3px;
}
.popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 12px;
    padding: 25px 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    text-align: center;
    color: #333;
    animation: fadeIn 0.4s ease forwards;
    z-index: 1000;
}

.popup.success {
    border-left: 6px solid #28a745;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translate(-50%, -60%); }
    to { opacity: 1; transform: translate(-50%, -50%); }
}
    </style>
>>>>>>> Phong

.forgot-password-box a:hover {
    color: #d70018;
    text-decoration: underline;
    text-underline-offset: 3px;
}
.popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    border-radius: 12px;
    padding: 25px 30px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    text-align: center;
    color: #333;
    animation: fadeIn 0.4s ease forwards;
    z-index: 1000;
}

.popup.success {
    border-left: 6px solid #28a745;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translate(-50%, -60%); }
    to { opacity: 1; transform: translate(-50%, -50%); }
}
=======
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
        .back-button {
            position: absolute;
            left: 0;
            text-decoration: none;
            font-size: 24px;
            color: #d70018;
            font-weight: bold;
            transition: 0.2s;
        }
        .back-button:hover {
            transform: translateX(-4px);
        }
        .header-row {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin-bottom: 15px;
        }
        .popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border-radius: 12px;
            padding: 25px 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            text-align: center;
            color: #333;
            animation: fadeIn 0.4s ease forwards;
            z-index: 1000;
        }
        .popup.success {
            border-left: 6px solid #28a745;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translate(-50%, -60%); }
            to { opacity: 1; transform: translate(-50%, -50%); }
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
>>>>>>> Vy
    </style>
</head>
<body>
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> Phong
    <div class="container">
        <div class="forgot-password-box">
            <div class="header-row">
                
                <h2>Quên mật khẩu</h2>
            </div>
            <p>Hãy nhập số điện thoại của bạn vào bên dưới để bắt đầu quá trình khôi phục mật khẩu.</p>

            <!-- Bước 1: Nhập số điện thoại và mật khẩu -->
            <div id="phone-step">
                <form method="POST" action="quenmk.php">
                    <label for="phone">Nhập vào số điện thoại của bạn</label>
                    <input type="text" id="phone" name="phone" placeholder="" required>

                  

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
                    <a href="login.php">Quay lại trang đăng nhập</a> 
                </form>

                <?php if (isset($successMessage)): ?>
                    <p style="color: green;"><?php echo $successMessage; ?></p>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <?php if (!empty($success_message)): ?>
    <div class="popup success">
        <?= htmlspecialchars($success_message) ?>
    </div>
<?php endif; ?>


<<<<<<< HEAD
=======
    <div class="auth-container">
        <div class="auth-image">
            <img src="images/nguoi-cao-tuoi-2.jpg" alt="Ảnh minh họa">
        </div>
        <div class="auth-wrapper">
            <div class="header-row">
                <a href="login.php" class="back-button">&larr;</a>
                <h2 class="auth-heading">Quên mật khẩu</h2>
            </div>
            <p style="font-size: 14px; color: #444; margin-bottom: 20px;">Hãy nhập số điện thoại của bạn vào bên dưới để bắt đầu quá trình khôi phục mật khẩu.</p>

            <form method="POST" action="quenmk.php">
                <label for="phone" style="display: block; text-align: left; font-weight: 500; font-size: 14px; margin-bottom: 6px; color: #222;">Nhập vào số điện thoại của bạn</label>
                <input type="text" id="phone" name="phone" class="auth-input" placeholder="" required>

                <label for="new_password" style="display: block; text-align: left; font-weight: 500; font-size: 14px; margin-bottom: 6px; color: #222;">Nhập mật khẩu mới</label>
                <input type="password" id="new_password" name="new_password" class="auth-input" placeholder="" required>

                <label for="confirm_password" style="display: block; text-align: left; font-weight: 500; font-size: 14px; margin-bottom: 6px; color: #222;">Nhập lại mật khẩu mới</label>
                <input type="password" id="confirm_password" name="confirm_password" class="auth-input" placeholder="" required>

                <div class="auth-error"><?php echo $phoneError; ?></div>
                <div class="auth-error"><?php echo $passwordError; ?></div>
                <div class="auth-error"><?php echo $confirmPasswordError; ?></div>

                <button type="submit" class="auth-submit">Đổi mật khẩu</button>
                <p class="auth-register">
                    <a href="login.php">Quay lại trang đăng nhập</a>
                </p>
            </form>

            <?php if (!empty($successMessage)): ?>
                <div class="popup success">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
>>>>>>> Vy
=======
>>>>>>> Phong
</body>
</html>
