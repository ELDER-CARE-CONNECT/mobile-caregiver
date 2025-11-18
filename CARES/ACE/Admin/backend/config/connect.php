<?php
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
