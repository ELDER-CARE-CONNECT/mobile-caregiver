<?php

session_start(); // Khởi tạo session

// =======================================
// BẮT BUỘC ĐĂNG NHẬP TRƯỚC KHI TRUY CẬP
// =======================================
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'khach_hang' || !isset($_SESSION['so_dien_thoai'])) {
    header("Location: DangNhap.php"); 
    exit();
}
// =======================================

// =======================================
// CẤU HÌNH KẾT NỐI DB
// =======================================
$host = '127.0.0.1';
$dbname = 'sanpham';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối DB thất bại: " . $e->getMessage());
}

// Biến để lưu thông tin đơn hàng và dịch vụ
$order = [];
$services = [];
$id_don_hang = 0; // Tự tìm đơn hàng mới nhất
$id_khach_hang = 0;
$so_dien_thoai = $_SESSION['so_dien_thoai'];

// =======================================
// LOGIC CHÍNH: LẤY ĐƠN HÀNG HOÀN THÀNH MỚI NHẤT
// =======================================
$stmt_kh = $pdo->prepare("SELECT id_khach_hang, ten_khach_hang, so_dien_thoai, dia_chi FROM khach_hang WHERE so_dien_thoai = ?");
$stmt_kh->execute([$so_dien_thoai]);
$user = $stmt_kh->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $id_khach_hang = $user['id_khach_hang'];

    // 1. Tìm ID đơn hàng ĐÃ HOÀN THÀNH MỚI NHẤT
    $stmt_latest = $pdo->prepare("
        SELECT id_don_hang
        FROM don_hang 
        WHERE id_khach_hang = ? 
        AND LOWER(TRIM(trang_thai)) IN ('hoàn thành', 'đã hoàn thành')
        ORDER BY ngay_dat DESC 
        LIMIT 1
    ");
    $stmt_latest->execute([$id_khach_hang]);
    $latest_order_id = $stmt_latest->fetchColumn();
    
    if ($latest_order_id) {
        $id_don_hang = intval($latest_order_id);

        // 2. Lấy chi tiết đơn hàng theo ID
        $stmt = $pdo->prepare("
            SELECT 
                dh.*, 
                kh.ten_khach_hang, kh.so_dien_thoai, kh.dia_chi AS dia_chi_kh,
                ncs.ho_ten AS ten_cham_soc, ncs.hinh_anh AS hinh_anh_cham_soc, ncs.id_cham_soc AS caregiver_id
            FROM don_hang dh
            LEFT JOIN khach_hang kh ON dh.id_khach_hang = kh.id_khach_hang
            LEFT JOIN nguoi_cham_soc ncs ON dh.id_cham_soc = ncs.id_cham_soc
            WHERE dh.id_don_hang = :id_dh AND dh.id_khach_hang = :id_kh
        ");
        $stmt->bindValue(':id_dh', $id_don_hang, PDO::PARAM_INT);
        $stmt->bindValue(':id_kh', $id_khach_hang, PDO::PARAM_INT);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($order) {
            // 3. Lấy chi tiết dịch vụ đã đặt
            $stmt_services = $pdo->prepare("SELECT * FROM dich_vu_don_hang WHERE id_don_hang = :id_dh");
            $stmt_services->execute(['id_dh' => $id_don_hang]);
            $services = $stmt_services->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
// =======================================

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Lịch Sử Đơn Hàng Hoàn Thành</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ======================================= */
        /* CÁC STYLE CHUNG & LAYOUT */
        /* ======================================= */
        /* Đổi màu chính từ xanh lá sang hồng/đỏ */
        :root {
            --main-color: #FF6B81; /* Màu chính (Hồng/Đỏ) */
            --secondary-color: #f7fff9; /* Nền nhẹ */
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #f8f8fa; color: #333; line-height: 1.6; display: flex; justify-content: center; min-height: 100vh; padding: 30px 15px; } 
        .container { background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); width: 100%; max-width: 700px; }
        h1 { color: var(--main-color); font-size: 28px; margin-bottom: 5px; font-weight: 800; text-align: center; }
        .order-id { text-align: center; font-size: 14px; color: #888; margin-bottom: 25px; font-weight: 500; }

        /* SECTIONS */
        .section-box { background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #e0e0e0; }
        .section-title { font-size: 18px; font-weight: 700; color: var(--main-color); border-bottom: 2px solid #F0C4CC; /* Màu sáng hơn của main-color */ padding-bottom: 10px; margin-bottom: 15px; }
        .detail-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px dashed #eee; font-size: 15px; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: 500; color: #666; display: flex; align-items: center; }
        .detail-value { font-weight: 600; color: #333; }
        .icon { margin-right: 8px; color: var(--main-color); }

        /* Caregiver Info */
        .caregiver-info { display: flex; align-items: center; gap: 15px; background: #fff0f2; /* Nền nhẹ hồng */ padding: 15px; border-radius: 10px; }
        .caregiver-info img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid var(--main-color); }
        .caregiver-name { font-weight: 700; color: var(--main-color); }
        .caregiver-id { font-size: 13px; color: #888; }
        
        /* Total */
        .total-section { border-top: 2px solid var(--main-color); padding-top: 15px; margin-top: 10px; font-size: 20px; font-weight: 800; display: flex; justify-content: space-between; }

        /* Status Tag */
        .status-tag { padding: 5px 10px; border-radius: 20px; font-weight: 700; font-size: 13px; text-transform: uppercase; background: #ffebee; color: var(--main-color); } /* Màu hồng nhẹ */
        
        /* ======================================= */
        /* NÚT CHỨC NĂNG (Đã điều chỉnh kích thước đồng nhất) */
        /* ======================================= */
        .buttons { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 10px; 
            justify-content: center; 
            margin-top: 30px; 
        }
        .button {
            padding: 12px 20px; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            transition: background-color 0.3s;
            text-decoration: none; 
            color: white; 
            font-size: 15px; 
            font-weight: 600; 
            text-align: center;
            display: inline-flex; 
            align-items: center; 
            justify-content: center; /* Căn giữa nội dung */
            gap: 8px;
            min-width: 130px; /* Thêm min-width để đồng nhất */
            height: 44px; /* Thêm height để đồng nhất */
        }
        
        .btn-back { background-color: #9e9e9e; color: white; }
        .btn-back:hover { background-color: #757575; }
        
        .btn-home { background-color: var(--main-color); color: white; } 
        .btn-home:hover { background-color: #E55B70; }

        .btn-rate { background-color: #FFC300; color: #333; } /* Giữ màu vàng cho đánh giá */
        .btn-rate:hover { background-color: #e6b100; }
        
        .btn-reorder { background-color: #00bcd4; color: white; } /* Giữ màu xanh dương cho đặt lại */
        .btn-reorder:hover { background-color: #0097a7; }

        .btn-rated { background-color: #cccccc; color: #666; cursor: default; }

    </style>
</head>
<body>
    <div class="container">
        
        <?php if (empty($order)): ?>
            <h1 style="color: #f44336;"><i class="fas fa-exclamation-triangle"></i> Lịch Sử Hoàn Thành Trống</h1>
            <p class="order-id" style="color:#d32f2f; font-size:16px;">Bạn chưa có đơn hàng nào đã hoàn thành trong hệ thống.</p>
            <div class="buttons">
                <a href="index.php" class="button btn-home"><i class="fas fa-home"></i> Trang Chủ</a>
                <a href="javascript:history.back()" class="button btn-back"><i class="fas fa-arrow-left"></i> Quay Lại</a>
            </div>
        <?php else: 
            $status_display = "Đã Hoàn Thành";
            $delivery_address = htmlspecialchars($order['dia_chi_giao_hang'] ?? $user['dia_chi'] ?? 'Không rõ');
            $is_rated = !empty($order['id_danh_gia']) && intval($order['id_danh_gia']) > 0;
            $order_id_current = $order['id_don_hang'];
            $caregiver_id_current = $order['caregiver_id']; // Lấy ID người chăm sóc
        ?>
            <h1><i class="fas fa-file-invoice"></i> Chi Tiết Lịch Sử Đơn Hàng</h1>
            <p class="order-id">Mã đơn hàng: <strong>#<?php echo htmlspecialchars($order_id_current); ?></strong></p>

            <div class="section-box">
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-info-circle"></i> Trạng Thái Đơn Hàng:</span>
                    <span class="status-tag"><?php echo $status_display; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-calendar-alt"></i> Ngày Đặt Hàng:</span>
                    <span class="detail-value"><?php echo htmlspecialchars(date('H:i, d/m/Y', strtotime($order['ngay_dat']))); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-money-bill-wave"></i> Phương thức thanh toán:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['hinh_thuc_thanh_toan'] ?? 'Chưa xác định'); ?></span>
                </div>
            </div>

            <?php if ($caregiver_id_current): ?>
            <div class="section-box">
                <div class="section-title"><i class="icon fas fa-user-nurse"></i> Người Chăm Sóc</div>
                <div class="caregiver-info">
                    <img src="<?php echo htmlspecialchars($order['hinh_anh_cham_soc'] ?: 'https://via.placeholder.com/150/FF6B81/fff?text=CS'); ?>" alt="Avatar">
                    <div>
                        <div class="caregiver-name"><?php echo htmlspecialchars($order['ten_cham_soc']); ?></div>
                        <div class="caregiver-id">ID: #<?php echo htmlspecialchars($caregiver_id_current); ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="section-box">
                <div class="section-title"><i class="icon fas fa-clipboard-list"></i> Dịch Vụ & Thời Gian</div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-clock"></i> Bắt đầu:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['thoi_gian_bat_dau']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-hourglass-end"></i> Kết thúc:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['thoi_gian_ket_thuc']); ?></span>
                </div>

                <?php if (!empty($services)): ?>
                    <div style="margin-top: 10px;">
                        <span class="detail-label" style="font-weight: 700; color: #333;"><i class="icon fas fa-tasks"></i> Các Nhiệm Vụ Cụ Thể:</span>
                        <?php foreach ($services as $service): ?>
                            <div style="padding: 5px 0; font-size: 15px;">
                                <strong>- <?php echo htmlspecialchars($service['ten_nhiem_vu']); ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="section-box">
                <div class="section-title"><i class="icon fas fa-map-marker-alt"></i> Địa Chỉ Nhận Dịch Vụ</div>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-user"></i> Người nhận dịch vụ:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['ten_nguoi_nhan'] ?? $user['ten_khach_hang'] ?? 'N/A'); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-phone"></i> SĐT liên hệ:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['so_dien_thoai_nguoi_nhan'] ?? $user['so_dien_thoai'] ?? 'N/A'); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-map-marked-alt"></i> Địa chỉ:</span>
                    <span class="detail-value" style="text-align: right; max-width: 60%;"><?php echo $delivery_address; ?></span>
                </div>
            </div>

            <div class="section-box total-section">
                <span>Tổng Cộng</span>
                <span style="color: var(--main-color);"><?php echo number_format($order['tong_tien'], 0, ',', '.'); ?> VND</span>
            </div>

            <div class="buttons">
                
                <a href="index.php" class="button btn-home"><i class="fas fa-home"></i> Trang Chủ</a>
                <a href="javascript:history.back()" class="button btn-back"><i class="fas fa-arrow-left"></i> Quay Lại</a>
                
                <?php if ($caregiver_id_current): // Chỉ hiển thị nếu có người chăm sóc được gán ?>

                    <?php if (!$is_rated): ?>
                        <a href="DanhGia.php?id_don_hang=<?php echo $order_id_current; ?>&id=<?php echo $caregiver_id_current; ?>" class="button btn-rate"><i class="fas fa-star"></i> Đánh Giá</a>
                    <?php else: ?>
                        <button class="button btn-rated"><i class="fas fa-check"></i> Đã Đánh Giá</button>
                    <?php endif; ?>

                    <a href="Datdonhang.php?id=<?php echo $caregiver_id_current; ?>" class="button btn-reorder"><i class="fas fa-redo"></i> Đặt Lại</a>

                <?php endif; ?>

            </div>
        <?php endif; ?>
    </div>
</body>
</html>