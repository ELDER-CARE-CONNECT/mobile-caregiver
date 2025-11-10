<?php
session_start();

// =======================================
// CẤU HÌNH KẾT NỐI DB (Sử dụng PDO)
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

$order = [];
$services = [];
$id_don_hang = isset($_GET['id']) ? intval($_GET['id']) : 0;
$id_khach_hang = 0;
$is_rated = false;

if (isset($_SESSION['role']) && $_SESSION['role'] === 'khach_hang' && isset($_SESSION['so_dien_thoai'])) {
    $so_dien_thoai = $_SESSION['so_dien_thoai'];

    $stmt_kh = $pdo->prepare("
        SELECT id_khach_hang, ten_khach_hang, so_dien_thoai
        FROM khach_hang
        WHERE so_dien_thoai = ?
    ");
    $stmt_kh->execute([$so_dien_thoai]);
    $user = $stmt_kh->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $id_khach_hang = $user['id_khach_hang'];
        $_SESSION['ten_khach_hang'] = $user['ten_khach_hang'];

        if ($id_don_hang > 0) {
            $stmt = $pdo->prepare("
                SELECT 
                    dh.*, 
                    dh.trang_thai_nhiem_vu,
                    kh.ten_khach_hang, kh.so_dien_thoai, 
                    TRIM(CONCAT_WS(', ', kh.ten_duong, kh.phuong_xa, kh.tinh_thanh)) AS dia_chi_kh,
                    ncs.ho_ten AS ten_cham_soc, ncs.hinh_anh AS hinh_anh_cham_soc, ncs.id_cham_soc AS caregiver_id
                FROM don_hang dh
                LEFT JOIN khach_hang kh ON dh.id_khach_hang = kh.id_khach_hang
                LEFT JOIN nguoi_cham_soc ncs ON dh.id_nguoi_cham_soc = ncs.id_cham_soc
                WHERE dh.id_don_hang = :id_dh AND dh.id_khach_hang = :id_kh
            ");
            $stmt->bindValue(':id_dh', $id_don_hang, PDO::PARAM_INT);
            $stmt->bindValue(':id_kh', $id_khach_hang, PDO::PARAM_INT);
            $stmt->execute();
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                if ($order['caregiver_id']) {
                    $stmt_check_review = $pdo->prepare("
                        SELECT id_danh_gia FROM danh_gia 
                        WHERE id_khach_hang = :id_kh AND id_cham_soc = :id_cs
                        LIMIT 1
                    ");
                    $stmt_check_review->bindValue(':id_kh', $id_khach_hang, PDO::PARAM_INT);
                    $stmt_check_review->bindValue(':id_cs', $order['caregiver_id'], PDO::PARAM_INT);
                    $stmt_check_review->execute();

                    if ($stmt_check_review->fetchColumn()) {
                        $is_rated = true;
                    }
                }

                if (!empty($order['ten_nhiem_vu'])) {
                    $tasks_raw = trim($order['ten_nhiem_vu'], '[""]');
                    $tasks_list = preg_split('/";\s*"/', $tasks_raw);
                    $status_raw = $order['trang_thai_nhiem_vu'] ?? 'chua_hoan_thanh';
                    $status_list = explode(';', $status_raw);

                    $is_array_string = (strpos($order['ten_nhiem_vu'], '["') === 0) && (strpos($order['ten_nhiem_vu'], '"]') !== false);
                    $final_tasks_list = [];

                    if ($is_array_string) {
                        $decoded_tasks = json_decode(str_replace(';', ',', $order['ten_nhiem_vu']), true);
                        if (is_array($decoded_tasks)) $final_tasks_list = $decoded_tasks;
                    }

                    $tasks_to_process = empty($final_tasks_list) ? $tasks_list : $final_tasks_list;
                    $task_count = count($tasks_to_process);
                    $status_count = count($status_list);
                    $services = [];

                    for ($i = 0; $i < $task_count; $i++) {
                        $task_name = trim($tasks_to_process[$i], ' "');
                        if (!empty($task_name)) {
                            $task_status = ($i < $status_count && !empty(trim($status_list[$i])))
                                ? trim($status_list[$i])
                                : (strtolower(trim($order['trang_thai'])) === 'đã hoàn thành' ? 'hoan_thanh' : 'chua_hoan_thanh');

                            $services[] = [
                                'ten_nhiem_vu' => $task_name,
                                'trang_thai_nhiem_vu' => $task_status
                            ];
                        }
                    }
                }
            }
        }
    }
}

function status_to_class($status) {
    $status = strtolower($status);
    $status = str_replace(
        ['á','à','ả','ã','ạ','ă','ằ','ắ','ẳ','ặ','â','ầ','ấ','ẩ','ậ','đ','é','è','ẻ','ẽ','ẹ','ê','ề','ế','ể','ệ','í','ì','ỉ','ĩ','ị','ó','ò','ỏ','õ','ọ','ô','ồ','ố','ổ','ộ','ơ','ờ','ớ','ở','ợ','ú','ù','ủ','ũ','ụ','ư','ừ','ứ','ử','ự','ý','ỳ','ỷ','ỹ','ỵ'],
        ['a','a','a','a','a','a','a','a','a','a','a','a','a','d','e','e','e','e','e','e','e','e','e','e','i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','u','u','u','u','u','u','u','u','u','u','y','y','y','y','y'],
        $status
    );
    return str_replace(' ', '_', $status);
}

$message = '';
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
        /* (GIỮ NGUYÊN CÁC STYLE CSS) */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { 
            background: #f8f8fa; 
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
            color: #FF6B81; 
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
            word-break: break-word; 
            text-align: right;
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
            border-bottom: 1px dashed #f0f0f0; 
            font-size: 15px;
        }
        .service-item:last-child {
            border-bottom: none;
        }
        .service-item strong {
            color: #555;
            font-weight: 600;
            max-width: 70%; 
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
            flex-shrink: 0; 
        }
        /* Style cho trạng thái nhiệm vụ */
        .status-chua_hoan_thanh {
            background-color: #fff3e0;
            color: #ff9800;
        }
        .status-hoan_thanh {
            background-color: #e8f5e9;
            color: #4caf50;
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

        /* Trạng thái Đơn hàng */
        .status-tag {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
        }
        .status-cho_xac_nhan { background: #fff3e0; color: #ff9800; }
        .status-dang_hoan_thanh, .status-da_nhan { background: #e3f2fd; color: #2196f3; }
        .status-da_hoan_thanh { background: #e8f5e9; color: #4caf50; }
        .status-da_huy { background: #ffebee; color: #f44336; }
        .status-that_bai { background: #ffebee; color: #f44336; }

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
        /* Nút Trang chủ */
        .btn-back {
            background-color: #9e9e9e; 
            color: white;
        }
        .btn-back:hover {
            background-color: #757575;
        }
        /* Đánh giá */
        .btn-rate {
            background-color: #FFFF66; 
            color: #333;
        }
        .btn-rate:hover {
            background-color: #e6b100;
        }
        /* Đặt lại */
        .btn-reorder {
            background-color: #FF6B81; 
        }
        .btn-reorder:hover {
            background-color: #E55B70;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php echo $message; // Hiển thị thông báo (thành công/lỗi) ?>

        <?php if (empty($order) || $id_don_hang == 0): ?>
            <h1 style="color: #f44336;"><i class="fas fa-exclamation-triangle"></i> Lỗi Truy Cập Đơn Hàng</h1>
            <p class="order-id" style="color:#d32f2f; font-size:16px;">Đơn hàng không tồn tại hoặc bạn không có quyền xem.</p>
            <div class="buttons">
                <a href="index.php" class="button btn-back"><i class="fas fa-home"></i> Về Trang Chủ</a>
            </div>
        <?php else: 
            $status_class = status_to_class($order['trang_thai']);
            $delivery_address = htmlspecialchars($order['dia_chi_giao_hang'] ?: $order['dia_chi_kh'] ?: 'Không rõ');
            
            // Xử lý hiển thị Phương thức thanh toán (GIỮ NGUYÊN)
            $payment_method = htmlspecialchars($order['hinh_thuc_thanh_toan'] ?? 'Chưa xác định');
            if (strtolower($payment_method) === 'cash' || strtolower($payment_method) === 'tiền mặt') {
                $payment_method = 'Tiền mặt';
            } elseif (strtolower($payment_method) === 'vnpay' || strpos(strtolower($payment_method), 'vnpay') !== false) {
                $payment_status = strtolower($order['thanh_toan_status'] ?? 'chưa thanh toán');
                if ($payment_status === 'đã thanh toán') {
                     $payment_method = 'VNPAY (Đã TT)';
                } else {
                     $payment_method = 'Thanh toán qua VNPAY (Chờ)';
                }
            } else {
                $payment_method = htmlspecialchars($order['hinh_thuc_thanh_toan'] ?? 'Chưa xác định');
            }
        ?>
            <h1><i class="fas fa-file-invoice"></i> Chi Tiết Lịch Sử Đơn Hàng</h1>
            <p class="order-id">Mã đơn hàng: <strong>#<?php echo htmlspecialchars($order['id_don_hang']); ?></strong></p>

            <div class="section-box">
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-info-circle"></i> Trạng Thái Đơn Hàng:</span>
                    <span class="status-tag status-<?php echo $status_class; ?>"><?php echo htmlspecialchars(ucfirst($order['trang_thai'])); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-calendar-alt"></i> Ngày Đặt Hàng:</span>
                    <span class="detail-value"><?php echo htmlspecialchars(date('H:i, d/m/Y', strtotime($order['ngay_dat']))); ?></span>
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
                    <img src="<?php 
                        $img_path = $order['hinh_anh_cham_soc'];
                        if (strpos($img_path, 'fontend/') === 0) {
                            echo htmlspecialchars('../' . $img_path); 
                        } else {
                            echo 'https://via.placeholder.com/150/ff6b81/fff?text=CS';
                        }
                    ?>" alt="Avatar">
                    <div>
                        <div class="caregiver-name"><?php echo htmlspecialchars($order['ten_cham_soc']); ?></div>
                        <div class="caregiver-id">ID: #<?php echo htmlspecialchars($order['caregiver_id']); ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

           <div class="section-box">
                <div class="section-title"><i class="icon fas fa-clipboard-list"></i> Dịch Vụ & Thời Gian</div>
                <?php 
                $start_datetime = $order['thoi_gian_bat_dau'] ?? null;
                $end_datetime = $order['thoi_gian_ket_thuc'] ?? null;

                $start_display = 'N/A';
                if ($start_datetime) {
                    $start_display = htmlspecialchars(date('H:i, d/m/Y', strtotime($start_datetime)));
                }

                $end_display = 'N/A';
                if ($end_datetime) {
                    $end_display = htmlspecialchars(date('H:i, d/m/Y', strtotime($end_datetime)));
                }
                ?>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-clock"></i> Bắt đầu:</span>
                    <span class="detail-value"><?php echo $start_display; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-hourglass-end"></i> Kết thúc:</span>
                    <span class="detail-value"><?php echo $end_display; ?></span>
                </div>

                <?php if (!empty($services)): ?>
                    <div style="margin-top: 15px;">
                        <span class="detail-label" style="font-weight: 700; color: #333;"><i class="icon fas fa-tasks"></i> Các Nhiệm Vụ Cụ Thể:</span>
                        
                        <?php foreach ($services as $service): 
                            $actual_status = strtolower(trim($service['trang_thai_nhiem_vu']));
                            $task_status_class = status_to_class($actual_status); 

                            // Hiển thị trạng thái tiếng Việt
                            $display_status = ($actual_status === 'hoan_thanh') ? 'Hoàn thành ✅' : 'Chờ thực hiện ⏳';
                        ?>
                            <div class="service-item">
                                <strong><?php echo htmlspecialchars($service['ten_nhiem_vu']); ?></strong>
                                <span class="task-status status-<?php echo $task_status_class; ?>">
                                    <?php echo $display_status; ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                        </div>
                <?php endif; ?>
            </div>

            <div class="section-box">
                <div class="section-title"><i class="icon fas fa-map-marker-alt"></i> Thông Tin Liên Hệ/Địa Chỉ</div>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-user"></i> Người liên hệ:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['ten_khach_hang'] ?? 'N/A'); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-phone"></i> SĐT liên hệ:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['so_dien_thoai'] ?? 'N/A'); ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-map-marked-alt"></i> Địa chỉ phục vụ:</span>
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
    // Kiểm tra xem đã có người chăm sóc chưa
    if ($order['caregiver_id']) { 
        // Kiểm tra trạng thái đơn hàng: Phải là 'Đã hoàn thành' hoặc 'da hoan thanh'
        $is_completed = (strtolower($order['trang_thai']) === 'đã hoàn thành' || strtolower($order['trang_thai']) === 'da hoan thanh');

        if ($is_completed) {
            if (!$is_rated) {
                // Đã hoàn thành VÀ Chưa đánh giá: Hiển thị nút Đánh Giá
                echo '<a href="Danhgia.php?id_cs=' . htmlspecialchars($order['caregiver_id']) . '&id_dh=' . htmlspecialchars($order['id_don_hang']) . '" class="button btn-rate"><i class="fas fa-star"></i> Đánh Giá</a>';
            } else {
                // Đã hoàn thành VÀ Đã đánh giá: Hiển thị nút Đã Đánh Giá
                echo '<button class="button btn-rate" style="opacity:0.6; cursor:not-allowed;"><i class="fas fa-check"></i> Đã Đánh Giá</button>';
            }
        }
    }
    ?>

    <a href="Dathanglai.php?id=<?php echo htmlspecialchars($order['id_don_hang']); ?>" class="button btn-reorder">
        <i class="fas fa-redo"></i> Đặt Lại
    </a>
</div>

<?php endif; // ✅ đóng khối if (empty($order)) ?>
    </div>
</body>
</html>
