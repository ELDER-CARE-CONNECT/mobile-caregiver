<?php
$activePage = 'khachhang';
$pageTitle = 'Quản Lý Khách Hàng';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $pageTitle; ?></title>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<style>
/* ===== Sidebar + Main CSS ===== */
* { margin:0; padding:0; box-sizing:border-box; font-family:Arial,sans-serif; }
.sidebar { width:250px; background:linear-gradient(180deg,#f8f9fa 0%,#e9ecef 100%); padding:20px; height:100vh; position:fixed; left:0; display:flex; flex-direction:column; }
.sidebar ul { list-style:none; margin-top:10px; padding:0; }
.sidebar ul li { margin:12px 0; }
.sidebar ul li a { display:flex; align-items:center; text-decoration:none; color:#000; font-weight:700; padding:10px 12px; border-radius:8px; transition:0.3s; }
.sidebar ul li.active a, .sidebar ul li a:hover { background:#007bff; color:#fff; transform:translateX(5px); }

/* Main content */
.main-content { margin-left:250px; padding:20px; }
.navbar { display:flex; justify-content:space-between; align-items:center; border-bottom:3px solid #3498db; padding-bottom:15px; margin-bottom:10px; }
.navbar h1 { color:#3498db; font-size:22px; font-weight:600; }
.search input { padding:7px 10px; border:1px solid #ccc; border-radius:6px; width:260px; }
.search button { background:#3498db; color:white; border:none; padding:7px 12px; border-radius:6px; cursor:pointer; }

/* Filter & summary */
.filter-box { margin-top:15px; margin-bottom:10px; display:flex; justify-content:flex-end; align-items:center; gap:10px; }
.avg-box { background:#eaf4ff; padding:10px 20px; border-radius:8px; margin-top:15px; display:inline-block; font-weight:600; color:#1e3a8a; }

/* Table */
table { width:100%; border-collapse:collapse; margin-top:25px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
th { background:#3498db; color:white; padding:12px; font-weight:600; }
td { padding:10px; border-bottom:1px solid #eee; text-align:center; color:#1e3a8a; }
tr:nth-child(even) { background:#f9f9f9; }
tr:hover { background:#eaf4ff; }

.avatar-img { width:50px; height:50px; border-radius:50%; object-fit:cover; border:2px solid #e5e7eb; }
.action-links a { text-decoration:none; margin:0 5px; color:#2980b9; }
.action-links a:hover { color:#e74c3c; }
.loading { color:#007bff; font-style:italic; }
.error { color:#e74c3c; }

.show-orders { background:#3498db; color:#fff; border:none; padding:6px 12px; border-radius:6px; cursor:pointer; font-weight:600; transition:0.3s; }
.show-orders:hover { background:#2563eb; transform:scale(1.05); }
.order-details-row { display:none; background:#eaf4ff; }
.order-details-row table { width:100%; border:1px solid #ddd; border-radius:6px; margin-top:8px; border-collapse:collapse; }
.order-details-row th { background:#3498db; color:#fff; padding:6px; font-weight:600; }
.order-details-row td { background:#fff; padding:6px; color:#1e3a8a; }

/* Multi images */
.image-container { display:flex; flex-wrap:wrap; justify-content:center; gap:4px; }
.image-container img { width:40px; height:40px; border-radius:4px; object-fit:cover; }

/* Responsive */
@media (max-width:768px){
    .main-content{ padding:15px; }
    .search input{ width:150px; }
}
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main-content">
    <header class="navbar">
        <h1>Quản Lý Khách Hàng</h1>
        <div class="search">
            <input type="text" id="searchInput" placeholder="Tìm kiếm khách hàng...">
            <button id="searchBtn">🔍</button>
        </div>
    </header>

    <div class="filter-box">
        <label for="filter">Lọc theo tổng đơn:</label>
        <select id="filter">
            <option value="">-- Tất cả --</option>
            <option value="high">Nhiều nhất</option>
            <option value="low">Ít nhất</option>
        </select>
        <button id="resetBtn">↻ Reset</button>
    </div>

    <div class="avg-box">
        🏷 Tổng khách hàng: <span id="totalCustomer">Đang tải...</span>
    </div>

    <table id="customerTable">
        <thead>
            <tr>
                <th>Mã KH</th>
                <th>Hình ảnh</th>
                <th>Họ & Tên</th>
                <th>Địa chỉ</th>
                <th>SĐT</th>
                <th>Tuổi</th>
                <th>Giới tính</th>
                <th>Chiều cao</th>
                <th>Cân nặng</th>
                <th>Tổng đơn</th>
                <th>Tổng chi tiêu</th>
                <th>Đơn chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="12" class="loading">Đang tải dữ liệu...</td></tr>
        </tbody>
    </table>
</main>

<script>
const backendPath = '../../backend/customers/khachhang.php';

function getAvatarUrl(path){
    if(!path) return '../auth/images/default_user.png';
    return path.startsWith('http') ? path : '../../../CareSeeker/PHP/Frontend/' + path.replace(/^\/+/, '');
}

function loadCustomers(search=''){
    $.getJSON(backendPath, { search: search })
    .done(function(res){
        const tbody = $('#customerTable tbody');
        tbody.empty();
        if(!res || res.status !== 'success'){
            tbody.append('<tr><td colspan="12" class="error">Lỗi: '+(res?.message||'Không xác định')+'</td></tr>');
            $('#totalCustomer').text('0');
            return;
        }
        if(res.customers.length===0){
            tbody.append('<tr><td colspan="12" class="loading">Không có khách hàng nào</td></tr>');
            $('#totalCustomer').text('0');
            return;
        }

        $('#totalCustomer').text(res.customers.length);

        res.customers.forEach(c=>{
            let imagesHtml = '';
            if(c.hinh_anh && Array.isArray(c.hinh_anh)) c.hinh_anh.forEach(imgUrl=>{
                imagesHtml+=`<img src="${imgUrl}" alt="KH" onerror="this.style.display='none'">`;
            });

            let ordersHtml = '';
            if(c.orders && c.orders.length>0){
                c.orders.forEach(o=>{
                    ordersHtml+=`<tr>
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
            } else ordersHtml=`<tr><td colspan="8" style="text-align:center;">Không có đơn hàng</td></tr>`;

            tbody.append(`
                <tr>
                    <td>${c.id_khach_hang}</td>
                    <td><div class="image-container">${imagesHtml}</div></td>
                    <td>${c.ten_khach_hang}</td>
                    <td>${c.dia_chi}</td>
                    <td>${c.so_dien_thoai}</td>
                    <td>${c.tuoi}</td>
                    <td>${c.gioi_tinh}</td>
                    <td>${c.chieu_cao}</td>
                    <td>${c.can_nang}</td>
                    <td>${c.tong_don}</td>
                    <td>${Number(c.tong_tien).toLocaleString('vi-VN')}</td>
                    <td><button class="show-orders" data-id="${c.id_khach_hang}">Xem đơn</button></td>
                </tr>
                <tr class="order-details-row" id="orders-${c.id_khach_hang}">
                    <td colspan="12">
                        <table>
                            <tr>
                                <th>Mã đơn</th><th>Ngày đặt</th><th>KH</th><th>Người CS</th>
                                <th>Thời gian</th><th>Giá tiền</th><th>Trạng thái</th><th>Đánh giá</th>
                            </tr>
                            ${ordersHtml}
                        </table>
                    </td>
                </tr>
            `);
        });
    })
    .fail(function(xhr){
        const tbody = $('#customerTable tbody');
        tbody.empty();
        tbody.append('<tr><td colspan="12" class="error">Lỗi kết nối server</td></tr>');
        $('#totalCustomer').text('0');
    });
}

$(document).ready(function(){
    loadCustomers();
    $('#searchBtn').click(()=>loadCustomers($('#searchInput').val().trim()));
    $('#searchInput').keypress(e=>{ if(e.which===13) loadCustomers($('#searchInput').val().trim()); });
    $(document).on('click','.show-orders', function(){
        const id=$(this).data('id');
        $('#orders-'+id).slideToggle(300);
    });
});
</script>
</body>
</html>
