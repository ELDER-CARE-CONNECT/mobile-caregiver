<<<<<<< HEAD
<<<<<<< HEAD

=======
=======
>>>>>>> Phong
<?php
session_start();

$conn = new mysqli("localhost", "root", "", "sanpham");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy thông tin khách hàng đang đăng nhập (nếu có)
$id_khach_hang_session = $_SESSION['id_khach_hang'] ?? 0; 
$user_info = null;

if ($id_khach_hang_session > 0) {
    $stmt_user = $conn->prepare("SELECT ten_khach_hang, so_dien_thoai, dia_chi FROM khach_hang WHERE id_khach_hang = ?");
    if ($stmt_user) {
        $stmt_user->bind_param("i", $id_khach_hang_session);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        if ($result_user->num_rows > 0) {
            $user_info = $result_user->fetch_assoc();
        }
        $stmt_user->close();
    }
}

$errors = []; // Khởi tạo $errors ở đây để dùng chung

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_booking'])) {
    // Lấy dữ liệu từ POST
    $id_cham_soc    = intval($_POST['id_cham_soc'] ?? 0);
    $tong_tien      = floatval($_POST['tong_tien'] ?? 0);
    $ngay_bat_dau   = $_POST['ngay_bat_dau'] ?? null;  
    $ngay_ket_thuc  = $_POST['ngay_ket_thuc'] ?? null;  
    $gio_bat_dau    = $_POST['gio_bat_dau'] ?? null;  
    $gio_ket_thuc   = $_POST['gio_ket_thuc'] ?? null;  
    $phuong_thuc    = $_POST['phuong_thuc'] ?? 'cash';
    
<<<<<<< HEAD
    // --- THU THẬP DỊCH VỤ TỪ INPUT DYNAMIC ---
    $raw_services = $_POST['dich_vu'] ?? [];
    $selected_services = [];
    
    if (is_array($raw_services)) {
        // Dùng array_unique để loại bỏ các nhiệm vụ trùng lặp, trim để loại bỏ khoảng trắng
        foreach ($raw_services as $service) {
            $service = trim($service);
            if (!empty($service) && !in_array($service, $selected_services)) {
                $selected_services[] = $service;
            }
        }
    }
    // --- KẾT THÚC THU THẬP DỊCH VỤ ---
=======
    // Thu thập dịch vụ từ TẤT CẢ các selects đã chọn (dùng tên chung)
    $selected_services = [];
    
    // Lấy giá trị đã chọn từ mỗi select
    $service_1 = trim($_POST['dich_vu1'] ?? '');
    $service_2 = trim($_POST['dich_vu2'] ?? '');
    $service_3 = trim($_POST['dich_vu3'] ?? '');
    
    // Chỉ thêm vào mảng nếu giá trị KHÔNG rỗng (đã chọn)
    if (!empty($service_1)) $selected_services[] = $service_1;
    if (!empty($service_2)) $selected_services[] = $service_2;
    if (!empty($service_3)) $selected_services[] = $service_3;
>>>>>>> Phong

    // Lấy thông tin người đặt (có thể là đặt hộ)
    $ten_khach_hang_post = trim($_POST['ten_khach_hang'] ?? '');
    $so_dien_thoai_post  = trim($_POST['so_dien_thoai'] ?? '');
    $dia_chi_post        = trim($_POST['dia_chi'] ?? '');

    // Quyết định thông tin cuối cùng để lưu vào DB
    $id_khach_hang_to_insert = $id_khach_hang_session > 0 ? $id_khach_hang_session : NULL;
    // Nếu đặt hộ, dùng thông tin đặt hộ. Nếu không, dùng thông tin session.
    $ten_to_insert = !empty($so_dien_thoai_post) ? $ten_khach_hang_post : ($user_info['ten_khach_hang'] ?? '');
    $sdt_to_insert = !empty($so_dien_thoai_post) ? $so_dien_thoai_post : ($user_info['so_dien_thoai'] ?? '');
    $dia_chi_to_insert = !empty($so_dien_thoai_post) ? $dia_chi_post : ($user_info['dia_chi'] ?? '');

    // Kiểm tra lỗi
    if ($id_cham_soc <= 0) $errors[] = "ID người chăm sóc không hợp lệ.";
    if ($tong_tien <= 0) $errors[] = "Tổng tiền không hợp lệ. Vui lòng chọn lại giờ.";
    if (!$ngay_bat_dau || !$ngay_ket_thuc) $errors[] = "Chưa chọn ngày.";
    if (!$gio_bat_dau || !$gio_ket_thuc) $errors[] = "Chưa chọn giờ.";
    if (empty($sdt_to_insert) || empty($ten_to_insert)) $errors[] = "Thiếu thông tin người đặt. Vui lòng đăng nhập hoặc điền thông tin đặt hộ.";
    
    // Kiểm tra dịch vụ đã chọn 
    if (empty($selected_services)) {
<<<<<<< HEAD
        $errors[] = "Vui lòng nhập ít nhất một dịch vụ cụ thể."; // Đã cập nhật thông báo lỗi
    }

    // ===================================================================
    // KHỐI LƯU DATABASE
