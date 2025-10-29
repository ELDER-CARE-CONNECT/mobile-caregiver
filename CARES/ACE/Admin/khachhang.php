<?php include 'check_login.php'; ?>
<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lí Khách Hàng</title>
    <link rel="stylesheet" href="fontend/css/khachhang.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <style>
    /* ===== Tổng thể ===== */
    body {
        font-family: "Segoe UI", sans-serif;
        background-color: #f0f4f8;
        color: #333;
        margin: 0;
        padding: 0;
    }

    /* ===== Bố cục ===== */
    .container {
        display: flex;
        min-height: 100vh;
    }

    .main-content {
        flex-grow: 1;
        background: #fff;
        padding: 25px 40px;
        border-radius: 12px;
        margin: 20px;
        box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }

    /* ===== Thanh navbar ===== */
    .navbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 3px solid #3498db;
        padding-bottom: 15px;
        margin-bottom: 10px;
    }

    .navbar h1 {
        color: #3498db;
        font-size: 22px;
        font-weight: 600;
    }

    /* ===== Ô tìm kiếm ===== */
    .search input {
        padding: 7px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        width: 260px;
    }

    .search button {
        background: #3498db;
        color: white;
        border: none;
        padding: 7px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: 0.3s;
    }

    .search button:hover {
        background: #2980b9;
    }

    /* ===== Tiêu đề phụ ===== */
    h2 {
        color: #2c3e50;
        font-size: 20px;
        margin-bottom: 15px;
        border-left: 5px solid #3498db;
        padding-left: 10px;
    }

    /* ===== Bảng dữ liệu ===== */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 25px;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    th {
        background: #3498db;
        color: #fff;
        padding: 12px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 14px;
        text-align: center;
    }

    td {
        padding: 10px;
        border-bottom: 1px solid #eee;
        text-align: center;
        font-size: 15px;
        color: #2c3e50;
    }

    tr:nth-child(even) {
        background: #f9f9f9;
    }

    tr:hover {
        background: #eaf4ff;
        transition: 0.2s;
    }

    /* ===== Ảnh khách hàng ===== */
    img {
        width: 70px;
        height: 70px;
        border-radius: 8px;
        object-fit: cover;
        box-shadow: 0 0 5px rgba(0,0,0,0.1);
    }

    /* ===== Nút xem đơn hàng ===== */
    .show-orders {
        background-color: #3498db;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: 0.3s;
    }

    .show-orders:hover {
        background-color: #2980b9;
        transform: scale(1.05);
    }

    /* ===== Dòng chi tiết đơn hàng ===== */
    .order-details-row {
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .order-details-row table {
        width: 100%;
        border: 1px solid #ddd;
        margin-top: 8px;
        border-radius: 6px;
    }

    .order-details-row th {
        background-color: #5dade2;
        color: white;
        padding: 6px;
    }

    .order-details-row td {
        background-color: #fff;
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
                            $orderSummary = $conn->query("SELECT COUNT(*) AS tong_don, SUM(tong_tien) AS tong_tien FROM don_hang WHERE id_khach_hang = $id");
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

                            echo "<tr class='order-details-row' id='orders-{$id}' style='display:none;'>
                                    <td colspan='12'>
                                    <table>
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
        $("#orders-" + id).slideToggle(300);
    });
});
</script>
</body>
</html>
