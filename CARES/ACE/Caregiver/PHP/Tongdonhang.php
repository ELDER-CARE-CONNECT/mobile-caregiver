<?php
session_name("CARES_SESSION");
if (session_status() === PHP_SESSION_NONE) session_start();

include 'connect.php';

$logged_in = isset($_SESSION['caregiver_id']);
$id_cham_soc = $logged_in ? (int)$_SESSION['caregiver_id'] : 0;

// Biến nhận từ form tìm kiếm
$keyword = $_GET['keyword'] ?? '';
$from_date = $_GET['from_date'] ?? '';
$to_date = $_GET['to_date'] ?? '';

// Nếu đăng nhập → lọc dữ liệu
$result = null;
$data = [];

if ($logged_in) {
    $sql = "
        SELECT id_don_hang, ten_khach_hang, dia_chi_giao_hang AS dia_chi,
               ngay_dat, trang_thai, tong_tien
        FROM don_hang
        WHERE id_cham_soc = ?
    ";

    // Lọc theo từ khóa
    if (!empty($keyword)) {
        $sql .= " AND (id_don_hang LIKE ? OR ten_khach_hang LIKE ?)";
    }

    // Lọc theo ngày
    if (!empty($from_date)) {
        $sql .= " AND ngay_dat >= ?";
    }
    if (!empty($to_date)) {
        $sql .= " AND ngay_dat <= ?";
    }

    $sql .= " ORDER BY ngay_dat DESC";

    // Chuẩn bị câu truy vấn
    $stmt = $conn->prepare($sql);

    // Gắn tham số linh hoạt
    $types = "i";
    $params = [$id_cham_soc];

    if (!empty($keyword)) {
        $kw = "%$keyword%";
        $types .= "ss";
        array_push($params, $kw, $kw);
    }
    if (!empty($from_date)) {
        $types .= "s";
        array_push($params, $from_date);
    }
    if (!empty($to_date)) {
        $types .= "s";
        array_push($params, $to_date);
    }

    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Tổng Đơn Hàng</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body {
    font-family: "Poppins", sans-serif;
    background: #f9fafb;
    margin: 0;
}
.container {
    max-width: 1200px;
    margin: 40px auto;
    background: #fff;
    padding: 40px 50px;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.07);
}

/* Header */
.header h1 {
    font-size: 28px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 10px;
    color: #111827;
}
.header h1 i {
    color: #4f46e5;
    font-size: 28px;
}
.header p {
    font-size: 15px;
    color: #6b7280;
}

/* Bộ lọc */
.filter-box {
    background: #f8fafc;
    border-radius: 15px;
    padding: 25px 25px 10px;
    margin-bottom: 25px;
    box-shadow: inset 0 0 5px rgba(0,0,0,0.03);
}
.filter-box input {
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 14px;
    width: 100%;
}
.filter-box button {
    background-color: #4f46e5;
    color: white;
    border: none;
    border-radius: 10px;
    padding: 10px 20px;
    font-weight: 600;
}
.filter-box button:hover {
    background-color: #4338ca;
}

/* Thống kê */
.summary {
    display: flex;
    justify-content: space-around;
    background: #fff;
    margin-bottom: 25px;
}
.summary-item {
    flex: 1;
    text-align: center;
    padding: 25px 15px;
    border-radius: 16px;
    background: #f9fafb;
    margin: 5px;
    box-shadow: 0 0 8px rgba(0,0,0,0.04);
}
.summary-item i {
    font-size: 32px;
    margin-bottom: 8px;
    color: #4f46e5;
}
.summary-item h2 {
    margin: 0;
    font-size: 22px;
    font-weight: 700;
}
.summary-item p {
    margin: 5px 0 0;
    color: #6b7280;
}

/* Bảng */
.table {
    border-radius: 12px;
    overflow: hidden;
}
.table thead {
    background: #f3f4f6;
    font-weight: 600;
    font-size: 15px;
}
.table tbody tr:hover {
    background: #f9fafb;
}

