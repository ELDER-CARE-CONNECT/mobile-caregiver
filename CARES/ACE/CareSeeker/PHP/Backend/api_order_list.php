<?php
// File: api_order_list.php

// Kết nối DB đã có sẵn từ api_gateway
global $conn;

// Lấy ID khách hàng từ Session
$id_khach_hang = $_SESSION['id_khach_hang'] ?? 0;

if ($id_khach_hang == 0) {
    sendResponse(400, ['success' => false, 'message' => 'Không tìm thấy thông tin khách hàng']);
}

// Truy vấn danh sách đơn hàng
$sql = "
    SELECT 
        dh.id_don_hang, 
        dh.ngay_dat, 
        dh.trang_thai, 
        dh.tong_tien,
        ncs.ho_ten AS ten_cham_soc
    FROM don_hang dh
    LEFT JOIN nguoi_cham_soc ncs ON dh.id_nguoi_cham_soc = ncs.id_cham_soc
    WHERE dh.id_khach_hang = ?
    ORDER BY dh.ngay_dat DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_khach_hang);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

// Lấy tên khách hàng (để hiển thị "Xin chào...")
$sql_user = "SELECT ten_khach_hang FROM khach_hang WHERE id_khach_hang = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $id_khach_hang);
$stmt_user->execute();
$res_user = $stmt_user->get_result();
$user = $res_user->fetch_assoc();
$customer_name = $user['ten_khach_hang'] ?? 'Khách hàng';

// Trả về JSON
sendResponse(200, [
    'success' => true,
    'data' => $orders,
    'customer_name' => $customer_name
]);
?>