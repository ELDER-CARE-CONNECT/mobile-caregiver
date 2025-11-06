<<<<<<< HEAD
<?php
session_start();
include_once('../../model/get_products.php');
$conn = connectdb();

// 🔒 Kiểm tra nếu chưa đăng nhập thì chuyển hướng
if (!isset($_SESSION['so_dien_thoai'])) {
    header("Location: ../../Admin/login.php");
    exit();
}

// 📱 Lấy thông tin người dùng đang đăng nhập
$so_dien_thoai = $_SESSION['so_dien_thoai'];

// 📦 Truy vấn các đơn hàng của người dùng đó (có id_cham_soc)
$sql = "SELECT id_don_hang, ten_khach_hang, id_cham_soc, ngay_dat, tong_tien, trang_thai 
        FROM don_hang 
        WHERE so_dien_thoai = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $so_dien_thoai);
$stmt->execute();
$result = $stmt->get_result();
?>

=======
>>>>>>> origin/Thanh
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<<<<<<< HEAD
    <title>Lịch sử đặt hàng</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
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
=======
    <title>Đơn Hàng Đã Nhận - Caregiver</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .breadcrumb {
    display: none !important;
}

/* Ẩn phần bộ lọc: Tìm kiếm, Từ ngày, Đến ngày, Xóa bộ lọc, Tìm kiếm */
.filters-section {
    display: none !important;
}

/* Ẩn nút Xuất Excel và In báo cáo */
.orders-section-actions {
    display: none !important;
}
        .accepted-orders-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
       /* ===== page-header ===== */
.hero{
  padding: 18px 4px 6px;
}
.hero-title{
  margin: 0 0 6px 0;
  font-size: 32px;            /* chữ to như ảnh */
  font-weight: 800;
  color: #111827;             /* gần #1f2937 */
  letter-spacing: .2px;
}
.hero-subtitle{
  margin: 0 0 14px 0;
  color: #6b7280;             /* xám dịu */
  font-size: 15px;
}

