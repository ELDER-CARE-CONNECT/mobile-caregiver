<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id_khieu_nai'];
    $phanhoi = trim($_POST['phan_hoi']);

    if (!empty($id) && !empty($phanhoi)) {
        // Cập nhật phản hồi và trạng thái
        $sql = "UPDATE khieu_nai 
                SET phan_hoi = ?, trang_thai = 'Đã giải quyết'
                WHERE id_khieu_nai = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $phanhoi, $id);

        if ($stmt->execute()) {
            // Chuyển hướng lại trang quản lí
            header("Location: quanli_khieunai.php?success=1");
            exit;
        } else {
            echo "❌ Lỗi khi cập nhật phản hồi: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "⚠️ Thiếu dữ liệu gửi phản hồi.";
    }
}
$conn->close();
?>
