<?php
session_start();

/* ✅ Nếu chưa có hồ sơ, chuyển đến trang tạo hồ sơ */
if (!isset($_SESSION['profile']) || empty($_SESSION['profile'])) {
    header('Location: hoso.php');
    exit;
}

/* ✅ Lấy thông tin hồ sơ đã lưu trong session */
$profile = $_SESSION['profile'];

/* ✅ Nếu có kết nối DB thì load lại cho chính xác (tùy chọn) */
@include 'connect.php';
if (isset($conn) && isset($profile['id_khach_hang'])) {
    $id = mysqli_real_escape_string($conn, $profile['id_khach_hang']);
    $rs = mysqli_query($conn, "SELECT * FROM khach_hang WHERE id_khach_hang='$id' LIMIT 1");
    if ($rs && mysqli_num_rows($rs) === 1) {
        $profile = mysqli_fetch_assoc($rs);
        $_SESSION['profile'] = $profile; // cập nhật lại session
    }
}

/* ✅ Hàm tiện ích nhỏ */
function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

/* ✅ Lấy dữ liệu hồ sơ */
$avatar   = $profile['avatar']   ?? 'uploads/default.png';
$ho_ten   = $profile['ten_khach_hang'] ?? $profile['ho_ten'] ?? '';
$dia_chi  = $profile['dia_chi']  ?? '';
$so_dt    = $profile['so_dien_thoai'] ?? $profile['so_dt'] ?? '';
$tuoi     = $profile['tuoi']     ?? '';
$gioi_tinh= $profile['gioi_tinh']?? '';
$chieu_cao= $profile['chieu_cao']?? '';
$can_nang = $profile['can_nang'] ?? '';

/* ✅ Xử lý khi người dùng cập nhật hồ sơ */
if (isset($_POST['update_profile'])) {
    // chỉ cập nhật những gì điền vào, giữ nguyên phần còn lại
    $new = [
        'ten_khach_hang' => trim($_POST['ho_ten'] ?? $ho_ten),
        'dia_chi'        => trim($_POST['dia_chi'] ?? $dia_chi),
        'so_dien_thoai'  => trim($_POST['so_dt'] ?? $so_dt),
        'tuoi'           => trim($_POST['tuoi'] ?? $tuoi),
        'gioi_tinh'      => trim($_POST['gioi_tinh'] ?? $gioi_tinh),
        'chieu_cao'      => trim($_POST['chieu_cao'] ?? $chieu_cao),
        'can_nang'       => trim($_POST['can_nang'] ?? $can_nang),
    ];

    // cập nhật session
    $_SESSION['profile'] = array_merge($profile, $new);

    // nếu có DB thì update luôn
    if (isset($conn) && isset($profile['id_khach_hang'])) {
        $id = mysqli_real_escape_string($conn, $profile['id_khach_hang']);
        $sql = "UPDATE khach_hang SET
            ten_khach_hang = '".mysqli_real_escape_string($conn,$new['ten_khach_hang'])."',
            dia_chi        = '".mysqli_real_escape_string($conn,$new['dia_chi'])."',
            so_dien_thoai  = '".mysqli_real_escape_string($conn,$new['so_dien_thoai'])."',
            tuoi           = '".mysqli_real_escape_string($conn,$new['tuoi'])."',
            gioi_tinh      = '".mysqli_real_escape_string($conn,$new['gioi_tinh'])."',
            chieu_cao      = '".mysqli_real_escape_string($conn,$new['chieu_cao'])."',
            can_nang       = '".mysqli_real_escape_string($conn,$new['can_nang'])."'
            WHERE id_khach_hang='$id'";
        mysqli_query($conn, $sql);
    }

    echo "<script>alert('Cập nhật hồ sơ thành công!'); window.location='Canhan.php';</script>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Trang cá nhân</title>
<style>
:root {
  --accent: #ff6b81;
  --bg-left: linear-gradient(135deg, #ffb6b9, #fae3d9, #bbded6, #61c0bf);
  --bg-right: #fff5f6;
}

/* RESET */
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

body {
  font-family: "Inter", Arial, sans-serif;
  background: var(--bg-left);
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  zoom: 1.2; /* 🔹 toàn trang phóng to 120% */
}

/* ===== CONTAINER (khung chính) ===== */
.container {
  display: flex;
  width: 96%; /* tăng độ rộng khung */
  max-width: 1500px; /* 🔹 khung to hơn */
  background: #fff;
  border-radius: 32px; /* bo tròn hơn một chút */
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  margin: 60px auto;
}

/* ===== CỘT TRÁI (ảnh) ===== */
.left {
  flex: 1.2;
  background: var(--bg-left);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 70px;
}

.left img {
  width: 480px; /* 🔹 to hơn */
  height: 480px;
  border-radius: 30px;
  object-fit: cover;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
  background: #fff;
}

/* ===== CỘT PHẢI (thông tin & nút) ===== */
.right {
  flex: 1.6; /* tăng độ rộng phần chữ */
  background: var(--bg-right);
  padding: 100px 120px; /* 🔹 thêm không gian thoáng */
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-height: 650px;
}

/* ===== PHẦN TIÊU ĐỀ ===== */
.right h2 {
  color: #111;
  font-size: 44px; /* 🔹 lớn rõ hơn */
  font-weight: 700;
  margin-bottom: 18px;
}

/* ===== PHẦN THÔNG TIN ===== */
.right p {
  color: #333;
  margin: 12px 0;
  font-size: 22px; /* 🔹 chữ to hơn rõ rệt */
  line-height: 1.7;
}

.info-group strong {
  color: #000;
  font-weight: 600;
}

/* ===== CÁC NÚT ===== */
.buttons {
  display: flex;
  flex-direction: column;
  gap: 22px;
  margin-top: 45px;
}

button {
  padding: 18px 28px;
  font-size: 20px; /* 🔹 to hơn */
  border: none;
  border-radius: 14px;
  cursor: pointer;
  transition: all 0.25s ease;
  font-weight: 600;
}

button:hover {
  opacity: 0.9;
  transform: translateY(-2px);
}

.btn-edit {
  background: var(--accent);
  color: #fff;
}

.btn-complaint {
  background: #fff;
  border: 2px solid var(--accent);
  color: var(--accent);
}

.btn-logout {
  background: #f0f0f0;
  color: #333;
}

/* ===== FORM CHỈNH SỬA ===== */
#editSection label {
  font-size: 20px; /* chữ label to hơn */
  font-weight: 600;
  color: #444;
  margin-top: 12px;
}

#editSection input,
#editSection select {
  font-size: 19px;
  padding: 12px 14px;
  border-radius: 10px;
  border: 1px solid #ccc;
  margin-top: 6px;
}

