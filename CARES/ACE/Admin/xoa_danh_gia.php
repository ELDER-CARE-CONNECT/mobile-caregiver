<?php
include 'check_login.php';
include 'connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Xóa đánh giá theo ID
    $sql = "DELETE FROM danh_gia WHERE id_danh_gia = $id";
    
    if ($conn->query($sql)) {
        echo "<script>alert('✅ Xóa đánh giá thành công!'); window.location='danhgia.php';</script>";
    } else {
        echo "<script>alert('❌ Lỗi khi xóa đánh giá!'); window.location='danhgia.php';</script>";
    }
} else {
    echo "<script>alert('Không tìm thấy ID đánh giá!'); window.location='danhgia.php';</script>";
}

$conn->close();
?>
