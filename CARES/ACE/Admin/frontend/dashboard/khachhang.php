<?php
// khachhang.php
// Đảm bảo biến $activePage được set để sidebar highlight đúng
$activePage = 'khachhang';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
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