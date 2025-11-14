<?php
session_start();
header('Content-Type: application/json');

include_once("connect.php"); 

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['email'])) {
    echo json_encode(['success' => false, 'message' => 'Thiếu email từ Google.']);
    exit;
}

$ten_gg = $data['ten_khach_hang'];
$email_gg = $data['email'];
$hinh_anh_gg = $data['hinh_anh'];

try {
    $sql_check = "SELECT * FROM khach_hang WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email_gg);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    $user_db = $result->fetch_assoc();

    $id_khach_hang_to_login = null;
    $ten_khach_hang_to_login = null;
    $sdt_to_login = null;

    if ($user_db) {
        
        $id_khach_hang_to_login = $user_db['id_khach_hang'];
        $ten_khach_hang_to_login = $user_db['ten_khach_hang'];
        $sdt_to_login = $user_db['so_dien_thoai']; 

        $anh_hien_tai = $user_db['hinh_anh'];

        $la_anh_custom_upload = false;
        if (!empty($anh_hien_tai) && str_contains($anh_hien_tai, 'uploads/avatars/')) {
            $la_anh_custom_upload = true;
        }

        if (!$la_anh_custom_upload) {
            $sql_update_pic = "UPDATE khach_hang SET hinh_anh = ? WHERE id_khach_hang = ?";
            $stmt_update_pic = $conn->prepare($sql_update_pic);
            $stmt_update_pic->bind_param("si", $hinh_anh_gg, $id_khach_hang_to_login);
            $stmt_update_pic->execute();
        }

    } else {
        
        $sql_insert = "INSERT INTO khach_hang (ten_khach_hang, email, hinh_anh, mat_khau) 
                       VALUES (?, ?, ?, NULL)";
        
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("sss", $ten_gg, $email_gg, $hinh_anh_gg);
        $stmt_insert->execute();
        
        $id_khach_hang_to_login = $conn->insert_id;
        $ten_khach_hang_to_login = $ten_gg;
        $sdt_to_login = null; 
    }
    session_regenerate_id(true); 
    
    $_SESSION['role'] = 'khach_hang';
    $_SESSION['id_khach_hang'] = $id_khach_hang_to_login;
    $_SESSION['ten_khach_hang'] = $ten_khach_hang_to_login;
    $_SESSION['so_dien_thoai'] = $sdt_to_login; 
    
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Lỗi cơ sở dữ liệu. Vui lòng thử lại.']);
}
?>