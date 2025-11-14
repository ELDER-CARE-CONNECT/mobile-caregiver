<?php

$host = '127.0.0.1'; 
$dbname = 'sanpham';
$username = 'root';
$password = ''; 
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

/**
 * Trả về một kết nối PDO.
 * @return PDO
 * @throws PDOException
 */
function get_pdo_connection() {
    global $dsn, $username, $password, $options;
    return new PDO($dsn, $username, $password, $options);
}
?>