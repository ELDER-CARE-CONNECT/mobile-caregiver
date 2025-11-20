<?php
session_start();
header('Content-Type: application/json');
include_once('../../../model/get_products.php');
$conn = connectdb();

// Kiểm tra đăng nhập
if (!isset($_SESSION['so_dien_thoai'])) {
    echo json_encode(['error'=>'Bạn chưa đăng nhập']);
    exit();
}

// Lấy id_don_hang từ POST hoặc GET
$id_don_hang = intval($_POST['id_don_hang'] ?? $_GET['id_don_hang'] ?? 0);
if(!$id_don_hang){
    echo json_encode(['error'=>'Không có đơn hàng nào được chọn!']);
    exit();
}

// Xử lý POST hành động
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // Hoàn thành nhiệm vụ
    if($action === 'hoan_thanh_nhiem_vu'){
        $id_nhiem_vu = intval($_POST['id_nhiem_vu'] ?? 0);
        if($id_nhiem_vu){
            $sql = "UPDATE nhiem_vu SET trang_thai_nhiem_vu='đã hoàn thành' WHERE id_nhiem_vu=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_nhiem_vu);
            $stmt->execute();
            $stmt->close();
        }
        echo json_encode(['status'=>'success']);
        exit();
    }

    // Xác nhận đơn / hoàn thành đơn
    if($action === 'xac_nhan_don'){
        $sql_get = "SELECT trang_thai FROM don_hang WHERE id_don_hang=?";
        $stmt_get = $conn->prepare($sql_get);
        $stmt_get->bind_param("i", $id_don_hang);
        $stmt_get->execute();
        $res_get = $stmt_get->get_result()->fetch_assoc();
        $stmt_get->close();

        $trangthai = $res_get['trang_thai'];

        // Kiểm tra tất cả nhiệm vụ đã hoàn thành chưa
        $sql_check = "SELECT COUNT(*) AS pending FROM nhiem_vu WHERE id_don_hang=? AND trang_thai_nhiem_vu='chờ xác nhận'";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("i", $id_don_hang);
        $stmt_check->execute();
        $pending = $stmt_check->get_result()->fetch_assoc()['pending'];
        $stmt_check->close();

        if ($trangthai == 'chờ xác nhận') {
            $new_status = 'đang hoàn thành';
        } elseif ($trangthai == 'đang hoàn thành') {
            if ($pending > 0) {
                echo json_encode(['status'=>'error','message'=>'Vui lòng hoàn thành tất cả nhiệm vụ trước khi hoàn thành đơn!']);
                exit();
            }
            $new_status = 'đã hoàn thành';
        } else {
            $new_status = $trangthai;
        }

        $sql = "UPDATE don_hang SET trang_thai=? WHERE id_don_hang=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $new_status, $id_don_hang);
        $stmt->execute();
        $stmt->close();

        echo json_encode(['status'=>'success','trang_thai'=>$new_status]);
        exit();
    }

    // Hủy đơn
    if($action === 'huy_don'){
        $sql = "UPDATE don_hang SET trang_thai='đã hủy' WHERE id_don_hang=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_don_hang);
        $stmt->execute();
        $stmt->close();
        echo json_encode(['status'=>'success']);
        exit();
    }
}

// Lấy thông tin đơn hàng
$sql = "SELECT * FROM don_hang WHERE id_don_hang=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_don_hang);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0){
    echo json_encode(['error'=>'Đơn hàng không tồn tại!']);
    exit();
}
$donhang = $result->fetch_assoc();
$stmt->close();

// Lấy danh sách nhiệm vụ từ bảng nhiem_vu
$sql_nv = "SELECT id_nhiem_vu, ten_nhiem_vu, trang_thai_nhiem_vu FROM nhiem_vu WHERE id_don_hang=?";
$stmt_nv = $conn->prepare($sql_nv);
$stmt_nv->bind_param("i", $id_don_hang);
$stmt_nv->execute();
$result_nv = $stmt_nv->get_result();
$nhiem_vu = [];
while($row = $result_nv->fetch_assoc()){
    $nhiem_vu[] = $row;
}
$stmt_nv->close();

$conn->close();

// Trả JSON gồm đơn hàng và danh sách nhiệm vụ
echo json_encode([
    'donhang' => $donhang,
    'nhiem_vu' => $nhiem_vu
]);
