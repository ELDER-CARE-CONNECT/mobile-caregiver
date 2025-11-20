<?php
header('Content-Type: application/json; charset=utf-8');
include '../config/connect.php';
$conn = connectdb();

<<<<<<< HEAD
// Khởi tạo response
$response = ['status'=>'error', 'message'=>'', 'data'=>null];

// Lấy ID người chăm sóc từ GET
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id <= 0){
    $response['status'] = 'error';
=======
$response = ['success'=>false, 'message'=>'', 'data'=>null];

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if($id <= 0){
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
    $response['message'] = "ID không hợp lệ!";
    echo json_encode($response);
    exit;
}

// Xóa người chăm sóc
$stmt = $conn->prepare("DELETE FROM nguoi_cham_soc WHERE id_cham_soc=?");
<<<<<<< HEAD
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
=======
$stmt->bind_param("i",$id);

if($stmt->execute()){
    $response['success'] = true;
    $response['message'] = "Xóa người chăm sóc thành công!";
}else{
    $response['message'] = "Lỗi khi xóa người chăm sóc!";
}

echo json_encode($response);
$conn->close();
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
