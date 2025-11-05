<?php
$servername = "localhost";
$username = "root";      // mặc định XAMPP
$password = "";          // để trống nếu bạn không đặt mật khẩu
$dbname = "sanpham";
// Tạo kết nối
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
?>
