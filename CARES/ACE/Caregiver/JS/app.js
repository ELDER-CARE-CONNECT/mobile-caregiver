// Dữ liệu mẫu cho đơn hàng
const sampleOrders = [
  {
    id: "DV001",
    date: "2025-10-20",
    customer: "Nguyễn Minh Thư",
    caregiver: "Trần Lan Vy",
    service: "Chăm sóc tại nhà",
    time: "09:00 - 17:00",
    price: 450000,
    status: "completed",
    details: {
      address: "123 Đường ABC, Quận 1, TP.HCM",
      phone: "0123456789",
      notes: "Chăm sóc người già, cần hỗ trợ đi lại"
    }
  },
  {
    id: "DV002",
    date: "2025-10-22",
    customer: "Phạm Hòa",
    caregiver: "Ngô Thành",
    service: "Vật lý trị liệu",
    time: "14:30 - 16:30",
    price: 300000,
    status: "pending",
    details: {
      address: "456 Đường XYZ, Quận 2, TP.HCM",
      phone: "0987654321",
      notes: "Tập phục hồi chức năng sau tai biến"
    }
  },
  {
    id: "DV003",
    date: "2025-10-18",
    customer: "Vũ Thịnh",
    caregiver: "Lê Trí",
    service: "Chăm sóc y tế",
    time: "08:00 - 12:00",
    price: 500000,
    status: "cancelled",
    details: {
      address: "789 Đường DEF, Quận 3, TP.HCM",
      phone: "0369258147",
      notes: "Hủy do lý do cá nhân"
    }
  },
  {
    id: "DV004",
    date: "2025-10-23",
    customer: "Đặng Phong",
    caregiver: "Bích Chi",
    service: "Tư vấn dinh dưỡng",
    time: "10:15 - 11:15",
    price: 350000,
    status: "completed",
    details: {
      address: "321 Đường GHI, Quận 4, TP.HCM",
      phone: "0741852963",
      notes: "Tư vấn chế độ ăn cho người tiểu đường"
    }
  },
  {
    id: "DV005",
    date: "2025-10-24",
    customer: "Nguyễn An",
    caregiver: "Hoàng Mai",
    service: "Chăm sóc tâm lý",
    time: "15:45 - 17:45",
    price: 400000,
    status: "pending",
    details: {
      address: "654 Đường JKL, Quận 5, TP.HCM",
      phone: "0852741963",
      notes: "Hỗ trợ tâm lý cho người cao tuổi"
    }
    }
  ];
  
  // Cấu hình phân trang
const ITEMS_PER_PAGE = 10;
let currentPage = 1;
let filteredOrders = [...sampleOrders];
let currentFilter = 'all';

// DOM Elements
const elements = {
  searchInput: document.getElementById('searchInput'),
  dateFrom: document.getElementById('dateFrom'),
  dateTo: document.getElementById('dateTo'),
  searchBtn: document.getElementById('searchBtn'),
  resetBtn: document.getElementById('resetBtn'),
  tabBtns: document.querySelectorAll('.tab-btn'),
  ordersTableBody: document.getElementById('ordersTableBody'),
  totalOrders: document.getElementById('totalOrders'),
  completedOrders: document.getElementById('completedOrders'),
  pendingOrders: document.getElementById('pendingOrders'),
  totalRevenue: document.getElementById('totalRevenue'),
  prevBtn: document.getElementById('prevBtn'),
  nextBtn: document.getElementById('nextBtn'),
  pageInfo: document.getElementById('pageInfo'),
  orderModal: document.getElementById('orderModal'),
  closeModal: document.getElementById('closeModal'),
  modalBody: document.getElementById('modalBody'),
  orderRowTemplate: document.getElementById('orderRowTemplate')
};

// Utility Functions
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND'
  }).format(amount);
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleDateString('vi-VN');
};

const getStatusText = (status) => {
  const statusMap = {
    completed: 'Hoàn thành',
    pending: 'Đang xử lý',
    cancelled: 'Đã hủy'
  };
  return statusMap[status] || status;
};

const getStatusClass = (status) => {
  return status;
};

