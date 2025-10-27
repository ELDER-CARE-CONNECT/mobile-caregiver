<?php include 'check_login.php'; ?>
<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lí Khách Hàng</title>
    <link rel="stylesheet" href="fontend/css/khachhang.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <style>
       body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background-color: #f5f7fb;
    color: #222;
    margin: 0;
    padding: 0;
}

.main-content {
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    padding: 20px;
    margin: 20px;
}

h1 {
    color: #1a237e;
    font-size: 26px;
    margin-bottom: 10px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

h2 {
    color: #333;
    font-size: 20px;
    margin-bottom: 15px;
    border-left: 5px solid #007bff;
    padding-left: 8px;
}

/* Bảng khách hàng */
table {
    border-collapse: collapse;
    width: 98%;
    margin: 0 auto;
    background-color: #ffffff;
    border-radius: 10px;
    overflow: hidden;
}

th {
    background-color: #007bff;
    color: #100e0eff;
    font-weight: bold;
    padding: 10px;
    text-align: center;
    font-size: 15px;
    border-bottom: 3px solid #0056b3;
}

td {
    padding: 10px;
    text-align: center;
    border-bottom: 1px solid #ddddddff;
    color: #222;
    font-size: 15px;
}

tr:hover {
    background-color: #f0f8ff;
    transition: background 0.3s;
}

/* Ảnh khách hàng */
img {
    width: 80px;
    height: 80px;
    border-radius: 6px;
    object-fit: cover;
}


/* Nút xem đơn hàng */
.show-orders {
    background-color: #28a745;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    font-weight: bold;
    transition: all 0.3s ease;
}

.show-orders:hover {
    background-color: #218838;
    transform: scale(1.05);
}

/* Thanh tìm kiếm */
.search input {
    padding: 8px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    outline: none;
    transition: all 0.2s;
}

.search input:focus {
    border-color: #007bff;
    box-shadow: 0 0 4px rgba(0,123,255,0.4);
}

.search button {
    padding: 8px 12px;
    border: none;
    background: #007bff;
    color: white;
    border-radius: 6px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
}

.search button:hover {
    background-color: #0056b3;
}

/* Dòng chi tiết đơn hàng */
.order-details-row {
    background-color: #f8f9fa;
}

.order-details-row table {
    background: #fff;
    border: 1px solid #3e2020ff;
    width: 100%;
}

.order-details-row th {
    background-color: #6c757d;
    color: white;
}
    </style>
</head>
<body>
<div class="container">
    <?php 
    $activePage = 'khachhang'; 
    $pageTitle = 'Quản Lí Khách Hàng';
    include 'sidebar.php'; 
    ?>

    <main class="main-content">
        <header class="navbar">
            <h1>Trang Quản Lí Khách Hàng</h1>
            <div class="search">
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Tìm kiếm khách hàng..." 
                           value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                    <button type="submit">🔍</button>
                </form>
            </div>
        </header>

        <section class="stats">
            <h2>Thông Tin Khách Hàng</h2>
            <div id="customer-info" class="customer-info">
                <table>
                    <tr>
                        <th>Mã KH</th>
                        <th>Hình ảnh</th>
                        <th>Họ và tên</th>
                        <th>Địa chỉ</th>
                        <th>Số điện thoại</th>
                        <th>Tuổi</th>
                        <th>Giới tính</th>
                        <th>Chiều cao (cm)</th>
                        <th>Cân nặng (kg)</th>
                        <th>Tổng đơn hàng</th>
                        <th>Tổng chi tiêu (VNĐ)</th>
                        <th>Đơn chi tiết</th>
                    </tr>

                    <?php
                    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
                    $sql = "SELECT * FROM khach_hang";
                    if ($searchTerm != '') {
                        $searchTerm = "%" . $conn->real_escape_string($searchTerm) . "%";
                        $sql .= " WHERE ten_khach_hang LIKE '$searchTerm'";
                    }

                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $id = $row['id_khach_hang'];

                            $orderSummary = $conn->query("SELECT COUNT(*) AS tong_don, SUM(tong_tien) AS tong_tien 
                                                          FROM don_hang WHERE id_khach_hang = $id");
                            $summary = $orderSummary->fetch_assoc();
                            $tong_don = $summary['tong_don'] ?? 0;
                            $tong_tien = $summary['tong_tien'] ?? 0;

                            echo "<tr>";
                            echo "<td>{$id}</td>";
                            echo "<td>";
                            if (!empty($row['hinh_anh'])) {
                                echo "<img src='uploads/{$row['hinh_anh']}' alt='Ảnh KH'>";
                            } else {
                                echo "<img src='uploads/default.png' alt='No Image'>";
                            }
                            echo "</td>";
                            echo "<td>{$row['ten_khach_hang']}</td>";
                            echo "<td>" . (!empty($row['dia_chi']) ? $row['dia_chi'] : '—') . "</td>";
                            echo "<td>{$row['so_dien_thoai']}</td>";
                            echo "<td>" . (!empty($row['tuoi']) ? $row['tuoi'] : '—') . "</td>";
                            echo "<td>" . (!empty($row['gioi_tinh']) ? $row['gioi_tinh'] : '—') . "</td>";
                            echo "<td>" . (!empty($row['chieu_cao']) ? $row['chieu_cao'] : '—') . "</td>";
                            echo "<td>" . (!empty($row['can_nang']) ? $row['can_nang'] : '—') . "</td>";
                            echo "<td>{$tong_don}</td>";
                            echo "<td>" . number_format($tong_tien, 0, ',', '.') . "</td>";
                            echo "<td><button class='show-orders' data-id='{$id}'>Xem đơn hàng</button></td>";
                            echo "</tr>";

                            // Chi tiết đơn hàng (ĐÃ CHỈNH)
                            echo "<tr class='order-details-row' id='orders-{$id}' style='display:none;'>
                                    <td colspan='12'>
                                    <table border='1' cellpadding='4' cellspacing='0'>
                                        <tr>
                                            <th>Mã đơn hàng</th>
                                            <th>Ngày đặt</th>
                                            <th>Tên khách hàng</th>
                                            <th>Tên người chăm sóc</th>
                                            <th>Thời gian làm việc</th>
                                            <th>Giá tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Đánh giá</th>
                                        </tr>";
                            $sqlOrders = "SELECT * FROM don_hang WHERE id_khach_hang = $id";
                            $orders = $conn->query($sqlOrders);
                            if ($orders && $orders->num_rows > 0) {
                                while($order = $orders->fetch_assoc()) {
                                    echo "<tr>
                                            <td>{$order['id_don_hang']}</td>
                                            <td>{$order['ngay_dat']}</td>
                                            <td>{$order['ten_khach_hang']}</td>
                                            <td>" . (!empty($order['ten_nguoi_cham_soc']) ? $order['ten_nguoi_cham_soc'] : '—') . "</td>
                                            <td>" . (!empty($order['thoi_gian_lam_viec']) ? $order['thoi_gian_lam_viec'] : '—') . "</td>
                                            <td>" . number_format($order['tong_tien'], 0, ',', '.') . "</td>
                                            <td>" . (!empty($order['trang_thai']) ? $order['trang_thai'] : '—') . "</td>
                                            <td>" . (!empty($order['danh_gia']) ? $order['danh_gia'] : '—') . "</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='8' style='text-align:center;'>Không có đơn hàng nào</td></tr>";
                            }
                            echo "</table></td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='12' style='text-align:center;'>Không có khách hàng nào</td></tr>";
                    }
                    $conn->close();
                    ?>
                </table>
            </div>
        </section>
    </main>
</div>

<script>
$(document).ready(function(){
    $(".show-orders").click(function(){
        const id = $(this).data("id");
        $("#orders-" + id).toggle(300);
    });
});
</script>
</body>
</html>
