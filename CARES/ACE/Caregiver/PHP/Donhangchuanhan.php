<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <title>Đơn Hàng Chưa Nhận - Caregiver</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <style>
    :root{
      --xanh:#0b5ed7; 
      --xanh-nhat:#eaf2ff; 
      --vien:#e7e9ef; 
      --chu:#1a1d29;
    }
    *{box-sizing:border-box} html,body{height:100%}
    body{
      margin:0; font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;
      color:var(--chu); background:#fff; display:grid; grid-template-columns:260px 1fr; min-height:100vh;
    }

    /* Sidebar (để sẵn – nếu không có sidebar sẽ tự 1 cột nhờ BRIDGE ở dưới) */
    .sidebar{background:var(--xanh); color:#fff; padding:22px 18px; display:flex; flex-direction:column; gap:20px}
    .logo{display:flex; align-items:center; gap:10px}
    .logo .hinh{width:36px; height:36px; border-radius:10px; background:#ffffff22; display:inline-block}
    .logo h2{margin:0; font-size:18px; line-height:1.2}
    .menu{display:flex; flex-direction:column; gap:6px}
    .menu a{color:#fff; text-decoration:none; padding:10px 12px; border-radius:10px; opacity:.95; display:flex; align-items:center; gap:10px}
    .menu a:hover,.menu a.active{background:#ffffff22; opacity:1}

    /* Nội dung chính */
    .wrap{display:flex; flex-direction:column}
    header{padding:14px 20px; border-bottom:1px solid var(--vien); background:#fff; position:sticky; top:0; z-index:5}
    header h1{margin:0; font-size:20px}
    header p{margin:4px 0 0 0; color:#5b6070; font-size:14px}

    /* ===== Bộ lọc / thanh công cụ ===== */
    .cg-filters{
      display:flex; flex-wrap:wrap; gap:12px 16px;
      padding:14px 20px; background:var(--xanh-nhat); border-bottom:1px solid var(--vien);
    }
    .cg-filters label{display:flex; flex-direction:column; gap:6px; font-size:14px}

    /* Thanh tìm kiếm mới */
    .cg-search{ flex:1; min-width:280px; position:relative; }
    .cg-search input[type="text"]{
      width:100%; padding:10px 14px 10px 38px;
      border:1px solid var(--vien); border-radius:10px; background:#fff;
    }
    .cg-search i{
      position:absolute; left:12px; top:50%; transform:translateY(-50%); pointer-events:none; color:#8b90a0;
    }
    /* Fix canh icon trong ô tìm kiếm */
.cg-search{ flex:1; min-width:280px; }
.cg-search .field{ position:relative; }

.cg-search .field i{
  position:absolute;
  left:12px;
  top:50%;
  transform:translateY(-50%);
  color:#8b90a0;
  pointer-events:none;
}

.cg-search .field input{
  width:100%;
  height:42px;                 /* đảm bảo chiều cao cố định */
  padding:10px 14px 10px 38px; /* chừa chỗ cho icon */
  border:1px solid var(--vien);
  border-radius:10px;
  background:#fff;
  line-height:1.2;
}

    .cg-actions{ display:flex; align-items:flex-end; gap:15px; margin-left:auto }
    .cg-btn{
      display:inline-flex; align-items:center; gap:8px; padding:10px 14px; border-radius:10px; cursor:pointer;
      border:1px solid transparent; font-size:14px; line-height:1;
    }
    .cg-btn i{font-size:14px}
    .cg-btn-primary{ background:var(--xanh); color:#fff }
    .cg-btn-primary:hover{ filter:brightness(.95) }
    .cg-btn-ghost{ background:#fff; color:#1a1d29; border:1px solid var(--vien) }
    .cg-btn-ghost:hover{ background:#f7f8fc }

    /* Topbar & phân trang */
    .cg-topbar{ display:flex; align-items:center; justify-content:space-between; padding:12px 20px; color:#485063; font-size:14px }
    .cg-muted{ color:#5b6070 }
    .cg-pager{ display:flex; align-items:center; gap:10px }
    .cg-pagebtn{
      padding:8px 12px; border:1px solid var(--vien); background:#fff; border-radius:10px; cursor:pointer; font-size:14px; display:inline-flex; align-items:center; gap:8px
    }
    .cg-pagebtn:disabled{ opacity:.5; cursor:not-allowed }
    .cg-pagebtn:hover:not(:disabled){ background:#f7f8fc }

    /* Lưới thẻ đơn */
    .cg-grid{ padding:18px 20px; display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:16px }
    .cg-card{ border:1px solid var(--vien); background:#fff; border-radius:12px; overflow:hidden; display:flex; flex-direction:column }
    .cg-card-header{ display:flex; align-items:center; justify-content:space-between; padding:12px 14px; border-bottom:1px solid var(--vien); background:#f7f8fc }
    .cg-card-header h3{ margin:0; font-size:16px }
    .cg-status{ padding:4px 10px; border-radius:999px; font-size:12px; border:1px solid transparent; display:inline-block }
    .cg-status-pending{ background:#fff9e6; color:#a15c00; border-color:#ffe6a1 }
    .cg-card-body{ padding:12px 14px; display:grid; gap:10px }
    .cg-info-row{ display:grid; grid-template-columns:20px 110px 1fr; align-items:start; gap:8px; font-size:14px }
    .cg-info-row i{ margin-top:2px }
    .cg-label{ color:#5b6070 }
    .cg-value{ color:#1a1d29; word-break:break-word }
    .cg-price .cg-value{ font-weight:700 }
    .cg-card-footer{ display:flex; gap:10px; padding:12px 14px; border-top:1px solid var(--vien) }

    /* Trạng thái rỗng & lỗi */
    .cg-empty,.cg-error{
      border:1px dashed var(--vien); border-radius:12px; padding:24px; text-align:center; color:#5b6070; background:#fff; grid-column:1 / -1
    }
    .cg-empty i,.cg-error i{ font-size:28px; margin-bottom:8px; display:block }

    /* BRIDGE: nếu không có sidebar thì bỏ layout 2 cột */
    body:has(.cg-wrap):not(:has(.sidebar)){ display:block; grid-template-columns:unset }

    /* Mobile */
    @media (max-width:600px){
      .cg-actions{ width:100%; justify-content:flex-end }
    }
  </style>
</head>

<body>
  <main class="cg-wrap">
    <header class="cg-header">
      <h1>Đơn Hàng Đang Chờ Nhận</h1>
      <p>Hiển thị tất cả đơn ở trạng thái <b>chờ xác nhận / chưa gán người chăm sóc</b></p>
    </header>

    <!-- Thanh tìm kiếm mới -->
    <form id="cg-filter" class="cg-filters">
      <div class="cg-search">
  <label for="q">Tìm kiếm</label>
  <div class="field">
    <i class="fas fa-search" aria-hidden="true"></i>
    <input id="q" type="text" placeholder="Mã đơn, tên khách hàng, SĐT, email">
  </div>
</div>
      <div class="cg-actions">
        <button type="submit" class="cg-btn cg-btn-primary"><i class="fas fa-magnifying-glass"></i> Tìm kiếm</button>
        <button type="button" id="resetBtn" class="cg-btn cg-btn-ghost"><i class="fas fa-rotate"></i> Làm mới</button>
      </div>
    </form>

    <div class="cg-topbar">
      <span id="summaryLine" class="cg-muted">Tổng đơn hàng: 18</span>
      <div class="cg-pager">
        <button id="prevBtn" class="cg-pagebtn" disabled><i class="fas fa-chevron-left"></i> Trước</button>
        <span id="pageInfo" class="cg-muted">Trang 1/2</span>
        <button id="nextBtn" class="cg-pagebtn">Sau <i class="fas fa-chevron-right"></i></button>
      </div>
    </div>

    <!-- Danh sách thẻ giả lập -->
    <section id="cg-list" class="cg-grid">
      <!-- Ví dụ 1 -->
      <div class="cg-card" data-order-id="42">
        <div class="cg-card-header">
          <h3>Đơn hàng #42</h3>
          <span class="cg-status cg-status-pending">CHỜ XÁC NHẬN</span>
        </div>
        <div class="cg-card-body">
          <div class="cg-info-row"><i class="fas fa-user"></i><span class="cg-label">Khách hàng:</span><span class="cg-value">Gia An</span></div>
          <div class="cg-info-row"><i class="fas fa-phone"></i><span class="cg-label">SĐT:</span><span class="cg-value">334290589</span></div>
          <div class="cg-info-row"><i class="fas fa-calendar"></i><span class="cg-label">Ngày đặt:</span><span class="cg-value">06/05/2025</span></div>
          <div class="cg-info-row"><i class="fas fa-clock"></i><span class="cg-label">Thời gian:</span><span class="cg-value">00:00 - 00:00</span></div>
          <div class="cg-info-row"><i class="fas fa-map-marker-alt"></i><span class="cg-label">Địa chỉ:</span><span class="cg-value">asdiasdias</span></div>
          <div class="cg-info-row cg-price"><i class="fas fa-money-bill-wave"></i><span class="cg-label">Tổng tiền:</span><span class="cg-value cg-price-value">20 VNĐ</span></div>
        </div>
        <div class="cg-card-footer">
          <button class="cg-btn cg-btn-primary"><i class="fas fa-handshake"></i> Nhận đơn</button>
          <button class="cg-btn cg-btn-ghost"><i class="fas fa-eye"></i> Chi tiết</button>
        </div>
      </div>

      <!-- Ví dụ 2 -->
      <div class="cg-card" data-order-id="41">
        <div class="cg-card-header">
          <h3>Đơn hàng #41</h3>
          <span class="cg-status cg-status-pending">CHỜ XÁC NHẬN</span>
        </div>
        <div class="cg-card-body">
          <div class="cg-info-row"><i class="fas fa-user"></i><span class="cg-label">Khách hàng:</span><span class="cg-value">Gia An</span></div>
          <div class="cg-info-row"><i class="fas fa-phone"></i><span class="cg-label">SĐT:</span><span class="cg-value">334290562</span></div>
          <div class="cg-info-row"><i class="fas fa-calendar"></i><span class="cg-label">Ngày đặt:</span><span class="cg-value">05/05/2025</span></div>
          <div class="cg-info-row"><i class="fas fa-clock"></i><span class="cg-label">Thời gian:</span><span class="cg-value">00:00 - 00:00</span></div>
          <div class="cg-info-row"><i class="fas fa-map-marker-alt"></i><span class="cg-label">Địa chỉ:</span><span class="cg-value">0</span></div>
          <div class="cg-info-row cg-price"><i class="fas fa-money-bill-wave"></i><span class="cg-label">Tổng tiền:</span><span class="cg-value cg-price-value">160 VNĐ</span></div>
        </div>
        <div class="cg-card-footer">
          <button class="cg-btn cg-btn-primary"><i class="fas fa-handshake"></i> Nhận đơn</button>
          <button class="cg-btn cg-btn-ghost"><i class="fas fa-eye"></i> Chi tiết</button>
        </div>
      </div>

      <!-- Ví dụ 3 -->
      <div class="cg-card" data-order-id="40">
        <div class="cg-card-header">
          <h3>Đơn hàng #40</h3>
          <span class="cg-status cg-status-pending">CHỜ XÁC NHẬN</span>
        </div>
        <div class="cg-card-body">
          <div class="cg-info-row"><i class="fas fa-user"></i><span class="cg-label">Khách hàng:</span><span class="cg-value">Minh Thư</span></div>
          <div class="cg-info-row"><i class="fas fa-phone"></i><span class="cg-label">SĐT:</span><span class="cg-value">0978 555 222</span></div>
          <div class="cg-info-row"><i class="fas fa-calendar"></i><span class="cg-label">Ngày đặt:</span><span class="cg-value">04/05/2025</span></div>
          <div class="cg-info-row"><i class="fas fa-clock"></i><span class="cg-label">Thời gian:</span><span class="cg-value">08:00 - 10:00</span></div>
          <div class="cg-info-row"><i class="fas fa-map-marker-alt"></i><span class="cg-label">Địa chỉ:</span><span class="cg-value">Q.1, TP.HCM</span></div>
          <div class="cg-info-row cg-price"><i class="fas fa-money-bill-wave"></i><span class="cg-label">Tổng tiền:</span><span class="cg-value cg-price-value">350.000 VNĐ</span></div>
        </div>
        <div class="cg-card-footer">
          <button class="cg-btn cg-btn-primary"><i class="fas fa-handshake"></i> Nhận đơn</button>
          <button class="cg-btn cg-btn-ghost"><i class="fas fa-eye"></i> Chi tiết</button>
        </div>
      </div>
    </section>
  </main>
</body>
</html>