=======
        $errors[] = "Vui lòng chọn ít nhất một dịch vụ cụ thể.";
    }

    // ===================================================================
    // KHỐI LƯU DATABASE (Đã sửa lỗi cú pháp try...catch và logic lưu dịch vụ)
>>>>>>> Phong
    // ===================================================================
    if (empty($errors)) {
        
        $conn->begin_transaction();

        try {
<<<<<<< HEAD
            // Chuyển đổi giờ từ định dạng 'H:i A' sang 24h và ghép với ngày
            $datetime_start_str = $ngay_bat_dau . ' ' . date("H:i:s", strtotime($gio_bat_dau));
            $datetime_end_str = $ngay_ket_thuc . ' ' . date("H:i:s", strtotime($gio_ket_thuc));

            // 1. TẠO ĐƠN HÀNG CHÍNH (ĐÃ THÊM THỜI GIAN VÀO BẢNG don_hang)
            $sql1 = "INSERT INTO don_hang
                (id_khach_hang, id_cham_soc, id_danh_gia, ngay_dat, tong_tien, dia_chi_giao_hang, ten_khach_hang, so_dien_thoai, trang_thai, thoi_gian_bat_dau, thoi_gian_ket_thuc, hinh_thuc_thanh_toan)
                VALUES (?, ?, 0, CURDATE(), ?, ?, ?, ?, 'chờ xác nhận', ?, ?, ?)";

                $stmt1 = $conn->prepare($sql1);
                if (!$stmt1) {
                    throw new Exception("Lỗi prepare (don_hang): " . $conn->error);
        }
            $stmt1->bind_param(
                "iidssssss", 
=======
            // 1. TẠO ĐƠN HÀNG CHÍNH
            $sql1 = "INSERT INTO don_hang 
                     (id_khach_hang, id_cham_soc, id_danh_gia, ngay_dat, tong_tien, dia_chi_giao_hang, ten_khach_hang, so_dien_thoai, trang_thai)
                     VALUES (?, ?, 0, CURDATE(), ?, ?, ?, ?, 'chờ xác nhận')";
            
            $stmt1 = $conn->prepare($sql1);
            if (!$stmt1) {
                throw new Exception("Lỗi prepare (don_hang): " . $conn->error);
            }
            $stmt1->bind_param(
                "iidsss", 
>>>>>>> Phong
                $id_khach_hang_to_insert, 
                $id_cham_soc, 
                $tong_tien, 
                $dia_chi_to_insert, 
                $ten_to_insert, 
<<<<<<< HEAD
                $sdt_to_insert,
                $datetime_start_str, 
                $datetime_end_str,
                $phuong_thuc  
=======
                $sdt_to_insert
>>>>>>> Phong
            );
            
            if (!$stmt1->execute()) {
                throw new Exception("Lỗi khi tạo đơn hàng chính: " . $stmt1->error);
            }
            $stmt1->close();

            $id_don_hang = $conn->insert_id;
            
            if ($id_don_hang > 0) {
<<<<<<< HEAD
                // 2. LƯU CHI TIẾT DỊCH VỤ 
                
=======
                // 2. LƯU CHI TIẾT DỊCH VỤ (Đã hợp nhất logic)
                
                // Chuyển đổi giờ từ định dạng 'H:i A' sang 24h và ghép với ngày
                // Lưu ý: date() và strtotime() cần thiết để chuyển đổi giờ từ "H:i A" của form sang "H:i:s"
                $datetime_start_str = $ngay_bat_dau . ' ' . date("H:i:s", strtotime($gio_bat_dau));
                $datetime_end_str = $ngay_ket_thuc . ' ' . date("H:i:s", strtotime($gio_ket_thuc));

>>>>>>> Phong
                $sql2 = "INSERT INTO dich_vu_don_hang 
                         (id_don_hang, ten_nhiem_vu, thoi_gian_bat_dau, thoi_gian_ket_thuc)
                         VALUES (?, ?, ?, ?)";
                
                $stmt2 = $conn->prepare($sql2);
                if (!$stmt2) {
                    throw new Exception("Lỗi prepare (dich_vu_don_hang): " . $conn->error);
                }

                // Lặp qua từng dịch vụ đã chọn (từ mảng gộp $selected_services)
                foreach ($selected_services as $service_name) {
                    // $service_name đã được trim và kiểm tra không rỗng ở trên
                    // BIND $id_don_hang (ID đơn hàng vừa tạo)
                    $stmt2->bind_param("isss", $id_don_hang, $service_name, $datetime_start_str, $datetime_end_str);

                    if (!$stmt2->execute()) {
                        throw new Exception("Lỗi khi lưu chi tiết dịch vụ: " . $stmt2->error);
                    }
                }
                
                $stmt2->close();
            }

            $conn->commit();
            
            $conn->close();
            // Điều hướng về trang chi tiết đơn hàng vừa tạo (dùng ID đơn hàng vừa tạo)
<<<<<<< HEAD
            header("Location: Chitietdonhang.php?id=" . $id_don_hang);
=======
            header("Location: Chitietdonhang.php"); // Quay về trang lịch sử để xem chi tiết đơn mới nhất
>>>>>>> Phong
            exit;

        } catch (Exception $e) { // Cú pháp catch đúng
            $conn->rollback();
            // Lưu lỗi vào mảng $errors để hiển thị trên form
            $errors[] = "Lỗi giao dịch: " . $e->getMessage();
        }
    }
}

