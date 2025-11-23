<?php
session_start();

// 1. Kiểm tra đăng nhập
if (!isset($_SESSION['so_dien_thoai'])) {
    header("Location: ../../Admin/frontend/auth/login.php");
    exit();
}

$so_dien_thoai = $_SESSION['so_dien_thoai'];

// 2. KẾT NỐI DATABASE TRỰC TIẾP (Thay vì gọi API)
require_once 'connect.php'; // File này nằm cùng thư mục Fontend
if (!function_exists('connectdb')) {
    die("Lỗi: File connect.php không đúng chuẩn.");
}
$conn = connectdb();

// 3. Lấy thông tin người chăm sóc từ SĐT
$sql_user = "SELECT id_cham_soc, ho_ten FROM nguoi_cham_soc WHERE so_dien_thoai = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("s", $so_dien_thoai);
$stmt->execute();
$result_user = $stmt->get_result();
$nguoiCS = $result_user->fetch_assoc();

if (!$nguoiCS) {
    // Nếu không tìm thấy trong bảng người chăm sóc -> Có thể đăng nhập nhầm tài khoản Khách hàng
    die("<div style='padding:20px; color:red; text-align:center;'>
            <h2>❌ Lỗi tài khoản</h2>
            <p>Số điện thoại <b>$so_dien_thoai</b> không tồn tại trong danh sách Người chăm sóc.</p>
            <a href='../../Admin/logout.php'>Đăng xuất và thử lại</a>
         </div>");
}

$id_cham_soc = $nguoiCS['id_cham_soc'];
$ho_ten = $nguoiCS['ho_ten'];

// Lưu ID vào session để dùng cho các trang khác (như api_profile)
$_SESSION['id_cham_soc'] = $id_cham_soc;

// 4. Xử lý nhận đơn / hủy đơn
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_don_hang = $_POST['id_don_hang'] ?? null;
    $trang_thai_moi = '';

    if (isset($_POST['nhan_don'])) {
        $trang_thai_moi = 'đang hoàn thành';
    } elseif (isset($_POST['huy_don'])) {
        $trang_thai_moi = 'đã hủy';
    }

    if ($id_don_hang && $trang_thai_moi) {
        $sql_update = "UPDATE don_hang SET trang_thai = ? WHERE id_don_hang = ? AND id_nguoi_cham_soc = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sii", $trang_thai_moi, $id_don_hang, $id_cham_soc);
        
        if ($stmt_update->execute()) {
            // Reload trang để cập nhật giao diện
            header("Location: DonHangChuaNhan.php");
            exit();
        } else {
            echo "<script>alert('Lỗi cập nhật đơn hàng: " . $conn->error . "');</script>";
        }
    }
}

// 5. Lấy danh sách đơn hàng (Chờ xác nhận & Đang hoàn thành)
$sql_orders = "SELECT id_don_hang, ten_khach_hang, ngay_dat, tong_tien, trang_thai 
               FROM don_hang 
               WHERE id_nguoi_cham_soc = ? 
               AND trang_thai IN ('chờ xác nhận', 'đang hoàn thành')
               ORDER BY id_don_hang DESC";

