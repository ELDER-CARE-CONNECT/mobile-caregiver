<?php
// backend/reviews/edit_review.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Chỉ test, thay bằng domain cụ thể khi deploy

include __DIR__ . '/../config/connect.php';
$conn = connectdb();

$method = $_SERVER['REQUEST_METHOD'];

// ===== GET: Lấy dữ liệu đánh giá =====
if ($method === 'GET') {
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID đánh giá không hợp lệ']);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM danh_gia WHERE id_danh_gia = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đánh giá']);
    } else {
        $row = $result->fetch_assoc();
        echo json_encode(['status' => 'success', 'data' => $row]);
    }

    $stmt->close();
}

// ===== POST: Cập nhật đánh giá =====
elseif ($method === 'POST') {
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $so_sao = isset($_POST['so_sao']) ? intval($_POST['so_sao']) : 0;
    $nhan_xet = isset($_POST['nhan_xet']) ? trim($_POST['nhan_xet']) : '';

    if ($id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'ID đánh giá không hợp lệ']);
        exit;
    }
    if ($so_sao < 1 || $so_sao > 5) {
        echo json_encode(['status' => 'error', 'message' => 'Số sao phải từ 1 đến 5']);
        exit;
    }
    if (empty($nhan_xet) || strlen($nhan_xet) > 500) {
        echo json_encode(['status' => 'error', 'message' => 'Nhận xét không được rỗng và tối đa 500 ký tự']);
        exit;
    }

    // Kiểm tra tồn tại
    $stmt_check = $conn->prepare("SELECT id_danh_gia FROM danh_gia WHERE id_danh_gia = ?");
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy đánh giá']);
        exit;
    }
    $stmt_check->close();

    // Cập nhật
    $stmt = $conn->prepare("UPDATE danh_gia SET so_sao = ?, nhan_xet = ? WHERE id_danh_gia = ?");
    $stmt->bind_param("isi", $so_sao, $nhan_xet, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Cập nhật đánh giá thành công']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Lỗi khi cập nhật: ' . $conn->error]);
    }

    $stmt->close();
}

// ===== Phương thức khác =====
else {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không được hỗ trợ']);
}

$conn->close();
exit;
?>
