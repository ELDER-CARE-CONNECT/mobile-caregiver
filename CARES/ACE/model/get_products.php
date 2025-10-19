<?php
function connectdb() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "sanpham"; // ← Đổi tên CSDL thật của em vào đây

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");
    return $conn;
}
?>
