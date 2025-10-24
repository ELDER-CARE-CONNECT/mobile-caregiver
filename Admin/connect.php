<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "sanpham";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
} else {
    echo "";
}
?>
