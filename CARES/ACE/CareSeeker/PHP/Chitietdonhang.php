<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập với vai trò khách hàng chưa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'khach_hang' || !isset($_SESSION['so_dien_thoai'])) {
    // Chuyển hướng nếu không phải khách hàng đã đăng nhập
    header("Location: index.php"); 
    exit();
}

// =======================================
// CẤU HÌNH KẾT NỐI DB (PDO)
// =======================================
$host = '127.0.0.1';
$dbname = 'sanpham';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Đặt chế độ fetch mặc định
} catch (PDOException $e) {
    // Dừng và báo lỗi nếu kết nối DB thất bại (Chỉ nên dùng cho môi trường Dev)
    die("Kết nối DB thất bại: " . $e->getMessage());
}

// Biến lưu thông tin
$order = [];
$services = []; // Thêm biến services để lưu chi tiết nhiệm vụ
$id_khach_hang = 0;
$is_rated = false;
$id_don_hang = isset($_GET['id']) ? intval($_GET['id']) : 0;
$so_dien_thoai_session = $_SESSION['so_dien_thoai'];

// =======================================
// LẤY ID KHÁCH HÀNG TỪ SESSION
// =======================================
$stmt_kh = $pdo->prepare("
    SELECT 
        id_khach_hang, 
        ten_khach_hang, 
        TRIM(CONCAT_WS(', ', ten_duong, phuong_xa, tinh_thanh)) AS dia_chi
    FROM khach_hang 
    WHERE so_dien_thoai = ?
");
$stmt_kh->execute([$so_dien_thoai_session]);
$user = $stmt_kh->fetch();

if ($user) {
    $id_khach_hang = $user['id_khach_hang'];
    $_SESSION['ten_khach_hang'] = $user['ten_khach_hang'];
    // Có thể lưu thêm ID vào session nếu cần cho các trang khác
} else {
    // Khách hàng không tồn tại trong DB, kết thúc phiên
    session_destroy();
    header("Location: login.php");
    exit();
}

// =======================================
// XỬ LÝ HỦY ĐƠN HÀNG (SỬ DỤNG $id_khach_hang đã lấy)
// =======================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_order') {
    $id_don_hang_to_cancel = isset($_POST['id_don_hang']) ? intval($_POST['id_don_hang']) : 0;

    if ($id_don_hang_to_cancel > 0 && $id_khach_hang > 0) {
        $stmt_check = $pdo->prepare("SELECT trang_thai FROM don_hang WHERE id_don_hang = ? AND id_khach_hang = ?");
        $stmt_check->execute([$id_don_hang_to_cancel, $id_khach_hang]);
        $order_status = $stmt_check->fetchColumn();

        // Kiểm tra trạng thái và quyền sở hữu trước khi hủy
        if ($order_status && strtolower(trim($order_status)) === 'chờ xác nhận') {
            try {
                $stmt_update = $pdo->prepare("UPDATE don_hang SET trang_thai = 'đã hủy' WHERE id_don_hang = ?");
                $stmt_update->execute([$id_don_hang_to_cancel]);
                header("Location: ChiTietDonHang.php?id=" . $id_don_hang_to_cancel . "&status=cancelled");
                exit();
            } catch (PDOException $e) {
                // Ghi log lỗi nếu cần
                header("Location: ChiTietDonHang.php?id=" . $id_don_hang_to_cancel . "&error=cancel_failed");
                exit();
            }
        } else {
            header("Location: ChiTietDonHang.php?id=" . $id_don_hang_to_cancel . "&error=status_mismatch");
            exit();
        }
    } else {
        header("Location: index.php");
        exit();
    }
}

