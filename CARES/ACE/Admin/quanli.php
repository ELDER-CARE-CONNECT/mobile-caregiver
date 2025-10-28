<?php include 'check_login.php'; ?>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sanpham";

// Kết nối CSDL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lí Đơn Hàng</title>
    <link rel="stylesheet" href="fontend/css/sanpham.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>
<body>
<div class="container">
    <?php 
    $activePage = 'quanli'; 
    include 'sidebar.php'; 
    ?>

    <main class="main-content">
        <header class="navbar">
            <h1>Trang Quản Lí Đơn Hàng</h1>
            <div class="search">
                <form method="get" action="">
                    <input type="text" name="search_id" placeholder="Tìm kiếm theo mã đơn hàng" 
                           value="<?php echo isset($_GET['search_id']) ? $_GET['search_id'] : ''; ?>">
                    <button type="submit">🔍</button>
                </form>
            </div>
        </header>

        <section class="stats">
            <div class="order-header">
                <h2>Danh sách đơn hàng</h2>
            </div>

            <table border="1" cellpadding="6" cellspacing="0" width="100%">
                <tr class="hang">
                    <th>Mã đơn hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tên khách hàng</th>
                    <th>Số điện thoại</th>
                    <th>Tên người chăm sóc</th>
                    <th>Trạng thái</th>
                    <th>Đánh giá</th>
                    <th>Nhận xét</th>
                    <th>Tổng tiền</th>
                    <th>Cập nhật</th>
                </tr>

                <?php
                // Lấy mã đơn hàng tìm kiếm (nếu có)
                $search_id = isset($_GET['search_id']) ? $_GET['search_id'] : '';

                // Truy vấn kết hợp 3 bảng
              $sql = "
                SELECT 
                    dh.id_don_hang,
                    dh.ngay_dat,
                    dh.ten_khach_hang,
                    dh.so_dien_thoai,
                    ncs.ho_ten AS ten_nguoi_cham_soc,
                    dh.trang_thai,
                    dg.so_sao AS danh_gia,
                    dg.nhan_xet,
                    dh.tong_tien
                FROM don_hang dh
                LEFT JOIN nguoi_cham_soc ncs ON dh.id_cham_soc = ncs.id_cham_soc
                LEFT JOIN danh_gia dg ON dh.id_danh_gia = dg.id_danh_gia
                WHERE 1
            ";


                if ($search_id) {
                    $sql .= " AND dh.id_don_hang LIKE '%$search_id%'";
                }

                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr class='hang'>";
                        echo "<td>" . $row['id_don_hang'] . "</td>";
                        echo "<td>" . $row['ngay_dat'] . "</td>";
                        echo "<td>" . $row['ten_khach_hang'] . "</td>";
                        echo "<td>" . $row['so_dien_thoai'] . "</td>";
                        echo "<td>" . ($row['ten_nguoi_cham_soc'] ?? 'Chưa có') . "</td>";
                        echo "<td>" . $row['trang_thai'] . "</td>";
                        echo "<td>" . ($row['diem_danh_gia'] ?? 'Chưa đánh giá') . "</td>";
                        echo "<td>" . ($row['nhan_xet'] ?? '—') . "</td>";
                        echo "<td>" . number_format($row['tong_tien'], 0) . " VND</td>";

                        // Form cập nhật trạng thái
                        echo "<td>
                            <form method='post' action='capnhat_trangthai.php'>
                                <select name='trang_thai'>
                                    <option value='Chờ xác nhận' ".($row['trang_thai']=='Chờ xác nhận'?'selected':'').">Chờ xác nhận</option>
                                    <option value='Đang giao' ".($row['trang_thai']=='Đang hoàn thành'?'selected':'').">Đang hoàn thành</option>
                                    <option value='Đã giao' ".($row['trang_thai']=='Đã hoàn thành'?'selected':'').">Đã hoàn thành</option>
                                    <option value='Đã hủy' ".($row['trang_thai']=='Đã hủy'?'selected':'').">Đã hủy</option>
                                </select>
                                <input type='hidden' name='id_don_hang' value='" . $row['id_don_hang'] . "'>
                                <button type='submit'>Cập nhật</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' style='text-align:center;'>Không có đơn hàng nào</td></tr>";
                }

                $conn->close();
                ?>
            </table>
        </section>
    </main>
</div>
</body>
</html>
