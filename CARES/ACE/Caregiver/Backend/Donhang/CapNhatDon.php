<?php
include_once('../../model/get_products.php');
$conn = connectdb();

$id_don_hang = $_POST['id_don_hang'] ?? 0;
$hanhdong    = $_POST['hanhdong'] ?? '';
$id_cham_soc = $_POST['id_cham_soc'] ?? 0;

if(!$id_don_hang || !$hanhdong || !$id_cham_soc){
    echo json_encode(['status'=>'error','message'=>'Thiếu dữ liệu']);
    exit();
}

if($hanhdong == 'nhan_don'){
    $trang_thai = 'đang hoàn thành';
} elseif($hanhdong == 'huy_don'){
    $trang_thai = 'đã hủy';
} else {
    echo json_encode(['status'=>'error','message'=>'Hành động không hợp lệ']);
    exit();
}

$sql = "UPDATE don_hang SET trang_thai = ? WHERE id_don_hang = ? AND id_nguoi_cham_soc = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii",$trang_thai,$id_don_hang,$id_cham_soc);
$success = $stmt->execute();

if($success){
    echo json_encode(['status'=>'success','message'=>'Cập nhật thành công']);
}else{
    echo json_encode(['status'=>'error','message'=>'Cập nhật thất bại']);
}