// Filter Functions
const filterOrders = () => {
  let filtered = [...sampleOrders];
  
  // Filter by status
  if (currentFilter !== 'all') {
    filtered = filtered.filter(order => order.status === currentFilter);
  }
  
  // Filter by search term
  const searchTerm = elements.searchInput.value.toLowerCase();
  if (searchTerm) {
    filtered = filtered.filter(order => 
      order.id.toLowerCase().includes(searchTerm) ||
      order.customer.toLowerCase().includes(searchTerm) ||
      order.caregiver.toLowerCase().includes(searchTerm) ||
      order.service.toLowerCase().includes(searchTerm)
    );
  }
  
  // Filter by date range
  const dateFrom = elements.dateFrom.value;
  const dateTo = elements.dateTo.value;
  
  if (dateFrom) {
    filtered = filtered.filter(order => order.date >= dateFrom);
  }
  
  if (dateTo) {
    filtered = filtered.filter(order => order.date <= dateTo);
  }
  
  filteredOrders = filtered;
  currentPage = 1;
  updateDisplay();
};

// Update Statistics
const updateStatistics = () => {
  const total = filteredOrders.length;
  const completed = filteredOrders.filter(order => order.status === 'completed').length;
  const pending = filteredOrders.filter(order => order.status === 'pending').length;
  const revenue = filteredOrders
    .filter(order => order.status === 'completed')
    .reduce((sum, order) => sum + order.price, 0);
  
  elements.totalOrders.textContent = total;
  elements.completedOrders.textContent = completed;
  elements.pendingOrders.textContent = pending;
  elements.totalRevenue.textContent = formatCurrency(revenue);
};

// Render Orders Table
const renderOrdersTable = () => {
  const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
  const endIndex = startIndex + ITEMS_PER_PAGE;
  const pageOrders = filteredOrders.slice(startIndex, endIndex);
  
  elements.ordersTableBody.innerHTML = '';
  
  if (pageOrders.length === 0) {
    elements.ordersTableBody.innerHTML = `
      <tr>
        <td colspan="9" class="empty-state">
          <i class="fas fa-clipboard-list"></i>
          <h3>Không có đơn hàng nào</h3>
          <p>Không tìm thấy đơn hàng phù hợp với bộ lọc hiện tại</p>
        </td>
      </tr>
    `;
    return;
  }
  
  pageOrders.forEach(order => {
    const row = elements.orderRowTemplate.content.cloneNode(true);
    
    row.querySelector('.order-id').textContent = order.id;
    row.querySelector('.order-date').textContent = formatDate(order.date);
    row.querySelector('.customer-name').textContent = order.customer;
    row.querySelector('.caregiver-name').textContent = order.caregiver;
    row.querySelector('.service-type').textContent = order.service;
    row.querySelector('.service-time').textContent = order.time;
    row.querySelector('.order-price').textContent = formatCurrency(order.price);
    
    const statusBadge = row.querySelector('.status-badge');
    statusBadge.textContent = getStatusText(order.status);
    statusBadge.classList.add(getStatusClass(order.status));
    
    // Add event listeners for action buttons
    const viewBtn = row.querySelector('.btn-view');
    const editBtn = row.querySelector('.btn-edit');
    const cancelBtn = row.querySelector('.btn-cancel');
    
    viewBtn.addEventListener('click', () => showOrderDetails(order));
    editBtn.addEventListener('click', () => editOrder(order));
    cancelBtn.addEventListener('click', () => cancelOrder(order));
    
    elements.ordersTableBody.appendChild(row);
  });
};

// Update Pagination
const updatePagination = () => {
  const totalPages = Math.ceil(filteredOrders.length / ITEMS_PER_PAGE);
  
  elements.pageInfo.textContent = `Trang ${currentPage}/${totalPages}`;
  elements.prevBtn.disabled = currentPage === 1;
  elements.nextBtn.disabled = currentPage === totalPages || totalPages === 0;
};

// Update Display
const updateDisplay = () => {
  updateStatistics();
  renderOrdersTable();
  updatePagination();
};

