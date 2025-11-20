<?php
// TẮT HOÀN TOÀN BUFFER VÀ BẮT ĐẦU SESSION
if (ob_get_level()) ob_end_clean();
session_start();

require_once 'config.php'; 
require_once 'db_connect.php'; 

$action = $_GET['action'] ?? $_POST['action'] ?? '';

// QUAN TRỌNG: VNPAY RETURN KHÔNG SET JSON HEADER
if ($action !== 'vnpay_return' && !headers_sent()) {
    header('Content-Type: application/json');
}

// XỬ LÝ VNPAY RETURN - ƯU TIÊN SỐ 1
if ($action === 'vnpay_return' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '99';
    $vnp_TxnRef = intval($_GET['vnp_TxnRef'] ?? 0);
    
    // URL CHUYỂN HƯỚNG CUỐI CÙNG - CHẮC CHẮN 100%
    $frontend_url = "http://localhost:8080/CARES/ACE/CareSeeker/PHP/Frontend/Chitietdonhang.php";
    
    try {
        $pdo = get_pdo_connection();
        
        if ($vnp_ResponseCode === '00') {
            // THANH TOÁN THÀNH CÔNG
            $sql = "UPDATE don_hang SET 
                    thanh_toan_status = 'đã thanh toán', 
                    ma_giao_dich_vnpay = ?,
                    vnp_ThoiGianThanhToan = NOW()
                    WHERE id_don_hang = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_GET['vnp_TransactionNo'] ?? null, $vnp_TxnRef]);
            
            $redirect_url = $frontend_url . "?id=" . $vnp_TxnRef . "&payment=success";
        } else {
            // THANH TOÁN THẤT BẠI
            $redirect_url = $frontend_url . "?id=" . $vnp_TxnRef . "&payment=failed";
        }
        
        // CHUYỂN HƯỚNG NGAY LẬP TỨC - KHÔNG CÓ BẤT KỲ OUTPUT NÀO
        header("Location: " . $redirect_url);
        exit();
        
    } catch (Exception $e) {
        // NẾU CÓ LỖI, VẪN CHUYỂN HƯỚNG VỀ TRANG ĐÍCH
        $redirect_url = $frontend_url . "?id=" . $vnp_TxnRef . "&payment=error";
        header("Location: " . $redirect_url);
        exit();
    }
}

// KIỂM TRA AUTH CHO CÁC ACTION KHÁC
if (!isset($_SESSION['id_khach_hang']) && $action !== 'vnpay_return') {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Lỗi xác thực']);
    exit();
}

$id_khach_hang = $_SESSION['id_khach_hang'] ?? 0;

try {
    // ACTION: TẠO URL THANH TOÁN
    if ($action === 'create_vnpay_url' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $order_id = intval($_POST['order_id'] ?? 0);
        $total_amount = floatval($_POST['total_amount'] ?? 0);

        if ($order_id === 0 || $total_amount === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đơn hàng']);
            exit();
        }
        
        $pdo = get_pdo_connection();
        
        // DỮ LIỆU CẤU HÌNH VNPAY
        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => VNP_TMN_CODE,
            "vnp_Amount" => $total_amount * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $_SERVER['REMOTE_ADDR'],
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Thanh toan don hang #$order_id",
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => VNP_RETURN_URL,
            "vnp_TxnRef" => $order_id,
        ];

        // TẠO URL VNPAY
        ksort($inputData);
        $query = http_build_query($inputData);
        $hashdata = implode('&', array_map(function($k, $v) { 
            return urlencode($k) . "=" . urlencode($v); 
        }, array_keys($inputData), $inputData));
        
        $vnpSecureHash = hash_hmac('sha512', $hashdata, VNP_HASH_SECRET);
        $vnp_Url = VNP_URL . "?" . $query . "&vnp_SecureHash=" . $vnpSecureHash;

        echo json_encode(['success' => true, 'payment_url' => $vnp_Url]);
        exit();
    }
    
    // ACTION KHÔNG HỢP LỆ
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống']);
}
?>