<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) {
    session_name("CARES_SESSION");
    session_start();
} else {
    session_start();
}

// ✅ Đường dẫn chính xác tới file get_products.php
require_once('../../model/get_products.php'); // hoặc ../../../ nếu model nằm cao hơn
$conn = connectdb();

if (!isset($_SESSION['caregiver_id'])) {
    echo json_encode(["success" => false, "error" => "Chưa đăng nhập"]);
    exit;
}

$caregiverId = (int)$_SESSION['caregiver_id'];

// ✅ Lấy cả đơn "Đã giao" và "Chờ xác nhận"
$sql = "SELECT * FROM don_hang 
        WHERE id_cham_soc = $caregiverId 
        AND trang_thai IN ('Đã giao', 'Chờ xác nhận')
        ORDER BY ngay_dat DESC";

$result = $conn->query($sql);
$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode(["success" => true, "data" => $data], JSON_UNESCAPED_UNICODE);
exit;

$caregiverId = (int)$_SESSION['caregiver_id'];

// ✅ Lấy toàn bộ đơn của người chăm sóc đăng nhập (Đã giao + Chờ xác nhận)
$sql = "
    SELECT 
        id_don_hang,
        ten_khach_hang,
        so_dien_thoai,
        dia_chi_giao_hang,
        ngay_dat,
        thoi_gian_bat_dau,
        thoi_gian_ket_thuc,
        tong_tien,
        trang_thai,
        phuong_thuc_thanh_toan,
        dich_vu,
        nguoi_cham_soc_ten
    FROM don_hang
    WHERE id_cham_soc = $caregiverId
      AND trang_thai IN ('Đã giao', 'Chờ xác nhận')
    ORDER BY ngay_dat DESC
";

$data = [];
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($r = $result->fetch_assoc()) {
        $data[] = $r;
    }
}

echo json_encode(["success" => true, "data" => $data], JSON_UNESCAPED_UNICODE);
?>
