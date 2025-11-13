<?php
session_start();

// Giả định config.php chứa các hằng số VNPAY
require_once 'config.php';

$conn = new mysqli("localhost", "root", "", "sanpham");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// 💥 FIX LỖI PARSE ERROR: Đã loại bỏ ký tự non-breaking space cuối dòng 9
$id_khach_hang_session = $_SESSION['id_khach_hang'] ?? 0; 
$user_info = null;

if ($id_khach_hang_session > 0) {
    // ✅ Truy vấn 3 cột địa chỉ từ bảng khach_hang
    $sql_select_user = "SELECT ten_khach_hang, so_dien_thoai, ten_duong, phuong_xa, tinh_thanh FROM khach_hang WHERE id_khach_hang = ?";
    $stmt_user = $conn->prepare($sql_select_user);
    
    if ($stmt_user) {
        $stmt_user->bind_param("i", $id_khach_hang_session);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        
        if ($result_user->num_rows > 0) {
            $user_info_raw = $result_user->fetch_assoc();
            
            // ✅ Nối 3 cột địa chỉ lại thành một chuỗi duy nhất
            $full_address = [];
            if (!empty($user_info_raw['ten_duong'])) $full_address[] = $user_info_raw['ten_duong'];
            if (!empty($user_info_raw['phuong_xa'])) $full_address[] = $user_info_raw['phuong_xa'];
            if (!empty($user_info_raw['tinh_thanh'])) $full_address[] = $user_info_raw['tinh_thanh'];
            
            $user_info = [
                'ten_khach_hang' => $user_info_raw['ten_khach_hang'],
                'so_dien_thoai' => $user_info_raw['so_dien_thoai'],
                'dia_chi' => implode(', ', $full_address) 
            ];
        }
        $stmt_user->close();
    }
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_booking'])) {
    
    // Lấy dữ liệu từ POST
    $id_nguoi_cham_soc = intval($_POST['id_nguoi_cham_soc'] ?? 0);
    $tong_tien         = floatval($_POST['tong_tien'] ?? 0);
    $ngay_bat_dau      = $_POST['ngay_bat_dau'] ?? null;  
    $ngay_ket_thuc     = $_POST['ngay_ket_thuc'] ?? null;  
    $gio_bat_dau       = $_POST['gio_bat_dau'] ?? null;  
    $gio_ket_thuc      = $_POST['gio_ket_thuc'] ?? null;  
    $phuong_thuc       = $_POST['phuong_thuc'] ?? 'Tiền mặt'; 

    // --- THU THẬP DỊCH VỤ (NHIỆM VỤ) ---
    $raw_services = $_POST['dich_vu'] ?? [];
    $selected_services = [];
    if (is_array($raw_services)) {
        foreach ($raw_services as $service) {
            $service = trim($service);
            if (!empty($service) && !in_array($service, $selected_services)) {
                $selected_services[] = $service;
            }
        }
    }
    // Gộp tất cả nhiệm vụ thành một chuỗi JSON duy nhất để lưu vào cột `ten_nhiem_vu` của bảng `don_hang`
    // 💥 CẢI TIẾN: Sử dụng JSON để lưu cấu trúc mảng nhiệm vụ rõ ràng hơn
    $ten_nhiem_vu_to_insert = json_encode($selected_services, JSON_UNESCAPED_UNICODE);
    
    // Lấy thông tin người đặt (đặt hộ)
    $ten_khach_hang_post = trim($_POST['ten_khach_hang'] ?? '');
    $so_dien_thoai_post  = trim($_POST['so_dien_thoai'] ?? '');
    $dia_chi_post        = trim($_POST['dia_chi'] ?? '');

    // Quyết định thông tin cuối cùng để lưu vào DB
    $id_khach_hang_to_insert = $id_khach_hang_session > 0 ? $id_khach_hang_session : NULL;
    $ten_to_insert = !empty($so_dien_thoai_post) ? $ten_khach_hang_post : ($user_info['ten_khach_hang'] ?? '');
    $sdt_to_insert = !empty($so_dien_thoai_post) ? $so_dien_thoai_post : ($user_info['so_dien_thoai'] ?? '');
    $dia_chi_to_insert = !empty($so_dien_thoai_post) ? $dia_chi_post : ($user_info['dia_chi'] ?? '');
    
    // Kiểm tra lỗi (giữ nguyên logic)
    if ($id_nguoi_cham_soc <= 0) $errors[] = "ID người chăm sóc không hợp lệ.";
    if ($tong_tien <= 0) $errors[] = "Tổng tiền không hợp lệ. Vui lòng chọn lại giờ.";
    if (!$ngay_bat_dau || !$ngay_ket_thuc) $errors[] = "Chưa chọn ngày.";
    if (!$gio_bat_dau || !$gio_ket_thuc) $errors[] = "Chưa chọn giờ.";
    if (empty($sdt_to_insert) || empty($ten_to_insert)) $errors[] = "Thiếu thông tin người đặt. Vui lòng đăng nhập hoặc điền thông tin đặt hộ.";
    if (empty($selected_services)) $errors[] = "Vui lòng nhập ít nhất một dịch vụ cụ thể.";

    if (empty($errors)) {
        
        // Chuyển đổi giờ AM/PM thành định dạng 24h (HH:mm:ss)
        $time_start_24h = date('H:i:s', strtotime($gio_bat_dau));
        $time_end_24h = date('H:i:s', strtotime($gio_ket_thuc));
        
        // Gộp ngày và giờ thành chuỗi DATETIME cho 2 cột DB: thoi_gian_bat_dau, thoi_gian_ket_thuc
        $start_datetime_full = $ngay_bat_dau . ' ' . $time_start_24h;
        $end_datetime_full = $ngay_ket_thuc . ' ' . $time_end_24h;
        
        $conn->begin_transaction();

        try {
            // 1. LƯU THÔNG TIN ĐƠN HÀNG VÀO BẢNG don_hang
            // ✅ Đã sửa: Bỏ bảng dich_vu_don_hang, thêm ten_nhiem_vu và trang_thai_nhiem_vu vào query
            $sql_don_hang = "INSERT INTO don_hang (
                                id_khach_hang, id_nguoi_cham_soc, tong_tien, dia_chi_giao_hang, 
                                ten_khach_hang, so_dien_thoai, trang_thai, 
                                thoi_gian_bat_dau, thoi_gian_ket_thuc, hinh_thuc_thanh_toan, 
                                ten_nhiem_vu, trang_thai_nhiem_vu
                             ) 
                             VALUES (
                                ?, ?, ?, ?, 
                                ?, ?, 'chờ xác nhận', 
                                ?, ?, ?, 
                                ?, 'chua_hoan_thanh'
                             )";
            
            $stmt_don_hang = $conn->prepare($sql_don_hang);

            // Cần 10 tham số: i (id_khach_hang), i (id_nguoi_cham_soc), d (tong_tien), s (7 tham số còn lại)
            $stmt_don_hang->bind_param("iidsssssss", 
                $id_khach_hang_to_insert, 
                $id_nguoi_cham_soc, 
                $tong_tien, 
                $dia_chi_to_insert, // ✅ Địa chỉ gộp 3 cột
                $ten_to_insert, 
                $sdt_to_insert, 
                $start_datetime_full, // thoi_gian_bat_dau (s)
                $end_datetime_full,   // thoi_gian_ket_thuc (s)
                $phuong_thuc,         // hinh_thuc_thanh_toan (s)
                $ten_nhiem_vu_to_insert // ten_nhiem_vu (s)
            );
            $stmt_don_hang->execute();
            $id_don_hang = $conn->insert_id;
            $stmt_don_hang->close();

            // *** KHÔNG CẦN LƯU VÀO dich_vu_don_hang NỮA ***

            $conn->commit();
            
            // XỬ LÝ CHUYỂN HƯỚNG THANH TOÁN (VNPAY)
            if ($phuong_thuc == 'vnpay') {
                // ... (Logic VNPAY giữ nguyên)
                $vnp_TxnRef = $id_don_hang; 
                $vnp_Amount = $tong_tien * 100; 
                $vnp_Locale = 'vn';
                $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
                $vnp_OrderInfo = 'Thanh toan don hang DICHVU#' . $id_don_hang;
                $vnp_OrderType = 'other';
                
                $inputData = array(
                    "vnp_Version" => "2.1.0",
                    "vnp_TmnCode" => VNP_TMN_CODE,
                    "vnp_Amount" => $vnp_Amount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => date('YmdHis'),
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnp_IpAddr,
                    "vnp_Locale" => $vnp_Locale,
                    "vnp_OrderInfo" => $vnp_OrderInfo,
                    "vnp_OrderType" => $vnp_OrderType,
                    "vnp_ReturnUrl" => VNP_RETURN_URL,
                    "vnp_TxnRef" => $vnp_TxnRef
                );
                
                ksort($inputData);
                
                $hashData = "";
                $query = "";
                
                foreach ($inputData as $key => $value) {
                    $hashData .= ($hashData ? '&' : '') . urlencode($key) . "=" . urlencode($value);
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }
                
                $query = trim($query, '&');
                $vnp_Url = VNP_URL . "?" . $query;

                if (VNP_HASH_SECRET != "") {
                    $vnpSecureHash = hash_hmac('sha512', $hashData, VNP_HASH_SECRET);
                    $vnp_Url .= '&vnp_SecureHash=' . $vnpSecureHash;
                }
                
                $conn->close();
                header('Location: ' . $vnp_Url);
                exit;

            } else {
                    // TH: THANH TOÁN TIỀN MẶT (cash)
                    $conn->close();
                    header("Location: Chitietdonhang.php?id=" . $id_don_hang); 
                    exit;
                }

            } catch (Exception $e) {
                $conn->rollback();
                $errors[] = "Lỗi giao dịch: " . $e->getMessage();
            }
        }
}

