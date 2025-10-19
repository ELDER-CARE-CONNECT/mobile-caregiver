<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Gọi file kết nối
if (file_exists('connect.php')) {
    include 'connect.php';
} else {
    die("Không tìm thấy file connect.php");
}

// Kiểm tra biến kết nối
if (!isset($conn)) {
    die("Biến \$conn chưa được khởi tạo.");
}

// Kiểm tra ID
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Lấy đường dẫn hình ảnh của sản phẩm
    $sql_img = "SELECT hinh_anh FROM san_pham WHERE id_san_pham = $id";
    $result_img = $conn->query($sql_img);

    if ($result_img && $result_img->num_rows > 0) {
        $row = $result_img->fetch_assoc();
        $image_path = $row['hinh_anh'];

        // Xóa sản phẩm khỏi CSDL
        $sql_delete = "DELETE FROM san_pham WHERE id_san_pham = $id";
        if ($conn->query($sql_delete) === TRUE) {
            // Nếu ảnh tồn tại thì xóa luôn
            if (!empty($image_path) && file_exists($image_path)) {
                unlink($image_path); // Xóa file ảnh khỏi thư mục uploads
            }
            header("Location: sanpham.php?msg=deleted");
            exit();
        } else {
            header("Location: sanpham.php?msg=error");
            exit();
        }
    } else {
        header("Location: sanpham.php?msg=not_found");
        exit();
    }
} else {
    header("Location: sanpham.php?msg=no_id");
    exit();
}

$conn->close();
?>
