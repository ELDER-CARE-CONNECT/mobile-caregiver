<?php
$conn = new mysqli("localhost", "root", "", "sanpham");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT * FROM nguoi_cham_soc WHERE id_cham_soc = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("<h2 style='text-align:center;color:red;'>Không tìm thấy người chăm sóc này!</h2>");
}

$row = $result->fetch_assoc();
// bổ sung thêm phần nhận xét trong trang thông tin người chăm sóc
$id_cham_soc = intval($row['id_cham_soc']);
$sql_danhgia = "SELECT dg.*, kh.ten_khach_hang 
                FROM danh_gia dg 
                LEFT JOIN khach_hang kh ON dg.id_khach_hang = kh.id_khach_hang 
                WHERE dg.id_cham_soc = $id_cham_soc
                ORDER BY dg.ngay_danh_gia DESC";
$result_danhgia = $conn->query($sql_danhgia);
$stmt->close();

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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
/* ĐỊNH NGHĨA MÀU CHỦ ĐẠO */
:root {
    --primary-color: #FF6B81; /* Hồng chủ đạo */
    --accent-color: #4A90E2; /* Xanh phụ (chỉ dùng cho giá tiền và icon nhỏ nếu cần) */
    --text-color: #333; /* Màu chữ chính: Xám đậm */
    --secondary-text-color: #555; /* Màu chữ phụ */
}

