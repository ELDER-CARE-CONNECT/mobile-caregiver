<?php
// File: Backend/api_order_create.php

// Kiểm tra Auth
if (!isset($_SESSION['id_khach_hang'])) {
    sendResponse(401, ['success' => false, 'message' => 'Bạn phải đăng nhập với tư cách Khách hàng.']);
}

$id_khach_hang_session = $_SESSION['id_khach_hang'];
$pdo = get_pdo_connection();
$errors = [];

try {
    $id_nguoi_cham_soc = intval($_POST['id_nguoi_cham_soc'] ?? 0);
    $tong_tien         = floatval($_POST['tong_tien'] ?? 0);
    $ngay_bat_dau      = $_POST['ngay_bat_dau'] ?? null; 
    $ngay_ket_thuc     = $_POST['ngay_ket_thuc'] ?? null; 
    $gio_bat_dau       = $_POST['gio_bat_dau'] ?? null; 
    $gio_ket_thuc      = $_POST['gio_ket_thuc'] ?? null; 
    $phuong_thuc       = $_POST['phuong_thuc'] ?? 'Tiền mặt'; 

    // Xử lý danh sách dịch vụ
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
    
    // Lấy thông tin khách hàng
    $ten_khach_hang_post = trim($_POST['ten_khach_hang'] ?? '');
    $so_dien_thoai_post  = trim($_POST['so_dien_thoai'] ?? '');
    $dia_chi_post        = trim($_POST['dia_chi'] ?? '');

    $ten_to_insert = '';
    $sdt_to_insert = '';
    $dia_chi_to_insert = '';

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
 
    // Validate dữ liệu
    if ($id_nguoi_cham_soc <= 0) $errors[] = "ID người chăm sóc không hợp lệ.";
    if ($tong_tien <= 0) $errors[] = "Tổng tiền không hợp lệ.";
    if (!$ngay_bat_dau || !$ngay_ket_thuc) $errors[] = "Chưa chọn ngày.";
    if (!$gio_bat_dau || !$gio_ket_thuc) $errors[] = "Chưa chọn giờ.";
    if (empty($selected_services)) $errors[] = "Vui lòng nhập ít nhất một dịch vụ cụ thể.";

    if (!empty($errors)) {
        sendResponse(400, ['success' => false, 'message' => implode(' ', $errors)]);
    }

    $time_start_24h = date('H:i:s', strtotime($gio_bat_dau));
    $time_end_24h = date('H:i:s', strtotime($gio_ket_thuc));
    $start_datetime_full = $ngay_bat_dau . ' ' . $time_start_24h;
    $end_datetime_full = $ngay_ket_thuc . ' ' . $time_end_24h;
    
    // Bắt đầu Transaction
    $pdo->beginTransaction();
    
    $sql_don_hang = "INSERT INTO don_hang (
                        id_khach_hang, id_nguoi_cham_soc, id_danh_gia, tong_tien, dia_chi_giao_hang, 
                        ten_khach_hang, so_dien_thoai, trang_thai, 
                        thoi_gian_bat_dau, thoi_gian_ket_thuc, hinh_thuc_thanh_toan, 
                        thanh_toan_status, ma_giao_dich_vnpay, vnp_ThoiGianThanhToan, 
                        ten_nhiem_vu, trang_thai_nhiem_vu
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt_don_hang = $pdo->prepare($sql_don_hang);
    $params = [
        $id_khach_hang_session, $id_nguoi_cham_soc, 0, $tong_tien, $dia_chi_to_insert,
        $ten_to_insert, $sdt_to_insert, 'chờ xác nhận',
        $start_datetime_full, $end_datetime_full, $phuong_thuc, 
        'chưa thanh toán', NULL, NULL, 
        $ten_nhiem_vu_to_insert, 'chờ xác nhận'
    ];
    
    $stmt_don_hang->execute($params);
    $id_don_hang = $pdo->lastInsertId();
    
    // Insert Nhiệm vụ vào bảng riêng
    if ($id_don_hang > 0 && !empty($selected_services)) {
        $sql_nhiem_vu = "INSERT INTO nhiem_vu (id_don_hang, ten_nhiem_vu, trang_thai_nhiem_vu) VALUES (?, ?, 'chờ xác nhận')";
        $stmt_nhiem_vu = $pdo->prepare($sql_nhiem_vu);
        foreach ($selected_services as $service_name) {
            $stmt_nhiem_vu->execute([$id_don_hang, $service_name]);
        }
    }
    
    $pdo->commit();
    
    // =================================================================
    // XỬ LÝ THANH TOÁN VNPAY (FIXED 100%)
    // =================================================================
    if ($phuong_thuc == 'vnpay') {
        
        // 1. Fix IP Localhost
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        if ($vnp_IpAddr === '::1') {
            $vnp_IpAddr = '127.0.0.1';
        }

        // 2. Tạo mã giao dịch DUY NHẤT (Tránh lỗi Duplicate Transaction - Code 99)
        $vnp_TxnRef = $id_don_hang . "_" . date("YmdHis");
        
        $vnp_Amount = intval($tong_tien * 100);
        $vnp_Locale = 'vn';
        $vnp_OrderInfo = "Thanh toan don hang #" . $id_don_hang;
        $vnp_OrderType = "other";
        
        // Không set vnp_BankCode rỗng ở đây để tránh lỗi hash

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => VNP_TMN_CODE,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => VNP_RETURN_URL,
            "vnp_TxnRef" => $vnp_TxnRef
        );

        // Chỉ thêm BankCode nếu người dùng chọn cụ thể (Ở đây ta để trống để sang VNPAY chọn)
        if (isset($_POST['bank_code']) && !empty($_POST['bank_code'])) {
            $inputData['vnp_BankCode'] = $_POST['bank_code'];
        }

        // Sắp xếp mảng theo key (BẮT BUỘC ĐỂ HASH ĐÚNG)
        ksort($inputData);
        
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            // Tạo chuỗi query (ở phiên bản cũ code bạn bị dư dấu &)
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        // Tạo URL cơ bản (sử dụng hashdata để đảm bảo sạch sẽ)
        $vnp_Url = VNP_URL . "?" . $hashdata;
        
        if (defined('VNP_HASH_SECRET') && VNP_HASH_SECRET != "") {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, VNP_HASH_SECRET);
            // Nối chuỗi hash vào URL
            $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
        }
        
        sendResponse(200, ['success' => true, 'redirect_url' => $vnp_Url]);

    } else {
        // Thanh toán tiền mặt
        sendResponse(200, ['success' => true, 'redirect_url' => "Chitietdonhang.php?id=" . $id_don_hang . "&status=new_cash_order"]); 
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) $pdo->rollback();
    sendResponse(500, ['success' => false, 'message' => "Lỗi tạo đơn: " . $e->getMessage()]);
}
?>