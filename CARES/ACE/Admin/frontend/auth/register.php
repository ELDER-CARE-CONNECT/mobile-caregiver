<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng ký</title>

<style>
    body { 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: url("images/nen_dang-nhap.jpg") no-repeat center center fixed;
        background-size: cover;
        min-height: 100vh;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
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
        position: relative;
        z-index: 1;
    }
    .auth-image img {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        object-fit: cover;
        z-index: -1;
    }
    .auth-wrapper {
        flex: 0 0 420px;
        background: rgba(255,255,255,0.95);
        padding: 40px 30px;
        border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        text-align: center;
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
    }
    .auth-error {
        color: red;
        font-size: 14px;
        margin-bottom: 10px;
        white-space: pre-line;
    }
    .popup {
        position: fixed;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        border-radius: 12px;
        padding: 25px 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.25);
        text-align: center;
        z-index: 1000;
        display: none;
    }
    .popup.success {
        border-left: 6px solid #28a745;
    }
</style>

</head>
<body>

<div class="auth-container">
    <div class="auth-image">
        <img src="images/nguoi-cao-tuoi-2.jpg" alt="">
    </div>

    <div class="auth-wrapper">

        <h2 class="auth-heading">Đăng ký</h2>

        <div id="error-message" class="auth-error"></div>

        <form id="registerForm">
            <input type="text" name="phone" class="auth-input" placeholder="Nhập số điện thoại" required>
            <input type="password" name="password" class="auth-input" placeholder="Nhập mật khẩu" required>
            <input type="password" name="confirm_password" class="auth-input" placeholder="Nhập lại mật khẩu" required>
            <button type="submit" class="auth-submit">Đăng ký</button>
        </form>

        <p class="auth-register">
            Bạn đã có tài khoản?
            <a href="login.php" style="color:#d70018;font-weight:bold;">Đăng nhập ngay</a>
        </p>

    </div>
</div>

<!-- Popup thành công -->
<div id="success-popup" class="popup success"></div>

<script>
const form = document.getElementById('registerForm');
const errorBox = document.getElementById('error-message');
const successPopup = document.getElementById('success-popup');

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    errorBox.innerText = "";
    successPopup.style.display = "none";

    const formData = new FormData(form);

    try {
        const res = await fetch("../../backend/auth/register.php", {
            method: "POST",
            body: formData
        });

        const text = await res.text();
        console.log("RAW RESPONSE:", text);

        let data;
        try {
            data = JSON.parse(text);
        } catch (err) {
            errorBox.innerText = "Lỗi server: phản hồi không phải JSON.";
            return;
        }

        if (data.success) {
            successPopup.innerText = data.message || "Đăng ký thành công!";
            successPopup.style.display = "block";

            setTimeout(() => {
                window.location.href = "login.php";
            }, 2000);

        } else {
            if (Array.isArray(data.messages)) {
                errorBox.innerText = data.messages.join("\n");
            } else {
                errorBox.innerText = data.message ?? "Có lỗi xảy ra.";
            }
        }

    } catch (err) {
        errorBox.innerText = "Không thể kết nối server.";
        console.error(err);
    }
});
</script>

</body>
</html>
