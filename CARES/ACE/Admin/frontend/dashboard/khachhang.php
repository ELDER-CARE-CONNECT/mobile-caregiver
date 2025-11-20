<?php
<<<<<<< HEAD
$activePage = 'khachhang';
$pageTitle = 'Quản Lý Khách Hàng';
=======
// khachhang.php
// Đảm bảo biến $activePage được set để sidebar highlight đúng
$activePage = 'khachhang';
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<<<<<<< HEAD
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
=======
    <meta charset="UTF-8">
    <title>Quản Lý Khách Hàng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* CSS Cơ bản */
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; margin: 0; }
        .main-content { margin-left: 250px; padding: 30px; transition: all 0.3s; }
        
        /* Header & Search */
        .header-section { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); }
        .page-title { font-size: 24px; font-weight: 700; color: #111827; margin: 0; }
        .search-box { position: relative; width: 300px; }
        .search-box input { width: 100%; padding: 10px 15px 10px 40px; border: 1px solid #e5e7eb; border-radius: 8px; outline: none; transition: 0.2s; }
        .search-box input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1); }
        .search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #9ca3af; }

        /* Table Styles */
        .table-container { background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        thead { background-color: #f9fafb; border-bottom: 1px solid #e5e7eb; }
        th { text-align: left; padding: 15px 20px; font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px; }
        td { padding: 15px 20px; border-bottom: 1px solid #f3f4f6; color: #374151; vertical-align: middle; font-size: 14px; }
        tr:last-child td { border-bottom: none; }
        tr:hover { background-color: #f9fafb; }

        /* Avatar Style */
        .user-info { display: flex; align-items: center; gap: 12px; }
        .avatar-img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #e5e7eb; }
        .user-details { display: flex; flex-direction: column; }
        .user-name { font-weight: 600; color: #111827; }
        .user-email { font-size: 12px; color: #6b7280; }

        /* Status Badge */
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-stats { background: #e0e7ff; color: #4338ca; }

        /* Loading & Empty */
        .loading, .empty-state { text-align: center; padding: 40px; color: #6b7280; }
        
        /* Responsive */
        @media (max-width: 768px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="header-section">
            <h1 class="page-title">Quản Lý Khách Hàng</h1>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Tìm theo tên hoặc SĐT...">
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Khách hàng</th>
                        <th>Liên hệ</th>
                        <th>Địa chỉ</th>
                        <th>Thống kê</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody id="customerTableBody">
                    <tr><td colspan="5" class="loading"><i class="fas fa-spinner fa-spin"></i> Đang tải dữ liệu...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Đường dẫn API (Bạn đã tạo ở bước trước)
        // File này ở: ACE/Admin/frontend/dashboard/khachhang.php
        // API ở: ACE/Admin/backend/customers/api_customers.php
        // Cần lùi ra frontend -> ra Admin -> vào backend...
        const API_URL = '../../backend/customers/api_customers.php';

        // --- HÀM XỬ LÝ ẢNH THÔNG MINH ---
        // --- HÀM XỬ LÝ ẢNH (ĐÃ CHỈNH ĐƯỜNG DẪN SANG CARESEEKER) ---
        function getAvatarUrl(path) {
            // 1. Nếu dữ liệu trống -> Trả về ảnh mặc định
            if (!path || path.trim() === '') {
                return '../auth/images/default_user.png'; 
            }

            // 2. Nếu là link Online (Google, Facebook...) -> Giữ nguyên
            if (path.startsWith('http')) {
                return path;
            }

            // 3. Xử lý link Local (Lấy từ thư mục CareSeeker)
            
            // Bước A: Làm sạch đường dẫn trong DB (nếu DB lỡ lưu chữ 'frontend/' thừa)
            // Ví dụ: DB lưu 'frontend/uploads/anh.jpg' -> chuyển thành 'uploads/anh.jpg'
            let cleanPath = path.replace('fontend/', '').replace('frontend/', '');
            
            // Đảm bảo không bị dư dấu / ở đầu
            if (cleanPath.startsWith('/')) cleanPath = cleanPath.substring(1);

            // Bước B: Tạo đường dẫn "xuyên không" sang thư mục CareSeeker
            // Từ: ACE/Admin/frontend/dashboard/
            // Sang: ACE/CareSeeker/PHP/Frontend/
            return '../../../CareSeeker/PHP/Frontend/' + cleanPath;
        }

        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        }

        function renderTable(customers) {
            const tbody = document.getElementById('customerTableBody');
            tbody.innerHTML = '';

            if (customers.length === 0) {
                tbody.innerHTML = `<tr><td colspan="5" class="empty-state">Không tìm thấy khách hàng nào.</td></tr>`;
                return;
            }

            customers.forEach(c => {
                // Xử lý ảnh
                const avatarSrc = getAvatarUrl(c.hinh_anh);
                
                // Xử lý giới tính để hiện icon
                const genderIcon = c.gioi_tinh === 'Nam' ? '<i class="fas fa-mars" style="color:#3b82f6"></i>' : 
                                  (c.gioi_tinh === 'Nữ' ? '<i class="fas fa-venus" style="color:#ec4899"></i>' : '');

                const html = `
                    <tr>
                        <td>
                            <div class="user-info">
                                <img src="${avatarSrc}" alt="${c.ten_khach_hang}" class="avatar-img" 
                                     onerror="this.onerror=null; this.src='../auth/images/default_user.png';">
                                <div class="user-details">
                                    <span class="user-name">${c.ten_khach_hang}</span>
                                    <span class="user-email">${genderIcon} ${c.tuoi !== '—' ? c.tuoi + ' tuổi' : ''}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight:500">${c.so_dien_thoai}</div>
                        </td>
                        <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            ${c.dia_chi}
                        </td>
                        <td>
                            <span class="badge badge-stats">${c.tong_don} đơn</span><br>
                            <small style="color:#059669; font-weight:600;">${formatCurrency(c.tong_tien)}</small>
                        </td>
                        <td>
                            <button onclick="alert('Chức năng xem chi tiết đang phát triển cho ID: ${c.id_khach_hang}')" 
                                    style="border:none; background:transparent; color:#6366f1; cursor:pointer; font-weight:600;">
                                Xem thêm
                            </button>
                        </td>
                    </tr>
                `;
                tbody.insertAdjacentHTML('beforeend', html);
            });
        }

        async function loadCustomers(search = '') {
            try {
                const res = await fetch(`${API_URL}?search=${encodeURIComponent(search)}`);
                const data = await res.json();
                
                if (data.status === 'success') {
                    renderTable(data.customers);
                } else {
                    document.getElementById('customerTableBody').innerHTML = 
                        `<tr><td colspan="5" class="empty-state" style="color:red">Lỗi: ${data.message}</td></tr>`;
                }
            } catch (error) {
                console.error(error);
                document.getElementById('customerTableBody').innerHTML = 
                    `<tr><td colspan="5" class="empty-state" style="color:red">Lỗi kết nối server.</td></tr>`;
            }
        }

        // Sự kiện tìm kiếm
        let timeout = null;
        document.getElementById('searchInput').addEventListener('input', function(e) {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                loadCustomers(e.target.value);
            }, 300); // Debounce 300ms
        });

        // Load lần đầu
        document.addEventListener('DOMContentLoaded', () => loadCustomers());
    </script>
</body>
</html>
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
