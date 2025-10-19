<?php
include 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_don_hang = $_POST['id_don_hang'];
    $trang_thai = $_POST['trang_thai'];

    $sql = "UPDATE don_hang SET trang_thai = ? WHERE id_don_hang = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $trang_thai, $id_don_hang);

    if ($stmt->execute()) {
        header("Location: quanli.php"); // trở lại trang danh sách
        exit;
    } else {
        echo "Lỗi cập nhật: " . $conn->error;
    }
}
?>
