<?php
// backend/auth/reset_password.php

header('Content-Type: application/json; charset=utf-8');
session_start();

<<<<<<< HEAD
try {
    // Include và kiểm tra connect.php
    $connectPath = __DIR__ . "/../config/connect.php";
    if (!file_exists($connectPath)) {
        throw new Exception("File config/connect.php không tồn tại.");
    }
    include_once($connectPath);

    if (!function_exists('connectdb')) {
        throw new Exception("Hàm connectdb() không tồn tại trong connect.php.");
    }

    $conn = connectdb();
    if (!$conn || $conn->connect_error) {
        throw new Exception("Không thể kết nối CSDL: " . ($conn->connect_error ?? "Lỗi không xác định."));
    }

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
    if (!$stmt) {
        throw new Exception("Lỗi prepare query kiểm tra SĐT: " . $conn->error);
    }
    $stmt->bind_param("s", $phone);
    if (!$stmt->execute()) {
        throw new Exception("Lỗi execute query kiểm tra SĐT: " . $stmt->error);
    }
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
    if (!$stmt) {
        throw new Exception("Lỗi prepare query cập nhật mật khẩu: " . $conn->error);
    }
    $stmt->bind_param("ss", $newPassword, $phone);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Đổi mật khẩu thành công!"]);
    } else {
        throw new Exception("Lỗi execute query cập nhật: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    // Log lỗi để debug (trong Docker, check logs của container PHP)
    error_log("Lỗi trong reset_password.php: " . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Lỗi server: " . $e->getMessage()]);
}
?>
=======
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
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
