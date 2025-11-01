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
$sql = "SELECT * FROM nguoi_cham_soc LIMIT 3"; 
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Elder Care Connect - Dịch vụ chăm sóc tận tâm</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
* { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
body { background: #f8f8fa; color: #333; overflow-x: hidden; line-height: 1.6; }

/* NAVBAR */
.navbar {
  background: #fff;
  padding: 15px 60px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  position: sticky; top:0; z-index:1000;
  transition: all 0.3s;
}
.navbar h2 {
  color: #FF6B81;
  font-size: 26px; font-weight:700;
}
.nav-links a {
  color:#555; text-decoration:none; margin:0 16px;
  font-weight:500; position:relative; padding-bottom:3px;
}
.nav-links a:hover { color:#FF6B81; }
.nav-links a::after {
  content: ''; position:absolute; width:0; height:2px; display:block;
  margin-top:5px; right:0; background:#FF6B81; transition:0.3s;
}
.nav-links a:hover::after { width:100%; left:0; }

/* SLIDESHOW */
.slideshow-container {
  position: relative; width: 90%; max-width: 1200px; margin: 40px auto; 
  border-radius: 18px; overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.12);
}
.slides {
  display: none; width:100%; height:auto; aspect-ratio:16/7; object-fit:cover;
  transition: 1s ease;
}
.slideshow-container::after {
  content: ""; position:absolute; left:0; bottom:0; width:100%; height:100px;
  background: linear-gradient(transparent, rgba(0,0,0,0.2));
}
.dot-container { text-align:center; position:absolute; bottom:20px; width:100%; }
.dot { height:12px; width:12px; margin:0 5px; background:rgba(255,255,255,0.7);
  border-radius:50%; display:inline-block; cursor:pointer; transition:0.3s; }
.active { background-color:#FF6B81; }

/* INTRO */
.intro { text-align:center; padding:80px 20px; background:#fff; margin-top:-40px; }
.intro h1 { color:#FF6B81; margin-bottom:12px; font-size:38px; font-weight:700; }
.intro p { max-width:900px; margin:auto; color:#666; font-size:17px; line-height:1.7; }

/* FEATURED SERVICES */
.featured-services { padding:60px 40px; text-align:center; background:#f8f8fa; }
.featured-services h2 { color:#4A90E2; font-size:30px; margin-bottom:50px; font-weight:600; }
.service-list { display:flex; justify-content:center; gap:30px; flex-wrap:wrap; max-width:1200px; margin:auto; }
.service-card { background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.05);
  width:300px; transition:0.3s; border-top:4px solid #4A90E2; text-align:center; }
.service-card:hover { transform:translateY(-5px); box-shadow:0 8px 20px rgba(0,0,0,0.1);}
.service-card i { font-size:40px; color:#FF6B81; margin-bottom:15px; }
.service-card h3 { font-size:18px; font-weight:600; margin-bottom:10px; }
.service-card p { font-size:15px; color:#777; }

/* CAREGIVERS */
.caregivers { padding:80px 40px; text-align:center; background:#fff; }
.caregivers h2 { color:#FF6B81; font-size:32px; margin-bottom:40px; font-weight:700; }
.caregiver-list { display:grid; grid-template-columns:repeat(auto-fit, minmax(300px,1fr)); gap:35px; max-width:1000px; margin:auto; }
.caregiver-card { background:#fff; border-radius:16px; box-shadow:0 10px 25px rgba(0,0,0,0.1); overflow:hidden; transition:0.4s; text-align:left; position:relative; }
.caregiver-card:hover { transform:translateY(-8px); box-shadow:0 15px 35px rgba(0,0,0,0.15); }
.caregiver-card img { width:100%; height:280px; object-fit:cover; transition:0.5s; }
.caregiver-card:hover img { transform:scale(1.05); }
.caregiver-card .info { padding:20px; }
.caregiver-card .info h3 { font-size:20px; color:#FF6B81; margin-bottom:4px; }
.caregiver-card .info > p { font-size:15px; color:#666; margin-bottom:5px; }
.caregiver-card .rating { color:#F7C513; font-weight:600; margin-bottom:10px; display:block; }
.caregiver-card .price { color:#4A90E2; font-weight:700; font-size:18px; margin-top:10px; display:block; }
.caregiver-card a { display:block; margin-top:15px; background:#FF6B81; color:#fff; padding:12px 14px; border-radius:10px; text-decoration:none; font-weight:600; text-align:center; transition:0.3s; }
.caregiver-card a:hover { background:#E55B70; }

/* ABOUT */
.about { background:#fff; padding:80px 30px; text-align:center; }
.about h2 { color:#FF6B81; font-size:38px; margin-bottom:50px; font-weight:800; text-transform:uppercase; }
.about-content-wrapper { display:flex; max-width:1200px; margin:auto; align-items:flex-start; text-align:left; gap:60px; }
.about-text { flex:1; padding-right:40px; border-right:1px solid #eee; }
.about-text h3 { color:#4A90E2; font-size:26px; margin-bottom:20px; font-weight:700; }
.about-text p { color:#555; font-size:17px; line-height:1.8; margin-bottom:25px; }
.mission-statement { background:#fff7f9; padding:20px; border-radius:10px; border-left:5px solid #FF6B81; font-style:italic; color:#FF6B81; font-weight:600; text-align:left; }

/* About Services */
.about-services { flex:1; padding-left:40px; }
.about-services h3 { color:#FF6B81; font-size:24px; margin-bottom:25px; font-weight:700; text-align:center; }
.about-services ul { list-style:none; padding:0; }
.about-services li { display:flex; align-items:flex-start; margin-bottom:30px; padding:15px; background:#fcfcfc; border-radius:8px; box-shadow:0 2px 10px rgba(0,0,0,0.05); transition:0.3s; }
.about-services li:hover { box-shadow:0 4px 15px rgba(0,0,0,0.1); transform:translateY(-2px); }
.about-services li i { font-size:30px; color:#4A90E2; margin-right:20px; min-width:30px; text-align:center; padding-top:5px; }
.service-detail h4 { color:#333; font-size:18px; font-weight:700; margin-bottom:3px; }
.service-detail p { color:#777; font-size:15px; line-height:1.5; }

/* CONTACT */
.contact { background:#fcfcfc; padding:60px 30px; text-align:center; border-top:1px solid #eee; }
.contact h2 { color:#333; font-size:32px; margin-bottom:30px; font-weight:700; }

/* FOOTER */
footer { background:#1f2937; color:#ddd; text-align:center; padding:30px; font-size:15px; margin-top:0; }
footer a { color:#FF6B81; text-decoration:none; }

@media (max-width:900px) {
  .about-content-wrapper { flex-direction:column; text-align:center; }
  .about-text, .about-services { width:100%!important; padding:0!important; border-right:none!important; }
}
</style>
</head>
<body>

<div class="navbar">
  <h2>Elder Care Connect</h2>
  <div class="nav-links">
    <a href="index.php">Trang chủ</a>
    <a href="dichvu.php">Dịch vụ</a>
    <a href="#featured-services">Lợi ích</a>
    <a href="#about">Giới thiệu</a>
    <a href="#contact">Liên hệ</a>
  </div>
</div>

<div class="slideshow-container">
  <img class="slides fade" src="../../img/banner1.jpg" alt="Chăm sóc tại nhà">
  <img class="slides fade" src="../../img/banner2.jpg" alt="Hỗ trợ y tế">
  <img class="slides fade" src="../../img/banner3.jpg" alt="Chăm sóc dinh dưỡng">
  <div class="dot-container">
    <span class="dot" onclick="currentSlide(1)"></span>
    <span class="dot" onclick="currentSlide(2)"></span>
    <span class="dot" onclick="currentSlide(3)"></span>
  </div>
</div>

<section class="intro">
  <h1>Chăm sóc tận tâm – Trao yêu thương trọn vẹn <i class="fas fa-heart" style="color:#FF6B81;"></i></h1>
  <p>
    Elder Care Connect là nền tảng kết nối <b>tin cậy</b> giữa gia đình bạn và đội ngũ người chăm sóc <b>chuyên nghiệp</b>. 
    Chúng tôi cam kết mang đến giải pháp chăm sóc toàn diện, cá nhân hóa, giúp người cao tuổi tận hưởng cuộc sống trọn vẹn, khỏe mạnh và hạnh phúc tại chính ngôi nhà của mình. 
    <b>Sứ mệnh của chúng tôi: "Mang yêu thương đến từng mái ấm".</b>
  </p>
</section>

<section class="featured-services" id="featured-services">
    <h2>Tại sao chọn Elder Care Connect?</h2>
    <div class="service-list">
        <div class="service-card">
            <i class="fas fa-user-shield"></i>
            <h3>An toàn & Tin cậy</h3>
            <p>100% người chăm sóc được xác minh lý lịch và kinh nghiệm kỹ lưỡng, đảm bảo sự an tâm tuyệt đối cho gia đình bạn.</p>
        </div>
        <div class="service-card">
            <i class="fas fa-hands-holding-heart"></i>
            <h3>Chăm sóc Cá nhân hóa</h3>
            <p>Lập kế hoạch chăm sóc riêng biệt, đáp ứng mọi nhu cầu từ y tế cơ bản đến hỗ trợ tâm lý và sinh hoạt hàng ngày.</p>
        </div>
        <div class="service-card">
            <i class="fas fa-mobile-alt"></i>
            <h3>Quản lý dễ dàng</h3>
            <p>Ứng dụng di động hiện đại giúp bạn đặt lịch, theo dõi tiến trình và giao tiếp với người chăm sóc mọi lúc, mọi nơi.</p>
        </div>
    </div>
</section>

<section class="caregivers" id="caregivers">
  <h2><i class="fas fa-star-of-life" style="color:#FF6B81;"></i> Gương mặt chăm sóc nổi bật</h2>
  <div class="caregiver-list">
    <?php while ($row = $result->fetch_assoc()) { 
        $rating = number_format($row['danh_gia_tb'], 1);
        $experience = htmlspecialchars($row['kinh_nghiem']);
        $price_per_hour = number_format($row['tong_tien_kiem_duoc'], 0, ',', '.');
    ?>
      <div class="caregiver-card">
        <img src="<?php echo '../../' . htmlspecialchars($row['hinh_anh']); ?>" alt="<?php echo htmlspecialchars($row['ho_ten']); ?>">
        <div class="info">
          <h3><?php echo htmlspecialchars($row['ho_ten']); ?></h3>
          <span class="rating"><i class="fas fa-star"></i> <?php echo $rating; ?>/5</span>
          <p>Kinh nghiệm: <b><?php echo $experience; ?></b></p>
          <p class="price"><?php echo $price_per_hour; ?> đ/giờ</p>
          <a href="Thongtinnguoichamsoc.php?id=<?php echo $row['id_cham_soc']; ?>">Xem Hồ sơ chi tiết <i class="fas fa-arrow-right"></i></a>
        </div>
      </div>
    <?php } ?>
  </div>
  <div style="margin-top:50px;">
    <a href="dichvu.php" style="background:#4A90E2;color:white;padding:12px 25px;border-radius:10px;text-decoration:none;font-weight:600;font-size:16px;transition:0.3s;box-shadow: 0 4px 10px rgba(74, 144, 226, 0.3);">Khám phá tất cả Người chăm sóc →</a>
  </div>
</section>

<section class="about" id="about">
  <h2>Về Elder Care Connect</h2>
  <div class="about-content-wrapper">
    <div class="about-text">
        <h3>Đối tác chăm sóc sức khỏe toàn diện</h3>
        <p>Chúng tôi không chỉ là một dịch vụ, mà là một đối tác đáng tin cậy đồng hành cùng sức khỏe và hạnh phúc của người thân bạn...</p>
        <div class="mission-statement">
            <i class="fas fa-quote-left"></i> "Luôn đặt sự tận tâm và tình yêu thương làm cốt lõi trong mọi hoạt động chăm sóc, vì người cao tuổi xứng đáng được trọn vẹn yêu thương."
        </div>
    </div>
    <div class="about-services">
        <h3>Các Dịch Vụ Cốt Lõi</h3>
        <ul>
            <li><i class="fas fa-home"></i><div class="service-detail"><h4>Chăm sóc tại nhà toàn thời gian</h4><p>Hỗ trợ sinh hoạt hàng ngày, theo dõi sức khỏe liên tục.</p></div></li>
            <li><i class="fas fa-user-md"></i><div class="service-detail"><h4>Hỗ trợ y tế chuyên khoa</h4><p>Thực hiện tiêm, truyền dịch, chăm sóc vết thương và quản lý thuốc.</p></div></li>
            <li><i class="fas fa-seedling"></i><div class="service-detail"><h4>Tư vấn dinh dưỡng cá nhân</h4><p>Xây dựng thực đơn khoa học, phù hợp thể trạng.</p></div></li>
            <li><i class="fas fa-comments"></i><div class="service-detail"><h4>Đồng hành tâm lý & Giải trí</h4><p>Trò chuyện, tổ chức các hoạt động nhẹ nhàng, giúp người cao tuổi minh mẫn.</p></div></li>
        </ul>
    </div>
  </div>
</section>

<section class="contact" id="contact">
  <h2><i class="fas fa-headset" style="color:#FF6B81;"></i> Liên hệ với chúng tôi</h2>
  <p>Đội ngũ hỗ trợ của Elder Care Connect luôn sẵn sàng lắng nghe và tư vấn cho bạn.</p>
  <p><i class="fas fa-map-marker-alt" style="color:#FF6B81;"></i> Địa chỉ: aaa, aaa, TP. Hồ Chí Minh</p>
  <p><i class="fas fa-phone-alt" style="color:#FF6B81;"></i> Hotline: 0000 111 111</p>
  <p><i class="fas fa-envelope" style="color:#FF6B81;"></i> Email: support@eldercareconnect.vn</p>
</section>

<footer>
  © 2025 Elder Care Connect | <i class="fas fa-shield-alt" style="color:#4A90E2;"></i> Cam kết chất lượng và sự tận tâm
</footer>

<script>
let slideIndex = 1;
showSlides(slideIndex);

function plusSlides(n){ showSlides(slideIndex+=n); }
function currentSlide(n){ showSlides(slideIndex=n); }

function showSlides(){
  const slides = document.getElementsByClassName("slides");
  const dots = document.getElementsByClassName("dot");
  if(arguments.length===0){ slideIndex++; if(slideIndex>slides.length){slideIndex=1;} setTimeout(showSlides,4000);}
  for(let i=0;i<slides.length;i++){slides[i].style.display="none";}
  if(slideIndex>slides.length) slideIndex=1;
  if(slideIndex<1) slideIndex=slides.length;
  for(let i=0;i<dots.length;i++){dots[i].classList.remove("active");}
  slides[slideIndex-1].style.display="block";
  dots[slideIndex-1].classList.add("active");
  if(arguments.length!==0){ clearTimeout(autoSlideTimer); autoSlideTimer=setTimeout(showSlides,4000);}
}
let autoSlideTimer = setTimeout(showSlides,3000);
</script>

</body>
</html>
<?php $conn->close(); ?>
