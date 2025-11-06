<<<<<<< HEAD
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
=======
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử Đơn hàng - Care Seeker</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Header Section */
        .header {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header h1 i {
            color: #667eea;
        }

        .back-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        /* Filter Section */
        .filter-section {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .filter-controls {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .filter-group label {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
        }

        .filter-group input,
        .filter-group select {
            padding: 14px 18px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #667eea;
        }

        .filter-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }

        .btn {
            padding: 14px 28px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        /* Order Cards */
        .orders-container {
            display: grid;
            gap: 20px;
        }

        .order-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 5px solid #667eea;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e5e7eb;
        }

        .order-id {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .order-id i {
            color: #667eea;
        }

        .status-badge {
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 15px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-processing {
            background: #dbeafe;
            color: #1e40af;
        }

        .order-body {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .order-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .order-info i {
            width: 24px;
            font-size: 20px;
            color: #667eea;
            text-align: center;
        }

        .order-info-content {
            flex: 1;
        }

        .order-info-label {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .order-info-value {
            font-size: 17px;
            font-weight: 600;
            color: #1f2937;
        }

        .order-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 2px solid #e5e7eb;
        }

        .order-total {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
        }

        .order-actions {
            display: flex;
            gap: 12px;
        }

        .action-btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-view {
            background: #667eea;
            color: white;
        }

        .btn-view:hover {
            background: #5568d3;
        }

        .btn-cancel {
            background: #fee2e2;
            color: #991b1b;
        }

        .btn-cancel:hover {
            background: #fecaca;
        }

        /* Empty State */
        .empty-state {
            background: white;
            border-radius: 15px;
            padding: 60px 30px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .empty-state i {
            font-size: 80px;
            color: #d1d5db;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 24px;
            color: #6b7280;
            margin-bottom: 10px;
        }

        .empty-state p {
            font-size: 16px;
            color: #9ca3af;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 25px 30px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 28px;
            color: #6b7280;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }

        .modal-close:hover {
            background: #f3f4f6;
        }

        .modal-body {
            padding: 30px;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .header h1 {
                font-size: 24px;
            }

            .filter-controls {
                grid-template-columns: 1fr;
            }

            .filter-actions {
                flex-direction: column;
            }

            .order-header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .order-body {
                grid-template-columns: 1fr;
            }

            .order-footer {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            .order-actions {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>
                <i class="fas fa-history"></i>
                Lịch sử Đơn hàng
            </h1>
            <button class="back-btn" onclick="goBack()">
                <i class="fas fa-arrow-left"></i>
                Quay lại
            </button>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="filter-controls">
                <div class="filter-group">
                    <label for="searchInput">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </label>
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="Mã đơn hàng, tên người chăm sóc..."
                    >
                </div>

                <div class="filter-group">
                    <label for="statusFilter">
                        <i class="fas fa-filter"></i> Trạng thái
                    </label>
                    <select id="statusFilter">
                        <option value="">Tất cả</option>
                        <option value="completed">Hoàn thành</option>
                        <option value="processing">Đang xử lý</option>
                        <option value="pending">Chờ xác nhận</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="dateFrom">
                        <i class="fas fa-calendar"></i> Từ ngày
                    </label>
                    <input type="date" id="dateFrom">
                </div>

                <div class="filter-group">
                    <label for="dateTo">
                        <i class="fas fa-calendar"></i> Đến ngày
                    </label>
                    <input type="date" id="dateTo">
                </div>
            </div>

            <div class="filter-actions">
                <button class="btn btn-secondary" onclick="resetFilters()">
                    <i class="fas fa-redo"></i>
                    Đặt lại
                </button>
                <button class="btn btn-primary" onclick="applyFilters()">
                    <i class="fas fa-search"></i>
                    Tìm kiếm
                </button>
            </div>
        </div>

        <!-- Orders Container -->
        <div class="orders-container" id="ordersContainer">
            <!-- Orders will be dynamically inserted here -->
        </div>

        <!-- Empty State -->
        <div class="empty-state" id="emptyState" style="display: none;">
            <i class="fas fa-inbox"></i>
            <h3>Không có đơn hàng nào</h3>
            <p>Bạn chưa có đơn hàng nào trong hệ thống</p>
        </div>
    </div>

    <!-- Modal for Order Details -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Chi tiết Đơn hàng</h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // Sample order data
        const ordersData = [
            {
                id: 'DH001',
                date: '2025-10-25',
                time: '08:00 - 17:00',
                caregiver: 'Nguyễn Thị Lan',
                service: 'Chăm sóc tại nhà',
                address: '123 Đường ABC, Quận 1, TP.HCM',
                total: 500000,
                status: 'completed',
                statusText: 'Hoàn thành'
            },
            {
                id: 'DH002',
                date: '2025-10-28',
                time: '09:00 - 18:00',
                caregiver: 'Trần Văn Minh',
                service: 'Chăm sóc người bệnh',
                address: '456 Đường XYZ, Quận 3, TP.HCM',
                total: 650000,
                status: 'processing',
                statusText: 'Đang xử lý'
            },
            {
                id: 'DH003',
                date: '2025-10-30',
                time: '07:00 - 15:00',
                caregiver: 'Chưa phân công',
                service: 'Chăm sóc sau phẫu thuật',
                address: '789 Đường DEF, Quận 5, TP.HCM',
                total: 800000,
                status: 'pending',
                statusText: 'Chờ xác nhận'
            },
            {
                id: 'DH004',
                date: '2025-10-15',
                time: '08:00 - 16:00',
                caregiver: 'Lê Thị Hoa',
                service: 'Chăm sóc người cao tuổi',
                address: '321 Đường GHI, Quận 7, TP.HCM',
                total: 550000,
                status: 'cancelled',
                statusText: 'Đã hủy'
            }
        ];

        let filteredOrders = [...ordersData];

        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }

        // Render orders
        function renderOrders(orders) {
            const container = document.getElementById('ordersContainer');
            const emptyState = document.getElementById('emptyState');

            if (orders.length === 0) {
                container.style.display = 'none';
                emptyState.style.display = 'block';
                return;
            }

            container.style.display = 'grid';
            emptyState.style.display = 'none';

            container.innerHTML = orders.map(order => `
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-id">
                            <i class="fas fa-file-invoice"></i>
                            ${order.id}
                        </div>
                        <span class="status-badge status-${order.status}">
                            ${order.statusText}
                        </span>
                    </div>

                    <div class="order-body">
                        <div class="order-info">
                            <i class="fas fa-calendar-alt"></i>
                            <div class="order-info-content">
                                <div class="order-info-label">Ngày đặt</div>
                                <div class="order-info-value">${order.date}</div>
                            </div>
                        </div>

                        <div class="order-info">
                            <i class="fas fa-clock"></i>
                            <div class="order-info-content">
                                <div class="order-info-label">Thời gian</div>
                                <div class="order-info-value">${order.time}</div>
                            </div>
                        </div>

                        <div class="order-info">
                            <i class="fas fa-user-nurse"></i>
                            <div class="order-info-content">
                                <div class="order-info-label">Người chăm sóc</div>
                                <div class="order-info-value">${order.caregiver}</div>
                            </div>
                        </div>

                        <div class="order-info">
                            <i class="fas fa-concierge-bell"></i>
                            <div class="order-info-content">
                                <div class="order-info-label">Dịch vụ</div>
                                <div class="order-info-value">${order.service}</div>
                            </div>
                        </div>

                        <div class="order-info" style="grid-column: 1 / -1;">
                            <i class="fas fa-map-marker-alt"></i>
                            <div class="order-info-content">
                                <div class="order-info-label">Địa chỉ</div>
                                <div class="order-info-value">${order.address}</div>
                            </div>
                        </div>
                    </div>

                    <div class="order-footer">
                        <div class="order-total">
                            ${formatCurrency(order.total)}
                        </div>
                        <div class="order-actions">
                            <button class="action-btn btn-view" onclick="viewOrderDetails('${order.id}')">
                                <i class="fas fa-eye"></i>
                                Chi tiết
                            </button>
                            ${order.status === 'pending' ? `
                                <button class="action-btn btn-cancel" onclick="cancelOrder('${order.id}')">
                                    <i class="fas fa-times"></i>
                                    Hủy đơn
                                </button>
                            ` : ''}
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Apply filters
        function applyFilters() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const dateFrom = document.getElementById('dateFrom').value;
            const dateTo = document.getElementById('dateTo').value;

            filteredOrders = ordersData.filter(order => {
                const matchesSearch = !searchTerm || 
                    order.id.toLowerCase().includes(searchTerm) ||
                    order.caregiver.toLowerCase().includes(searchTerm);

                const matchesStatus = !statusFilter || order.status === statusFilter;

                const matchesDateFrom = !dateFrom || order.date >= dateFrom;
                const matchesDateTo = !dateTo || order.date <= dateTo;

                return matchesSearch && matchesStatus && matchesDateFrom && matchesDateTo;
            });

            renderOrders(filteredOrders);
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('dateFrom').value = '';
            document.getElementById('dateTo').value = '';
            filteredOrders = [...ordersData];
            renderOrders(filteredOrders);
        }

        // View order details
        function viewOrderDetails(orderId) {
            const order = ordersData.find(o => o.id === orderId);
            if (!order) return;

            const modalBody = document.getElementById('modalBody');
            modalBody.innerHTML = `
                <div class="order-detail-section">
                    <h4><i class="fas fa-info-circle"></i> Thông tin đơn hàng</h4>
                    <div class="detail-grid">
                        <div class="detail-item-modal">
                            <i class="fas fa-hashtag"></i>
                            <span class="detail-label-modal">Mã đơn:</span>
                            <span class="detail-value-modal">${order.id}</span>
                        </div>
                        <div class="detail-item-modal">
                            <i class="fas fa-calendar"></i>
                            <span class="detail-label-modal">Ngày đặt:</span>
                            <span class="detail-value-modal">${order.date}</span>
                        </div>
                        <div class="detail-item-modal">
                            <i class="fas fa-clock"></i>
                            <span class="detail-label-modal">Thời gian:</span>
                            <span class="detail-value-modal">${order.time}</span>
                        </div>
                        <div class="detail-item-modal">
                            <i class="fas fa-user-nurse"></i>
                            <span class="detail-label-modal">Người chăm sóc:</span>
                            <span class="detail-value-modal">${order.caregiver}</span>
                        </div>
                        <div class="detail-item-modal" style="grid-column: 1 / -1;">
                            <i class="fas fa-map-marker-alt"></i>
                            <span class="detail-label-modal">Địa chỉ:</span>
                            <span class="detail-value-modal">${order.address}</span>
                        </div>
                    </div>
                </div>
                <div style="margin-top: 20px; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; color: white; text-align: center;">
                    <h3 style="margin: 0; font-size: 28px; font-weight: 700;">${formatCurrency(order.total)}</h3>
                    <p style="margin: 5px 0 0; opacity: 0.9;">Tổng tiền</p>
                </div>
            `;

            document.getElementById('orderModal').classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        // Cancel order
        function cancelOrder(orderId) {
            if (confirm(`Bạn có chắc chắn muốn hủy đơn hàng ${orderId}?`)) {
                const orderIndex = ordersData.findIndex(o => o.id === orderId);
                if (orderIndex !== -1) {
                    ordersData[orderIndex].status = 'cancelled';
                    ordersData[orderIndex].statusText = 'Đã hủy';
                    applyFilters();
                    alert(`Đơn hàng ${orderId} đã được hủy thành công!`);
                }
            }
        }

        // Close modal
        function closeModal() {
            document.getElementById('orderModal').classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        // Go back
        function goBack() {
            window.history.back();
        }

        // Close modal on outside click
        window.onclick = function(event) {
            const modal = document.getElementById('orderModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // Close modal with ESC key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            renderOrders(filteredOrders);

            // Add real-time search
            document.getElementById('searchInput').addEventListener('input', applyFilters);
            document.getElementById('statusFilter').addEventListener('change', applyFilters);
        });
    </script>
</body>
</html>


>>>>>>> Thư
