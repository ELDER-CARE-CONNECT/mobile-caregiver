<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chi Tiết Đơn Hàng</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
/* --- CSS toàn bộ --- */
body { font-family: 'Inter', sans-serif; background: #f8f8fa; color: #333; line-height: 1.6; margin: 0; padding-top: 60px;}
.main-content { display: flex; justify-content: center; padding: 0 15px 30px; box-sizing: border-box; min-height: calc(100vh - 60px); margin-top: 5vh;}
.container { background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); width: 100%; max-width: 700px;}
h1 { color: #FF6B81; font-size: 28px; margin-bottom: 5px; font-weight: 800; text-align: center; }
.order-id { text-align: center; font-size: 14px; color: #888; margin-bottom: 25px; font-weight: 500; }
.section-box { background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #f0f0f0; }
.section-title { font-size: 18px; font-weight: 700; color: #333; border-bottom: 2px solid #FFD8E0; padding-bottom: 10px; margin-bottom: 15px; }
.detail-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px dashed #eee; font-size: 15px; }
.detail-row:last-child { border-bottom: none; }
.detail-label { font-weight: 500; color: #666; display: flex; align-items: center; }
.detail-value { font-weight: 600; color: #333; text-align: right; }
.icon { margin-right: 8px; color: #FF6B81; }
.service-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #f0f0f0; font-size: 15px; cursor:pointer; }
.service-item:last-child { border-bottom: none; }
.service-item strong { color: #555; font-weight: 600; }
.task-status { padding: 4px 10px; border-radius: 15px; font-size: 13px; font-weight: 600; text-transform: uppercase; }
.task-pending { background: #fff3e0; color: #ff9800; }
.task-done { background: #e8f5e9; color: #4caf50; }
.total-section { display: flex; justify-content: space-between; align-items: center; border-top: 2px solid #FF6B81; padding-top: 15px; margin-top: 10px; font-size: 20px; font-weight: 800; }
.status-tag { padding: 5px 10px; border-radius: 20px; font-weight: 700; font-size: 13px; text-transform: uppercase; }
.status-cho_xac_nhan { background: #fff3e0; color: #ff9800; }
.status-dang_hoan_thanh { background: #e3f2fd; color: #2196f3; }
.status-da_hoan_thanh { background: #e8f5e9; color: #4caf50; }
.status-da_huy { background: #ffebee; color: #f44336; }
.buttons { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 30px; }
.button { padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; transition: background-color 0.3s; text-decoration: none; color: white; font-size: 15px; font-weight: 600; text-align: center; display: inline-flex; align-items: center; gap: 8px; }
.btn-chat { background-color: #2196f3; }
.btn-confirm { background-color: #4caf50; }
.btn-cancel { background-color: #f44336; }
@media (max-width: 768px) { .container { padding: 20px; } .main-content { padding: 140px 10px 30px; } }
</style>
</head>
<?php include 'Dieuhuong.php'; ?>
<body>
<div class="main-content">
    <div class="container" id="orderContainer">
        <!-- Nội dung sẽ được JS load từ backend -->
    </div>
</div>

<script>
const id_don_hang = new URLSearchParams(window.location.search).get('id_don_hang');

async function fetchOrder(){
    const res = await fetch(`../Backend/Chitietdonhang/chitietdonhang.php?id_don_hang=${id_don_hang}`);
    const data = await res.json();
    if(data.error){ alert(data.error); return; }
    renderOrder(data);
}

function renderOrder(d){
    const container = document.getElementById('orderContainer');
    container.innerHTML = `
        <h1><i class="fas fa-file-invoice"></i> Chi Tiết Đơn Hàng</h1>
        <p class="order-id">Mã đơn hàng: <strong>#${d.donhang.id_don_hang}</strong></p>

        <div class="section-box">
            <div class="detail-row">
                <span class="detail-label"><i class="icon fas fa-info-circle"></i> Trạng Thái:</span>
                <span class="status-tag ${statusClass(d.donhang.trang_thai)}">${d.donhang.trang_thai}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="icon fas fa-calendar-alt"></i> Ngày Đặt:</span>
                <span class="detail-value">${new Date(d.donhang.ngay_dat).toLocaleString('vi-VN')}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="icon fas fa-credit-card"></i> Thanh toán:</span>
                <span class="detail-value">${d.donhang.hinh_thuc_thanh_toan}</span>
            </div>
        </div>

        <div class="section-box">
            <div class="section-title"><i class="icon fas fa-clipboard-list"></i> Dịch Vụ & Thời Gian</div>
            <div class="detail-row">
                <span class="detail-label"><i class="icon fas fa-clock"></i> Bắt đầu:</span>
                <span class="detail-value">${new Date(d.donhang.thoi_gian_bat_dau).toLocaleString('vi-VN')}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="icon fas fa-hourglass-end"></i> Kết thúc:</span>
                <span class="detail-value">${new Date(d.donhang.thoi_gian_ket_thuc).toLocaleString('vi-VN')}</span>
            </div>

            ${d.nhiem_vu.map(nv => `
                <div class="service-item" onclick="hoanThanhNhiemVu('${d.donhang.trang_thai}', '${nv.trang_thai_nhiem_vu}', ${nv.id_nhiem_vu})">
                    <strong>${nv.ten_nhiem_vu}</strong>
                    <span class="task-status ${nv.trang_thai_nhiem_vu=='chờ xác nhận'?'task-pending':'task-done'}">
                        ${nv.trang_thai_nhiem_vu}
                    </span>
                </div>
            `).join('')}
        </div>

        <div class="section-box">
            <div class="section-title"><i class="icon fas fa-map-marker-alt"></i> Thông Tin Liên Hệ/Địa Chỉ</div>
            <div class="detail-row">
                <span class="detail-label"><i class="icon fas fa-user"></i> Người liên hệ:</span>
                <span class="detail-value">${d.donhang.ten_khach_hang}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="icon fas fa-phone"></i> SĐT liên hệ:</span>
                <span class="detail-value">${d.donhang.so_dien_thoai}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label"><i class="icon fas fa-map-marked-alt"></i> Địa chỉ:</span>
                <span class="detail-value">${d.donhang.dia_chi_giao_hang}</span>
            </div>
        </div>

        <div class="section-box total-section">
            <span>Tổng Cộng</span>
            <span style="color: #FF6B81;">${Number(d.donhang.tong_tien).toLocaleString('vi-VN')} VND</span>
        </div>

        <div class="buttons">
          <a href="chat.php?id_don_hang=${d.donhang.id_don_hang}" class="button btn-chat">
            <i class="fas fa-comment-dots"></i> Chat
          </a>

          <button class="button btn-confirm" onclick="xacNhanDon()">
            <i class="fas fa-check-circle"></i> ${d.donhang.trang_thai=='chờ xác nhận'?'Nhận đơn':d.donhang.trang_thai=='đang hoàn thành'?'Hoàn thành đơn':d.donhang.trang_thai}
          </button>

          ${d.donhang.trang_thai=='chờ xác nhận' ? `<button class="button btn-cancel" onclick="huyDon()"><i class="fas fa-times"></i> Hủy đơn</button>` : ''}

         
        </div>
    `;
}

function statusClass(status){
    switch(status){
        case 'chờ xác nhận': return 'status-cho_xac_nhan';
        case 'đang hoàn thành': return 'status-dang_hoan_thanh';
        case 'đã hoàn thành': return 'status-da_hoan_thanh';
        case 'đã hủy': return 'status-da_huy';
        default: return '';
    }
}

function hoanThanhNhiemVu(trangThaiDon, trangThaiNhiemVu, idNhiemVu){
    if(trangThaiDon === 'đang hoàn thành' && trangThaiNhiemVu==='chờ xác nhận'){
        if(confirm("Bạn có muốn hoàn thành nhiệm vụ này không?")){
            postAction('hoan_thanh_nhiem_vu', idNhiemVu);
        }
    }
}

async function postAction(action, idNhiemVu=null){
    const params = new URLSearchParams({id_don_hang, action});
    if(idNhiemVu) params.append('id_nhiem_vu', idNhiemVu);

    const res = await fetch('../Backend/Chitietdonhang/chitietdonhang.php', {
        method:'POST',
        body: params
    });
    const data = await res.json();
    if(data.status==='success') fetchOrder();
    else if(data.message) alert(data.message);
}

function xacNhanDon(){
    const trangThaiDon = document.querySelector('.status-tag').textContent.trim();

    if(trangThaiDon === 'chờ xác nhận'){
        if(confirm("Bạn có chắc chắn nhận đơn hàng này không?")){
            postAction('xac_nhan_don');
        }
    } else if(trangThaiDon === 'đang hoàn thành'){
        // Kiểm tra tất cả nhiệm vụ đã hoàn thành
        const pendingTasks = Array.from(document.querySelectorAll('.task-status.task-pending'));
        if(pendingTasks.length > 0){
            alert("Vui lòng hoàn thành tất cả nhiệm vụ trước khi hoàn thành đơn!");
            return;
        }
        if(confirm("Bạn có chắc chắn hoàn thành đơn hàng này không?")){
            postAction('xac_nhan_don');
        }
    }
}


function huyDon(){
    if(confirm("Bạn có chắc chắn hủy đơn hàng này không?")) postAction('huy_don');
}

// Load order khi trang mở
fetchOrder();
</script>
</body>
</html>
