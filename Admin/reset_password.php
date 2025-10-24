<?php
// Gọi kết nối CSDL
include 'connect.php';

// Biến thông báo
$message = "";
$showResetForm = false;

// Xử lý khi submit form nhập email
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['check_email'])) {
    $email = $_POST['email'];

    // Kiểm tra email trong bảng admins
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        // Nếu email tồn tại trong hệ thống, hiển thị form đặt lại mật khẩu
        if ($result->num_rows > 0) {
            $showResetForm = true;
        } else {
            $message = "Email không tồn tại trong hệ thống!";
        }
        $stmt->close();
    } else {
        $message = "Lỗi truy vấn: " . $conn->error;
    }
}

// Xử lý khi submit form đặt lại mật khẩu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $email = $_POST['email'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $message = "Mật khẩu xác nhận không khớp!";
        $showResetForm = true;
    } else {
        // Nếu không sử dụng hashing, mật khẩu sẽ được lưu dưới dạng plain text
        // Không mã hóa mật khẩu
        $password = $newPassword;

        // Chuẩn bị câu lệnh UPDATE
        $update = $conn->prepare("UPDATE admins SET password = ? WHERE email = ?");
        
        if ($update) {
            $update->bind_param("ss", $password, $email);
            
            // Kiểm tra việc thực thi câu lệnh
            if ($update->execute()) {
                $message = "Đặt lại mật khẩu thành công! <a href='admin_login.php'>Đăng nhập</a>";
            } else {
                $message = "Lỗi khi cập nhật mật khẩu: " . $update->error;
            }
            $update->close();
        } else {
            $message = "Lỗi khi chuẩn bị câu lệnh UPDATE: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt Lại Mật Khẩu</title>
    <link rel="stylesheet" href="fontend/css/index.css">
    <style>
        /* Styling here (you can use your existing styles) */
    </style>
</head>
<body>
    <div class="reset-container">
        <h2>Khôi Phục Mật Khẩu</h2>

        <?php if (!$showResetForm): ?>
            <form method="POST">
                <div class="form-group">
                    <label for="email">Nhập Email:</label>
                    <input type="email" name="email" required>
                </div>
                <button type="submit" name="check_email">Tiếp tục</button>
            </form>
        <?php else: ?>
            <form method="POST">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_POST['email']); ?>">
                <div class="form-group">
                    <label for="new_password">Mật khẩu mới:</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Xác nhận mật khẩu:</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" name="reset_password">Đặt lại mật khẩu</button>
            </form>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="back-to-login">
            <a href="admin_login.php">Quay lại đăng nhập</a>
        </div>
    </div>
</body>
</html>
