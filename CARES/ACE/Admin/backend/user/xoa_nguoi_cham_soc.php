<?php
header('Content-Type: application/json; charset=utf-8');
include '../config/connect.php';
$conn = connectdb();

$response = ['success'=>false, 'message'=>'', 'data'=>null];

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id <= 0){
    $response['message'] = "ID không hợp lệ!";
    echo json_encode($response);
    exit;
}

// Xóa người chăm sóc
$stmt = $conn->prepare("DELETE FROM nguoi_cham_soc WHERE id_cham_soc=?");
$stmt->bind_param("i",$id);

if($stmt->execute()){
    $response['success'] = true;
    $response['message'] = "Xóa người chăm sóc thành công!";
}else{
    $response['message'] = "Lỗi khi xóa người chăm sóc!";
}

echo json_encode($response);
$conn->close();
