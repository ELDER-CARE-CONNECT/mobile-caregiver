<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Đăng nhập</title>
<style>
    body { 
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-size: cover;
        min-height: 100vh;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        box-sizing: border-box;
    }
    .auth-container { display: flex; gap: 50px; background: rgba(255,255,255,0.85); border-radius: 20px;
        padding: 40px; max-width: 940px; width: 100%; box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        align-items: center; justify-content: center; box-sizing: border-box; position: relative; z-index: 1; }
    .auth-image { flex: 1; display: flex; justify-content: center; align-items: center; }
    .auth-image img { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1; object-fit: cover; }
    .auth-wrapper { flex: 0 0 420px; background: rgba(255,255,255,0.95); padding: 40px 30px; border-radius: 16px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1); text-align: center; transition: all 0.3s ease; }
    .auth-wrapper:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
    .auth-logo { width: 90px; margin-bottom: 20px; }
    .auth-heading { font-size:26px; font-weight:bold; color:#d70018; margin-bottom:25px; }
    .auth-input { width:100%; padding:14px 18px; margin-bottom:18px; border:1px solid #ddd; border-radius:10px; font-size:15px; box-sizing:border-box; }
    .auth-input:focus { outline:none; border-color:#d70018; box-shadow:0 0 5px rgba(215,0,24,0.3); }
    .auth-error { color:red; font-size:14px; margin-top:5px; }
    .auth-forgot { text-align:right; font-size:14px; margin-top:5px; margin-bottom:20px; }
    .auth-forgot a { color:#d70018; text-decoration:none; font-weight:500; }
    .auth-forgot a:hover { text-decoration:underline; }
    .auth-submit { width:100%; padding:14px; background-color:#d70018; color:white; border:none; border-radius:10px; cursor:pointer; font-size:17px; font-weight:bold; transition:background 0.3s ease; }
    .auth-submit:hover { background-color:#b30013; }
    .auth-register { font-size:14px; margin-top:25px; }
    .auth-register a { color:#d70018; font-weight:bold; text-decoration:none; }
    .auth-register a:hover { text-decoration:underline; }
    .google-btn { width:100%; background-color:#db4437; color:white; border:none; border-radius:10px; padding:12px; margin-top:15px; font-size:16px; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:10px; }
    .google-btn img { width:20px; height:20px; }
    @media(max-width:900px){
        body{padding:10px;}
        .auth-container{flex-direction:column;padding:20px 15px;max-width:100%;box-shadow:none;background:transparent;}
        .auth-image{display:none;}
        .auth-wrapper{width:100%;max-width:420px;padding:30px 20px;box-shadow:0 8px 25px rgba(0,0,0,0.1);background:rgba(255,255,255,0.95);border-radius:16px;}
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

        <form id="loginForm">
            <input type="text" placeholder="Nhập số điện thoại" class="auth-input" name="phone" id="phone">
            <input type="password" placeholder="Nhập mật khẩu" class="auth-input" name="password" id="password">
            <div id="error-message" class="auth-error"></div>

            <div class="auth-forgot">
                <a href="quenmk.php">Quên mật khẩu?</a>
            </div>

            <button class="auth-submit" type="submit">Đăng nhập</button>
        </form>

        <button class="google-btn" type="button" id="googleLoginBtn">
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

// --- Google login ---
document.getElementById('googleLoginBtn').addEventListener('click', async ()=>{
    try{
        const result = await signInWithPopup(auth, provider);
        const user = result.user;
        const res = await fetch('../../backend/auth/save_google_user.php', {
            method:'POST',
            headers:{'Content-Type':'application/json'},
            body:JSON.stringify({
                ten_khach_hang:user.displayName,
                email:user.email,
                hinh_anh:user.photoURL
            })
        });
        const data = await res.json();
        if(data.success) window.location.href="../CareSeeker/PHP/index.php";
        else alert("Lỗi lưu thông tin Google: "+(data.message||""));
    }catch(err){ console.error(err); alert("Đăng nhập bằng Google thất bại!"); }
});

// --- Username/Password login ---
document.getElementById('loginForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const formData = new FormData(e.target);

    try{
        const res = await fetch('../../backend/auth/login.php', {
            method:'POST',
            body:formData
        });

        const text = await res.text();
        let data;
        try{
            data = JSON.parse(text);
        }catch(err){
            console.error("Response không phải JSON:", text);
            alert("Lỗi server! Vui lòng thử lại.");
            return;
        }

        if(data.success){
            window.location.href = data.redirect;
        } else {
            document.getElementById('error-message').innerText = data.message;
        }
    }catch(err){
        console.error(err);
        alert("Lỗi khi đăng nhập!");
    }
});
</script>
</body>
</html>