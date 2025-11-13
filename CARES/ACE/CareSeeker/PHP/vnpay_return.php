<?php
session_start();

// 1. Tải cấu hình VNPAY từ file config.php
require_once 'config.php';

// ==========================
// KẾT NỐI DATABASE
// ==========================
$conn = new mysqli("localhost", "root", "", "sanpham");
if ($conn->connect_error) {
    die("Kết nối DB thất bại: " . $conn->connect_error);
}

// ==========================
// XỬ LÝ PHẢN HỒI TỪ VNPAY
// ==========================
$vnp_SecureHash = $_GET['vnp_SecureHash'] ?? '';
$inputData = array();

// Lọc các tham số bắt đầu bằng "vnp_"
foreach ($_GET as $key => $value) {
    if (substr($key, 0, 4) == "vnp_") {
        $inputData[$key] = $value;
    }
}

// Loại bỏ chữ ký khỏi dữ liệu cần hash
unset($inputData['vnp_SecureHash']);
ksort($inputData); // Sắp xếp theo thứ tự alphabet (QUAN TRỌNG)

$hashData = "";
// Tạo chuỗi hashData
foreach ($inputData as $key => $value) {
    $hashData .= urlencode($key) . "=" . urlencode($value) . '&';
}
$hashData = rtrim($hashData, '&'); // Loại bỏ & cuối cùng

// Tạo chữ ký bảo mật để so sánh
$secureHash = hash_hmac('sha512', $hashData, VNP_HASH_SECRET);

// ==========================
// LẤY THÔNG TIN GIAO DỊCH
// ==========================
$orderId = $_GET['vnp_TxnRef'] ?? '';
$RspCode = $_GET['vnp_ResponseCode'] ?? '';
$vnp_Amount = ($_GET['vnp_Amount'] ?? 0) / 100; // Số tiền (đơn vị: xu) -> VNĐ
$vnp_TransactionNo = $_GET['vnp_TransactionNo'] ?? '';
$vnp_PayDate = $_GET['vnp_PayDate'] ?? '';
$vnp_BankCode = $_GET['vnp_BankCode'] ?? '';
$vnp_CardType = $_GET['vnp_CardType'] ?? '';

// Định dạng thời gian VNPAY (YYYYMMDDHHmmss) sang MySQL (YYYY-MM-DD HH:mm:ss)
$paymentTime = null;
if (strlen($vnp_PayDate) == 14) {
    $paymentTime = substr($vnp_PayDate, 0, 4) . '-' . substr($vnp_PayDate, 4, 2) . '-' . substr($vnp_PayDate, 6, 2) . ' ' .
                   substr($vnp_PayDate, 8, 2) . ':' . substr($vnp_PayDate, 10, 2) . ':' . substr($vnp_PayDate, 12, 2);
}

// Xác định Phương thức thanh toán VNPAY (để cập nhật DB)
$paymentMethod = "VNPAY";
if (!empty($vnp_CardType)) {
    $paymentMethod .= " (" . htmlspecialchars($vnp_CardType) . ")"; 
} elseif (!empty($vnp_BankCode)) {
    $paymentMethod .= " (" . htmlspecialchars($vnp_BankCode) . ")";
}

