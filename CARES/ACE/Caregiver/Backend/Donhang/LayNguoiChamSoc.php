<?php
include_once('../../model/get_products.php');
$conn = connectdb();

$so_dien_thoai = $_POST['so_dien_thoai'] ?? '';

if(!$so_dien_thoai){
    echo json_encode(['status'=>'error','message'=>'Chưa gửi số điện thoại']);
    exit();
}

$sql = "SELECT id_cham_soc, ho_ten FROM nguoi_cham_soc WHERE so_dien_thoai = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $so_dien_thoai);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows === 0){
    echo json_encode(['status'=>'error','message'=>'Không tìm thấy người chăm sóc']);
    exit();
}

$nguoiCS = $result->fetch_assoc();
echo json_encode(['status'=>'success','data'=>$nguoiCS]);
