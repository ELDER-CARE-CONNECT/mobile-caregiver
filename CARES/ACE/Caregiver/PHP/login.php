<?php
session_start();
include 'connect.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM nguoi_cham_soc WHERE ten_tai_khoan = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if ($password === $row['mat_khau']) {
            $_SESSION['caregiver_id'] = $row['id_cham_soc'];
            $_SESSION['caregiver_name'] = $row['ho_ten'];
            header("Location: canhan.php");
            exit;
        } else {
            $error = "Sai mật khẩu!";
        }
    } else {
        $error = "Tài khoản không tồn tại!";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đăng nhập người chăm sóc</title>
<style>
body {
    font-family: "Segoe UI", sans-serif;
    background: linear-gradient(135deg, #ffe8ef, #e8f0ff);
    height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.form-container {
    background: #fff;
    padding: 40px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    width: 360px;
}
h2 {
    text-align: center;
    color: #ff6b81;
}
input {
    width: 100%;
    padding: 10px;
    margin: 8px 0;
    border: 1px solid #ddd;
    border-radius: 8px;
}
button {
    width: 100%;
    background: #ff6b81;
    color: white;
    border: none;
    padding: 10px;
    border-radius: 8px;
    cursor: pointer;
}
button:hover {
    background: #ff4c60;
}
.error {
    color: red;
    text-align: center;
}
</style>
</head>
<body>
<div class="form-container">
    <h2>Đăng nhập</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Tên tài khoản" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>
        <button type="submit">Đăng nhập</button>
        <?php if($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    </form>
</div>
</body>
</html>
