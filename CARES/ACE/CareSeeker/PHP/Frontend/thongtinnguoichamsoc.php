<?php
session_start();

if (!isset($_SESSION['so_dien_thoai'])) {
    header("Location: ../../../Admin/login.php");
    exit();
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) {
    die("<h2 style='text-align:center;color:red;'>ID người chăm sóc không hợp lệ!</h2>");
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Thông tin người chăm sóc</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS ĐÃ ĐƯỢC KHÔI PHỤC VÀ CHUẨN HÓA */
        :root {
            --primary-color: #FF6B81;
            --accent-color: #4A90E2;
            --text-color: #333;
            --secondary-text-color: #555;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f8f8fa;
            margin: 0;
            color: var(--text-color);
        }

        .container {
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            overflow: hidden;
            min-height: 400px;
        }

        .header {
            display: flex;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 40px;
        }

        .header img {
            width: 320px;
            height: 320px;
            border-radius: 20px;
            object-fit: cover;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .info {
            flex: 1;
        }

        h1 {
            margin: 0 0 15px;
            color: var(--primary-color);
            font-size: 32px;
            font-weight: 700;
        }

        .info p {
            font-size: 17px;
            margin: 8px 0;
            color: var(--secondary-text-color);
        }

        .info strong {
            color: var(--text-color);
            font-weight: 600;
        }

        .rating {
            color: #F7C513;
            font-weight: bold;
            font-size: 18px;
        }

        .price {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 22px;
            display: block;
            margin-top: 10px;
        }

        .back-btn {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            text-decoration: none;
            margin-top: 25px;
            margin-right: 15px;
            font-weight: 600;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #E55B70;
        }

        .reviews {
            margin-top: 50px;
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            border: 1px solid #f0f0f0;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .reviews h3 {
            color: var(--text-color);
            margin-bottom: 20px;
            font-size: 24px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }

        .reviews h3 i {
            color: var(--primary-color);
            margin-right: 10px;
        }

        .review-box {
            background: #fcfcfc;
            border-left: 5px solid var(--primary-color);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        .review-box p {
            margin: 5px 0;
            color: var(--text-color);
        }

        .review-box .name {
            font-size: 16px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .review-box .star {
            color: #F7C513;
            font-weight: 600;
        }

        .review-box .comment {
            font-style: italic;
            color: var(--secondary-text-color);
            margin-top: 10px;
            line-height: 1.6;
        }

        .review-box .date {
            font-size: 13px;
            color: #999;
            display: block;
            margin-top: 10px;
        }

        .suggest-section {
            margin-top: 50px;
        }

        .suggest-title {
            font-size: 26px;
            font-weight: 700;
            color: var(--text-color);
            border-left: 5px solid var(--primary-color);
            padding-left: 15px;
            margin-bottom: 25px;
        }

        .suggest-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }

        .card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s;
            border-top: 4px solid var(--primary-color);
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }

        .card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .card-content {
            padding: 18px;
        }

        .card-content h3 {
            margin: 0 0 5px;
            color: var(--primary-color);
            font-size: 18px;
        }

        .card-content p {
            margin: 5px 0;
            font-size: 15px;
            color: var(--secondary-text-color);
        }

        .card-content strong {
            color: #F7C513;
        }

        .card-content .money {
            color: var(--primary-color);
            font-weight: 700;
            margin-top: 5px;
            display: block;
        }

        .detail-btn {
            display: inline-block;
            margin-top: 10px;
            background: var(--primary-color);
            color: white;
            padding: 9px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.3s;
        }

        .detail-btn:hover {
            background: #E55B70;
        }

        .loading-placeholder {
            text-align: center;
            padding: 80px 20px;
            font-size: 20px;
            color: #999;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .header img {
                width: 100%;
                height: auto;
                max-width: 300px;
            }

            .container {
                padding: 20px;
            }

            .suggest-title {
                text-align: center;
                border-left: none;
                padding-left: 0;
            }
        }
    </style>
</head>

<body>

    <div class="container" id="mainContentContainer">
        <div class="loading-placeholder">
            <i class="fas fa-spinner fa-spin fa-2x" style="color:var(--primary-color);"></i>
            <p>Đang tải thông tin chi tiết...</p>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const caregiverId = <?php echo $id; ?>;
            const container = document.getElementById('mainContentContainer');
            
            // BẮT ĐẦU SỬA ĐỔI: Sử dụng API Gateway và Sửa lỗi Action
            const GATEWAY_URL = '../Backend/api_gateway.php';
            // Thêm &action=get_details để khắc phục lỗi 400 "Hành động không hợp lệ"
            const API_DETAIL_URL = `${GATEWAY_URL}?route=caregiver/details&id=${caregiverId}&action=get_details`; 
            const apiUrl = API_DETAIL_URL;
            // KẾT THÚC SỬA ĐỔI

            function formatCurrency(num) {
                return (parseInt(num) || 0).toLocaleString('vi-VN') + ' đ/giờ';
            }

            function renderStars(ratingStr) {
                const rating = parseFloat(ratingStr) || 0;
                let stars = '';
                for (let i = 1; i <= 5; i++) {
                    if (i <= rating) {
                        stars += '<i class="fas fa-star"></i>';
                    } else if (i - 0.5 <= rating) {
                        stars += '<i class="fas fa-star-half-alt"></i>';
                    } else {
                        stars += '<i class="far fa-star"></i>';
                    }
                }
                return `${stars} ${rating.toFixed(1)}/5`;
            }

            function renderPage(data) {
                const {
                    caregiver,
                    reviews,
                    related
                } = data;

                let reviewsHtml = '';
                if (reviews.length > 0) {
                    let count = 0;
                    reviewsHtml = reviews.map(dg => {
                        count++;
                        const starRating = dg.so_sao || dg.diem_danh_gia || 0;
                        const hidden = count > 5 ? "style='display:none'" : "";
                        return `
                        <div class='review-box' ${hidden}>
                            <p class='name'><i class='fas fa-user'></i> ${dg.ten_khach_hang || 'Khách hàng'}</p>
                            <p><span class'star'>${renderStars(starRating)}</span></p>
                            <p class='comment'>${dg.nhan_xet || 'Không có nhận xét.'}</p>
                            <span class='date'>📅 ${new Date(dg.ngay_danh_gia).toLocaleString('vi-VN')}</span>
                        </div>
                        `;
                    }).join('');

                    if (count > 5) {
                        reviewsHtml += `<div style='text-align:center; margin-top:15px;'>
                            <button id='loadMoreBtn' style='padding:10px 20px; background:var(--primary-color); color:white; border:none; border-radius:8px; cursor:pointer; font-weight:600;'>Xem thêm</button>
                            <button id='hideBtn' style='padding:10px 20px; background:#ccc; color:#333; border:none; border-radius:8px; cursor:pointer; font-weight:600; display:none; margin-left:10px;'>Ẩn bớt</button>
                        </div>`;
                    }
                } else {
                    reviewsHtml = "<p style='color:#999; text-align:center;'>Chưa có nhận xét nào cho người chăm sóc này.</p>";
                }

                let relatedHtml = '';
                if (related.length > 0) {
                    relatedHtml = related.map(r => `
                        <div class="card">
                            <img src="${r.hinh_anh}" alt="${r.ho_ten}">
                            <div class="card-content">
                                <h3>${r.ho_ten}</h3>
                                <p>⭐ Đánh giá: <strong>${r.danh_gia_tb}/5</strong></p>
                                <p><i class="fas fa-briefcase" style="color:#555;"></i> Kinh nghiệm: ${r.kinh_nghiem}</p>
                                <p class="money">💰 ${formatCurrency(r.tong_tien_kiem_duoc)}</p>
<<<<<<< HEAD
                                <a href="thongtinnguoichamsoc.php?id=${r.id_cham_soc}" class="detail-btn">Xem chi tiết <i class="fas fa-arrow-right"></i></a>
=======
                                <a href="Thongtinnguoichamsoc.php?id=${r.id_cham_soc}" class="detail-btn">Xem chi tiết <i class="fas fa-arrow-right"></i></a>
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
                            </div>
                        </div>
                    `).join('');
                } else {
                    relatedHtml = "<p style='text-align:center; padding: 20px; color:#999;'>Không có người chăm sóc nào khác để đề xuất.</p>";
                }

                container.innerHTML = `
                    <div class="header">
                        <img src="${caregiver.hinh_anh}" alt="Ảnh người chăm sóc">
                        <div class="info">
                            <h1><i class="fas fa-user-nurse" style="color:var(--primary-color);"></i> ${caregiver.ho_ten}</h1>
                            <p><strong>Tuổi:</strong> ${caregiver.tuoi}</p>
                            <p><strong>Giới tính:</strong> ${caregiver.gioi_tinh}</p>
                            <p><strong>Chiều cao:</strong> ${caregiver.chieu_cao} cm</p>
                            <p><strong>Cân nặng:</strong> ${caregiver.can_nang} kg</p>
                            <p><strong>Trung bình đánh giá:</strong> <span class="rating">${renderStars(caregiver.danh_gia_tb)}</span></p>
                            <p><strong>Kinh nghiệm:</strong> ${caregiver.kinh_nghiem}</p>
                            <p><strong>Số lượng đơn đã nhận:</strong> ${caregiver.don_da_nhan}</p>
                            <p><strong>Giá tiền/giờ:</strong> <span class="price">${formatCurrency(caregiver.tong_tien_kiem_duoc)}</span></p>
<<<<<<< HEAD
                            <a href="datdonhang.php?id=${caregiver.id_cham_soc}" class="back-btn">📝 Đặt dịch vụ ngay</a>
                            <a href="dichvu.php" class="back-btn">← Quay lại danh sách</a>
=======
                            <a href="Datdonhang.php?id=${caregiver.id_cham_soc}" class="back-btn">📝 Đặt dịch vụ ngay</a>
                            <a href="Dichvu.php" class="back-btn">← Quay lại danh sách</a>
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
                        </div>
                    </div>

                    <div class="reviews">
                        <h3><i class="fas fa-comments"></i> Nhận xét từ khách hàng</h3>
                        <div id="review-list">
                            ${reviewsHtml}
                        </div>
                    </div>

                    <div class="suggest-section">
                        <div class="suggest-title">✨ Đề xuất thêm người chăm sóc khác</div>
                        <div class="suggest-grid">
                            ${relatedHtml}
                        </div>
                    </div>
                `;

                attachReviewToggleListeners();
            }

            async function loadCaregiverData() {
                try {
                    // Sử dụng apiUrl mới (đã bao gồm Gateway và action=get_details)
                    const response = await fetch(apiUrl); 

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || `Lỗi HTTP: ${response.status}`);
                    }

                    const result = await response.json();

                    if (result.success) {
                        renderPage(result);
                    } else {
                        throw new Error(result.message || 'Không thể tải dữ liệu.');
                    }

                } catch (error) {
                    console.error('Lỗi tải dữ liệu:', error);
                    container.innerHTML = `<h2 style='text-align:center;color:red;'>Lỗi: ${error.message}</h2><p style='text-align:center;'>Vui lòng kiểm tra lại trạng thái đăng nhập hoặc ID người chăm sóc.</p>`;
                }
            }

            function attachReviewToggleListeners() {
                const loadBtn = document.getElementById("loadMoreBtn");
                const hideBtn = document.getElementById("hideBtn");

                if (loadBtn) {
                    loadBtn.addEventListener("click", function() {
                        document.querySelectorAll("#review-list .review-box").forEach(box => {
                            box.style.display = "block";
                        });
                        loadBtn.style.display = "none";
                        hideBtn.style.display = "inline-block";
                    });
                }

                if (hideBtn) {
                    hideBtn.addEventListener("click", function() {
                        const boxes = document.querySelectorAll("#review-list .review-box");
                        boxes.forEach((box, index) => {
                            box.style.display = index < 5 ? "block" : "none";
                        });
                        hideBtn.style.display = "none";
                        loadBtn.style.display = "inline-block";
                    });
                }
            }

            loadCaregiverData();
        });
    </script>

</body>

</html>