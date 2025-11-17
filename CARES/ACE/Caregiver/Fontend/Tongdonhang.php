<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đơn Hàng Đã Nhận - Caregiver</title>
<link rel="stylesheet" href="../CSS/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />

<style>
/* CSS giữ nguyên như file cũ */
.accepted-orders-container { max-width: 1200px; margin:0 auto; padding:20px; }
.hero { padding: 18px 4px 6px; }
.hero-title { margin:0 0 6px 0; font-size:32px; font-weight:800; color:#111827; }
.hero-subtitle { margin:0 0 14px 0; color:#6b7280; font-size:15px; }

.summary-cards { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:20px; margin-bottom:30px; }
.summary-card { background:white; padding:25px; border-radius:15px; box-shadow:0 4px 20px rgba(0,0,0,0.08); text-align:center; }
.summary-card i { font-size:40px; margin-bottom:15px; color:#667eea; }
.summary-card h3 { margin:0 0 10px; font-size:32px; font-weight:700; color:#1f2937; }
.summary-card p { margin:0; color:#6b7280; font-weight:500; }

.orders-table-container { overflow-x:auto; }
.orders-table { width:100%; border-collapse:collapse; background:white; border-radius:10px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
.orders-table th, .orders-table td { padding:15px 12px; border-bottom:1px solid #f3f4f6; text-align:left; font-size:14px; color:#1f2937; }
.orders-table th { border-bottom:2px solid #e5e7eb; background:#f8f9fa; font-weight:600; }
.customer-cell { display:flex; align-items:center; gap:10px; }
.customer-avatar-small { width:40px; height:40px; border-radius:50%; background:linear-gradient(135deg,#667eea 0%,#764ba2 100%); display:flex; align-items:center; justify-content:center; color:white; font-size:14px; font-weight:bold; flex-shrink:0; }
.action-btn-small { padding:6px 12px; border:1px solid #e5e7eb; border-radius:6px; background:white; color:#6b7280; font-size:12px; cursor:pointer; transition:all 0.2s ease; display:inline-flex; align-items:center; gap:5px; }
.action-btn-small.primary { background:#667eea; color:white; border-color:#667eea; }
.action-btn-small.primary:hover { background:#5a67d8; border-color:#5a67d8; }
.pagination { display:flex; justify-content:center; align-items:center; gap:10px; margin-top:30px; }
.page-btn { padding:10px 15px; border:2px solid #e5e7eb; background:white; border-radius:8px; cursor:pointer; transition:all 0.3s ease; }
.page-btn:hover:not(:disabled) { border-color:rgb(40,72,216); color:rgb(40,72,216); }
.page-btn.active { background:rgb(40,72,216); border-color:rgb(40,72,216); color:white; }
.page-btn:disabled { opacity:0.5; cursor:not-allowed; }
.empty-state { text-align:center; padding:60px 20px; color:#6b7280; }
.empty-state i { font-size:64px; margin-bottom:20px; color:#d1d5db; }
.empty-state h3 { margin:0 0 10px; font-size:24px; color:#374151; }

.status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: inline-block; }
.status-da_hoan_thanh { background-color: #d1fae5; color: #065f46; }
.status-da_huy { background-color: #fee2e2; color: #dc2626; }
.status-dang_hoan_thanh { background-color: #e0f2fe; color: #0284c7; }
.status-cho_xac_nhan { background-color: #fef3c7; color: #b45309; }
</style>
</head>
<body>
<?php include 'Dieuhuong.php'; ?>
<div class="accepted-orders-container">
    <div class="hero">
        <h1><i class="fas fa-check-circle"></i> Tổng đơn hàng</h1>
        <p>Danh sách các đơn hàng bạn đã nhận</p>
    </div>

    <!-- Summary Cards -->
    <div class="summary-cards" id="summary-cards"></div>

    <!-- Orders Table -->
    <div class="orders-table-container" id="orders-table-container"></div>

    <!-- Pagination -->
    <div class="pagination" id="pagination"></div>
</div>

<script>
function formatCurrency(amount) {
    return amount.toLocaleString('vi-VN', {style:'currency', currency:'VND'});
}
function formatDate(dateStr) {
    const d = new Date(dateStr);
    return d.toLocaleDateString('vi-VN');
}
function getAvatarInitials(name) {
    if(!name) return 'KH';
    const words = name.trim().split(' ');
    if(words.length >= 2) return (words[0][0]+words[words.length-1][0]).toUpperCase();
    return name.slice(0,2).toUpperCase();
}

let currentPage = 1;
let search = '';
let from_date = '';
let to_date = '';

function loadOrders() {
    fetch(`../Backend/Lichsu/api_orders.php?page=${currentPage}&search=${encodeURIComponent(search)}&from_date=${from_date}&to_date=${to_date}`)
    .then(res => res.json())
    .then(data => {
        const { orders, summary } = data;

        // Summary Cards
        document.getElementById('summary-cards').innerHTML = `
            <div class="summary-card">
                <i class="fas fa-shopping-cart"></i>
                <h3>${summary.totalOrders}</h3>
                <p>Tổng đơn hàng</p>
            </div>
            <div class="summary-card">
                <i class="fas fa-money-bill-wave"></i>
                <h3>${formatCurrency(summary.totalAmount)}</h3>
                <p>Tổng doanh thu</p>
            </div>
        `;

        // Orders Table
        const tableContainer = document.getElementById('orders-table-container');
        if(orders.length === 0) {
            tableContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Không có đơn hàng nào</h3>
                    <p>Chưa có đơn hàng trong khoảng thời gian này</p>
                </div>
            `;
        } else {
            let html = `<table class="orders-table">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Khách hàng</th>
                        <th>Thời gian</th>
                        <th>Giá tiền</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>`;
            orders.forEach(order => {
                let statusClass = '';
                switch(order.trang_thai) {
                    case 'đã hoàn thành': statusClass='status-da_hoan_thanh'; break;
                    case 'đã hủy': statusClass='status-da_huy'; break;
                    case 'đang hoàn thành': statusClass='status-dang_hoan_thanh'; break;
                    case 'chờ xác nhận': statusClass='status-cho_xac_nhan'; break;
                }
                html += `
                <tr>
                    <td>#${order.id_don_hang}</td>
                    <td>${formatDate(order.ngay_dat)}</td>
                    <td>
                        <div class="customer-cell">
                            <div class="customer-avatar-small">${getAvatarInitials(order.ten_khach_hang)}</div>
                            <div>
                                <div>${order.ten_khach_hang}</div>
                                <div style="font-size:12px;color:#6b7280">${order.so_dien_thoai}</div>
                            </div>
                        </div>
                    </td>
                    <td>${order.thoi_gian_bat_dau} - ${order.thoi_gian_ket_thuc}</td>
                    <td>${formatCurrency(order.tong_tien)}</td>
                    <td><span class="status-badge ${statusClass}">${order.trang_thai}</span></td>
                    <td>
                        <a href="chitietdonhang.php?id_don_hang=${order.id_don_hang}" class="action-btn-small primary">
                            <i class="fas fa-eye"></i> Xem
                        </a>
                    </td>
                </tr>`;
            });
            html += `</tbody></table>`;
            tableContainer.innerHTML = html;
        }

        // Pagination
        const pagination = document.getElementById('pagination');
        let paginationHtml = '';
        if(summary.totalPages > 1){
            if(currentPage > 1) paginationHtml += `<button class="page-btn" onclick="changePage(${currentPage-1})">&laquo;</button>`;
            for(let i=1;i<=summary.totalPages;i++){
                paginationHtml += `<button class="page-btn ${i===currentPage?'active':''}" onclick="changePage(${i})">${i}</button>`;
            }
            if(currentPage < summary.totalPages) paginationHtml += `<button class="page-btn" onclick="changePage(${currentPage+1})">&raquo;</button>`;
        }
        pagination.innerHTML = paginationHtml;
    });
}

function changePage(page){
    currentPage = page;
    loadOrders();
}

// Load lần đầu
loadOrders();
</script>
</body>
</html>
  <script>
    window.addEventListener('load', () => {
        if(!sessionStorage.getItem('reloaded')){
            sessionStorage.setItem('reloaded', 'true');
            location.reload(); // reload lại trang 1 lần
        }
    });
  </script>
  <script>
    window.addEventListener('pageshow', function(event) {
    if (event.persisted || window.performance && window.performance.getEntriesByType('navigation')[0].type === 'back_forward') {
        location.reload(); // reload dữ liệu mới
    }
});

  </script>