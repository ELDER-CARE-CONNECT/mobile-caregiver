<?php
// backend/auth/reset_password.php

header('Content-Type: application/json; charset=utf-8');
session_start();

include_once(__DIR__ . "/../config/connect.php");
$conn = connectdb();

// --- Nhận dữ liệu ---
$phone = trim($_POST['phone'] ?? '');
$newPassword = trim($_POST['new_password'] ?? '');
$confirmPassword = trim($_POST['confirm_password'] ?? '');

// --- Kiểm tra dữ liệu ---
if (empty($phone) || empty($newPassword) || empty($confirmPassword)) {
    echo json_encode(["success" => false, "message" => "Vui lòng điền đầy đủ thông tin!"]);
    exit();
}

// --- Kiểm tra định dạng số điện thoại ---
if (!preg_match('/^[0-9]{9,11}$/', $phone)) {
    echo json_encode(["success" => false, "message" => "Số điện thoại không hợp lệ."]);
    exit();
}

// --- Kiểm tra tồn tại người dùng ---
$stmt = $conn->prepare("SELECT 1 FROM khach_hang WHERE so_dien_thoai = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

if ($result->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "Số điện thoại chưa được đăng ký."]);
    exit();
}

// --- Kiểm tra mật khẩu ---
if ($newPassword !== $confirmPassword) {
    echo json_encode(["success" => false, "message" => "Mật khẩu xác nhận không khớp."]);
    exit();
}

if (strlen($newPassword) < 6) {
    echo json_encode(["success" => false, "message" => "Mật khẩu phải có ít nhất 6 ký tự."]);
    exit();
}

// --- Cập nhật mật khẩu trực tiếp (không hash) ---
$stmt = $conn->prepare("UPDATE khach_hang SET mat_khau = ? WHERE so_dien_thoai = ?");
$stmt->bind_param("ss", $newPassword, $phone);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Đổi mật khẩu thành công!"]);
} else {
    echo json_encode(["success" => false, "message" => "Đã có lỗi xảy ra. Vui lòng thử lại."]);
}

$stmt->close();
$conn->close();
?>
