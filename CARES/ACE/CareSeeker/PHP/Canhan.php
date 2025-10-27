<?php
session_start();
if (!isset($_SESSION['profile'])) {
  header('Location: hoso.php');
  exit;
}
$profile = $_SESSION['profile'];
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Trang cá nhân</title>
<style>
:root{
  --accent:#ff6b81;
  --bg-left:linear-gradient(135deg,#ffb6b9,#fae3d9,#bbded6,#61c0bf);
  --bg-right:#fff5f6;
}
*{box-sizing:border-box;margin:0;padding:0}
body{
  font-family:Inter,Arial,sans-serif;
  background:var(--bg-left);
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
}
.container{
  display:flex;
  width:90%;
  max-width:1100px;
  background:#fff;
  border-radius:24px;
  box-shadow:0 10px 40px rgba(0,0,0,0.1);
  overflow:hidden;
}
.left{
  flex:1;
  background:var(--bg-left);
  display:flex;
  align-items:center;
  justify-content:center;
  padding:40px;
}
.left img{
  width:320px;
  height:320px;
  border-radius:20px;
  object-fit:cover;
  box-shadow:0 8px 20px rgba(0,0,0,0.2);
  background:#fff;
}
.right{
  flex:1.2;
  background:var(--bg-right);
  padding:50px 60px;
  display:flex;
  flex-direction:column;
  justify-content:center;
  min-height:400px;
}
.right h2{
  color:#222;
  font-size:26px;
  margin-bottom:6px;
}
.right p{
  color:#555;
  margin:6px 0;
  font-size:16px;
}
.info-group strong{color:#111;}
.buttons{
  display:flex;
  flex-direction:column;
  gap:12px;
  margin-top:30px;
}
button{
  padding:12px 20px;
  font-size:15px;
  border:none;
  border-radius:10px;
  cursor:pointer;
  transition:all 0.2s ease;
  font-weight:600;
}
button:hover{opacity:0.9;transform:translateY(-1px);}
.btn-edit{background:var(--accent);color:#fff;}
.btn-complaint{background:#fff;border:2px solid var(--accent);color:var(--accent);}
.btn-logout{background:#f0f0f0;color:#333;}
.hidden{display:none}

/* KHIEU NAI SECTION */
.complaint-section h3{color:var(--accent);margin-bottom:10px}
.complaint-item{
  padding:10px;border:1px solid #eee;border-radius:8px;margin-top:10px;background:#fafafa;cursor:pointer;
}
textarea,select{
  width:100%;
  padding:10px;
  border-radius:8px;
  border:1px solid #ccc;
  margin-top:5px;
  font-size:15px;
}
.btn-small{
  margin-top:12px;
  background:var(--accent);
  color:#fff;
  border:none;
  border-radius:8px;
  padding:8px 14px;
  cursor:pointer;
}
.btn-back{
  margin-top:12px;
  background:#ccc;
  color:#000;
  border:none;
  border-radius:8px;
  padding:8px 14px;
  cursor:pointer;
}
</style>
</head>
<body>

<div class="container">
  <!-- ẢNH TRÁI -->
  <div class="left">
    <img src="<?php echo $profile['avatar'] ? $profile['avatar'] : 'uploads/default.png'; ?>" alt="avatar">
  </div>

  <!-- THÔNG TIN / KHIẾU NẠI PHẢI -->
  <div class="right">

    <!-- PHẦN THÔNG TIN CÁ NHÂN -->
    <div id="infoSection">
      <h2>Xin chào, <?php echo htmlspecialchars($profile['ho_ten']); ?> 👋</h2>
      <div class="info-group">
        <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($profile['dia_chi']); ?></p>
        <p><strong>SĐT:</strong> <?php echo htmlspecialchars($profile['so_dt']); ?></p>
        <p><strong>Tuổi:</strong> <?php echo htmlspecialchars($profile['tuoi']); ?></p>
        <p><strong>Giới tính:</strong> <?php echo htmlspecialchars($profile['gioi_tinh']); ?></p>
        <p><strong>Chiều cao:</strong> <?php echo htmlspecialchars($profile['chieu_cao']); ?> cm</p>
        <p><strong>Cân nặng:</strong> <?php echo htmlspecialchars($profile['can_nang']); ?> kg</p>
      </div>

      <div class="buttons">
        <button class="btn-edit" onclick="window.location.href='hoso.php'">Chỉnh sửa hồ sơ</button>
        <button class="btn-complaint" id="btnKhieuNai">Khiếu nại</button>
        <button class="btn-logout" onclick="window.location.href='logout.php'">Đăng xuất</button>
      </div>
    </div>

    <!-- PHẦN KHIẾU NẠI -->
    <div id="complaintSection" class="hidden complaint-section">
      <h3>Khiếu nại</h3>

      <div id="complaintList">
        <div id="complaintItems"></div>
        <button class="btn-small" id="btnNewComplaint">+ Gửi khiếu nại mới</button>
        <button class="btn-back" id="backToInfo">← Quay lại</button>
      </div>

      <div id="newComplaintForm" class="hidden">
        <label>Chọn đơn hàng</label>
        <select id="orderSelect">
          <option value="">-- Chọn đơn hàng --</option>
          <option>Đơn hàng #1001</option>
          <option>Đơn hàng #1002</option>
        </select>

        <label>Mô tả khiếu nại</label>
        <textarea id="complaintText" rows="3" placeholder="Nhập nội dung khiếu nại..."></textarea>

        <button class="btn-small" id="sendComplaint">Gửi khiếu nại</button>
        <button class="btn-back" id="cancelComplaint">Hủy</button>
      </div>

      <div id="complaintDetail" class="hidden">
        <p><strong>Đơn hàng:</strong> <span id="detailOrder"></span></p>
        <p><strong>Mô tả:</strong> <span id="detailText"></span></p>
        <p><strong>Phản hồi:</strong> <span id="detailReply"></span></p>
        <button class="btn-back" id="backList">← Quay lại danh sách</button>
      </div>
    </div>
  </div>
</div>

<script>
const btnKhieuNai = document.getElementById('btnKhieuNai');
const infoSection = document.getElementById('infoSection');
const complaintSection = document.getElementById('complaintSection');
const btnBackToInfo = document.getElementById('backToInfo');

btnKhieuNai.onclick = () => {
  infoSection.classList.add('hidden');
  complaintSection.classList.remove('hidden');
};
btnBackToInfo.onclick = () => {
  complaintSection.classList.add('hidden');
  infoSection.classList.remove('hidden');
};

// Xử lý khiếu nại
const complaintItems = document.getElementById('complaintItems');
const newComplaintForm = document.getElementById('newComplaintForm');
const complaintList = document.getElementById('complaintList');
const complaintDetail = document.getElementById('complaintDetail');

const btnNewComplaint = document.getElementById('btnNewComplaint');
const btnCancelComplaint = document.getElementById('cancelComplaint');
const btnSendComplaint = document.getElementById('sendComplaint');
const btnBackList = document.getElementById('backList');

let complaints = [];

btnNewComplaint.onclick = () => {
  complaintList.classList.add('hidden');
  newComplaintForm.classList.remove('hidden');
};
btnCancelComplaint.onclick = () => {
  newComplaintForm.classList.add('hidden');
  complaintList.classList.remove('hidden');
};
btnSendComplaint.onclick = () => {
  const order = document.getElementById('orderSelect').value;
  const text = document.getElementById('complaintText').value.trim();
  if (!order || !text) {
    alert("Vui lòng nhập đầy đủ thông tin!");
    return;
  }
  complaints.push({order,text,reply:"Đang chờ phản hồi..."});
  renderComplaints();
  document.getElementById('orderSelect').value="";
  document.getElementById('complaintText').value="";
  newComplaintForm.classList.add('hidden');
  complaintList.classList.remove('hidden');
};

function renderComplaints(){
  complaintItems.innerHTML = "";
  if(complaints.length === 0){
    complaintItems.innerHTML = "<p>Chưa có khiếu nại nào.</p>";
    return;
  }
  complaints.forEach((c,i)=>{
    const div = document.createElement("div");
    div.className = "complaint-item";
    div.innerHTML = `<strong>${c.order}</strong><br>${c.text}`;
    div.onclick = ()=> showDetail(i);
    complaintItems.appendChild(div);
  });
}

function showDetail(i){
  complaintList.classList.add('hidden');
  complaintDetail.classList.remove('hidden');
  document.getElementById("detailOrder").innerText = complaints[i].order;
  document.getElementById("detailText").innerText = complaints[i].text;
  document.getElementById("detailReply").innerText = complaints[i].reply;
}

btnBackList.onclick = () => {
  complaintDetail.classList.add('hidden');
  complaintList.classList.remove('hidden');
};

renderComplaints();
</script>
</body>
</html>
