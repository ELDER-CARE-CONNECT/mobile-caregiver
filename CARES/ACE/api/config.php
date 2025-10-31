<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

include "../Admin/connect.php"; // Đường dẫn kết nối CSDL từ thư mục ACE

$API_TOKEN = "ELDER_CARE_CONNECT_2025";

function response($status, $message, $data = null){
    echo json_encode([
        "status" => $status,
        "message" => $message,
        "data" => $data
    ], JSON_PRETTY_PRINT);
    exit();
}

function checkToken(){
    global $API_TOKEN;
    if(!isset($_GET['token']) || $_GET['token'] !== $API_TOKEN){
        response("error", "Sai hoặc thiếu token truy cập!");
    }
}
