<?php
include("connect.php");

// Tổng đơn & doanh thu
$sql_tong = "SELECT COUNT(*) AS tong_don, SUM(tong_tien) AS tong_tien FROM don_hang WHERE trang_thai = 'Đã giao'";
$result_tong = mysqli_query($conn, $sql_tong);
$tong = mysqli_fetch_assoc($result_tong);

// Trung bình đánh giá
$sql_dg = "SELECT ROUND(AVG(so_sao),1) AS trung_binh FROM danh_gia";
$result_dg = mysqli_query($conn, $sql_dg);
$danhgia = mysqli_fetch_assoc($result_dg);

// Đơn hàng hoàn thành
$sql_don = "SELECT * FROM don_hang WHERE trang_thai = 'Đã giao' ORDER BY ngay_dat DESC";
$result_don = mysqli_query($conn, $sql_don);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thống kê tổng đơn hàng</title>
<style>
  body {font-family: Arial; margin:40px; background:#f9f9f9;}
  h1 {text-align:center; color:#0d6efd;}
  .summary {text-align:center; font-size:18px; margin-bottom:20px;}
  table {width:100%; border-collapse:collapse; background:white; box-shadow:0 2px 6px rgba(0,0,0,0.1);}
  th, td {border:1px solid #ccc; padding:10px; text-align:center;}
  th {background-color:#0d6efd; color:white;}
  tr:nth-child(even){background-color:#f2f2f2;}
  tr:hover {background-color:#e9f3ff;}
  .button {
    display:inline-block;
    margin:20px auto;
    padding:10px 20px;
    background:#6c757d;
    color:white;
    text-decoration:none;
    border-radius:6px;
  }
  .button:hover {background:#495057;}
</style>
</head>
<body>

<h1>Thống kê tổng đơn hàng</h1>

<div class="summary">
  <p><strong>Tổng số đơn:</strong> <?= $tong['tong_don'] ?> đơn</p>
  <p><strong>Tổng doanh thu:</strong> <?= number_format($tong['tong_tien'], 0, ',', '.') ?> đ</p>
  <p><strong>Trung bình đánh giá:</strong> <?= $danhgia['trung_binh'] ?> ⭐</p>
</div>

<h2 style="text-align:center;">Danh sách đơn hàng đã hoàn thành</h2>
<table>
  <tr>
    <th>ID</th>
    <th>Tên khách hàng</th>
    <th>Ngày đặt</th>
    <th>Tổng tiền</th>
    <th>Trạng thái</th>
  </tr>
  <?php 
  if (mysqli_num_rows($result_don) > 0) {
    while ($row = mysqli_fetch_assoc($result_don)) { ?>
      <tr>
        <td><?= $row['id_don_hang'] ?></td>
        <td><?= htmlspecialchars($row['ten_khach_hang']) ?></td>
        <td><?= $row['ngay_dat'] ?></td>
        <td><?= number_format($row['tong_tien'], 0, ',', '.') ?> đ</td>
        <td><?= $row['trang_thai'] ?></td>
      </tr>
  <?php } } else { ?>
      <tr><td colspan="5">Không có đơn hàng hoàn thành</td></tr>
  <?php } ?>
</table>

<div style="text-align:center;">
 <a href="ChitietDonhang.php" class="button">⬅ Quay lại chi tiết đơn hàng</a>



</div>

</body>
</html>
