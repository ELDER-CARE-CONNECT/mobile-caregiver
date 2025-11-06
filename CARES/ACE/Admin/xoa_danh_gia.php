<?php
<<<<<<< HEAD
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> Phong
=======
>>>>>>> origin/Thanh
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
<<<<<<< HEAD
<<<<<<< HEAD
=======
include 'connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Lấy id_cham_soc trước khi xóa
    $sql_get = "SELECT id_cham_soc FROM danh_gia WHERE id_danh_gia = '$id'";
    $result_get = mysqli_query($conn, $sql_get);
    if ($result_get && $row = mysqli_fetch_assoc($result_get)) {
        $id_cham_soc = $row['id_cham_soc'];

        // Xóa đánh giá
        $sql_delete = "DELETE FROM danh_gia WHERE id_danh_gia = '$id'";
        mysqli_query($conn, $sql_delete);

        // Cập nhật lại trung bình
        $sql_tb = "SELECT AVG(so_sao) AS tb FROM danh_gia WHERE id_cham_soc = '$id_cham_soc'";
        $result_tb = mysqli_query($conn, $sql_tb);
        $row_tb = mysqli_fetch_assoc($result_tb);
        $tb_moi = $row_tb['tb'] ? round($row_tb['tb'], 1) : 0;

        // Cập nhật sang bảng người chăm sóc
        $sql_update = "UPDATE nguoi_cham_soc SET danh_gia_tb = '$tb_moi' WHERE id_cham_soc = '$id_cham_soc'";
        mysqli_query($conn, $sql_update);
    }

    header("Location: danhgia.php");
    exit;
}
>>>>>>> Vy
=======
>>>>>>> Phong
=======
>>>>>>> origin/Thanh
?>
