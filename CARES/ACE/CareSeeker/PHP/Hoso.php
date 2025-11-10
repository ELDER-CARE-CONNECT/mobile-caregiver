<?php
session_start();

// ⭐ Xử lý thông báo thành công (FLASH MESSAGE)
$errors = [];
$success = "";

// Kiểm tra nếu có thông báo thành công được lưu trong Session (sau khi redirect)
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message']; // Lấy thông báo ra
    unset($_SESSION['success_message']);     // Xóa thông báo khỏi session để không hiện lại
}

// =================================================================
// 1. 🔒 KIỂM TRA ĐĂNG NHẬP & LẤY ĐỊNH DANH (DÙNG ID KHÁCH HÀNG)
// =================================================================
// KIỂM TRA SESSION ĐỊNH DANH (ID khách hàng)
if (!isset($_SESSION['id_khach_hang'])) { 
    header("Location: ../../admin/login.php");
    exit();
}

// ✅ SỬ DỤNG id_khach_hang LÀM ĐỊNH DANH DUY NHẤT VÀ KHÔNG ĐỔI
$id_khach_hang_hien_tai = $_SESSION['id_khach_hang']; 
// =================================================================
// 2. KẾT NỐI DATABASE & CẤU HÌNH THƯ MỤC UPLOAD
// =================================================================
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sanpham";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Ket noi that bai: " . $conn->connect_error);
}
// Thiết lập charset UTF8
$conn->set_charset("utf8");

// ⚠️ CẤU HÌNH ĐƯỜNG DẪN LƯU ẢNH
$base_dir = __DIR__;
$upload_dir = $base_dir . '/uploads/avatars/'; 
$base_url_path = 'uploads/avatars/';

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); 
}

$profile = []; 

// =================================================================
// 3. TẢI DỮ LIỆU HIỆN TẠI (GET)
// =================================================================
// Dùng id_khach_hang để tìm kiếm
$sql_fetch = "SELECT 
    id_khach_hang, ten_khach_hang, so_dien_thoai, email, 
    ten_duong, phuong_xa, tinh_thanh,
    tuoi, gioi_tinh, chieu_cao, can_nang, hinh_anh 
    FROM khach_hang WHERE id_khach_hang = ?";
    
$stmt_fetch = $conn->prepare($sql_fetch);

// ✅ Kiểm tra và xử lý nếu prepare thất bại
if (!$stmt_fetch) {
    die('Lỗi chuẩn bị câu lệnh SQL: ' . $conn->error);
}

$stmt_fetch->bind_param("i", $id_khach_hang_hien_tai);
$stmt_fetch->execute();
$result_fetch = $stmt_fetch->get_result();
$profile = $result_fetch->fetch_assoc();
$stmt_fetch->close();

if (!$profile) {
    // Trường hợp không tìm thấy profile, xóa session và đăng xuất
    session_unset();
    session_destroy();
    header("Location: ../../admin/login.php");
    exit();
}

