<?php
// ===================================================================
// CẤU HÌNH VNPAY CHÍNH THỨC CHO DỰ ÁN CareeSeeker
// ===================================================================

// Terminal ID / Mã Website
define('VNP_TMN_CODE', 'PG2M2Y3P'); 

// Secret Key / Chuỗi bí mật tạo checksum
define('VNP_HASH_SECRET', 'J4OXMCGJNZ1ECXK6WGRGGN5S5V5RGZ35'); 

// URL thanh toán VNPAY Sandbox (Môi trường Test)
define('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'); 

// URL xử lý kết quả thanh toán sau khi khách hàng thanh toán xong (vnpay_return.php)
// PHẢI CHỈ ĐÚNG TÊN THƯ MỤC DỰ ÁN: /CareeSeeker/
// config.php
// File: config.php

// ĐƯỜNG DẪN NÀY LÀ CẦN THIẾT PHẢI ĐÚNG TUYỆT ĐỐI
define('VNP_RETURN_URL', 'http://localhost/ELDER-CARE-CONNECT/CareSeeker/PHP/vnpay_return.php');

// Tên thư mục dự án (để tham khảo)
define('PROJECT_FOLDER', 'CareeSeeker');
?>