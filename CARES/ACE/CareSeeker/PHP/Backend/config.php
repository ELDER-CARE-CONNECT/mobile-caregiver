<?php
// CẤU HÌNH VNPAY CHÍNH XÁC (Theo ảnh bạn cung cấp)

date_default_timezone_set('Asia/Ho_Chi_Minh');

// 1. Mã Website (Terminal ID)
define('VNP_TMN_CODE', 'XORMORF8'); 

// 2. Chuỗi bí mật (Secret Key) - Rất quan trọng, không được thừa khoảng trắng
define('VNP_HASH_SECRET', 'M18MN4FMEBS0GA9RFGF8CAYVD34IEVWT'); 

// 3. URL thanh toán (Môi trường TEST)
define('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');

// 4. URL trả về (Return URL) - Phải khớp với cấu hình trên web VNPAY
define('VNP_RETURN_URL', 'http://localhost:8080/ACE/CareSeeker/PHP/Backend/api_gateway.php?route=payment/vnpay/return');
?>