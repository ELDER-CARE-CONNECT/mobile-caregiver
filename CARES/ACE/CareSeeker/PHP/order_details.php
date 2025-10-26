<?php
$host = '127.0.0.1';
$dbname = 'sanpham';
$username = 'root'; // Thay bằng username DB của bạn
$password = ''; // Thay bằng password DB của bạn

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối DB thất bại: " . $e->getMessage());
}

// Lấy id_don_hang từ GET
$id_don_hang = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_don_hang > 0) {
    // Truy vấn đơn hàng và thông tin liên quan
    $stmt = $pdo->prepare("
        SELECT dh.*, kh.ten_khach_hang AS ten_kh, kh.so_dien_thoai, kh.dia_chi,
               ncs.ho_ten AS ten_cham_soc
        FROM don_hang dh
        LEFT JOIN khach_hang kh ON dh.id_khach_hang = kh.id_khach_hang
        LEFT JOIN nguoi_cham_soc ncs ON dh.id_cham_soc = ncs.id_cham_soc
        WHERE dh.id_don_hang = :id
    ");
    $stmt->execute(['id' => $id_don_hang]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $order = null;
}

if (!$order) {
    echo "Không tìm thấy đơn hàng.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng - Elder Care Connect</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .section {
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
            color: #555;
        }
        .value {
            color: #000;
        }
        .total {
            font-size: 1.2em;
            font-weight: bold;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Chi Tiết Đơn Hàng</h1>
        
        <div class="section">
            <div class="label">Thông tin khách hàng:</div>
            <div class="value">Tên: <?php echo htmlspecialchars($order['ten_khach_hang'] ?? $order['ten_kh']); ?><br>Địa chỉ: <?php echo htmlspecialchars($order['dia_chi_giao_hang'] ?? $order['dia_chi']); ?><br>Số điện thoại: <?php echo htmlspecialchars($order['so_dien_thoai']); ?><br>Email: <?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></div>
        </div>
        
        <div class="section">
            <div class="label">Ngày và giờ hẹn:</div>
            <div class="value">Ngày: <?php echo htmlspecialchars($order['ngay_dat']); ?><br>Giờ: <?php echo htmlspecialchars($order['thoi_gian_bat_dau'] . ' - ' . $order['thoi_gian_ket_thuc']); ?></div>
        </div>
        
        <div class="section">
            <div class="label">Phương thức thanh toán:</div>
            <div class="value"><?php echo htmlspecialchars($order['trang_thai']); ?> (Phương thức: N/A - Cập nhật sau)</div>
        </div>
        
        <div class="section total">
            <div class="label">Tổng tiền:</div>
            <div class="value"><?php echo number_format($order['tong_tien'], 2); ?> VND</div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Trang chi tiết đơn hàng đã tải.');
        });
    </script>
</body>
</html>