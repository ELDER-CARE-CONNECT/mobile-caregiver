<?php
?>
<style>
.navbar {
    background: #fff;
    height: 70px;
    display: flex; 
    width: 100%;
    box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    position: fixed; 
    top: 0; 
    left: 0; 
    z-index: 1000;
    transition: all 0.3s;
}

.navbar-container {
    max-width: 1059.2px; 
    width: 100%;
    margin: 0 auto; 
    
    display: flex;
    justify-content: space-between;
    align-items: center;

    padding: 0 20px;
    box-sizing: border-box; 
}

.navbar h2 {
    color: #FF6B81;
    font-size: 26px; 
    font-weight:700;
    margin: 0; 
}
.nav-links a {
    color:#555; 
    text-decoration:none; 
    margin:0 16px;
    font-weight:500; 
    position:relative; 
    padding-bottom:3px;
}
.nav-links a:hover { color:#FF6B81; }
.nav-links a::after {
    content: ''; 
    position:absolute; 
    width:0; 
    height:2px; 
    display:block;
    margin-top:5px; 
    right:0; 
    background:#FF6B81; 
    transition:0.3s;
}
.nav-links a:hover::after { width:100%; left:0; }
.nav-links a.active {
    color: #FF6B81; 
    font-weight: 600;
}
.nav-links a.active::after {
    width: 100%; 
    left: 0;
}

</style>

<div class="navbar">
  <div class="navbar-container">
    <h2>Elder Care Connect</h2>
    <div class="nav-links">
      <a href="index.php">Trang chủ</a>
      <a href="dichvu.php">Dịch vụ</a>
      <a href="tongdonhang.php">Đơn hàng</a>
      <a href="Canhan.php">Cá nhân</a>
    </div>
  </div>
</div>

<script>
(function() {
    var currentPage = window.location.pathname.split('/').pop();
    if (currentPage === "" || currentPage === "index.php") {
      currentPage = "index.php"; 
    }

    var navLinks = document.querySelectorAll('.nav-links a');

    navLinks.forEach(function(link) {
      var linkPage = new URL(link.href).pathname.split('/').pop();
      if (linkPage === "") {
        linkPage = "index.php";
      }

      if (linkPage === currentPage) {
        link.classList.add('active'); 
      }
    });
})();

(function() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;

    let lastScrollTop = 0;
    const navbarHeight = navbar.offsetHeight; 

    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop && scrollTop > navbarHeight) {
            navbar.style.top = `-${navbarHeight}px`;
            navbar.style.top = '0'; 
        }
        
        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; 
    }, false);
})();
</script>