<?php
$activePage = 'khachhang';
$pageTitle = 'Khách Hàng';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản Lí Khách Hàng</title>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<style>
/* ===== Tổng thể ===== */
body { font-family: "Segoe UI", sans-serif; background-color: #f0f4f8; color: #333; margin:0; padding:0; }
.container { display: flex; min-height: 100vh; }
.main-content { flex-grow: 1; background: #fff; padding: 25px 40px; border-radius: 12px; margin:20px; box-shadow: 0 0 10px rgba(0,0,0,0.05);}
.navbar { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #3498db; padding-bottom: 15px; margin-bottom: 10px; }
.navbar h1 { color:#3498db; font-size:22px; font-weight:600; }
.search input { padding: 7px 10px; border:1px solid #ccc; border-radius:6px; width:260px; }
.search button { background:#3498db; color:white; border:none; padding:7px 12px; border-radius:6px; cursor:pointer; transition:0.3s; }
.search button:hover { background:#2980b9; }
h2 { color:#2c3e50; font-size:20px; margin-bottom:15px; border-left:5px solid #3498db; padding-left:10px; }
table { width:100%; border-collapse: collapse; margin-top:25px; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
th { background:#3498db; color:#fff; padding:12px; font-weight:600; text-transform:uppercase; font-size:14px; text-align:center; }
td { padding:10px; border-bottom:1px solid #eee; text-align:center; font-size:15px; color:#2c3e50; }
tr:nth-child(even) { background:#f9f9f9; }
tr:hover { background:#eaf4ff; transition:0.2s; }
img { width:70px; height:70px; border-radius:8px; object-fit:cover; box-shadow:0 0 5px rgba(0,0,0,0.1); }
.show-orders { background-color:#3498db; color:white; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-size:14px; font-weight:600; transition:0.3s; }
.show-orders:hover { background-color:#2980b9; transform:scale(1.05); }
.order-details-row { background-color:#f8f9fa; transition:all 0.3s ease; }
.order-details-row table { width:100%; border:1px solid #ddd; margin-top:8px; border-radius:6px; }
.order-details-row th { background-color:#5dade2; color:white; padding:6px; }
.order-details-row td { background-color:#fff; }
</style>
</head>
<body>
<div class="container">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <header class="navbar">
            <h1>Trang Quản Lí Khách Hàng</h1>
            <div class="search">
                <input type="text" id="searchInput" placeholder="Tìm kiếm khách hàng...">
                <button id="searchBtn">🔍</button>
            </div>
        </header>

        <section class="stats">
            <h2>Thông Tin Khách Hàng</h2>
            <div id="customer-info">
                <table id="customerTable">
                    <thead>
                        <tr>
                            <th>Mã KH</th><th>Hình ảnh</th><th>Họ và tên</th>
                            <th>Địa chỉ</th><th>Số điện thoại</th><th>Tuổi</th>
                            <th>Giới tính</th><th>Chiều cao</th><th>Cân nặng</th>
                            <th>Tổng đơn hàng</th><th>Tổng chi tiêu</th><th>Đơn chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td colspan="12" style="text-align:center;">Đang tải dữ liệu...</td></tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<script>
const backendPath = '../../backend/customers/khachhang.php'; // Sửa: Từ frontend/dashboard/ lên root, rồi xuống backend/customers/

function loadCustomers(search='') {
    $.getJSON(backendPath, { search: search })
    .done(function(res){
        const tbody = $('#customerTable tbody');
        tbody.empty();

        if(!res || res.status !== 'success'){
            tbody.append('<tr><td colspan="12" style="text-align:center;color:red;">Lỗi: '+(res?.message||'Không xác định')+'</td></tr>');
            return;
        }

        if(res.customers.length === 0){
            tbody.append('<tr><td colspan="12" style="text-align:center;">Không có khách hàng nào</td></tr>');
            return;
        }

        res.customers.forEach(c => {
            const img = c.hinh_anh ? `../../uploads/${c.hinh_anh}` : '../../uploads/default.png'; // Sửa đường dẫn ảnh nếu uploads ở root
            let ordersHtml = '';
            if(c.orders && c.orders.length){
                c.orders.forEach(o=>{
                    ordersHtml += `<tr>
                        <td>${o.id_don_hang}</td>
                        <td>${o.ngay_dat}</td>
                        <td>${o.ten_khach_hang}</td>
                        <td>${o.ten_nguoi_cham_soc}</td>
                        <td>${o.thoi_gian_lam_viec}</td>
                        <td>${Number(o.tong_tien).toLocaleString('vi-VN')}</td>
                        <td>${o.trang_thai}</td>
                        <td>${o.danh_gia}</td>
                    </tr>`;
                });
            } else {
                ordersHtml = `<tr><td colspan="8" style="text-align:center;">Không có đơn hàng nào</td></tr>`;
            }

            tbody.append(`
                <tr>
                    <td>${c.id_khach_hang}</td>
                    <td><img src="${img}" alt="Ảnh KH"></td>
                    <td>${c.ten_khach_hang}</td>
                    <td>${c.dia_chi}</td>
                    <td>${c.so_dien_thoai}</td>
                    <td>${c.tuoi}</td>
                    <td>${c.gioi_tinh}</td>
                    <td>${c.chieu_cao}</td>
                    <td>${c.can_nang}</td>
                    <td>${c.tong_don}</td>
                    <td>${Number(c.tong_tien).toLocaleString('vi-VN')}</td>
                    <td><button class="show-orders" data-id="${c.id_khach_hang}">Xem đơn hàng</button></td>
                </tr>
                <tr class="order-details-row" id="orders-${c.id_khach_hang}" style="display:none;">
                    <td colspan="12">
                        <table>
                            <tr>
                                <th>Mã đơn hàng</th><th>Ngày đặt</th><th>Tên khách hàng</th>
                                <th>Tên người chăm sóc</th><th>Thời gian làm việc</th><th>Giá tiền</th>
                                <th>Trạng thái</th><th>Đánh giá</th>
                            </tr>
                            ${ordersHtml}
                        </table>
                    </td>
                </tr>
            `);
        });
    })
    .fail(function(){
        const tbody = $('#customerTable tbody');
        tbody.empty();
        tbody.append('<tr><td colspan="12" style="text-align:center;color:red;">Lỗi kết nối server</td></tr>');
    });
}

$(document).ready(function(){
    loadCustomers();

    $('#searchBtn').click(function(){
        const search = $('#searchInput').val().trim();
        loadCustomers(search);
    });

    $(document).on('click', '.show-orders', function(){
        const id = $(this).data('id');
        $('#orders-' + id).slideToggle(300);
    });
});
</script>
</body>
</html>