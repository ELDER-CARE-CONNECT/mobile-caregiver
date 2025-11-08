

<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Chi tiết đơn hàng</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      padding: 20px;
    }

    .container {
      max-width: 800px;
      margin: 0 auto;
    }

    .back {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: white;
      text-decoration: none;
      font-weight: 600;
      margin-bottom: 20px;
      padding: 10px 15px;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .back:hover {
      background: rgba(255, 255, 255, 0.1);
      transform: translateX(-5px);
    }

    .card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      overflow: hidden;
    }

    .card-hd {
      padding: 30px;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .card-hd h2 {
      margin: 0;
      font-size: 28px;
      font-weight: 700;
    }

    .badge {
      padding: 8px 16px;
      border-radius: 20px;
      font-size: 13px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      background: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
    }

    .card-bd {
      padding: 30px;
    }

    /* Modal styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.5);
      animation: fadeIn 0.3s ease;
    }

    .modal.show {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .modal-content {
      background: white;
      border-radius: 15px;
      box-shadow: 0 20px 60px rgba(0,0,0,0.3);
      max-width: 600px;
      width: 90%;
      max-height: 80vh;
      overflow-y: auto;
    }

    .modal-header {
      padding: 25px 30px 20px;
      border-bottom: 1px solid #e5e7eb;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .modal-title {
      font-size: 24px;
      font-weight: 700;
      color: #1f2937;
      margin: 0;
    }

    .modal-close {
      background: none;
      border: none;
      font-size: 24px;
      color: #6b7280;
      cursor: pointer;
      padding: 5px;
      border-radius: 50%;
      width: 35px;
      height: 35px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.2s ease;
    }

    .modal-close:hover {
      background: #f3f4f6;
      color: #374151;
    }

    .modal-body {
      padding: 30px;
    }

    .order-detail-section {
      margin-bottom: 25px;
    }

    .order-detail-section h4 {
      font-size: 18px;
      font-weight: 600;
      color: #374151;
      margin: 0 0 15px;
      padding-bottom: 8px;
      border-bottom: 2px solid #e5e7eb;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .detail-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 15px;
    }

    .detail-item-modal {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 12px;
      background: #f8f9fa;
      border-radius: 8px;
    }

    .detail-item-modal i {
      width: 20px;
      color: #667eea;
      text-align: center;
    }

    .detail-label-modal {
      font-weight: 600;
      color: #374151;
      min-width: 100px;
    }

    .detail-value-modal {
      color: #1f2937;
      flex: 1;
    }

    .customer-info-modal {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 20px;
      background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
      border-radius: 10px;
      margin-bottom: 20px;
    }

    .customer-avatar-modal {
      width: 70px;
      height: 70px;
      border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 28px;
      font-weight: bold;
      flex-shrink: 0;
    }

    .customer-details-modal h3 {
      margin: 0 0 8px;
      font-size: 20px;
      font-weight: 700;
      color: #1f2937;
    }

    .customer-details-modal p {
      margin: 0 0 4px;
      color: #6b7280;
      font-size: 14px;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .customer-details-modal p i {
      width: 16px;
      text-align: center;
    }

    .sum {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 20px 25px;
      border-radius: 12px;
      font-size: 24px;
      font-weight: 700;
      text-align: center;
      width: 100%;
      margin-top: 20px;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideIn {
      from { 
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
      }
      to { 
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    @media (max-width: 768px) {
      .detail-grid {
        grid-template-columns: 1fr;
      }
      
      .customer-info-modal {
        flex-direction: column;
        text-align: center;
      }

      .card-hd {
        flex-direction: column;
        gap: 15px;
        text-align: center;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <a class="back" href="javascript:void(0)" onclick="goBack()">
      <i class="fas fa-arrow-left"></i> Quay lại
    </a>

    <div class="card">
      <div class="card-hd">
        <h2>Chi tiết đơn hàng #DH001</h2>
        <span class="badge">Chờ xác nhận</span>
      </div>
      <div class="card-bd">
        <!-- Thông tin khách hàng -->
        <div class="customer-info-modal">
          <div class="customer-avatar-modal">NT</div>
          <div class="customer-details-modal">
            <h3>Nguyễn Văn A</h3>
            <p><i class="fas fa-phone"></i> 0123456789</p>
            <p><i class="fas fa-envelope"></i> nguyenvana@email.com</p>
            <p><i class="fas fa-map-marker-alt"></i> 123 Đường ABC, Quận 1, TP.HCM</p>
          </div>
        </div>

        <!-- Thông tin đơn hàng -->
        <div class="order-detail-section">
          <h4><i class="fas fa-info-circle"></i> Thông tin đơn hàng</h4>
          <div class="detail-grid">
            <div class="detail-item-modal">
              <i class="fas fa-hashtag"></i>
              <span class="detail-label-modal">Mã đơn:</span>
              <span class="detail-value-modal">#DH001</span>
            </div>
            <div class="detail-item-modal">
              <i class="fas fa-calendar"></i>
              <span class="detail-label-modal">Ngày đặt:</span>
              <span class="detail-value-modal">31/10/2025</span>
            </div>
            <div class="detail-item-modal">
              <i class="fas fa-clock"></i>
              <span class="detail-label-modal">Thời gian:</span>
              <span class="detail-value-modal">08:00 - 17:00</span>
            </div>
            <div class="detail-item-modal">
              <i class="fas fa-user-nurse"></i>
              <span class="detail-label-modal">Người chăm sóc:</span>
              <span class="detail-value-modal">Chưa phân công</span>
            </div>
            <div class="detail-item-modal">
              <i class="fas fa-concierge-bell"></i>
              <span class="detail-label-modal">Dịch vụ:</span>
              <span class="detail-value-modal">Chăm sóc tại nhà</span>
            </div>
            <div class="detail-item-modal">
              <i class="fas fa-credit-card"></i>
              <span class="detail-label-modal">Thanh toán:</span>
              <span class="detail-value-modal">Tiền mặt</span>
            </div>
          </div>
        </div>

        <!-- Trạng thái đơn hàng -->
        <div class="order-detail-section">
          <h4><i class="fas fa-tasks"></i> Trạng thái đơn hàng</h4>
          <div class="detail-item-modal">
            <i class="fas fa-clock"></i>
            <span class="detail-label-modal">Trạng thái:</span>
            <span class="detail-value-modal">
              <span class="badge">Chờ xác nhận</span>
            </span>
          </div>
        </div>

        <!-- Tổng tiền -->
        <div class="sum">
          Tổng tiền: 500.000 ₫
        </div>
      </div>
    </div>
  </div>

  <script>
    function goBack() {
      history.back();
    }

    function closeModal() {
      document.getElementById('orderModal').classList.remove('show');
      document.body.style.overflow = 'auto';
    }

    // Đóng modal khi click bên ngoài
    window.onclick = function(event) {
      const modal = document.getElementById('orderModal');
      if (event.target == modal) {
        closeModal();
      }
    }

    // Đóng modal với phím ESC
    document.addEventListener('keydown', function(event) {
      if (event.key === 'Escape') {
        closeModal();
      }
    });
  </script>
</body>
</html>


