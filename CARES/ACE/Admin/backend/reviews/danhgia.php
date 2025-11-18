<?php
session_start();
include '../config/connect.php'; // Sửa: Từ backend/reviews/ lên root/config/ (thêm ../ nếu cần)
$conn = connectdb();

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Thêm nếu CORS lỗi (e.g., frontend và backend khác domain)
ini_set('display_errors', 1);
error_reporting(E_ALL);

$keyword = $_GET['keyword'] ?? '';
$star = isset($_GET['star']) ? intval($_GET['star']) : 0;

try {
    $params = [];
    $types = '';

    // Câu truy vấn
    $sql = "SELECT d.id_danh_gia, kh.ten_khach_hang, ncs.ho_ten AS ten_cham_soc,
                   d.so_sao, d.nhan_xet, d.ngay_danh_gia
            FROM danh_gia d
            JOIN khach_hang kh ON d.id_khach_hang = kh.id_khach_hang
            JOIN nguoi_cham_soc ncs ON d.id_cham_soc = ncs.id_cham_soc
            WHERE 1=1";

    if(!empty($keyword)){
        $sql .= " AND (kh.ten_khach_hang LIKE ? OR ncs.ho_ten LIKE ? OR d.nhan_xet LIKE ?)";
        $like = "%$keyword%";
        $params = [$like,$like,$like];
        $types = 'sss';
    }

    if($star>0 && $star<=5){
        $sql .= " AND d.so_sao=?";
        $params[] = $star;
        $types .= 'i';
    }

    $sql .= " ORDER BY d.so_sao DESC, d.ngay_danh_gia DESC";

    $stmt = $conn->prepare($sql);

    if(!empty($params)){
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $reviews = [];
    while($row=$result->fetch_assoc()){
        $reviews[]=$row;
    }

    // Tính trung bình sao
    $params_avg = $params;
    $types_avg = $types;
    $avg_sql = "SELECT AVG(d.so_sao) AS avg_star
                FROM danh_gia d
                JOIN khach_hang kh ON d.id_khach_hang = kh.id_khach_hang
                JOIN nguoi_cham_soc ncs ON d.id_cham_soc = ncs.id_cham_soc
                WHERE 1=1";

    if(!empty($keyword)){
        $avg_sql .= " AND (kh.ten_khach_hang LIKE ? OR ncs.ho_ten LIKE ? OR d.nhan_xet LIKE ?)";
    }
    if($star>0 && $star<=5){
        $avg_sql .= " AND d.so_sao=?";
    }

    $stmt_avg = $conn->prepare($avg_sql);
    if(!empty($params_avg)){
        $stmt_avg->bind_param($types_avg, ...$params_avg);
    }
    $stmt_avg->execute();
    $avg_result = $stmt_avg->get_result()->fetch_assoc();
    $avg_star = $avg_result['avg_star'] ? round($avg_result['avg_star'],1) : 0;

    echo json_encode([
        'status'=>'success',
        'avg_star'=>$avg_star,
        'reviews'=>$reviews
    ], JSON_UNESCAPED_UNICODE);

} catch(Exception $e){
    echo json_encode([
        'status'=>'error',
        'message'=>$e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

$conn->close();
exit;
?>