<?php
session_name("CARES_SESSION");
session_start();

header('Content-Type: application/json; charset=utf-8');
require_once('../../model/get_products.php');

$conn = connectdb();

// ✅ Debug session (bạn có thể bỏ sau khi test xong)
if (!isset($_SESSION['caregiver_id'])) {
    echo json_encode([
        "success" => false,
        "error" => "Chưa đăng nhập hoặc mất session",
        "session_debug" => $_SESSION
    ]);
    exit;
}

$caregiverId = (int)$_SESSION['caregiver_id'];

// ✅ Nhận filter từ client
$search    = isset($_GET['search']) ? trim($_GET['search']) : '';
$from_date = isset($_GET['from_date']) ? trim($_GET['from_date']) : '';
$to_date   = isset($_GET['to_date']) ? trim($_GET['to_date']) : '';
$page      = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit     = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset    = ($page - 1) * $limit;

// ✅ Chỉ lấy đơn “đã hoàn thành” hoặc “đã hủy” của người chăm sóc đăng nhập
$where = [];
$where[] = "(LOWER(trang_thai) IN ('đã hoàn thành', 'đã hủy'))";
$where[] = "(id_cham_soc = $caregiverId)";

// 🔍 Bộ lọc tìm kiếm
if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $where[] = "(id_don_hang LIKE '%$s%' OR ten_khach_hang LIKE '%$s%' OR so_dien_thoai LIKE '%$s%')";
}

// 📅 Bộ lọc theo ngày
if ($from_date !== '') {
    $fd = $conn->real_escape_string($from_date);
    $where[] = "DATE(ngay_dat) >= '$fd'";
}
if ($to_date !== '') {
    $td = $conn->real_escape_string($to_date);
    $where[] = "DATE(ngay_dat) <= '$td'";
}

// ✅ Gộp điều kiện WHERE
$where_sql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

// ✅ Truy vấn dữ liệu chính
$sql = "
    SELECT 
        id_don_hang, ten_khach_hang, so_dien_thoai, dia_chi_giao_hang,
        ngay_dat, tong_tien, trang_thai, thoi_gian_bat_dau, thoi_gian_ket_thuc
    FROM don_hang
    $where_sql
    ORDER BY ngay_dat DESC
    LIMIT $limit OFFSET $offset
";

$data = [];
if ($rs = $conn->query($sql)) {
    while ($r = $rs->fetch_assoc()) {
        $data[] = $r;
    }
}

// ✅ Đếm tổng đơn và tổng doanh thu
$countSql = "
    SELECT COUNT(*) AS total_orders, COALESCE(SUM(tong_tien), 0) AS total_amount
    FROM don_hang $where_sql
";
$countRes = $conn->query($countSql)->fetch_assoc();

// ✅ Trả kết quả JSON
echo json_encode([
    "success" => true,
    "data" => $data,
    "summary" => [
        "total_orders" => (int)$countRes['total_orders'],
        "total_amount" => (float)$countRes['total_amount']
    ],
    "pagination" => [
        "current_page" => $page,
        "total_pages" => max(1, ceil($countRes['total_orders'] / $limit))
    ]
], JSON_UNESCAPED_UNICODE);
?>
