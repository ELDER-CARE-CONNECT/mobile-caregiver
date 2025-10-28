<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <title>Đơn Hàng Chưa Nhận - Caregiver</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="../CSS/style.css">
</head>
<body>
  <main class="cg-wrap">
    <header class="cg-header">
      <h1>Đơn Hàng Đang Chờ Nhận</h1>
      <p>Hiển thị tất cả đơn ở trạng thái <b>chờ xác nhận / chưa gán người chăm sóc</b></p>
    </header>

    <!-- Bộ lọc -->
    <form id="cg-filter" class="cg-filters">
      <label>Từ ngày
        <input id="from" type="date">
      </label>
      <label>Đến ngày
        <input id="to" type="date">
      </label>
      <label>Phương thức
        <select id="pm">
          <option value="">-- Tất cả --</option>
          <option value="Tien_mat">Tiền mặt</option>
          <option value="Chuyen_khoan">Chuyển khoản</option>
          <option value="Vi_dien_tu">Ví điện tử</option>
        </select>
      </label>
      <div class="cg-actions">
        <button type="submit" class="cg-btn cg-btn-primary"><i class="fas fa-filter"></i> Lọc</button>
        <button type="button" id="resetBtn" class="cg-btn cg-btn-ghost"><i class="fas fa-rotate"></i> Làm mới</button>
      </div>
    </form>

    <div class="cg-topbar">
      <span id="summaryLine" class="cg-muted">Tổng dịch vụ: 0</span>
      <div class="cg-pager">
        <button id="prevBtn" class="cg-pagebtn" disabled><i class="fas fa-chevron-left"></i> Trước</button>
        <span id="pageInfo" class="cg-muted">Trang 1/1</span>
        <button id="nextBtn" class="cg-pagebtn" disabled>Sau <i class="fas fa-chevron-right"></i></button>
      </div>
    </div>

    <!-- Danh sách thẻ -->
    <section id="cg-list" class="cg-grid"></section>
  </main>

  <script>
    // Biến toàn cục
    let currentPage = 1;
    let totalPages = 1;
    let totalOrders = 0;
    
    // Khởi tạo trang
    document.addEventListener('DOMContentLoaded', function() {
      loadOrders();
      setupEventListeners();
    });
    
    // Thiết lập event listeners
    function setupEventListeners() {
      // Form lọc
      document.getElementById('cg-filter').addEventListener('submit', function(e) {
        e.preventDefault();
        currentPage = 1;
        loadOrders();
      });
      
      // Nút reset
      document.getElementById('resetBtn').addEventListener('click', function() {
        document.getElementById('from').value = '';
        document.getElementById('to').value = '';
        document.getElementById('pm').value = '';
        currentPage = 1;
        loadOrders();
      });
      
      // Nút phân trang
      document.getElementById('prevBtn').addEventListener('click', function() {
        if (currentPage > 1) {
          currentPage--;
          loadOrders();
        }
      });
      
      document.getElementById('nextBtn').addEventListener('click', function() {
        if (currentPage < totalPages) {
          currentPage++;
          loadOrders();
        }
      });
    }
    
    // Tải danh sách đơn hàng
    async function loadOrders() {
      try {
        const fromDate = document.getElementById('from').value;
        const toDate = document.getElementById('to').value;
        
        const params = new URLSearchParams({
          page: currentPage,
          from: fromDate,
          to: toDate
        });
        
        const response = await fetch(`get_unassigned_orders.php?${params}`);
        const data = await response.json();
        
        if (data.success) {
          displayOrders(data.data);
          updatePagination(data.pagination);
        } else {
          showError('Lỗi tải dữ liệu: ' + data.message);
        }
      } catch (error) {
        showError('Lỗi kết nối: ' + error.message);
      }
    }
    
    // Hiển thị danh sách đơn hàng
    function displayOrders(orders) {
      const container = document.getElementById('cg-list');
      
      if (orders.length === 0) {
        container.innerHTML = `
          <div class="cg-empty">
            <i class="fas fa-inbox"></i>
            <h3>Không có đơn hàng nào</h3>
            <p>Hiện tại không có đơn hàng nào chưa được gán người chăm sóc.</p>
          </div>
        `;
        return;
      }
      
      container.innerHTML = orders.map(order => `
        <div class="cg-card" data-order-id="${order.id_don_hang}">
          <div class="cg-card-header">
            <h3>Đơn hàng #${order.id_don_hang}</h3>
            <span class="cg-status cg-status-pending">${order.trang_thai}</span>
          </div>
          
          <div class="cg-card-body">
            <div class="cg-info-row">
              <i class="fas fa-user"></i>
              <span class="cg-label">Khách hàng:</span>
              <span class="cg-value">${order.ten_khach_hang}</span>
            </div>
            
            <div class="cg-info-row">
              <i class="fas fa-phone"></i>
              <span class="cg-label">SĐT:</span>
              <span class="cg-value">${order.so_dien_thoai}</span>
            </div>
            
            ${order.email ? `
            <div class="cg-info-row">
              <i class="fas fa-envelope"></i>
              <span class="cg-label">Email:</span>
              <span class="cg-value">${order.email}</span>
            </div>
            ` : ''}
            
            <div class="cg-info-row">
              <i class="fas fa-calendar"></i>
              <span class="cg-label">Ngày đặt:</span>
              <span class="cg-value">${formatDate(order.ngay_dat)}</span>
            </div>
            
            <div class="cg-info-row">
              <i class="fas fa-clock"></i>
              <span class="cg-label">Thời gian:</span>
              <span class="cg-value">${order.thoi_gian_bat_dau} - ${order.thoi_gian_ket_thuc}</span>
            </div>
            
            <div class="cg-info-row">
              <i class="fas fa-map-marker-alt"></i>
              <span class="cg-label">Địa chỉ:</span>
              <span class="cg-value">${order.dia_chi_giao_hang || order.dia_chi_khach_hang || 'Chưa cập nhật'}</span>
            </div>
            
            <div class="cg-info-row cg-price">
              <i class="fas fa-money-bill-wave"></i>
              <span class="cg-label">Tổng tiền:</span>
              <span class="cg-value cg-price-value">${order.tong_tien}</span>
            </div>
          </div>
          
          <div class="cg-card-footer">
            <button class="cg-btn cg-btn-primary" onclick="acceptOrder(${order.id_don_hang})">
              <i class="fas fa-handshake"></i> Nhận đơn
            </button>
            <button class="cg-btn cg-btn-secondary" onclick="viewOrderDetail(${order.id_don_hang})">
              <i class="fas fa-eye"></i> Chi tiết
            </button>
          </div>
        </div>
      `).join('');
    }
    
    // Cập nhật thông tin phân trang
    function updatePagination(pagination) {
      totalPages = pagination.total_pages;
      totalOrders = pagination.total_orders;
      
      document.getElementById('summaryLine').textContent = `Tổng đơn hàng: ${totalOrders}`;
      document.getElementById('pageInfo').textContent = `Trang ${pagination.current_page}/${totalPages}`;
      
      document.getElementById('prevBtn').disabled = pagination.current_page <= 1;
      document.getElementById('nextBtn').disabled = pagination.current_page >= totalPages;
    }
    
    // Định dạng ngày
    function formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('vi-VN');
    }
    
    // Nhận đơn hàng
    function acceptOrder(orderId) {
      if (confirm('Bạn có chắc chắn muốn nhận đơn hàng này?')) {
        // TODO: Implement accept order functionality
        alert('Chức năng nhận đơn hàng sẽ được triển khai sau');
      }
    }
    
    // Xem chi tiết đơn hàng
    function viewOrderDetail(orderId) {
      // TODO: Implement view order detail functionality
      alert('Chức năng xem chi tiết sẽ được triển khai sau');
    }
    
    // Hiển thị lỗi
    function showError(message) {
      const container = document.getElementById('cg-list');
      container.innerHTML = `
        <div class="cg-error">
          <i class="fas fa-exclamation-triangle"></i>
          <h3>Lỗi</h3>
          <p>${message}</p>
          <button class="cg-btn cg-btn-primary" onclick="loadOrders()">
            <i class="fas fa-refresh"></i> Thử lại
          </button>
        </div>
      `;
    }
  </script>
</body>
</html>




