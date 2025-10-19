<?php include 'check_login.php'; ?>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sanpham";

// Kết nối
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quản Lí Đơn Hàng</title>
    <link rel="stylesheet" href="fontend/css/sanpham.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="fontend/js/quanlisp.js"></script>
</head>
<body>
<div class="container">
    <?php 
    $activePage = 'quanli'; 
    $pageTitle = 'Quản Lí Đơn Hàng';
    include 'sidebar.php'; 
    ?>

    <main class="main-content">
        <header class="navbar">
            <h1>Trang Quản Lí Đơn Hàng</h1>
            <div class="search">
                <!-- Form tìm kiếm đơn hàng theo mã đơn hàng -->
                <form method="get" action="">
                    <input type="text" name="search_id" placeholder="Tìm kiếm theo mã đơn hàng" value="<?php echo isset($_GET['search_id']) ? $_GET['search_id'] : ''; ?>">
                    <button type="submit">🔍</button>
                </form>
            </div>
        </header>

        <section class="stats">
            <div class="order-header">
                <h2>Danh sách đơn hàng</h2>
            </div>

            <table id="orderTable" align="center" border="1" cellpadding="4" cellspacing="0" width="100%">
                <tr class="hang">
                    <th>Mã đơn hàng</th>
                    <th>Ngày đặt</th>
                    <th>Khách hàng</th>
                    <th>Trạng thái</th>
                    <th>Địa chỉ giao hàng</th>  
                    <th>Tổng tiền</th> 
                    <th>Cập nhật</th>                       
                </tr>

                <?php
                // Lấy giá trị tìm kiếm mã đơn hàng từ form (nếu có)
                $search_id = isset($_GET['search_id']) ? $_GET['search_id'] : '';

                // Cập nhật câu truy vấn SQL để tìm kiếm theo mã đơn hàng
                $sql = "SELECT * FROM don_hang WHERE 1"; // Điều kiện mặc định

                if ($search_id) {
                    // Nếu có giá trị tìm kiếm, lọc theo mã đơn hàng
                    $sql .= " AND id_don_hang LIKE '%$search_id%'";
                }

                // Thực hiện truy vấn
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Nếu có đơn hàng, hiển thị dữ liệu
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='hang'>";
                        echo "<td>" . $row['id_don_hang'] . "</td>";
                        echo "<td>" . $row['ngay_dat'] . "</td>";
                        echo "<td>" . $row['ten_khach_hang'] . "</td>";
                        echo "<td>" . $row['trang_thai'] . "</td>";
                        echo "<td>" . $row['dia_chi_giao_hang'] . "</td>";
                        echo "<td>" . number_format($row['tong_tien'], 0) . " VND</td>";

                        // Form cập nhật trạng thái
                        echo "<td>
                            <form method='post' action='capnhat_trangthai.php'>
                                <select name='trang_thai'>
                                    <option value='Chờ xác nhận' ".($row['trang_thai'] == 'Chờ xác nhận' ? 'selected' : '').">Chờ xác nhận</option>
                                    <option value='Đang giao' ".($row['trang_thai'] == 'Đang giao' ? 'selected' : '').">Đang giao</option>
                                    <option value='Đã giao' ".($row['trang_thai'] == 'Đã giao' ? 'selected' : '').">Đã giao</option>
                                    <option value='Đã hủy' ".($row['trang_thai'] == 'Đã hủy' ? 'selected' : '').">Đã hủy</option>
                                </select>
                                <input type='hidden' name='id_don_hang' value='" . $row['id_don_hang'] . "'>
                                <button type='submit'>Cập nhật</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center;'>Không có đơn hàng nào</td></tr>";
                }

                // Đóng kết nối
                $conn->close();
                ?>
            </table>
        </section>
    </main>
</div>
</body>
</html>
