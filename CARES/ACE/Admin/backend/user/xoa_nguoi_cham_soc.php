<?php
header('Content-Type: application/json; charset=utf-8');
include '../config/connect.php';
$conn = connectdb();

// Khởi tạo response
$response = ['status'=>'error', 'message'=>'', 'data'=>null];

// Lấy ID người chăm sóc từ GET
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id <= 0){
    $response['status'] = 'error';
    $response['message'] = "ID không hợp lệ!";
    echo json_encode($response);
    exit;
}

// Xóa người chăm sóc
$stmt = $conn->prepare("DELETE FROM nguoi_cham_soc WHERE id_cham_soc=?");
$stmt->bind_param("i", $id);

if($stmt->execute()){
    $response['status'] = 'success';
    $response['message'] = "Xóa người chăm sóc thành công!";
} else {
    $response['status'] = 'error';
    $response['message'] = "Lỗi khi xóa người chăm sóc: " . $conn->error;
}

$stmt->close();
$conn->close();

echo json_encode($response);
