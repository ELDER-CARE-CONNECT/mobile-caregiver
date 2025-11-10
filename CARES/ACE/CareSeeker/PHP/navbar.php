<?php
// File này chứa HTML, CSS và JS (logic active link) cho Navbar
?>
<style>
/* ----------------------------------- */
/* CSS CHỈ DÀNH CHO NAVBAR */
/* ----------------------------------- */
.navbar {
  background: #fff;
  padding: 15px 60px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  width: 100%;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  position: fixed; 
  top: 0; 
  left: 0; 
  z-index: 1000;
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
.nav-links a.active {
  color: #FF6B81; /* Màu giống như khi hover */
  font-weight: 600;
}
.nav-links a.active::after {
  width: 100%; /* Hiện gạch chân giống như khi hover */
  left: 0;
}
/* LƯU Ý: Phần padding-top cho body sẽ được thêm vào Hoso.php và Canhan.php */
</style>

<div class="navbar">
  <h2>Elder Care Connect</h2>
  <div class="nav-links">
    <a href="index.php">Trang chủ</a>
    <a href="dichvu.php">Dịch vụ</a>
    <a href="tongdonhang.php">Đơn hàng</a>
    <a href="Canhan.php">Cá nhân</a>
  </div>
</div>

<script>
// Logic JavaScript để đánh dấu link đang hoạt động (Active Link)
(function() {
    // Lấy tên file của trang hiện tại (ví dụ: "index.php" hoặc "tongdonhang.php")
    var currentPage = window.location.pathname.split('/').pop();
    if (currentPage === "" || currentPage === "index.php") {
      currentPage = "index.php"; // Mặc định là trang chủ
    }

    // Lấy tất cả các link trong navbar
    var navLinks = document.querySelectorAll('.nav-links a');

    navLinks.forEach(function(link) {
      // Lấy tên file từ thuộc tính href của link
      var linkPage = new URL(link.href).pathname.split('/').pop();
      if (linkPage === "") {
        linkPage = "index.php";
      }

      // So sánh nếu tên file của link trùng với tên file của trang hiện tại
      if (linkPage === currentPage) {
        link.classList.add('active'); // Thêm class 'active'
      }
    });
})();
</script>