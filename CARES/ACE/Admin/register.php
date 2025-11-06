<?php
include_once("../model/get_products.php");
$conn = connectdb();

session_start();

$success_message = "";
$error_messages = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Kiểm tra dữ liệu
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

    // Nếu hợp lệ -> thêm vào DB
    if (empty($error_messages)) {
        $stmt = $conn->prepare("INSERT INTO khach_hang (so_dien_thoai, mat_khau, role) VALUES (?, ?, 0)");
        $stmt->bind_param("ss", $phone, $password);

        if ($stmt->execute()) {
            $success_message = "🎉 Đăng ký thành công! Đang chuyển đến trang đăng nhập...";
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
    <style>
<<<<<<< HEAD
    
   /* --- RESET --- */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

/* --- TOÀN TRANG --- */
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url("fontend/images/nen_dang-nhap.jpg") no-repeat center center fixed;
    background-size: cover;
    display: flex;
    align-items: center;
    justify-content: flex-start;
    min-height: 100vh;
    margin: 0;
    color: #333;
}

/* --- LỚP MỜ NỀN --- */
body::before {
    content: "";
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.25);
    z-index: -1;
}

/* --- KHUNG NGOÀI --- */
.wrapper {
    margin-left: 65%; /* canh phải cho đồng bộ */
    width: 100%;
    max-width: 420px;
    padding: 20px;
}

/* --- KHUNG FORM --- */
.form-container {
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    padding: 40px 30px;
    text-align: center;
    transition: all 0.3s ease;
}

.form-container:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

/* --- TIÊU ĐỀ --- */
.form-container h2 {
    font-size: 26px;
    font-weight: bold;
    color: #d70018;
    margin-bottom: 25px;
}

/* --- Ô NHẬP --- */
.form-container input {
    width: 100%;
    padding: 14px 16px;
    margin-bottom: 15px;
    border: 1px solid #ddd;
    border-radius: 10px;
    font-size: 15px;
    transition: 0.2s;
    box-sizing: border-box; /* tránh tràn */
}

.form-container input:focus {
    outline: none;
    border-color: #d70018;
    box-shadow: 0 0 5px rgba(215, 0, 24, 0.3);
}

/* --- NÚT ĐĂNG KÝ --- */
.register-btn {
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

.register-btn:hover {
    background-color: #b30013;
}

/* --- LIÊN KẾT ĐĂNG NHẬP --- */
.form-container p {
    font-size: 14px;
    margin-top: 20px;
    color: #333;
}

.form-container a {
    color: #d70018;
    font-weight: bold;
    text-decoration: none;
}

.form-container a:hover {
    text-decoration: underline;
}

/* --- THÔNG BÁO LỖI / THÀNH CÔNG --- */
.error-messages ul {
    list-style: none;
    padding-left: 0;
    margin-bottom: 15px;
}

.error-messages li {
    color: red;
    font-size: 14px;
    margin: 4px 0;
}

#success-message {
    color: green;
    font-size: 14px;
    margin-top: 10px;
}

/* --- RESPONSIVE --- */
@media (max-width: 768px) {
    body {
        justify-content: center;
        padding: 20px;
    }

    .wrapper {
        margin-left: 0;
        max-width: 90%;
    }

    .form-container {
        padding: 30px 20px;
    }
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
</head>
<body>

    <div class="wrapper">
        <div class="form-container">
            <h2>Đăng ký</h2>

            <?php if (!empty($error_messages)): ?>
                <div class="error-messages" style="color: red;">
                    <ul>
                        <?php foreach ($error_messages as $msg): ?>
                            <li><?= htmlspecialchars($msg) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>


            <form method="POST" action="register.php">
                <input type="text" name="phone" placeholder="Nhập số điện thoại" required>
                <input type="password" name="password" placeholder="Nhập mật khẩu" required>
                <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu" required>

                <button type="submit" class="register-btn">Đăng ký</button>
            </form>

            <p>Bạn đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
        </div>
    </div>
    <?php if (!empty($success_message)): ?>
    <div class="popup success">
        <?= htmlspecialchars($success_message) ?>
    </div>
<?php endif; ?>


    
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
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-image">
            <img src="images/nguoi-cao-tuoi-2.jpg" alt="Ảnh minh họa">
        </div>
        <div class="auth-wrapper">
            <h2 class="auth-heading">Đăng ký</h2>

            <?php if (!empty($error_messages)): ?>
                <div class="auth-error">
                    <ul style="list-style: none; padding-left: 0;">
                        <?php foreach ($error_messages as $msg): ?>
                            <li><?php echo htmlspecialchars($msg); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <input type="text" name="phone" class="auth-input" placeholder="Nhập số điện thoại" required>
                <input type="password" name="password" class="auth-input" placeholder="Nhập mật khẩu" required>
                <input type="password" name="confirm_password" class="auth-input" placeholder="Nhập lại mật khẩu" required>

                <button type="submit" class="auth-submit">Đăng ký</button>
            </form>

            <p class="auth-register">Bạn đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
        </div>
    </div>

    <?php if (!empty($success_message)): ?>
        <div class="popup success">
            <?php echo htmlspecialchars($success_message); ?>
        </div>
    <?php endif; ?>
>>>>>>> Vy
</body>
</html>