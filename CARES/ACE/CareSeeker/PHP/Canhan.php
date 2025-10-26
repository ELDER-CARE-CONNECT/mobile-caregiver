<?php
// Kết nối cơ sở dữ liệu
include("../../Caregiver/PHP/connect.php");

// Giả định khách hàng đang đăng nhập (thay bằng session nếu có)
$id_khach_hang = 4;

// Lấy thông tin khách hàng hiện tại
$sql = "SELECT * FROM khach_hang WHERE id_khach_hang = $id_khach_hang";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Nếu người dùng bấm nút cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['ten_khach_hang'];
    $email = $_POST['email'];
    $sdt = $_POST['so_dien_thoai'];
    $diachi = $_POST['dia_chi'];
    $gioitinh = $_POST['gioi_tinh'];
    $stk = $_POST['so_tai_khoan'];
    $nganhang = $_POST['ten_ngan_hang'];

    $update = "UPDATE khach_hang SET 
                ten_khach_hang='$ten',
                email='$email',
                so_dien_thoai='$sdt',
                dia_chi='$diachi',
                gioi_tinh='$gioitinh',
                so_tai_khoan='$stk',
                ten_ngan_hang='$nganhang'
               WHERE id_khach_hang=$id_khach_hang";

    if (mysqli_query($conn, $update)) {
        echo "<script>alert('✅ Cập nhật thông tin thành công!'); window.location.reload();</script>";
    } else {
        echo "<script>alert('❌ Lỗi khi cập nhật!');</script>";
    }
}

// Lấy danh sách tất cả hồ sơ khách hàng để hiển thị
$list = mysqli_query($conn, "SELECT * FROM khach_hang");
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thông tin cá nhân khách hàng</title>
<style>
body {
    font-family: 'Segoe UI', Arial;
    background-color: #f2f4f8;
    margin: 0;
    padding: 30px;
}
.container {
    max-width: 1000px;
    background: white;
    margin: auto;
    padding: 30px 40px;
    border-radius: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.15);
}
h1 {
    text-align: center;
    color: #007BFF;
    margin-bottom: 25px;
}
h2 {
    color: #333;
    margin-top: 40px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 15px;
}
table, th, td {
    border: 1px solid #ccc;
}
th {
    background: #007BFF;
    color: white;
    padding: 10px;
}
td {
    padding: 8px;
    text-align: center;
}
tr:nth-child(even) {
    background: #f9f9f9;
}
form {
    margin-top: 40px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px 30px;
}
label {
    font-weight: bold;
}
input, select {
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    width: 100%;
}
button {
    grid-column: 1 / span 2;
    padding: 12px;
    background-color: #007BFF;
    color: white;
    font-weight: bold;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    margin-top: 20px;
    cursor: pointer;
}
button:hover {
    background-color: #0056b3;
}
</style>
</head>
<body>

<div class="container">
    <h1>Danh sách hồ sơ khách hàng</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Số điện thoại</th>
            <th>Địa chỉ</th>
            <th>Giới tính</th>
        </tr>
        <?php while ($r = mysqli_fetch_assoc($list)) { ?>
        <tr>
            <td><?= $r['id_khach_hang'] ?></td>
            <td><?= htmlspecialchars($r['ten_khach_hang']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td><?= htmlspecialchars($r['so_dien_thoai']) ?></td>
            <td><?= htmlspecialchars($r['dia_chi']) ?></td>
            <td><?= htmlspecialchars($r['gioi_tinh']) ?></td>
        </tr>
        <?php } ?>
    </table>

    <h1>Chỉnh sửa thông tin cá nhân</h1>
    <form method="POST">
        <label>Họ tên:</label>
        <input type="text" name="ten_khach_hang" value="<?= htmlspecialchars($user['ten_khach_hang']) ?>">

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>">

        <label>Số điện thoại:</label>
        <input type="text" name="so_dien_thoai" value="<?= htmlspecialchars($user['so_dien_thoai']) ?>">

        <label>Địa chỉ:</label>
        <input type="text" name="dia_chi" value="<?= htmlspecialchars($user['dia_chi']) ?>">

        <label>Giới tính:</label>
        <select name="gioi_tinh">
            <option value="Nam" <?= ($user['gioi_tinh'] == 'Nam') ? 'selected' : '' ?>>Nam</option>
            <option value="Nữ" <?= ($user['gioi_tinh'] == 'Nữ') ? 'selected' : '' ?>>Nữ</option>
        </select>

        <label>Số tài khoản ngân hàng:</label>
        <input type="text" name="so_tai_khoan" value="<?= isset($user['so_tai_khoan']) ? htmlspecialchars($user['so_tai_khoan']) : '' ?>">

        <label>Tên ngân hàng:</label>
        <input type="text" name="ten_ngan_hang" value="<?= isset($user['ten_ngan_hang']) ? htmlspecialchars($user['ten_ngan_hang']) : '' ?>">

        <button type="submit">💾 Lưu thay đổi</button>
    </form>
</div>

</body>
</html>
