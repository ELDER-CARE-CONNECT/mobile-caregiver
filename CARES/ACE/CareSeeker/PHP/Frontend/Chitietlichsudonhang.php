<?php
session_start();

// Kiểm tra đăng nhập (Session toàn cục đã được set ở file Login)
if (!isset($_SESSION['id_khach_hang'])) { 
    header("Location: ../../../Admin/frontend/auth/login.php");
    exit();
}

$id_don_hang = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_don_hang === 0) {
    die("Lỗi: Không tìm thấy ID đơn hàng.");
}

// Biến PHP sẽ được sử dụng trong JavaScript
$id_don_hang_js = $id_don_hang; 
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi Tiết Lịch Sử Đơn Hàng #<?php echo htmlspecialchars($id_don_hang); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: #f8f8fa; color: #333; line-height: 1.6; display: flex; justify-content: center; min-height: 100vh; padding: 30px 15px; }
        .container { background: #fff; border-radius: 16px; padding: 30px; box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1); width: 100%; max-width: 700px; min-height: 400px; }
        h1 { color: #FF6B81; font-size: 28px; margin-bottom: 5px; font-weight: 800; text-align: center; }
        .order-id { text-align: center; font-size: 14px; color: #888; margin-bottom: 25px; font-weight: 500; }
        .section-box { background: #fff; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #f0f0f0; }
        .section-title { font-size: 18px; font-weight: 700; color: #333; border-bottom: 2px solid #FFD8E0; padding-bottom: 10px; margin-bottom: 15px; }
        .detail-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px dashed #eee; font-size: 15px; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { font-weight: 500; color: #666; display: flex; align-items: center; }
        .detail-value { font-weight: 600; color: #333; word-break: break-word; text-align: right; max-width: 60%; }
        .icon { margin-right: 8px; color: #FF6B81; }
        .caregiver-info { display: flex; align-items: center; gap: 15px; background: #fff7f9; padding: 15px; border-radius: 10px; }
        .caregiver-info img { width: 50px; height: 50px; border-radius: 50%; object-fit: cover; border: 2px solid #FFD8E0; }
        .caregiver-name { font-weight: 700; color: #FF6B81; }
        .caregiver-id { font-size: 13px; color: #888; }
        .service-item { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px dashed #f0f0f0; font-size: 15px; }
        .service-item:last-child { border-bottom: none; }
        .service-item strong { color: #555; font-weight: 600; max-width: 90%; }
        .task-status { padding: 4px 8px; border-radius: 6px; font-size: 13px; font-weight: 600; flex-shrink: 0; }
        .status-chua_hoan_thanh { background-color: #fff3e0; color: #ff9800; }
        .status-hoan_thanh { background-color: #e8f5e9; color: #4caf50; }
        .total-section { display: flex; justify-content: space-between; align-items: center; border-top: 2px solid #FF6B81; padding-top: 15px; margin-top: 10px; font-size: 20px; font-weight: 800; }
        .status-tag { padding: 5px 10px; border-radius: 20px; font-weight: 700; font-size: 13px; text-transform: uppercase; }
        .status-cho_xac_nhan { background: #fff3e0; color: #ff9800; }
        .status-dang_hoan_thanh, .status-da_nhan { background: #e3f2fd; color: #2196f3; }
        .status-da_hoan_thanh { background: #e8f5e9; color: #4caf50; }
        .status-da_huy { background: #ffebee; color: #f44336; }
        .status-that_bai { background: #ffebee; color: #f44336; }
        .buttons { display: flex; flex-wrap: wrap; gap: 10px; justify-content: center; margin-top: 30px; }
        .button { padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; transition: background-color 0.3s, transform 0.1s; text-decoration: none; color: white; font-size: 15px; font-weight: 600; text-align: center; display: inline-flex; align-items: center; gap: 8px; }
        .button:active { transform: scale(0.98); }
        .btn-back { background-color: #9e9e9e; color: white; }
        .btn-back:hover { background-color: #757575; }
        .btn-rate { background-color: #FFC300; color: #333; }
        .btn-rate:hover { background-color: #e6b100; }
        .btn-reorder { background-color: #FF6B81; }
        .btn-reorder:hover { background-color: #E55B70; }
        .btn-chat { background-color: #2196f3; color: white; }
        .btn-chat:hover { background-color: #0c83e1; }
        .hidden { display: none; }
        .message-box { padding: 15px; margin-bottom: 20px; border-radius: 8px; text-align: center; font-weight: bold; }
        .msg-loading { background: #e3f2fd; color: #2196F3; }
        .msg-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <div class="container" id="mainContainer">

        <div id="loadingView" class="message-box msg-loading">
            <i class="fas fa-spinner fa-spin"></i> Đang tải chi tiết đơn hàng...
        </div>

        <div id="errorView" class="message-box msg-error hidden"></div>

        <div id="contentView" class="hidden">
            <h1><i class="fas fa-file-invoice"></i> Chi Tiết Lịch Sử Đơn Hàng</h1>
            <p class="order-id">Mã đơn hàng: <strong id="orderId">#...</strong></p>

            <div class="section-box">
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-info-circle"></i> Trạng Thái Đơn Hàng:</span>
                    <span class="status-tag" id="orderStatus">...</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-calendar-alt"></i> Ngày Đặt Hàng:</span>
                    <span class="detail-value" id="orderNgayDat">...</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-credit-card"></i> Phương thức thanh toán:</span>
                    <span class="detail-value" id="orderPayment">...</span>
                </div>
            </div>

            <div id="caregiverBox" class="section-box hidden">
                <div class="section-title"><i class="icon fas fa-user-nurse"></i> Người Chăm Sóc</div>
                <div class="caregiver-info">
                    <img id="caregiverAvatar" src="uploads/avatars/default.png" alt="Avatar">
                    <div>
                        <div class="caregiver-name" id="caregiverName">...</div>
                        <div class="caregiver-id" id="caregiverId"></div>
                    </div>
                </div>
            </div>

            <div class="section-box">
                <div class="section-title"><i class="icon fas fa-clipboard-list"></i> Dịch Vụ & Thời Gian</div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-clock"></i> Bắt đầu:</span>
                    <span class="detail-value" id="orderStartTime">...</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-hourglass-end"></i> Kết thúc:</span>
                    <span class="detail-value" id="orderEndTime">...</span>
                </div>
                <div id="serviceListContainer" style="margin-top: 15px;">
                </div>
            </div>

            <div class="section-box">
                <div class="section-title"><i class="icon fas fa-map-marker-alt"></i> Thông Tin Liên Hệ/Địa Chỉ</div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-user"></i> Người liên hệ:</span>
                    <span class="detail-value" id="orderTenKH">...</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-phone"></i> SĐT liên hệ:</span>
                    <span class="detail-value" id="orderSdtKH">...</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-map-marked-alt"></i> Địa chỉ phục vụ:</span>
                    <span class="detail-value" style="text-align: right; max-width: 60%;" id="orderDiaChi">...</span>
                </div>
            </div>

            <div class="section-box total-section">
                <span>Tổng Cộng (Đã bao gồm VAT)</span>
                <span style="color: #FF6B81;" id="orderTongTien">... VND</span>
            </div>

            <div class="buttons" id="actionButtonsContainer">
            </div>

        </div>
    </div>

    <script>
        const ID_DON_HANG = <?php echo $id_don_hang_js; ?>; 
        
        // CẤU HÌNH API GATEWAY
        const GATEWAY_URL = '../Backend/api_gateway.php';
        const API_DETAIL_URL = `${GATEWAY_URL}?route=order/details`; 

        function showError(message) {
            document.getElementById('loadingView').classList.add('hidden');
            const errorView = document.getElementById('errorView');
            errorView.textContent = `Lỗi: ${message}`;
            errorView.classList.remove('hidden');
        }

        function formatCurrency(value) {
            return (parseFloat(value) || 0).toLocaleString('vi-VN') + ' VND';
        }

        function formatDateTime(dateTimeStr) {
            if (!dateTimeStr) return 'N/A';
            return new Date(dateTimeStr).toLocaleString('vi-VN', {
                hour: '2-digit',
                minute: '2-digit',
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
        }

        function statusToClass(status) {
            if (!status) return 'cho_xac_nhan';
            status = status.toLowerCase().trim().replace(/ /g, '_');
            if (status === 'đã_hoàn_thành') return 'da_hoan_thanh';
            if (status === 'chờ_xác_nhận') return 'cho_xac_nhan';
            if (status === 'đang_hoàn_thành') return 'dang_hoan_thanh';
            if (status === 'đã_hủy') return 'da_huy';
            return status;
        }

        function renderData(data) {
            const { order, services, is_rated } = data;

            document.getElementById('contentView').classList.remove('hidden');
            document.getElementById('loadingView').classList.add('hidden');

            // --- HEADER ---
            document.getElementById('orderId').textContent = `#${order.id_don_hang}`;
            
            const statusClass = statusToClass(order.trang_thai);
            const statusEl = document.getElementById('orderStatus');
            statusEl.textContent = order.trang_thai;
            statusEl.className = `status-tag status-${statusClass}`;

            document.getElementById('orderNgayDat').textContent = formatDateTime(order.ngay_dat);

            let payment_method = order.hinh_thuc_thanh_toan || 'Chưa xác định';
            if (payment_method.toLowerCase() === 'vnpay' && (order.thanh_toan_status || '').toLowerCase() !== 'đã thanh toán') {
                payment_method = 'VNPAY';
            }
            document.getElementById('orderPayment').textContent = payment_method;

            // --- NGƯỜI CHĂM SÓC ---
            if (order.caregiver_id) {
                document.getElementById('caregiverBox').classList.remove('hidden');
                document.getElementById('caregiverName').textContent = order.ten_cham_soc;
                document.getElementById('caregiverId').textContent = `ID: #${order.caregiver_id}`;
                // Giả định ảnh nằm ở thư mục gốc, cần lùi 3 cấp thư mục
                document.getElementById('caregiverAvatar').src = `../../../${order.hinh_anh_cham_soc}`;
            }

            // --- THỜI GIAN ---
            document.getElementById('orderStartTime').textContent = formatDateTime(order.thoi_gian_bat_dau);
            document.getElementById('orderEndTime').textContent = formatDateTime(order.thoi_gian_ket_thuc);

            // --- DANH SÁCH DỊCH VỤ (Đã sửa logic trạng thái nhiệm vụ) ---
            const serviceContainer = document.getElementById('serviceListContainer');
            if (services.length > 0) {
                serviceContainer.innerHTML = '<span class="detail-label" style="font-weight: 700; color: #333;"><i class="icon fas fa-tasks"></i> Các Nhiệm Vụ Cụ Thể:</span>';

                // 1. Lấy trạng thái chung từ DB
                let dbTaskStatus = (order.trang_thai_nhiem_vu || '').trim().toLowerCase();

                // 2. Nếu DB chưa set trạng thái nhiệm vụ, dùng trạng thái đơn hàng để hiển thị tạm
                if (!dbTaskStatus) {
                    if (statusClass === 'da_hoan_thanh') {
                        dbTaskStatus = 'đã hoàn thành';
                    } else {
                        dbTaskStatus = 'chờ xác nhận';
                    }
                }

                // 3. Xác định text hiển thị và màu sắc
                let displayStatusStr = '';
                let cssClass = '';

                if (dbTaskStatus === 'đã hoàn thành') {
                    displayStatusStr = 'Hoàn thành ✅';
                    cssClass = 'status-hoan_thanh'; // Màu xanh
                } else {
                    displayStatusStr = 'Chờ thực hiện ⏳';
                    cssClass = 'status-chua_hoan_thanh'; // Màu cam
                }

                // 4. Loop qua services và gán cùng 1 status
                services.forEach((service) => {
                    serviceContainer.innerHTML += `
                        <div class="service-item">
                            <strong>${service.ten_nhiem_vu}</strong>
                            <span class="task-status ${cssClass}">
                                ${displayStatusStr}
                            </span>
                        </div>
                    `;
                });
            }

            // --- THÔNG TIN KHÁCH HÀNG ---
            const dia_chi_kh = order.dia_chi_giao_hang || order.dia_chi_kh || 'Không rõ';
            document.getElementById('orderTenKH').textContent = order.ten_khach_hang || 'N/A';
            document.getElementById('orderSdtKH').textContent = order.so_dien_thoai || 'N/A';
            document.getElementById('orderDiaChi').textContent = dia_chi_kh;

            document.getElementById('orderTongTien').textContent = formatCurrency(order.tong_tien);

            renderActionButtons(order, is_rated);
        }

        function renderActionButtons(order, is_rated) {
            const container = document.getElementById('actionButtonsContainer');
            container.innerHTML = '';

            // Nút Quay lại
            container.innerHTML += `<a href="tongdonhang.php" class="button btn-back"><i class="fas fa-list-alt"></i> Quay lại Đơn hàng</a>`;

            const status = (order.trang_thai || '').toLowerCase().trim();

            // Nút Đánh giá (Chỉ hiện khi đã hoàn thành)
            if (status === 'đã hoàn thành' && order.caregiver_id) {
                if (is_rated) {
                    container.innerHTML += `<button class="button btn-rate" style="opacity:0.6; cursor:not-allowed;" disabled><i class="fas fa-check"></i> Đã Đánh Giá</button>`;
                } else {
                    container.innerHTML += `<a href="danhgia.php?id_cs=${order.caregiver_id}&id_dh=${order.id_don_hang}" class="button btn-rate"><i class="fas fa-star"></i> Đánh Giá</a>`;
                }
            }
            
            // --- NÚT CHAT ĐÃ ĐƯỢC CẬP NHẬT ---
            if (order.caregiver_id) {
                container.innerHTML += `<a href="ChatKhachHang.php?id_don_hang=${order.id_don_hang}" class="button btn-chat"><i class="fas fa-comment-dots"></i> Chat</a>`;
            }
            
            // Nút Đặt Lại
            if (order.caregiver_id) {
                container.innerHTML += `<a href="datdonhang.php?id=${order.caregiver_id}" class="button btn-reorder"><i class="fas fa-redo"></i> Đặt Lại</a>`;
            }
        }

        async function loadData() {
            try {
                // Gọi API Gateway, nhớ gửi kèm credentials để nhận Session
                const response = await fetch(`${API_DETAIL_URL}&action=get_details&id=${ID_DON_HANG}`, {
                    credentials: 'include' 
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || `Lỗi HTTP: ${response.status}`);
                }

                const data = await response.json();
                if (data.success) {
                    renderData(data);
                } else {
                    showError(data.message);
                }
            } catch (error) {
                showError(error.message);
            }
        }

        document.addEventListener('DOMContentLoaded', loadData);
    </script>

</body>
</html>