// Lấy ID người chăm sóc để hiển thị trang
$id = 0;
if (isset($_GET['id'])) $id = intval($_GET['id']);
elseif (isset($_POST['id_cham_soc'])) $id = intval($_POST['id_cham_soc']);

if ($id <= 0) {
    echo "<h2 style='text-align:center;color:red;'>ID người chăm sóc không hợp lệ hoặc không được cung cấp.</h2>";
    exit;
}
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

// Hàm tạo các option cho giờ (từ 1:00 AM đến 11:30 PM)
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
/* ======================================= */
/* CÁC STYLE CHUNG */
/* ======================================= */
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
/* CHỈNH SỬA: Căn chỉnh lại row cho Ngày/Giờ */
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
/* Tạo cặp Ngày và Giờ nằm cạnh nhau */
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
<<<<<<< HEAD
/* STYLE CHO INPUT DYNAMIC */
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
=======
/* STYLE CHO ACCORDION (KHUNG THU GỌN) */
/* ======================================= */
.accordion-container {
    margin-bottom: 20px;
}
.accordion-item {
    border: 1px solid #FFD8E0;
    border-radius: 10px;
    margin-bottom: 10px;
    overflow: hidden;
}
.accordion-header {
    background-color: #FFF0F3;
    color: #FF6B81;
    cursor: pointer;
    padding: 15px 20px;
    width: 100%;
    border: none;
    text-align: left;
    outline: none;
    font-size: 16px;
    font-weight: 600;
    transition: background-color 0.3s;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.accordion-header:hover {
    background-color: #FFE6EB;
}
.accordion-header .fas {
    transition: transform 0.3s ease;
}
.accordion-header.active .fas {
    transform: rotate(180deg);
}
.service-select-container {
    display: flex;
    flex-direction: column;
    gap: 15px;
    width: 100%; /* Đổi chiều rộng thành 100% để hiển thị tốt hơn */
}

.service-select label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
}

.service-select select {
    width: 100%;
    padding: 8px 12px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 6px;
    background-color: #fff;
    cursor: pointer;
}

>>>>>>> Phong
</style>
</head>
<body>

