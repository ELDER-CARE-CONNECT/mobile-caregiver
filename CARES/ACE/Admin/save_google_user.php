<?php
session_start();
include_once("connect.php");

$data = json_decode(file_get_contents("php://input"), true);

if ($data && isset($data['email'])) {
    $ten_khach_hang = trim($data['ten_khach_hang'] ?? '');
    $email = trim(strtolower($data['email']));
    $hinh_anh = trim($data['hinh_anh'] ?? '');

    // Kiểm tra email hợp lệ
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["success" => false, "message" => "Email không hợp lệ!"]);
        exit;
    }

    // Kiểm tra email đã có trong DB chưa
    $check = $conn->prepare("SELECT id_khach_hang FROM khach_hang WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Cập nhật lại tên & ảnh nếu có thay đổi
        $stmt = $conn->prepare("UPDATE khach_hang SET ten_khach_hang=?, hinh_anh=? WHERE email=?");
        $stmt->bind_param("sss", $ten_khach_hang, $hinh_anh, $email);
        $stmt->execute();
    } else {
        // Thêm mới nếu chưa có
        $stmt = $conn->prepare("INSERT INTO khach_hang (ten_khach_hang, email, hinh_anh) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $ten_khach_hang, $email, $hinh_anh);
        $stmt->execute();
    }

    // Lấy lại thông tin người dùng
    $stmt_user = $conn->prepare("SELECT * FROM khach_hang WHERE email = ?");
    $stmt_user->bind_param("s", $email);
    $stmt_user->execute();
    $user = $stmt_user->get_result()->fetch_assoc();

    // Tạo session
    $_SESSION['role'] = 'khach_hang';
    $_SESSION['id_khach_hang'] = $user['id_khach_hang'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['ten_khach_hang'] = $user['ten_khach_hang'];

    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Dữ liệu không hợp lệ!"]);
}
?>