#editSection h3 {
  font-size: 26px;
  color: var(--accent);
  margin-bottom: 15px;
}

.btn-small,
.btn-back {
  font-size: 18px;
  padding: 12px 20px;
  border-radius: 10px;
}

/* ===== PHẦN KHIẾU NẠI ===== */
.complaint-section h3 {
  font-size: 26px;
  color: var(--accent);
  margin-bottom: 14px;
}

.complaint-item {
  padding: 14px;
  font-size: 18px;
  border: 1px solid #eee;
  border-radius: 10px;
  margin-top: 12px;
  background: #fafafa;
  cursor: pointer;
}
.order-card {
  background: #fff7f8;
  border: 1px solid #ffe0e4;
  border-radius: 12px;
  padding: 15px;
  margin-bottom: 15px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.05);
  transition: transform 0.2s ease;
}
.order-card:hover {
  transform: translateY(-3px);
}
.send-complaint-btn {
  background: var(--accent);
  color: white;
  border: none;
  border-radius: 8px;
  padding: 8px 14px;
  cursor: pointer;
  font-weight: 600;
  margin-top: 8px;
}
.send-complaint-btn:hover {
  opacity: 0.9;
}


/* ===== SCROLL (nếu có form dài) ===== */
html {
  scroll-behavior: smooth;
}

