<?php
<<<<<<< HEAD
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
=======
header('Content-Type: application/json; charset=utf-8');

include_once(__DIR__ . "/../config/connect.php"); 
$conn = connectdb();

$response = ['success' => false, 'messages' => []];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Phương thức không hợp lệ!");
    }

    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validate
    if (empty($phone)) $response['messages'][] = "Vui lòng nhập số điện thoại.";
    if (empty($password)) $response['messages'][] = "Vui lòng nhập mật khẩu.";
    if ($password !== $confirm_password) $response['messages'][] = "Mật khẩu xác nhận không khớp.";

    // Nếu có lỗi → trả về luôn
    if (!empty($response['messages'])) {
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Kiểm tra trùng số điện thoại
    $stmt = $conn->prepare("SELECT 1 FROM khach_hang WHERE so_dien_thoai=?");
    if (!$stmt) throw new Exception("Lỗi prepare.");

    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'messages' => ["Số điện thoại đã tồn tại."]
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $stmt->close();

    // KHÔNG HASH mật khẩu - lưu mật khẩu dạng thường
    $plain_password = $password;

    // Insert dữ liệu
    $stmt = $conn->prepare("INSERT INTO khach_hang (so_dien_thoai, mat_khau, role) VALUES (?, ?, 0)");
    $stmt->bind_param("ss", $phone, $plain_password);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => "Đăng ký thành công!"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'messages' => ["Lỗi tạo tài khoản."]
        ], JSON_UNESCAPED_UNICODE);
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
    }

    $stmt->close();
    $conn->close();

} catch (Throwable $e) {
<<<<<<< HEAD
    error_log("Register error: " . $e->__toString());
    echo json_encode(["success" => false, "message" => "Lỗi server."]);
}
=======
    error_log("Register error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'messages' => ["Lỗi server."]
    ], JSON_UNESCAPED_UNICODE);
}
?>
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
