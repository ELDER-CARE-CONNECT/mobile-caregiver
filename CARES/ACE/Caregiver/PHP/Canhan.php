<?php
session_name("CARES_SESSION");
session_start();

if (!isset($_SESSION['caregiver_id'])) {
    header("Location: ../../Admin/login.php");
    exit;
}

include 'connect.php';
$id = $_SESSION['caregiver_id'];

// Lấy thông tin người chăm sóc
$stmt = $conn->prepare("SELECT * FROM nguoi_cham_soc WHERE id_cham_soc = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();  // ✔️ FIX rồi
$user = $result->fetch_assoc();

if (!$user) {
    echo "<p style='color:white;text-align:center;'>Không tìm thấy thông tin người dùng.</p>";
    exit;
}
?>

?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thông tin cá nhân</title>
<style>
  /* ===== NÚT ĐĂNG XUẤT GÓC PHẢI ===== */
.logout-top {
  position: fixed;
  top: 20px;
  right: 30px;
  background-color: #ff6b81;
  color: white;
  padding: 12px 24px;
  font-size: 18px;
  border-radius: 10px;
  text-decoration: none;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  transition: all 0.3s ease;
  z-index: 1000;
}

.logout-top:hover {
  background-color: #e05268;
  transform: translateY(-2px);
}

  body {
    font-family: 'Segoe UI', sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, #ffd1ff 0%, #ffe6f7 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
  }

  .profile-container {
    background: #fff;
    border-radius: 25px;
    width: 1200px; /* ✅ mở rộng khung */
    max-width: 95%;
    display: flex;
    padding: 60px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    align-items: center;
  }

  .profile-left {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
  }

  .profile-left img {
    width: 340px; /* ✅ to hơn */
    height: 340px;
    border-radius: 25px;
    object-fit: cover;
    box-shadow: 0 8px 25px rgba(0,0,0,0.2);
  }

  .profile-right {
    flex: 1.5;
    padding-left: 70px; /* ✅ khoảng cách rộng hơn */
  }

  h2 {
    font-size: 40px; /* ✅ chữ lớn hơn */
    font-weight: 700;
    margin-bottom: 20px;
  }

  .highlight {
    color: #e91e63;
  }

  .info-item {
    font-size: 22px; /* ✅ phóng to chữ thông tin */
    margin: 14px 0;
    line-height: 1.6;
  }

  .info-item b {
    color: #333;
  }

  .rating {
    color: #FFD700;
    font-size: 26px; /* ✅ icon to hơn */
    display: inline-block;
    margin-left: 6px;
  }

  .back-btn {
    margin-top: 40px;
    display: inline-block;
    padding: 16px 35px;
    border-radius: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    font-weight: bold;
    transition: 0.3s;
    font-size: 20px;
  }

  .back-btn:hover {
    opacity: 0.85;
    transform: scale(1.03);
  }

  @media (max-width: 900px) {
    .profile-container {
      flex-direction: column;
      text-align: center;
      width: 95%;
      padding: 30px;
    }

    .profile-left img {
      width: 250px;
      height: 250px;
      margin-bottom: 25px;
    }

    .profile-right {
      padding-left: 0;
    }
  }
</style>
</head>
<body>

<div class="profile-container">
  <div class="profile-left">
    <img src="../../<?php echo htmlspecialchars($user['hinh_anh']); ?>" alt="Ảnh đại diện">
  </div>
  <div class="profile-right">
    <h2>Xin chào, <span class="highlight"><?php echo htmlspecialchars($user['ho_ten']); ?></span> 👋</h2>
    <div class="info-item"><b>Địa chỉ:</b> <?php echo htmlspecialchars($user['dia_chi']); ?></div>
    <div class="info-item"><b>Tuổi:</b> <?php echo htmlspecialchars($user['tuoi']); ?></div>
    <div class="info-item"><b>Giới tính:</b> <?php echo htmlspecialchars($user['gioi_tinh']); ?></div>
    <div class="info-item"><b>Chiều cao:</b> <?php echo htmlspecialchars($user['chieu_cao']); ?> cm</div>
    <div class="info-item"><b>Cân nặng:</b> <?php echo htmlspecialchars($user['can_nang']); ?> kg</div>

    <div class="info-item">
      <b>Đánh giá trung bình:</b> <?php echo htmlspecialchars($user['danh_gia_tb']); ?>/5
      <span class="rating">⭐</span><span class="rating">⭐</span><span class="rating">⭐</span><span class="rating">⭐</span><span class="rating">✨</span>
    </div>

    <div class="info-item"><b>Kinh nghiệm:</b> <?php echo htmlspecialchars($user['kinh_nghiem']); ?></div>
    <div class="info-item"><b>Số đơn đã nhận:</b> <?php echo htmlspecialchars($user['don_da_nhan']); ?></div>
    <div class="info-item"><b>Tổng tiền kiếm được:</b> <?php echo number_format($user['tong_tien_kiem_duoc'], 0, ',', '.'); ?> ₫</div>

    <a href="javascript:history.back()" class="back-btn">⬅ Quay lại</a>
  </div>
</div>

</body>
<a href="logout.php" class="logout-top">Đăng xuất</a>
</html>

