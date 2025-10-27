<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <title>Hệ thống chăm sóc người già - CareGiver</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="../CSS/style.css" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  <!-- Sidebar Navigation -->
  <aside class="sidebar">
    <div class="logo">
      <i class="fas fa-heart-pulse"></i>
      <h2>CareGiver</h2>
    </div>
    
    <nav class="menu">
      <a href="#" class="menu-item">
        <i class="fas fa-home"></i>
        <span>Trang chủ</span>
      </a>
      
      <a href="#" class="menu-item">
        <i class="fas fa-user-plus"></i>
        <span>Thêm hồ sơ</span>
      </a>
      
      <a href="#" class="menu-item">
        <i class="fas fa-users"></i>
        <span>Hồ Sơ Bệnh Nhân</span>
      </a>
      
      <a href="#" class="menu-item active">
        <i class="fas fa-clipboard-list"></i>
        <span>Lịch Sử Đặt Dịch Vụ</span>
        <span class="badge">5</span>
      </a>
      
      <a href="#" class="menu-item">
        <i class="fas fa-calendar-check"></i>
        <span>Lịch Hẹn</span>
      </a>
      
      <a href="#" class="menu-item">
        <i class="fas fa-chart-line"></i>
        <span>Báo Cáo</span>
      </a>
      
      <a href="#" class="menu-item">
        <i class="fas fa-bell"></i>
        <span>Thông Báo</span>
        <span class="badge notification">99+</span>
      </a>
      
      <a href="#" class="menu-item">
        <i class="fas fa-cog"></i>
        <span>Cài Đặt</span>
      </a>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="main-content">
    <header class="header">
      <div class="header-left">
        <h1>Lịch Sử Đặt Dịch Vụ</h1>
        <p>Quản Lý Và Theo Dõi Các Dịch Vụ Chăm Sóc Đã Đặt</p>
      </div>
      <div class="header-right">
        <div class="search-box">
          <i class="fas fa-search"></i>
          <input type="text" placeholder="Tìm kiếm..." />
        </div>
        <div class="user-profile">
          <img src="https://via.placeholder.com/40" alt="Avatar" />
          <span>Nguyễn Minh Thư</span>
        </div>
      </div>
    </header>

    <!-- Breadcrumb -->
    <div class="breadcrumb">
      <span>Trang chủ</span>
      <i class="fas fa-chevron-right"></i>
      <span>Lịch Sử Đặt Dịch Vụ</span>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs">
      <button class="tab-btn active" data-status="all">
        <i class="fas fa-list"></i>
        Tất cả
      </button>
      <button class="tab-btn" data-status="completed">
        <i class="fas fa-check-circle"></i>
        Hoàn thành
      </button>
      <button class="tab-btn" data-status="pending">
        <i class="fas fa-clock"></i>
        Đang xử lý
      </button>
      <button class="tab-btn" data-status="cancelled">
        <i class="fas fa-times-circle"></i>
        Đã hủy
      </button>
    </div>

    <!-- Search and Filter Tools -->
    <div class="tools-section">
      <div class="search-filters">
        <div class="filter-group">
          <label for="searchInput">Tìm kiếm</label>
          <div class="search-input">
            <i class="fas fa-search"></i>
            <input id="searchInput" type="text" placeholder="Mã đơn, tên khách hàng, người chăm sóc..." />
          </div>
        </div>
        
        <div class="filter-group">
          <label for="dateFrom">Từ ngày</label>
          <input id="dateFrom" type="date" />
        </div>
        
        <div class="filter-group">
          <label for="dateTo">Đến ngày</label>
          <input id="dateTo" type="date" />
        </div>
        
        <div class="filter-group">
          <button class="btn-primary" id="searchBtn">
            <i class="fas fa-search"></i>
            Tìm kiếm
          </button>
        </div>
        
        <div class="filter-group">
          <button class="btn-secondary" id="resetBtn">
            <i class="fas fa-refresh"></i>
            Làm mới
          </button>
        </div>
      </div>
    </div>

    <!-- Statistics -->
    <div class="stats-section">
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-clipboard-list"></i>
        </div>
        <div class="stat-content">
          <h3 id="totalOrders">0</h3>
          <p>Tổng đơn hàng</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-check-circle"></i>
        </div>
        <div class="stat-content">
          <h3 id="completedOrders">0</h3>
          <p>Hoàn thành</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
          <h3 id="pendingOrders">0</h3>
          <p>Đang xử lý</p>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="stat-icon">
          <i class="fas fa-dollar-sign"></i>
        </div>
        <div class="stat-content">
          <h3 id="totalRevenue">0₫</h3>
          <p>Tổng doanh thu</p>
        </div>
      </div>
    </div>

    <!-- Orders Table -->
    <div class="table-section">
      <div class="table-header">
        <h2>Danh sách đơn hàng</h2>
        <div class="table-actions">
          <button class="btn-export">
            <i class="fas fa-download"></i>
            Xuất Excel
          </button>
          <button class="btn-print">
            <i class="fas fa-print"></i>
            In báo cáo
          </button>
        </div>
      </div>
      
      <div class="table-container">
        <table class="orders-table">
          <thead>
            <tr>
              <th>Mã đơn</th>
              <th>Ngày đặt</th>
              <th>Khách hàng</th>
              <th>Người chăm sóc</th>
              <th>Dịch vụ</th>
              <th>Thời gian</th>
              <th>Giá tiền</th>
              <th>Trạng thái</th>
              <th>Hành động</th>
            </tr>
          </thead>
          <tbody id="ordersTableBody">
            <!-- Data will be populated by JavaScript -->
          </tbody>
        </table>
      </div>
      
      <!-- Pagination -->
      <div class="pagination">
        <button class="page-btn" id="prevBtn">
          <i class="fas fa-chevron-left"></i>
          Trước
        </button>
        <div class="page-info">
          <span id="pageInfo">Trang 1/1</span>
        </div>
        <button class="page-btn" id="nextBtn">
          Sau
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>
  </div>

  <!-- Order Detail Modal -->
  <div id="orderModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>Chi tiết đơn hàng</h2>
        <button class="close-btn" id="closeModal">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="modal-body" id="modalBody">
        <!-- Order details will be populated here -->
      </div>
    </div>
  </div>

  <!-- Template for order row -->
  <template id="orderRowTemplate">
    <tr class="order-row">
      <td class="order-id"></td>
      <td class="order-date"></td>
      <td class="customer-name"></td>
      <td class="caregiver-name"></td>
      <td class="service-type"></td>
      <td class="service-time"></td>
      <td class="order-price"></td>
      <td class="order-status">
        <span class="status-badge"></span>
      </td>
      <td class="order-actions">
        <button class="btn-view" title="Xem chi tiết">
          <i class="fas fa-eye"></i>
        </button>
        <button class="btn-edit" title="Chỉnh sửa">
          <i class="fas fa-edit"></i>
        </button>
        <button class="btn-cancel" title="Hủy đơn">
          <i class="fas fa-times"></i>
        </button>
      </td>
    </tr>
  </template>

  <script src="../JS/app.js"></script>
</body>
</html>