/* Trạng thái */
.status {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 25px;
    font-weight: 600;
    font-size: 13px;
    text-transform: uppercase;
}
.status.cho {
    background: #fff8db;
    color: #a16207;
}
.status.hoan {
    background: #dcfce7;
    color: #15803d;
}
.status.dang {
    background: #dbeafe;
    color: #1e40af;
}
.status.huy {
    background: #fee2e2;
    color: #b91c1c;
}

/* Khi chưa đăng nhập */
.empty {
    text-align: center;
    padding: 80px 20px;
    color: #6b7280;
}
.empty i {
    font-size: 60px;
    color: #d1d5db;
    margin-bottom: 15px;
}
.empty h3 {
    font-size: 22px;
    color: #374151;
}
</style>
</head>
<body>

<div class="container">
    <div class="header mb-4">
        <h1><i class="fa-solid fa-box"></i> Tổng Đơn Hàng</h1>
        <p>Danh sách các đơn hàng bạn đã nhận.</p>
    </div>

    <?php if (!$logged_in): ?>
        <div class="empty">
            <i class="fa-solid fa-lock"></i>
            <h3>Bạn chưa đăng nhập</h3>
            <p>Vui lòng đăng nhập để xem danh sách đơn hàng của bạn.</p>
        </div>
    <?php else: ?>

        <!-- Bộ lọc -->
        <form class="filter-box row g-3 align-items-center" method="GET">
            <div class="col-md-4">
                <input type="text" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="Tìm kiếm theo tên hoặc ID đơn hàng...">
            </div>
            <div class="col-md-3">
                <input type="date" name="from_date" value="<?php echo $from_date; ?>">
            </div>
            <div class="col-md-3">
                <input type="date" name="to_date" value="<?php echo $to_date; ?>">
            </div>
            <div class="col-md-2 text-end">
                <button type="submit"><i class="fa-solid fa-search"></i> Tìm kiếm</button>
            </div>
        </form>

        <!-- Thống kê -->
        <div class="summary">
            <div class="summary-item">
                <i class="fa-solid fa-cart-shopping"></i>
                <h2><?php echo count($data); ?></h2>
                <p>Tổng đơn hàng</p>
            </div>
            <div class="summary-item">
                <i class="fa-solid fa-money-bill-wave"></i>
                <h2>
                    <?php 
                    $tong = 0;
                    foreach ($data as $row) $tong += $row['tong_tien'];
                    echo number_format($tong, 0, ',', '.') . ' ₫';
                    ?>
                </h2>
                <p>Tổng giá trị</p>
            </div>
        </div>

        <!-- Danh sách -->
        <?php if (count($data) > 0): ?>
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID đơn hàng</th>
                    <th>Tên khách hàng</th>
                    <th>Ngày đặt</th>
                    <th>Địa chỉ</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td>#<?php echo $row['id_don_hang']; ?></td>
                        <td><?php echo htmlspecialchars($row['ten_khach_hang']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['ngay_dat'])); ?></td>
                        <td><?php echo htmlspecialchars($row['dia_chi']); ?></td>
                        <td>
                            <?php
                                $trangthai = mb_strtolower($row['trang_thai']);
                                $class = '';
                                if (strpos($trangthai, 'chờ') !== false) $class = 'cho';
                                elseif (strpos($trangthai, 'đang') !== false) $class = 'dang';
                                elseif (strpos($trangthai, 'giao') !== false || strpos($trangthai, 'hoàn') !== false) $class = 'hoan';
                                elseif (strpos($trangthai, 'hủy') !== false) $class = 'huy';
                            ?>
                            <span class="status <?php echo $class; ?>"><?php echo strtoupper($row['trang_thai']); ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty">
                <i class="fa-regular fa-box-open"></i>
                <h3>Không có đơn hàng nào phù hợp</h3>
                <p>Vui lòng thử lại với từ khóa hoặc ngày khác.</p>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

</body>
</html>