// 4. XỬ LÝ CẬP NHẬT DỮ LIỆU (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $ho_ten = trim($_POST['ho_ten'] ?? '');
    $so_dien_thoai_moi = trim($_POST['so_dt'] ?? ''); 
    $email_moi = trim($_POST['email'] ?? ''); 
    
    $ten_duong = trim($_POST['ten_duong'] ?? '');
    $phuong_xa = trim($_POST['phuong_xa'] ?? '');
    $tinh_thanh = trim($_POST['tinh_thanh'] ?? '');
    
    $tuoi = intval($_POST['tuoi'] ?? 0); 
    $gioi_tinh = $_POST['gioi_tinh'] ?? '';
    $chieu_cao = floatval($_POST['chieu_cao'] ?? 0); 
    $can_nang = floatval($_POST['can_nang'] ?? 0);
    
    // Sử dụng ảnh cũ nếu không upload ảnh mới
    $hinh_anh_path = $profile['hinh_anh'] ?? ''; 

    // --- ⭐ BƯỚC 1: KIỂM TRA TẤT CẢ CÁC Ô BẮT BUỘC KHÔNG ĐƯỢC ĐỂ TRỐNG (VALIDATION) ---
    
    if ($ho_ten === '') $errors[] = 'Vui lòng nhập **Họ và tên**.';
    if ($so_dien_thoai_moi === '') $errors[] = 'Vui lòng nhập **Số điện thoại**.';
    if ($email_moi === '') $errors[] = 'Vui lòng nhập **Email**.';
    if ($ten_duong === '') $errors[] = 'Vui lòng nhập **Số nhà, Tên đường**.';
    if ($phuong_xa === '') $errors[] = 'Vui lòng nhập **Phường/Xã**.';
    if ($tinh_thanh === '') $errors[] = 'Vui lòng nhập **Tỉnh/Thành phố**.';
    if ($tuoi <= 0) $errors[] = 'Vui lòng nhập **Tuổi hợp lệ**.';
    if ($gioi_tinh === '') $errors[] = 'Vui lòng chọn **Giới tính**.';

    // --- BƯỚC 2: KIỂM TRA ĐỊNH DẠNG VÀ LOGIC NẾU KHÔNG CÓ LỖI TRỐNG ---
    if (empty($errors)) {
        
        // 1. Kiểm tra SĐT (chỉ chấp nhận 10 chữ số)
        if (!preg_match('/^[0-9]{10}$/', $so_dien_thoai_moi)) {
            $errors[] = 'Số điện thoại phải **đúng 10 chữ số** (ví dụ: 0912345678).';
        }
        // Kiểm tra trùng lặp SĐT mới (trừ SĐT hiện tại)
        $so_dien_thoai_hien_tai = $profile['so_dien_thoai'] ?? ''; 
        if (empty($errors) && $so_dien_thoai_moi !== $so_dien_thoai_hien_tai) {
            // ✅ SỬA: Bổ sung kiểm tra không trùng với chính tài khoản hiện tại (id_khach_hang <> ?)
            $stmt_check_sdt = $conn->prepare("SELECT so_dien_thoai FROM khach_hang WHERE so_dien_thoai = ? AND id_khach_hang <> ?");
            $stmt_check_sdt->bind_param("si", $so_dien_thoai_moi, $id_khach_hang_hien_tai);
            $stmt_check_sdt->execute();
            $result_check_sdt = $stmt_check_sdt->get_result();
            if ($result_check_sdt->num_rows > 0) {
                $errors[] = 'Số điện thoại này đã được đăng ký cho tài khoản khác.';
            }
            $stmt_check_sdt->close();
        }

        // 2. Kiểm tra Email
        if (!filter_var($email_moi, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email không hợp lệ. Vui lòng kiểm tra lại.';
        }
        // Kiểm tra trùng lặp Email mới (trừ Email hiện tại)
        $email_hien_tai = $profile['email'] ?? ''; 
        if (empty($errors) && $email_moi !== $email_hien_tai) {
            // ✅ SỬA: Bổ sung kiểm tra không trùng với chính tài khoản hiện tại (id_khach_hang <> ?)
            $stmt_check_email = $conn->prepare("SELECT email FROM khach_hang WHERE email = ? AND id_khach_hang <> ?");
            $stmt_check_email->bind_param("si", $email_moi, $id_khach_hang_hien_tai);
            $stmt_check_email->execute();
            $result_check_email = $stmt_check_email->get_result();
            if ($result_check_email->num_rows > 0) {
                $errors[] = 'Email này đã được đăng ký cho tài khoản khác.';
            }
            $stmt_check_email->close();
        }
        
        // 3. Các kiểm tra logic khác
        if ($tuoi <= 0 || $tuoi > 120) $errors[] = 'Vui lòng nhập Tuổi hợp lệ (1-120).';
        if (!in_array($gioi_tinh, ['Nam','Nữ','Khác'])) $errors[] = 'Vui lòng chọn Giới tính hợp lệ (Nam, Nữ, Khác).';
        
        // --- XỬ LÝ UPLOAD ẢNH ---
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['avatar'];
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($file_ext, $allowed_ext)) {
                $errors[] = 'Chỉ chấp nhận file ảnh có định dạng JPG, JPEG, PNG, GIF.';
            } elseif ($file['size'] > 5000000) { 
                $errors[] = 'Kích thước file quá lớn (tối đa 5MB).';
            } else {
                $new_file_name = uniqid('avatar_', true) . '.' . $file_ext;
                $new_image_file_target = $upload_dir . $new_file_name;
                
                if (move_uploaded_file($file['tmp_name'], $new_image_file_target)) {
                    $new_hinh_anh_path = $base_url_path . $new_file_name; 
                    
                    // Xóa ảnh cũ nếu tồn tại
                    if (!empty($profile['hinh_anh']) && file_exists($base_dir . '/' . $profile['hinh_anh'])) {
                        unlink($base_dir . '/' . $profile['hinh_anh']);
                    }
                    $hinh_anh_path = $new_hinh_anh_path;
                } else {
                    $errors[] = 'Lỗi khi upload file ảnh.';
                }
            }
        }
    }


    if (empty($errors)) {
        // --- BƯỚC 3: CẬP NHẬT DỮ LIỆU VÀO DB BẰNG id_khach_hang ---
        $sql_update = "UPDATE khach_hang SET 
            ten_khach_hang = ?, 
            so_dien_thoai = ?,
            email = ?,
            ten_duong = ?, phuong_xa = ?, tinh_thanh = ?, 
            tuoi = ?, gioi_tinh = ?, chieu_cao = ?, can_nang = ?,
            hinh_anh = ?
            WHERE id_khach_hang = ?"; 
        
        $stmt_update = $conn->prepare($sql_update);
        
        if ($stmt_update) {
            $stmt_update->bind_param("ssssssisddsi", 
                $ho_ten, 
                $so_dien_thoai_moi,
                $email_moi, 
                $ten_duong, $phuong_xa, $tinh_thanh, 
                $tuoi, $gioi_tinh, $chieu_cao, $can_nang, 
                $hinh_anh_path,
                $id_khach_hang_hien_tai
            );
            
            if ($stmt_update->execute()) {
                // ⭐ THỰC HIỆN FLASH MESSAGE: Lưu thông báo vào SESSION
                $_SESSION['success_message'] = "Cập nhật hồ sơ thành công! 🎉";
                
                // Cập nhật lại Session cho dữ liệu mới
                $_SESSION['ten_khach_hang'] = $ho_ten; 
                $_SESSION['so_dien_thoai'] = $so_dien_thoai_moi; 
                $_SESSION['email'] = $email_moi; 
                
                // Cập nhật lại $profile trong session (optional nhưng nên có)
                $_SESSION['profile'] = [
                    'id_khach_hang' => $id_khach_hang_hien_tai, 
                    'ten_khach_hang' => $ho_ten, 
                    'so_dien_thoai' => $so_dien_thoai_moi, 
                    'email' => $email_moi, 
                    'ten_duong' => $ten_duong, 
                    'phuong_xa' => $phuong_xa, 
                    'tinh_thanh' => $tinh_thanh,
                    'tuoi' => $tuoi, 
                    'gioi_tinh' => $gioi_tinh, 
                    'chieu_cao' => $chieu_cao, 
                    'can_nang' => $can_nang, 
                    'hinh_anh' => $hinh_anh_path, 
                ];
                
                // ✅ CHUYỂN HƯỚNG VỀ TRANG index.php SAU KHI CẬP NHẬT HOÀN TẤT
                header("Location: index.php"); 
                exit();
            } else {
                $errors[] = "Lỗi cập nhật dữ liệu: " . $stmt_update->error;
            }
            $stmt_update->close();
        } else {
             $errors[] = 'Lỗi chuẩn bị câu lệnh SQL: ' . $conn->error;
        }
        
        // ✅ Cập nhật lại $profile để hiển thị dữ liệu mới nhất nếu update thất bại (không redirect)
        $profile = [
            'ten_khach_hang' => $ho_ten,
            'so_dien_thoai' => $so_dien_thoai_moi,
            'email' => $email_moi,
            'ten_duong' => $ten_duong,
            'phuong_xa' => $phuong_xa,
            'tinh_thanh' => $tinh_thanh,
            'tuoi' => $tuoi,
            'gioi_tinh' => $gioi_tinh,
            'chieu_cao' => $chieu_cao,
            'can_nang' => $can_nang,
            'hinh_anh' => $hinh_anh_path,
        ];
    }
}
$conn->close();
// --- END PHP ---
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ cá nhân</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS giữ nguyên theo cấu trúc cũ */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #f8f8fa; color: #333; line-height: 1.6; }
        .container { max-width: 900px; margin: 50px auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); }
        h1 { text-align: center; color: #FF6B81; margin-bottom: 30px; font-weight: 700; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #555; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 16px; transition: border-color 0.3s; }
        .form-group input:focus, .form-group select:focus { border-color: #FF6B81; outline: none; }
        .form-group input[readonly] { background-color: #eee; cursor: not-allowed; }
        .form-row { display: flex; gap: 20px; }
        .form-row > .form-group { flex: 1; }
        .btn-submit { display: block; width: 100%; padding: 14px; background-color: #FF6B81; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 18px; font-weight: 600; transition: background-color 0.3s; margin-top: 30px; }
        .btn-submit:hover { background-color: #E65A6E; }
        .alert-error { color: #d9534f; background-color: #f2dede; border: 1px solid #ebccd1; padding: 10px; border-radius: 6px; margin-bottom: 15px; }
        .alert-success { color: #4CAF50; background-color: #dff0d8; border: 1px solid #d6e9c6; padding: 10px; border-radius: 6px; margin-bottom: 15px; }
        .avatar-upload { text-align: center; margin-bottom: 30px; }
        .avatar-box { width: 120px; height: 120px; border-radius: 50%; border: 3px solid #ddd; overflow: hidden; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; background-color: #f0f0f0; }
        .avatar-box img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body>
  <?php 
// Giả định navbar.php nằm cùng thư mục
// include 'navbar.php'; 
?>

<div class="container">
    <h1>Cập nhật Hồ sơ Khách hàng</h1>

    <?php if (!empty($errors)): ?>
        <div class="alert-error">
            <?php foreach ($errors as $error): ?>
                <p>⚠️ <?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert-success">
            <p>✅ <?php echo htmlspecialchars($success); ?></p>
        </div>
    <?php endif; ?>

    <form method="POST" id="profileForm" enctype="multipart/form-data"> 
        
        <div class="avatar-upload">
            <label for="avatar">Ảnh đại diện</label>
            <div class="avatar-box" id="avatarBox">
                <?php 
                // ✅ Đảm bảo đường dẫn ảnh hiển thị đúng (cần /CareSeeker/PHP/ đứng trước path trong DB)
                if (!empty($profile['hinh_anh'])): ?>
                    <img src="<?php echo htmlspecialchars($profile['hinh_anh']); ?>" alt="Ảnh đại diện">
                <?php else: ?>
                    <div class="small">Chưa có ảnh</div>
                <?php endif; ?>
            </div>
            <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;">
            <label for="avatar" style="cursor: pointer; color: #FF6B81; font-weight: 500;">Chọn ảnh</label>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="so_dt">Số điện thoại <span style="color:red;">(*)</span></label>
                <input type="text" id="so_dt" name="so_dt" 
                 value="<?php 
                $sdt = $profile['so_dien_thoai'] ?? '';
                // ✅ CHỈ HIỂN THỊ NẾU NÓ LÀ CHUỖI 10 SỐ HỢP LỆ, CÒN LẠI HIỂN THỊ TRỐNG
                if (preg_match('/^[0-9]{10}$/', $sdt)) {
                    echo htmlspecialchars($sdt);
                }
               ?>" 
        placeholder="Vui lòng nhập số điện thoại (10 số)" 
        required pattern="[0-9]{10}" title="Vui lòng nhập đúng 10 chữ số">
            </div>
            <div class="form-group">
                <label for="email">Email <span style="color:red;">(*)</span></label>
                <input type="email" id="email" name="email" placeholder="Nhập địa chỉ email"
                        value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="ho_ten">Họ và tên <span style="color:red;">(*)</span></label>
            <input type="text" id="ho_ten" name="ho_ten" placeholder="Nhập họ và tên" 
                    value="<?php echo htmlspecialchars($profile['ten_khach_hang'] ?? ''); ?>" required>
            </div>
        
        <div class="form-group">
            <label for="ten_duong">Số nhà, Tên đường <span style="color:red;">(*)</span></label>
            <input type="text" id="ten_duong" name="ten_duong" placeholder="Ví dụ: 123 Nguyễn Văn Linh" 
                value="<?php echo htmlspecialchars($profile['ten_duong'] ?? ''); ?>" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="phuong_xa">Phường/Xã <span style="color:red;">(*)</span></label>
                <input type="text" id="phuong_xa" name="phuong_xa" placeholder="Ví dụ: Phường 1" 
                    value="<?php echo htmlspecialchars($profile['phuong_xa'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="tinh_thanh">Tỉnh/Thành phố <span style="color:red;">(*)</span></label>
                <input type="text" id="tinh_thanh" name="tinh_thanh" placeholder="Ví dụ: TP. Hồ Chí Minh" 
                    value="<?php echo htmlspecialchars($profile['tinh_thanh'] ?? ''); ?>" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label for="tuoi">Tuổi <span style="color:red;">(*)</span></label>
                <input type="number" id="tuoi" name="tuoi" min="1" max="120" placeholder="Nhập tuổi" 
                    value="<?php echo (($profile['tuoi'] ?? 0) > 0) ? htmlspecialchars($profile['tuoi']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="gioi_tinh">Giới tính <span style="color:red;">(*)</span></label>
                <select id="gioi_tinh" name="gioi_tinh" required>
                    <option value="">-- Chọn giới tính --</option>
                    <option value="Nam" <?php echo ($profile['gioi_tinh'] ?? '') === 'Nam' ? 'selected' : ''; ?>>Nam</option>
                    <option value="Nữ" <?php echo ($profile['gioi_tinh'] ?? '') === 'Nữ' ? 'selected' : ''; ?>>Nữ</option>
                    <option value="Khác" <?php echo ($profile['gioi_tinh'] ?? '') === 'Khác' ? 'selected' : ''; ?>>Khác</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="chieu_cao">Chiều cao (cm)</label>
                <input type="number" id="chieu_cao" name="chieu_cao" min="50" max="250" placeholder="Chiều cao (cm)" 
                value="<?php echo (($profile['chieu_cao'] ?? 0) > 0) ? htmlspecialchars($profile['chieu_cao']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="can_nang">Cân nặng (kg)</label>
                <input type="number" id="can_nang" name="can_nang" min="10" max="200" placeholder="Cân nặng (kg)" 
                 value="<?php echo (($profile['can_nang'] ?? 0) > 0) ? htmlspecialchars($profile['can_nang']) : ''; ?>">
            </div>
        </div>
        <p style="font-size: 14px; text-align: right; color: #555;"><span style="color:red;">(*)</span> Là các trường bắt buộc.</p>

        <button type="submit" class="btn-submit">Cập nhật Hồ sơ</button>
    </form>
</div>

<script>
// JavaScript xử lý xem trước ảnh 
const avatarInput = document.getElementById('avatar');
const avatarBox = document.getElementById('avatarBox');

avatarInput && avatarInput.addEventListener('change', function(e){
    const file = e.target.files[0];
    if (!file) return;
    if (!file.type.startsWith('image/')) return;
    const reader = new FileReader();
    reader.onload = function(ev){
        avatarBox.innerHTML = '';
        const img = document.createElement('img');
        img.src = ev.target.result;
        avatarBox.appendChild(img);
    }
    reader.readAsDataURL(file);
});
</script>

</body>
</html>