// === PHẦN HIỂN THỊ HTML/CSS/JS KHÔNG THAY ĐỔI ===
$id = 0;
// Lấy ID người chăm sóc từ GET hoặc POST
if (isset($_GET['id'])) $id = intval($_GET['id']);
elseif (isset($_POST['id_nguoi_cham_soc'])) $id = intval($_POST['id_nguoi_cham_soc']);

if ($id <= 0) {
    echo "<h2 style='text-align:center;color:red;'>ID người chăm sóc không hợp lệ hoặc không được cung cấp.</h2>";
    exit;
}
// Lấy thông tin người chăm sóc (Dựa vào bảng nguoi_cham_soc dùng cột id_cham_soc)
$stmt2 = $conn->prepare("SELECT * FROM nguoi_cham_soc WHERE id_cham_soc = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$res2 = $stmt2->get_result();
if ($res2->num_rows === 0) {
    echo "<h2 style='text-align:center;color:red;'>Không tìm thấy người chăm sóc này!</h2>";
    $stmt2->close();
    $conn->close();
    exit;
}
$row = $res2->fetch_assoc();
$stmt2->close();

function generateTimeOptions() {
    $options = '';
    for ($h = 0; $h < 24; $h++) {
        for ($m = 0; $m < 60; $m += 30) {
            $time_24 = sprintf("%02d:%02d", $h, $m);
            $time_ampm = date("g:i A", strtotime($time_24));
            $options .= "<option value=\"$time_ampm\">$time_ampm</option>";
        }
    }
    return $options;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đặt dịch vụ - <?php echo htmlspecialchars($row['ho_ten']); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
/* ----------------------------------- */
/* CSS (Giữ nguyên) */
/* ----------------------------------- */
* { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
body { background: #FFF9FA; color: #333; overflow-x: hidden; line-height: 1.6; } 

.container { 
    max-width: 1000px; 
    margin: 40px auto; 
    background: #fff; 
    border-radius: 16px; 
    padding: 40px; 
    box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
}
h1 { 
    text-align: center; 
    color: #FF6B81; 
    font-size: 32px;
    margin-bottom: 30px;
    font-weight: 800;
}
form label { 
    display: block; 
    margin: 15px 0 8px; 
    font-weight: 600; 
    color: #444;
}
.row { 
    display: flex; 
    gap: 20px; 
    margin-bottom: 20px;
    flex-wrap: wrap; 
}
.row > div {
    flex: 1;
    min-width: 250px;
}
.date-time-pair {
    display: flex;
    gap: 20px;
    width: 100%;
    margin-bottom: 20px;
}
.date-time-pair > div {
    flex: 1;
    min-width: 45%;
}

select, input:not(#tongTien), input#hoTen, input#diaChi, input#soDienThoai, input[type="date"] { 
    width: 100%; 
    padding: 12px; 
    height: 48px; 
    border: 1px solid #FFD8E0; 
    border-radius: 10px; 
    box-sizing: border-box; 
    font-size: 16px;
    transition: all 0.3s;
}
select:focus, input:focus {
    border-color: #FF6B81;
    box-shadow: 0 0 0 3px rgba(255, 107, 129, 0.15); 
    outline: none;
}
#tongTien {
    background: #fff;
    font-size: 20px;
    color: #FF6B81 !important; 
    font-weight: 700 !important;
    border: 1px solid #FFD8E0; 
}
.btn-row { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-top: 30px; 
}
.btn-confirm { 
    background: #FF6B81; 
    color: #fff; 
    border: none; 
    padding: 15px 30px; 
    border-radius: 10px; 
    font-weight: 700; 
    cursor: pointer;
    font-size: 18px;
    transition: background 0.3s;
}
.btn-confirm:hover { background: #E55B70; } 
.btn-back { 
    background: none; 
    border: 2px solid #FFD8E0; 
    padding: 10px 20px;
    border-radius: 10px;
    color: #444; 
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    transition: background 0.3s, border-color 0.3s;
}
.btn-back:hover { 
    background: #FFF0F3; 
    border-color: #FF6B81;
}
.summary { 
    background: #fff7f9; 
    padding: 25px; 
    border-radius: 12px; 
    margin-bottom: 30px; 
    box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
    border-left: 5px solid #ff6b81; 
}
.summary h3 {
    color: #333;
    margin-top: 0;
    margin-bottom: 15px;
    border-bottom: 1px dashed #FFD8E0; 
    padding-bottom: 10px;
    font-weight: 700;
}
.summary p strong {
    color: #ff6b81; 
}
.summary img {
    border-radius: 8px;
    object-fit: cover;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.error-box { 
    background: #FFF0F3; 
    border: 1px solid #FFB4C4; 
    color: #9B1C3C; 
    padding: 15px; 
    border-radius: 8px; 
    margin-bottom: 20px; 
    font-weight: 500;
}
/* ======================================= */
/* STYLE CHO INPUT DYNAMIC (HIỆN CÓ) */
/* ======================================= */
.btn-remove-service {
    background: #FF6B81;
    color: #fff;
    border: none;
    padding: 10px 15px;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    width: 48px;
    flex-shrink: 0;
    transition: background 0.3s;
}
.btn-remove-service:hover {
    background: #E55B70;
}
/* 💥 KHỐI CSS BỔ SUNG ĐỂ HOÀN THIỆN HIỂN THỊ NÚT XÓA */
.service-input-group button.btn-remove-service {
    /* Đảm bảo icon hiện giữa */
    display: flex; 
    align-items: center; 
    justify-content: center;
}
.service-input-group button.btn-remove-service:hover {
    background: #E55B70 !important; /* Quan trọng để override inline style khi hiển thị */
}
/* 💥 KẾT THÚC KHỐI CSS BỔ SUNG */
</style>
</head>
<body>

<div class="container">
    <h1> Đặt dịch vụ chăm sóc</h1>

    <?php
    if (!empty($errors)) {
        echo '<div class="error-box"><ul>';
        foreach ($errors as $er) echo '<li><i class="fas fa-exclamation-triangle"></i> ' . htmlspecialchars($er) . '</li>';
        echo '</ul></div>';
    }
    if (isset($_GET['booked'])) {
        echo '<div class="success-box"><i class="fas fa-check-circle"></i> Đặt dịch vụ thành công! Hệ thống đang chờ xác nhận.</div>';
    }
    ?>

    <div class="summary" style="display: flex; align-items: flex-start; gap: 30px;">
        <div style="flex: 2;">
            <h3>Thông tin người chăm sóc</h3>
            <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($row['ho_ten']); ?></p>
            <p><strong>Kinh nghiệm:</strong> <?php echo htmlspecialchars($row['kinh_nghiem']); ?></p>
            <p><strong>Đánh giá:</strong> <span style="color:#F7C513">⭐</span> <?php echo htmlspecialchars($row['danh_gia_tb']); ?>/5</p>
            <p><strong>Giá tiền/giờ:</strong> 
                <span style="color:#FF6B81; font-weight:700;">
                    <?php echo number_format($row['tong_tien_kiem_duoc'], 0, ',', '.'); ?> đ/giờ
                </span>
            </p>
        </div>
        <div style="flex: 1; text-align: center;">
            <?php if (!empty($row['hinh_anh'])): ?>
                <img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>" 
                    alt="Ảnh của <?php echo htmlspecialchars($row['ho_ten']); ?>" 
                    width="200" height="200">
            <?php else: ?>
                <img src="fontend/img/default-avatar.jpg" 
                    alt="Không có ảnh" 
                    width="200" height="200">
            <?php endif; ?>
        </div>
    </div>
    <form id="bookingForm" method="post">
        <input type="hidden" name="id_nguoi_cham_soc" value="<?php echo intval($row['id_cham_soc']); ?>">
        <input type="hidden" name="tong_tien" id="tong_tien_input">
        
        <input type="hidden" name="ngay_bat_dau" id="ngay_bat_dau_input">
        <input type="hidden" name="ngay_ket_thuc" id="ngay_ket_thuc_input">
        <input type="hidden" name="gio_bat_dau" id="gio_bat_dau_input">
        <input type="hidden" name="gio_ket_thuc" id="gio_ket_thuc_input">
        
        <input type="hidden" name="phuong_thuc" id="phuong_thuc_input">
        <input type="hidden" name="ten_khach_hang" id="ten_khach_hang_input">
        <input type="hidden" name="so_dien_thoai" id="so_dien_thoai_input">
        <input type="hidden" name="dia_chi" id="dia_chi_input"> 

        <label><i class="fas fa-list-alt"></i> Chọn dịch vụ/Nhiệm vụ cụ thể:</label>
        
        <div id="serviceInputs">
            <div class="service-input-group" id="group-1" style="margin-bottom: 15px;">
                <label for="dich_vu_1" style="font-weight: 500;">Nhiệm vụ 1:</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="dich_vu_1" name="dich_vu[]" placeholder="Ví dụ: Hỗ trợ tắm rửa, Nấu ăn theo chế độ" required
                        style="flex-grow: 1; width: 100%; padding: 12px; height: 48px; border: 1px solid #FFD8E0; border-radius: 10px; box-sizing: border-box; font-size: 16px;">
                    <button type="button" class="btn-remove-service" style="visibility: hidden; width: 48px; flex-shrink: 0; background: none; border: none; padding: 0;"><i class="fas fa-minus"></i></button>
                </div>
            </div>
        </div>
        <button type="button" id="addServiceBtn" style="background: #FF6B81; color: #fff; border: none; padding: 10px 15px; border-radius: 8px; font-weight: 600; cursor: pointer; margin-bottom: 20px; font-size: 15px;">
            <i class="fas fa-plus"></i> Thêm Nhiệm Vụ Khác
        </button>

        <label><i class="fas fa-calendar-alt"></i> Chọn thời gian dịch vụ:</label>
        
        <div class="date-time-pair">
            <div>
                <label for="startDate">Ngày bắt đầu:</label>
                <input type="date" id="startDate" required 
                    value="<?php echo htmlspecialchars($_POST['ngay_bat_dau'] ?? ''); ?>"> 
            </div>
            
            <div>
                <label for="startHour">Giờ bắt đầu:</label>
                <select id="startHour" required name="select_start_hour">
                    <option value="">Chọn giờ</option>
                    <?php 
                        $options_start = generateTimeOptions();
                        $selected_start_hour = $_POST['gio_bat_dau'] ?? '';
                        echo str_replace(
                            "value=\"{$selected_start_hour}\"", 
                            "value=\"{$selected_start_hour}\" selected", 
                            $options_start
                        );
                    ?>
                </select>
            </div>
        </div>

        <div class="date-time-pair">
            <div>
                <label for="endDate">Ngày kết thúc:</label>
                <input type="date" id="endDate" required
                    value="<?php echo htmlspecialchars($_POST['ngay_ket_thuc'] ?? ''); ?>">
            </div>

            <div>
                <label for="endHour">Giờ kết thúc:</label>
                <select id="endHour" required name="select_end_hour">
                    <option value="">Chọn giờ</option>
                    <?php 
                        $options_end = generateTimeOptions();
                        $selected_end_hour = $_POST['gio_ket_thuc'] ?? '';
                        echo str_replace(
                            "value=\"{$selected_end_hour}\"", 
                            "value=\"{$selected_end_hour}\" selected", 
                            $options_end
                        );
                    ?>
                </select>
            </div>
        </div>
        <hr style="border:0; border-top: 1px dashed #FFD8E0; margin: 25px 0;">

        <label><i class="fas fa-user-circle"></i> Hồ sơ đặt</label>
        <select id="profileSelect">
            <option value="own" <?php echo ($user_info) ? 'selected' : ''; ?>>
                Sử dụng hồ sơ của tôi <?php echo ($user_info) ? '('.htmlspecialchars($user_info['ten_khach_hang']).')' : '(Vui lòng đăng nhập)'; ?>
            </option>
            <option value="new" <?php echo (!$user_info) ? 'selected' : ''; ?>>Đặt hộ người khác</option>
        </select>

        <div id="customProfile" style="<?php echo (!$user_info) ? 'display:block;' : 'display:none;'; ?> margin-top:10px">
            <label for="hoTen">Họ và tên người nhận dịch vụ</label>
            <input type="text" id="hoTen" placeholder="Nhập họ tên">
            <label for="diaChi">Địa chỉ nhận dịch vụ</label>
            <input type="text" id="diaChi" placeholder="Nhập địa chỉ chi tiết">
            <label for="soDienThoai">Số điện thoại liên hệ</label>
            <input type="text" id="soDienThoai" placeholder="Nhập số điện thoại">
        </div>

        <div style="margin-top:25px" class="form-group">
            <label for="tongTien"><i class="fas fa-money-bill-wave"></i> Tổng tiền (ước tính)</label>
            <input type="text" id="tongTien" value="0 đ" readonly>
        </div>

        <div style="margin-top:12px" class="form-group">
            <label for="payment"><i class="far fa-credit-card"></i> Phương thức thanh toán</label>
            <select id="payment">
                <option value="Tiền mặt">Tiền mặt khi hoàn thành dịch vụ</option>
                <option value="vnpay">Thanh toán VNPAY (Thẻ/QR)</option>
            </select>
        </div>

        <div class="btn-row">
            <button type="submit" name="submit_booking" class="btn-confirm"><i class="fas fa-check-circle"></i> Xác nhận đặt dịch vụ</button>
            <button type="button" class="btn-back" onclick="window.history.back()"><i class="fas fa-arrow-left"></i> Quay lại</button>
        </div>
    </form>

    </div>

<footer>
    © 2025 Elder Care Connect | Mang yêu thương đến từng mái ấm 💖
</footer>


<script>
// Truyền thông tin PHP sang JS
const pricePerHour = <?php echo floatval($row['tong_tien_kiem_duoc']); ?>;

// Hàm chuyển đổi thời gian sang đối tượng Date để so sánh
function parseDateTime(dateStr, timeStr) {
    if (!dateStr || !timeStr) return null;
    
    // timeStr có dạng "H:i A" (ví dụ: "8:30 AM")
    const [time, ampm] = timeStr.split(' ');
    const [hourStr, minuteStr] = time.split(':');

    let hour = parseInt(hourStr);
    const minute = parseInt(minuteStr);

    if (ampm === "PM" && hour !== 12) {
        hour += 12;
    } else if (ampm === "AM" && hour === 12) {
        hour = 0; // 12:xx AM là 00:xx giờ
    }

    const dateTimeStr = `${dateStr}T${String(hour).padStart(2,'0')}:${String(minute).padStart(2,'0')}:00`;
    return new Date(dateTimeStr);
}


function calcTotal() {
    const startDateVal = document.getElementById("startDate").value;
    const endDateVal = document.getElementById("endDate").value;
    const startHourVal = document.getElementById("startHour").value;
    const endHourVal = document.getElementById("endHour").value;

    if (!startDateVal || !endDateVal || !startHourVal || !endHourVal) {
        document.getElementById("tongTien").value = "0 đ";
        return 0;
    }

    const start = parseDateTime(startDateVal, startHourVal);
    const end = parseDateTime(endDateVal, endHourVal);

    if (!start || !end) {
        document.getElementById("tongTien").value = "0 đ";
        return 0;
    }

    const diffMs = end - start;
    if (diffMs <= 0) {
        document.getElementById("tongTien").value = "Giờ kết thúc phải sau giờ bắt đầu";
        return 0;
    }

    const diffHours = diffMs / (1000 * 60 * 60);
    const total = diffHours * pricePerHour;
    document.getElementById("tongTien").value = Math.round(total).toLocaleString('vi-VN') + " đ";
    return total;
}

// Gắn sự kiện thay đổi cho tất cả các trường ngày giờ
document.querySelectorAll("#startDate, #endDate, #startHour, #endHour")
    .forEach(el => el.addEventListener("change", calcTotal));


document.getElementById("profileSelect").addEventListener("change", function(){
    document.getElementById("customProfile").style.display =
    this.value === "new" ? "block" : "none";
});

// ===========================================
// 💥 KHỐI JS MỚI (THAY THẾ TOÀN BỘ LOGIC INPUT DYNAMIC CŨ)
// ===========================================
let serviceCount = 1;

/**
 * Hàm quản lý trạng thái hiển thị nút Xóa
 * - Ẩn nút xóa nếu chỉ còn 1 nhóm nhiệm vụ
 * - Hiện nút xóa nếu có 2 nhóm nhiệm vụ trở lên
 */
function updateRemoveButtonVisibility() {
    const allGroups = document.querySelectorAll('#serviceInputs .service-input-group');
    const isMultiple = allGroups.length > 1;

    allGroups.forEach(group => {
        const removeBtn = group.querySelector('.btn-remove-service');
        if (removeBtn) {
            if (isMultiple) {
                // Hiện nút xóa
                removeBtn.style.visibility = 'visible';
                removeBtn.style.background = '#FF6B81'; 
                removeBtn.style.border = 'none';
                removeBtn.style.padding = '10px 15px';
            } else {
                // Ẩn nút xóa
                removeBtn.style.visibility = 'hidden';
                removeBtn.style.background = 'none';
                removeBtn.style.border = 'none';
                removeBtn.style.padding = '0';
            }
        }
    });
}


/**
 * Cập nhật lại số thứ tự cho nhãn (Label)
 */
function updateServiceLabels() {
    const groups = document.querySelectorAll('#serviceInputs .service-input-group');
    groups.forEach((group, index) => {
        const label = group.querySelector('label');
        if (label) {
            label.textContent = `Nhiệm vụ ${index + 1}:`;
        }
    });
    serviceCount = groups.length; 
    updateRemoveButtonVisibility(); // Gọi sau khi thay đổi số lượng
}


/**
 * Tạo và chèn một ô nhập nhiệm vụ mới
 */
function createServiceInput() {
    serviceCount++;
    const container = document.getElementById('serviceInputs');
    
    const divGroup = document.createElement('div');
    divGroup.className = 'service-input-group';
    divGroup.style.marginBottom = '15px';
    divGroup.id = 'group-' + serviceCount;
    
    const label = document.createElement('label');
    label.htmlFor = 'dich_vu_' + serviceCount;
    label.textContent = `Nhiệm vụ ${serviceCount}:`;
    label.style.fontWeight = '500';

    const inputWrapper = document.createElement('div');
    inputWrapper.style.display = 'flex';
    inputWrapper.style.gap = '10px';

    const input = document.createElement('input');
    input.type = 'text';
    input.id = 'dich_vu_' + serviceCount;
    input.name = 'dich_vu[]'; // Quan trọng: PHP nhận mảng
    input.placeholder = 'Nhập tên nhiệm vụ (Ví dụ: Đưa đi khám bệnh)';
    input.required = true;
    input.style.cssText = 'flex-grow: 1; width: 100%; padding: 12px; height: 48px; border: 1px solid #FFD8E0; border-radius: 10px; box-sizing: border-box; font-size: 16px;';
    
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'btn-remove-service';
    removeBtn.innerHTML = '<i class="fas fa-minus"></i>';
    removeBtn.title = 'Xóa nhiệm vụ';
    // Đảm bảo CSS ban đầu là hiện, sau đó hàm updateRemoveButtonVisibility sẽ quản lý
    removeBtn.style.cssText = 'width: 48px; flex-shrink: 0; background: #FF6B81; color: #fff; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: background 0.3s;';
    
    removeBtn.onclick = function() {
        if (container.childElementCount > 1) {
            container.removeChild(divGroup);
            updateServiceLabels();
        } else {
             alert("Phải có ít nhất một Nhiệm vụ cụ thể để đặt dịch vụ.");
        }
    };
    
    inputWrapper.appendChild(input);
    inputWrapper.appendChild(removeBtn);
    
    divGroup.appendChild(label);
    divGroup.appendChild(inputWrapper);
    
    container.appendChild(divGroup);
    
    updateServiceLabels();
}

// Bắt sự kiện cho nút thêm
document.getElementById("addServiceBtn").addEventListener("click", createServiceInput);


// 💥 KHỐI CODE BỔ SUNG: Khởi tạo và xử lý nút xóa cho item mặc định (group-1)
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('serviceInputs');
    
    // Gán sự kiện xóa cho nút xóa của item mặc định (group-1)
    const firstGroup = document.getElementById('group-1');
    const firstRemoveBtn = firstGroup ? firstGroup.querySelector('.btn-remove-service') : null;
    
    if (firstRemoveBtn) {
        firstRemoveBtn.onclick = function() {
            if (container.childElementCount > 1) {
                container.removeChild(firstGroup);
                updateServiceLabels();
            } else {
                 alert("Phải có ít nhất một Nhiệm vụ cụ thể để đặt dịch vụ.");
            }
        };
    }
    
    // Khởi tạo trạng thái hiển thị của nút xóa ngay khi tải trang
    updateRemoveButtonVisibility(); 
});
// ===========================================


document.getElementById("bookingForm").addEventListener("submit", function(e){
    const total = Math.round(calcTotal());
    
    const serviceInputs = document.querySelectorAll('#serviceInputs input[name="dich_vu[]"]');
    let hasValidService = false;
    serviceInputs.forEach(input => {
        if (input.value.trim() !== '') {
            hasValidService = true;
        }
    });

    if (!hasValidService) {
        alert("Vui lòng nhập ít nhất một Nhiệm vụ cụ thể để đặt dịch vụ.");
        e.preventDefault();
        updateRemoveButtonVisibility();
        return;
    }


    if (total <= 0) {
        alert("Vui lòng chọn ngày/giờ hợp lệ để tính tổng tiền.");
        e.preventDefault();
        return;
    }
    
    const startDateVal = document.getElementById("startDate").value;
    const endDateVal = document.getElementById("endDate").value;
    const startHourVal = document.getElementById("startHour").value; 
    const endHourVal = document.getElementById("endHour").value; 

    // Điền vào các trường hidden
    document.getElementById("tong_tien_input").value = total;
    document.getElementById("ngay_bat_dau_input").value = startDateVal;
    document.getElementById("ngay_ket_thuc_input").value = endDateVal;
    document.getElementById("gio_bat_dau_input").value = startHourVal;
    document.getElementById("gio_ket_thuc_input").value = endHourVal;
    
    // Lưu giá trị thanh toán để PHP có thể phân biệt (Tiền mặt hoặc vnpay)
    document.getElementById("phuong_thuc_input").value = document.getElementById("payment").value;
    
    // Xử lý thông tin người nhận dịch vụ
    if (document.getElementById("profileSelect").value === "new") {
        const ten = document.getElementById("hoTen").value.trim();
        const diachi = document.getElementById("diaChi").value.trim();
        const sdt = document.getElementById("soDienThoai").value.trim();
        if (!ten || !diachi || !sdt) {
            alert("Vui lòng nhập đầy đủ Họ tên, Địa chỉ và Số điện thoại của người được đặt hộ.");
            e.preventDefault();
            return;
        }
        document.getElementById("ten_khach_hang_input").value = ten;
        document.getElementById("dia_chi_input").value = diachi;
        document.getElementById("so_dien_thoai_input").value = sdt;
    } else {
        document.getElementById("ten_khach_hang_input").value = "";
        document.getElementById("dia_chi_input").value = "";
        document.getElementById("so_dien_thoai_input").value = "";
    }
});
</script>
</body>
</html>

<?php
if (isset($conn) && $conn) {
    $conn->close();
}
?>
