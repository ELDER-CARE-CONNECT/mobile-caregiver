<?php
function connectdb() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sanpham";

    // Tạo kết nối
    $conn = new mysqli($servername, $username, $password, $dbname);
    
    // Đặt charset utf8 để không bị lỗi font tiếng Việt
    $conn->set_charset("utf8");

    // Kiểm tra kết nối
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }
    
    return $conn;
}
?>