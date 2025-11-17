<?php
session_start();
header('Content-Type: application/json');

// Chỉ cho người chăm sóc truy cập
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'nguoi_cham_soc') {
    http_response_code(403);
    echo json_encode(["error" => "Bạn chưa đăng nhập hoặc không có quyền truy cập."]);
    exit;
}

require_once('../../../Admin/connect.php'); // Kết nối DB
$caregiverId = (int)$_SESSION['id_cham_soc'];

// Nhận filter từ GET
$search    = isset($_GET['search']) ? trim($_GET['search']) : '';
$from_date = isset($_GET['from_date']) ? trim($_GET['from_date']) : '';
$to_date   = isset($_GET['to_date']) ? trim($_GET['to_date']) : '';
$page      = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit     = 10;
$offset    = ($page - 1) * $limit;

// Điều kiện WHERE
$where = ["id_nguoi_cham_soc = $caregiverId"];
if ($search !== '') {
    $s = $conn->real_escape_string($search);
    $where[] = "(id_don_hang LIKE '%$s%' OR ten_khach_hang LIKE '%$s%' OR so_dien_thoai LIKE '%$s%')";
}
if ($from_date !== '') {
    $fd = $conn->real_escape_string($from_date);
    $where[] = "DATE(ngay_dat) >= '$fd'";
}
if ($to_date !== '') {
    $td = $conn->real_escape_string($to_date);
    $where[] = "DATE(ngay_dat) <= '$td'";
}

$where_sql = count($where) ? 'WHERE '.implode(' AND ', $where) : '';

// Lấy đơn hàng
$sql = "
    SELECT * FROM don_hang
    $where_sql
    ORDER BY id_don_hang DESC
    LIMIT $limit OFFSET $offset
";
$result = $conn->query($sql);
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// Tổng đơn và tổng tiền
$countSql = "SELECT COUNT(*) AS total_orders, COALESCE(SUM(tong_tien),0) AS total_amount FROM don_hang $where_sql";
$countRes = $conn->query($countSql)->fetch_assoc();
$totalOrders = (int)$countRes['total_orders'];
$totalAmount = (float)$countRes['total_amount'];
$totalPages = max(1, ceil($totalOrders / $limit));

// Trả JSON
echo json_encode([
    "orders" => $orders,
    "summary" => [
        "totalOrders" => $totalOrders,
        "totalAmount" => $totalAmount,
        "totalPages" => $totalPages,
        "currentPage" => $page
    ]
]);
