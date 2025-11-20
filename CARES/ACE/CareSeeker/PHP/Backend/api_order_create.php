<?php
// api_order_create.php

// Session đã được start bởi Gateway
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

require_once 'db_connect.php'; 
require_once 'config.php'; 

if (!isset($_SESSION['id_khach_hang'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Lỗi xác thực: Vui lòng đăng nhập.']);
    exit;
}
$id_khach_hang_session = $_SESSION['id_khach_hang'];

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ.']);
    exit;
}

$errors = [];
$pdo = get_pdo_connection();

try {
    $id_nguoi_cham_soc = intval($_POST['id_nguoi_cham_soc'] ?? 0);
    $tong_tien         = floatval($_POST['tong_tien'] ?? 0);
    $ngay_bat_dau      = $_POST['ngay_bat_dau'] ?? null; 
    $ngay_ket_thuc     = $_POST['ngay_ket_thuc'] ?? null; 
    $gio_bat_dau       = $_POST['gio_bat_dau'] ?? null; 
    $gio_ket_thuc      = $_POST['gio_ket_thuc'] ?? null; 
    $phuong_thuc       = $_POST['phuong_thuc'] ?? 'Tiền mặt'; 

    $raw_services = $_POST['dich_vu'] ?? [];
    $selected_services = [];
    if (is_array($raw_services)) {
        foreach ($raw_services as $service) {
            $service = trim($service);
            if (!empty($service) && !in_array($service, $selected_services)) {
                $selected_services[] = $service;
            }
        }
    }
    $ten_nhiem_vu_to_insert = json_encode($selected_services, JSON_UNESCAPED_UNICODE);
    
    $ten_khach_hang_post = trim($_POST['ten_khach_hang'] ?? '');
    $so_dien_thoai_post  = trim($_POST['so_dien_thoai'] ?? '');
    $dia_chi_post        = trim($_POST['dia_chi'] ?? '');

    $ten_to_insert = '';
    $sdt_to_insert = '';
    $dia_chi_to_insert = '';

    // Xử lý thông tin người đặt
    if (!empty($so_dien_thoai_post)) {
        $ten_to_insert = $ten_khach_hang_post;
        $sdt_to_insert = $so_dien_thoai_post;
        $dia_chi_to_insert = $dia_chi_post;
    } else {
        $stmt_user = $pdo->prepare("SELECT ten_khach_hang, so_dien_thoai, ten_duong, phuong_xa, tinh_thanh FROM khach_hang WHERE id_khach_hang = ?");
        $stmt_user->execute([$id_khach_hang_session]);
        $user_info_raw = $stmt_user->fetch(PDO::FETCH_ASSOC);

        if ($user_info_raw) {
            $ten_to_insert = $user_info_raw['ten_khach_hang'];
            $sdt_to_insert = $user_info_raw['so_dien_thoai'];
            $full_address = array_filter([$user_info_raw['ten_duong'], $user_info_raw['phuong_xa'], $user_info_raw['tinh_thanh']]);
            $dia_chi_to_insert = implode(', ', $full_address);
        }
    }
 
    if ($id_nguoi_cham_soc <= 0) $errors[] = "ID người chăm sóc không hợp lệ.";
    if ($tong_tien <= 0) $errors[] = "Tổng tiền không hợp lệ.";
    if (!$ngay_bat_dau || !$ngay_ket_thuc) $errors[] = "Chưa chọn ngày.";
    if (!$gio_bat_dau || !$gio_ket_thuc) $errors[] = "Chưa chọn giờ.";
    if (empty($sdt_to_insert) || empty($ten_to_insert)) $errors[] = "Thiếu thông tin người đặt.";
    if (empty($selected_services)) $errors[] = "Vui lòng nhập ít nhất một dịch vụ cụ thể.";

    if (!empty($errors)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
        exit;
    }

    $time_start_24h = date('H:i:s', strtotime($gio_bat_dau));
    $time_end_24h = date('H:i:s', strtotime($gio_ket_thuc));
    $start_datetime_full = $ngay_bat_dau . ' ' . $time_start_24h;
    $end_datetime_full = $ngay_ket_thuc . ' ' . $time_end_24h;
    
    // Bắt đầu transaction
    $pdo->beginTransaction();
    
    // 1. Insert vào bảng DON_HANG
    // 16 cột và 16 giá trị
    $sql_don_hang = "INSERT INTO don_hang (
                        id_khach_hang, id_nguoi_cham_soc, id_danh_gia, tong_tien, dia_chi_giao_hang, 
                        ten_khach_hang, so_dien_thoai, trang_thai, 
                        thoi_gian_bat_dau, thoi_gian_ket_thuc, hinh_thuc_thanh_toan, 
                        thanh_toan_status, ma_giao_dich_vnpay, vnp_ThoiGianThanhToan, 
                        ten_nhiem_vu, trang_thai_nhiem_vu
                    ) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_don_hang = $pdo->prepare($sql_don_hang);
    
    // 16 tham số (giá trị)
    $params = [
        $id_khach_hang_session, 
        $id_nguoi_cham_soc, 
        0, // id_danh_gia 
        $tong_tien, 
        $dia_chi_to_insert,
        $ten_to_insert, 
        $sdt_to_insert, 
        'chờ xác nhận', // trang_thai
        $start_datetime_full, 
        $end_datetime_full, 
        $phuong_thuc, 
        'chưa thanh toán', // thanh_toan_status 
        NULL, // ma_giao_dich_vnpay 
        NULL, // vnp_ThoiGianThanhToan 
        $ten_nhiem_vu_to_insert,
        'chờ xác nhận' // trang_thai_nhiem_vu
    ];
    
    $stmt_don_hang->execute($params);
    $id_don_hang = $pdo->lastInsertId();
    
    // 2. Insert chi tiết vào bảng NHIEM_VU 
    if ($id_don_hang > 0 && !empty($selected_services)) {
        $sql_nhiem_vu = "INSERT INTO nhiem_vu (id_don_hang, ten_nhiem_vu, trang_thai_nhiem_vu) VALUES (?, ?, 'chờ xác nhận')";
        $stmt_nhiem_vu = $pdo->prepare($sql_nhiem_vu);
        
        foreach ($selected_services as $service_name) {
            $stmt_nhiem_vu->execute([$id_don_hang, $service_name]);
        }
    }
    
    $pdo->commit();
    
    // Xử lý thanh toán (VNPAY hoặc Tiền mặt)
    if ($phuong_thuc == 'vnpay') {
        $vnp_TmnCode = VNP_TMN_CODE;
        $vnp_HashSecret = VNP_HASH_SECRET;
        $vnp_Url = VNP_URL;
        $vnp_Returnurl = VNP_RETURN_URL;
        $vnp_TxnRef = $id_don_hang; 
        $vnp_OrderInfo = "Thanh toan don hang DICHVU#" . $id_don_hang;
        $vnp_OrderType = 'other';
        $vnp_Amount = $tong_tien * 100; 
        $vnp_Locale = 'vn';
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef
        );
        ksort($inputData);
        $hashData = ""; $query = "";
        foreach ($inputData as $key => $value) {
            $hashData .= ($hashData ? '&' : '') . urlencode($key) . "=" . urlencode($value);
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        $query = trim($query, '&');
        $vnp_Url = VNP_URL . "?" . $query;
        if (VNP_HASH_SECRET != "") {
            $vnpSecureHash = hash_hmac('sha512', $hashData, VNP_HASH_SECRET);
            $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
        }
        echo json_encode(['success' => true, 'redirect_url' => $vnp_Url]);
        exit;
    } else {
        echo json_encode(['success' => true, 'redirect_url' => "Chitietdonhang.php?id=" . $id_don_hang . "&status=new_cash_order"]); 
        exit;
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollback();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "Lỗi giao dịch: " . $e->getMessage()]);
    exit;
}
?>