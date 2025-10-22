<?php
$conn = new mysqli("localhost", "root", "", "sanpham");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin người chăm sóc được chọn
$stmt = $conn->prepare("SELECT * FROM nguoi_cham_soc WHERE id_cham_soc = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("<h2 style='text-align:center;color:red;'>Không tìm thấy người chăm sóc này!</h2>");
}

$row = $result->fetch_assoc();
$stmt->close();

// Lấy thêm 3 người chăm sóc khác để đề xuất
$related = $conn->query("SELECT id_cham_soc, ho_ten, hinh_anh, danh_gia_tb, kinh_nghiem, tong_tien_kiem_duoc 
                         FROM nguoi_cham_soc 
                         WHERE id_cham_soc != $id 
                         ORDER BY RAND() 
                         LIMIT 3");
$conn->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thông tin người chăm sóc - <?php echo htmlspecialchars($row['ho_ten']); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
  font-family: 'Poppins', sans-serif;
  background: linear-gradient(135deg, #fff4f6, #f8f9ff);
  margin: 0;
  color: #333;
}
.container {
  max-width: 1000px;
  margin: 40px auto;
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  padding: 30px;
  overflow: hidden;
}
.header {
  display: flex;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 30px;
}
.header img {
  width: 300px;
  height: 300px;
  border-radius: 20px;
  object-fit: cover;
  box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}
.info {
  flex: 1;
}
h1 {
  margin: 0 0 10px;
  color: #ff6b81;
  font-size: 28px;
}
.info p {
  font-size: 16px;
  margin: 6px 0;
}
.rating {
  color: #f1c40f;
  font-weight: bold;
}
.price {
  color: #ff4757;
  font-weight: 600;
  font-size: 18px;
}
.back-btn {
  display: inline-block;
  background: #ff6b81;
  color: white;
  padding: 10px 18px;
  border-radius: 10px;
  text-decoration: none;
  margin-top: 20px;
  transition: 0.3s;
}
.back-btn:hover {
  background: #ff4757;
}
.suggest-section {
  margin-top: 50px;
}
.suggest-title {
  font-size: 22px;
  font-weight: 600;
  color: #444;
  border-left: 5px solid #ff6b81;
  padding-left: 10px;
  margin-bottom: 20px;
}
.suggest-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}
.card {
  background: #fff;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  overflow: hidden;
  transition: all 0.3s;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}
.card img {
  width: 100%;
  height: 220px;
  object-fit: cover;
}
.card-content {
  padding: 14px;
}
.card-content h3 {
  margin: 0 0 8px;
  color: #333;
}
.card-content p {
  margin: 4px 0;
  font-size: 14px;
}
.detail-btn {
  display: inline-block;
  margin-top: 8px;
  background: #ff6b81;
  color: white;
  padding: 7px 12px;
  border-radius: 8px;
  text-decoration: none;
  font-size: 13px;
  transition: 0.3s;
}
.detail-btn:hover {
  background: #ff4757;
}
</style>
</head>
<body>

<div class="container">
  <div class="header">
    <img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>" alt="Ảnh người chăm sóc">
    <div class="info">
      <h1><?php echo htmlspecialchars($row['ho_ten']); ?></h1>
      <p><strong>Tuổi:</strong> <?php echo $row['tuoi']; ?></p>
      <p><strong>Giới tính:</strong> <?php echo $row['gioi_tinh']; ?></p>
      <p><strong>Chiều cao:</strong> <?php echo $row['chieu_cao']; ?> cm</p>
      <p><strong>Cân nặng:</strong> <?php echo $row['can_nang']; ?> kg</p>
      <p><strong>Trung bình đánh giá:</strong> 
         <span class="rating">⭐ <?php echo $row['danh_gia_tb']; ?>/5</span></p>
      <p><strong>Kinh nghiệm:</strong> <?php echo htmlspecialchars($row['kinh_nghiem']); ?></p>
      <p><strong>Số lượng đơn đã nhận:</strong> <?php echo $row['don_da_nhan']; ?></p>
      <p><strong>Giá tiền/giờ:</strong> 
         <span class="price"><?php echo number_format($row['tong_tien_kiem_duoc'], 0, ',', '.'); ?> đ/giờ</span></p>
         <a href="dat_dichvu.php?id=<?php echo $row['id_cham_soc']; ?>" class="back-btn">📝 Đặt dịch vụ ngay</a>
      <a href="dichvu.php" class="back-btn">← Quay lại danh sách</a>
    </div>
  </div>

  <div class="suggest-section">
    <div class="suggest-title">✨ Đề xuất thêm người chăm sóc khác</div>
    <div class="suggest-grid">
      <?php if ($related && $related->num_rows > 0): ?>
        <?php while ($r = $related->fetch_assoc()): ?>
          <div class="card">
            <img src="<?php echo htmlspecialchars($r['hinh_anh']); ?>" alt="Avatar">
            <div class="card-content">
              <h3><?php echo htmlspecialchars($r['ho_ten']); ?></h3>
              <p>⭐ Đánh giá: <strong><?php echo $r['danh_gia_tb']; ?>/5</strong></p>
              <p>💼 Kinh nghiệm: <?php echo htmlspecialchars($r['kinh_nghiem']); ?></p>
              <p>💰 <?php echo number_format($r['tong_tien_kiem_duoc'], 0, ',', '.'); ?> đ/giờ</p>
              <a href="chitiet_chamsoc.php?id=<?php echo $r['id_cham_soc']; ?>" class="detail-btn">Xem chi tiết</a>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>Không có người chăm sóc nào khác để đề xuất.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

</body>
</html>