$stmt_orders = $conn->prepare($sql_orders);
$stmt_orders->bind_param("i", $id_cham_soc);
$stmt_orders->execute();
$result_orders = $stmt_orders->get_result();
$orders = [];
while ($row = $result_orders->fetch_assoc()) {
    $orders[] = $row;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đơn hàng được giao</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
/* CSS Cũ giữ nguyên */
body{font-family:'Segoe UI',sans-serif;background:#f9fafb;margin:0;padding:0}
.accepted-orders-container{max-width:1200px;margin:40px auto;padding:20px}
.hero h1{font-size:28px;font-weight:700;margin-bottom:20px;color:#111827;display:flex;align-items:center;gap:10px}
.orders-wrapper{background:#fff;border-radius:18px;box-shadow:0 6px 25px rgba(0,0,0,0.1);padding:30px;border:1px solid #e5e7eb}
.orders-wrapper h2{font-size:22px;font-weight:700;color:#1f2937;margin-bottom:25px;border-bottom:2px solid #e5e7eb;padding-bottom:10px}
.order-cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,max-content));justify-content:start;gap:24px;justify-items:flex-start}
.order-card{text-decoration:none;color:inherit;background:#fff;border-radius:16px;border:1px solid #e5e7eb;box-shadow:0 4px 20px rgba(0,0,0,0.05);width:340px;height:190px;padding:20px;display:flex;flex-direction:column;justify-content:space-between;transition:all 0.3s ease}
.order-card:hover{transform:translateY(-5px);box-shadow:0 8px 25px rgba(0,0,0,0.12);border-color:#3b82f6}
.order-card h3{margin:0 0 10px;font-size:17px;font-weight:700;color:#2563eb}
.order-info p{margin:4px 0;color:#374151;font-size:15px;line-height:1.4}
.status{display:inline-block;padding:4px 12px;border-radius:20px;font-size:13px;font-weight:600}
.status.completed{background:#d1fae5;color:#065f46}
.status.pending{background:#fef3c7;color:#92400e}
.btn-container{display:flex;gap:10px;justify-content:flex-end}
.accept-btn{background:#2563eb;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;transition:all 0.25s ease}
.accept-btn:hover{background:#1d4ed8;transform:translateY(-2px)}
.cancel-btn{background:#dc2626;color:#fff;border:none;padding:8px 16px;border-radius:8px;font-weight:600;font-size:14px;cursor:pointer;transition:all 0.25s ease}
.cancel-btn:hover{background:#b91c1c;transform:translateY(-2px)}
@media(max-width:768px){.order-card{width:100%;height:auto}}
</style>
</head>

<?php include 'Dieuhuong.php'; ?>

<body>
<div class="accepted-orders-container">
    <div class="hero"><h1><i class="fas fa-list"></i> Đơn hàng được giao cho bạn</h1></div>
    
    <div class="orders-wrapper">
        <h2>Xin chào, <?php echo htmlspecialchars($ho_ten); ?>!</h2>
        
        <div class="order-cards">
            <?php if (!empty($orders)): ?>
                <?php foreach($orders as $row): ?>
                    <a href='Chitietdonhang.php?id_don_hang=<?php echo $row['id_don_hang']; ?>' class='order-card'>
                        <div>
                            <h3>Mã đơn: #<?php echo $row['id_don_hang']; ?></h3>
                            <div class='order-info'>
                                <p><strong>Khách hàng:</strong> <?php echo htmlspecialchars($row['ten_khach_hang']); ?></p>
                                <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y', strtotime($row['ngay_dat'])); ?></p>
                                <p><strong>Trạng thái:</strong> 
                                    <span class='status <?php echo ($row['trang_thai']=='đang hoàn thành' ? 'completed' : 'pending'); ?>'>
                                        <?php echo $row['trang_thai']; ?>
                                    </span>
                                </p>
                                <p><strong>Tổng tiền:</strong> <?php echo number_format($row['tong_tien'], 0, ',', '.'); ?>₫</p>
                            </div>
                        </div>

                        <?php if($row['trang_thai'] == 'chờ xác nhận'): ?>
                        <div class='btn-container'>
                            <form method='POST' style='display:inline;' onsubmit="return confirm('Bạn chắc chắn muốn nhận đơn này?');" onClick='event.stopPropagation();'>
                                <input type='hidden' name='id_don_hang' value='<?php echo $row['id_don_hang']; ?>'>
                                <button type='submit' name='nhan_don' class='accept-btn'>Nhận đơn</button>
                            </form>

                            <form method='POST' style='display:inline;' onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn này?');" onClick='event.stopPropagation();'>
                                <input type='hidden' name='id_don_hang' value='<?php echo $row['id_don_hang']; ?>'>
                                <button type='submit' name='huy_don' class='cancel-btn'>Hủy đơn</button>
                            </form>
                        </div>
                        <?php endif; ?>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="padding: 20px; color: #666;">❌ Hiện tại bạn chưa có đơn hàng nào mới.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>