<?php
session_start();
if (!isset($_SESSION['so_dien_thoai'])) {
    header("Location: ../../admin/login.php");
    exit();
}
$conn = new mysqli("localhost", "root", "", "sanpham");
if ($conn->connect_error) {
  die("Kết nối thất bại: " . $conn->connect_error);
}
$sql = "SELECT * FROM nguoi_cham_soc LIMIT 4";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Elder Care Connect - Dịch vụ chăm sóc tận tâm</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
* { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }
body { background: #fff; color: #333; overflow-x: hidden; }

.navbar {
  background: linear-gradient(135deg, #ff6b81, #ff9bb1);
  padding: 12px 60px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}
.navbar h2 {
  color: #fff;
  font-size: 24px;
  letter-spacing: 1px;
}
.nav-links a {
  color: #fff;
  text-decoration: none;
  margin: 0 14px;
  font-weight: 500;
  transition: 0.3s;
}
.nav-links a:hover {
  text-decoration: underline;
}
.slideshow-container {
  position: relative;
  width: 80%;           
  max-width: 1000px;     
  margin: 40px auto;    
  border-radius: 16px;   
  overflow: hidden;
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}
.slides {
  display: none;
  width: 100%;
  height: auto;
  aspect-ratio: 16 / 9;
  object-fit: cover;
}

@media (max-width: 768px) {
  .slideshow-container { max-height: 240px; } 
  .slides { aspect-ratio: 4 / 3; }
}
.fade {
  animation: fadeEffect 2s;
}
@keyframes fadeEffect {
  from { opacity: 0.4; }
  to { opacity: 1; }
}
.dot-container {
  text-align: center;
  position: absolute;
  bottom: 20px;
  width: 100%;
}
.dot {
  height: 12px; width: 12px;
  margin: 0 4px;
  background-color: #ddd;
  border-radius: 50%;
  display: inline-block;
  transition: 0.4s;
}
.active { background-color: #ff6b81; }

.intro {
  text-align: center;
  padding: 60px 20px;
  background: #fff7f9;
}
.intro h1 {
  color: #ff6b81;
  margin-bottom: 18px;
  font-size: 32px;
}
.intro p {
  max-width: 800px;
  margin: auto;
  line-height: 1.6;
  color: #555;
}

.caregivers {
  padding: 70px 40px;
  text-align: center;
}
.caregivers h2 {
  color: #ff6b81;
  font-size: 28px;
  margin-bottom: 40px;
}
.caregiver-list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  gap: 28px;
  max-width: 1100px;
  margin: auto;
}
.caregiver-card {
  background: #fff;
  border-radius: 14px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.08);
  overflow: hidden;
  transition: 0.3s;
}
.caregiver-card:hover { transform: translateY(-5px); }
.caregiver-card img {
  width: 100%;
  height: 250px;
  object-fit: cover;
}
.caregiver-card .info {
  padding: 16px;
}
.caregiver-card .info h3 { margin-bottom: 6px; }
.caregiver-card p { color: #666; font-size: 14px; }
.caregiver-card .price {
  color: #ff4757;
  font-weight: 600;
  margin-top: 8px;
}
.caregiver-card a {
  display: inline-block;
  margin-top: 10px;
  background: #ff6b81;
  color: #fff;
  padding: 8px 14px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 500;
  transition: 0.2s;
}
.caregiver-card a:hover { background: #ff4757; }
.about {
  background: #fff7f9;
  padding: 70px 30px;
  text-align: center;
}
.about h2 {
  color: #ff6b81;
  font-size: 28px;
  margin-bottom: 20px;
}
.about p {
  max-width: 900px;
  margin: auto;
  color: #555;
  line-height: 1.7;
}

footer {
  background: #222;
  color: #bbb;
  text-align: center;
  padding: 20px;
  font-size: 14px;
  margin-top: 50px;
}
</style>
</head>
<body>

<div class="navbar">
  <h2>Elder Care Connect</h2>
  <div class="nav-links">
    <a href="index.php">Trang chủ</a>
    <a href="dichvu.php">Dịch vụ</a>
    <a href="#about">Giới thiệu</a>
    <a href="#contact">Liên hệ</a>
  </div>
</div>

<!-- chỗ ảnh slide show nháaaa -->
<div class="slideshow-container">
  <img class="slides fade" src="../../img/banner1.jpg" alt="Banner 1">
  <img class="slides fade" src="../../img/banner2.jpg" alt="Banner 2">
  <img class="slides fade" src="../../img/banner3.jpg" alt="Banner 3">
  <div class="dot-container">
    <span class="dot"></span>
    <span class="dot"></span>
    <span class="dot"></span>
  </div>
</div>

<!-- này là introduce về web -->
<section class="intro">
  <h1>Chăm sóc tận tâm – Trao yêu thương trọn vẹn 💖</h1>
  <p>
    Elder Care Connect là nền tảng kết nối những người chăm sóc tận tâm với gia đình cần hỗ trợ.  
    Chúng tôi giúp bạn tìm được người chăm sóc phù hợp nhất — có kinh nghiệm, kỹ năng, và lòng nhân ái.  
    Với sứ mệnh "Mang yêu thương đến từng mái ấm", chúng tôi đồng hành cùng người cao tuổi mỗi ngày.
  </p>
</section>

<!-- giới thiệu người chăm sóc nhá  -->
<section class="caregivers">
  <h2>👩‍⚕️ Một số người chăm sóc nổi bật</h2>
  <div class="caregiver-list">
    <?php while ($row = $result->fetch_assoc()) { ?>
      <div class="caregiver-card">
        
        <img src="<?php echo '../../' . htmlspecialchars($row['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($row['ho_ten']); ?>">
        
        <div class="info">
          <h3><?php echo htmlspecialchars($row['ho_ten']); ?></h3>
          <p>⭐ <?php echo $row['danh_gia_tb']; ?>/5</p>
          <p>Kinh nghiệm: <?php echo htmlspecialchars($row['kinh_nghiem']); ?></p>
          <p class="price"><?php echo number_format($row['tong_tien_kiem_duoc'], 0, ',', '.'); ?> đ/giờ</p>
          
          <a href="Thongtinnguoichamsoc.php?id=<?php echo $row['id_cham_soc']; ?>">Xem chi tiết</a>
          
        </div>
      </div>
    <?php } ?>
  </div>
  <div style="margin-top:40px;">
    <a href="dichvu.php" style="background:#ff6b81;color:white;padding:10px 20px;border-radius:8px;text-decoration:none;">Xem tất cả người chăm sóc →</a>
  </div>
</section>

<!-- chỗ about trang web I -->
<section class="about" id="about">
  <h2>Về Elder Care Connect</h2>
  <p>
    Chúng tôi cung cấp các dịch vụ: chăm sóc người cao tuổi tại nhà, hỗ trợ y tế, dinh dưỡng và tâm lý.  
    Đội ngũ của chúng tôi luôn sẵn sàng 24/7 để đồng hành cùng gia đình bạn.  
    Với công nghệ hiện đại, bạn có thể đặt dịch vụ, theo dõi tiến trình và đánh giá người chăm sóc dễ dàng chỉ với vài cú nhấp chuột.
  </p>
</section>

<!-- chỗ contact web nha -->
<section class="about" id="contact">
  <h2>Liên hệ với chúng tôi</h2>
  <p>
    📍 Địa chỉ: aaa, aaa. TP. Hồ Chí Minh <br>
    ☎️ Hotline: 0000 111 111  <br>
    📧 Email: support@eldercareconnect.vn
  </p>
</section>

<!-- cuối trang -->
<footer>
  © 2025 Elder Care Connect | Mang yêu thương đến từng mái ấm 💖
</footer>

<script>
let slideIndex = 0;
showSlides();
function showSlides() {
  const slides = document.getElementsByClassName("slides");
  const dots = document.getElementsByClassName("dot");
  for (let i = 0; i < slides.length; i++) slides[i].style.display = "none";
  slideIndex++;
  if (slideIndex > slides.length) slideIndex = 1;
  for (let i = 0; i < dots.length; i++) dots[i].classList.remove("active");
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].classList.add("active");
  setTimeout(showSlides, 4000);
}
</script>

</body>
</html>
