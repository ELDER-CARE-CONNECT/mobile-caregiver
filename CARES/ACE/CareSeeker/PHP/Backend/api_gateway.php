<?php
/**
 * API GATEWAY TỐI ƯU TỐC ĐỘ
 */

// CẤU HÌNH SESSION TỐI ƯU
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// KIỂM TRA REDIRECT REQUEST NHANH
$is_redirect_request = (($_GET['route'] ?? '') === 'payment/vnpay/return') || (($_GET['action'] ?? '') === 'vnpay_return');

// CHỈ SET HEADER NẾU KHÔNG PHẢI REDIRECT
if (!headers_sent() && !$is_redirect_request) {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}

// XỬ LÝ PREFLIGHT NHANH
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';
require_once 'db_connect.php';

// LẤY ROUTE NHANH
$route = $_GET['route'] ?? '';
if (empty($route)) {
    $request_uri = $_SERVER['REQUEST_URI'];
    $base_path = '/CARES/ACE/CareSeeker/PHP/Backend/';
    $route = str_replace($base_path, '', parse_url($request_uri, PHP_URL_PATH));
    $route = trim($route, '/');
}

$method = $_SERVER['REQUEST_METHOD'];

// PARSE INPUT NHANH
$input = [];
if (in_array($method, ['POST', 'PUT'])) {
    $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
    $input = (strpos($content_type, 'application/json') !== false) 
        ? json_decode(file_get_contents('php://input'), true) 
        : $_POST;
}

// ĐỊNH NGHĨA ROUTES TỐI ƯU
$routes = [
    // PUBLIC ROUTES
    'auth/login' => ['file' => 'api_auth.php', 'auth' => false],
    'auth/logout' => ['file' => 'api_auth.php', 'auth' => false],
    'auth/register' => ['file' => 'api_auth.php', 'auth' => false],
    'caregiver/list' => ['file' => 'api_caregiver.php', 'auth' => false],
    'caregiver/featured' => ['file' => 'api_caregiver.php', 'auth' => false],
    'caregiver/details' => ['file' => 'api_caregiver.php', 'auth' => false],
    'payment/vnpay/return' => ['file' => 'api_payment.php', 'auth' => false],
    
    // PROTECTED ROUTES
    'canhan' => ['file' => 'api_canhan.php', 'auth' => true],
    'profile' => ['file' => 'api_profile.php', 'auth' => true],
    'user' => ['file' => 'api_user.php', 'auth' => true],
    'order/create' => ['file' => 'api_order_create.php', 'auth' => true],
    'order/list' => ['file' => 'api_order_list.php', 'auth' => true],
    'order/details' => ['file' => 'api_order_details.php', 'auth' => true],
    'complaint/submit' => ['file' => 'api_complaint.php', 'auth' => true],
    'complaint/send' => ['file' => 'api_guikhieunai.php', 'auth' => true],
    'payment/vnpay/create' => ['file' => 'api_payment.php', 'auth' => true],
    'rating/submit' => ['file' => 'api_rating.php', 'auth' => true],
];

// HÀM KIỂM TRA AUTH NHANH
function checkAuth() {
    if (!isset($_SESSION['id_khach_hang'])) {
        sendResponse(401, ['success' => false, 'message' => 'Vui lòng đăng nhập']);
    }
}

// HÀM RESPONSE NHANH
function sendResponse($statusCode, $data) {
    http_response_code($statusCode);
    exit(json_encode($data, JSON_UNESCAPED_UNICODE));
}

// XỬ LÝ ROUTE CHÍNH
try {
    // TÌM ROUTE NHANH
    if (!isset($routes[$route])) {
        sendResponse(404, ['success' => false, 'message' => 'API không tồn tại']);
    }
    
    $target_config = $routes[$route];
    $target_file = $target_config['file'];
    $auth_required = $target_config['auth'];
    
    // KIỂM TRA AUTH
    if ($auth_required) {
        checkAuth();
    }
    
    // KIỂM TRA FILE TỒN TẠI
    $file_path = __DIR__ . '/' . $target_file;
    if (!file_exists($file_path)) {
        sendResponse(500, ['success' => false, 'message' => 'Lỗi hệ thống']);
    }
    
    // TRUYỀN BIẾN TOÀN CỤC
    $GLOBALS['api_route'] = $route;
    $GLOBALS['api_method'] = $method;
    $GLOBALS['api_input'] = $input;
    
    // GỌI FILE XỬ LÝ
    require_once $file_path;
    
} catch (Exception $e) {
    sendResponse(500, ['success' => false, 'message' => 'Lỗi hệ thống']);
}
?>