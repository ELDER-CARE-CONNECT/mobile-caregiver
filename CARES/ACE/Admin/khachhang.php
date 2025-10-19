<?php include 'check_login.php'; ?>
<?php include 'connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lí Khách Hàng</title>
    <link rel="stylesheet" href="fontend/css/khachhang.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="fontend/js/khachhang.js"></script>
    <style>
        .order-details {
            display: none;
            background-color: #f9f9f9;
        }
        .order-details td {
            padding: 5px 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <?php 
    $activePage = 'khachhang'; 
    $pageTitle = 'Quản Lí Khách hàng';
    include 'sidebar.php'; 
    ?>

    <main class="main-content">
        <header class="navbar">
            <h1>Trang Quản Lí Khách Hàng</h1>
            <div class="search">
                <form method="GET" action="">
                    <input type="text" name="search" placeholder="Tìm kiếm khách hàng..." value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                    <button type="submit">🔍</button>
                </form>
            </div>
        </header>

        <section class="stats">
            <h2>Thông Tin Khách Hàng</h2>
            <div id="customer-info" class="customer-info">
                <table id="distin" align="center" border="1" cellpadding="4" cellspacing="0" width="800">
                    <tr class="hang">
                        <th>Mã khách Hàng</th>
                        <th>Họ và Tên</th>
                        <th>Số Điện Thoại</th>
                        <th>Email</th>
                        <th>Đơn Hàng</th>
                    </tr>

                    <?php
                    // Lấy giá trị tìm kiếm nếu có
                    $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';

                    // Tạo câu truy vấn SQL với điều kiện tìm kiếm
                    $sql = "SELECT * FROM khach_hang";
                    if ($searchTerm != '') {
                        $searchTerm = "%" . $conn->real_escape_string($searchTerm) . "%"; // Bảo vệ khỏi SQL Injection
                        $sql .= " WHERE ten_khach_hang LIKE '$searchTerm'";
                    }

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $id = $row['id_khach_hang'];
                            echo "<tr class='hang'>";
                            echo "<td>{$id}</td>";
                            echo "<td>{$row['ten_khach_hang']}</td>";
                            echo "<td>{$row['so_dien_thoai']}</td>";
                            echo "<td>{$row['email']}</td>";
                            echo "<td><button class='show-orders' data-id='{$id}'>Xem đơn hàng</button></td>";
                            echo "</tr>";

                            // Hiển thị đơn hàng (ẩn mặc định)
                            echo "<tr class='order-details-row' id='orders-{$id}' style='display: none;'><td colspan='5'>";
                            echo "<table border='1' cellpadding='4' cellspacing='0' width='100%'>";
                            echo "<tr>
                                <th>Mã đơn</th>
                                <th>Tên Khách Hàng</th>
                                <th>Giá Tiền</th>
                                <th>Ngày mua</th>
                                </tr>";

                            // Lấy đơn hàng của khách
                            $sqlOrders = "SELECT * FROM don_hang WHERE id_khach_hang = $id";
                            $orders = $conn->query($sqlOrders);
                            if ($orders->num_rows > 0) {
                                while($order = $orders->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>{$order['id_don_hang']}</td>";
                                    echo "<td>{$order['ten_khach_hang']}</td>";
                                    echo "<td>{$order['tong_tien']}</td>";
                                    echo "<td>{$order['ngay_dat']}</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>Không có đơn hàng nào</td></tr>";
                            }
                            echo "</table>";
                            echo "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align:center;'>Không có khách hàng nào</td></tr>";
                    }

                    $conn->close();
                    ?>
                </table>
            </div>
        </section>
    </main>
</div>

</body>
</html>
