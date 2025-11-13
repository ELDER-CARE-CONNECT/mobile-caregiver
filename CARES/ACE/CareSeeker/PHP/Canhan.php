<?php
session_start();

// ==========================================================
// THIẾT LẬP KẾT NỐI DATABASE ĐÚNG CÁCH (DỰA TRÊN TONGDONHANG.PHP)
// LƯU Ý: ĐƯỜNG DẪN "../../model/get_products.php" CÓ THỂ CẦN CHỈNH SỬA
// NẾU CẤU TRÚC THƯ MỤC CỦA BẠN KHÁC
// ==========================================================
$conn = null;
@include_once('../../model/get_products.php'); 
if (function_exists('connectdb')) {
    $conn = connectdb();
} 

// 🛑 DEBUG KIỂM TRA KẾT NỐI: BẠN CÓ THỂ BỎ ĐOẠN NÀY SAU KHI CHẠY THÀNH CÔNG
/*
echo "<p style='color: blue; text-align: center; font-weight: bold;'>[DEBUG] ID Khách hàng (\$id_khach): " . htmlspecialchars($_SESSION['profile']['id_khach_hang'] ?? 'NULL/EMPTY') . "</p>";
if (isset($conn)) {
    echo "<p style='color: green; text-align: center; font-weight: bold;'>[DEBUG] Biến \$conn: Đã được thiết lập (KẾT NỐI DB OK)</p>";
} else {
    echo "<p style='color: red; text-align: center; font-weight: bold;'>[DEBUG] Biến \$conn: CHƯA ĐƯỢC THIẾT LẬP (LỖI CONNECT.PHP?)</p>";
}
*/
// 🛑 KẾT THÚC DEBUG
// ==========================================================


/* ✅ Nếu chưa có hồ sơ, chuyển đến trang tạo hồ sơ */
if (!isset($_SESSION['profile']) || empty($_SESSION['profile'])) {
    header('Location: hoso.php');
    exit;
}

/* ✅ Lấy thông tin hồ sơ đã lưu trong session */
$profile = $_SESSION['profile'];


/* ✅ Nếu có kết nối DB thì load lại cho chính xác (tùy chọn) */
if (isset($conn) && isset($profile['id_khach_hang'])) {
    $id = mysqli_real_escape_string($conn, $profile['id_khach_hang']);
    // Cập nhật: SELECT * để lấy tất cả các trường, bao gồm các trường địa chỉ mới.
    $rs = mysqli_query($conn, "SELECT * FROM khach_hang WHERE id_khach_hang='$id' LIMIT 1");
    if ($rs && mysqli_num_rows($rs) === 1) {
        $profile_db = mysqli_fetch_assoc($rs);
        $_SESSION['profile'] = array_merge($profile, $profile_db); 
        $profile = $_SESSION['profile']; 
    }
}

/* ✅ Hàm tiện ích nhỏ */
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

/* ✅ Lấy dữ liệu hồ sơ */
$avatar       = $profile['avatar']        ?? 'uploads/default.png';
if (isset($profile['hinh_anh']) && !empty($profile['hinh_anh'])) {
    $avatar = $profile['hinh_anh'];
}

$ho_ten       = $profile['ten_khach_hang'] ?? $profile['ho_ten'] ?? '';
$email        = $profile['email']          ?? '';

// Lấy 3 trường địa chỉ
$ten_duong    = $profile['ten_duong']    ?? ''; 
$phuong_xa    = $profile['phuong_xa']    ?? ''; 
$tinh_thanh   = $profile['tinh_thanh']   ?? ''; 

// Hợp nhất 3 trường thành địa chỉ hiển thị
$parts = array_filter([$ten_duong, $phuong_xa, $tinh_thanh]);
$dia_chi = implode(', ', $parts);
if (empty($dia_chi)) {
    $dia_chi = 'Chưa cập nhật địa chỉ';
}

$so_dt        = $profile['so_dien_thoai'] ?? $profile['so_dt'] ?? '';
$tuoi         = $profile['tuoi']          ?? '';
$gioi_tinh    = $profile['gioi_tinh']     ?? '';
$chieu_cao    = $profile['chieu_cao']     ?? '';
$can_nang     = $profile['can_nang']      ?? '';

// Tính chỉ số BMI (tùy chọn)
$bmi = '';
if ((float)$chieu_cao > 0 && (float)$can_nang > 0) {
    $chieu_cao_m = (float)$chieu_cao / 100;
    $bmi_val = (float)$can_nang / ($chieu_cao_m * $chieu_cao_m);
    $bmi = number_format($bmi_val, 1);
}