/* Breadcrumb */
.breadcrumb{
  display:flex; align-items:center; gap:10px;
  padding: 10px 4px 14px;
  border-bottom: 1px solid #e5e7eb;
  color:#6b7280;
  margin-bottom: 18px;
}
.bc-link{
  color:#6b7280; text-decoration:none; font-weight:600;
}
.bc-link:hover{ color:#374151; text-decoration:underline; }
.bc-sep{ color:#9ca3af; }
.bc-current{ color:#374151; font-weight:600; }

        
        .filters-section {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }
        
        .filters-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        .filter-group label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .filter-group input,
        .filter-group select {
            padding: 12px 15px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .filter-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
        }
        
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg,rgb(39, 73, 226) 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #f8f9fa;
            color: #6b7280;
            border: 2px solid #e5e7eb;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
            color: #374151;
        }
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            text-align: center;
            transition: transform 0.3s ease;
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
        }
        
        .summary-card i {
            font-size: 40px;
            margin-bottom: 15px;
            color: #667eea;
        }
        
        .summary-card h3 {
            margin: 0 0 10px;
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
        }
        
        .summary-card p {
            margin: 0;
            color: #6b7280;
            font-weight: 500;
        }
        
        .orders-section {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }
        
        .orders-section-header {
            padding: 25px 30px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .orders-section-title {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin: 0;
        }
        
        .orders-section-actions {
            display: flex;
            gap: 12px;
        }
        
        .action-btn {
            padding: 10px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            background: white;
            color: #6b7280;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .action-btn:hover {
            border-color: #667eea;
            color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        }
        
        .action-btn.export {
            border-color: #10b981;
            color: #10b981;
        }
        
        .action-btn.export:hover {
            background: #10b981;
            color: white;
        }
        
        .action-btn.print {
            border-color: #f59e0b;
            color: #f59e0b;
        }
        
        .action-btn.print:hover {
            background: #f59e0b;
            color: white;
        }
        
        .orders-table-container {
            padding: 0 30px 30px;
            overflow-x: auto;
        }
        
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .orders-table thead {
            background: #f8f9fa;
        }
        
        .orders-table th {
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
            border-bottom: 2px solid #e5e7eb;
            white-space: nowrap;
        }
        
        .orders-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 14px;
            color: #1f2937;
            vertical-align: middle;
        }
        
        .orders-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .orders-table tbody tr:last-child td {
            border-bottom: none;
        }
        
        .order-id-cell {
            font-weight: 600;
            color: #667eea;
        }
        
        .customer-cell {
>>>>>>> origin/Thanh
            display: flex;
            align-items: center;
            gap: 10px;
        }
<<<<<<< HEAD

        /* ===== KHUNG LỚN ===== */
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

        /* ===== GRID ===== */
        .order-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, max-content));
            justify-content: start;
            gap: 24px;
            justify-items: flex-start;
        }

        /* ===== CARD ===== */
        .order-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            width: 340px;
            height: 180px; /* tăng nhẹ để đủ hiển thị người chăm sóc */
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            border-color: #3b82f6;
        }

        .order-card h3 {
            margin: 0 0 12px;
            font-size: 17px;
            font-weight: 700;
            color: #2563eb;
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

        .status.completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        /* ===== Nút Xem ===== */
        .view-btn {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.25s ease;
            align-self: flex-end;
        }

        .view-btn:hover {
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
            transform: translateY(-2px);
        }

        /* Khi chỉ có 1 đơn hàng — căn giữa */
        .order-cards:has(.order-card:only-child) {
            justify-content: center;
        }

        @media (max-width: 768px) {
            .order-card {
                width: 100%;
                height: auto;
=======
        
        .customer-avatar-small {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 14px;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .customer-info-small {
            min-width: 0;
        }
        
        .customer-name {
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .customer-phone {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-completed {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #d97706;
        }
        
        .status-cancelled {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .price-cell {
            font-weight: 700;
            color: #667eea;
            font-size: 15px;
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .action-btn-small {
            padding: 6px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            background: white;
            color: #6b7280;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        
        .action-btn-small:hover {
            border-color: #667eea;
            color: #667eea;
            background: #f8f9fa;
        }
        
        .action-btn-small.primary {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }
        
        .action-btn-small.primary:hover {
            background: #5a67d8;
            border-color: #5a67d8;
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
            animation: slideIn 0.3s ease;
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
        }
        
        .order-total-modal {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin-top: 20px;
        }
        
        .order-total-modal h3 {
            margin: 0 0 5px;
            font-size: 28px;
            font-weight: 700;
        }
        
        .order-total-modal p {
            margin: 0;
            opacity: 0.9;
            font-size: 16px;
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
            .modal-content {
                width: 95%;
                margin: 20px;
            }
            
            .modal-header, .modal-body {
                padding: 20px;
            }
            
            .detail-grid {
                grid-template-columns: 1fr;
            }
            
            .customer-info-modal {
                flex-direction: column;
                text-align: center;
            }
        }
        
        .order-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .order-id {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }
        
        .order-status {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: #d1fae5;
            color: #065f46;
        }
        
        .order-body {
            padding: 25px;
        }
        
        .customer-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .customer-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e5e7eb;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
        }
        
        .customer-details h4 {
            margin: 0 0 5px;
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .customer-details p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }
        
        .order-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .detail-item i {
            width: 20px;
            color: #667eea;
            text-align: center;
        }
        
        .detail-label {
            font-weight: 600;
            color: #374151;
            min-width: 100px;
        }
        
        .detail-value {
            color: #1f2937;
        }
        
        .order-total {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        
        .order-total h3 {
            margin: 0 0 5px;
            font-size: 24px;
            font-weight: 700;
        }
        
        .order-total p {
            margin: 0;
            opacity: 0.9;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 30px;
        }
        
        .page-btn {
            padding: 10px 15px;
            border: 2px solid #e5e7eb;
            background: white;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .page-btn:hover:not(:disabled) {
            border-color:rgb(40, 72, 216);
            color:rgb(40, 72, 216);
        }
        
        .page-btn.active {
            background:rgb(40, 72, 216);
            border-color:rgb(40, 72, 216);
            color: white;
        }
        
        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .loading {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        
        .loading i {
            font-size: 48px;
            margin-bottom: 20px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        
        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            color: #d1d5db;
        }
        
        .empty-state h3 {
            margin: 0 0 10px;
            font-size: 24px;
            color: #374151;
        }
        
        .empty-state p {
            margin: 0;
            font-size: 16px;
        }
        
        @media (max-width: 768px) {
            .filters-row {
                grid-template-columns: 1fr;
            }
            
            .filter-actions {
                justify-content: center;
            }
            
            .order-details {
                grid-template-columns: 1fr;
            }
            
            .customer-info {
                flex-direction: column;
                text-align: center;
>>>>>>> origin/Thanh
            }
        }
    </style>
</head>
<<<<<<< HEAD
<body>
<div class="accepted-orders-container">
    <div class="hero">
        <h1><i class="fas fa-check-circle"></i> Lịch sử đặt hàng của bạn</h1>
    </div>

    <div class="orders-wrapper">
        <h2>Xin chào, <?php echo htmlspecialchars($_SESSION['ten_khach_hang']); ?>!</h2>

        <div class="order-cards">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {

                    // 🔍 Tìm tên người chăm sóc dựa trên id_cham_soc
                    $ten_cham_soc = "Chưa có";
                    if (!empty($row['id_cham_soc'])) {
                        $sql2 = "SELECT ho_ten FROM nguoi_cham_soc WHERE id_cham_soc = ?";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->bind_param("i", $row['id_cham_soc']);
                        $stmt2->execute();
                        $res2 = $stmt2->get_result();
                        if ($res2->num_rows > 0) {
                            $ten_cham_soc = $res2->fetch_assoc()['ho_ten'];
                        }
                        $stmt2->close();
                    }

                    // 💬 Hiển thị từng đơn hàng
                    echo "
                    <div class='order-card'>
                        <div>
                            <h3>Mã đơn: #{$row['id_don_hang']}</h3>
                            <div class='order-info'>
                                <p><strong>Người chăm sóc:</strong> {$ten_cham_soc}</p>
                                <p><strong>Ngày đặt:</strong> {$row['ngay_dat']}</p>
                                <p><strong>Trạng thái:</strong> 
                                    <span class='status " . 
                                        ($row['trang_thai'] == 'hoàn thành' ? 'completed' : 'pending') . "'>
                                        {$row['trang_thai']}
                                    </span>
                                </p>
                                <p><strong>Tổng tiền:</strong> " . number_format($row['tong_tien'], 0, ',', '.') . "₫</p>
                            </div>
                        </div>
                        <button class='view-btn'>Xem</button>
                    </div>";
                }
            } else {
                echo "<p>❌ Bạn chưa có đơn hàng nào.</p>";
            }

            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>
</div>
</body>
</html>
=======
<body> 
    
    <div class="accepted-orders-container">
        <!-- Header -->
        <div class="hero">
    <h1><i class="fas fa-check-circle"></i> Tổng đơn hàng đã hoàn thành</h1>
    <p>Danh sách các đơn hàng đã hoàn thành</p>
</div>

        <!-- Breadcrumb -->
<nav class="breadcrumb">
  <a href="#" class="bc-link">Trang chủ</a>
  <span class="bc-sep">›</span>
  <span class="bc-current">Đơn Hàng Đã Nhận</span>
</nav>
        
        <!-- Filters -->
        <div class="filters-section">
            <div class="filters-row">
                <div class="filter-group">
                    <label for="search">Tìm kiếm</label>
                    <input type="text" id="search" placeholder="Tìm theo mã đơn, tên khách hàng, SĐT...">
                </div>
                <div class="filter-group">
                    <label for="from_date">Từ ngày</label>
                    <input type="date" id="from_date">
                </div>
                <div class="filter-group">
                    <label for="to_date">Đến ngày</label>
                    <input type="date" id="to_date">
                </div>
            </div>
            <div class="filter-actions">
                <button class="btn btn-secondary" onclick="clearFilters()">
                    <i class="fas fa-times"></i> Xóa bộ lọc
                </button>
                <button class="btn btn-primary" onclick="loadOrders()">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="summary-cards" id="summaryCards">
            <!-- Summary cards will be loaded here -->
        </div>
        
        <!-- Orders List Section -->
        <div class="orders-section">
            <div class="orders-section-header">
                <h2 class="orders-section-title">Danh sách đơn hàng</h2>
                <div class="orders-section-actions">
                    <button class="action-btn export" onclick="exportToExcel()">
                        <i class="fas fa-download"></i>
                        Xuất Excel
                    </button>
                    <button class="action-btn print" onclick="printReport()">
                        <i class="fas fa-print"></i>
                        In báo cáo
                    </button>
                </div>
            </div>
            <div class="orders-table-container">
                <div id="ordersContainer">
                    <div class="loading">
                        <i class="fas fa-spinner"></i>
                        <p>Đang tải dữ liệu...</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="pagination" id="pagination">
            <!-- Pagination will be loaded here -->
        </div>
    </div>

    <!-- Modal for order details -->
    <div id="orderModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Chi tiết đơn hàng</h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        let currentPage = 1;
        let totalPages = 1;
        let currentFilters = {};
        
        // Load orders on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadOrders();
        });
        
        // Load orders function
        async function loadOrders(page = 1) {
            currentPage = page;
            
            // Get filter values
            const search = document.getElementById('search').value;
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;
            
            // Update current filters
            currentFilters = {
                search: search,
                from_date: fromDate,
                to_date: toDate,
                page: page,
                limit: 10
            };
            
            try {
                // Show loading
                document.getElementById('ordersContainer').innerHTML = `
                    <div class="loading">
                        <i class="fas fa-spinner"></i>
                        <p>Đang tải dữ liệu...</p>
                    </div>
                `;
                
                // Build query string
                const queryParams = new URLSearchParams();
                Object.keys(currentFilters).forEach(key => {
                    if (currentFilters[key]) {
                        queryParams.append(key, currentFilters[key]);
                    }
                });
                
                // Fetch data
                const response = await fetch(`get_accepted_orders.php?${queryParams}`);
                const data = await response.json();
                
                if (data.success) {
                    displayOrders(data.data);
                    displaySummary(data.summary);
                    displayPagination(data.pagination);
                } else {
                    showError(data.error || 'Có lỗi xảy ra khi tải dữ liệu');
                }
            } catch (error) {
                showError('Lỗi kết nối: ' + error.message);
            }
        }
        
        // Display orders
        function displayOrders(orders) {
            const container = document.getElementById('ordersContainer');
            
            // Store current orders for export
            currentOrders = orders;
            
            if (orders.length === 0) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>Không có đơn hàng nào</h3>
                        <p>Chưa có đơn hàng đã nhận nào trong khoảng thời gian này</p>
                    </div>
                `;
                return;
            }
            
            const ordersHTML = `
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
                    <tbody>
                        ${orders.map(order => `
                            <tr>
                                <td class="order-id-cell">#${order.id_don_hang}</td>
                                <td>${formatDate(order.ngay_dat)}</td>
                                <td class="customer-cell">
                                    <div class="customer-avatar-small">
                                        ${getAvatarInitials(order.ten_khach_hang)}
                                    </div>
                                    <div class="customer-info-small">
                                        <div class="customer-name">${order.ten_khach_hang}</div>
                                        <div class="customer-phone">${order.so_dien_thoai}</div>
                                    </div>
                                </td>
                                <td>${order.nguoi_cham_soc_ten || 'Chưa phân công'}</td>
                                <td>Chăm sóc tại nhà</td>
                                <td>${order.thoi_gian_bat_dau} - ${order.thoi_gian_ket_thuc}</td>
                                <td class="price-cell">${formatCurrency(order.tong_tien)}</td>
                                <td>
                                    <span class="status-badge status-completed">${order.trang_thai}</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn-small primary" onclick="viewOrder(${order.id_don_hang})">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn-small" onclick="editOrder(${order.id_don_hang})">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
            
            container.innerHTML = ordersHTML;
        }
        
        // Display summary
        function displaySummary(summary) {
            const summaryHTML = `
                <div class="summary-card">
                    <i class="fas fa-shopping-cart"></i>
                    <h3>${summary.total_orders}</h3>
                    <p>Tổng đơn hàng</p>
                </div>
                <div class="summary-card">
                    <i class="fas fa-money-bill-wave"></i>
                    <h3>${formatCurrency(summary.total_amount)}</h3>
                    <p>Tổng doanh thu</p>
                </div>
            `;
            
            document.getElementById('summaryCards').innerHTML = summaryHTML;
        }
        
        // Display pagination
        function displayPagination(pagination) {
            totalPages = pagination.total_pages;
            const current = pagination.current_page;
            
            let paginationHTML = '';
            
            // Previous button
            paginationHTML += `
                <button class="page-btn" ${current <= 1 ? 'disabled' : ''} 
                        onclick="loadOrders(${current - 1})">
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;
            
            // Page numbers
            const startPage = Math.max(1, current - 2);
            const endPage = Math.min(totalPages, current + 2);
            
            for (let i = startPage; i <= endPage; i++) {
                paginationHTML += `
                    <button class="page-btn ${i === current ? 'active' : ''}" 
                            onclick="loadOrders(${i})">
                        ${i}
                    </button>
                `;
            }
            
            // Next button
            paginationHTML += `
                <button class="page-btn" ${current >= totalPages ? 'disabled' : ''} 
                        onclick="loadOrders(${current + 1})">
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;
            
            document.getElementById('pagination').innerHTML = paginationHTML;
        }
        
        // Clear filters
        function clearFilters() {
            document.getElementById('search').value = '';
            document.getElementById('from_date').value = '';
            document.getElementById('to_date').value = '';
            loadOrders(1);
        }
        
        // Show error
        function showError(message) {
            document.getElementById('ordersContainer').innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Lỗi tải dữ liệu</h3>
                    <p>${message}</p>
                </div>
            `;
        }
        
        // Format date
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });
        }
        
        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND'
            }).format(amount);
        }
        
        // Get avatar initials from name
        function getAvatarInitials(name) {
            if (!name) return 'KH';
            const words = name.trim().split(' ');
            if (words.length >= 2) {
                return (words[0][0] + words[words.length - 1][0]).toUpperCase();
            }
            return name.substring(0, 2).toUpperCase();
        }
        
        // Export to Excel
        function exportToExcel() {
            const data = currentOrders || [];
            if (data.length === 0) {
                alert('Không có dữ liệu để xuất!');
                return;
            }
            
            // Create CSV content
            let csvContent = "Mã đơn,Ngày đặt,Khách hàng,SĐT,Email,Địa chỉ,Tổng tiền,Trạng thái\n";
            
            data.forEach(order => {
                csvContent += `"${order.id_don_hang}","${order.ngay_dat}","${order.ten_khach_hang}","${order.so_dien_thoai}","${order.email || ''}","${order.dia_chi_giao_hang || ''}","${order.tong_tien}","${order.trang_thai}"\n`;
            });
            
            // Download file
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', `don_hang_da_nhan_${new Date().toISOString().split('T')[0]}.csv`);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
        
        // Print report
        function printReport() {
            const printContent = document.getElementById('ordersContainer').innerHTML;
            const originalContent = document.body.innerHTML;
            
            document.body.innerHTML = `
                <div style="font-family: Arial, sans-serif; padding: 20px;">
                    <h1 style="text-align: center; color: #333;">BÁO CÁO ĐƠN HÀNG ĐÃ NHẬN</h1>
                    <p style="text-align: center; color: #666;">Ngày xuất: ${new Date().toLocaleDateString('vi-VN')}</p>
                    <div style="margin-top: 30px;">
                        ${printContent}
                    </div>
                </div>
            `;
            
            window.print();
            document.body.innerHTML = originalContent;
            loadOrders(currentPage); // Reload data
        }
        
        // Store current orders for export
        let currentOrders = [];
        
        // View order details
        function viewOrder(orderId) {
            // Find order data
            const order = currentOrders.find(o => o.id_don_hang == orderId);
            if (!order) {
                alert('Không tìm thấy thông tin đơn hàng');
                return;
            }
            
            // Show modal
            showOrderModal(order);
        }
        
        // Show order modal
        function showOrderModal(order) {
            const modal = document.getElementById('orderModal');
            const modalBody = document.getElementById('modalBody');
            
            modalBody.innerHTML = `
                <div class="customer-info-modal">
                    <div class="customer-avatar-modal">
                        ${getAvatarInitials(order.ten_khach_hang)}
                    </div>
                    <div class="customer-details-modal">
                        <h3>${order.ten_khach_hang}</h3>
                        <p><i class="fas fa-phone"></i> ${order.so_dien_thoai}</p>
                        <p><i class="fas fa-envelope"></i> ${order.email || 'Chưa có email'}</p>
                        <p><i class="fas fa-map-marker-alt"></i> ${order.dia_chi_giao_hang || 'Chưa có địa chỉ'}</p>
                    </div>
                </div>
                
                <div class="order-detail-section">
                    <h4><i class="fas fa-info-circle"></i> Thông tin đơn hàng</h4>
                    <div class="detail-grid">
                        <div class="detail-item-modal">
                            <i class="fas fa-hashtag"></i>
                            <span class="detail-label-modal">Mã đơn:</span>
                            <span class="detail-value-modal">#${order.id_don_hang}</span>
                        </div>
                        <div class="detail-item-modal">
                            <i class="fas fa-calendar"></i>
                            <span class="detail-label-modal">Ngày đặt:</span>
                            <span class="detail-value-modal">${formatDate(order.ngay_dat)}</span>
                        </div>
                        <div class="detail-item-modal">
                            <i class="fas fa-clock"></i>
                            <span class="detail-label-modal">Thời gian:</span>
                            <span class="detail-value-modal">${order.thoi_gian_bat_dau} - ${order.thoi_gian_ket_thuc}</span>
                        </div>
                        <div class="detail-item-modal">
                            <i class="fas fa-user-md"></i>
                            <span class="detail-label-modal">Người chăm sóc:</span>
                            <span class="detail-value-modal">${order.nguoi_cham_soc_ten || 'Chưa phân công'}</span>
                        </div>
                        <div class="detail-item-modal">
                            <i class="fas fa-concierge-bell"></i>
                            <span class="detail-label-modal">Dịch vụ:</span>
                            <span class="detail-value-modal">Chăm sóc tại nhà</span>
                        </div>
                        <div class="detail-item-modal">
                            <i class="fas fa-credit-card"></i>
                            <span class="detail-label-modal">Thanh toán:</span>
                            <span class="detail-value-modal">${order.phuong_thuc_thanh_toan}</span>
                        </div>
                    </div>
                </div>
                
                <div class="order-detail-section">
                    <h4><i class="fas fa-tasks"></i> Trạng thái đơn hàng</h4>
                    <div class="detail-item-modal">
                        <i class="fas fa-check-circle"></i>
                        <span class="detail-label-modal">Trạng thái:</span>
                        <span class="detail-value-modal">
                            <span class="status-badge status-completed">${order.trang_thai}</span>
                        </span>
                    </div>
                </div>
                
                <div class="order-total-modal">
                    <h3>${formatCurrency(order.tong_tien)}</h3>
                    <p>Tổng tiền đơn hàng</p>
                </div>
            `;
            
            modal.classList.add('show');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }
        
        // Close modal
        function closeModal() {
            const modal = document.getElementById('orderModal');
            modal.classList.remove('show');
            document.body.style.overflow = 'auto'; // Restore scrolling
        }
        
        // Close modal when clicking outside
        document.addEventListener('click', function(event) {
            const modal = document.getElementById('orderModal');
            if (event.target === modal) {
                closeModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
        
        // Edit order
        function editOrder(orderId) {
            alert(`Chỉnh sửa đơn hàng #${orderId}`);
            // Có thể mở modal hoặc redirect đến trang chỉnh sửa
        }
        
    </script>
</body>
</html>
>>>>>>> origin/Thanh
