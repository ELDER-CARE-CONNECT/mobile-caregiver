<?php
// backend/auth/register.php
header('Content-Type: application/json; charset=utf-8');
session_start();

include_once(__DIR__ . "/../config/connect.php");

try {
    $conn = connectdb();
    if (!$conn) throw new Exception("Không thể kết nối CSDL.");

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(["success" => false, "message" => "Phương thức không hợp lệ."]);
        exit();
    }

    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (!$phone || !$password || !$confirmPassword) {
        echo json_encode(["success" => false, "message" => "Vui lòng điền đầy đủ thông tin!"]);
        exit();
    }

    if (!preg_match('/^[0-9]{9,11}$/', $phone)) {
        echo json_encode(["success" => false, "message" => "Số điện thoại không hợp lệ."]);
        exit();
    }

    if ($password !== $confirmPassword) {
        echo json_encode(["success" => false, "message" => "Mật khẩu xác nhận không khớp."]);
        exit();
    }

    if (strlen($password) < 6) {
        echo json_encode(["success" => false, "message" => "Mật khẩu phải có ít nhất 6 ký tự."]);
        exit();
    }

    // Kiểm tra số điện thoại đã tồn tại
    $stmt = $conn->prepare("SELECT 1 FROM khach_hang WHERE so_dien_thoai = ?");
    if (!$stmt) throw new Exception("Lỗi prepare: " . $conn->error);
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Số điện thoại đã được đăng ký."]);
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert với giá trị mặc định cho các cột NOT NULL
    $stmt = $conn->prepare("
        INSERT INTO khach_hang 
        (so_dien_thoai, mat_khau, role, ten_khach_hang, tuoi, gioi_tinh, chieu_cao, can_nang, hinh_anh)
        VALUES (?, ?, 0, '', 0, 'Nam', 0, 0, '')
    ");
    if (!$stmt) throw new Exception("Lỗi prepare insert: " . $conn->error);
    $stmt->bind_param("ss", $phone, $hashedPassword);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Đăng ký thành công!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Lỗi khi thêm người dùng: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();

} catch (Throwable $e) {
    error_log("Register error: " . $e->__toString());
    echo json_encode(["success" => false, "message" => "Lỗi server."]);
}
