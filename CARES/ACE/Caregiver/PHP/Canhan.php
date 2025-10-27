<?php
session_start();
if (!isset($_SESSION['caregiver_id'])) {
    echo "<script>alert('Vui lòng đăng nhập trước!'); window.location.href='login_caregiver.php';</script>";
    exit;
}
include 'connect.php';

$id = $_SESSION['caregiver_id'];
$sql = "SELECT * FROM nguoi_cham_soc WHERE id_cham_soc = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$info = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Trang cá nhân người chăm sóc</title>
<style>
body {
    font-family: "Segoe UI", sans-serif;
    background: linear-gradient(135deg, #fef1f4, #eef5ff);
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}
.container {
    display: flex;
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    padding: 30px;
    width: 900px;
}
.left {
    flex: 1;
    text-align: center;
    border-right: 1px solid #eee;
    padding-right: 30px;
}
.left img {
    width: 220px;
    height: 220px;
    border-radius: 12px;
    object-fit: cover;
    background: #f5f5f5;
    display: block;
    margin: 0 auto 20px;
}
.btn {
    background: #ff6b81;
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 15px;
    font-weight: 600;
    transition: 0.3s;
}
.btn:hover {
    background: #ff4c60;
}
.right {
    flex: 2;
    padding-left: 40px;
}
.right h2 {
    color: #ff6b81;
}
.right p {
    margin: 8px 0;
    font-size: 16px;
}
</style>
</head>
<body>

<div class="container">
    <div class="left">
        <!-- Nếu chưa có ảnh thì hiển thị ô trống -->
        <img src="<?php echo !empty($info['hinh_anh']) ? $info['hinh_anh'] : 'uploads/default.jpg'; ?>" alt="Ảnh đại diện">
        
        <h3><?php echo htmlspecialchars($info['ho_ten']); ?></h3>
        <!-- ✅ Nút đăng xuất nằm dưới ảnh -->
        <button onclick="window.location.href='logout.php'" class="btn">Đăng xuất</button>
    </div>

    <div class="right">
        <h2>Thông tin cá nhân</h2>
        <p><strong>Địa chỉ:</strong> <?php echo $info['dia_chi']; ?></p>
        <p><strong>Tuổi:</strong> <?php echo $info['tuoi']; ?></p>
        <p><strong>Giới tính:</strong> <?php echo $info['gioi_tinh']; ?></p>
        <p><strong>Chiều cao:</strong> <?php echo $info['chieu_cao']; ?> cm</p>
        <p><strong>Cân nặng:</strong> <?php echo $info['can_nang']; ?> kg</p>
        <p><strong>Kinh nghiệm:</strong> <?php echo $info['kinh_nghiem']; ?></p>
        <p><strong>Đơn đã nhận:</strong> <?php echo $info['don_da_nhan']; ?></p>
        <p><strong>Tổng tiền kiếm được:</strong> <?php echo number_format($info['tong_tien_kiem_duoc'], 0, ',', '.'); ?> đ</p>
    </div>
</div>

</body>
</html>
