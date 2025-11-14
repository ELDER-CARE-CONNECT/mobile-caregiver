<?php
// Tệp: backend/api_profile.php
// Microservice: Quản lý Hồ sơ Khách hàng (Tải dữ liệu GET, Cập nhật POST)
session_start();
header('Content-Type: application/json');

// --- CẤU HÌNH DB VÀ UPLOAD ---
require_once 'db_connect.php'; 

// CẤU HÌNH UPLOAD (Dựa trên logic hoso.php gốc)
$base_dir = dirname(__DIR__) . '/Frontend'; // Thư mục Frontend (ví dụ: .../PHP/Frontend)
$upload_dir = $base_dir . '/uploads/avatars/'; // Đường dẫn vật lý .../Frontend/uploads/avatars/
$base_url_path = 'uploads/avatars/'; // SỬA LỖI: Chỉ lưu đường dẫn tương đối này vào CSDL

if (!is_dir($upload_dir)) {
    // Thử tạo thư mục nếu nó không tồn tại
    // (Hãy đảm bảo thư mục .../Frontend/uploads/ có quyền ghi)
    if (!mkdir($upload_dir, 0777, true) && !is_dir($upload_dir)) {
         http_response_code(500);
         echo json_encode(['success' => false, 'message' => "Lỗi: Không thể tạo thư mục upload tại: $upload_dir"]);
         exit;
    }
}
// --- END CẤU HÌNH ---

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['id_khach_hang'])) { 
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Lỗi xác thực: Vui lòng đăng nhập.']);
    exit;
}
$id_khach_hang_hien_tai = $_SESSION['id_khach_hang']; 

