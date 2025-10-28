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

// Phân trang
$items_per_page = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $items_per_page;

$stmt = $pdo->prepare("
    SELECT dh.*, kh.ten_khach_hang, kh.so_dien_thoai, kh.dia_chi,
           ncs.ho_ten AS ten_cham_soc, ncs.id_cham_soc AS caregiver_id
    FROM don_hang dh
    LEFT JOIN khach_hang kh ON dh.id_khach_hang = kh.id_khach_hang
    LEFT JOIN nguoi_cham_soc ncs ON dh.id_cham_soc = ncs.id_cham_soc
    WHERE dh.trang_thai = 'Đã hoàn thành'
    ORDER BY dh.ngay_dat DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $items_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Đếm tổng số đơn hàng đã hoàn thành để tính phân trang
$total_stmt = $pdo->query("SELECT COUNT(*) FROM don_hang WHERE trang_thai = 'Đã hoàn thành'");
$total_items = $total_stmt->fetchColumn();
$total_pages = ceil($total_items / $items_per_page);

// Xử lý đặt lại đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rebook']) && isset($_POST['id'])) {
    $id_to_rebook = intval($_POST['id']);
    $stmt_select = $pdo->prepare("SELECT * FROM don_hang WHERE id_don_hang = :id");
    $stmt_select->execute(['id' => $id_to_rebook]);
    $order = $stmt_select->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $stmt_insert = $pdo->prepare("
            INSERT INTO don_hang (id_khach_hang, id_cham_soc, id_danh_gia, ngay_dat, tong_tien, dia_chi_giao_hang, ten_khach_hang, so_dien_thoai, trang_thai, thoi_gian_bat_dau, thoi_gian_ket_thuc)
            VALUES (:kh, :cs, 0, CURDATE(), :tien, :dia_chi, :ten, :sdt, 'Chờ xác nhận', :bat_dau, :ket_thuc)
        ");
        $stmt_insert->execute([
            'kh' => $order['id_khach_hang'],
            'cs' => $order['id_cham_soc'],
            'tien' => $order['tong_tien'],
            'dia_chi' => $order['dia_chi_giao_hang'],
            'ten' => $order['ten_khach_hang'],
            'sdt' => $order['so_dien_thoai'],
            'bat_dau' => $order['thoi_gian_bat_dau'],
            'ket_thuc' => $order['thoi_gian_ket_thuc']
        ]);
        echo "<script>alert('Đơn hàng đã được đặt lại!'); window.location.href='Chitietlichsudonhang.php?page=$page';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Lịch Sử Đơn Hàng</title>
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
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1000px;
        }
        h1 {
            color: #1a73e8;
            text-align: center;
            margin-bottom: 20px;
        }
        .table-wrapper {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #1a73e8;
            color: white;
            font-weight: bold;
        }
        td {
            color: #333;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .btn-container {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 10px;
        }
        .action-buttons {
            display: flex;
            gap: 5px;
        }
        button, a.button {
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            color: white;
            font-size: 14px;
        }
        .btn-rebook {
            background-color: #ff9800;
        }
        .btn-rebook:hover {
            background-color: #f57c00;
        }
        .btn-rate {
            background-color: #9c27b0;
        }
        .btn-rate:hover {
            background-color: #7b1fa2;
        }
        .btn-home {
            background-color: #1a73e8;
        }
        .btn-home:hover {
            background-color: #1557a0;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            padding: 8px 12px;
            margin: 0 5px;
            background-color: #ddd;
            color: #333;
            text-decoration: none;
            border-radius: 5px;
        }
        .pagination a.active {
            background-color: #1a73e8;
            color: white;
        }
        @media (max-width: 600px) {
            .container { padding: 10px; }
            th, td { padding: 8px; font-size: 14px; }
            .btn-container, .action-buttons { flex-direction: column; }
            button, a.button { width: 100%; margin-bottom: 5px; }
            .pagination a { padding: 6px 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Chi Tiết Lịch Sử Đơn Hàng</h1>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID Đơn Hàng</th>
                        <th>Tên Khách Hàng</th>
                        <th>Địa Chỉ</th>
                        <th>Số Điện Thoại</th>
                        <th>Ngày</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Thời Gian Bắt Đầu</th>
                        <th>Thời Gian Kết Thúc</th>
                        <th>Người Chăm Sóc</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['id_don_hang']); ?></td>
                            <td><?php echo htmlspecialchars($order['ten_khach_hang']); ?></td>
                            <td><?php echo htmlspecialchars($order['dia_chi_giao_hang'] ?? $order['dia_chi']); ?></td>
                            <td><?php echo htmlspecialchars($order['so_dien_thoai']); ?></td>
                            <td><?php echo htmlspecialchars($order['ngay_dat']); ?></td>
                            <td><?php echo number_format($order['tong_tien'], 2); ?> VND</td>
                            <td><?php echo htmlspecialchars($order['trang_thai']); ?></td>
                            <td><?php echo htmlspecialchars($order['thoi_gian_bat_dau']); ?></td>
                            <td><?php echo htmlspecialchars($order['thoi_gian_ket_thuc']); ?></td>
                            <td><?php echo htmlspecialchars($order['ten_cham_soc'] ?? 'Chưa gán'); ?></td>
                            <td class="action-buttons">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="id" value="<?php echo $order['id_don_hang']; ?>">
                                    <button type="submit" class="btn-rebook" name="rebook">Đặt Lại</button>
                                </form>
                                <?php if ($order['caregiver_id']): ?>
                                    <a href="Danhgia.php?id=<?php echo $order['caregiver_id']; ?>" class="button btn-rate">Đánh Giá</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="btn-container">
            <a href="Dichvu.php" class="button btn-home">Quay Về Trang Chủ</a>
        </div>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i; ?>" class="<?php echo $page == $i ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>