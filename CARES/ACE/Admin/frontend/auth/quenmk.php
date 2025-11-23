<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quên mật khẩu</title>
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
    }
    .auth-wrapper {
        flex: 0 0 420px;
        background: rgba(255,255,255,0.95);
        padding: 40px 35px;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: all 0.3s ease;
    }
    .auth-heading { font-size: 26px; font-weight: bold; color: #FF6B81; margin-bottom: 25px; }
    .auth-input { width: 100%; padding: 14px 5px; margin-bottom: 18px; border: 1px solid #ddd; border-radius: 10px; font-size: 15px; }
    .auth-input:focus { outline: none; border-color: #FF6B81; box-shadow: 0 0 5px rgba(215,0,24,0.3); }
    .auth-error { color: #FF6B81; font-size: 14px; margin-top: 5px; }
    .auth-submit { width: 100%; padding: 14px; background-color: #FF6B81; color: white; border: none; border-radius: 10px; cursor: pointer; font-size: 17px; font-weight: bold; }
    .auth-submit:hover { background-color: #FF6B81; }
    .auth-register { font-size: 14px; margin-top: 25px; }
    .auth-register a { color: #FF6B81; font-weight: bold; text-decoration: none; }
    .auth-register a:hover { text-decoration: underline; }
    .back-button { position: absolute; left: 0; text-decoration: none; font-size: 24px; color: #FF6B81; font-weight: bold; }
    .header-row { display: flex; align-items: center; justify-content: center; position: relative; margin-bottom: 15px; }
    .popup { position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; border-radius: 12px; padding: 25px 30px; box-shadow: 0 4px 20px rgba(0,0,0,0.2); text-align: center; color: #333; z-index: 1000; display: none; }
    .popup.success { border-left: 6px solid #28a745; }
</style>
</head>
<body>
<div class="auth-container">
    <div class="auth-image">
        <img src="images/nguoi-cao-tuoi-2.jpg" alt="Ảnh minh họa">
    </div>
    <div class="auth-wrapper">
        <div class="header-row">
            <a href="login.php" class="back-button">&larr;</a>
            <h2 class="auth-heading">Quên mật khẩu</h2>
        </div>
        <p style="font-size: 14px; color: #444; margin-bottom: 20px;">
            Hãy nhập số điện thoại của bạn để khôi phục mật khẩu.
        </p>

        <form id="forgotPasswordForm">
            <label for="phone">Số điện thoại</label>
            <input type="text" id="phone" name="phone" class="auth-input" required>

            <label for="new_password">Mật khẩu mới</label>
            <input type="password" id="new_password" name="new_password" class="auth-input" required>

            <label for="confirm_password">Xác nhận mật khẩu</label>
            <input type="password" id="confirm_password" name="confirm_password" class="auth-input" required>

            <div class="auth-error" id="error-message"></div>

            <button type="submit" class="auth-submit">Đổi mật khẩu</button>
            <p class="auth-register"><a href="login.php">Quay lại trang đăng nhập</a></p>
        </form>

        <div id="success-popup" class="popup success"></div>
    </div>
</div>

<script>
const form = document.getElementById('forgotPasswordForm');
const errorDiv = document.getElementById('error-message');
const successPopup = document.getElementById('success-popup');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorDiv.innerText = '';
    successPopup.style.display = 'none';

    const formData = new FormData(form);

    try {
        const res = await fetch('../../backend/auth/reset_password.php', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if(data.success){
            successPopup.innerText = data.message;
            successPopup.style.display = 'block';
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 2000);
        } else {
            errorDiv.innerText = data.message;
        }
    } catch(err){
        console.error(err);
        errorDiv.innerText = 'Đã có lỗi xảy ra. Vui lòng thử lại.';
    }
});
</script>
</body>
</html>
