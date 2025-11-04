<?php

session_start(); // Khởi tạo session

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
$id_don_hang = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_khach_hang = 0;

if (isset($_SESSION['role']) && $_SESSION['role'] === 'khach_hang' && isset($_SESSION['so_dien_thoai'])) {
    $so_dien_thoai = $_SESSION['so_dien_thoai'];

    // 1. Lấy id_khach_hang và ten_khach_hang dựa trên so_dien_thoai
    $stmt_kh = $pdo->prepare("SELECT id_khach_hang, ten_khach_hang, so_dien_thoai, dia_chi FROM khach_hang WHERE so_dien_thoai = ?");
    $stmt_kh->execute([$so_dien_thoai]);
    $user = $stmt_kh->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $id_khach_hang = $user['id_khach_hang'];
        $_SESSION['ten_khach_hang'] = $user['ten_khach_hang'];

        if ($id_don_hang > 0) {
            // 2. Lấy chi tiết đơn hàng theo ID và kiểm tra quyền sở hữu
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
                // 3. Lấy chi tiết dịch vụ đã đặt (từ bảng dich_vu_don_hang)
                $stmt_services = $pdo->prepare("SELECT ten_nhiem_vu, trang_thai_nhiem_vu FROM dich_vu_don_hang WHERE id_don_hang = :id_dh");
                $stmt_services->execute(['id_dh' => $id_don_hang]);
                $services = $stmt_services->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Đơn Hàng #<?php echo htmlspecialchars($id_don_hang); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ======================================= */
        /* CÁC STYLE CHUNG & LAYOUT */
        /* ======================================= */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { 
            background: #f8f8fa; /* Nền nhẹ nhàng */
            color: #333; 
            line-height: 1.6; 
            display: flex; 
            justify-content: center; 
            min-height: 100vh;
            padding: 30px 15px; 
        } 
        .container { 
            background: #fff; 
            border-radius: 16px; 
            padding: 30px; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.1); 
            width: 100%;
            max-width: 700px; 
        }
        h1 { 
            color: #FF6B81; /* Màu chủ đạo */
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: 800;
            text-align: center;
        }
        .order-id {
            text-align: center;
            font-size: 14px;
            color: #888;
            margin-bottom: 25px;
            font-weight: 500;
        }

        /* ======================================= */
        /* CÁC SECTION THÔNG TIN */
        /* ======================================= */
        .section-box {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            border-bottom: 2px solid #FFD8E0;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed #eee;
            font-size: 15px;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: 500;
            color: #666;
            display: flex;
            align-items: center;
        }
        .detail-value {
            font-weight: 600;
            color: #333;
        }
        .icon {
            margin-right: 8px;
            color: #FF6B81;
        }

        /* Thông tin Người chăm sóc */
        .caregiver-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #fff7f9;
            padding: 15px;
            border-radius: 10px;
        }
        .caregiver-info img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #FFD8E0;
        }
        .caregiver-name {
            font-weight: 700;
            color: #FF6B81;
        }
        .caregiver-id {
            font-size: 13px;
            color: #888;
        }

        /* Dịch vụ đã đặt */
        .service-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
            font-size: 15px;
        }
        .service-item:last-child {
            border-bottom: none;
        }
        .service-item strong {
            color: #555;
            font-weight: 600;
        }
        .service-item span {
            font-size: 13px;
            color: #777;
            display: block;
            margin-top: 2px;
        }
        .task-status {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
        }
        .status-hoan_thanh {
            background-color: #e8f5e9;
            color: #4caf50;
        }
        .status-chua_hoan_thanh, .status-dang_lam {
            background-color: #fff3e0;
            color: #ff9800;
        }


        /* Tổng tiền */
        .total-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px solid #FF6B81;
            padding-top: 15px;
            margin-top: 10px;
            font-size: 20px;
            font-weight: 800;
        }

        /* Trạng thái */
        .status-tag {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
        }
        .status-chờ_xác_nhận { background: #fff3e0; color: #ff9800; }
        .status-đã_nhận, .status-đang_tiến_hành { background: #e3f2fd; color: #2196f3; }
        .status-hoàn_thành { background: #e8f5e9; color: #4caf50; }
        .status-đã_hủy, .status-thất_bại { background: #ffebee; color: #f44336; }

        /* ======================================= */
        /* NÚT CHỨC NĂNG */
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
            transition: background-color 0.3s, transform 0.1s;
            text-decoration: none;
            color: white;
            font-size: 15px;
            font-weight: 600;
            text-align: center;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .button:active {
            transform: scale(0.98);
        }
        /* Nút Trang chủ (thay cho Quay Lại) */
        .btn-back {
            background-color: #9e9e9e; /* Xám */
            color: white;
        }
        .btn-back:hover {
            background-color: #757575;
        }
        /* Đánh giá */
        .btn-rate {
            background-color: #FFC300; /* Vàng Đánh giá */
            color: #333;
        }
        .btn-rate:hover {
            background-color: #e6b100;
        }
        /* Đặt lại */
        .btn-reorder {
            background-color: #FF6B81; /* Màu chủ đạo */
        }
        .btn-reorder:hover {
            background-color: #E55B70;
        }
        
    </style>
</head>
<body>
    <div class="container">
        <?php if (empty($order) || $id_don_hang == 0): ?>
            <h1 style="color: #f44336;"><i class="fas fa-exclamation-triangle"></i> Lỗi Truy Cập Đơn Hàng</h1>
            <p class="order-id" style="color:#d32f2f; font-size:16px;">Đơn hàng không tồn tại hoặc bạn không có quyền xem.</p>
            <div class="buttons">
                <a href="trangchu.php" class="button btn-back"><i class="fas fa-home"></i> Về Trang Chủ</a>
            </div>
        <?php else: 
            $status_class = strtolower(str_replace(' ', '_', $order['trang_thai']));
            $delivery_address = htmlspecialchars($order['dia_chi_giao_hang'] ?? $order['dia_chi_kh'] ?? 'Không rõ');
            $payment_method = htmlspecialchars($order['hinh_thuc_thanh_toan'] ?? 'Chưa xác định');
            if ($payment_method === 'cash') {
                $payment_method = 'Tiền mặt khi hoàn thành dịch vụ';
            } elseif ($payment_method === 'momo') {
                $payment_method = 'Thanh toán qua Momo';
            }
        ?>
            <h1><i class="fas fa-file-invoice"></i> Chi Tiết Đơn Hàng</h1>
            <p class="order-id">Mã đơn hàng: <strong>#<?php echo htmlspecialchars($order['id_don_hang']); ?></strong></p>

            <div class="section-box">
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-info-circle"></i> Trạng Thái Đơn Hàng:</span>
                    <span class="status-tag status-<?php echo $status_class; ?>"><?php echo htmlspecialchars(ucfirst($order['trang_thai'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-calendar-alt"></i> Ngày Đặt Hàng:</span>
                    <span class="detail-value"><?php echo htmlspecialchars(date('d/m/Y', strtotime($order['ngay_dat']))); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-credit-card"></i> Phương thức thanh toán:</span>
                    <span class="detail-value"><?php echo $payment_method; ?></span>
                </div>
            </div>

            <?php if ($order['caregiver_id']): ?>
            <div class="section-box">
                <div class="section-title"><i class="icon fas fa-user-nurse"></i> Người Chăm Sóc</div>
                <div class="caregiver-info">
                    <img src="<?php echo htmlspecialchars($order['hinh_anh_cham_soc'] ?: 'https://via.placeholder.com/150/ff6b81/fff?text=CS'); ?>" alt="Avatar">
                    <div>
                        <div class="caregiver-name"><?php echo htmlspecialchars($order['ten_cham_soc']); ?></div>
                        <div class="caregiver-id">ID: #<?php echo htmlspecialchars($order['caregiver_id']); ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="section-box">
                <div class="section-title"><i class="icon fas fa-clipboard-list"></i> Dịch Vụ & Thời Gian</div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-clock"></i> Bắt đầu:</span>
                    <span class="detail-value"><?php echo htmlspecialchars(date('H:i d/m/Y', strtotime($order['thoi_gian_bat_dau']))); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-hourglass-end"></i> Kết thúc:</span>
                    <span class="detail-value"><?php echo htmlspecialchars(date('H:i d/m/Y', strtotime($order['thoi_gian_ket_thuc']))); ?></span>
                </div>

                <?php if (!empty($services)): ?>
                    <div style="margin-top: 10px;">
                        <span class="detail-label" style="font-weight: 700; color: #333;"><i class="icon fas fa-tasks"></i> Các Nhiệm Vụ Cụ Thể:</span>
                        <?php foreach ($services as $service): 
                            $task_status = strtolower(str_replace(' ', '_', $service['trang_thai_nhiem_vu'] ?? 'chua_hoan_thanh'));
                            $display_status = ($task_status === 'hoan_thanh') ? 'Hoàn thành ✅' : 'Chưa hoàn thành ⏳';
                        ?>
                            <div class="service-item">
                                <strong><?php echo htmlspecialchars($service['ten_nhiem_vu']); ?></strong>
                                <span class="task-status status-<?php echo $task_status; ?>">
                                    <?php echo $display_status; ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="section-box">
                <div class="section-title"><i class="icon fas fa-map-marker-alt"></i> Địa Chỉ Nhận Dịch Vụ</div>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-user"></i> Người nhận dịch vụ:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['ten_nguoi_nhan'] ?? $order['ten_khach_hang'] ?? 'N/A'); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-phone"></i> SĐT liên hệ:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['so_dien_thoai_nguoi_nhan'] ?? $order['so_dien_theo_thoai'] ?? 'N/A'); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-map-marked-alt"></i> Địa chỉ:</span>
                    <span class="detail-value" style="text-align: right; max-width: 60%;"><?php echo $delivery_address; ?></span>
                </div>
            </div>

            <div class="section-box total-section">
                <span>Tổng Cộng (Đã bao gồm VAT)</span>
                <span style="color: #FF6B81;"><?php echo number_format($order['tong_tien'], 0, ',', '.'); ?> VND</span>
            </div>

            <div class="buttons">
                <a href="index.php" class="button btn-back"><i class="fas fa-home"></i> Trang Chủ</a>
                
                <?php 
                if ($order['caregiver_id']): 
                ?>
                    <a href="Danhgia.php?id=<?php echo $order['caregiver_id']; ?>" class="button btn-rate"><i class="fas fa-star"></i> Đánh Giá</a>
                
                    <a href="Datdonhang.php?id=<?php echo $order['caregiver_id']; ?>" class="button btn-reorder"><i class="fas fa-redo-alt"></i> Đặt Lại</a>
                <?php endif; ?>
                
            </div>
        <?php endif; ?>
    </div>
</body>
</html>