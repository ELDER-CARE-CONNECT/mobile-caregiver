<?php
<<<<<<< HEAD
function connectdb() {
    // Thông tin kết nối MySQL trong Docker
    $servername = "db";           // tên service MySQL trong docker-compose
    $username = "user";           // từ docker-compose.yml
    $password = "userpassword";   // từ docker-compose.yml
    $dbname = "caresdb";          // database đã khai báo trong docker-compose

    // Kết nối
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Kiểm tra lỗi
    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    // Set charset UTF-8
    $conn->set_charset("utf8");

    return $conn;
}
?>
=======
// config/connect.php

function connectdb() {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "sanpham";

    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_errno) {
        return null; // ❌ không die(), trả về null để API xử lý lỗi
    }

    $conn->set_charset("utf8");
    return $conn;
}
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
