<?php
header('Content-Type: application/json; charset=utf-8');
include __DIR__ . '/../config/connect.php';
$conn = connectdb();

$response = ['success' => false, 'data' => [], 'message' => ''];

$sql = "SELECT kn.*, kh.ten_khach_hang 
        FROM khieu_nai kn
        JOIN khach_hang kh ON kn.id_khach_hang = kh.id_khach_hang
        ORDER BY kn.id_khieu_nai DESC";
$result = $conn->query($sql);

if ($result) {
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    $response['success'] = true;
    $response['data'] = $data;
} else {
    $response['message'] = $conn->error;
}

$conn->close();
echo json_encode($response);
