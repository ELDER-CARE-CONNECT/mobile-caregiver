<?php
// backend/auth/login.php
session_set_cookie_params(0, '/');
// --- Bắt đầu session nếu chưa có ---
if (session_status() == PHP_SESSION_NONE) session_start();

// --- Thiết lập header JSON ---
header('Content-Type: application/json; charset=utf-8');

// --- Include kết nối database ---
// Đảm bảo đường dẫn này đúng với cấu trúc thư mục của bạn
include_once(__DIR__ . "/../config/connect.php"); 

// Gọi hàm connectdb() (Lúc này đã được định nghĩa ở Bước 1)
$conn = connectdb(); 

// --- Nhận dữ liệu từ frontend ---
$phone = $password = '';

$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (stripos($contentType, 'application/json') !== false) {
    $data = json_decode(file_get_contents('php://input'), true);
    $phone = trim($data['phone'] ?? '');
    $password = trim($data['password'] ?? '');
} else {
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
}

// --- Kiểm tra dữ liệu trống ---
if (empty($phone) || empty($password)) {
    echo json_encode([
        "success" => false,
        "message" => "Số điện thoại và mật khẩu không được để trống!"
    ]);
    exit();
}

// --- Hàm kiểm tra người dùng ---
function checkUser($conn, $table, $fieldUser, $fieldPass, $phone, $password) {
    $sql = "SELECT * FROM $table WHERE $fieldUser = ? AND $fieldPass = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        error_log("Lỗi prepare statement: " . $conn->error);
        return false;
    }

    $stmt->bind_param("ss", $phone, $password);

    if (!$stmt->execute()) {
        error_log("Lỗi execute statement: " . $stmt->error);
        return false;
    }

    return $stmt->get_result();
}

// --- 1. Kiểm tra Admin ---
$result = checkUser($conn, 'admin', 'so_dien_thoai', 'mat_khau', $phone, $password);
if ($result && $result->num_rows > 0) {
    $_SESSION['role'] = 'admin';
    $_SESSION['so_dien_thoai'] = $phone;

    echo json_encode([
        "success" => true,
        "redirect" => "../dashboard/tongquan.php"
    ]);
    exit();
}

// --- 2. Kiểm tra Khách Hàng ---
$result = checkUser($conn, 'khach_hang', 'so_dien_thoai', 'mat_khau', $phone, $password);
if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();

    $_SESSION['role'] = 'khach_hang';
    $_SESSION['so_dien_thoai'] = $phone;
    $_SESSION['ten_khach_hang'] = $user['ten_khach_hang'] ?? '';
    $_SESSION['id_khach_hang'] = $user['id_khach_hang'];

    $redirect = empty($user['ten_khach_hang'])
        ? "../../../CareSeeker/PHP/Frontend/Hoso.php"
        : "../../../CareSeeker/PHP/Frontend/index.php";

    echo json_encode([
        "success" => true,
        "redirect" => $redirect
    ]);
    exit();
}

// --- 3. Kiểm tra Người Chăm Sóc ---
$result = checkUser($conn, 'nguoi_cham_soc', 'so_dien_thoai', 'mat_khau', $phone, $password);
if ($result && $result->num_rows > 0) {
    $_SESSION['role'] = 'nguoi_cham_soc';
    
    // --- SỬA QUAN TRỌNG: Đổi 'So_dien_thoai' thành 'so_dien_thoai' (chữ thường) ---
    // Để khớp với file DonHangChuaNhan.php
    $_SESSION['so_dien_thoai'] = $phone; 

    echo json_encode([
        "success" => true,
        "redirect" => "../../../Caregiver/Fontend/DonHangChuaNhan.php"
    ]);
    exit();
}

// --- Không tìm thấy tài khoản ---
echo json_encode([
    "success" => false,
    "message" => "Sai số điện thoại hoặc mật khẩu!"
]);
exit();
?>