/* ✅ Xử lý khi người dùng cập nhật hồ sơ (Từ form trong Edit Section) */
if (isset($_POST['update_profile'])) {
    $new = [
        'ten_khach_hang' => trim($_POST['ho_ten'] ?? $ho_ten),
        'email'          => trim($_POST['email'] ?? $email), 
        'so_dien_thoai'  => trim($_POST['so_dt'] ?? $so_dt),
        'tuoi'           => trim($_POST['tuoi'] ?? $tuoi),
        'gioi_tinh'      => trim($_POST['gioi_tinh'] ?? $gioi_thanh),
        'chieu_cao'      => trim($_POST['chieu_cao'] ?? $chieu_cao),
        'can_nang'       => trim($_POST['can_nang'] ?? $can_nang),
        'ten_duong'      => trim($_POST['ten_duong'] ?? $ten_duong),
        'phuong_xa'      => trim($_POST['phuong_xa'] ?? $phuong_xa),
        'tinh_thanh'     => trim($_POST['tinh_thanh'] ?? $tinh_thanh),
    ];
    
    // Cập nhật session (để hiển thị ngay mà không cần refresh)
    $_SESSION['profile'] = array_merge($profile, $new);
    $profile = $_SESSION['profile']; // Cập nhật biến $profile
    
    if (isset($conn) && isset($profile['id_khach_hang'])) {
        
        if (empty($profile['id_khach_hang'])) {
            die("<h1 style='color:#FF6B81; text-align:center;'>LỖI NGHIÊM TRỌNG:</h1> <p style='text-align:center;'>Không tìm thấy ID khách hàng (id_khach_hang) trong Session. Vui lòng đăng nhập lại.</p>");
        }
        
        $id = mysqli_real_escape_string($conn, $profile['id_khach_hang']);
        
        // Câu lệnh UPDATE SQL (cập nhật tất cả các trường bao gồm cả địa chỉ)
        $sql = "UPDATE khach_hang SET
            ten_khach_hang = '".mysqli_real_escape_string($conn,$new['ten_khach_hang'])."',
            email          = '".mysqli_real_escape_string($conn,$new['email'])."',
            ten_duong      = '".mysqli_real_escape_string($conn,$new['ten_duong'])."',
            phuong_xa      = '".mysqli_real_escape_string($conn,$new['phuong_xa'])."',
            tinh_thanh     = '".mysqli_real_escape_string($conn,$new['tinh_thanh'])."',
            so_dien_thoai  = '".mysqli_real_escape_string($conn,$new['so_dien_thoai'])."',
            tuoi           = '".mysqli_real_escape_string($conn,$new['tuoi'])."',
            gioi_tinh      = '".mysqli_real_escape_string($conn,$new['gioi_tinh'])."',
            chieu_cao      = '".mysqli_real_escape_string($conn,$new['chieu_cao'])."',
            can_nang       = '".mysqli_real_escape_string($conn,$new['can_nang'])."'
            WHERE id_khach_hang='$id'";
        
        // Hiển thị LỖI TRUY VẤN SQL (nếu có)
        if (!mysqli_query($conn, $sql)) {
            die("Lỗi truy vấn SQL: " . mysqli_error($conn) . "<br>Truy vấn: " . $sql);
        }
    }

    echo "<script>alert('Cập nhật hồ sơ thành công!'); window.location='Canhan.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Trang cá nhân</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
/* ----------------------------------- */
/* CSS TỪ NAVBAR.PHP (SAO CHÉP TỪ TONGDONHANG.PHP) */
/* ----------------------------------- */
.navbar {
    background: #fff;
    padding: 15px 60px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%; /* FIX: Đảm bảo Navbar luôn chiếm 100% */
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    position: fixed; 
    top: 0; 
    left: 0; 
    z-index: 1000;
    transition: all 0.3s;
}
.navbar h2 {
    color: #FF6B81;
    font-size: 26px; font-weight:700;
}
.nav-links a {
    color:#555; text-decoration:none; margin:0 16px;
    font-weight:500; position:relative; padding-bottom:3px;
}
.nav-links a:hover { color:#FF6B81; }
.nav-links a::after {
    content: ''; position:absolute; width:0; height:2px; display:block;
    margin-top:5px; right:0; background:#FF6B81; transition:0.3s;
}
.nav-links a:hover::after { width:100%; left:0; }
.nav-links a.active {
    color: #FF6B81;
    font-weight: 600;
}
.nav-links a.active::after {
    width: 100%;
    left: 0;
}

/* ----------------------------------- */
/* CSS KHÁC CỦA TRANG CÁ NHÂN */
/* ----------------------------------- */
:root {
    --accent: #FF6B81; /* Hồng */
    --accent-light: #FFE5E8; /* Hồng nhạt */
    --text-primary: #1f2937; /* Màu chữ đậm */
    --text-secondary: #6b7280; /* Màu chữ xám */
    --bg-light: #f9fafb; /* Nền rất nhạt */
    --bg-card: #ffffff; /* Nền card */
    --shadow-card: 0 4px 12px rgba(0, 0, 0, 0.05);
    --shadow-hover: 0 8px 20px rgba(0, 0, 0, 0.1);
    --radius: 12px;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Inter', Arial, sans-serif;
    background: var(--bg-light);
    min-height: 100vh;
    padding-top: 80px; /* FIX: Thêm padding-top để tránh bị Navbar che khuất */
}

.profile-dashboard {
    max-width: 1000px;
    margin: 30px auto 50px;
    padding: 0 15px;
}

.header-banner {
    background: linear-gradient(90deg, #bbded6, #61c0bf); 
    color: #fff;
    padding: 40px;
    border-radius: var(--radius);
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
}
.header-banner h1 {
    font-size: 30px;
    font-weight: 700;
    margin-bottom: 5px;
}
.header-banner p {
    font-size: 16px;
    opacity: 0.9;
}

.profile-card {
    background: var(--bg-card);
    padding: 30px;
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-top: -60px; 
    position: relative;
    z-index: 10;
    transition: all 0.3s;
}

.avatar-box {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid var(--accent-light);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    margin-bottom: 15px;
}
.avatar-box img {
    width: 100%; height: 100%; object-fit: cover;
}

.profile-card h2 {
    font-size: 28px;
    color: var(--text-primary);
    font-weight: 700;
    margin-bottom: 5px;
}
.profile-card span {
    color: var(--text-secondary);
    font-size: 15px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.details-card, .health-card {
    background: var(--bg-card);
    padding: 30px;
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
    transition: transform 0.2s;
}

.details-card h3, .health-card h3 {
    color: var(--text-primary);
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 20px;
    border-bottom: 2px solid var(--accent-light);
    padding-bottom: 10px;
    display: flex;
    align-items: center;
}
.details-card h3 i, .health-card h3 i {
    color: var(--accent);
    margin-right: 10px;
    font-size: 20px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 15px;
}
.info-item i {
    color: var(--accent);
    margin-right: 15px;
    font-size: 16px;
    margin-top: 3px;
    min-width: 20px;
}
.info-item strong {
    color: var(--text-secondary);
    font-weight: 500;
    min-width: 100px;
}
.info-item span {
    color: var(--text-primary);
    font-weight: 600;
    flex-grow: 1;
}

.metric-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 15px;
}
.metric-item {
    background: var(--accent-light);
    padding: 15px;
    border-radius: var(--radius);
    text-align: center;
}
.metric-item i {
    font-size: 24px;
    color: var(--accent);
    margin-bottom: 5px;
}
.metric-item .value {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
}
.metric-item .label {
    font-size: 14px;
    color: var(--text-secondary);
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
}

.btn {
    padding: 12px 25px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    box-shadow: 0 4px 8px rgba(0,0,0,0.05);
}

.btn-edit {
    background: var(--accent);
    color: #fff;
}

.btn-complaint {
    background: #fff;
    border: 1px solid var(--accent);
    color: var(--accent);
}

.btn-logout {
    background: #e9ecef;
    color: #333;
}

.hidden { display: none !important; }

#editSection, #complaintSection {
    background: var(--bg-light);
    padding: 40px;
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
    margin-top: 20px;
}
#editSection h3, #complaintSection h3 {
    font-size: 28px;
    color: var(--accent);
    margin-bottom: 25px;
    text-align: center;
}
#editSection label {
    font-size: 15px; font-weight: 600; color: #444; margin-top: 15px; display: block;
}
#editSection input, #editSection select {
    width: 100%; font-size: 16px; padding: 10px 14px; border-radius: 8px; border: 1px solid #ccc;
    margin-top: 5px; transition: border-color 0.3s;
}
#editSection input:focus, #editSection select:focus {
    border-color: var(--accent); outline: none; box-shadow: 0 0 0 3px rgba(255, 107, 129, 0.1);
}
.form-row-edit { display: flex; gap: 20px; }
.form-row-edit > div { flex: 1; }
.edit-buttons { margin-top: 20px; display: flex; gap: 10px; }
.btn-save { background: var(--accent); color: white; padding: 12px 20px; border-radius: 8px; flex: 1; }
.btn-back { background: #f0f0f0; color: #333; padding: 12px 20px; border-radius: 8px; }

.order-card {
    background: #fff7f8;
    border: 1px solid #ffe0e4;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.order-card p { font-size: 15px; line-height: 1.4; margin: 4px 0; color: #444; }
.order-card strong { font-weight: 600; color: #000; }

/* CSS MỚI CHO CHỨC NĂNG KHIẾU NẠI */
.order-info-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 10px; /* Thêm khoảng cách sau thông tin đơn hàng */
}
.send-complaint-btn {
    background: var(--accent);
    color: white;
    border: none;
    border-radius: 6px;
    padding: 8px 14px;
    cursor: pointer;
    font-weight: 500;
    transition: all 0.2s;
    float: none; /* Bỏ float để nó nằm trong order-info-row */
}
.disabled-btn {
    background: #ccc !important; /* Màu xám */
    cursor: not-allowed !important;
    opacity: 0.8;
    color: #444 !important;
}
.order-card-status {
    font-size: 15px; 
    line-height: 1.4; 
    color: #444;
}
.status-kn-pending { 
    color: #ff9800; /* Cam */
    font-weight: 700; 
} 
.status-kn-resolved { 
    color: #4CAF50; /* Xanh lá */
    font-weight: 700; 
}
/* KẾT THÚC CSS MỚI */

/* --- CSS CHO MODAL KHIẾU NẠI MỚI (PHẦN QUAN TRỌNG NHẤT) --- */
.modal-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0, 0, 0, 0.6);
    display: none; /* Mặc định ẩn */
    justify-content: center;
    align-items: center;
    z-index: 2000;
}

