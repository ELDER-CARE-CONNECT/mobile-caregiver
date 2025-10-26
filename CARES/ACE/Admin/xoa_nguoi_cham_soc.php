<?php
include 'connect.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // chống SQL injection

    // Xóa bản ghi trong bảng nguoi_cham_soc
    $sql = "DELETE FROM nguoi_cham_soc WHERE id_cham_soc = $id";

    if ($conn->query($sql)) {
        echo "<script>alert('✅ Xóa người chăm sóc thành công!'); window.location='nguoi_cham_soc.php';</script>";
    } else {
        echo "<script>alert('❌ Lỗi khi xóa người chăm sóc!'); window.location='nguoi_cham_soc.php';</script>";
    }
} else {
    echo "<script>window.location='nguoi_cham_soc.php';</script>";
}

$conn->close();
?>