<div class="container">
    <h1> Đặt dịch vụ chăm sóc</h1>

    <?php
    // Hiển thị lỗi (nếu có)
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
        <input type="hidden" name="id_cham_soc" value="<?php echo intval($row['id_cham_soc']); ?>">
        <input type="hidden" name="tong_tien" id="tong_tien_input">
        <input type="hidden" name="ngay_bat_dau" id="ngay_bat_dau_input">
        <input type="hidden" name="ngay_ket_thuc" id="ngay_ket_thuc_input">
        <input type="hidden" name="gio_bat_dau" id="gio_bat_dau_input">
        <input type="hidden" name="gio_ket_thuc" id="gio_ket_thuc_input">
        <input type="hidden" name="phuong_thuc" id="phuong_thuc_input">
        <input type="hidden" name="ten_khach_hang" id="ten_khach_hang_input">
        <input type="hidden" name="so_dien_thoai" id="so_dien_thoai_input">
        <input type="hidden" name="dia_chi" id="dia_chi_input">

<<<<<<< HEAD
        <label><i class="fas fa-list-alt"></i> Chọn dịch vụ/Nhiệm vụ cụ thể:</label>
        
        <div id="serviceInputs">
            <div class="service-input-group" id="group-1" style="margin-bottom: 15px;">
                <label for="dich_vu_1" style="font-weight: 500;">Nhiệm vụ 1:</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="dich_vu_1" name="dich_vu[]" placeholder="Ví dụ: Hỗ trợ tắm rửa, Nấu ăn theo chế độ" required
                        style="flex-grow: 1; width: 100%; padding: 12px; height: 48px; border: 1px solid #FFD8E0; border-radius: 10px; box-sizing: border-box; font-size: 16px;">
                    <button type="button" class="btn-remove-service" style="visibility: hidden; width: 48px; flex-shrink: 0; background: none; border: none; padding: 0;"></button>
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
                // Thay thế giá trị đã chọn nếu tồn tại trong POST
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
                // Thay thế giá trị đã chọn nếu tồn tại trong POST
                echo str_replace(
                    "value=\"{$selected_end_hour}\"", 
                    "value=\"{$selected_end_hour}\" selected", 
                    $options_end
                );
            ?>
        </select>
    </div>
</div>
=======
        <label><i class="fas fa-list-alt"></i> Chọn dịch vụ cụ thể:</label>
        
        <div class="accordion-container">
            
            <div class="service-select-container">
                <div class="service-select">
                    <label>1. Chăm sóc và Y tế cơ bản:</label>
                    <select name="dich_vu1">
                        <option value="">Chọn dịch vụ cụ thể</option>
                        <option value="Chăm sóc người già">Chăm sóc người già</option>
                        <option value="Chăm sóc người bệnh">Chăm sóc người bệnh</option>
                        <option value="Hỗ trợ uống thuốc">Hỗ trợ uống thuốc</option>
                        <option value="Đo huyết áp/đường huyết cơ bản">Đo huyết áp/đường huyết cơ bản</option>
                        <option value="Theo dõi sức khỏe và báo cáo">Theo dõi sức khỏe và báo cáo</option>
                    </select>
                </div>

                <div class="service-select">
                    <label>2. Việc nhà và Dinh dưỡng:</label>
                    <select name="dich_vu2">
                        <option value="">Chọn dịch vụ cụ thể</option>
                        <option value="Nấu ăn cho người già">Nấu ăn theo chế độ</option>
                        <option value="Dọn dẹp nhà cửa">Dọn dẹp khu vực sinh hoạt</option>
                        <option value="Giặt giũ và ủi đồ">Giặt giũ và ủi đồ cá nhân</option>
                        <option value="Đi chợ/Mua sắm">Đi chợ/Mua sắm thực phẩm</option>
                        <option value="Rửa chén bát">Rửa chén bát</option>
                    </select>
                </div>

                <div class="service-select">
                    <label>3. Hỗ trợ Cá nhân và Tinh thần:</label>
                    <select name="dich_vu3">
                        <option value="">Chọn dịch vụ cụ thể</option>
                        <option value="Hỗ trợ tắm rửa">Hỗ trợ tắm rửa/vệ sinh cá nhân</option>
                        <option value="Hỗ trợ đi lại">Hỗ trợ đi lại/tập vật lý trị liệu</option>
                        <option value="Đi dạo/Vận động nhẹ">Đi dạo/Vận động nhẹ</option>
                        <option value="Xoa bóp/Massage cơ bản">Xoa bóp/Massage cơ bản</option>
                        <option value="Trò chuyện/Giải trí">Trò chuyện/Hỗ trợ tinh thần</option>
                    </select>
                </div>
            </div>

        </div>
        <label><i class="fas fa-calendar-alt"></i> Chọn thời gian dịch vụ:</label>
        
        <div class="date-time-pair">
            <div>
                <label for="startDate">Ngày bắt đầu:</label>
                <input type="date" id="startDate" required> 
            </div>
            
            <div>
                <label for="startHour">Giờ bắt đầu:</label>
                <select id="startHour" required>
                    <option value="">Chọn giờ</option>
                    <?php echo generateTimeOptions(); ?>
                </select>
            </div>
        </div>
        
        <div class="date-time-pair">
            <div>
                <label for="endDate">Ngày kết thúc:</label>
                <input type="date" id="endDate" required>
            </div>

            <div>
                <label for="endHour">Giờ kết thúc:</label>
                <select id="endHour" required>
                    <option value="">Chọn giờ</option>
                    <?php echo generateTimeOptions(); ?>
                </select>
            </div>
        </div>
        
