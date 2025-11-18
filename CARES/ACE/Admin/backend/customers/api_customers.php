<?php
// File: ACE/Admin/backend/customers/api_customers.php

// 1. Dọn dẹp bộ nhớ đệm để tránh ký tự lạ làm hỏng JSON
if (ob_get_length()) ob_clean();

session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Tắt hiển thị lỗi ra màn hình (chỉ log lỗi)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// 2. CƠ CHẾ TỰ TÌM FILE KẾT NỐI (Fix lỗi đường dẫn)
$found = false;
$paths_to_check = [
    __DIR__ . '/../config/connect.php',       // Nếu nằm cùng cấp cha (backend/config)
    __DIR__ . '/../../config/connect.php',    // Nếu nằm xa hơn 1 cấp
    __DIR__ . '/../../../config/connect.php', // Nếu nằm xa hơn 2 cấp
    $_SERVER['DOCUMENT_ROOT'] . '/CARES/ACE/Admin/backend/config/connect.php' // Đường dẫn tuyệt đối
];

foreach ($paths_to_check as $path) {
    if (file_exists($path)) {
        require_once $path;
        $found = true;
        break;
    }
}

if (!$found) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy file config/connect.php. Hãy kiểm tra lại cấu trúc thư mục.']);
    exit;
}

if (!function_exists('connectdb')) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'File connect.php không chứa hàm connectdb().']);
    exit;
}

$conn = connectdb();
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Lỗi kết nối CSDL: ' . $conn->connect_error]);
    exit;
}

try {
    $searchTerm = trim($_GET['search'] ?? '');

    if($searchTerm !== '') {
        $stmt = $conn->prepare("SELECT * FROM khach_hang WHERE ten_khach_hang LIKE ? OR so_dien_thoai LIKE ?");
        $like = "%$searchTerm%";
        $stmt->bind_param('ss', $like, $like); 
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT * FROM khach_hang ORDER BY id_khach_hang DESC");
    }

    $customers = [];

    while ($row = $result->fetch_assoc()) {
        $id = (int)$row['id_khach_hang'];

        // Thống kê đơn hàng
        $stmtSum = $conn->prepare("
            SELECT COUNT(*) as tong_don, COALESCE(SUM(tong_tien), 0) as tong_tien 
            FROM don_hang 
            WHERE id_khach_hang = ? AND trang_thai != 'đã hủy'
        ");
        $stmtSum->bind_param('i', $id);
        $stmtSum->execute();
        $summary = $stmtSum->get_result()->fetch_assoc();

        // Xử lý đường dẫn ảnh (Xóa ../ thừa nếu có)
        $imgUrl = $row['hinh_anh'];
        if(!empty($imgUrl) && !str_starts_with($imgUrl, 'http')){
             $imgUrl = str_replace('../', '', $imgUrl); 
        }

        $customers[] = [
            'id_khach_hang' => $id,
            'ten_khach_hang' => $row['ten_khach_hang'],
            'dia_chi' => trim(($row['ten_duong'] ?? '') . ' ' . ($row['phuong_xa'] ?? '') . ' ' . ($row['tinh_thanh'] ?? '')),
            'so_dien_thoai' => $row['so_dien_thoai'] ?? '—',
            'tuoi' => $row['tuoi'] ?? '—',
            'gioi_tinh' => $row['gioi_tinh'] ?? '—',
            'hinh_anh' => $imgUrl,
            'tong_don' => (int)($summary['tong_don'] ?? 0),
            'tong_tien' => (float)($summary['tong_tien'] ?? 0)
        ];
    }

    echo json_encode(['status'=>'success', 'customers'=>$customers], JSON_UNESCAPED_UNICODE);

} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['status'=>'error', 'message'=>'Lỗi server: '.$e->getMessage()]);
}

$conn->close();
?>