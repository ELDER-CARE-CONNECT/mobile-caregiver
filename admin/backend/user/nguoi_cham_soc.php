<?php
header('Content-Type: application/json; charset=utf-8');
include '../config/connect.php';
$conn = connectdb();

$response = ['success'=>false, 'data'=>null, 'message'=>''];

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

try {
    if ($id > 0) {
        // Lấy 1 người chăm sóc
        $stmt = $conn->prepare("SELECT * FROM nguoi_cham_soc WHERE id_cham_soc=?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res->num_rows === 0) {
            echo json_encode(['success'=>false,'message'=>'Không tìm thấy người chăm sóc!']);
            exit;
        }
        $row = $res->fetch_assoc();

        // Lấy đánh giá trung bình
        $stmtAvg = $conn->prepare("SELECT COALESCE(AVG(so_sao),0) AS danh_gia_tb FROM danh_gia WHERE id_cham_soc=?");
        $stmtAvg->bind_param("i", $id);
        $stmtAvg->execute();
        $avg = $stmtAvg->get_result()->fetch_assoc();
        $row['danh_gia_tb'] = $avg['danh_gia_tb'];

        // Lấy review chi tiết
        $stmtRev = $conn->prepare("
            SELECT khach_hang.ten_khach_hang, dg.so_sao, dg.nhan_xet, dg.ngay_danh_gia
            FROM danh_gia dg
            JOIN khach_hang ON dg.id_khach_hang = khach_hang.id_khach_hang
            WHERE dg.id_cham_soc=?
        ");
        $stmtRev->bind_param("i", $id);
        $stmtRev->execute();
        $resRev = $stmtRev->get_result();
        $reviews = [];
        while ($r = $resRev->fetch_assoc()) $reviews[] = $r;
        $row['reviews'] = $reviews;

        // Đảm bảo hinh_anh có giá trị string
        $row['hinh_anh'] = $row['hinh_anh'] ?? '';

        echo json_encode(['success'=>true,'data'=>$row]);
        exit;
    } else {
        // Lấy danh sách người chăm sóc
        $keyword = $_GET['keyword'] ?? '';
        $sql = "
            SELECT ncs.*, COALESCE(AVG(dg.so_sao),0) AS danh_gia_tb
            FROM nguoi_cham_soc ncs
            LEFT JOIN danh_gia dg ON ncs.id_cham_soc = dg.id_cham_soc
            WHERE ncs.ho_ten LIKE ? OR ncs.dia_chi LIKE ? OR ncs.gioi_tinh LIKE ? OR ncs.kinh_nghiem LIKE ?
            GROUP BY ncs.id_cham_soc
            ORDER BY ncs.id_cham_soc
        ";
        $stmt = $conn->prepare($sql);
        $kw = "%$keyword%";
        $stmt->bind_param("ssss", $kw, $kw, $kw, $kw);
        $stmt->execute();
        $res = $stmt->get_result();

        $caregivers = [];
        while ($row = $res->fetch_assoc()) {
            $stmtRev = $conn->prepare("
                SELECT khach_hang.ten_khach_hang, dg.so_sao, dg.nhan_xet, dg.ngay_danh_gia
                FROM danh_gia dg
                JOIN khach_hang ON dg.id_khach_hang = khach_hang.id_khach_hang
                WHERE dg.id_cham_soc=?
            ");
            $stmtRev->bind_param("i", $row['id_cham_soc']);
            $stmtRev->execute();
            $resRev = $stmtRev->get_result();
            $reviews = [];
            while ($r = $resRev->fetch_assoc()) $reviews[] = $r;
            $row['reviews'] = $reviews;
            $row['hinh_anh'] = $row['hinh_anh'] ?? '';
            $caregivers[] = $row;
        }

        echo json_encode(['success'=>true,'data'=>$caregivers]);
    }

} catch(Exception $e) {
    echo json_encode(['success'=>false,'message'=>$e->getMessage()]);
}

$conn->close();