>>>>>>> Phong
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
                <option value="cash">Tiền mặt khi hoàn thành dịch vụ</option>
                <option value="momo">Momo (Thanh toán trước)</option>
            </select>
        </div>

        <div class="btn-row">
            <button type="submit" name="submit_booking" class="btn-confirm"><i class="fas fa-check-circle"></i> Xác nhận đặt dịch vụ</button>
            <button type="button" class="btn-back" onclick="window.history.back()"><i class="fas fa-arrow-left"></i> Quay lại</button>
        </div>
    </form>

    <div id="qrBox" style="display:none;">
        <h3>Quét mã để thanh toán qua Momo 💖</h3>
        <img id="qrImage" src="" alt="Momo QR Code">
        <p><strong>Số tiền:</strong> <span id="qrAmount" style="color:#FF6B81;"></span></p>
        <p><strong>Nội dung:</strong> Thanh toán dịch vụ chăm sóc cho <?php echo htmlspecialchars($row['ho_ten']); ?></p>
    </div>
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

<<<<<<< HEAD
// ===========================================
// LOGIC XỬ LÝ INPUT DYNAMIC (MỚI)
// ===========================================
// Biến đếm để tạo ID/Label cho input. Bắt đầu từ 1 vì input đầu tiên đã có sẵn.
let serviceCount = 1;

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
    // Dùng style inline để đảm bảo giao diện thống nhất với các input khác
    input.style.cssText = 'flex-grow: 1; width: 100%; padding: 12px; height: 48px; border: 1px solid #FFD8E0; border-radius: 10px; box-sizing: border-box; font-size: 16px;';
    
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'btn-remove-service';
    removeBtn.innerHTML = '<i class="fas fa-minus"></i>';
    removeBtn.title = 'Xóa nhiệm vụ';
    // Dùng style inline để đảm bảo giao diện thống nhất với các nút khác
    removeBtn.style.cssText = 'width: 48px; flex-shrink: 0; background: #FF6B81; color: #fff; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: background 0.3s;';
    
    removeBtn.onclick = function() {
        container.removeChild(divGroup);
        // Sau khi xóa, cập nhật lại số thứ tự nhiệm vụ
        updateServiceLabels();
    };
    
    inputWrapper.appendChild(input);
    inputWrapper.appendChild(removeBtn);
    
    divGroup.appendChild(label);
    divGroup.appendChild(inputWrapper);
    
    container.appendChild(divGroup);
    
    // Cập nhật lại nhãn sau khi thêm
    updateServiceLabels();
}

function updateServiceLabels() {
    // Cập nhật lại nhãn "Nhiệm vụ X" sau khi thêm/xóa
    const groups = document.querySelectorAll('#serviceInputs .service-input-group');
    groups.forEach((group, index) => {
        const label = group.querySelector('label');
        if (label) {
            label.textContent = `Nhiệm vụ ${index + 1}:`;
        }
    });
    serviceCount = groups.length; // Đặt lại biến đếm theo số lượng hiện có
}

// Bắt sự kiện cho nút thêm
document.getElementById("addServiceBtn").addEventListener("click", createServiceInput);
// ===========================================
// KẾT THÚC LOGIC INPUT DYNAMIC
// ===========================================

=======
>>>>>>> Phong

