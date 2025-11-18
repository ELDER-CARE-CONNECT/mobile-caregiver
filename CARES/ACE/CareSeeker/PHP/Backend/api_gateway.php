<?php
/**
 * API Gateway cho hệ thống Elder Care Connect
 * Điểm trung tâm để xử lý tất cả các API requests
 */

// --- SỬA 1: QUAN TRỌNG - Fix lỗi session không nhận diện được ---
session_set_cookie_params(0, '/'); 
// ----------------------------------------------------------------

// Chỉ start session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- SỬA 3: Chỉ set header JSON nếu chưa gửi header ---
if (!headers_sent()) {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
}

// Xử lý preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';
require_once 'db_connect.php';

// Lấy route từ URL
$request_uri = $_SERVER['REQUEST_URI'];

// --- SỬA 2: Cập nhật đúng tên thư mục dự án của bạn là 'CARES' ---
// Nếu sau này đổi tên thư mục, nhớ đổi dòng này
$base_path = '/CARES/ACE/CareSeeker/PHP/Backend/'; 
// ------------------------------------------------------------------

$route = str_replace($base_path, '', parse_url($request_uri, PHP_URL_PATH));
$route = trim($route, '/');

// Ưu tiên lấy route từ query parameter (Ví dụ: api_gateway.php?route=auth/login)
// Đây là cách an toàn nhất để tránh lỗi đường dẫn
if (isset($_GET['route'])) {
    $route = $_GET['route'];
} elseif (strpos($request_uri, 'api_gateway.php') !== false) {
    // Trường hợp không có param route nhưng gọi trực tiếp file
    $route = $_GET['route'] ?? '';
}

// Request method
$method = $_SERVER['REQUEST_METHOD'];

// Parse request body cho POST/PUT
$input = [];
if (in_array($method, ['POST', 'PUT'])) {
    $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
    
    if (strpos($content_type, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
    } else {
        $input = $_POST;
    }
}

/**
 * Định nghĩa các routes
 */
$routes = [
    // Routes công khai
    'auth/login' => ['file' => 'api_auth.php', 'auth' => false],
    'auth/logout' => ['file' => 'api_auth.php', 'auth' => false], // File này đã sửa ở bước trước
    'auth/register' => ['file' => 'api_auth.php', 'auth' => false],
    
    // Routes cần xác thực
    'canhan' => ['file' => 'api_canhan.php', 'auth' => true],
    'profile' => ['file' => 'api_profile.php', 'auth' => true],
    'user' => ['file' => 'api_user.php', 'auth' => true],
    
    'caregiver/list' => ['file' => 'api_caregiver.php', 'auth' => false],
    'caregiver/featured' => ['file' => 'api_caregiver.php', 'auth' => false],
    'caregiver/details' => ['file' => 'api_caregiver.php', 'auth' => false],
    
    'order/create' => ['file' => 'api_order_create.php', 'auth' => true],
    'order/list' => ['file' => 'api_order_list.php', 'auth' => true],
    'order/details' => ['file' => 'api_order_details.php', 'auth' => true],
    
    'complaint/submit' => ['file' => 'api_complaint.php', 'auth' => true],
    'complaint/send' => ['file' => 'api_guikhieunai.php', 'auth' => true],
    
    'payment/vnpay/create' => ['file' => 'api_payment.php', 'auth' => true],
    'payment/vnpay/return' => ['file' => 'api_payment.php', 'auth' => false],
    
    'rating/submit' => ['file' => 'api_rating.php', 'auth' => true],
];

/**
 * Hàm kiểm tra xác thực
 */
function checkAuth() {
    // Kiểm tra session ID khách hàng
    if (!isset($_SESSION['id_khach_hang'])) {
        sendResponse(401, [
            'success' => false,
            'message' => 'Lỗi xác thực: Vui lòng đăng nhập.'
        ]);
    }
}

/**
 * Hàm gửi response
 */
function sendResponse($statusCode, $data) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Logging request
 */
function logRequest($route, $method, $target_file, $auth_required) {
    // Tạm tắt log để tránh đầy file log server nếu không cần thiết
    // error_log(...)
}

try {
    // Tìm route phù hợp
    $target_route = null;
    $target_file = null;
    $auth_required = true;
    
    // 1. Tìm exact match
    if (isset($routes[$route])) {
        $target_route = $route;
        $target_file = $routes[$route]['file'];
        $auth_required = $routes[$route]['auth'];
    } else {
        // 2. Tìm partial match
        foreach ($routes as $pattern => $config) {
            if (strpos($route, $pattern) === 0) {
                $target_route = $pattern;
                $target_file = $config['file'];
                $auth_required = $config['auth'];
                break;
            }
        }
    }
    
    // Route không tìm thấy
    if (!$target_file) {
        sendResponse(404, [
            'success' => false,
            'message' => 'API endpoint không tồn tại',
            'route' => $route,
            // 'available_routes' => array_keys($routes) // Ẩn đi để bảo mật hơn
        ]);
    }
    
    // Kiểm tra xác thực nếu cần
    if ($auth_required) {
        checkAuth();
    }
    
    // Include target API file
    $file_path = __DIR__ . '/' . $target_file;
    
    if (!file_exists($file_path)) {
        sendResponse(500, [
            'success' => false,
            'message' => 'API file không tồn tại trên server',
            'file' => $target_file
        ]);
    }
    
    // Truyền các biến global để API files sử dụng
    $GLOBALS['api_route'] = $route;
    $GLOBALS['api_method'] = $method;
    $GLOBALS['api_input'] = $input;
    
    // Gọi file xử lý chính
    require_once $file_path;
    
} catch (Exception $e) {
    sendResponse(500, [
        'success' => false,
        'message' => 'Lỗi hệ thống Gateway',
        'error' => $e->getMessage()
    ]);
}
?>