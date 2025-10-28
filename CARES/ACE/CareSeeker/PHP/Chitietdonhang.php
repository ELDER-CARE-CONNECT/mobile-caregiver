<?php
session_start(); // Khởi tạo session

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

// Biến để lưu thông tin đơn hàng mới nhất
$order = [];

if (isset($_SESSION['role']) && $_SESSION['role'] === 'khach_hang' && isset($_SESSION['so_dien_thoai'])) {
    $so_dien_thoai = $_SESSION['so_dien_thoai'];

    // Lấy id_khach_hang và ten_khach_hang dựa trên so_dien_thoai
    $stmt_kh = $pdo->prepare("SELECT id_khach_hang, ten_khach_hang FROM khach_hang WHERE so_dien_thoai = ?");
    $stmt_kh->execute([$so_dien_thoai]);
    $user = $stmt_kh->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $id_khach_hang = $user['id_khach_hang'];
        $_SESSION['ten_khach_hang'] = $user['ten_khach_hang']; // Đảm bảo tên được lưu trong session

        // Lấy đơn hàng mới nhất
        $stmt = $pdo->prepare("
            SELECT dh.*, kh.ten_khach_hang, kh.so_dien_thoai, kh.dia_chi,
                   ncs.ho_ten AS ten_cham_soc, ncs.id_cham_soc AS caregiver_id
            FROM don_hang dh
            LEFT JOIN khach_hang kh ON dh.id_khach_hang = kh.id_khach_hang
            LEFT JOIN nguoi_cham_soc ncs ON dh.id_cham_soc = ncs.id_cham_soc
            WHERE dh.id_khach_hang = :id_kh
            ORDER BY dh.ngay_dat DESC
            LIMIT 1
        ");
        $stmt->bindValue(':id_kh', $id_khach_hang, PDO::PARAM_INT);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Xử lý hủy đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel']) && isset($_POST['id']) && $order) {
    $id_to_cancel = intval($_POST['id']);
    $stmt_check = $pdo->prepare("SELECT trang_thai FROM don_hang WHERE id_don_hang = :id");
    $stmt_check->execute(['id' => $id_to_cancel]);
    $order_status = $stmt_check->fetchColumn();

    if ($order_status === 'Chờ xác nhận') {
        $stmt_delete = $pdo->prepare("DELETE FROM don_hang WHERE id_don_hang = :id");
        $stmt_delete->execute(['id' => $id_to_cancel]);
        echo "<script>alert('Đơn hàng đã được hủy!'); window.location.href='Chitietdonhang.php';</script>";
    } else {
        echo "<script>alert('Chỉ có thể hủy đơn khi trạng thái là Chờ xác nhận!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
            position: relative;
        }
        .user-info {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #333;
            font-size: 16px;
            font-weight: bold;
        }
        h1 {
            color: #1a73e8;
            text-align: center;
            margin-bottom: 20px;
            margin-top: 40px;
        }
        .invoice {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            background-color: #fff;
            margin-bottom: 20px;
        }
        .invoice-header {
            border-bottom: 2px solid #1a73e8;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .invoice-header h2 {
            color: #333;
            font-size: 1.5em;
            margin: 0;
        }
        .invoice-details {
            margin: 15px 0;
            color: #555;
        }
        .invoice-details .label {
            font-weight: bold;
            color: #333;
        }
        .invoice-details div {
            margin: 8px 0;
        }
        .invoice-total {
            border-top: 2px solid #e0e0e0;
            padding-top: 15px;
            text-align: right;
            font-size: 1.3em;
            font-weight: bold;
            color: #d32f2f;
        }
        .buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }
        button, a.button {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            color: white;
            font-size: 16px;
        }
        .btn-cancel {
            background-color: #f44336;
        }
        .btn-cancel:hover {
            background-color: #d32f2f;
        }
        .btn-chat {
            background-color: #4caf50;
        }
        .btn-chat:hover {
            background-color: #45a049;
        }
        .btn-home {
            background-color: #1a73e8;
        }
        .btn-home:hover {
            background-color: #1557a0;
        }
        .no-orders {
            color: #d32f2f;
            text-align: center;
            margin-top: 20px;
        }
        @media (max-width: 600px) {
            .container { padding: 15px; }
            .invoice { padding: 15px; }
            .buttons { flex-direction: column; }
            button, a.button { width: 100%; margin-bottom: 10px; }
            .user-info { left: 10px; font-size: 14px; }
            h1 { margin-top: 50px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'khach_hang' && isset($_SESSION['ten_khach_hang'])): ?>
            <div class="user-info">Chào, <?php echo htmlspecialchars($_SESSION['ten_khach_hang']); ?>!</div>
        <?php endif; ?>
        <h1>Chi Tiết Đơn Hàng</h1>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'khach_hang' && isset($_SESSION['so_dien_thoai'])): ?>
            <?php if (empty($order)): ?>
                <div class="no-orders">Bạn chưa có đơn hàng nào.</div>
            <?php else: ?>
                <div class="invoice">
                    <div class="invoice-header">
                        <h2>Hóa Đơn Thanh Toán #<?php echo htmlspecialchars($order['id_don_hang']); ?></h2>
                    </div>
                    <div class="invoice-details">
                        <div><span class="label">Tên Khách Hàng:</span> <?php echo htmlspecialchars($order['ten_khach_hang']); ?></div>
                        <div><span class="label">Địa Chỉ Giao Hàng:</span> <?php echo htmlspecialchars($order['dia_chi_giao_hang'] ?? $order['dia_chi']); ?></div>
                        <div><span class="label">Số Điện Thoại:</span> <?php echo htmlspecialchars($order['so_dien_thoai']); ?></div>
                        <div><span class="label">Ngày Đặt:</span> <?php echo htmlspecialchars($order['ngay_dat']); ?></div>
                        <div><span class="label">Tổng Tiền:</span> <?php echo number_format($order['tong_tien'], 2); ?> VND</div>
                        <div><span class="label">Trạng Thái:</span> <?php echo htmlspecialchars($order['trang_thai']); ?></div>
                        <div><span class="label">Thời Gian Bắt Đầu:</span> <?php echo htmlspecialchars($order['thoi_gian_bat_dau']); ?></div>
                        <div><span class="label">Thời Gian Kết Thúc:</span> <?php echo htmlspecialchars($order['thoi_gian_ket_thuc']); ?></div>
                        <div><span class="label">Người Chăm Sóc:</span> <?php echo htmlspecialchars($order['ten_cham_soc'] ?? 'Chưa gán'); ?></div>
                    </div>
                    <div class="invoice-total">Tổng: <?php echo number_format($order['tong_tien'], 2); ?> VND</div>
                    <div class="buttons">
                        <?php if ($order['trang_thai'] === 'Chờ xác nhận'): ?>
                            <form method="POST" style="margin: 0;">
                                <input type="hidden" name="id" value="<?php echo $order['id_don_hang']; ?>">
                                <button type="submit" class="btn-cancel" name="cancel" onclick="return confirm('Bạn có chắc muốn hủy đơn hàng?');">Hủy Đơn</button>
                            </form>
                        <?php endif; ?>
                        <?php if ($order['caregiver_id']): ?>
                            <a href="Chatkhachhang.php?id=<?php echo $order['caregiver_id']; ?>" class="button btn-chat">Chat Với Người Chăm Sóc</a>
                        <?php endif; ?>
                        <a href="Dichvu.php" class="button btn-home">Quay Về Trang Chủ</a>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div style="color: #d32f2f; text-align: center; margin-top: 20px;">Bạn cần đăng nhập để xem chi tiết đơn hàng.</div>
        <?php endif; ?>
    </div>
</body>
</html>