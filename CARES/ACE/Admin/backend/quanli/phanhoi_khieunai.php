<?php
// phanhoi_khieunai.php
include __DIR__ . '/../config/connect.php';
$conn = connectdb();

header('Content-Type: application/json; charset=utf-8');

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id_khieu_nai'] ?? 0);
    $phanhoi = trim($_POST['phan_hoi'] ?? '');

    if ($id > 0 && !empty($phanhoi)) {
        $sql = "UPDATE khieu_nai 
                SET phan_hoi = ?, trang_thai = 'Đã giải quyết'
                WHERE id_khieu_nai = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $phanhoi, $id);

        if ($stmt->execute()) {
            $response = ['status' => 'success', 'message' => 'Phản hồi đã được gửi thành công!'];
        } else {
            $response['message'] = 'Lỗi khi cập nhật phản hồi: ' . $conn->error;
        }
        $stmt->close();
    } else {
        $response['message'] = 'Thiếu dữ liệu phản hồi hoặc ID không hợp lệ.';
    }
}

$conn->close();
echo json_encode($response);
exit;
?>
