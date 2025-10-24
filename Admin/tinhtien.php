<?php
// Kết nối cơ sở dữ liệu
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "iphone_store";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Cập nhật cột 'tong_tien' trong bảng 'chi_tiet_don_hang'
$id_don_hang = 1;  // Ví dụ ID đơn hàng
$sql_update = "
    UPDATE chi_tiet_don_hang ctdh
    JOIN san_pham sp ON ctdh.id_san_pham = sp.id_san_pham
    SET ctdh.tong_tien = sp.gia * ctdh.so_luong
    WHERE ctdh.id_don_hang = ?";
$stmt = $conn->prepare($sql_update);
$stmt->bind_param("i", $id_don_hang);
$stmt->execute();

// Truy vấn lại để lấy tất cả các chi tiết đơn hàng mới nhất (bao gồm tổng tiền)
$sql_select = "
    SELECT 
        ctdh.id_don_hang,
        sp.ten_san_pham,
        ctdh.so_luong,
        sp.gia,
        ctdh.tong_tien
    FROM chi_tiet_don_hang ctdh
    JOIN san_pham sp ON ctdh.id_san_pham = sp.id_san_pham
    WHERE ctdh.id_don_hang = ?";
$stmt_select = $conn->prepare($sql_select);
$stmt_select->bind_param("i", $id_don_hang);
$stmt_select->execute();
$result = $stmt_select->get_result();

// Hiển thị dữ liệu chi tiết đơn hàng
$total_price = 0; // Tổng cộng tiền
echo "<h2>Chi tiết đơn hàng</h2>";
while ($row = $result->fetch_assoc()) {
    echo "Sản phẩm: " . $row['ten_san_pham'] . "<br>";
    echo "Số lượng: " . $row['so_luong'] . "<br>";
    echo "Đơn giá: " . $row['gia'] . " VND<br>";
    echo "Tổng tiền: " . $row['tong_tien'] . " VND<br><br>";
    
    // Cộng dồn vào tổng tiền của đơn hàng
    $total_price += $row['tong_tien'];
}

// Hiển thị tổng tiền toàn bộ đơn hàng
echo "<h3>Tổng tiền đơn hàng: " . $total_price . " VND</h3>";

$conn->close();
?>
