<?php
// File: db_connect.php

$servername = "db";
$username   = "user";
$password   = "userpassword";
$dbname     = "caresdb";

// 1. KẾT NỐI MYSQLI (Cho Gateway và các file đơn giản)
$conn = mysqli_connect($servername, $username, $password, $dbname);
if (!$conn) {
    die(json_encode(['success' => false, 'message' => 'Database Connection Failed: ' . mysqli_connect_error()]));
}
mysqli_set_charset($conn, "utf8");

// 2. HÀM HỖ TRỢ PDO (Cho api_order_create, api_profile...)
function get_pdo_connection() {
    global $servername, $username, $password, $dbname;
    try {
        $dsn = "mysql:host=$servername;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        // Trả về lỗi JSON nếu kết nối thất bại
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'PDO Connection Error: ' . $e->getMessage()]);
        exit;
    }
}
?>