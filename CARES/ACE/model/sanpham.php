<?php
function getall_sp($iddm) {
    $conn = new mysqli("localhost", "root", "", "sanpham");

    if ($conn->connect_error) {
        echo "Lỗi kết nối: " . $conn->connect_error;
        return [];
    }

    $conn->set_charset("utf8");
    $sql = "SELECT * FROM san_pham WHERE 1";
    if($iddm>0){
        $sql .= " AND loai_san_pham = " . intval($iddm);
    }
    $result = $conn->query($sql);

    $data = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    $conn->close();
    return $data;
}

function getall_spgiam($iddm) {
    $conn = new mysqli("localhost", "root", "", "sanpham");

    if ($conn->connect_error) {
        echo "Lỗi kết nối: " . $conn->connect_error;
        return [];
    }

    $conn->set_charset("utf8");
    $sql = "SELECT * FROM san_pham WHERE gia_giam IS NOT NULL AND gia_giam > 0";
    if($iddm>0){
        $sql .= " AND loai_san_pham = " . intval($iddm);
    }
    $result = $conn->query($sql);

    $data = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    $conn->close();
    return $data;
}
?>


