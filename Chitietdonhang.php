<?php
include("connect.php");

// Lấy tất cả đơn hàng
$sql = "SELECT * FROM don_hang ORDER BY ngay_dat DESC";
$result = mysqli_query($conn, $sql);

// Giả lập phương thức thanh toán
$phuong_thuc = ["Tiền mặt", "Chuyển khoản", "Ví điện tử"];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi tiết đơn hàng</title>
<style>
  body {font-family: Arial; background:#f8f9fa; margin:30px;}
  h1 {text-align:center; color:#0d6efd;}
  table {width:100%; border-collapse:collapse; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,0.1);}
  th, td {border:1px solid #ccc; padding:10px; text-align:center;}
  th {background:#0d6efd; color:#fff;}
  tr:nth-child(even){background:#f2f2f2;}
  tr:hover {background:#e9f3ff;}
  .button {
    display:inline-block;
    margin:20px auto;
    padding:10px 20px;
    background:#198754;
    color:white;
    text-decoration:none;
    border-radius:6px;
    text-align:center;
  }
  .button:hover {background:#145c32;}
</style>
</head>
<body>

<h1>Chi tiết đơn hàng</h1>

<table>
  <tr>
    <th>Thông tin khách hàng</th>
    <th>Ngày & Giờ hẹn</th>
    <th>Phương thức thanh toán</th>
    <th>Tổng tiền</th>
    <th>Trạng thái</th>
  </tr>

  <?php 
  if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
      $pttt = $phuong_thuc[array_rand($phuong_thuc)];
  ?>
  <tr>
    <td><strong><?= htmlspecialchars($row['ten_khach_hang']) ?></strong><br>
        SĐT: <?= htmlspecialchars($row['so_dien_thoai']) ?><br>
        Địa chỉ: <?= htmlspecialchars($row['dia_chi_giao_hang']) ?></td>
    <td><?= $row['ngay_dat'] ?><br><?= $row['thoi_gian_bat_dau'] ?> - <?= $row['thoi_gian_ket_thuc'] ?></td>
    <td><?= $pttt ?></td>
    <td><?= number_format($row['tong_tien'], 0, ',', '.') ?> đ</td>
    <td><?= $row['trang_thai'] ?></td>
  </tr>
  <?php 
    }
  } else {
    echo "<tr><td colspan='5'>Không có dữ liệu</td></tr>";
  }
  ?>
</table>

<div style="text-align:center;">
  <a href="Thongkedonhang.php" class="button">➡ Xem thống kê tổng đơn hàng</a>
</div>

</body>
</html>
