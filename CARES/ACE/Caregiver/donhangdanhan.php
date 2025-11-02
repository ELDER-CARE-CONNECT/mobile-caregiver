<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <title>Đơn Hàng Đã Nhận </title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  
  <style>
    :root {
      --xanh: #0b5ed7;
      --xanh-nhat: #eaf2ff;
      --vien: #e7e9ef;
      --chu: #1a1d29;
    }

    * {
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
    }

    body {
      margin: 0;
      font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;
      color: var(--chu);
      background: #fff;
      display: grid;
      grid-template-columns: 260px 1fr;
      min-height: 100vh;
    }

    /* Sidebar (tùy chọn) */
    .sidebar {
      background: var(--xanh);
      color: #fff;
      padding: 22px 18px;
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .logo {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo .hinh {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      background: #ffffff22;
      display: inline-block;
    }

    .logo h2 {
      margin: 0;
      font-size: 18px;
      line-height: 1.2;
    }

    .menu {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .menu a {
      color: #fff;
      text-decoration: none;
      padding: 10px 12px;
      border-radius: 10px;
      opacity: 0.95;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .menu a:hover,
    .menu a.active {
      background: #ffffff22;
      opacity: 1;
    }

    /* Nội dung chính */
    .cg-wrap {
      display: flex;
      flex-direction: column;
    }

    header.cg-header {
      padding: 14px 20px;
      border-bottom: 1px solid var(--vien);
      background: #fff;
      position: sticky;
      top: 0;
      z-index: 5;
    }

    header h1 {
      margin: 0;
      font-size: 20px;
    }

    header p {
      margin: 4px 0 0 0;
      color: #5b6070;
      font-size: 14px;
    }

    /* Bộ lọc / thanh công cụ */
    .cg-filters {
      display: flex;
      flex-wrap: wrap;
      gap: 12px 16px;
      padding: 14px 20px;
      background: var(--xanh-nhat);
      border-bottom: 1px solid var(--vien);
    }

    .cg-filters label {
      display: flex;
      flex-direction: column;
      gap: 6px;
      font-size: 14px;
    }

    /* Thanh tìm kiếm */
    .cg-search {
      flex: 1;
      min-width: 280px;
    }

    .cg-search .field {
      position: relative;
    }

    .cg-search .field i {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #8b90a0;
      pointer-events: none;
    }

    .cg-search .field input {
      width: 100%;
      height: 42px;
      padding: 10px 14px 10px 38px;
      border: 1px solid var(--vien);
      border-radius: 10px;
      background: #fff;
      line-height: 1.2;
    }

    .cg-actions {
      display: flex;
      align-items: flex-end;
      gap: 15px;
      margin-left: auto;
    }

    .cg-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 14px;
      border-radius: 10px;
      cursor: pointer;
      border: 1px solid transparent;
      font-size: 14px;
      line-height: 1;
      transition: all 0.3s ease;
    }

    .cg-btn i {
      font-size: 14px;
    }

    .cg-btn-primary {
      background: var(--xanh);
      color: #fff;
    }

    .cg-btn-primary:hover {
      filter: brightness(0.95);
    }

    .cg-btn-ghost {
      background: #fff;
      color: #1a1d29;
      border: 1px solid var(--vien);
    }

    .cg-btn-ghost:hover {
      background: #f7f8fc;
    }

    /* Topbar & phân trang */
    .cg-topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 20px;
      color:;
      font-size: 14px;
    }

    .cg-muted {
      color: #5b6070;
    }

    .cg-pager {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .cg-pagebtn {
      padding: 8px 12px;
      border: 1px solid var(--vien);
      background: #fff;
      border-radius: 10px;
      cursor: pointer;
      font-size: 14px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .cg-pagebtn:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .cg-pagebtn:hover:not(:disabled) {
      background: #f7f8fc;
    }

    /* Lưới thẻ đơn */
    .cg-grid {
      padding: 18px 20px;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 16px;
    }

    .cg-card {
      border: 1px solid var(--vien);
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .cg-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .cg-card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 12px 14px;
      border-bottom: 1px solid var(--vien);
      background: #f7f8fc;
    }

    .cg-card-header h3 {
      margin: 0;
      font-size: 16px;
    }

    .cg-status {
      padding: 4px 10px;
      border-radius: 999px;
      font-size: 12px;
      border: 1px solid transparent;
      display: inline-block;
    }

    .cg-status-pending {
      background: #fff9e6;
      color: #a15c00;
      border-color: #ffe6a1;
    }

    .cg-card-body {
      padding: 12px 14px;
      display: grid;
      gap: 10px;
    }

    .cg-info-row {
      display: grid;
      grid-template-columns: 20px 110px 1fr;
      align-items: start;
      gap: 8px;
      font-size: 14px;
    }

    .cg-info-row i {
      margin-top: 2px;
      color: var(--xanh);
    }

    .cg-label {
      color: #5b6070;
    }

    .cg-value {
      color: #1a1d29;
      word-break: break-word;
    }

    .cg-price .cg-value {
      font-weight: 700;
      color: var(--xanh);
    }

    .cg-card-footer {
      display: flex;
      gap: 10px;
      padding: 12px 14px;
      border-top: 1px solid var(--vien);
    }

    /* Trạng thái rỗng */
    .cg-empty,
    .cg-error {
      border: 1px dashed var(--vien);
      border-radius: 12px;
      padding: 24px;
      text-align: center;
      color: #5b6070;
      background: #fff;
      grid-column: 1 / -1;
    }

    .cg-empty i,
    .cg-error i {
      font-size: 28px;
      margin-bottom: 8px;
      display: block;
      color: #d1d5db;
    }

    /* BRIDGE: nếu không có sidebar */
    body:has(.cg-wrap):not(:has(.sidebar)) {
      display: block;
      grid-template-columns: unset;
    }

    /* Mobile */
    @media (max-width: 768px) {
      body {
        grid-template-columns: 1fr;
      }

      .sidebar {
        display: none;
      }

      .cg-filters {
        flex-direction: column;
      }

      .cg-actions {
        width: 100%;
        justify-content: flex-end;
      }

      .cg-topbar {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
      }

      .cg-grid {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 600px) {
      .cg-actions {
        width: 100%;
        justify-content: flex-end;
      }

      .cg-card-footer {
        flex-direction: column;
      }

      .cg-card-footer .cg-btn {
        width: 100%;
        justify-content: center;
      }
    }
  </style>
</head>

<body>
  <main class="cg-wrap">
    <header class="cg-header">
      <h1>Đơn Hàng Đã Xác Nhận</h1>
      <p>Hiển thị tất cả đơn ở trạng thái <b>Đã Xác Nhận</b></p>
    </header>

    <!-- Thanh tìm kiếm và bộ lọc -->
    <form id="cg-filter" class="cg-filters">
      <div class="cg-search">
        <label for="q">Tìm kiếm</label>
        <div class="field">
          <i class="fas fa-search" aria-hidden="true"></i>
          <input id="q" type="text" placeholder="Mã đơn, tên khách hàng, SĐT, email">
        </div>
      </div>
      <div class="cg-actions">
        <button type="submit" class="cg-btn cg-btn-primary">
          <i class="fas fa-magnifying-glass"></i> Tìm kiếm
        </button>
        <button type="button" id="resetBtn" class="cg-btn cg-btn-ghost">
          <i class="fas fa-rotate"></i> Làm mới
        </button>
      </div>
    </form>

    <!-- Thanh tóm tắt và phân trang -->
    <div class="cg-topbar">
      <span id="summaryLine" class="cg-muted">Tổng đơn hàng: </span>
      <div class="cg-pager">
        <button id="prevBtn" class="cg-pagebtn" disabled>
          <i class="fas fa-chevron-left"></i> Trước
        </button>
        <span id="pageInfo" class="cg-muted">Trang 1/1</span>
        <button id="nextBtn" class="cg-pagebtn">
          Sau <i class="fas fa-chevron-right"></i>
        </button>
      </div>
    </div>

    <!-- Danh sách đơn hàng -->
    <section id="cg-list" class="cg-grid">
      <!-- Empty state -->
      <div class="cg-empty" style="display: none;">
        <i class="fas fa-inbox"></i>
        <p>Chưa có đơn hàng nào đã xác nhận</p>
      </div>

      <!-- Card đơn hàng mẫu 1 -->
      <div class="cg-card" data-order-id="1">
        <div class="cg-card-header">
          <h3>Đơn hàng #DH001</h3>
          <span class="cg-status cg-status-pending">ĐÃ XÁC NHẬN</span>
        </div>
        <div class="cg-card-body">
          <div class="cg-info-row">
            <i class="fas fa-user"></i>
            <span class="cg-label">Khách hàng:</span>
            <span class="cg-value">Nguyễn Văn A</span>
          </div>
          <div class="cg-info-row">
            <i class="fas fa-phone"></i>
            <span class="cg-label">SĐT:</span>
            <span class="cg-value">0123456789</span>
          </div>
          <div class="cg-info-row">
            <i class="fas fa-calendar"></i>
            <span class="cg-label">Ngày đặt:</span>
            <span class="cg-value">31/10/2025</span>
          </div>
          <div class="cg-info-row">
            <i class="fas fa-clock"></i>
            <span class="cg-label">Thời gian:</span>
            <span class="cg-value">08:00 - 17:00</span>
          </div>
          <div class="cg-info-row">
            <i class="fas fa-map-marker-alt"></i>
            <span class="cg-label">Địa chỉ:</span>
            <span class="cg-value">123 Đường ABC, Quận 1, TP.HCM</span>
          </div>
          <div class="cg-info-row cg-price">
            <i class="fas fa-money-bill-wave"></i>
            <span class="cg-label">Tổng tiền:</span>
            <span class="cg-value cg-price-value">500.000 ₫</span>
          </div>
        </div>
        <div class="cg-card-footer">
          <button class="cg-btn cg-btn-primary" onclick="cancelOrder(1)">
            <i class="fas fa-times"></i> Huỷ đơn
          </button>
          <button class="cg-btn cg-btn-ghost" onclick="viewDetails(1)">
            <i class="fas fa-eye"></i> Chi tiết
          </button>
        </div>
      </div>

      <!-- Card đơn hàng mẫu 2 -->
      <div class="cg-card" data-order-id="2">
        <div class="cg-card-header">
          <h3>Đơn hàng #DH002</h3>
          <span class="cg-status cg-status-pending">ĐÃ XÁC NHẬN</span>
        </div>
        <div class="cg-card-body">
          <div class="cg-info-row">
            <i class="fas fa-user"></i>
            <span class="cg-label">Khách hàng:</span>
            <span class="cg-value">Trần Thị B</span>
          </div>
          <div class="cg-info-row">
            <i class="fas fa-phone"></i>
            <span class="cg-label">SĐT:</span>
            <span class="cg-value">0987654321</span>
          </div>
          <div class="cg-info-row">
            <i class="fas fa-calendar"></i>
            <span class="cg-label">Ngày đặt:</span>
            <span class="cg-value">30/10/2025</span>
          </div>
          <div class="cg-info-row">
            <i class="fas fa-clock"></i>
            <span class="cg-label">Thời gian:</span>
            <span class="cg-value">09:00 - 18:00</span>
          </div>
          <div class="cg-info-row">
            <i class="fas fa-map-marker-alt"></i>
            <span class="cg-label">Địa chỉ:</span>
            <span class="cg-value">456 Đường XYZ, Quận 3, TP.HCM</span>
          </div>
          <div class="cg-info-row cg-price">
            <i class="fas fa-money-bill-wave"></i>
            <span class="cg-label">Tổng tiền:</span>
            <span class="cg-value cg-price-value">650.000 ₫</span>
          </div>
        </div>
        <div class="cg-card-footer">
          <button class="cg-btn cg-btn-primary" onclick="cancelOrder(2)">
            <i class="fas fa-times"></i> Huỷ đơn
          </button>
          <button class="cg-btn cg-btn-ghost" onclick="viewDetails(2)">
            <i class="fas fa-eye"></i> Chi tiết
          </button>
        </div>
      </div>

      <!-- Card đơn hàng mẫu 3 -->
      <div class="cg-card" data-order-id="3">
        <div class="cg-card-header">
          <h3>Đơn hàng #DH003</h3>
          <span class="cg-status cg-status-pending">ĐÃ XÁC NHẬN</span>
        </div>
        <div class="cg-card-body">
          <div class="cg-info-row">
            <i class="fas fa-user"></i>
            <span class="cg-label">Khách hàng:</span>
            <span class="cg-value">Lê Văn C</span>
          </div>
          <div class="cg-info-row">
            <i class="fas fa-phone"></i>
            <span class="cg-label">SĐT:</span>
            <span class="cg-value">0912345678</span>
          </div>
          <div class="cg-info-row">
            <i class="fas fa-calendar"></i>
            <span class="cg-label">Ngày đặt:</span>
            <span class="cg-value">29/10/2025</span>
          </div>
          <div class="cg-info-row">
            <i class="fas fa-clock"></i>
            <span class="cg-label">Thời gian:</span>
            <span class="cg-value">07:00 - 16:00</span>
          </div>
          <div class="cg-info-row">
            <i class="fas fa-map-marker-alt"></i>
            <span class="cg-label">Địa chỉ:</span>
            <span class="cg-value">789 Đường DEF, Quận 5, TP.HCM</span>
          </div>
          <div class="cg-info-row cg-price">
            <i class="fas fa-money-bill-wave"></i>
            <span class="cg-label">Tổng tiền:</span>
            <span class="cg-value cg-price-value">800.000 ₫</span>
          </div>
        </div>
        <div class="cg-card-footer">
          <button class="cg-btn cg-btn-primary" onclick="cancelOrder(3)">
            <i class="fas fa-times"></i> Huỷ đơn
          </button>
          <button class="cg-btn cg-btn-ghost" onclick="viewDetails(3)">
            <i class="fas fa-eye"></i> Chi tiết
          </button>
        </div>
      </div>
    </section>
  </main>

  <script>
    // Cập nhật summary khi trang load
    document.addEventListener('DOMContentLoaded', function() {
      updateSummary();
    });

    // Hàm cập nhật tóm tắt số lượng đơn hàng
    function updateSummary() {
      const cards = document.querySelectorAll('.cg-card[data-order-id]');
      const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
      const summaryLine = document.getElementById('summaryLine');
      
      if (summaryLine) {
        summaryLine.textContent = `Tổng đơn hàng: ${visibleCards.length}`;
      }

      // Kiểm tra empty state
      const cgList = document.getElementById('cg-list');
      const emptyState = cgList.querySelector('.cg-empty');
      
      if (visibleCards.length === 0) {
        if (emptyState) {
          emptyState.style.display = 'block';
        }
      } else {
        if (emptyState) {
          emptyState.style.display = 'none';
        }
      }
    }

    // Xử lý tìm kiếm
    document.getElementById('cg-filter').addEventListener('submit', function(e) {
      e.preventDefault();
      
      const query = document.getElementById('q').value.toLowerCase().trim();
      const cards = document.querySelectorAll('.cg-card[data-order-id]');
      
      cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        if (query === '' || text.includes(query)) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
      
      updateSummary();
    });

    // Làm mới/Reset filter
    document.getElementById('resetBtn').addEventListener('click', function() {
      document.getElementById('q').value = '';
      
      const cards = document.querySelectorAll('.cg-card[data-order-id]');
      cards.forEach(card => {
        card.style.display = 'flex';
      });
      
      updateSummary();
    });

    // Hàm hủy đơn hàng
    function cancelOrder(orderId) {
      if (!confirm('Bạn có chắc chắn muốn hủy đơn hàng này?')) {
        return;
      }
      
      // Gửi AJAX request để cập nhật trạng thái
      fetch('donhangdanhan.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({
          action: 'cancel',
          order_id: orderId
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Đã hủy đơn hàng thành công');
          location.reload();
        } else {
          alert('Lỗi: ' + (data.message || 'Không thể hủy đơn'));
        }
      })
      .catch(error => {
        alert('Lỗi kết nối: ' + error.message);
      });
    }

    // Hàm xem chi tiết đơn hàng
    function viewDetails(orderId) {
      window.location.href = `Chitietdonhang.php?id=${orderId}`;
    }

    // Xử lý phân trang
    let currentPage = 1;
    const itemsPerPage = 12;

    function updatePagination() {
      const cards = document.querySelectorAll('.cg-card[data-order-id]');
      const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
      const totalPages = Math.ceil(visibleCards.length / itemsPerPage);
      
      document.getElementById('pageInfo').textContent = `Trang ${currentPage}/${totalPages || 1}`;
      
      const prevBtn = document.getElementById('prevBtn');
      const nextBtn = document.getElementById('nextBtn');
      
      prevBtn.disabled = currentPage === 1;
      nextBtn.disabled = currentPage === totalPages || totalPages === 0;
    }

    document.getElementById('prevBtn').addEventListener('click', function() {
      if (currentPage > 1) {
        currentPage--;
        updatePagination();
      }
    });

    document.getElementById('nextBtn').addEventListener('click', function() {
      const cards = document.querySelectorAll('.cg-card[data-order-id]');
      const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
      const totalPages = Math.ceil(visibleCards.length / itemsPerPage);
      
      if (currentPage < totalPages) {
        currentPage++;
        updatePagination();
      }
    });

    // Initialize pagination
    updatePagination();
  </script>
</body>
</html>
