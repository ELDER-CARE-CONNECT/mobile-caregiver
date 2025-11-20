<?php
function connectdb() {
    $servername = "db";          // Tên service MySQL trong docker-compose
    $username = "user";          // user docker-compose
    $password = "userpassword";  // password docker-compose
    $dbname = "caresdb";         // database docker-compose

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Kết nối thất bại: " . $conn->connect_error);
    }

    $conn->set_charset("utf8");
    return $conn;
}
?>