// =======================================
// LẤY DỮ LIỆU ĐƠN HÀNG CHI TIẾT
// =======================================
if ($id_khach_hang > 0) {
    // Nếu không có ID đơn hàng trên URL, lấy đơn hàng mới nhất
    if ($id_don_hang == 0) {
        $stmt_latest = $pdo->prepare("SELECT id_don_hang FROM don_hang WHERE id_khach_hang = ? ORDER BY ngay_dat DESC LIMIT 1");
        $stmt_latest->execute([$id_khach_hang]);
        $latest_order_id = $stmt_latest->fetchColumn();
        if ($latest_order_id) {
            $id_don_hang = intval($latest_order_id);
        }
    }

    // Lấy thông tin đơn hàng chi tiết (chỉ lấy đơn hàng của khách hàng hiện tại)
    if ($id_don_hang > 0) {
        $stmt = $pdo->prepare("
            SELECT 
                dh.*, 
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
        $order = $stmt->fetch();

        if ($order) {
            // Kiểm tra đã đánh giá chưa nếu đơn hàng đã hoàn thành và có người chăm sóc
            if (strtolower(trim($order['trang_thai'])) === 'đã hoàn thành' && $order['caregiver_id']) {
                $stmt_check_review = $pdo->prepare("
                    SELECT id_danh_gia 
                    FROM danh_gia 
                    WHERE id_khach_hang = :id_kh AND id_cham_soc = :id_cs AND id_don_hang = :id_dh
                    LIMIT 1
                ");
                $stmt_check_review->bindValue(':id_kh', $id_khach_hang, PDO::PARAM_INT);
                $stmt_check_review->bindValue(':id_cs', $order['caregiver_id'], PDO::PARAM_INT);
                $stmt_check_review->bindValue(':id_dh', $id_don_hang, PDO::PARAM_INT); // Thêm id_don_hang để đánh giá chính xác theo đơn
                $stmt_check_review->execute();
                if ($stmt_check_review->fetchColumn()) {
                    $is_rated = true;
                }
            }
            
            // Xử lý dịch vụ (nhiệm vụ) - giống Chitietlichsudonhang.php nhưng không cần trạng thái nhiệm vụ
            if (!empty($order['ten_nhiem_vu'])) {
                // Xử lý chuỗi JSON-like (nếu có)
                $is_array_string = (strpos($order['ten_nhiem_vu'] ?? '', '["') === 0) && (strpos($order['ten_nhiem_vu'] ?? '', '"]') !== false);
                        
                if ($is_array_string) {
                    $tasks_list = json_decode(str_replace(';', ',', $order['ten_nhiem_vu']), true);
                } else {
                    // Xử lý chuỗi phân cách bởi xuống dòng (như logic cũ của Chitietdonhang.php)
                    $tasks_list = preg_split("/\r\n|\n|\r/", $order['ten_nhiem_vu']);
                }

                $tasks_list = array_filter(array_map('trim', $tasks_list)); // Loại bỏ dòng trống và khoảng trắng

                foreach ($tasks_list as $task) {
                    $task_name = trim($task, ' "');
                    if (!empty($task_name)) {
                        $services[] = [
                            'ten_nhiem_vu' => $task_name,
                            // KHÔNG CÓ CỘT TRẠNG THÁI NHIỆM VỤ Ở ĐÂY theo yêu cầu
                        ];
                    }
                }
            }
        }
    }
}

// =======================================
// HÀM HỖ TRỢ (để xử lý trong HTML)
// =======================================
function status_to_class($status) {
    $status = strtolower(trim($status));
    $status = str_replace(
        [' ', 'á','à','ả','ã','ạ','ă','ằ','ắ','ẳ','ặ','â','ầ','ấ','ẩ','ậ','đ','é','è','ẻ','ẽ','ẹ','ê','ề','ế','ể','ệ','í','ì','ỉ','ĩ','ị','ó','ò','ỏ','õ','ọ','ô','ồ','ố','ổ','ộ','ơ','ờ','ớ','ở','ợ','ú','ù','ủ','ũ','ụ','ư','ừ','ứ','ử','ự','ý','ỳ','ỷ','ỹ','ỵ'],
        ['_','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','d','e','e','e','e','e','e','e','e','e','e','i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','u','u','u','u','u','u','u','u','u','u','y','y','y','y','y'],
        $status
    );
    // Xử lý các trạng thái đa từ đặc biệt
    if ($status === 'dang_hoan_thanh') return 'dang_tien_hanh'; 
    if ($status === 'da_hoan_thanh') return 'hoan_thanh';
    return $status;
}

// =======================================
// THÔNG BÁO
// =======================================
$message = '';
if (isset($_GET['status']) && $_GET['status'] === 'cancelled') {
    $message = '<div style="background-color: #e8f5e9; color: #4caf50; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; text-align: center;"><i class="fas fa-check-circle"></i> Đơn hàng #' . htmlspecialchars($id_don_hang) . ' đã được hủy thành công!</div>';
} elseif (isset($_GET['error'])) {
    $error_msg = 'Đã xảy ra lỗi khi hủy đơn hàng.';
    if ($_GET['error'] === 'status_mismatch') {
        $error_msg = 'Chỉ có thể hủy đơn hàng ở trạng thái **Chờ xác nhận**.';
    } elseif ($_GET['error'] === 'cancel_failed') {
        $error_msg = 'Lỗi hệ thống: Không thể cập nhật trạng thái đơn hàng.';
    }
    $message = '<div style="background-color: #ffebee; color: #f44336; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; text-align: center;"><i class="fas fa-times-circle"></i> ' . $error_msg . '</div>';
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
        
        /* DANH SÁCH NHIỆM VỤ (task-list) */
        /* Dùng style chung với service-item của file lịch sử để đồng bộ */
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
            max-width: 95%; /* Cho phép chiếm gần hết chiều rộng */
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
        .status-cho_xac_nhan { background: #fff3e0; color: #ff9800; }
        .status-dang_tien_hanh { background: #e3f2fd; color: #2196f3; }
        .status-hoan_thanh { background: #e8f5e9; color: #4caf50; }
        .status-da_huy { background: #ffebee; color: #f44336; }

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
        
        /* Quay Lại */
        .btn-back {
            background-color: #9e9e9e; /* Xám */
            color: white;
        }
        .btn-back:hover {
            background-color: #757575;
        }
        
        /* Trang Chủ */
        .btn-home {
            background-color: #FF6B81; /* Màu chủ đạo */
        }
        .btn-home:hover {
            background-color: #E55B70;
        }

        /* Đánh giá */
        .btn-rate {
            background-color: #FFC300; /* Vàng Đánh giá */
            color: #333;
        }
        .btn-rate:hover {
            background-color: #e6b100;
        }
        
        /* Hủy Đơn */
        .btn-cancel {
            background-color: #f44336; /* Đỏ */
        }
        .btn-cancel:hover {
            background-color: #d32f2f;
        }
        
        /* Chat */
        .btn-chat {
            background-color: #2196f3; /* Xanh dương */
        }
        .btn-chat:hover {
            background-color: #1976d2;
        }
    </style>
    <script>
        function confirmCancel(id_don_hang) {
            if (confirm("Bạn có chắc chắn muốn HỦY đơn hàng #" + id_don_hang + " không? Hành động này không thể hoàn tác.")) {
                document.getElementById('cancelForm').submit();
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <?php echo $message; // Hiển thị thông báo (thành công/lỗi) ?>
        
        <?php if (empty($order) || $id_don_hang == 0): ?>
            <h1 style="color: #f44336;"><i class="fas fa-exclamation-triangle"></i> Lỗi Truy Cập Đơn Hàng</h1>
            <p class="order-id" style="color:#d32f2f; font-size:16px;">Đơn hàng không tồn tại, bạn không có quyền xem hoặc chưa có đơn hàng nào.</p>
            <div class="buttons">
                <a href="index.php" class="button btn-home"><i class="fas fa-home"></i> Trang Chủ</a>
                <a href="javascript:history.back()" class="button btn-back"><i class="fas fa-arrow-left"></i> Quay Lại</a>
            </div>
        <?php else: 
            $status_class = status_to_class($order['trang_thai']);
            
            // Lấy địa chỉ giao hàng, ưu tiên địa chỉ riêng của đơn hàng nếu có, không thì dùng địa chỉ khách hàng
            $delivery_address = htmlspecialchars($order['dia_chi_giao_hang'] ?? $order['dia_chi_kh'] ?? 'Không rõ');

            // --- XỬ LÝ NGÀY GIỜ TỪ DATETIME ---
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

            // Xử lý hiển thị Phương thức thanh toán (Giống file Lịch sử)
            $payment_method = htmlspecialchars($order['hinh_thuc_thanh_toan'] ?? 'Chưa xác định');
            if (strtolower($payment_method) === 'cash' || strtolower($payment_method) === 'tiền mặt') {
                $payment_method = 'Tiền mặt';
            } elseif (strtolower($payment_method) === 'vnpay' || strpos(strtolower($payment_method), 'vnpay') !== false) {
                $payment_status = strtolower($order['thanh_toan_status'] ?? 'chưa thanh toán');
                if ($payment_status === 'đã thanh toán') {
                     $payment_method = 'VNPAY';
                } else {
                     $payment_method = 'Thanh toán qua VNPAY (Chờ)';
                }
            } else {
                $payment_method = htmlspecialchars($order['hinh_thuc_thanh_toan'] ?? 'Chưa xác định');
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
                        // Xử lý đường dẫn tương đối từ DB (giả định cấu trúc tương tự file lịch sử)
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
                        
                        <?php foreach ($services as $service): ?>
                            <div class="service-item">
                                <strong><?php echo htmlspecialchars($service['ten_nhiem_vu']); ?></strong>
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
                
                <a href="index.php" class="button btn-home"><i class="fas fa-home"></i> Trang Chủ</a>
                
                <a href="javascript:history.back()" class="button btn-back"><i class="fas fa-arrow-left"></i> Quay Lại</a>
                
                <?php 
                // Nút Đánh Giá (chỉ khi HOÀN THÀNH và CHƯA đánh giá)
                if (strtolower(trim($order['trang_thai'])) === 'đã hoàn thành' && !$is_rated && $order['caregiver_id']): 
                ?>
                    <a href="Danhgia.php?id=<?php echo $order['id_don_hang']; ?>" class="button btn-rate"><i class="fas fa-star"></i> Đánh Giá</a>
                <?php elseif (strtolower(trim($order['trang_thai'])) === 'đã hoàn thành' && $is_rated): ?>
                    <button class="button btn-rate" style="background-color: #4caf50; cursor: default;" disabled>
                        <i class="fas fa-check-circle"></i> Đã Đánh Giá
                    </button>
                <?php endif; ?>

                <?php 
                // Nút Hủy Đơn (chỉ khi ở trạng thái CHỜ XÁC NHẬN)
                if (strtolower(trim($order['trang_thai'])) === 'chờ xác nhận'): 
                ?>
                    <button class="button btn-cancel" onclick="confirmCancel(<?php echo $order['id_don_hang']; ?>)">
                        <i class="fas fa-times-circle"></i> Hủy Đơn Hàng
                    </button>
                    <form id="cancelForm" method="POST" action="ChiTietDonHang.php?id=<?php echo $order['id_don_hang']; ?>" style="display: none;">
                        <input type="hidden" name="action" value="cancel_order">
                        <input type="hidden" name="id_don_hang" value="<?php echo $order['id_don_hang']; ?>">
                    </form>
                <?php endif; ?>

                <?php 
                // Nút Chat với người chăm sóc (chỉ khi có người chăm sóc được gán)
                if ($order['caregiver_id']): 
                ?>
                    <a href="Chatkhachhang.php?caregiver_id=<?php echo $order['caregiver_id']; ?>" class="button btn-chat"><i class="fas fa-comment-dots"></i> Chat</a>
                <?php endif; ?>

            </div>
        <?php endif; ?>
    </div>
</body>
</html>
