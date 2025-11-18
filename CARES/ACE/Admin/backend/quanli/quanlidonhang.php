<?php
header('Content-Type: application/json; charset=utf-8');
include '../config/connect.php';

// Kết nối CSDL
$conn = connectdb();
if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Không thể kết nối CSDL']);
    exit;
}

// Lấy param search
$search_id = isset($_GET['search_id']) ? trim($_GET['search_id']) : '';

// Câu lệnh SQL
$sql = "
    SELECT 
        dh.id_don_hang,
        dh.ngay_dat,
        dh.ten_khach_hang,
        dh.so_dien_thoai,
        ncs.ho_ten AS nguoi_cham_soc,
        dh.trang_thai,
        dg.so_sao AS danh_gia,
        dg.nhan_xet,
        dh.tong_tien
    FROM don_hang dh
    LEFT JOIN nguoi_cham_soc ncs ON dh.id_nguoi_cham_soc = ncs.id_cham_soc
    LEFT JOIN danh_gia dg ON dh.id_danh_gia = dg.id_danh_gia
    WHERE 1
";

// Nếu có tìm kiếm theo mã đơn hàng
if ($search_id !== '') {
    $safe_id = $conn->real_escape_string($search_id);
    $sql .= " AND dh.id_don_hang LIKE '%$safe_id%'";
}

// Thực thi query
$result = $conn->query($sql);
$orders = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $orders[] = [
            'id_don_hang'     => $row['id_don_hang'],
            'ngay_dat'        => $row['ngay_dat'],
            'ten_khach_hang'  => $row['ten_khach_hang'] ?? '',
            'so_dien_thoai'   => $row['so_dien_thoai'] ?? '',
            'nguoi_cham_soc'  => $row['nguoi_cham_soc'] ?? 'Chưa có',
            'trang_thai'      => $row['trang_thai'] ?? '',
            'danh_gia'        => $row['danh_gia'] ?? 'Chưa đánh giá',
            'nhan_xet'        => $row['nhan_xet'] ?? '—',
            'tong_tien'       => isset($row['tong_tien']) ? floatval($row['tong_tien']) : 0
        ];
    }

    echo json_encode([
        'status' => 'success',
        'orders' => $orders
    ], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi truy vấn: ' . $conn->error
    ]);
}

$conn->close();
