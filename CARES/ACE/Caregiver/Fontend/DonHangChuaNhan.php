<?php
session_start();
if (!isset($_SESSION['so_dien_thoai'])) {
    header("Location: ../Admin/login.php");
    exit();
}
$so_dien_thoai = $_SESSION['so_dien_thoai'];

// --- Lấy thông tin người chăm sóc ---
$ch = curl_init("http://localhost/CARES/ACE/Caregiver/Api_getway/API.php?dichvu=donhang&hanhdong=lay-nguoichamsoc");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(["so_dien_thoai"=>$so_dien_thoai]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/x-www-form-urlencoded"]);
$response = curl_exec($ch);
if($response===false) die("❌ Lỗi cURL: ".curl_error($ch));
$nguoiCS = json_decode($response,true);
curl_close($ch);

if(!is_array($nguoiCS) || $nguoiCS['status']!='success') die("❌ Không tìm thấy người chăm sóc!");
$id_cham_soc = $nguoiCS['data']['id_cham_soc'];
$ho_ten = $nguoiCS['data']['ho_ten'];

// --- Xử lý POST nhận / hủy đơn ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_don_hang = $_POST['id_don_hang'] ?? null;
    if(isset($_POST['nhan_don']) || isset($_POST['huy_don'])) {
        $trang_thai = isset($_POST['nhan_don']) ? 'nhan_don' : 'huy_don';
        $ch = curl_init("http://localhost/CARES/ACE/Caregiver/Api_getway/API.php?dichvu=donhang&hanhdong=capnhat");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            "id_don_hang"=>$id_don_hang,
            "hanhdong"=>$trang_thai,
            "id_cham_soc"=>$id_cham_soc
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        if($response===false) die("❌ Lỗi cURL: ".curl_error($ch));
        $result = json_decode($response,true);
        curl_close($ch);
        if($result['status']==='success'){
            header("Location: DonHangChuaNhan.php");
            exit();
        }else{
            die("❌ Cập nhật đơn thất bại: ".$result['message']);
        }
    }
}

// --- Lấy danh sách đơn hàng ---
$ch = curl_init("http://localhost/CARES/ACE/Caregiver/Api_getway/API.php?dichvu=donhang&hanhdong=lay-danhsach");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(["id_cham_soc"=>$id_cham_soc]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
if($response===false) die("❌ Lỗi cURL: ".curl_error($ch));
$donhang = json_decode($response,true);
curl_close($ch);
$orders = $donhang['data'] ?? [];
if(!empty($orders)){
    usort($orders, function($a, $b){
        return $b['id_don_hang'] - $a['id_don_hang'];
    });
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đơn hàng được giao</title>
<link rel="stylesheet" href="../CSS/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
/* CSS giống như trước, không thay đổi */
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
 <?php
  include 'Dieuhuong.php'; 
  ?>
<body>
<div class="accepted-orders-container">
<div class="hero"><h1><i class="fas fa-list"></i> Đơn hàng được giao cho bạn</h1></div>
<div class="orders-wrapper"><h2>Xin chào, <?php echo htmlspecialchars($ho_ten); ?>!</h2>
<div class="order-cards">
<?php
if(!empty($orders)){
    foreach($orders as $row){
        echo "<a href='Chitietdonhang.php?id_don_hang={$row['id_don_hang']}' class='order-card'>";
        echo "<div><h3>Mã đơn: #{$row['id_don_hang']}</h3><div class='order-info'>";
        echo "<p><strong>Khách hàng:</strong> {$row['ten_khach_hang']}</p>";
        echo "<p><strong>Ngày đặt:</strong> {$row['ngay_dat']}</p>";
        echo "<p><strong>Trạng thái:</strong> <span class='status ".($row['trang_thai']=='đang hoàn thành'?'completed':'pending')."'>{$row['trang_thai']}</span></p>";
        echo "<p><strong>Tổng tiền:</strong> ".number_format($row['tong_tien'],0,',','.')."₫</p></div></div>";
        if($row['trang_thai']=='chờ xác nhận'){
            echo "<div class='btn-container'>
            <form method='POST' style='display:inline;' onClick='event.stopPropagation();'>
            <input type='hidden' name='id_don_hang' value='{$row['id_don_hang']}'>
            <button type='submit' name='nhan_don' class='accept-btn'>Nhận đơn</button>
            </form>
            <form method='POST' style='display:inline;' onClick='event.stopPropagation();'>
            <input type='hidden' name='id_don_hang' value='{$row['id_don_hang']}'>
            <button type='submit' name='huy_don' class='cancel-btn'>Hủy đơn</button>
            </form></div>";
        }
        echo "</a>";
    }
}else{
    echo "<p>❌ Hiện tại bạn chưa có đơn hàng nào được giao.</p>";
}
?>
</div></div></div>
</body>
</html>
<script>
window.addEventListener('pageshow', function(event) {
    // Nếu trang được load từ cache (back/forward)
    if (event.persisted || (window.performance && window.performance.getEntriesByType('navigation')[0].type === 'back_forward')) {
        // reload lại trang 1 lần
        location.reload();
    }
});
</script>

