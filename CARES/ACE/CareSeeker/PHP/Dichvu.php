
<?php
// Ket noi database
$conn = new mysqli("localhost", "root", "", "sanpham");
if ($conn->connect_error) {
    die("Ket noi that bai: " . $conn->connect_error);
}

// Lay danh sach nguoi cham soc
$sql = "SELECT id_cham_soc, ho_ten, hinh_anh, danh_gia_tb, kinh_nghiem, don_da_nhan, tong_tien_kiem_duoc 
        FROM nguoi_cham_soc";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Dịch vụ - Danh sách người chăm sóc</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
body {
  font-family: 'Inter', sans-serif;
  background: linear-gradient(135deg, #fef6f9, #fff);
  margin: 0;
  padding: 0;
}
header {
  background: #ff6b81;
  color: white;
  text-align: center;
  padding: 20px 0;
  font-size: 24px;
  font-weight: bold;
  box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}
.container {
  max-width: 1200px;
  margin: 30px auto;
  padding: 0 20px;
}
.grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}
.card {
  background: #fff;
  border-radius: 15px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  overflow: hidden;
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
.card img {
  width: 100%;
  height: 220px;
  object-fit: cover;
}
.card-content {
  padding: 16px;
}
.card h3 {
  margin: 0;
  font-size: 18px;
  color: #333;
}
.info {
  font-size: 14px;
  color: #555;
  margin: 6px 0;
}
.rating {
  color: #ffa502;
  font-weight: bold;
}
.price {
  font-size: 16px;
  color: #ff4757;
  font-weight: 600;
  margin-top: 10px;
}
.filter-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  flex-wrap: wrap;
}
.filter-bar input {
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
  width: 250px;
}
.filter-bar select {
  padding: 8px 10px;
  border-radius: 8px;
  border: 1px solid #ccc;
}
.btn-detail {
  display: inline-block;
  margin-top: 10px;
  background: #ff6b81;
  color: white;
  padding: 8px 14px;
  border-radius: 8px;
  text-decoration: none;
  font-size: 14px;
  transition: 0.2s;
}
.btn-detail:hover {
  background: #ff4757;
}

</style>
</head>
<body>
<header>Dịch vụ chăm sóc - Danh sách người chăm sóc</header>

<div class="container">
  <div class="filter-bar">
    <input type="text" id="searchInput" placeholder="Tìm theo tên...">
    <select id="sortSelect">
      <option value="">-- Sắp xếp --</option>
      <option value="tong_tien_kiem_duoc">Tổng tiền kiếm được (thấp → cao)</option>
      <option value="danh_gia_tb">Đánh giá (cao → thấp)</option>
      <option value="don_da_nhan">Đơn đã nhận (nhiều → ít)</option>
    </select>
  </div>

  <div class="grid" id="caregiverGrid">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="card" data-name="<?php echo strtolower($row['ho_ten']); ?>"
             data-money="<?php echo $row['tong_tien_kiem_duoc']; ?>"
             data-rating="<?php echo $row['danh_gia_tb']; ?>"
             data-orders="<?php echo $row['don_da_nhan']; ?>">
          <img src="<?php echo htmlspecialchars($row['hinh_anh']); ?>" alt="Avatar">
          <div class="card-content">
            <h3><?php echo htmlspecialchars($row['ho_ten']); ?></h3>
            <div class="info">⭐ Trung bình đánh giá: <span class="rating"><?php echo $row['danh_gia_tb']; ?>/5</span></div>
            <div class="info">💼 Kinh nghiệm: <?php echo htmlspecialchars($row['kinh_nghiem']); ?></div>
            <div class="info">📦 Đơn đã nhận: <?php echo $row['don_da_nhan']; ?></div>
            <div class="price"><?php echo number_format($row['tong_tien_kiem_duoc'], 0, ',', '.'); ?> đ/giờ</div>
            <a href="Thongtinnguoichamsoc.php?id=<?php echo $row['id_cham_soc']; ?>" class="btn-detail">Xem chi tiết</a>
            <a href="Datdonhang.php?id=<?php echo $row['id_cham_soc']; ?>" class="btn-detail"> Đặt dịch vụ ngay</a>

          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>Chưa có người chăm sóc nào trong hệ thống.</p>
    <?php endif; ?>
  </div>
</div>

<script>
// Tim kiem theo ten
const searchInput = document.getElementById("searchInput");
const caregiverGrid = document.getElementById("caregiverGrid");

searchInput.addEventListener("input", () => {
  const keyword = searchInput.value.toLowerCase();
  document.querySelectorAll(".card").forEach(card => {
    const name = card.getAttribute("data-name");
    card.style.display = name.includes(keyword) ? "block" : "none";
  });
});

// Sap xep theo lua chon
const sortSelect = document.getElementById("sortSelect");
sortSelect.addEventListener("change", () => {
  const cards = Array.from(document.querySelectorAll(".card"));
  const sortBy = sortSelect.value;

  cards.sort((a, b) => {
    if (sortBy === "tong_tien_kiem_duoc")
      return a.dataset.money - b.dataset.money;
    if (sortBy === "danh_gia_tb")
      return b.dataset.rating - a.dataset.rating;
    if (sortBy === "don_da_nhan")
      return b.dataset.orders - a.dataset.orders;
    return 0;
  });

  caregiverGrid.innerHTML = "";
  cards.forEach(card => caregiverGrid.appendChild(card));
});
</script>
</body>
</html>
<?php $conn->close(); ?>