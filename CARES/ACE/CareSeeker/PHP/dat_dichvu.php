<?php
session_start();

// Kết nối DB
$conn = new mysqli("localhost", "root", "", "sanpham");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Nếu form được submit -> xử lý lưu don_hang
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_booking'])) {
    // Lấy dữ liệu POST (đã có input ẩn được JS gán trước khi submit)
    $id_cham_soc   = intval($_POST['id_cham_soc'] ?? 0);
    $ten_khach_hang= trim($_POST['ten_khach_hang'] ?? '');
    $so_dien_thoai = trim($_POST['so_dien_thoai'] ?? '');
    $dia_chi       = trim($_POST['dia_chi'] ?? '');
    $tong_tien     = floatval($_POST['tong_tien'] ?? 0);
    $ngay_bat_dau  = $_POST['ngay_bat_dau'] ?? null; 
    $ngay_ket_thuc = $_POST['ngay_ket_thuc'] ?? null; 
    $gio_bat_dau   = $_POST['gio_bat_dau'] ?? null; 
    $gio_ket_thuc  = $_POST['gio_ket_thuc'] ?? null; 
    $phuong_thuc   = $_POST['phuong_thuc'] ?? 'cash';

    // Kiểm tra cơ bản
    $errors = [];
    if ($id_cham_soc <= 0) $errors[] = "ID người chăm sóc không hợp lệ.";
    if ($tong_tien <= 0) $errors[] = "Tổng tiền không hợp lệ.";
    if (!$ngay_bat_dau || !$ngay_ket_thuc) $errors[] = "Chưa chọn ngày.";
    if (!$gio_bat_dau || !$gio_ket_thuc) $errors[] = "Chưa chọn giờ.";

    if (empty($errors)) {
        // Insert vào don_hang (id_khach_hang để NULL => nếu cần bạn có thể map user login)
        $sql = "INSERT INTO don_hang 
            (id_khach_hang, id_cham_soc, id_danh_gia, ngay_dat, tong_tien, dia_chi_giao_hang, ten_khach_hang, so_dien_thoai, trang_thai, thoi_gian_bat_dau, thoi_gian_ket_thuc)
            VALUES (NULL, ?, 0, CURDATE(), ?, ?, ?, ?, 'Chờ xác nhận', ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $errors[] = "Lỗi prepare: " . $conn->error;
        } else {
            // types: i (id_cham_soc), d (tong_tien), s (dia_chi), s (ten_khach_hang), s (so_dien_thoai), s (gio_bat_dau), s (gio_ket_thuc)
            $stmt->bind_param("idsssss", $id_cham_soc, $tong_tien, $dia_chi, $ten_khach_hang, $so_dien_thoai, $gio_bat_dau, $gio_ket_thuc);
            if ($stmt->execute()) {
                // Lưu thành công -> redirect về trang chi tiết hoặc trang thông báo
                $stmt->close();
                $conn->close();
                // Nếu phương thức Momo: bạn có thể redirect để hiển thị QR; ở đây redirect về chi tiết
                header("Location: chitiet_chamsoc.php?id=" . $id_cham_soc . "&booked=1");
                exit;
            } else {
                $errors[] = "Lỗi khi lưu đơn hàng: " . $stmt->error;
                $stmt->close();
            }
        }
    }
    // nếu có lỗi, tiếp tục xuống phần hiển thị form và show $errors
}

// Lấy id người chăm sóc (ưu tiên GET, nếu POST (sau submit thất bại) thì lấy POST)
$id = 0;
if (isset($_GET['id'])) $id = intval($_GET['id']);
elseif (isset($_POST['id_cham_soc'])) $id = intval($_POST['id_cham_soc']);

if ($id <= 0) {
    // Không có id, show lỗi
    echo "<h2 style='text-align:center;color:red;'>ID người chăm sóc không hợp lệ hoặc không được cung cấp.</h2>";
    exit;
}

