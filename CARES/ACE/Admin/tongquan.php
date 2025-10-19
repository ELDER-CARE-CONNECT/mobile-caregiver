<?php include 'check_login.php'; ?>
<?php
// Kết nối tới cơ sở dữ liệu
include 'connect.php'; // Đảm bảo bạn có file này để kết nối CSDL

// Truy vấn doanh thu theo tháng
$sql_doanhthu = "SELECT MONTH(ngay_dat) AS thang, SUM(tong_tien) AS doanh_thu 
                 FROM don_hang 
                 GROUP BY MONTH(ngay_dat) 
                 ORDER BY thang";
$result_doanhthu = $conn->query($sql_doanhthu);

$labels = [];
$data = [];

while ($row = $result_doanhthu->fetch_assoc()) {
    $labels[] = 'Tháng ' . $row['thang'];
    $data[] = $row['doanh_thu'];
}

$labels_json = json_encode($labels);
$data_json = json_encode($data);

// Truy vấn lấy 3 đơn hàng gần nhất
$sql_donhangmoi = "SELECT * FROM don_hang ORDER BY ngay_dat DESC LIMIT 3";
$result_donhangmoi = $conn->query($sql_donhangmoi);

// Truy vấn tổng doanh thu
$sql_tongtien = "SELECT SUM(tong_tien) AS total_revenue FROM don_hang";
$result_tongtien = $conn->query($sql_tongtien);
$total_revenue = 0;
if ($result_tongtien && $row = $result_tongtien->fetch_assoc()) {
    $total_revenue = $row['total_revenue'] ?? 0;
}

// Truy vấn tổng số đơn hàng
$sql_donhang = "SELECT COUNT(*) AS total_orders FROM don_hang";
$result_donhang = $conn->query($sql_donhang);
$total_orders = 0;
if ($result_donhang && $row = $result_donhang->fetch_assoc()) {
    $total_orders = $row['total_orders'] ?? 0;
}

// Truy vấn tổng số khách hàng
$sql_khachhang = "SELECT COUNT(*) AS total_customers FROM khach_hang";
$result_khachhang = $conn->query($sql_khachhang);
$total_customers = 0;
if ($result_khachhang && $row = $result_khachhang->fetch_assoc()) {
    $total_customers = $row['total_customers'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản Phẩm</title>
    <link rel="stylesheet" href="fontend/css/tongquan.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="fontend/js/quanlisp.js"></script>
</head>
<body>
    <div class="container">
    <?php 
    $activePage = 'tongquan'; 
    $pageTitle = 'Trang Tổng Quan';
    include 'sidebar.php'; 
    ?>


        <main class="main-content">
            <header class="navbar">
                <h1> Trang Tổng Quan</h1>
            </header>

            <section class="stats">
                <section class="customer-section">          
                    <div class="cards">
                        <div class="card">
                            <h3>Doanh thu</h3>
                            <p><?php echo number_format($total_revenue, 0); ?> VND</p>
                        </div>
                        <div class="card">
                            <h3>Tổng Đơn Hàng</h3>
                            <p><?php echo $total_orders; ?> đơn</p>
                        </div>
                        <div class="card">
                            <h3>Tổng Khách Hàng</h3>
                            <p><?php echo $total_customers; ?> khách</p>
                        </div>
                    </div>   

                    <h2>Lịch Sử Giao Dịch Gần Đây</h2>
                    <form id="form1" name="form1" method="post" action="loaitin_them.php">
                        <table id="distin" align="center" border="1" cellpadding="4" cellspacing="0" width="600">
                            <tr class="hang">
                                <th>Mã đơn hàng</th>
                                <th>Ngày đặt</th>
                                <th>Tên khách hàng</th>
                                <th>Trạng thái</th>
                                <th>Địa chỉ giao hàng</th>  
                                <th>Tổng tiền</th>
                            </tr>

                            <?php
                            // Kiểm tra nếu có dữ liệu trả về từ cơ sở dữ liệu
                            if ($result_donhangmoi->num_rows > 0) {
    while ($row = $result_donhangmoi->fetch_assoc()) {
        echo "<tr class='hang'>";
        echo "<td>" . $row['id_don_hang'] . "</td>";
        echo "<td>" . (isset($row['ngay_dat']) ? $row['ngay_dat'] : 'N/A') . "</td>";
        echo "<td>" . (isset($row['ten_khach_hang']) ? $row['ten_khach_hang'] : 'N/A') . "</td>";
        echo "<td>" . (isset($row['trang_thai']) ? $row['trang_thai'] : 'N/A') . "</td>";
        echo "<td>" . (isset($row['dia_chi_giao_hang']) ? $row['dia_chi_giao_hang'] : 'N/A') . "</td>";
        echo "<td>" . (isset($row['tong_tien']) ? $row['tong_tien'] : 'N/A') . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='7' style='text-align: center;'>Chưa có đơn hàng nào.</td></tr>";
}

                            ?>
                        </table>
                    </form>
                </section>  
            </section>

            <h2>Biểu đồ doanh thu theo tháng</h2>
            <canvas id="revenueChart" width="400" height="200"></canvas>
        </main>
    </div>
<script>
  const labelsFromPHP = <?php echo $labels_json; ?>;
  const dataFromPHP = <?php echo $data_json; ?>;
</script>

<script>
  const ctx = document.getElementById('revenueChart').getContext('2d');
  const revenueChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: labelsFromPHP,
      datasets: [{
        label: 'Doanh thu (VND)',
        data: dataFromPHP,
        backgroundColor: 'rgba(52, 152, 219, 0.6)',
        borderColor: 'rgba(41, 128, 185, 1)',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return '₫' + value.toLocaleString();
            }
          }
        }
      }
    }
  });
</script>
</body>
</html>

<?php
$conn->close(); // Đóng kết nối
?>