document.getElementById("bookingForm").addEventListener("submit", function(e){
    const total = Math.round(calcTotal());
    
<<<<<<< HEAD
    // --- LOGIC KIỂM TRA DỊCH VỤ MỚI ---
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
        return;
    }
    // --- KẾT THÚC LOGIC KIỂM TRA DỊCH VỤ MỚI ---
=======
    // Kiểm tra đã chọn ít nhất 1 dịch vụ chưa
    const selects = ['dich_vu1', 'dich_vu2', 'dich_vu3'];
      let hasService = false;
      for (let selName of selects) {
          const sel = document.querySelector(`select[name="${selName}"]`);
          if (sel && sel.value.trim() !== '') {
              hasService = true;
              break;
          }
      }
    if (!hasService) {
        alert("Vui lòng chọn ít nhất một dịch vụ cụ thể.");
        e.preventDefault();
        return;
    }
>>>>>>> Phong


    if (total <= 0) {
        alert("Vui lòng chọn ngày/giờ hợp lệ để tính tổng tiền.");
        e.preventDefault();
        return;
    }
    
    // Lấy các giá trị ngày/giờ
    const startDateVal = document.getElementById("startDate").value;
    const endDateVal = document.getElementById("endDate").value;
    const startHourVal = document.getElementById("startHour").value; // dạng "8:30 AM"
<<<<<<< HEAD
    const endHourVal = document.getElementById("endHour").value; // dạng "4:00 PM"
=======
    const endHourVal = document.getElementById("endHour").value;      // dạng "4:00 PM"
>>>>>>> Phong

    // Điền vào các trường hidden
    document.getElementById("tong_tien_input").value = total;
    document.getElementById("ngay_bat_dau_input").value = startDateVal;
    document.getElementById("ngay_ket_thuc_input").value = endDateVal;
<<<<<<< HEAD

=======
    
>>>>>>> Phong
    // Gửi đi giờ đầy đủ (dạng "8:30 AM")
    document.getElementById("gio_bat_dau_input").value = startHourVal;
    document.getElementById("gio_ket_thuc_input").value = endHourVal;
    document.getElementById("phuong_thuc_input").value = document.getElementById("payment").value;
<<<<<<< HEAD
    
    // Xử lý thông tin người nhận dịch vụ (Đã có logic Địa chỉ)
=======

>>>>>>> Phong
    if (document.getElementById("profileSelect").value === "new") {
        // Nếu là "Đặt hộ"
        const ten = document.getElementById("hoTen").value.trim();
        const diachi = document.getElementById("diaChi").value.trim();
        const sdt = document.getElementById("soDienThoai").value.trim();
<<<<<<< HEAD
        if (!ten || !diachi || !sdt) {
            alert("Vui lòng nhập đầy đủ Họ tên, Địa chỉ và Số điện thoại của người được đặt hộ.");
=======
        if (!ten || !sdt) {
            alert("Vui lòng nhập họ tên và số điện thoại của người được đặt hộ.");
>>>>>>> Phong
            e.preventDefault();
            return;
        }
        document.getElementById("ten_khach_hang_input").value = ten;
        document.getElementById("dia_chi_input").value = diachi;
        document.getElementById("so_dien_thoai_input").value = sdt;
    } else {
        // Nếu là "Sử dụng hồ sơ của tôi"
<<<<<<< HEAD
        // Gửi các trường rỗng để PHP dùng thông tin session
=======
        // Gửi SĐT rỗng để PHP biết và dùng thông tin session
>>>>>>> Phong
        document.getElementById("ten_khach_hang_input").value = "";
        document.getElementById("dia_chi_input").value = "";
        document.getElementById("so_dien_thoai_input").value = "";
    }

    // Xử lý Momo (giữ nguyên)
    if (document.getElementById("payment").value === "momo") {
        e.preventDefault();
        const amountText = total.toLocaleString('vi-VN') + " đ";
        // Thêm data tốt hơn
        const qrData = `2|99|0${total}|<?php echo $row['ho_ten']; ?>|0|0|0|ElderCareConnect`; 
        const qrLink = `https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=${encodeURIComponent(qrData)}`;
        
        document.getElementById("qrBox").style.display = "block";
        document.getElementById("qrImage").src = qrLink;
        document.getElementById("qrAmount").textContent = amountText;
        window.scrollTo({top: document.getElementById("qrBox").offsetTop, behavior: 'smooth'});
        
        alert("Vui lòng quét mã Momo để thanh toán. Sau khi thanh toán thành công, bạn cần gửi lại đơn hàng.");
        
        return; 
    }
});
</script>
</body>
</html>

<?php
// Đóng kết nối cuối file
if (isset($conn) && $conn) {
    $conn->close();
}
<<<<<<< HEAD
?>
>>>>>>> Trí
=======
?>
>>>>>>> Phong
