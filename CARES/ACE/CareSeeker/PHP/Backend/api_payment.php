<?php
// File: Backend/api_payment.php - PHẦN XỬ LÝ RETURN
// Đã chuyển đổi sang PDO để tương thích với api_gateway.php

if (strpos($route, 'return') !== false) {
    $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '99';
    $vnp_TxnRef_Raw   = $_GET['vnp_TxnRef'] ?? '';
    
    // TÁCH VNPAY TxnRef: ID_TimeStamp -> Lấy ID đơn hàng
    $parts = explode('_', $vnp_TxnRef_Raw);
    $vnp_TxnRef = intval($parts[0]); 

    $vnp_TransactionNo = $_GET['vnp_TransactionNo'] ?? '';
    
    // URL Frontend đích
    $frontend_base_url = "http://localhost:8080/ACE/CareSeeker/PHP/Frontend/Chitietdonhang.php";
    
    // Sử dụng PDO connection từ Gateway
    $pdo = get_pdo_connection();

    if ($vnp_ResponseCode === '00') {
        // --- THÀNH CÔNG ---
        $sql = "UPDATE don_hang SET 
                thanh_toan_status = 'Đã thanh toán', 
                ma_giao_dich_vnpay = ?,
                vnp_ThoiGianThanhToan = NOW()
                WHERE id_don_hang = ?";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$vnp_TransactionNo, $vnp_TxnRef]);
            
            $redirect_url = $frontend_base_url . "?id=" . $vnp_TxnRef . "&payment=success";
        } catch (Exception $e) {
            // Trường hợp lỗi DB update dù thanh toán thành công
            $redirect_url = $frontend_base_url . "?id=" . $vnp_TxnRef . "&payment=error_db";
        }

    } else {
        // --- THẤT BẠI ---
        $redirect_url = $frontend_base_url . "?id=" . $vnp_TxnRef . "&payment=failed";
    }
    
    header("Location: " . $redirect_url);
    exit();
}
?>