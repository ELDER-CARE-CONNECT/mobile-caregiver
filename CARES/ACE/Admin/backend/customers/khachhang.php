<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

include '../config/connect.php';
$conn = connectdb();

if (!$conn) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Không thể kết nối CSDL'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $searchTerm = trim($_GET['search'] ?? '');

    // ===== LẤY DANH SÁCH KHÁCH HÀNG =====
    if ($searchTerm !== '') {
        $stmt = $conn->prepare("SELECT * FROM khach_hang WHERE ten_khach_hang LIKE ?");
        $like = "%$searchTerm%";
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query("SELECT * FROM khach_hang");
    }

    $customers = [];

    // URL gốc để truy cập ảnh từ trình duyệt
    $baseUrl = "http://localhost/CareSeeker/PHP/frontend/uploads/avatars/";

    while ($row = $result->fetch_assoc()) {
        $id = (int)$row['id_khach_hang'];

        // ===== TỔNG HỢP ĐƠN =====
        $tong_don = 0;
        $tong_tien = 0;

        $stmtSum = $conn->prepare("
            SELECT COUNT(*) AS tong_don, COALESCE(SUM(tong_tien),0) AS tong_tien 
            FROM don_hang 
            WHERE id_khach_hang = ?
        ");
        $stmtSum->bind_param('i', $id);
        $stmtSum->execute();
        $summary = $stmtSum->get_result()->fetch_assoc();

        if ($summary) {
            $tong_don = (int)$summary['tong_don'];
            $tong_tien = (float)$summary['tong_tien'];
        }

        // ===== CHI TIẾT ĐƠN =====
        $orders = [];
        $stmtOrder = $conn->prepare("SELECT * FROM don_hang WHERE id_khach_hang = ?");
        $stmtOrder->bind_param('i', $id);
        $stmtOrder->execute();
        $ordersRes = $stmtOrder->get_result();

        while ($o = $ordersRes->fetch_assoc()) {
            $orders[] = [
                'id_don_hang'          => (int)$o['id_don_hang'],
                'ngay_dat'             => $o['ngay_dat'] ?? '—',
                'ten_khach_hang'       => $o['ten_khach_hang'] ?? '—',
                'ten_nguoi_cham_soc'   => $o['ten_nguoi_cham_soc'] ?? '—',
                'thoi_gian_lam_viec'   => $o['thoi_gian_lam_viec'] ?? '—',
                'tong_tien'            => (float)($o['tong_tien'] ?? 0),
                'trang_thai'           => $o['trang_thai'] ?? '—',
                'danh_gia'             => $o['danh_gia'] ?? '—'
            ];
        }

        // ===== XỬ LÝ ẢNH =====
        $imagePaths = [];
        $hinhAnh = $row['hinh_anh'] ?? '';

        if (!empty($hinhAnh)) {
            $hinhArr = explode(',', $hinhAnh);

            foreach ($hinhArr as $img) {
                $img = trim($img);
                if (empty($img)) continue;

                // Nếu là URL đầy đủ
                if (filter_var($img, FILTER_VALIDATE_URL)) {
                    $imagePaths[] = $img;
                    continue;
                }

                // Nếu chỉ lưu tên file (vd: abc.jpg)
                $fileName = basename($img);
                $fullPath = $_SERVER['DOCUMENT_ROOT'] . "/CareSeeker/PHP/frontend/uploads/avatars/" . $fileName;

                if (file_exists($fullPath)) {
                    $imagePaths[] = $baseUrl . $fileName;
                }
            }
        }

        // ===== ĐỊA CHỈ =====
        $diaChiParts = [];
        if (!empty($row['ten_duong'])) $diaChiParts[] = $row['ten_duong'];
        if (!empty($row['phuong_xa'])) $diaChiParts[] = $row['phuong_xa'];
        if (!empty($row['tinh_thanh'])) $diaChiParts[] = $row['tinh_thanh'];

        $diaChi = !empty($diaChiParts) ? implode(', ', $diaChiParts) : '—';

        // ===== PUSH VÀO JSON =====
        $customers[] = [
            'id_khach_hang' => $id,
            'ten_khach_hang' => $row['ten_khach_hang'] ?? '—',
            'dia_chi' => $diaChi,
            'so_dien_thoai' => $row['so_dien_thoai'] ?? '—',
            'tuoi' => $row['tuoi'] ?? '—',
            'gioi_tinh' => $row['gioi_tinh'] ?? '—',
            'chieu_cao' => $row['chieu_cao'] ?? '—',
            'can_nang' => $row['can_nang'] ?? '—',
            'hinh_anh' => $imagePaths,
            'tong_don' => $tong_don,
            'tong_tien' => $tong_tien,
            'orders' => $orders
        ];
    }

    echo json_encode([
        'status' => 'success',
        'customers' => $customers
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi server: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>
