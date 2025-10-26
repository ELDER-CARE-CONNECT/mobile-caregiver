<?php
require_once '../../model/get_products.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $conn = connectdb();
    
    // Lấy các tham số lọc từ request
    $from_date = isset($_GET['from']) ? $_GET['from'] : '';
    $to_date = isset($_GET['to']) ? $_GET['to'] : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = 10; // Số đơn hàng mỗi trang
    $offset = ($page - 1) * $limit;
    
    // Xây dựng câu truy vấn
    $sql = "SELECT 
                dh.id_don_hang,
                dh.ngay_dat,
                dh.tong_tien,
                dh.dia_chi_giao_hang,
                dh.ten_khach_hang,
                dh.so_dien_thoai,
                dh.trang_thai,
                dh.thoi_gian_bat_dau,
                dh.thoi_gian_ket_thuc,
                kh.email,
                kh.dia_chi as dia_chi_khach_hang
            FROM don_hang dh
            LEFT JOIN khach_hang kh ON dh.id_khach_hang = kh.id_khach_hang
            WHERE dh.id_cham_soc = 0 AND dh.trang_thai = 'Chờ xác nhận'";
    
    $params = [];
    $types = '';
    
    // Thêm điều kiện lọc theo ngày
    if (!empty($from_date)) {
        $sql .= " AND dh.ngay_dat >= ?";
        $params[] = $from_date;
        $types .= 's';
    }
    
    if (!empty($to_date)) {
        $sql .= " AND dh.ngay_dat <= ?";
        $params[] = $to_date;
        $types .= 's';
    }
    
    // Đếm tổng số đơn hàng
    $count_sql = "SELECT COUNT(*) as total FROM don_hang dh WHERE dh.id_cham_soc = 0 AND dh.trang_thai = 'Chờ xác nhận'";
    if (!empty($from_date)) {
        $count_sql .= " AND dh.ngay_dat >= ?";
    }
    if (!empty($to_date)) {
        $count_sql .= " AND dh.ngay_dat <= ?";
    }
    
    $count_stmt = $conn->prepare($count_sql);
    if (!empty($params)) {
        $count_stmt->bind_param($types, ...$params);
    }
    $count_stmt->execute();
    $total_result = $count_stmt->get_result();
    $total_orders = $total_result->fetch_assoc()['total'];
    
    // Lấy dữ liệu đơn hàng với phân trang
    $sql .= " ORDER BY dh.ngay_dat DESC, dh.id_don_hang DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = [
            'id_don_hang' => $row['id_don_hang'],
            'ngay_dat' => $row['ngay_dat'],
            'tong_tien' => number_format($row['tong_tien'], 0, ',', '.') . ' VNĐ',
            'dia_chi_giao_hang' => $row['dia_chi_giao_hang'],
            'ten_khach_hang' => $row['ten_khach_hang'],
            'so_dien_thoai' => $row['so_dien_thoai'],
            'email' => $row['email'],
            'dia_chi_khach_hang' => $row['dia_chi_khach_hang'],
            'trang_thai' => $row['trang_thai'],
            'thoi_gian_bat_dau' => $row['thoi_gian_bat_dau'],
            'thoi_gian_ket_thuc' => $row['thoi_gian_ket_thuc']
        ];
    }
    
    // Tính toán thông tin phân trang
    $total_pages = ceil($total_orders / $limit);
    
    $response = [
        'success' => true,
        'data' => $orders,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $total_pages,
            'total_orders' => $total_orders,
            'per_page' => $limit
        ]
    ];
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ];
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
