<?php
include_once('../../model/get_products.php');
$conn = connectdb();

$id_cham_soc = $_POST['id_cham_soc'] ?? 0;
if(!$id_cham_soc){
    echo json_encode(['status'=>'error','message'=>'Chưa gửi id_cham_soc']);
    exit();
}

// Lấy danh sách đơn hàng với tên khách hàng trực tiếp từ bảng don_hang
$sql = "SELECT id_don_hang, ten_khach_hang, ngay_dat, tong_tien, trang_thai
        FROM don_hang
        WHERE id_nguoi_cham_soc = ?
          AND trang_thai IN ('chờ xác nhận','đang hoàn thành')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_cham_soc);
$stmt->execute();
$result = $stmt->get_result();

$donhang = [];
while($row = $result->fetch_assoc()){
    $donhang[] = $row;
}

echo json_encode(['status'=>'success','data'=>$donhang]);
