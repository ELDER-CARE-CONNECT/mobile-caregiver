<?php
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
    }

    $stmt->close();
    $conn->close();

} catch (Throwable $e) {
    error_log("Register error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'messages' => ["Lỗi server."]
    ], JSON_UNESCAPED_UNICODE);
}
?>