// Show Order Details Modal
const showOrderDetails = (order) => {
  elements.modalBody.innerHTML = `
    <div class="order-details">
      <div class="detail-section">
        <h3>Thông tin đơn hàng</h3>
        <div class="detail-grid">
          <div class="detail-item">
            <label>Mã đơn hàng:</label>
            <span>${order.id}</span>
          </div>
          <div class="detail-item">
            <label>Ngày đặt:</label>
            <span>${formatDate(order.date)}</span>
          </div>
          <div class="detail-item">
            <label>Trạng thái:</label>
            <span class="status-badge ${getStatusClass(order.status)}">${getStatusText(order.status)}</span>
          </div>
          <div class="detail-item">
            <label>Giá tiền:</label>
            <span>${formatCurrency(order.price)}</span>
          </div>
        </div>
      </div>
      
      <div class="detail-section">
        <h3>Thông tin khách hàng</h3>
        <div class="detail-grid">
          <div class="detail-item">
            <label>Tên khách hàng:</label>
            <span>${order.customer}</span>
          </div>
          <div class="detail-item">
            <label>Số điện thoại:</label>
            <span>${order.details.phone}</span>
          </div>
          <div class="detail-item">
            <label>Địa chỉ:</label>
            <span>${order.details.address}</span>
          </div>
        </div>
      </div>
      
      <div class="detail-section">
        <h3>Thông tin dịch vụ</h3>
        <div class="detail-grid">
          <div class="detail-item">
            <label>Người chăm sóc:</label>
            <span>${order.caregiver}</span>
          </div>
          <div class="detail-item">
            <label>Loại dịch vụ:</label>
            <span>${order.service}</span>
          </div>
          <div class="detail-item">
            <label>Thời gian:</label>
            <span>${order.time}</span>
          </div>
          <div class="detail-item">
            <label>Ghi chú:</label>
            <span>${order.details.notes}</span>
          </div>
        </div>
      </div>
    </div>
  `;
  
  elements.orderModal.style.display = 'block';
};

// Edit Order
const editOrder = (order) => {
  alert(`Chỉnh sửa đơn hàng ${order.id}`);
  // Implement edit functionality
};

// Cancel Order
const cancelOrder = (order) => {
  if (confirm(`Bạn có chắc chắn muốn hủy đơn hàng ${order.id}?`)) {
    order.status = 'cancelled';
    updateDisplay();
    alert('Đã hủy đơn hàng thành công!');
  }
};

// Event Listeners
const initializeEventListeners = () => {
  // Search and filter
  elements.searchBtn.addEventListener('click', filterOrders);
  elements.resetBtn.addEventListener('click', () => {
    elements.searchInput.value = '';
    elements.dateFrom.value = '';
    elements.dateTo.value = '';
    currentFilter = 'all';
    document.querySelector('.tab-btn.active').classList.remove('active');
    document.querySelector('[data-status="all"]').classList.add('active');
    filterOrders();
  });
  
  // Tab filters
  elements.tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelector('.tab-btn.active').classList.remove('active');
      btn.classList.add('active');
      currentFilter = btn.dataset.status;
      filterOrders();
    });
  });
  
  // Pagination
  elements.prevBtn.addEventListener('click', () => {
    if (currentPage > 1) {
      currentPage--;
      updateDisplay();
    }
  });
  
  elements.nextBtn.addEventListener('click', () => {
    const totalPages = Math.ceil(filteredOrders.length / ITEMS_PER_PAGE);
    if (currentPage < totalPages) {
      currentPage++;
      updateDisplay();
    }
  });
  
  // Modal
  elements.closeModal.addEventListener('click', () => {
    elements.orderModal.style.display = 'none';
  });
  
  elements.orderModal.addEventListener('click', (e) => {
    if (e.target === elements.orderModal) {
      elements.orderModal.style.display = 'none';
    }
  });
  
  // Search on Enter
  elements.searchInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
      filterOrders();
    }
  });
  
  // Date change
  elements.dateFrom.addEventListener('change', filterOrders);
  elements.dateTo.addEventListener('change', filterOrders);
};

// Export Functions
const exportToExcel = () => {
  alert('Tính năng xuất Excel đang được phát triển');
};

const printReport = () => {
  window.print();
};

// Initialize App
const initializeApp = () => {
  initializeEventListeners();
  updateDisplay();
  
  // Set default date range (last 30 days)
  const today = new Date();
  const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));
  
  elements.dateFrom.value = thirtyDaysAgo.toISOString().split('T')[0];
  elements.dateTo.value = today.toISOString().split('T')[0];
  
  // Add export/print button listeners
  document.querySelector('.btn-export')?.addEventListener('click', exportToExcel);
  document.querySelector('.btn-print')?.addEventListener('click', printReport);
};

// Start the application
document.addEventListener('DOMContentLoaded', initializeApp);

// Add CSS for modal details
const style = document.createElement('style');
style.textContent = `
  .order-details {
    display: flex;
    flex-direction: column;
    gap: 2rem;
  }
  
  .detail-section h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--primary-color);
  }
  
  .detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
  }
  
  .detail-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
  }
  
  .detail-item label {
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 0.875rem;
  }
  
  .detail-item span {
    color: var(--text-primary);
    font-size: 1rem;
  }
`;
document.head.appendChild(style);