.modal-content {
    background: #fff;
    padding: 25px;
    border-radius: 12px;
    width: 90%;
    max-width: 450px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    animation: fadeIn 0.3s ease-out;
}

.modal-content h4 {
    color: var(--accent);
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 15px;
    border-bottom: 2px solid var(--accent-light);
    padding-bottom: 10px;
    display: flex;
    align-items: center;
}
.modal-content h4 i {
    margin-right: 10px;
}

.modal-content textarea {
    width: 100%;
    min-height: 120px;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-size: 15px;
    resize: vertical;
    margin-top: 10px;
    transition: border-color 0.3s;
}

.modal-content textarea:focus {
    border-color: var(--accent);
    outline: none;
    box-shadow: 0 0 0 3px rgba(255, 107, 129, 0.1);
}

.modal-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.modal-buttons .btn {
    padding: 10px 20px;
    font-size: 15px;
    font-weight: 600;
}

.btn-cancel {
    background: #e9ecef;
    color: #333;
}

.btn-submit {
    background: var(--accent);
    color: #fff;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}



@media (max-width: 768px) {
    .profile-dashboard {
        margin: 20px auto 30px;
    }
    .header-banner {
        padding: 30px 20px;
    }
    .info-grid {
        grid-template-columns: 1fr;
    }
    .profile-card {
        padding: 20px;
        margin-top: -40px;
    }
    .action-buttons {
        flex-direction: column;
        gap: 10px;
    }
    .btn {
        width: 100%;
    }
    .form-row-edit {
        flex-direction: column;
        gap: 0;
    }
    .navbar {
        padding: 15px 20px;
    }
    .modal-content {
        width: 95%; /* Tăng độ rộng trên mobile */
    }
}
</style>
</head>
<body>

<div class="navbar">
    <h2>Elder Care Connect</h2>
    <div class="nav-links">
        <a href="index.php">Trang chủ</a>
        <a href="dichvu.php">Dịch vụ</a>
        <a href="tongdonhang.php">Đơn hàng</a>
        <a href="Canhan.php" class="active">Cá nhân</a>
    </div>
</div>

<script>
// Logic JavaScript để đánh dấu link đang hoạt động (Active Link)
(function() {
    // Lấy tên file của trang hiện tại (ví dụ: "tongdonhang.php")
    var currentPage = window.location.pathname.split('/').pop();
    if (currentPage === "" || currentPage === "index.php") {
      currentPage = "index.php"; // Mặc định là trang chủ
    }

    // Lấy tất cả các link trong navbar
    var navLinks = document.querySelectorAll('.nav-links a');

    navLinks.forEach(function(link) {
      // Lấy tên file từ thuộc tính href của link
      var linkPage = new URL(link.href).pathname.split('/').pop();
      if (linkPage === "") {
        linkPage = "index.php";
      }

      // So sánh nếu tên file của link trùng với tên file của trang hiện tại
      if (linkPage === currentPage) {
        // Loại bỏ class 'active' khỏi các link khác trước (đảm bảo chỉ 1 link active)
        navLinks.forEach(l => l.classList.remove('active')); 
        link.classList.add('active'); // Thêm class 'active'
      }
    });
})();
</script>
<div class="profile-dashboard">
    
    <div class="header-banner">
        <h1>Quản lý Hồ sơ Cá nhân</h1>
        <p>Kiểm tra thông tin của bạn và theo dõi các chỉ số sức khỏe.</p>
    </div>

    <div id="infoSection">
        
        <div class="profile-card">
            <div class="avatar-box">
                <img src="<?php echo h($avatar); ?>" alt="avatar">
            </div>
            <h2><?php echo h($ho_ten); ?></h2>
            <span>Khách hàng thân thiết</span>
        </div>

        <div class="info-grid">
            
            <div class="details-card">
                <h3><i class="fas fa-address-card"></i> Thông tin Liên hệ</h3>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <strong>Địa chỉ:</strong>
                    <span><?php echo h($dia_chi); ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <strong>Email:</strong>
                    <span><?php echo h($email); ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone-alt"></i>
                    <strong>Số điện thoại:</strong>
                    <span><?php echo h($so_dt); ?></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-venus-mars"></i>
                    <strong>Giới tính:</strong>
                    <span><?php echo h($gioi_tinh); ?></span>
                </div>
            </div>

            <div class="health-card">
                <h3><i class="fas fa-heartbeat"></i> Chỉ số Sức khỏe</h3>
                <div class="metric-grid">
                    <div class="metric-item">
                        <i class="fas fa-birthday-cake"></i>
                        <div class="value"><?php echo h($tuoi); ?></div>
                        <div class="label">Tuổi</div>
                    </div>
                    <div class="metric-item">
                        <i class="fas fa-ruler-vertical"></i>
                        <div class="value"><?php echo h($chieu_cao); ?></div>
                        <div class="label">Chiều cao (cm)</div>
                    </div>
                    <div class="metric-item">
                        <i class="fas fa-weight"></i>
                        <div class="value"><?php echo h($can_nang); ?></div>
                        <div class="label">Cân nặng (kg)</div>
                    </div>
                       <div class="metric-item">
                         <i class="fas fa-chart-bar"></i>
                         <div class="value"><?php echo h($bmi); ?></div>
                         <div class="label">BMI</div>
                    </div>
                </div>
                <div style="margin-top: 20px; font-size: 14px; color: var(--text-secondary);">
                    BMI là chỉ số khối cơ thể, giúp đánh giá cân nặng.
                </div>
            </div>

        </div> <div class="action-buttons">
            <button class="btn btn-edit" id="btnEdit"><i class="fas fa-user-edit"></i> Chỉnh sửa hồ sơ</button>
            <button class="btn btn-complaint" id="btnKhieuNai"><i class="fas fa-exclamation-circle"></i> Khiếu nại dịch vụ</button>
            <button class="btn btn-logout" onclick="window.location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Đăng xuất</button>
        </div>
    </div>

    <div id="editSection" class="hidden">
      <h3>Chỉnh sửa Thông tin Cá nhân</h3>
      <form method="POST">
        
        <div class="form-row-edit">
            <div>
                <label>Họ và tên</label>
                <input type="text" name="ho_ten" value="<?php echo h($ho_ten); ?>">
            </div>
            <div>
                <label>Số điện thoại</label>
                <input type="text" name="so_dt" value="<?php echo h($so_dt); ?>">
            </div>
        </div>
        
        <label>Email</label>
        <input type="email" name="email" value="<?php echo h($email); ?>">
        
        <label>Tên đường/Số nhà</label>
        <input type="text" name="ten_duong" value="<?php echo h($ten_duong); ?>">

        <label>Phường/Xã</label>
        <input type="text" name="phuong_xa" value="<?php echo h($phuong_xa); ?>">

        <label>Tỉnh/Thành phố</label>
        <input type="text" name="tinh_thanh" value="<?php echo h($tinh_thanh); ?>">
        <div class="form-row-edit">
            <div>
                <label>Tuổi</label>
                <input type="number" name="tuoi" value="<?php echo h($tuoi); ?>">
            </div>
            <div>
                <label>Giới tính</label>
                <select name="gioi_tinh">
                <option value="Nam" <?php echo ($gioi_tinh=='Nam'?'selected':''); ?>>Nam</option>
                <option value="Nữ" <?php echo ($gioi_tinh=='Nữ'?'selected':''); ?>>Nữ</option>
                <option value="Khác" <?php echo ($gioi_tinh=='Khác'?'selected':''); ?>>Khác</option>
                </select>
            </div>
        </div>
        
        <div class="form-row-edit">
            <div>
                <label>Chiều cao (cm)</label>
                <input type="number" name="chieu_cao" value="<?php echo h($chieu_cao); ?>">
            </div>
            <div>
                <label>Cân nặng (kg)</label>
                <input type="number" name="can_nang" value="<?php echo h($can_nang); ?>">
            </div>
        </div>

        <div class="edit-buttons">
          <button type="submit" name="update_profile" class="btn btn-save">Lưu thay đổi</button>
          <button type="button" class="btn btn-back" id="btnCancelEdit">Hủy</button>
        </div>
      </form>
    </div>

    <div id="complaintSection" class="hidden">
      <h3><i class="fas fa-bug"></i> Danh sách Đơn hàng & Khiếu nại</h3>

      <?php
      $id_khach = $_SESSION['profile']['id_khach_hang'] ?? null;
      $donhangs = [];

      // CHỈ THỰC THI TRUY VẤN KHI CÓ KẾT NỐI DB VÀ CÓ ID KHÁCH HÀNG
      if ($id_khach && isset($conn)) {
          $id_khach_sql = mysqli_real_escape_string($conn, $id_khach);

          // ĐÃ CHỈNH SỬA: Xóa điều kiện lọc trạng thái để hiển thị TẤT CẢ đơn hàng.
          $sql = "
          SELECT 
            d.id_don_hang, d.id_nguoi_cham_soc, d.ngay_dat, d.tong_tien, d.trang_thai,
            kn.id_khieu_nai, kn.trang_thai AS trang_thai_khieu_nai
          FROM don_hang d
          LEFT JOIN khieu_nai kn 
            ON d.id_don_hang = kn.id_don_hang
          WHERE d.id_khach_hang = '$id_khach_sql'
          ORDER BY d.ngay_dat DESC
          ";


          $rs = mysqli_query($conn, $sql);
          if ($rs) {
              while ($row = mysqli_fetch_assoc($rs)) {
                  $row['id_cham_soc'] = $row['id_nguoi_cham_soc']; 
                  $donhangs[] = $row;
              }
          } else {
              // Hiển thị lỗi truy vấn để dễ debug
              echo "<p style='color: red; text-align: center;'>Lỗi truy vấn: " . mysqli_error($conn) . "</p>";
          }
      }
      ?>

      <div id="complaintList">
          <?php if (!empty($donhangs)): ?>
              <?php foreach ($donhangs as $d): 
                  $has_complaint = !empty($d['id_khieu_nai']);
                  $complaint_status = htmlspecialchars($d['trang_thai_khieu_nai'] ?? '');
                  $complaint_status_class = '';
                  
                  if ($complaint_status == 'Chờ xử lý') {
                      $complaint_status_class = 'status-kn-pending';
                  } elseif ($complaint_status == 'Đã giải quyết') {
                      $complaint_status_class = 'status-kn-resolved';
                  }
                  
                  // Thiết lập màu cho trạng thái đơn hàng (tùy chọn)
                  $order_status_color = '#007bff'; // Màu mặc định cho trạng thái đang tiến hành
                  if (strpos(strtolower($d['trang_thai']), 'hoàn thành') !== false) {
                      $order_status_color = '#4CAF50'; // Xanh lá nếu hoàn thành
                  } elseif (strpos(strtolower($d['trang_thai']), 'hủy') !== false) {
                      $order_status_color = '#f44336'; // Đỏ nếu đã hủy
                  }
                  ?>
                  <div class="order-card">
                      <p><strong>Mã đơn hàng:</strong> #<?= htmlspecialchars($d['id_don_hang']) ?></p>
                      <p><strong>Trạng thái ĐH:</strong> <span style="font-weight:700; color:<?= $order_status_color ?>;"><?= htmlspecialchars($d['trang_thai']) ?></span></p>
                      <p><strong>Ngày đặt:</strong> <?= htmlspecialchars($d['ngay_dat']) ?></p>
                      <p><strong>Tổng tiền:</strong> <?= number_format($d['tong_tien'], 0, ',', '.') ?>₫</p>
                      <p><strong>Người chăm sóc ID:</strong> <?= htmlspecialchars($d['id_cham_soc']) ?></p>
                      
                      <div class="order-info-row">
                          <div class="order-card-status">
                              <?php if ($has_complaint): ?>
                                  <strong>Tình trạng Khiếu nại:</strong> 
                                  <span class="<?= $complaint_status_class ?>"><?= $complaint_status ?></span>
                              <?php endif; ?>
                          </div>
                          
                          <?php if ($has_complaint): ?>
                              <button class="send-complaint-btn disabled-btn" disabled>
                                  <i class="fas fa-check-circle"></i> Đã gửi KN
                              </button>
                          <?php else: ?>
                              <button class="send-complaint-btn" 
                                        data-id="<?= htmlspecialchars($d['id_don_hang']) ?>">
                                  <i class="fas fa-exclamation-triangle"></i> Gửi khiếu nại
                              </button>
                          <?php endif; ?>
                      </div>
                  </div>
              <?php endforeach; ?>
          <?php else: ?>
              <p style="padding: 15px; background: #fff; border-radius: 8px; text-align: center;">
                  ⚠️ Bạn chưa có đơn hàng nào trong lịch sử để hiển thị.
              </p>
          <?php endif; ?>

          <button type="button" class="btn btn-back" id="backToInfo" style="margin-top: 20px;">
              <i class="fas fa-arrow-left"></i> Quay lại Hồ sơ
          </button>
      </div>
  </div>


</div> 

<div id="complaintModal" class="modal-overlay">
    <div class="modal-content">
        <h4><i class="fas fa-exclamation-circle"></i> Gửi Khiếu nại Đơn hàng</h4>
        <p>Vui lòng nhập chi tiết vấn đề bạn gặp phải với đơn hàng <strong id="modalOrderId"></strong>:</p>
        <textarea id="complaintReason" placeholder="Nhập nội dung khiếu nại (ví dụ: Dịch vụ không đúng mô tả, Người chăm sóc đến trễ...)" autofocus></textarea>
        
        <div class="modal-buttons">
            <button type="button" class="btn btn-cancel" id="btnModalCancel">Hủy</button>
            <button type="button" class="btn btn-submit" id="btnModalSubmit">Gửi Khiếu Nại</button>
        </div>
    </div>
</div>
<script>
// ==================== LẤY PHẦN TỬ HTML ====================
const infoSection = document.getElementById('infoSection');
const editSection = document.getElementById('editSection');
const complaintSection = document.getElementById('complaintSection');

const btnEdit = document.getElementById('btnEdit');
const btnCancelEdit = document.getElementById('btnCancelEdit');
const btnKhieuNai = document.getElementById('btnKhieuNai');
const btnBackToInfo = document.getElementById('backToInfo');

// PHẦN TỬ MODAL MỚI
const modal = document.getElementById('complaintModal');
const modalOrderId = document.getElementById('modalOrderId');
const complaintReason = document.getElementById('complaintReason');
const btnModalCancel = document.getElementById('btnModalCancel');
const btnModalSubmit = document.getElementById('btnModalSubmit');
let currentOrderId = null; // Biến lưu trữ ID đơn hàng đang được khiếu nại

// ==================== XỬ LÝ CHUYỂN ĐỔI GIAO DIỆN (GIỮ NGUYÊN) ====================

// Bấm “Chỉnh sửa hồ sơ”
if (btnEdit) {
  btnEdit.addEventListener('click', () => {
    infoSection.classList.add('hidden');
    editSection.classList.remove('hidden');
    complaintSection.classList.add('hidden');
  });
}

// Bấm “Hủy chỉnh sửa”
if (btnCancelEdit) {
  btnCancelEdit.addEventListener('click', () => {
    editSection.classList.add('hidden');
    infoSection.classList.remove('hidden');
  });
}

// Bấm “Khiếu nại”
if (btnKhieuNai) {
  btnKhieuNai.addEventListener('click', () => {
    infoSection.classList.add('hidden');
    editSection.classList.add('hidden');
    complaintSection.classList.remove('hidden');
  });
}

// Bấm “← Quay lại”
if (btnBackToInfo) {
  btnBackToInfo.addEventListener('click', () => {
    complaintSection.classList.add('hidden');
    infoSection.classList.remove('hidden');
  });
}

// ==================== LOGIC XỬ LÝ MODAL KHIẾU NẠI (PHẦN MỚI) ====================

function hideModal() {
    modal.style.display = 'none';
    complaintReason.value = ''; // Xóa nội dung
    currentOrderId = null; // Reset ID đơn hàng
    // Đảm bảo nút Gửi không bị disabled nếu trước đó bị disabled do gửi thất bại
    btnModalSubmit.disabled = false;
    btnModalSubmit.innerHTML = 'Gửi Khiếu Nại';
    btnModalSubmit.classList.remove('disabled-btn');
}

// 1. Mở Modal khi bấm "Gửi khiếu nại"
document.querySelectorAll('.send-complaint-btn').forEach(btn => {
  btn.addEventListener('click', (e) => {
    if (btn.classList.contains('disabled-btn')) return; // Bỏ qua nếu đã gửi KN

    currentOrderId = btn.getAttribute('data-id');
    modalOrderId.textContent = `#${currentOrderId}`;
    modal.style.display = 'flex'; // Hiển thị Modal
    complaintReason.focus(); // Tập trung vào ô nhập liệu
  });
});

// 2. Đóng Modal khi bấm "Hủy" hoặc click ra ngoài
if (btnModalCancel) {
    btnModalCancel.addEventListener('click', hideModal);
}
if (modal) {
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            hideModal();
        }
    });
}
// 3. Xử lý logic Gửi khi bấm "Gửi Khiếu Nại"
if (btnModalSubmit) {
    btnModalSubmit.addEventListener('click', async () => {
        const idDon = currentOrderId;
        const reason = complaintReason.value.trim();
        
        if (reason === "") {
            alert('Nội dung khiếu nại không được để trống.');
            complaintReason.focus();
            return;
        }

        // Hiển thị trạng thái đang gửi
        const originalContent = btnModalSubmit.innerHTML;
        btnModalSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
        btnModalSubmit.disabled = true;
        btnModalSubmit.classList.add('disabled-btn');
        
        hideModal(); // Ẩn Modal ngay sau khi bắt đầu gửi

        // Tìm nút "Gửi khiếu nại" tương ứng trên danh sách để update trạng thái
        const targetBtn = document.querySelector(`.send-complaint-btn[data-id="${idDon}"]`);
        const originalTargetBtnContent = targetBtn ? targetBtn.innerHTML : '';
        if (targetBtn) {
            targetBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
            targetBtn.disabled = true;
            targetBtn.classList.add('disabled-btn');
        }

        try {
            // Gửi dữ liệu đến file guikhieunai.php để lưu vào DB
            const response = await fetch('guikhieunai.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_don_hang=${idDon}&noi_dung=${encodeURIComponent(reason)}`
            });
            
            const result = await response.json();
            
            if (result.success) {
                alert(`✅ Gửi khiếu nại thành công cho đơn hàng #${idDon}`);
                // Tải lại trang để cập nhật trạng thái khiếu nại từ DB
                window.location.reload(); 
            } else {
                alert(`❌ Lỗi khi gửi khiếu nại: ${result.message || 'Không rõ lỗi.'}`);
                // Đặt lại nút nếu thất bại
                if (targetBtn) {
                    targetBtn.innerHTML = originalTargetBtnContent;
                    targetBtn.disabled = false;
                    targetBtn.classList.remove('disabled-btn');
                }
            }

        } catch (error) {
            alert('❌ Lỗi kết nối hoặc lỗi server. Đảm bảo file guikhieunai.php tồn tại và hoạt động.');
            // Đặt lại nút nếu thất bại
            if (targetBtn) {
                targetBtn.innerHTML = originalTargetBtnContent;
                targetBtn.disabled = false;
                targetBtn.classList.remove('disabled-btn');
            }
        }
    });
}
// ==================== KẾT THÚC LOGIC MỚI ====================
</script>

</body>
</html>
