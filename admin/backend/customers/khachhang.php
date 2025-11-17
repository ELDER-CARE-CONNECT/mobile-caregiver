<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Thêm nếu CORS lỗi (e.g., frontend và backend khác domain)

include '../config/connect.php'; // Sửa: Từ backend/customers/ lên root/config/ (thêm ../ nếu cần)
$conn = connectdb();

if (!$conn) {
    echo json_encode(['status'=>'error','message'=>'Không thể kết nối CSDL'], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $searchTerm = trim($_GET['search'] ?? '');

    if($searchTerm !== '') {
        $stmt = $conn->prepare("SELECT * FROM khach_hang WHERE ten_khach_hang LIKE ?");
        $like = "%$searchTerm%";
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT * FROM khach_hang");
    }

    $customers = [];
    while ($row = $result->fetch_assoc()) {
        $id = (int)$row['id_khach_hang'];

        // Thống kê tổng đơn & tổng tiền
        $stmtSum = $conn->prepare("SELECT COUNT(*) AS tong_don, SUM(tong_tien) AS tong_tien FROM don_hang WHERE id_khach_hang = ?");
        $stmtSum->bind_param('i', $id);
        $stmtSum->execute();
        $summary = $stmtSum->get_result()->fetch_assoc() ?? ['tong_don'=>0,'tong_tien'=>0];

        // Lấy đơn hàng chi tiết
        $orders = [];
        $stmtOrder = $conn->prepare("SELECT * FROM don_hang WHERE id_khach_hang = ?");
        $stmtOrder->bind_param('i', $id);
        $stmtOrder->execute();
        $ordersRes = $stmtOrder->get_result();
        while($o = $ordersRes->fetch_assoc()) {
            $orders[] = [
                'id_don_hang'=>$o['id_don_hang'],
                'ngay_dat'=>$o['ngay_dat'],
                'ten_khach_hang'=>$o['ten_khach_hang'],
                'ten_nguoi_cham_soc'=>$o['ten_nguoi_cham_soc'] ?? '—',
                'thoi_gian_lam_viec'=>$o['thoi_gian_lam_viec'] ?? '—',
                'tong_tien'=> (float)($o['tong_tien'] ?? 0),
                'trang_thai'=>$o['trang_thai'] ?? '—',
                'danh_gia'=>$o['danh_gia'] ?? '—'
            ];
        }

        $customers[] = [
            'id_khach_hang'=>$id,
            'ten_khach_hang'=>$row['ten_khach_hang'],
            'dia_chi'=> ($row['ten_duong'] ?? '') . ($row['phuong_xa'] ? ', '.$row['phuong_xa'] : '') . ($row['tinh_thanh'] ? ', '.$row['tinh_thanh'] : '') ?: '—',
            'so_dien_thoai'=>$row['so_dien_thoai'] ?? '—',
            'tuoi'=>$row['tuoi'] ?? '—',
            'gioi_tinh'=>$row['gioi_tinh'] ?? '—',
            'chieu_cao'=>$row['chieu_cao'] ?? '—',
            'can_nang'=>$row['can_nang'] ?? '—',
            'hinh_anh'=>$row['hinh_anh'] ?? '',
            'tong_don'=>(int)($summary['tong_don'] ?? 0),
            'tong_tien'=>(float)($summary['tong_tien'] ?? 0),
            'orders'=>$orders
        ];
    }

    echo json_encode(['status'=>'success','customers'=>$customers], JSON_UNESCAPED_UNICODE);

} catch(Exception $e) {
    echo json_encode(['status'=>'error','message'=>'Lỗi server: '.$e->getMessage()], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>