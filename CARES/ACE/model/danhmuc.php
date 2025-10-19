<?php
function getall_dm() {
    $conn = new mysqli("localhost", "root", "", "sanpham");

    if ($conn->connect_error) {
        echo "Lỗi kết nối: " . $conn->connect_error;
        return [];
    }

    $conn->set_charset("utf8");
    $sql = "SELECT * FROM loai_san_pham";

    $result = $conn->query("SELECT * FROM loai_san_pham");

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
