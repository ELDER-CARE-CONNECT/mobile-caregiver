<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['so_dien_thoai']) && !isset($_SESSION['id_khach_hang'])) {
    header("Location: ../../../Admin/frontend/auth/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Dịch vụ - Danh sách người chăm sóc</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #f8f8fa; color: #333; overflow-x: hidden; line-height: 1.6; padding-top: 30px; }
        footer { background: #1f2937; color: #ddd; text-align: center; padding: 30px; font-size: 15px; }
        footer a { color: #FF6B81; text-decoration: none; }
        h1.page-title { text-align: center; font-size: 38px; color: #FF6B81; margin: 50px 0 20px; font-weight: 800; }
        .container { max-width: 1200px; margin: 0 auto 50px; padding: 0 20px; }
        .filter-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; flex-wrap: wrap; gap: 20px; background: #ffffff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05); }
        .filter-bar input, .filter-bar select { padding: 12px 18px; border-radius: 8px; border: 1px solid #ddd; font-size: 16px; transition: border-color 0.3s, box-shadow 0.3s; color: #333; flex: 1; min-width: 250px; }
        .filter-bar input:focus, .filter-bar select:focus { border-color: #4A90E2; box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1); outline: none; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 35px; }
        .card { background: #fff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); overflow: hidden; transition: 0.4s; text-align: left; }
        .card:hover { transform: translateY(-8px); box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15); }
        .card img { width: 100%; height: 280px; object-fit: cover; }
        .card-content { padding: 20px; }
        .card-content h3 { margin: 0 0 4px; font-size: 22px; color: #FF6B81; }
        .info { font-size: 16px; color: #666; margin: 8px 0; }
        .info i { color: #333; margin-right: 8px; }
        .rating { color: #ffd640ff; font-weight: 600; }
        .price { font-size: 20px; color: #4A90E2; font-weight: 700; margin-top: 15px; display: block; }
        .btn-group { display: flex; gap: 10px; margin-top: 15px; }
        .btn-detail { display: inline-block; background: #FF6B81; color: white; padding: 12px 14px; border-radius: 10px; text-decoration: none; font-size: 16px; font-weight: 600; transition: 0.3s; flex: 1; text-align: center; }
        .btn-detail:last-child { background: #FF6B81 }
        .btn-detail:last-child:hover { background: #FF6B81 }
        .btn-detail:first-child:hover { background: #E55B70; }
        @media (max-width: 768px) { .navbar { padding: 15px 20px; } .nav-links { display: none; } .page-title { font-size: 30px; } .filter-bar { flex-direction: column; align-items: stretch; } .filter-bar input, .filter-bar select { min-width: 100%; } .btn-group { flex-direction: column; } }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <h1 class="page-title">Danh sách Người Chăm Sóc Tận Tâm</h1>

    <div class="container">
        <div class="filter-bar">
            <input type="text" id="searchInput" placeholder="Tìm theo tên...">
            <select id="sortSelect">
                <option value="">-- Sắp xếp --</option>
                <option value="tong_tien_kiem_duoc">Mức giá (thấp → cao)</option>
                <option value="danh_gia_tb">Đánh giá (cao → thấp)</option>
                <option value="don_da_nhan">Kinh nghiệm (nhiều → ít)</option>
            </select>
        </div>

        <div class="grid" id="caregiverGrid">
            <p id="loading-message" style="text-align:center; padding: 50px; font-size: 18px; color:#999; grid-column: 1 / -1;">Đang tải danh sách người chăm sóc...</p>
        </div>
    </div>

    <footer>
        © 2025 Elder Care Connect | <i class="fas fa-shield-alt" style="color:#4A90E2;"></i> Cam kết chất lượng và sự tận tâm
    </footer>

    <script>
        let allCaregiversData = [];
        
        // CẤU HÌNH API GATEWAY
        const GATEWAY_URL = '../Backend/api_gateway.php';
        const API_CAREGIVER_LIST_ALL = `${GATEWAY_URL}?route=caregiver/list&action=list_all`;

        function formatCurrency(number) {
            if (typeof number !== 'number') {
                number = parseInt(number) || 0;
            }
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".") + ' đ/giờ';
        }

        function renderStars(rating) {
            const floatRating = parseFloat(rating) || 0;
            const fullStars = Math.floor(floatRating);
            const halfStar = (floatRating - fullStars) >= 0.5;
            const emptyStars = 5 - fullStars - (halfStar ? 1 : 0);
            let starsHtml = '';

            for (let i = 0; i < fullStars; i++) starsHtml += '<i class="fas fa-star"></i>';
            if (halfStar) starsHtml += '<i class="fas fa-star-half-alt"></i>';
            for (let i = 0; i < emptyStars; i++) starsHtml += '<i class="far fa-star"></i>';

            return starsHtml;
        }
        

        function createCardHtml(caregiver) {
            // --- XỬ LÝ ẢNH CHUẨN XÁC ---
            let hinh_anh_url = 'img/default_avatar.png'; 

            if (caregiver.hinh_anh && caregiver.hinh_anh.trim() !== '') {
                // 1. Nếu là link online (http/https) -> Giữ nguyên
                if (caregiver.hinh_anh.startsWith('http')) {
                    hinh_anh_url = caregiver.hinh_anh;
                } 
                // 2. Nếu là link trong máy
                else {
                    // Bước 1: Sửa lỗi chính tả 'fontend' thành 'frontend' nếu có
                    let cleanPath = caregiver.hinh_anh.replace('fontend/', 'frontend/');
                    
                    // Bước 2: Tạo đường dẫn tương đối chính xác
                    // Từ dichvu.php (CareSeeker/PHP/Frontend) -> lùi 3 cấp ra ACE -> vào Admin -> nối tiếp đường dẫn trong DB
                    hinh_anh_url = '../../../Admin/' + cleanPath;
                }
            }

            const rating = parseFloat(caregiver.danh_gia_tb).toFixed(1);

            return `
                <div class="card" data-name="${caregiver.ho_ten.toLowerCase()}"
                         data-money="${caregiver.tong_tien_kiem_duoc}"
                         data-rating="${rating}"
                         data-orders="${caregiver.don_da_nhan}">
                    
                    <img src="${hinh_anh_url}" alt="${caregiver.ho_ten}" 
                         onerror="this.onerror=null; this.src='img/default_avatar.png';">
                         
                    <div class="card-content">
                        <h3>${caregiver.ho_ten}</h3>
                        <div class="info">
                            <span class="rating">
                                ${renderStars(rating)}
                                ${rating}/5
                            </span>
                        </div>
                        <div class="info"><i class="fas fa-briefcase"></i> Kinh nghiệm: ${caregiver.kinh_nghiem || 'Chưa cập nhật'}</div>
                        <div class="info"><i class="fas fa-box-open"></i> Đơn đã nhận: ${caregiver.don_da_nhan}</div>
                        <div class="price">${formatCurrency(caregiver.tong_tien_kiem_duoc)}</div>
                        <div class="btn-group"> 
<<<<<<< HEAD
                            <a href="thongtinnguoichamsoc.php?id=${caregiver.id_cham_soc}" class="btn-detail"><i class="fas fa-info-circle"></i> Xem chi tiết</a>
                            <a href="datdonhang.php?id=${caregiver.id_cham_soc}" class="btn-detail"><i class="fas fa-calendar-check"></i> Đặt dịch vụ</a>
=======
                            <a href="Thongtinnguoichamsoc.php?id=${caregiver.id_cham_soc}" class="btn-detail"><i class="fas fa-info-circle"></i> Xem chi tiết</a>
                            <a href="Datdonhang.php?id=${caregiver.id_cham_soc}" class="btn-detail"><i class="fas fa-calendar-check"></i> Đặt dịch vụ</a>
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
                        </div>
                    </div>
                </div>
            `;
        }

        function displayCaregivers(data) {
            const caregiverGrid = document.getElementById("caregiverGrid");
            caregiverGrid.innerHTML = '';

            if (data.length === 0) {
                caregiverGrid.innerHTML = '<p style="text-align:center; padding: 50px; font-size: 18px; color:#999; grid-column: 1 / -1;">Không tìm thấy người chăm sóc nào phù hợp.</p>';
                return;
            }

            data.forEach(caregiver => {
                caregiverGrid.insertAdjacentHTML('beforeend', createCardHtml(caregiver));
            });
        }

        function filterAndSortCaregivers() {
            const keyword = searchInput.value.toLowerCase();
            const sortBy = sortSelect.value;

            let filteredData = allCaregiversData.filter(caregiver =>
                caregiver.ho_ten.toLowerCase().includes(keyword)
            );

            if (sortBy) {
                filteredData.sort((a, b) => {
                    if (sortBy === "tong_tien_kiem_duoc") {
                        return (parseFloat(a.tong_tien_kiem_duoc) || 0) - (parseFloat(b.tong_tien_kiem_duoc) || 0);
                    }
                    if (sortBy === "danh_gia_tb") {
                        return (parseFloat(b.danh_gia_tb) || 0) - (parseFloat(a.danh_gia_tb) || 0);
                    }
                    if (sortBy === "don_da_nhan") {
                        return (parseInt(b.don_da_nhan) || 0) - (parseInt(a.don_da_nhan) || 0);
                    }
                    return 0;
                });
            }

            displayCaregivers(filteredData);
        }

        async function fetchCaregivers() {
            const caregiverGrid = document.getElementById("caregiverGrid");
            const apiUrl = API_CAREGIVER_LIST_ALL;

            try {
                // Gửi kèm credentials để nhận diện session đăng nhập
                const response = await fetch(apiUrl, {
                    credentials: 'include' 
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    caregiverGrid.innerHTML = `<p style="text-align:center; color:red; padding: 50px; grid-column: 1 / -1;">Lỗi kết nối API (${response.status}). Chi tiết: ${errorText.substring(0, 100)}...</p>`;
                    return;
                }

                const result = await response.json();

                if (result.success && Array.isArray(result.data)) {
                    allCaregiversData = result.data;
                    filterAndSortCaregivers();
                } else {
                    caregiverGrid.innerHTML = `<p style="text-align:center; color:red; padding: 50px; grid-column: 1 / -1;">Lỗi tải dữ liệu: ${result.message || 'Không có dữ liệu.'}</p>`;
                }
            } catch (error) {
                console.error('Lỗi gọi API:', error);
                caregiverGrid.innerHTML = `<p style="text-align:center; color:red; padding: 50px; grid-column: 1 / -1;">Lỗi kết nối Server. (${error.message})</p>`;
            }
        }

        const searchInput = document.getElementById("searchInput");
        const sortSelect = document.getElementById("sortSelect");

        searchInput.addEventListener("input", filterAndSortCaregivers);
        sortSelect.addEventListener("change", filterAndSortCaregivers);

        document.addEventListener('DOMContentLoaded', () => {
            fetchCaregivers();
        });

        (function() {
            var currentPage = window.location.pathname.split('/').pop().toLowerCase();
            if (currentPage === "") {
                currentPage = "index.php";
            }
            var navLinks = document.querySelectorAll('.nav-links a');
            navLinks.forEach(function(link) {
                if (link.href && link.hash === "") {
                    var linkPage = new URL(link.href).pathname.split('/').pop().toLowerCase();
                    if (linkPage === "") linkPage = "index.php";
                    if (linkPage === currentPage) link.classList.add('active');
                }
            });
        })();
    </script>
</body>
</html>