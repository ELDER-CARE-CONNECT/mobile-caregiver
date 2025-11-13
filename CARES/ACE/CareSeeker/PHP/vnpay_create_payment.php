<?php
session_start();
// Tải cấu hình
require_once("config.php");

$vnp_TxnRef = rand(100000,999999); 
$vnp_OrderInfo = "Thanh toan don hang test";
$vnp_OrderType = "billpayment";
$vnp_Amount = $_POST['amount'] * 100; 
$vnp_Locale = "vn";
$vnp_BankCode = "NCB"; 
$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

$inputData = array(
    "vnp_Version" => "2.1.0",
    "vnp_TmnCode" => VNP_TMN_CODE, // SỬA: Dùng hằng số
    "vnp_Amount" => $vnp_Amount,
    "vnp_Command" => "pay",
    "vnp_CreateDate" => date('YmdHis'),
    "vnp_CurrCode" => "VND",
    "vnp_IpAddr" => $vnp_IpAddr,
    "vnp_Locale" => $vnp_Locale,
    "vnp_OrderInfo" => $vnp_OrderInfo,
    "vnp_OrderType" => $vnp_OrderType,
    "vnp_ReturnUrl" => VNP_RETURN_URL, // SỬA: Dùng hằng số
    "vnp_TxnRef" => $vnp_TxnRef
);

ksort($inputData);
$query = "";
$hashdata = "";
foreach ($inputData as $key => $value) {
    $hashdata .= ($hashdata ? '&' : '') . urlencode($key) . "=" . urlencode($value);
    $query .= urlencode($key) . "=" . urlencode($value) . '&';
}

$vnp_Url = VNP_URL . "?" . $query; // SỬA: Dùng hằng số
$vnpSecureHash = hash_hmac('sha512', $hashdata, VNP_HASH_SECRET); // SỬA: Dùng hằng số
$vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

header('Location: ' . $vnp_Url);
exit();
?>