// Biến lưu trạng thái thành công cuối cùng
$isSuccess = false;

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả thanh toán VNPAY</title>
    <style>
        .container {
            max-width: 800px; margin: 50px auto; padding: 30px; border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); font-family: Arial, sans-serif;
        }
        h2 { color: #333; text-align: center; border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 25px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        td { padding: 12px; border-bottom: 1px solid #ddd; }
        td:first-child { font-weight: bold; width: 35%; color: #555; }
        .success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 15px; margin-bottom: 20px; border-radius: 8px; }
        .fail { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 15px; margin-bottom: 20px; border-radius: 8px; }
        .back-btn { text-align: center; }
        .back-btn a { display: inline-block; padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 5px; transition: background-color 0.3s; }
        .back-btn a:hover { background-color: #0056b3; }
    </style>
</head>
<body>

<div class="container">
    <h2>Kết quả thanh toán qua VNPAY</h2>

    <?php
    // Kiểm tra chữ ký an toàn
    if ($secureHash === $vnp_SecureHash) {
        
        // Lấy thông tin đơn hàng hiện tại: tổng tiền, trạng thái dịch vụ, trạng thái thanh toán
        $stmt_check = $conn->prepare("SELECT tong_tien, trang_thai, thanh_toan_status FROM don_hang WHERE id_don_hang = ?");
        $stmt_check->bind_param("i", $orderId);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        
        if ($result_check->num_rows > 0) {
            $order = $result_check->fetch_assoc();
            $dbAmount = $order['tong_tien'];
            $dbServiceStatus = $order['trang_thai']; // Trạng thái dịch vụ
            $dbPaymentStatus = $order['thanh_toan_status']; // Trạng thái thanh toán
            
            // 1. Kiểm tra số tiền
            if (abs($dbAmount - $vnp_Amount) < 0.01) { 
                
                // 2. Kiểm tra trạng thái thanh toán hiện tại
                if ($dbPaymentStatus == 'chưa thanh toán') {
                    
                    if ($RspCode == '00') {
                        // Thành công: Cập nhật trạng thái thanh toán, hình thức TT, và thông tin VNPAY
                        $stmt_update = $conn->prepare("UPDATE don_hang SET 
                            thanh_toan_status = 'đã thanh toán', 
                            hinh_thuc_thanh_toan = ?, 
                            ma_giao_dich_vnpay = ?, 
                            vnp_ThoiGianThanhToan = ? 
                            WHERE id_don_hang = ?");
                        
                        $stmt_update->bind_param("sssi", $paymentMethod, $vnp_TransactionNo, $paymentTime, $orderId);
                        $stmt_update->execute();
                        $stmt_update->close();
                        echo "<p class='success'>✅ Thanh toán VNPAY thành công! Đơn hàng #$orderId đã được cập nhật trạng thái thanh toán.</p>";
                        $isSuccess = true;
                    } else {
                        // Thất bại: Cập nhật trạng thái thanh toán thất bại
                        $stmt_update = $conn->prepare("UPDATE don_hang SET thanh_toan_status = 'thanh toán thất bại' WHERE id_don_hang = ?");
                        $stmt_update->bind_param("i", $orderId);
                        $stmt_update->execute();
                        $stmt_update->close();
                        echo "<p class='fail'>❌ Thanh toán VNPAY thất bại. Mã lỗi VNPAY: " . htmlspecialchars($RspCode) . "</p>";
                    }
                    
                } else {
                    // Đơn hàng đã được xử lý (tránh xử lý lại)
                    echo "<p class='fail'>⚠️ Đơn hàng đã được xử lý trước đó (Trạng thái thanh toán: " . htmlspecialchars($dbPaymentStatus) . ").</p>";
                    // Nếu trạng thái đã là 'đã thanh toán', vẫn coi là thành công khi người dùng truy cập lại.
                    $isSuccess = ($dbPaymentStatus == 'đã thanh toán'); 
                }
            } else {
                // Lỗi Số tiền không khớp (Nghiêm trọng: cần kiểm tra log)
                echo "<p class='fail'>❌ Lỗi bảo mật: Số tiền giao dịch không khớp ({$vnp_Amount} VNĐ). Vui lòng liên hệ hỗ trợ.</p>";
            }

            $stmt_check->close();
        } else {
             echo "<p class='fail'>⚠️ Không tìm thấy đơn hàng #$orderId trong hệ thống.</p>";
        }

    } else {
        echo "<p class='fail'>⚠️ **Sai chữ ký (Invalid Signature)!** Vui lòng kiểm tra lại cấu hình VNPAY (VNP_HASH_SECRET).</p>";
    }
    ?>

    <table>
        <tr><td>Mã giao dịch đơn hàng:</td><td><?= htmlspecialchars($_GET['vnp_TxnRef'] ?? '') ?></td></tr>
        <tr><td>Số tiền:</td><td><?= number_format($vnp_Amount, 0, ',', '.') ?> VNĐ</td></tr>
        <tr><td>Nội dung thanh toán:</td><td><?= htmlspecialchars($_GET['vnp_OrderInfo'] ?? '') ?></td></tr>
        <tr><td>Mã giao dịch VNPAY:</td><td><?= htmlspecialchars($vnp_TransactionNo) ?></td></tr>
        <tr><td>Thời gian thanh toán:</td><td><?= htmlspecialchars($paymentTime ?? 'N/A') ?></td></tr>
        <tr><td>Phương thức thanh toán:</td><td><?= htmlspecialchars($paymentMethod) ?></td></tr>
        <tr><td>Mã phản hồi VNPAY:</td><td><?= htmlspecialchars($RspCode) ?></td></tr>
        <tr><td>Ngân hàng:</td><td><?= htmlspecialchars($_GET['vnp_BankCode'] ?? '') ?></td></tr>
        <tr><td>Mã GD Thẻ:</td><td><?= htmlspecialchars($_GET['vnp_CardType'] ?? '') ?></td></tr>
    </table>

    <div class="back-btn">
        <?php if ($isSuccess): ?>
            <a href="chitietdonhang.php?id=<?= htmlspecialchars($orderId) ?>">
                ➡️ Xem Chi Tiết Đơn Hàng #<?= htmlspecialchars($orderId) ?>
            </a>
        <?php else: ?>
            <a href="chitietdonhang.php">
                ⬅️ Xem Chi Tiết Đơn Hàng
            </a>
        <?php endif; ?>
    </div>
</div>
<?php
// Đóng kết nối cuối file
if (isset($conn) && $conn) {
    $conn->close();
}
?>
</body>
</html>