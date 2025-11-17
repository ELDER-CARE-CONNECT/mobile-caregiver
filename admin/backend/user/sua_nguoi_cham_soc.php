<?php
header('Content-Type: application/json; charset=utf-8');
include '../config/connect.php'; // Giả định đúng path
$conn = connectdb();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['id']) || ($id = intval($_GET['id'])) <= 0) {
        $response['message'] = 'ID người chăm sóc không hợp lệ!';
        echo json_encode($response);
        exit;
    }
    $stmt = $conn->prepare("SELECT * FROM nguoi_cham_soc WHERE id_cham_soc = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        $response['message'] = 'Không tìm thấy người chăm sóc!';
    } else {
        $row = $result->fetch_assoc();
        $response = ['success' => true, 'data' => $row];
    }
    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id']) || ($id = intval($_POST['id'])) <= 0) {
        $response['message'] = 'ID người chăm sóc không hợp lệ!';
        echo json_encode($response);
        exit;
    }
    $ho_ten = trim($_POST['ho_ten'] ?? '');
    $dia_chi = trim($_POST['dia_chi'] ?? '');
    $tuoi = intval($_POST['tuoi'] ?? 0);
    $gioi_tinh = trim($_POST['gioi_tinh'] ?? '');
    $chieu_cao = intval($_POST['chieu_cao'] ?? 0);
    $can_nang = intval($_POST['can_nang'] ?? 0);
    $kinh_nghiem = trim($_POST['kinh_nghiem'] ?? '');

    // Validation cơ bản
    if (empty($ho_ten) || strlen($ho_ten) > 100) {
        $response['message'] = 'Họ tên không hợp lệ (không rỗng, max 100 ký tự)!';
        echo json_encode($response);
        exit;
    }
    if ($tuoi <= 0 || $tuoi > 120) {
        $response['message'] = 'Tuổi phải từ 1 đến 120!';
        echo json_encode($response);
        exit;
    }
    if ($chieu_cao <= 0 || $chieu_cao > 300) {
        $response['message'] = 'Chiều cao không hợp lệ!';
        echo json_encode($response);
        exit;
    }
    if ($can_nang <= 0 || $can_nang > 500) {
        $response['message'] = 'Cân nặng không hợp lệ!';
        echo json_encode($response);
        exit;
    }

    // Lấy ảnh cũ
    $stmt = $conn->prepare("SELECT hinh_anh FROM nguoi_cham_soc WHERE id_cham_soc = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $hinh_anh = $row['hinh_anh'] ?? '';
    $stmt->close();

    // Upload ảnh mới nếu có
    if (!empty($_FILES['hinh_anh']['name'])) {
        $target_dir = "../../frontend/uploads/"; // Sửa: Lưu vào /frontend/uploads/
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $file_name = basename($_FILES["hinh_anh"]["name"]);
        $fileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!in_array($fileType, ['jpg', 'png', 'jpeg'])) {
            $response['message'] = 'Chỉ chấp nhận file JPG, PNG!';
            echo json_encode($response);
            exit;
        }
        if ($_FILES["hinh_anh"]["size"] > 2000000) { // 2MB
            $response['message'] = 'File quá lớn (max 2MB)!';
            echo json_encode($response);
            exit;
        }
        $file_name = time() . "_" . $file_name; // Đổi tên để tránh trùng
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_file)) {
            // Xóa ảnh cũ nếu có
            if (!empty($hinh_anh) && file_exists("../../" . $hinh_anh)) {
                unlink("../../" . $hinh_anh);
            }
            $hinh_anh = "frontend/uploads/" . $file_name;
        } else {
            $response['message'] = 'Lỗi upload ảnh!';
            echo json_encode($response);
            exit;
        }
    }

    // Cập nhật DB
    $stmt = $conn->prepare("UPDATE nguoi_cham_soc SET ho_ten=?, dia_chi=?, tuoi=?, gioi_tinh=?, chieu_cao=?, can_nang=?, kinh_nghiem=?, hinh_anh=? WHERE id_cham_soc=?");
    $stmt->bind_param("ssississi", $ho_ten, $dia_chi, $tuoi, $gioi_tinh, $chieu_cao, $can_nang, $kinh_nghiem, $hinh_anh, $id);
    if ($stmt->execute()) {
        $response = ['success' => true, 'message' => 'Cập nhật thành công!'];
    } else {
        $response['message'] = 'Lỗi khi cập nhật: ' . $conn->error;
    }
    $stmt->close();
} else {
    $response['message'] = 'Phương thức không được hỗ trợ!';
}

echo json_encode($response);
$conn->close();
?>