try {
    $pdo = get_pdo_connection();
    $method = $_SERVER['REQUEST_METHOD'];

    // =============================================
    // GET: TẢI DỮ LIỆU HIỆN TẠI
    // =============================================
    if ($method === 'GET') {
        $sql_fetch = "SELECT id_khach_hang, ten_khach_hang, so_dien_thoai, email, 
                        ten_duong, phuong_xa, tinh_thanh,
                        tuoi, gioi_tinh, chieu_cao, can_nang, hinh_anh 
                      FROM khach_hang WHERE id_khach_hang = ?";
                      
        $stmt_fetch = $pdo->prepare($sql_fetch);
        $stmt_fetch->execute([$id_khach_hang_hien_tai]);
        $profile = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

        if (!$profile) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy hồ sơ khách hàng.']);
            exit;
        }

        echo json_encode(['success' => true, 'profile' => $profile]);
        exit;
    }


    // =============================================
    // POST: CẬP NHẬT DỮ LIỆU
    // =============================================
    if ($method === 'POST') {
        $errors = [];
        
        // Tải dữ liệu hồ sơ hiện tại từ DB để so sánh và lấy ảnh cũ
        $stmt_old = $pdo->prepare("SELECT so_dien_thoai, email, hinh_anh FROM khach_hang WHERE id_khach_hang = ?");
        $stmt_old->execute([$id_khach_hang_hien_tai]);
        $profile_old = $stmt_old->fetch(PDO::FETCH_ASSOC);
        
        if (!$profile_old) {
             $errors[] = 'Không tìm thấy hồ sơ để cập nhật.';
        }
        
        // Lấy dữ liệu từ form (sử dụng $_POST vì enctype="multipart/form-data")
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
        $hinh_anh_path = $profile_old['hinh_anh'] ?? ''; // Mặc định là ảnh cũ

        // --- VALIDATION (Dựa trên code gốc của bạn) ---
        if ($ho_ten === '') $errors[] = 'Vui lòng nhập **Họ và tên**.';
        if ($so_dien_thoai_moi === '') $errors[] = 'Vui lòng nhập **Số điện thoại**.';
        if ($email_moi === '') $errors[] = 'Vui lòng nhập **Email**.';
        if ($ten_duong === '') $errors[] = 'Vui lòng nhập **Số nhà, Tên đường**.';
        if ($phuong_xa === '') $errors[] = 'Vui lòng nhập **Phường/Xã**.';
        if ($tinh_thanh === '') $errors[] = 'Vui lòng nhập **Tỉnh/Thành phố**.';
        if ($tuoi <= 0) $errors[] = 'Vui lòng nhập **Tuổi hợp lệ**.';
        if ($gioi_tinh === '') $errors[] = 'Vui lòng chọn **Giới tính**.';

        if (empty($errors)) {
            // 1. Kiểm tra SĐT hợp lệ
            if (!preg_match('/^[0-9]{10}$/', $so_dien_thoai_moi)) {
                $errors[] = 'Số điện thoại phải **đúng 10 chữ số** (ví dụ: 0912345678).';
            }
            // 2. Kiểm tra Email hợp lệ
            if (!filter_var($email_moi, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Email không hợp lệ. Vui lòng kiểm tra lại.';
            }
            // 3. Kiểm tra trùng lặp SĐT
            if ($so_dien_thoai_moi !== $profile_old['so_dien_thoai']) {
                $stmt_check_sdt = $pdo->prepare("SELECT 1 FROM khach_hang WHERE so_dien_thoai = ? AND id_khach_hang <> ?");
                $stmt_check_sdt->execute([$so_dien_thoai_moi, $id_khach_hang_hien_tai]);
                if ($stmt_check_sdt->fetch()) { $errors[] = 'Số điện thoại này đã được đăng ký cho tài khoản khác.'; }
            }
            // 4. Kiểm tra trùng lặp Email
            if ($email_moi !== $profile_old['email']) {
                $stmt_check_email = $pdo->prepare("SELECT 1 FROM khach_hang WHERE email = ? AND id_khach_hang <> ?");
                $stmt_check_email->execute([$email_moi, $id_khach_hang_hien_tai]);
                if ($stmt_check_email->fetch()) { $errors[] = 'Email này đã được đăng ký cho tài khoản khác.'; }
            }
        }
        
        // --- XỬ LÝ UPLOAD ẢNH (Dựa trên logic gốc) ---
        if (empty($errors) && isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
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
                    // SỬA LỖI: Chỉ lưu đường dẫn tương đối từ /Frontend
                    $new_hinh_anh_path = $base_url_path . $new_file_name; 
                    
                    // Xóa ảnh cũ nếu tồn tại
                    $old_path_on_disk = $base_dir . '/' . $profile_old['hinh_anh'];
                    if (!empty($profile_old['hinh_anh']) && file_exists($old_path_on_disk)) {
                         @unlink($old_path_on_disk); // @ để tránh lỗi nếu không xóa được
                    }
                    $hinh_anh_path = $new_hinh_anh_path;
                } else {
                    $errors[] = 'Lỗi khi upload file ảnh.';
                }
            }
        }

        if (!empty($errors)) {
            http_response_code(400); // Bad Request
            echo json_encode(['success' => false, 'message' => 'Vui lòng kiểm tra lại thông tin.', 'errors' => $errors]);
            exit;
        }

        // --- BƯỚC 4: CẬP NHẬT DỮ LIỆU VÀO DB ---
        $sql_update = "UPDATE khach_hang SET 
            ten_khach_hang = ?, so_dien_thoai = ?, email = ?,
            ten_duong = ?, phuong_xa = ?, tinh_thanh = ?, 
            tuoi = ?, gioi_tinh = ?, chieu_cao = ?, can_nang = ?,
            hinh_anh = ?
            WHERE id_khach_hang = ?"; 
        
        $stmt_update = $pdo->prepare($sql_update);
        
        $update_params = [
            $ho_ten, $so_dien_thoai_moi, $email_moi, 
            $ten_duong, $phuong_xa, $tinh_thanh, 
            $tuoi, $gioi_tinh, $chieu_cao, $can_nang, 
            $hinh_anh_path, // Đường dẫn đã sửa
            $id_khach_hang_hien_tai
        ];

        if ($stmt_update->execute($update_params)) {
            // Cập nhật lại Session sau khi update thành công 
            $_SESSION['ten_khach_hang'] = $ho_ten; 
            $_SESSION['so_dien_thoai'] = $so_dien_thoai_moi; 

            echo json_encode(['success' => true, 'message' => 'Cập nhật hồ sơ thành công! 🎉']);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Lỗi CSDL khi cập nhật.']);
        }
        exit;
    }

} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
    exit;
}
?>