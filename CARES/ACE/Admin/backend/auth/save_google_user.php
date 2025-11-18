<?php
session_set_cookie_params(0, '/');
session_start();
header('Content-Type: application/json; charset=utf-8');

include_once(__DIR__ . "/../config/connect.php");
$conn = connectdb();

// Đọc JSON
$data = json_decode(file_get_contents('php://input'), true);
$email = strtolower(trim($data['email'] ?? ''));
$ten = trim($data['ten_khach_hang'] ?? '');
$hinh = trim($data['hinh_anh'] ?? '');

// Validate
if (!$email) {
    echo json_encode(["success" => false, "message" => "Email không hợp lệ!"]);
    exit;
}

// Tạo số điện thoại giả để hệ thống hoạt động bình thường
$fake_phone = "GG_" . time();
$default_pass = "google_user";

// Kiểm tra tồn tại
$stmt = $conn->prepare("SELECT id_khach_hang FROM khach_hang WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Update
    $stmt = $conn->prepare(
        "UPDATE khach_hang SET ten_khach_hang=?, hinh_anh=? WHERE email=?"
    );
    $stmt->bind_param("sss", $ten, $hinh, $email);
    $stmt->execute();
} else {
    // Insert mới
    $stmt = $conn->prepare(
        "INSERT INTO khach_hang (ten_khach_hang, email, hinh_anh, so_dien_thoai, mat_khau)
         VALUES (?,?,?,?,?)"
    );
    $stmt->bind_param("sssss", $ten, $email, $hinh, $fake_phone, $default_pass);
    $stmt->execute();
}

// Lấy lại thông tin để tạo session
$stmt = $conn->prepare("SELECT id_khach_hang, ten_khach_hang FROM khach_hang WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$_SESSION['role'] = 'khach_hang';
$_SESSION['id_khach_hang'] = $user['id_khach_hang'];
$_SESSION['ten_khach_hang'] = $user['ten_khach_hang'];

echo json_encode(["success" => true]);
exit;
?>
