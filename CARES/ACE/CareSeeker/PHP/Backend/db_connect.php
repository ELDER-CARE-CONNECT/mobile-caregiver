<?php
/**
 * DATABASE CONNECTION TỐI ƯU
 */

// CẤU HÌNH DATABASE
$host = 'db';
$dbname = 'caresdb';
$username = 'user';
$password = 'userpassword';
$charset = 'utf8mb4';

// DSN VÀ OPTIONS TỐI ƯU
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
    PDO::ATTR_PERSISTENT => false, // TẮT PERSISTENT ĐỂ TĂNG TỐC
];

function get_pdo_connection() {
    global $dsn, $username, $password, $options;
    
    try {
        return new PDO($dsn, $username, $password, $options);
    } catch (PDOException $e) {
        throw new PDOException("Lỗi kết nối database");
    }
}
?>