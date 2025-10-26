<?php
$host = '127.0.0.1';
$dbname = 'sanpham';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối DB thất bại: " . $e->getMessage());
}

// Lấy id_don_hang từ GET
$id_don_hang = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_don_hang > 0) {
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

// Xử lý đặt lại (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rebook'])) {
    // Clone đơn hàng mới
    $new_stmt = $pdo->prepare("
        INSERT INTO don_hang (id_khach_hang, id_cham_soc, id_danh_gia, ngay_dat, tong_tien, dia_chi_giao_hang, ten_khach_hang, so_dien_thoai, trang_thai, thoi_gian_bat_dau, thoi_gian_ket_thuc)
        VALUES (:kh, :cs, 0, CURDATE(), :tien, :dia_chi, :ten, :sdt, 'Chờ xác nhận', :bat_dau, :ket_thuc)
    ");
    $new_stmt->execute([
        'kh' => $order['id_khach_hang'],
        'cs' => $order['id_cham_soc'],
        'tien' => $order['tong_tien'],
        'dia_chi' => $order['dia_chi_giao_hang'],
        'ten' => $order['ten_khach_hang'],
        'sdt' => $order['so_dien_thoai'],
        'bat_dau' => $order['thoi_gian_bat_dau'],
        'ket_thuc' => $order['thoi_gian_ket_thuc']
    ]);
    echo "<script>alert('Đơn hàng đã được đặt lại!');</script>";
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Lịch Sử Đơn Hàng - Elder Care Connect</title>
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
        button {
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: block;
            margin: 20px auto 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Chi Tiết Lịch Sử Đơn Hàng</h1>
        
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
            <div class="value"><?php echo htmlspecialchars($order['trang_thai']); ?> (Phương thức: N/A)</div>
        </div>
        
        <div class="section total">
            <div class="label">Tổng tiền:</div>
            <div class="value"><?php echo number_format($order['tong_tien'], 2); ?> VND</div>
        </div>
        
        <form method="POST">
            <button type="submit" name="rebook">Đặt Lại</button>
        </form>
    </div>

    <script>
        // Có thể thêm JS nếu cần
    </script>
</body>
</html>
