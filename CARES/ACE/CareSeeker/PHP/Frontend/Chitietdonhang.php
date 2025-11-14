<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'khach_hang' || !isset($_SESSION['so_dien_thoai'])) {
    header("Location: ../../../Admin/login.php");
    exit();
}

$id_don_hang = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id_don_hang === 0) {
    die("Lỗi: Không tìm thấy ID đơn hàng.");
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi Tiết Đơn Hàng #<?php echo $id_don_hang; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f8f8fa;
            color: #333;
            line-height: 1.6;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            padding: 30px 15px;
        }

        .container {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 700px;
        }

        h1 {
            color: #FF6B81;
            font-size: 28px;
            margin-bottom: 5px;
            font-weight: 800;
            text-align: center;
        }

        .order-id {
            text-align: center;
            font-size: 14px;
            color: #888;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .section-box {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #f0f0f0;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            border-bottom: 2px solid #FFD8E0;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px dashed #eee;
            font-size: 15px;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 500;
            color: #666;
            display: flex;
            align-items: center;
        }

        .detail-value {
            font-weight: 600;
            color: #333;
            text-align: right;
        }

        .icon {
            margin-right: 8px;
            color: #FF6B81;
        }

        .caregiver-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #fff7f9;
            padding: 15px;
            border-radius: 10px;
        }

        .caregiver-info img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #FFD8E0;
        }

        .caregiver-name {
            font-weight: 700;
            color: #FF6B81;
        }

        .service-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #f0f0f0;
            font-size: 15px;
        }

        .service-item:last-child {
            border-bottom: none;
        }

        .service-item strong {
            color: #555;
            font-weight: 600;
        }

        .total-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 2px solid #FF6B81;
            padding-top: 15px;
            margin-top: 10px;
            font-size: 20px;
            font-weight: 800;
        }

        .status-tag {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
        }

        .status-cho_xac_nhan {
            background: #fff3e0;
            color: #ff9800;
        }

        .status-dang_hoan_thanh,
        .status-dang_tien_hanh {
            background: #e3f2fd;
            color: #2196f3;
        }

        .status-da_hoan_thanh {
            background: #e8f5e9;
            color: #4caf50;
        }

        .status-da_huy {
            background: #ffebee;
            color: #f44336;
        }

        .buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-top: 30px;
        }

        .button {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
            color: white;
            font-size: 15px;
            font-weight: 600;
            text-align: center;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-back {
            background-color: #9e9e9e;
            color: white;
        }

        .btn-home {
            background-color: #FF6B81;
        }

        .btn-rate {
            background-color: #FFC300;
            color: #333;
        }

        .btn-reorder {
            background-color: #FF6B81;
        }

        .btn-chat {
            background-color: #2196f3;
            color: white;
        }

        .btn-cancel {
            background-color: #f44336;
            color: white;
        }

        .hidden {
            display: none !important;
        }

        .message-box {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-weight: bold;
        }

        .msg-loading {
            background: #e3f2fd;
            color: #2196F3;
        }

        .msg-error {
            background: #f8d7da;
            color: #721c24;
        }

        .msg-success {
            background: #e8f5e9;
            color: #4caf50;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
            visibility: hidden;
        }

        .modal-overlay:not(.hidden) {
            opacity: 1;
            visibility: visible;
        }

        .success-modal {
            background: #fff;
            padding: 40px 30px;
            border-radius: 16px;
            text-align: center;
            width: 90%;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }

        .modal-overlay:not(.hidden) .success-modal {
            transform: scale(1);
        }

        .success-icon {
            font-size: 72px;
            color: #4caf50;
            margin-bottom: 20px;
            animation: popIn 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55) 0.2s forwards;
            transform: scale(0);
        }

        .success-modal h2 {
            font-size: 24px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .success-modal p {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
            line-height: 1.5;
        }

        .success-modal .button {
            width: 100%;
            justify-content: center;
        }

        @keyframes popIn {
            0% {
                transform: scale(0);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
</head>

<body>

    <div id="successOverlay" class="modal-overlay hidden">
        <div class="success-modal">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 id="successModalTitle">Đặt hàng thành công!</h2>
            <p id="successModalMessage">Cảm ơn bạn! Đơn hàng đang chờ xác nhận.</p>
            <button id="closeSuccessModal" class="button btn-home">Xem chi tiết đơn hàng</button>
        </div>
    </div>

    <div class="container" id="mainContainer">

        <div id="loadingView" class="message-box msg-loading">
            <i class="fas fa-spinner fa-spin"></i> Đang tải chi tiết đơn hàng...
        </div>

        <div id="errorView" class="message-box msg-error hidden"></div>

        <div id="contentView" class="hidden">
            <h1><i class="fas fa-file-invoice"></i> Chi Tiết Đơn Hàng</h1>
            <p class="order-id">Mã đơn hàng: <strong id="orderId">#...</strong></p>

            <div class="section-box">
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-info-circle"></i> Trạng Thái:</span>
                    <span class="status-tag" id="orderStatus">...</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-calendar-alt"></i> Ngày Đặt:</span>
                    <span class="detail-value" id="orderNgayDat">...</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label"><i class="icon fas fa-credit-card"></i> Thanh toán:</span>
                    <span class="detail-value" id="orderPayment">...</span>
                </div>
            </div>

            <div id="caregiverBox" class="section-box hidden">
                <div class="section-title"><i class="icon fas fa-user-nurse"></i> Người Chăm Sóc</div>
                <div class="caregiver-info">
                    <img id="caregiverAvatar" src="img/default_avatar.png" alt="Avatar">
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
                <span>Tổng Cộng</span>
                <span style="color: #FF6B81;" id="orderTongTien">... VND</span>
            </div>

            <div class="buttons" id="actionButtonsContainer">
            </div>
        </div>
    </div>

    <script>
        const ID_DON_HANG = <?php echo $id_don_hang; ?>;
        const API_URL = '../Backend/api_order_details.php';

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
            const {
                order,
                services,
                is_rated
            } = data;

            document.getElementById('contentView').classList.remove('hidden');
            document.getElementById('loadingView').classList.add('hidden');

            document.getElementById('orderId').textContent = `#${order.id_don_hang}`;

            const statusClass = statusToClass(order.trang_thai);
            const statusEl = document.getElementById('orderStatus');
            statusEl.textContent = order.trang_thai;
            statusEl.className = `status-tag status-${statusClass}`;

            document.getElementById('orderNgayDat').textContent = formatDateTime(order.ngay_dat);

            let payment_method = order.hinh_thuc_thanh_toan || 'Chưa xác định';
            if (payment_method.toLowerCase() === 'vnpay' && (order.thanh_toan_status || '').toLowerCase() !== 'đã thanh toán') {
                payment_method = 'VNPAY (Chờ)';
            }
            document.getElementById('orderPayment').textContent = payment_method;

            if (order.caregiver_id) {
                document.getElementById('caregiverBox').classList.remove('hidden');
                document.getElementById('caregiverName').textContent = order.ten_cham_soc;
                document.getElementById('caregiverId').textContent = `ID: #${order.caregiver_id}`;
                document.getElementById('caregiverAvatar').src = `../../../${order.hinh_anh_cham_soc}`;
            }

            document.getElementById('orderStartTime').textContent = formatDateTime(order.thoi_gian_bat_dau);
            document.getElementById('orderEndTime').textContent = formatDateTime(order.thoi_gian_ket_thuc);

            const serviceContainer = document.getElementById('serviceListContainer');
            if (services.length > 0) {
                serviceContainer.innerHTML = '<span class="detail-label" style="font-weight: 700; color: #333;"><i class="icon fas fa-tasks"></i> Các Nhiệm Vụ Cụ Thể:</span>';
                services.forEach(service => {
                    serviceContainer.innerHTML += `
                        <div class="service-item">
                            <strong>${service.ten_nhiem_vu}</strong>
                        </div>
                    `;
                });
            }

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

            container.innerHTML += `<a href="index.php" class="button btn-home"><i class="fas fa-home"></i> Trang Chủ</a>`;

            const status = (order.trang_thai || '').toLowerCase().trim();

            if (status === 'chờ xác nhận') {
                container.innerHTML += `<button class="button btn-cancel" onclick="handleCancelOrder()"><i class="fas fa-times-circle"></i> Hủy Đơn Hàng</button>`;
            }

            if (status === 'đã hoàn thành' && order.caregiver_id) {
                if (is_rated) {
                    container.innerHTML += `<button class="button btn-rate" style="background-color: #4caf50; cursor: default;" disabled><i class="fas fa-check"></i> Đã Đánh Giá</button>`;
                } else {
                    container.innerHTML += `<a href="danhgia.php?id_cs=${order.caregiver_id}&id_dh=${order.id_don_hang}" class="button btn-rate"><i class="fas fa-star"></i> Đánh Giá</a>`;
                }
            }

            if (order.caregiver_id) {
                container.innerHTML += `<a href="Chatkhachhang.php?caregiver_id=${order.caregiver_id}" class="button btn-chat"><i class="fas fa-comment-dots"></i> Chat</a>`;
            }
        }

        async function handleCancelOrder() {
            if (!confirm('Bạn có chắc chắn muốn HỦY đơn hàng #' + ID_DON_HANG + ' không?')) return;

            const data = new URLSearchParams();
            data.append('action', 'cancel_order');
            data.append('id_don_hang', ID_DON_HANG);

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    body: data
                });
                const result = await response.json();

                if (result.success) {
                    alert('Hủy đơn hàng thành công!');
                    await loadData();
                } else {
                    alert(`Lỗi: ${result.message}`);
                }
            } catch (error) {
                alert('Lỗi kết nối khi hủy đơn: ' + error.message);
            }
        }

        async function loadData() {
            try {
                const response = await fetch(`${API_URL}?action=get_details&id=${ID_DON_HANG}`);

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

        function showSuccessModal(title, message) {
            document.getElementById('successModalTitle').textContent = title;
            document.getElementById('successModalMessage').textContent = message;
            document.getElementById('successOverlay').classList.remove('hidden');
        }

        document.getElementById('closeSuccessModal').addEventListener('click', () => {
            document.getElementById('successOverlay').classList.add('hidden');
            loadData();
        });

        document.addEventListener('DOMContentLoaded', () => {
            const urlParams = new URLSearchParams(window.location.search);
            let showedModal = false;

            if (urlParams.get('payment') === 'success') {
                showSuccessModal(
                    'Thanh toán VNPAY thành công!',
                    'Cảm ơn bạn! Đơn hàng đã được thanh toán và đang chờ xử lý.'
                );
                showedModal = true;
            } else if (urlParams.get('status') === 'new_cash_order') {
                showSuccessModal(
                    'Đặt hàng thành công!',
                    'Cảm ơn bạn đã sử dụng dịch vụ. Đơn hàng của bạn đang chờ được xác nhận.'
                );
                showedModal = true;
            } else if (urlParams.get('payment') === 'failed') {
                showError('Thanh toán VNPAY thất bại.');
            }

            if (!showedModal) {
                loadData();
            }
        });
    </script>
</body>

</html>