.right h2{color:#222;font-size:26px;margin-bottom:6px;}
.right p{color:#555;margin:6px 0;font-size:16px;}
.info-group strong{color:#111;}
.buttons{display:flex;flex-direction:column;gap:12px;margin-top:30px;}
button{padding:12px 20px;font-size:15px;border:none;border-radius:10px;cursor:pointer;transition:all 0.2s ease;font-weight:600;}
button:hover{opacity:.9;transform:translateY(-1px);}
.btn-edit{background:var(--accent);color:#fff;}
.btn-complaint{background:#fff;border:2px solid var(--accent);color:var(--accent);}
.btn-logout{background:#f0f0f0;color:#333;}
.hidden{display:none}

/* Khiếu nại */
.complaint-section h3{color:var(--accent);margin-bottom:10px}
.complaint-item{padding:10px;border:1px solid #eee;border-radius:8px;margin-top:10px;background:#fafafa;cursor:pointer;}
textarea,select,input{width:100%;padding:10px;border-radius:8px;border:1px solid #ccc;margin-top:5px;font-size:15px;}
.btn-small{margin-top:12px;background:var(--accent);color:#fff;border:none;border-radius:8px;padding:8px 14px;cursor:pointer;}
.btn-back{margin-top:12px;background:#ccc;color:#000;border:none;border-radius:8px;padding:8px 14px;cursor:pointer;}
</style>
</head>
<body>

<div class="container">
  <div class="left">
    <img src="<?php echo h($avatar); ?>" alt="avatar">
  </div>

  <div class="right">

    <!-- HIỂN THỊ HỒ SƠ -->
    <div id="infoSection">
      <h2>Xin chào, <?php echo h($ho_ten); ?> 👋</h2>
      <div class="info-group">
        <p><strong>Địa chỉ:</strong> <?php echo h($dia_chi); ?></p>
        <p><strong>SĐT:</strong> <?php echo h($so_dt); ?></p>
        <p><strong>Tuổi:</strong> <?php echo h($tuoi); ?></p>
        <p><strong>Giới tính:</strong> <?php echo h($gioi_tinh); ?></p>
        <p><strong>Chiều cao:</strong> <?php echo h($chieu_cao); ?> cm</p>
        <p><strong>Cân nặng:</strong> <?php echo h($can_nang); ?> kg</p>
      </div>

      <div class="buttons">
        <button class="btn-edit" id="btnEdit">Chỉnh sửa hồ sơ</button>
        <button class="btn-complaint" id="btnKhieuNai">Khiếu nại</button>
        <button class="btn-logout" onclick="window.location.href='logout.php'">Đăng xuất</button>
      </div>
    </div>

    <!-- FORM CHỈNH SỬA -->
    <div id="editSection" class="hidden">
      <h3>Chỉnh sửa hồ sơ</h3>
      <form method="POST">
        <label>Họ và tên</label>
        <input type="text" name="ho_ten" value="<?php echo h($ho_ten); ?>">
        <label>Địa chỉ</label>
        <input type="text" name="dia_chi" value="<?php echo h($dia_chi); ?>">
        <label>Số điện thoại</label>
        <input type="text" name="so_dt" value="<?php echo h($so_dt); ?>">
        <label>Tuổi</label>
        <input type="number" name="tuoi" value="<?php echo h($tuoi); ?>">
        <label>Giới tính</label>
        <select name="gioi_tinh">
          <option value="Nam" <?php echo ($gioi_tinh=='Nam'?'selected':''); ?>>Nam</option>
          <option value="Nữ" <?php echo ($gioi_tinh=='Nữ'?'selected':''); ?>>Nữ</option>
          <option value="Khác" <?php echo ($gioi_tinh=='Khác'?'selected':''); ?>>Khác</option>
        </select>
        <label>Chiều cao (cm)</label>
        <input type="number" name="chieu_cao" value="<?php echo h($chieu_cao); ?>">
        <label>Cân nặng (kg)</label>
        <input type="number" name="can_nang" value="<?php echo h($can_nang); ?>">
        <div style="margin-top:15px;">
          <button type="submit" name="update_profile" class="btn-edit">Lưu thay đổi</button>
          <button type="button" class="btn-back" id="btnCancelEdit">Hủy</button>
        </div>
      </form>
    </div>

<!-- PHẦN KHIẾU NẠI -->
<div id="complaintSection" class="hidden complaint-section">
  <h3>Khiếu nại</h3>

  <?php
  // ✅ LẤY DANH SÁCH ĐƠN HÀNG CỦA KHÁCH HÀNG
  $id_khach = $_SESSION['profile']['id_khach_hang'] ?? null;
  $donhangs = [];

  if ($id_khach && isset($conn)) {
      $sql = "
          SELECT d.id_don_hang, d.id_cham_soc, d.ngay_dat, d.tong_tien, k.ten_khach_hang
          FROM don_hang d
          JOIN khach_hang k ON d.id_khach_hang = k.id_khach_hang
          WHERE d.id_khach_hang = '$id_khach'
      ";
      $rs = mysqli_query($conn, $sql);
      if ($rs && mysqli_num_rows($rs) > 0) {
          while ($row = mysqli_fetch_assoc($rs)) {
              $donhangs[] = $row;
          }
      } else {
          echo "<p>⚠️ Không có đơn hàng trong DB (id_khach_hang = $id_khach)</p>";
      }
  } else {
      echo "<p>⚠️ Không có ID khách hàng trong session hoặc chưa kết nối DB</p>";
  }
  ?>

  <div id="complaintList">
    <?php if (!empty($donhangs)): ?>
      <?php foreach ($donhangs as $d): ?>
        <div class="order-card">
          <p><strong>Mã đơn hàng:</strong> <?= htmlspecialchars($d['id_don_hang']) ?></p>
          <p><strong>ID người chăm sóc:</strong> <?= htmlspecialchars($d['id_cham_soc']) ?></p>
          <p><strong>Ngày đặt:</strong> <?= htmlspecialchars($d['ngay_dat']) ?></p>
          <p><strong>Tổng tiền:</strong> <?= number_format($d['tong_tien'], 0, ',', '.') ?>₫</p>
          <p><strong>Tên khách hàng:</strong> <?= htmlspecialchars($d['ten_khach_hang']) ?></p>
          <button class="btn-small send-complaint-btn" 
                  data-id="<?= htmlspecialchars($d['id_don_hang']) ?>">Gửi khiếu nại</button>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Không có đơn hàng nào để khiếu nại.</p>
    <?php endif; ?>

    <button class="btn-back" id="backToInfo">← Quay lại</button>
  </div>
</div>

<style>
.order-cards { display:flex; flex-wrap:wrap; gap:20px; margin-top:20px; }
.order-card {
  display:flex; width:45%; background:#fff; border:1px solid #eee;
  border-radius:12px; padding:15px; box-shadow:0 4px 8px rgba(0,0,0,0.05);
}
.order-card img {
  width:100px; height:100px; border-radius:10px; object-fit:cover; margin-right:15px;
}
.order-info h4 { color:#ff6b81; margin-bottom:6px; }
.order-info p { margin:4px 0; font-size:14px; color:#444; }
</style>

<script>
function goToComplaint(orderId){
  alert("Mở form khiếu nại cho đơn #" + orderId);
  // bạn có thể thay alert bằng mở modal hoặc chuyển sang form khiếu nại riêng
}
</script>


<script>
// ==================== LẤY PHẦN TỬ HTML ====================
const infoSection = document.getElementById('infoSection');
const editSection = document.getElementById('editSection');
const complaintSection = document.getElementById('complaintSection');

const btnEdit = document.getElementById('btnEdit');
const btnCancelEdit = document.getElementById('btnCancelEdit');
const btnKhieuNai = document.getElementById('btnKhieuNai');
const btnBackToInfo = document.getElementById('backToInfo');

// ==================== XỬ LÝ CHUYỂN ĐỔI GIAO DIỆN ====================

// Bấm “Chỉnh sửa hồ sơ”
if (btnEdit) {
  btnEdit.addEventListener('click', () => {
    infoSection.classList.add('hidden');
    editSection.classList.remove('hidden');
  });
}

// Bấm “Hủy chỉnh sửa”
if (btnCancelEdit) {
  btnCancelEdit.addEventListener('click', () => {
    editSection.classList.add('hidden');
    infoSection.classList.remove('hidden');
  });
}

// Bấm “Khiếu nại”
if (btnKhieuNai) {
  btnKhieuNai.addEventListener('click', () => {
    infoSection.classList.add('hidden');
    editSection.classList.add('hidden');
    complaintSection.classList.remove('hidden');
  });
}

// Bấm “← Quay lại”
if (btnBackToInfo) {
  btnBackToInfo.addEventListener('click', () => {
    complaintSection.classList.add('hidden');
    infoSection.classList.remove('hidden');
  });
}

// ==================== GỬI KHIẾU NẠI ====================
document.querySelectorAll('.send-complaint-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    const idDon = btn.getAttribute('data-id');
    const reason = prompt(`Nhập nội dung khiếu nại cho đơn hàng #${idDon}:`);
    if (!reason) return;
    alert(`✅ Đã gửi khiếu nại cho đơn hàng #${idDon}\n📩 Nội dung: ${reason}`);
  });
});
</script>

</body>
</html>
