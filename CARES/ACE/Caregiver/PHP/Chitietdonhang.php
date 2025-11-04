<?php
session_name("CARES_SESSION");
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['caregiver_id'])) {
    echo "<script>alert('Vui lòng đăng nhập trước!'); window.location.href='../../login/login.php';</script>";
    exit;
}
?>
<!doctype html>
<html lang="vi">
<head>
<meta charset="utf-8">
<title>Chi tiết đơn hàng</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
 body {
  font-family: 'Segoe UI', sans-serif;
  background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%);
  margin: 0;
  padding: 40px;
  color: #333;
}

h1 {
  text-align: center;
  color: white;
  font-weight: 700;
  margin-bottom: 40px;
}

.orders-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 30px;
  justify-content: center;
  max-width: 1400px;
  margin: 0 auto;
}

@media (min-width: 1200px) {
  .orders-grid {
    grid-template-columns: repeat(4, 1fr);
  }
}

.order-card {
  background: white;
  border-radius: 15px;
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
  overflow: hidden;
  display: flex;
  flex-direction: column;
  transition: all 0.25s ease;
}

.order-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.order-header {
  background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
  color: white;
  padding: 15px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: bold;
}

.order-body {
  padding: 20px;
  font-size: 14px;
}

.order-body p {
  margin: 8px 0;
  line-height: 1.5;
}

.status-badge {
  padding: 5px 10px;
  border-radius: 15px;
  font-size: 12px;
  font-weight: bold;
  text-transform: uppercase;
}

.status-completed {
  background-color: #d1fae5;
  color: #065f46;
}

.status-pending {
  background-color: #fef3c7;
  color: #92400e;
}

.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 6px;
  margin-top: 10px;
}

.detail-item {
  background: #f9fafb;
  border-radius: 8px;
  padding: 6px 10px;
  font-size: 13px;
  color: #333;
}

.sum {
  text-align: right;
  font-weight: 700;
  color: #333;
  padding: 10px 15px 15px;
  border-top: 1px solid #eee;
}
</style>
</head>
<body>
<div class="container">
  <h1>Danh sách đơn hàng của bạn</h1>
  <div id="ordersList"><p class="no-data">Đang tải dữ liệu...</p></div>
</div>

<script>
async function loadOrders() {
  try {
    const res = await fetch("get_order_details.php", { credentials: "include" });
    const data = await res.json();

    const list = document.getElementById("ordersList");
    if (!data.success) {
      list.innerHTML = `<p class="no-data">${data.error || 'Không thể tải dữ liệu'}</p>`;
      return;
    }
    if (data.data.length === 0) {
  list.innerHTML = `<p class="no-data">Không có đơn hàng nào</p>`;
  return;
}

list.innerHTML = `
  <div class="orders-grid">
    ${data.data.map(order => `
      <div class="order-card">
        <div class="order-header">
          <h3>Đơn hàng #${order.id_don_hang}</h3>
          <span class="status-badge ${order.trang_thai === 'Đã giao' ? 'status-completed' : 'status-pending'}">
            ${order.trang_thai}
          </span>
        </div>
        <div class="order-body">
          <p><b>Khách hàng:</b> ${order.ten_khach_hang}</p>
          <p><b>Địa chỉ:</b> ${order.dia_chi_giao_hang}</p>
          <p><b>SĐT:</b> ${order.so_dien_thoai}</p>
          <div class="detail-grid">
            <div class="detail-item">Ngày đặt: ${formatDate(order.ngay_dat)}</div>
            <div class="detail-item">Thời gian: ${order.thoi_gian_bat_dau} - ${order.thoi_gian_ket_thuc}</div>
            <div class="detail-item">Dịch vụ: ${order.dich_vu || 'Chăm sóc tại nhà'}</div>
            <div class="detail-item">Thanh toán: ${order.phuong_thuc_thanh_toan || 'Tiền mặt'}</div>
          </div>
        </div>
        <div class="sum">Tổng tiền: ${formatCurrency(order.tong_tien)} ₫</div>
      </div>
    `).join("")}
  </div>
`;


  } catch (err) {
    document.getElementById("ordersList").innerHTML =
      `<p class="no-data">Lỗi kết nối: ${err.message}</p>`;
  }
}

function formatCurrency(num) {
  return new Intl.NumberFormat("vi-VN").format(num);
}
function formatDate(dateStr) {
  const d = new Date(dateStr);
  return d.toLocaleDateString("vi-VN");
}

document.addEventListener("DOMContentLoaded", loadOrders);
</script>
</body>
</html>