// Truy vấn thông tin người chăm sóc
$stmt2 = $conn->prepare("SELECT * FROM nguoi_cham_soc WHERE id_cham_soc = ?");
$stmt2->bind_param("i", $id);
$stmt2->execute();
$res2 = $stmt2->get_result();
if ($res2->num_rows === 0) {
    echo "<h2 style='text-align:center;color:red;'>Không tìm thấy người chăm sóc này!</h2>";
    $stmt2->close();
    $conn->close();
    exit;
}
$row = $res2->fetch_assoc();
$stmt2->close();
// Giữ kết nối mở để có thể dùng nếu cần (nhưng chúng ta sẽ đóng ở cuối file)
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đặt dịch vụ - <?php echo htmlspecialchars($row['ho_ten']); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
/* CSS như bạn có, thêm chỉnh sửa nhỏ */
body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg,#fff4f6,#f8f9ff); margin:0; color:#333; }
.container { max-width:900px; margin:40px auto; background:#fff; border-radius:20px; padding:30px; box-shadow:0 10px 30px rgba(0,0,0,0.1); }
h1 { text-align:center; color:#ff6b81; }
form label { display:block; margin:10px 0 5px; font-weight:500; }
.row { display:flex; gap:15px; }
.col { flex:1; }
select, input[type="text"]#tongTien, input, select { width:100%; padding:10px 12px; height:42px; border:1px solid #ddd; border-radius:8px; box-sizing:border-box; font-size:15px; }
.btn-row { display:flex; justify-content:space-between; align-items:center; margin-top:25px; }
.btn-confirm { background:#ff6b81; color:#fff; border:none; padding:10px 20px; border-radius:8px; font-weight:600; cursor:pointer; }
.btn-confirm:hover { background:#ff4d6d; }
.btn-back { background:none; border:none; color:#444; cursor:pointer; }
.summary { background:#fff7f9; padding:20px; border-radius:10px; margin-bottom:25px; box-shadow:0 4px 15px rgba(0,0,0,0.05); }
.price { color:#ff4757; font-weight:600; font-size:18px; }
#qrBox { text-align:center; margin-top:30px; display:none; }
#qrBox img { width:240px; height:240px; margin-bottom:10px; }
.error-box { background:#ffecec; border:1px solid #ffb4bd; color:#9b1c1c; padding:10px; border-radius:6px; margin-bottom:12px; }
</style>
</head>
<body>

<div class="container">
  <h1> Đặt dịch vụ chăm sóc</h1>

  <?php
  // Nếu có lỗi từ phần POST thì hiển thị
  if (!empty($errors)) {
      echo '<div class="error-box"><ul>';
      foreach ($errors as $er) echo '<li>' . htmlspecialchars($er) . '</li>';
      echo '</ul></div>';
  }
  if (isset($_GET['booked'])) {
      echo '<div class="summary" style="border:1px solid #cfe9d8;color:#2a7a2a">Đặt dịch vụ thành công! Hệ thống đang chờ xác nhận.</div>';
  }
  ?>

  <div class="summary">
    <h3>Thông tin người chăm sóc</h3>
    <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($row['ho_ten']); ?></p>
    <p><strong>Kinh nghiệm:</strong> <?php echo htmlspecialchars($row['kinh_nghiem']); ?></p>
    <p><strong>Đánh giá:</strong> ⭐ <?php echo htmlspecialchars($row['danh_gia_tb']); ?>/5</p>
    <p><strong>Giá tiền/giờ:</strong> <span class="price"><?php echo number_format($row['tong_tien_kiem_duoc'], 0, ',', '.'); ?> đ/giờ</span></p>
  </div>

  <!-- FORM đặt -->
  <form id="bookingForm" method="post">
    <!-- Hidden values để PHP đọc khi submit -->
    <input type="hidden" name="id_cham_soc" value="<?php echo intval($row['id_cham_soc']); ?>">
    <input type="hidden" name="tong_tien" id="tong_tien_input">
    <input type="hidden" name="ngay_bat_dau" id="ngay_bat_dau_input">
    <input type="hidden" name="ngay_ket_thuc" id="ngay_ket_thuc_input">
    <input type="hidden" name="gio_bat_dau" id="gio_bat_dau_input">
    <input type="hidden" name="gio_ket_thuc" id="gio_ket_thuc_input">
    <input type="hidden" name="phuong_thuc" id="phuong_thuc_input">
    <!-- Thêm trường tên/địa chỉ/SDT cho trường hợp đặt hộ -->
    <input type="hidden" name="ten_khach_hang" id="ten_khach_hang_input">
    <input type="hidden" name="so_dien_thoai" id="so_dien_thoai_input">
    <input type="hidden" name="dia_chi" id="dia_chi_input">

    <div class="row">
      <div class="col">
        <label>Ngày bắt đầu</label>
        <input type="date" id="startDate" required>
      </div>
      <div class="col">
        <label>Ngày kết thúc</label>
        <input type="date" id="endDate" required>
      </div>
    </div>

    <div class="row" style="margin-top:12px">
      <div class="col">
        <label>Giờ bắt đầu</label>
        <input type="time" id="startTime" required>
      </div>
      <div class="col">
        <label>Giờ kết thúc</label>
        <input type="time" id="endTime" required>
      </div>
    </div>

    <div style="margin-top:12px">
      <label>Hồ sơ đặt</label>
      <select id="profileSelect">
        <option value="own">Sử dụng hồ sơ của tôi</option>
        <option value="new">Đặt hộ người khác</option>
      </select>
    </div>

    <div id="customProfile" style="display:none; margin-top:10px">
      <label>Họ và tên</label>
      <input type="text" id="hoTen">
      <label>Địa chỉ</label>
      <input type="text" id="diaChi">
      <label>Số điện thoại</label>
      <input type="text" id="soDienThoai">
    </div>

    <div style="margin-top:12px" class="form-group">
      <label for="tongTien">Tổng tiền (ước tính)</label>
      <input type="text" id="tongTien" readonly style="font-weight:600;color:#ff4757">
    </div>

    <div style="margin-top:12px" class="form-group">
      <label for="payment">Phương thức thanh toán</label>
      <select id="payment">
        <option value="cash">Tiền mặt</option>
        <option value="momo">Momo (QR)</option>
      </select>
    </div>

    <div class="btn-row">
      <button type="submit" name="submit_booking" class="btn-confirm">Xác nhận đặt dịch vụ</button>
      <button type="button" class="btn-back" onclick="window.history.back()">← Quay lại</button>
    </div>
  </form>

  <!-- Hộp QR thanh toán -->
  <div id="qrBox">
    <h3>Quét mã để thanh toán qua Momo 💖</h3>
    <img id="qrImage" src="" alt="Momo QR Code">
    <p><strong>Số tiền:</strong> <span id="qrAmount"></span></p>
    <p><strong>Nội dung:</strong> Thanh toán dịch vụ chăm sóc cho <?php echo htmlspecialchars($row['ho_ten']); ?></p>
  </div>
</div>

<script>
// Lấy giá tiền/giờ từ PHP (an toàn vì $row tồn tại)
const pricePerHour = <?php echo floatval($row['tong_tien_kiem_duoc']); ?>;

// Hiển thị form nhập mới nếu đặt hộ người khác
document.getElementById("profileSelect").addEventListener("change", function(){
  document.getElementById("customProfile").style.display =
    this.value === "new" ? "block" : "none";
});

// Tính tổng tiền
function calcTotal(){
  const startVal = document.getElementById("startDate").value;
  const endVal = document.getElementById("endDate").value;
  const startH = document.getElementById("startTime").value;
  const endH = document.getElementById("endTime").value;

  if(!startVal || !endVal || !startH || !endH) return 0;
  const start = new Date(startVal);
  const end = new Date(endVal);
  // tính số ngày (bao gồm cả ngày bắt đầu và kết thúc)
  const days = Math.floor((end - start) / (1000*60*60*24)) + 1;
  // tính số giờ trong mỗi ngày (giờ kết thúc - giờ bắt đầu)
  const hours = (parseInt(endH.split(':')[0],10) + parseInt(endH.split(':')[1],10)/60) - (parseInt(startH.split(':')[0],10) + parseInt(startH.split(':')[1],10)/60);
  if (days > 0 && hours > 0){
    const total = days * hours * pricePerHour;
    document.getElementById("tongTien").value = Math.round(total).toLocaleString() + " đ";
    return total;
  } else {
    document.getElementById("tongTien").value = "";
    return 0;
  }
}
document.querySelectorAll("#startDate,#endDate,#startTime,#endTime").forEach(el => el.addEventListener("change", calcTotal));

// Submit form: điền input ẩn rồi gửi POST
document.getElementById("bookingForm").addEventListener("submit", function(e){
  // Cập nhật hidden inputs
  const total = Math.round(calcTotal());
  if (total <= 0) {
    alert("Vui lòng chọn ngày/giờ hợp lệ để tính tổng tiền.");
    e.preventDefault();
    return;
  }
  document.getElementById("tong_tien_input").value = total;
  document.getElementById("ngay_bat_dau_input").value = document.getElementById("startDate").value;
  document.getElementById("ngay_ket_thuc_input").value = document.getElementById("endDate").value;
  document.getElementById("gio_bat_dau_input").value = document.getElementById("startTime").value;
  document.getElementById("gio_ket_thuc_input").value = document.getElementById("endTime").value;
  document.getElementById("phuong_thuc_input").value = document.getElementById("payment").value;

  // Nếu đặt hộ, gán tên/địa chỉ/đt vào input ẩn, ngược lại có thể để rỗng hoặc fill từ session user
  if (document.getElementById("profileSelect").value === "new") {
    const ten = document.getElementById("hoTen").value.trim();
    const diachi = document.getElementById("diaChi").value.trim();
    const sdt = document.getElementById("soDienThoai").value.trim();
    if (!ten || !sdt) {
      alert("Vui lòng nhập họ tên và số điện thoại của người được đặt hộ.");
      e.preventDefault();
      return;
    }
    document.getElementById("ten_khach_hang_input").value = ten;
    document.getElementById("dia_chi_input").value = diachi;
    document.getElementById("so_dien_thoai_input").value = sdt;
  } else {
    // nếu có session user, bạn có thể fill tên/sdt từ session ở đây. Hiện để trống.
    document.getElementById("ten_khach_hang_input").value = "";
    document.getElementById("dia_chi_input").value = "";
    document.getElementById("so_dien_thoai_input").value = "";
  }

  // Nếu chọn momo: show QR và không submit ngay (bạn có thể chọn submit rồi redirect sang trang thanh toán)
  if (document.getElementById("payment").value === "momo") {
    e.preventDefault();
    const amountText = total.toLocaleString() + " đ";
    const qrLink = `https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=MOMO%20PAY%20-%20${amountText}`;
    document.getElementById("qrBox").style.display = "block";
    document.getElementById("qrImage").src = qrLink;
    document.getElementById("qrAmount").textContent = amountText;
    window.scrollTo({top: document.getElementById("qrBox").offsetTop, behavior: 'smooth'});
    
    return;
  }

});
</script>
</body>
</html>
