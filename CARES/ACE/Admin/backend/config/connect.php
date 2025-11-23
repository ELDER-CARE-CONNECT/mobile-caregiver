<?php
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
