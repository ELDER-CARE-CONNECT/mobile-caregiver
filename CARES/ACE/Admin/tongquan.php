<?php
include 'connect.php';

// === Doanh thu theo tháng ===
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

// === Tổng doanh thu ===
$sql_tongtien = "SELECT SUM(tong_tien) AS total_revenue FROM don_hang";
$result_tongtien = $conn->query($sql_tongtien);
$total_revenue = $result_tongtien->fetch_assoc()['total_revenue'] ?? 0;

// === Tổng đơn hàng ===
$sql_donhang = "SELECT COUNT(*) AS total_orders FROM don_hang";
$result_donhang = $conn->query($sql_donhang);
$total_orders = $result_donhang->fetch_assoc()['total_orders'] ?? 0;

// === Tổng khách hàng ===
$sql_khachhang = "SELECT COUNT(*) AS total_customers FROM khach_hang";
$result_khachhang = $conn->query($sql_khachhang);
$total_customers = $result_khachhang->fetch_assoc()['total_customers'] ?? 0;

// === Tổng người chăm sóc ===
$sql_chamsoc = "SELECT COUNT(id_cham_soc) AS total_caregivers FROM nguoi_cham_soc";
$result_chamsoc = $conn->query($sql_chamsoc);
$total_caregivers = $result_chamsoc->fetch_assoc()['total_caregivers'] ?? 0;

// === Trung bình đánh giá ===
$sql_danhgia = "SELECT AVG(so_sao) AS avg_rating FROM danh_gia";
$result_danhgia = $conn->query($sql_danhgia);
$avg_rating = round($result_danhgia->fetch_assoc()['avg_rating'] ?? 0, 1);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng Quan</title>
    <link rel="stylesheet" href="fontend/css/tongquan.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        /* Gộp 5 ô thành 1 hàng ngang */
        .cards {
            display: flex;
            flex-wrap: nowrap;
            gap: 20px;
            justify-content: space-between;
            overflow-x: auto;
            padding-bottom: 10px;
        }

        .card {
            flex: 1;
            min-width: 180px;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card h3 {
            font-size: 18px;
            color: #555;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
        }

        .main-content {
            padding: 30px;
        }

        h1 {
            margin-bottom: 30px;
        }
    </style>
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
                <h1>Trang Tổng Quan</h1>
            </header>

            <section class="stats">
                <div class="cards">
                    <div class="card">
                        <h3>Tổng Doanh Thu</h3>
                        <p><?= number_format($total_revenue, 0) ?> VND</p>
                    </div>
                    <div class="card">
                        <h3>Tổng Đơn Hàng</h3>
                        <p><?= $total_orders ?> đơn</p>
                    </div>
                    <div class="card">
                        <h3>Tổng Khách Hàng</h3>
                        <p><?= $total_customers ?> khách</p>
                    </div>
                    <div class="card">
                        <h3>Tổng Người Chăm Sóc</h3>
                        <p><?= $total_caregivers ?> người</p>
                    </div>
                    <div class="card">
                        <h3>Trung Bình Đánh Giá</h3>
                        <p><?= $avg_rating ?> ⭐</p>
                    </div>
                </div>
            </section>

            <h2 style="margin-top: 40px;">Biểu đồ doanh thu theo tháng</h2>
            <canvas id="revenueChart" width="400" height="200"></canvas>
        </main>
    </div>

<script>
  const labelsFromPHP = <?= $labels_json ?>;
  const dataFromPHP = <?= $data_json ?>;

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

<?php $conn->close(); ?>
