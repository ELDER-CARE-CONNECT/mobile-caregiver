<?php
/**
 * API GATEWAY - TÍCH HỢP TOÀN BỘ MODULE
 */

// 1. Tắt hiển thị lỗi ra màn hình
error_reporting(E_ALL);
ini_set('display_errors', 0);

// 2. Bắt đầu bộ đệm
ob_start();

// CẤU HÌNH SESSION
session_set_cookie_params([
    'lifetime' => 0, 'path' => '/', 'domain' => '', 'secure' => false, 'httponly' => true, 'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// KIỂM TRA REDIRECT REQUEST (VNPAY RETURN)
// Các request này cần trả về HTML (chuyển hướng) chứ không phải JSON
$route_param = $_GET['route'] ?? '';
$is_redirect_request = ($route_param === 'payment/vnpay/return');

// SET HEADER CORS & JSON (Trừ khi là redirect)
if (!headers_sent() && !$is_redirect_request) {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}

// XỬ LÝ PREFLIGHT
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';
require_once 'db_connect.php';

// HÀM PHẢN HỒI CHUẨN
function sendResponse($statusCode, $data) {
    ob_clean(); 
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

// LẤY ROUTE
$route = $_GET['route'] ?? '';
if (empty($route)) {
    $request_uri = $_SERVER['REQUEST_URI'];
    $base_path = '/CARES/ACE/CareSeeker/PHP/Backend/';
    $route = str_replace($base_path, '', parse_url($request_uri, PHP_URL_PATH));
    $route = trim($route, '/');
}

$method = $_SERVER['REQUEST_METHOD'];
$input = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : [];

// =================== BẢNG ĐỊNH TUYẾN ===================
$routes = [
    // Auth
    'auth/login'    => ['file' => 'api_auth.php', 'auth' => false],
    'auth/logout'   => ['file' => 'api_auth.php', 'auth' => false],
    'auth/register' => ['file' => 'api_auth.php', 'auth' => false],

    // User & Profile
    'user'          => ['file' => 'api_user.php',    'auth' => true],
    'profile'       => ['file' => 'api_profile.php', 'auth' => true],
    'canhan'        => ['file' => 'api_canhan.php',  'auth' => true],

    // Caregiver
    'caregiver/list'     => ['file' => 'api_caregiver.php', 'auth' => false],
    'caregiver/featured' => ['file' => 'api_caregiver.php', 'auth' => false],
    'caregiver/details'  => ['file' => 'api_caregiver.php', 'auth' => false],

    // Order & Task
    'order/create'  => ['file' => 'api_order_create.php',  'auth' => true],
    'order/list'    => ['file' => 'api_order_list.php',    'auth' => true],
    'order/details' => ['file' => 'api_order_details.php', 'auth' => true],
    'task/update'   => ['file' => 'api_task_update.php',   'auth' => true],

    // Complaint & Rating
    'complaint/submit' => ['file' => 'api_complaint.php',    'auth' => true],
    'complaint/send'   => ['file' => 'api_guikhieunai.php',  'auth' => true],
    'rating/submit'    => ['file' => 'api_rating.php',       'auth' => true],

    // Payment
    'payment/vnpay/create' => ['file' => 'api_payment.php', 'auth' => true],
    'payment/vnpay/return' => ['file' => 'api_payment.php', 'auth' => false], // Public để VNPAY gọi lại
];

// HÀM KIỂM TRA AUTH
function checkAuth() {
    if (!isset($_SESSION['id_khach_hang']) && !isset($_SESSION['id_cham_soc'])) {
        sendResponse(401, ['success' => false, 'message' => 'Vui lòng đăng nhập (Unauthorized)']);
    }
}

// =================== XỬ LÝ CHÍNH ===================
try {
    if (!isset($routes[$route])) {
        sendResponse(404, ['success' => false, 'message' => 'API Route không tồn tại: ' . $route]);
    }
    
    $target = $routes[$route];
    if ($target['auth']) checkAuth();
    
    $file_path = __DIR__ . '/' . $target['file'];
    if (!file_exists($file_path)) {
        sendResponse(500, ['success' => false, 'message' => 'File backend chưa được tạo: ' . $target['file']]);
    }
    
    // Truyền biến toàn cục
    $GLOBALS['api_route'] = $route;
    $GLOBALS['api_method'] = $method;
    $GLOBALS['api_input'] = $input;
    
    require_once $file_path;
    
} catch (Exception $e) {
    sendResponse(500, ['success' => false, 'message' => 'Lỗi Gateway: ' . $e->getMessage()]);
}
?>