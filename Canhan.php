<?php
include("connect.php");

$sql = "SELECT * FROM nguoi_cham_soc";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Hồ sơ người chăm sóc</title>
  <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
  <h1>Danh sách người chăm sóc</h1>
  <table border="1" cellpadding="10">
    <tr>
      <th>Họ tên</th>
      <th>Địa chỉ</th>
      <th>Tuổi</th>
      <th>Giới tính</th>
      <th>Kinh nghiệm</th>
      <th>Đánh giá TB</th>
      <th>Đơn đã nhận</th>
      <th>Tổng tiền kiếm được</th>
    </tr>

    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?= $row['ho_ten'] ?></td>
        <td><?= $row['dia_chi'] ?></td>
        <td><?= $row['tuoi'] ?></td>
        <td><?= $row['gioi_tinh'] ?></td>
        <td><?= $row['kinh_nghiem'] ?></td>
        <td><?= $row['danh_gia_tb'] ?></td>
        <td><?= $row['don_da_nhan'] ?></td>
        <td><?= number_format($row['tong_tien_kiem_duoc']) ?> đ</td>
      </tr>
    <?php } ?>
  </table>
</body>
</html>
