<?php
error_reporting(0); // tắt notice/warning
// backend/dashboard/tongquan.php
include_once("../config/connect.php"); // sửa đường dẫn đúng
$conn = connectdb();

if (!$conn) {
    echo json_encode(["error"=>"Không kết nối được DB"]);
    exit();
}

// Khởi tạo session an toàn
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json; charset=utf-8');

// Doanh thu theo tháng
$sql_doanhthu = "SELECT MONTH(ngay_dat) AS thang, SUM(tong_tien) AS doanh_thu 
                 FROM don_hang 
                 GROUP BY MONTH(ngay_dat) 
                 ORDER BY thang";
$result_doanhthu = $conn->query($sql_doanhthu);

$labels = [];
$data = [];
if ($result_doanhthu) {
    while ($row = $result_doanhthu->fetch_assoc()) {
        $labels[] = 'Tháng ' . $row['thang'];
        $data[] = (float)$row['doanh_thu'];
    }
}

// Tổng doanh thu
$total_revenue = (float)($conn->query("SELECT SUM(tong_tien) AS total_revenue FROM don_hang")->fetch_assoc()['total_revenue'] ?? 0);

// Tổng đơn hàng
$total_orders = (int)($conn->query("SELECT COUNT(*) AS total_orders FROM don_hang")->fetch_assoc()['total_orders'] ?? 0);

// Tổng khách hàng
$total_customers = (int)($conn->query("SELECT COUNT(*) AS total_customers FROM khach_hang")->fetch_assoc()['total_customers'] ?? 0);

// Tổng người chăm sóc
$total_caregivers = (int)($conn->query("SELECT COUNT(id_cham_soc) AS total_caregivers FROM nguoi_cham_soc")->fetch_assoc()['total_caregivers'] ?? 0);

// Trung bình đánh giá
$avg_rating = round((float)($conn->query("SELECT AVG(so_sao) AS avg_rating FROM danh_gia")->fetch_assoc()['avg_rating'] ?? 0), 1);

// Trả dữ liệu JSON
echo json_encode([
    "labels" => $labels,
    "data" => $data,
    "total_revenue" => $total_revenue,
    "total_orders" => $total_orders,
    "total_customers" => $total_customers,
    "total_caregivers" => $total_caregivers,
    "avg_rating" => $avg_rating
]);

$conn->close();
exit();
?>
