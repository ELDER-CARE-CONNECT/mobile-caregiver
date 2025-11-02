<?php
$conn = new mysqli("localhost", "root", "", "sanpham");
if ($conn->connect_error) {
    die("Ket noi that bai: " . $conn->connect_error);
}

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
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
* { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
body { background: #f8f8fa; color: #333; overflow-x: hidden; line-height: 1.6; }

.navbar {
  background: #ffffff;
  padding: 15px 60px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  position: sticky;
  top: 0;
  z-index: 1000;
}
.navbar h2 { color: #FF6B81; font-size: 26px; letter-spacing: -0.5px; font-weight: 700; }
.nav-links a { color: #555; text-decoration: none; margin: 0 16px; font-weight: 500; transition: 0.3s; position: relative; padding-bottom: 3px; }
.nav-links a:hover { color: #FF6B81; }
.nav-links a::after { content: ''; position: absolute; width: 0; height: 2px; display: block; margin-top: 5px; right: 0; background: #FF6B81; transition: width 0.3s ease; }
.nav-links a:hover::after { width: 100%; left: 0; background: #FF6B81; }
.nav-links a.active { color: #FF6B81; font-weight: 600; }
.nav-links a.active::after { width: 100%; left: 0; }

footer { background: #1f2937; color: #ddd; text-align: center; padding: 30px; font-size: 15px; }
footer a { color: #FF6B81; text-decoration: none; }

h1.page-title { text-align: center; font-size: 38px; color: #FF6B81; margin: 50px 0 20px; font-weight: 800; }
.container { max-width: 1200px; margin: 0 auto 50px; padding: 0 20px; }

.filter-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; flex-wrap: wrap; gap: 20px; background: #ffffff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
.filter-bar input, .filter-bar select { padding: 12px 18px; border-radius: 8px; border: 1px solid #ddd; font-size: 16px; transition: border-color 0.3s, box-shadow 0.3s; color: #333; flex: 1; min-width: 250px; }
.filter-bar input:focus, .filter-bar select:focus { border-color: #4A90E2; box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1); outline: none; }

.grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 35px; }
.card { background: #fff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); overflow: hidden; transition: 0.4s; text-align: left; }
.card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0,0,0,0.15); }
.card img { width: 100%; height: 280px; object-fit: cover; }
.card-content { padding: 20px; }
.card-content h3 { margin: 0 0 4px; font-size: 22px; color: #FF6B81; }
.info { font-size: 16px; color: #666; margin: 8px 0; }
.info i { color: #333; margin-right: 8px; }
.rating { color: #ffd640ff; font-weight: 600; }
.price { font-size: 20px; color: #4A90E2; font-weight: 700; margin-top: 15px; display: block; }
.btn-group { display: flex; gap: 10px; margin-top: 15px; }
.btn-detail { display: inline-block; background: #FF6B81; color: white; padding: 12px 14px; border-radius: 10px; text-decoration: none; font-size: 16px; font-weight: 600; transition: 0.3s; flex: 1; text-align: center; }
.btn-detail:last-child { background: #FF6B81 }
.btn-detail:last-child:hover { background: #FF6B81 }
.btn-detail:first-child:hover { background: #E55B70; }

@media (max-width: 768px) {
    .navbar { padding: 15px 20px; }
    .nav-links { display: none; }
    .page-title { font-size: 30px; }
    .filter-bar { flex-direction: column; align-items: stretch; }
    .filter-bar input, .filter-bar select { min-width: 100%; }
    .btn-group { flex-direction: column; }
}
</style>
</head>
<body>

<div class="navbar">
  <h2>Elder Care Connect</h2>
  <div class="nav-links">
    <a href="index.php">Trang chủ</a>
    <a href="index.php#featured-services">Lợi ích</a>
    <a href="index.php#about">Giới thiệu</a>
    <a href="index.php#contact">Liên hệ</a>
  </div>
</div>

<h1 class="page-title">Danh sách Người Chăm Sóc Tận Tâm</h1>

<div class="container">
  <div class="filter-bar">
    <input type="text" id="searchInput" placeholder="Tìm theo tên...">
    <select id="sortSelect">
      <option value="">-- Sắp xếp --</option>
      <option value="tong_tien_kiem_duoc">Mức giá (thấp → cao)</option>
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
          <img src="<?php echo !empty($row['hinh_anh']) ? htmlspecialchars($row['hinh_anh']) : 'img/default_avatar.png'; ?>" alt="Avatar">
          <div class="card-content">
            <h3><?php echo htmlspecialchars($row['ho_ten']); ?></h3>
            <div class="info">
              <span class="rating">
                <?php
                $fullStars = floor($row['danh_gia_tb']);
                $halfStar = ($row['danh_gia_tb'] - $fullStars) >= 0.5;
                for($i=0; $i<$fullStars; $i++) echo '<i class="fas fa-star"></i>';
                if($halfStar) echo '<i class="fas fa-star-half-alt"></i>';
                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                for($i=0; $i<$emptyStars; $i++) echo '<i class="far fa-star"></i>';
                echo " {$row['danh_gia_tb']}/5";
                ?>
              </span>
            </div>
            <div class="info"><i class="fas fa-briefcase"></i> Kinh nghiệm: <?php echo htmlspecialchars($row['kinh_nghiem']); ?></div>
            <div class="info"><i class="fas fa-box-open"></i> Đơn đã nhận: <?php echo $row['don_da_nhan']; ?></div>
            <div class="price"><?php echo number_format($row['tong_tien_kiem_duoc'], 0, ',', '.'); ?> đ/giờ</div>
            <div class="btn-group"> 
                <a href="Thongtinnguoichamsoc.php?id=<?php echo $row['id_cham_soc']; ?>" class="btn-detail"><i class="fas fa-info-circle"></i> Xem chi tiết</a>
                <a href="Datdonhang.php?id=<?php echo $row['id_cham_soc']; ?>" class="btn-detail"><i class="fas fa-calendar-check"></i> Đặt dịch vụ</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center; padding: 50px; font-size: 18px; color:#999; grid-column: 1 / -1;">Chưa có người chăm sóc nào trong hệ thống.</p>
    <?php endif; ?>
  </div>
</div>

<footer>
  © 2025 Elder Care Connect | <i class="fas fa-shield-alt" style="color:#4A90E2;"></i> Cam kết chất lượng và sự tận tâm
</footer>

<script>
const searchInput = document.getElementById("searchInput");
const caregiverGrid = document.getElementById("caregiverGrid");

searchInput.addEventListener("input", () => {
  const keyword = searchInput.value.toLowerCase();
  document.querySelectorAll(".card").forEach(card => {
    const name = card.getAttribute("data-name");
    card.style.display = name.includes(keyword) ? "grid" : "none"; 
  });
});

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