<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

include '../config/connect.php';  // Giả sử connect.php ở backend/config/
$conn = connectdb();

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {  // Dùng POST thay GET để an toàn hơn
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    if ($id <= 0) {
        $response['message'] = 'ID đánh giá không hợp lệ';
        echo json_encode($response);
        exit;
    }

    // Lấy id_cham_soc trước khi xóa
    $stmt_get = $conn->prepare("SELECT id_cham_soc FROM danh_gia WHERE id_danh_gia = ?");
    $stmt_get->bind_param("i", $id);
    $stmt_get->execute();
    $result_get = $stmt_get->get_result();
    if ($result_get->num_rows === 0) {
        $response['message'] = 'Đánh giá không tồn tại';
        echo json_encode($response);
        exit;
    }
    $row = $result_get->fetch_assoc();
    $id_cham_soc = $row['id_cham_soc'];

    // Xóa đánh giá
    $stmt_delete = $conn->prepare("DELETE FROM danh_gia WHERE id_danh_gia = ?");
    $stmt_delete->bind_param("i", $id);
    if (!$stmt_delete->execute()) {
        $response['message'] = 'Lỗi khi xóa đánh giá: ' . $conn->error;
        echo json_encode($response);
        exit;
    }

    // Cập nhật lại trung bình sao
    $stmt_avg = $conn->prepare("SELECT COALESCE(AVG(so_sao), 0) AS tb FROM danh_gia WHERE id_cham_soc = ?");
    $stmt_avg->bind_param("i", $id_cham_soc);
    $stmt_avg->execute();
    $result_avg = $stmt_avg->get_result();
    $row_avg = $result_avg->fetch_assoc();
    $tb_moi = round($row_avg['tb'], 1);

    // Cập nhật bảng nguoi_cham_soc
    $stmt_update = $conn->prepare("UPDATE nguoi_cham_soc SET danh_gia_tb = ? WHERE id_cham_soc = ?");
    $stmt_update->bind_param("di", $tb_moi, $id_cham_soc);
    if ($stmt_update->execute()) {
        $response = ['success' => true, 'message' => 'Xóa đánh giá thành công và cập nhật trung bình'];
    } else {
        $response['message'] = 'Lỗi khi cập nhật trung bình: ' . $conn->error;
    }

    $stmt_get->close();
    $stmt_delete->close();
    $stmt_avg->close();
    $stmt_update->close();
}

$conn->close();
echo json_encode($response);
exit;
?>