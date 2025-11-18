<?php
session_set_cookie_params(0, '/');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_khach_hang'])) { 

    header("Location: ../../../Admin/frontend/auth/login.php"); 
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đặt hàng</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }
        body {
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
            padding-top: 20px;
            max-width: 100%;
            overflow-x: hidden;
        }
        .accepted-orders-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .hero h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .orders-wrapper {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border: 1px solid #e5e7eb;
        }
        .orders-wrapper h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 25px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }
        .order-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }
        .order-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            height: 190px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
        }
        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
            border-color: #FF6B81;
        }
        .order-card h3 {
            margin: 0 0 12px;
            font-size: 17px;
            font-weight: 700;
            color: #1f2937;
        }
        .order-info p {
            margin: 4px 0;
            color: #374151;
            font-size: 15px;
            line-height: 1.4;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .status.completed,
        .status.da_hoan_thanh {
            background: #d1fae5;
            color: #065f46;
        }
        .status.pending,
        .status.cho_xac_nhan,
        .status.dang_hoan_thanh {
            background: #fef3c7;
            color: #92400e;
        }
        .status.da_huy {
            background: #fee2e2;
            color: #dc2626;
        }
        .status.khac {
            background: #e0f2f1;
            color: #1d4ed8;
        }
        .view-btn {
            background: #FF6B81;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            align-self: flex-end;
            text-decoration: none;
            display: inline-block;
        }
        .view-btn:hover {
            background: #E55B70;
            box-shadow: 0 4px 10px rgba(255, 107, 129, 0.3);
            transform: translateY(-2px);
        }
        .order-cards:has(.order-card:only-child) {
            justify-content: center;
        }
        #loading-message,
        #error-message {
            text-align: center;
            padding: 50px;
            font-size: 18px;
            color: #999;
            grid-column: 1 / -1;
        }
        @media (max-width: 768px) {
            .order-card {
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>
<body>

    <?php
    include 'navbar.php';
    ?>

    <div class="accepted-orders-container">
        <div class="hero">
            <h1><i class="fas fa-check-circle"></i> Lịch sử đặt hàng của bạn</h1>
        </div>

        <div class="orders-wrapper">
            <h2>Xin chào, <span id="customerNamePlaceholder" class="highlight">Đang tải...</span> 👋</h2>

            <div class="order-cards" id="orderCardsContainer">
                <p id="loading-message">Đang tải danh sách đơn hàng...</p>
            </div>
        </div>
    </div>

    <script>
        // BẮT ĐẦU THAY ĐỔI: Sử dụng API Gateway
        const GATEWAY_URL = '../Backend/api_gateway.php';
        const API_ORDER_LIST_URL = `${GATEWAY_URL}?route=order/list`;
        // KẾT THÚC THAY ĐỔI
        
        function formatCurrency(amount) {
            if (typeof amount !== 'number') {
                amount = parseFloat(amount) || 0;
            }
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                currencyDisplay: 'symbol'
            }).format(amount).replace('₫', '₫').trim();
        }

        function getStatusClass(status) {
            if (!status) return 'khac';
            const s = status.toLowerCase().trim();
            if (s.includes('hoàn thành')) return 'completed';
            if (s.includes('chờ xác nhận') || s.includes('đang hoàn thành')) return 'pending';
            if (s.includes('hủy')) return 'da_huy';
            return 'khac';
        }

        function createOrderCard(order) {
            const statusClass = getStatusClass(order.trang_thai);
            const ten_cham_soc = order.ten_cham_soc || "Chưa có";
            const detailUrl = 'Chitietlichsudonhang.php';

            return `
                <div class='order-card'>
                    <div>
                        <h3>Mã đơn: #${order.id_don_hang}</h3>
                        <div class='order-info'>
                            <p><strong>Người chăm sóc:</strong> ${ten_cham_soc}</p>
                            <p><strong>Ngày đặt:</strong> ${new Date(order.ngay_dat).toLocaleDateString('vi-VN')}</p>
                            <p><strong>Trạng thái:</strong> 
                                <span class='status ${statusClass}'>
                                    ${order.trang_thai}
                                </span>
                            </p>
                            <p><strong>Tổng tiền:</strong> ${formatCurrency(order.tong_tien)}</p>
                        </div>
                    </div>
                    <a href='${detailUrl}?id=${order.id_don_hang}' class='view-btn'>Xem chi tiết</a>
                </div>
            `;
        }

        async function fetchOrders() {
            const container = document.getElementById('orderCardsContainer');
            const namePlaceholder = document.getElementById('customerNamePlaceholder');
            // const apiUrl = '../Backend/api_order_list.php'; // Dòng cũ
            const apiUrl = API_ORDER_LIST_URL; // Dòng mới sử dụng Gateway

            try {
                const response = await fetch(apiUrl);

                if (!response.ok) {
                    const errorText = await response.text();
                    container.innerHTML = `<p id="error-message" style="color:red; grid-column: 1 / -1;">
                        Lỗi kết nối API (${response.status}): ${response.status === 401 ? 'Bạn chưa đăng nhập.' : 'Vui lòng kiểm tra lại đường dẫn API.'}
                        </p>`;
                    namePlaceholder.textContent = 'Khách hàng';
                    return;
                }

                const result = await response.json();

                if (result.success && Array.isArray(result.data)) {
                    container.innerHTML = '';
                    namePlaceholder.textContent = result.customer_name || 'Khách hàng';

                    if (result.data.length === 0) {
                        container.innerHTML = "<p style='text-align: center; color: #ff6b81;'>❌ Bạn chưa có đơn hàng nào.</p>";
                        return;
                    }

                    result.data.sort((a, b) => b.id_don_hang - a.id_don_hang);

                    result.data.forEach(order => {
                        container.insertAdjacentHTML('beforeend', createOrderCard(order));
                    });
                } else {
                    container.innerHTML = `<p id="error-message" style="color:red; grid-column: 1 / -1;">Lỗi tải dữ liệu: ${result.message || 'API trả về lỗi không xác định.'}</p>`;
                    namePlaceholder.textContent = 'Khách hàng';
                }
            } catch (error) {
                console.error('Lỗi gọi API:', error);
                container.innerHTML = `<p id="error-message" style="color:red; grid-column: 1 / -1;">Lỗi kết nối Microservice. Vui lòng kiểm tra trạng thái server. (${error.message})</p>`;
                namePlaceholder.textContent = 'Khách hàng';
            }
        }

        (function() {
            fetchOrders();
        })();
    </script>

</body>
</html>