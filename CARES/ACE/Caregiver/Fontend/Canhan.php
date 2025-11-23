<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thông tin cá nhân</title>

<style>
    body { font-family: 'Segoe UI', sans-serif; margin:0; padding:0; background: linear-gradient(135deg, #ffd1ff 0%, #ffe6f7 100%); display:flex; justify-content:center; align-items:center; min-height:100vh;}
    .profile-container { background:#fff; border-radius:25px; width:1200px; max-width:95%; display:flex; padding:60px; box-shadow:0 15px 40px rgba(0,0,0,0.15); align-items:center; margin-top: 60px; }
    .profile-left { flex:1; display:flex; justify-content:center; align-items:center; }
    .profile-left img { width:340px; height:340px; border-radius:25px; object-fit:cover; box-shadow:0 8px 25px rgba(0,0,0,0.2); }
    .profile-right { flex:1.5; padding-left:70px; }
    h2 { font-size:40px; font-weight:700; margin-bottom:20px; }
    .highlight { color:#e91e63; }
    .info-item { font-size:22px; margin:14px 0; line-height:1.6; }
    .info-item b { color:#333; }
    .rating { color:#FFD700; font-size:26px; display:inline-block; margin-left:6px; }
    .back-btn, .logout-btn { padding:16px 35px; border-radius:12px; color:white; text-decoration:none; font-weight:bold; transition:0.3s; font-size:20px; text-align:center; display:inline-block; }
    .back-btn { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    .back-btn:hover { opacity:0.85; transform:scale(1.03); }
    .logout-btn { background: linear-gradient(135deg, #ff6b81 0%, #e91e63 100%); }
    .logout-btn:hover { opacity:0.85; transform:scale(1.03); }
    .button-group { margin-top:40px; display:flex; gap:20px; flex-wrap:wrap; }
    @media (max-width:900px){ .profile-container{flex-direction:column;text-align:center;width:95%;padding:30px;} .profile-left img{width:250px;height:250px;margin-bottom:25px;} .profile-right{padding-left:0;} .button-group{justify-content:center;} }
</style>
</head>
<body>
<?php include 'Dieuhuong.php'; ?>

<div class="profile-container">
  <div class="profile-left">
  <img id="avatar" 
     src="/ACE/Admin/frontend/upload/default.jpg" 
     alt="Ảnh đại diện"
     onerror="this.src='/ACE/Admin/frontend/upload/default.jpg'; this.onerror=null;">
  </div>
     
  <div class="profile-right">
    <h2>Xin chào, <span class="highlight" id="ho_ten">Đang tải...</span></h2>
    
    <div class="info-item"><b>Địa chỉ:</b> <span id="dia_chi">...</span></div>
    <div class="info-item"><b>Tuổi/Giới tính:</b> <span id="tuoi">...</span> / <span id="gioi_tinh">...</span></div>
    <div class="info-item"><b>Chiều cao:</b> <span id="chieu_cao">...</span> cm</div>
    <div class="info-item"><b>Cân nặng:</b> <span id="can_nang">...</span> kg</div>
    <div class="info-item"><b>Kinh nghiệm:</b> <span id="kinh_nghiem">...</span></div>
    <div class="info-item"><b>Giá tiền:</b> <span id="tong_tien_kiem_duoc" style="color:#2ecc71; font-weight:bold">0</span> ₫</div>
    <div class="info-item"><b>Đánh giá:</b> <span id="danh_gia_tb">0</span>/5 <span id="stars"></span></div>

    <div class="button-group">
      <a href="DonHangChuaNhan.php" class="back-btn">Trang chủ</a>
      <a href="logout.php" class="logout-btn">Đăng xuất</a>
    </div>
  </div>
</div>

<script>
function renderStars(avg){
  const full = Math.floor(avg || 0);
  const half = (avg - full >= 0.5)?1:0;
  const empty = 5 - full - half;
  let html = '';
  for(let i=0;i<full;i++) html+='<span class="rating">⭐</span>';
  if(half) html+='<span class="rating">✨</span>';
  for(let i=0;i<empty;i++) html+='<span class="rating">☆</span>';
  return html;
}

function formatMoney(n) {
    return parseInt(n || 0).toLocaleString('vi-VN');
}

// Gọi API
fetch('../Backend/Canhan/api_profile.php')
.then(async res => {
    const text = await res.text(); 
    try {
        const data = JSON.parse(text);
        if (!res.ok) throw new Error(data.error || text);
        return data;
    } catch (e) {
        console.error("Server response:", text);
        throw new Error("Lỗi dữ liệu từ server.");
    }
})
.then(data => {
    const u = data.user;

    // === BẮT CHƯỚC 100% CÁCH LẤY ẢNH CỦA TRANG DANH SÁCH ===
    let hinh_anh_url = '../../../Admin/frontend/upload/default.jpg'; 

    if (u.hinh_anh && u.hinh_anh.trim() !== '') {
        // 1. Nếu là link online (http/https) → Giữ nguyên
        if (u.hinh_anh.startsWith('http')) {
            hinh_anh_url = u.hinh_anh;
        } 
        // 2. Nếu là link trong máy
        else {
            // Sửa lỗi chính tả 'fontend' thành 'frontend' nếu có
            let cleanPath = u.hinh_anh.replace('fontend/', 'frontend/');
            
            // Từ Canhan.php (Caregiver/Fontend/) → lùi 3 cấp ra ACE → vào Admin
            hinh_anh_url = '../../Admin/' + cleanPath;
        }
    }

    document.getElementById('avatar').src = hinh_anh_url;
    // =====================================================

    document.getElementById('ho_ten').innerText = u.ho_ten || 'Người dùng';
    document.getElementById('dia_chi').innerText = u.dia_chi || 'Chưa cập nhật';
    document.getElementById('tuoi').innerText = u.tuoi || '--';
    document.getElementById('gioi_tinh').innerText = u.gioi_tinh || '--';
    document.getElementById('chieu_cao').innerText = u.chieu_cao || '--';
    document.getElementById('can_nang').innerText = u.can_nang || '--';
    document.getElementById('kinh_nghiem').innerText = u.kinh_nghiem || 'Chưa có';
    document.getElementById('tong_tien_kiem_duoc').innerText = formatMoney(u.tong_tien_kiem_duoc);
    document.getElementById('danh_gia_tb').innerText = data.danh_gia_tb || '0';
    document.getElementById('stars').innerHTML = renderStars(data.danh_gia_tb || 0);
})
.catch(err => {
    alert(err.message);
    if (err.message.includes('đăng nhập')) {
        window.location.href = '../../Admin/login.php';
    }
});
</script>

</body>
</html>