body {
  font-family: 'Inter', sans-serif;
  background: #f8f8fa; 
  margin: 0;
  color: var(--text-color);
}
/* CONTAINER CHÍNH */
.container {
  max-width: 1100px; 
  margin: 40px auto;
  background: #fff;
  border-radius: 20px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  padding: 40px; 
  overflow: hidden;
}
/* HEADER (Thông tin cơ bản) */
.header {
  display: flex;
  align-items: flex-start;
  flex-wrap: wrap;
  gap: 40px;
}
.header img {
  width: 320px; 
  height: 320px;
  border-radius: 20px;
  object-fit: cover;
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
.info {
  flex: 1;
}
h1 {
  margin: 0 0 15px;
  color: var(--primary-color); /* Tên người chăm sóc dùng màu chủ đạo */
  font-size: 32px; 
  font-weight: 700;
}
.info p {
  font-size: 17px;
  margin: 8px 0;
  color: var(--secondary-text-color); /* Thông tin cơ bản dùng màu xám */
}
.info strong {
    color: var(--text-color); /* Tiêu đề thông tin dùng màu xám đậm */
    font-weight: 600;
}
.rating {
  color: #F7C513; /* Màu vàng giữ nguyên cho sao */
  font-weight: bold;
  font-size: 18px;
}
.price {
  color: var(--primary-color); /* Giá chuyển sang màu hồng chủ đạo */
  font-weight: 700;
  font-size: 22px;
  display: block;
  margin-top: 10px;
}
/* NÚT QUAY LẠI/ĐẶT DỊCH VỤ - Cả hai đều màu Hồng chủ đạo */
.back-btn {
  display: inline-block;
  background: var(--primary-color); /* Màu hồng chủ đạo */
  color: white;
  padding: 12px 20px;
  border-radius: 10px;
  text-decoration: none;
  margin-top: 25px;
  margin-right: 15px;
  font-weight: 600;
  transition: 0.3s;
}
.back-btn:hover {
  background: #E55B70;
}
/* Loại bỏ style riêng cho nút Quay lại */
.back-btn[style*="background: var(--accent-color)"] { 
    background: var(--primary-color) !important;
}
.back-btn[style*="background: var(--accent-color)"]:hover {
    background: #E55B70 !important;
}

/* PHẦN NHẬN XÉT (REVIEWS) */
.reviews {
  margin-top: 50px;
  background: #fff;
  padding: 30px;
  border-radius: 15px;
  border: 1px solid #f0f0f0;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
.reviews h3 {
  color: var(--text-color); /* Tiêu đề Nhận xét dùng màu xám đậm */
  margin-bottom: 20px;
  font-size: 24px;
  border-bottom: 2px solid #eee;
  padding-bottom: 10px;
}
.reviews h3 i {
    color: var(--primary-color); /* Icon dùng màu chủ đạo */
    margin-right: 10px;
}
.review-box {
  background: #fcfcfc;
  border-left: 5px solid var(--primary-color); /* Viền trái chuyển sang màu hồng */
  border-radius: 8px;
  padding: 15px;
  margin-bottom: 20px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}
.review-box p {
  margin: 5px 0;
  color: var(--text-color);
}
.review-box .name {
    font-size: 16px;
    font-weight: 700;
    color: var(--primary-color); /* Tên khách hàng dùng màu chủ đạo */
}
.review-box .star {
    color: #F7C513;
    font-weight: 600;
}
.review-box .comment {
  font-style: italic;
  color: var(--secondary-text-color); /* Nhận xét dùng màu xám */
  margin-top: 10px;
  line-height: 1.6;
}
.review-box .date {
  font-size: 13px;
  color: #999;
  display: block;
  margin-top: 10px;
}

/* PHẦN ĐỀ XUẤT (SUGGEST) */
.suggest-section {
  margin-top: 50px;
}
.suggest-title {
  font-size: 26px;
  font-weight: 700;
  color: var(--text-color); /* Tiêu đề dùng màu xám đậm */
  border-left: 5px solid var(--primary-color);
  padding-left: 15px;
  margin-bottom: 25px;
}
.suggest-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 25px;
}
.card {
  background: #fff;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.08);
  overflow: hidden;
  transition: all 0.3s;
  border-top: 4px solid var(--primary-color); /* Viền trên thẻ đề xuất chuyển sang màu hồng */
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}
.card img {
  width: 100%;
  height: 200px;
  object-fit: cover;
}
.card-content {
  padding: 18px;
}
.card-content h3 {
  margin: 0 0 5px;
  color: var(--primary-color); /* Tên người chăm sóc dùng màu chủ đạo */
  font-size: 18px;
}
.card-content p {
  margin: 5px 0;
  font-size: 15px;
  color: var(--secondary-text-color); /* Thông tin phụ dùng màu xám */
}
.card-content strong {
    color: #F7C513; /* Đánh giá dùng màu vàng */
}
.card-content .money {
    color: var(--primary-color); /* Giá chuyển sang màu hồng chủ đạo */
    font-weight: 700;
    margin-top: 5px;
    display: block;
}
.detail-btn {
  display: inline-block;
  margin-top: 10px;
  background: var(--primary-color); /* Nút dùng màu chủ đạo */
  color: white;
  padding: 9px 15px;
  border-radius: 8px;
  text-decoration: none;
  font-size: 14px;
  font-weight: 600;
  transition: 0.3s;
}
.detail-btn:hover {
  background: #E55B70;
}
/* Media Query */
@media (max-width: 768px) {
    .header {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    .header img {
        width: 100%;
        height: auto;
        max-width: 300px;
    }
    .container {
        padding: 20px;
    }
    .suggest-title {
        text-align: center;
        border-left: none;
        padding-left: 0;
    }
}
</style>
</head>
<body>

<div class="container">
  <div class="header">
    <img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>" alt="Ảnh người chăm sóc">
    <div class="info">
      <h1><i class="fas fa-user-nurse" style="color:var(--primary-color);"></i> <?php echo htmlspecialchars($row['ho_ten']); ?></h1>
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
         <a href="Datdonhang.php?id=<?php echo $row['id_cham_soc']; ?>" class="back-btn">📝 Đặt dịch vụ ngay</a>
      <a href="Dichvu.php" class="back-btn">← Quay lại danh sách</a>
    </div>
  </div>
  <div class="reviews">
  <h3><i class="fas fa-comments"></i> Nhận xét từ khách hàng</h3>
  <?php
  if ($result_danhgia && $result_danhgia->num_rows > 0) {
      while ($dg = $result_danhgia->fetch_assoc()) {
          echo "<div class='review-box'>";
          echo "<p class='name'><i class='fas fa-user'></i> " . htmlspecialchars($dg['ten_khach_hang']) . "</p>";
          echo "<p><span class='star'><i class='fas fa-star'></i> " . $dg['so_sao'] . "/5</span></p>";
          echo "<p class='comment'>" . htmlspecialchars($dg['nhan_xet']) . "</p>";
          echo "<span class='date'>📅 " . date("d/m/Y H:i", strtotime($dg['ngay_danh_gia'])) . "</span>";
          echo "</div>";
      }
  } else {
      echo "<p style='color:#999; text-align:center;'>Chưa có nhận xét nào cho người chăm sóc này.</p>";
  }
  ?>
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
              <p><i class="fas fa-briefcase" style="color:#555;"></i> Kinh nghiệm: <?php echo htmlspecialchars($r['kinh_nghiem']); ?></p>
              <p class="money">💰 <?php echo number_format($r['tong_tien_kiem_duoc'], 0, ',', '.'); ?> đ/giờ</p>
              <a href="Thongtinnguoichamsoc.php?id=<?php echo $r['id_cham_soc']; ?>" class="detail-btn">Xem chi tiết <i class="fas fa-arrow-right"></i></a>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p style="text-align:center; padding: 20px; color:#999;">Không có người chăm sóc nào khác để đề xuất.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

</body>
</html>
