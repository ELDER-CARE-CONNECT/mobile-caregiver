<?php
$activePage = 'quanlidonhang';
$pageTitle = 'Quản lí đơn hàng';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($pageTitle) ?></title>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<style>
body { font-family:"Segoe UI",sans-serif; background:linear-gradient(135deg,#e3f2fd,#bbdefb); margin:0; padding:0; color:#333; }
.container { display:flex; min-height:100vh; }
.main-content { flex-grow:1; background:#fff; padding:25px 40px; margin:20px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.1); animation:fadeIn 0.5s ease; }
@keyframes fadeIn { from {opacity:0; transform:translateY(-10px);} to {opacity:1; transform:translateY(0);} }
.navbar { display:flex; justify-content:space-between; align-items:center; border-bottom:3px solid #2196f3; padding-bottom:15px; margin-bottom:25px; }
.navbar h1 { color:#0d47a1; font-size:24px; }
.search input { padding:8px 12px; border-radius:6px; border:1px solid #90caf9; width:240px; }
.search button { background:#1e88e5; color:white; border:none; padding:8px 14px; border-radius:6px; cursor:pointer; margin-left:5px; transition:0.3s; }
.search button:hover { background:#0d47a1; }
table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
th { background:#1e88e5; color:white; text-transform:uppercase; font-size:14px; padding:12px; }
td { padding:10px; border-bottom:1px solid #eee; text-align:center; }
tr:nth-child(even) { background:#f9f9f9; }
tr:hover { background:#e3f2fd; transition:0.3s; }
select { padding:5px 8px; border-radius:6px; border:1px solid #90caf9; }
button.update-btn { background:#2196f3; border:none; color:white; padding:6px 10px; border-radius:6px; cursor:pointer; transition:0.3s; }
button.update-btn:hover { background:#1565c0; }
.order-header h2 { color:#0d47a1; font-size:20px; margin-bottom:15px; }
.no-order { text-align:center; padding:20px; color:#777; font-style:italic; }
.message { text-align:center; font-weight:bold; margin-top:10px; }
</style>
</head>
<body>

<div class="container">
    <?php include 'sidebar.php'; ?>
    <main class="main-content">
        <header class="navbar">
            <h1>Trang Quản Lí Đơn Hàng</h1>
            <div class="search">
                <input type="text" id="search_id" placeholder="Tìm kiếm theo mã đơn hàng">
                <button id="btnSearch">🔍</button>
            </div>
        </header>

        <section class="stats">
            <div class="order-header">
                <h2>Danh sách đơn hàng</h2>
            </div>

            <div id="message" class="message"></div>

            <table id="orderTable">
                <tr>
                    <th>Mã đơn hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tên khách hàng</th>
                    <th>Số điện thoại</th>
                    <th>Người chăm sóc</th>
                    <th>Trạng thái</th>
                    <th>Đánh giá</th>
                    <th>Nhận xét</th>
                    <th>Tổng tiền</th>
                    <th>Cập nhật</th>
                </tr>
                <tr><td colspan="10" class="no-order">Đang tải dữ liệu...</td></tr>
            </table>
        </section>
    </main>
</div>

<script>
const backendPath = '../../backend/quanli'; // Đường dẫn backend chính xác

function loadOrders(search='') {
    $.getJSON(`${backendPath}/quanlidonhang.php`, {search_id: search})
    .done(function(res){
        let table = $('#orderTable');
        table.find('tr:gt(0)').remove(); 
        $('#message').text('');

        if(res.status !== 'success'){
            $('#message').text('Lỗi tải dữ liệu: ' + (res.message||'')).css('color','red');
            table.append('<tr><td colspan="10" class="no-order">Không thể tải dữ liệu</td></tr>');
            return;
        }

        if(res.orders.length > 0){
            $.each(res.orders, function(i, row){
                table.append(`
                    <tr>
                        <td>${row.id_don_hang}</td>
                        <td>${row.ngay_dat}</td>
                        <td>${row.ten_khach_hang || ''}</td>
                        <td>${row.so_dien_thoai || ''}</td>
                        <td>${row.nguoi_cham_soc || 'Chưa có'}</td>
                        <td>${row.trang_thai || ''}</td>
                        <td>${row.danh_gia || 'Chưa đánh giá'}</td>
                        <td>${row.nhan_xet || '—'}</td>
                        <td>${Number(row.tong_tien || 0).toLocaleString('vi-VN')} VND</td>
                        <td>
                            <select class="trangthai" data-id="${row.id_don_hang}">
                                <option value="chờ xác nhận" ${(row.trang_thai=='chờ xác nhận')?'selected':''}>Chờ xác nhận</option>
                                <option value="đang hoàn thành" ${(row.trang_thai=='đang hoàn thành')?'selected':''}>Đang hoàn thành</option>
                                <option value="đã hoàn thành" ${(row.trang_thai=='đã hoàn thành')?'selected':''}>Đã hoàn thành</option>
                                <option value="đã hủy" ${(row.trang_thai=='đã hủy')?'selected':''}>Đã hủy</option>
                            </select>
                            <button class="update-btn" data-id="${row.id_don_hang}">Lưu</button>
                        </td>
                    </tr>
                `);
            });
        } else {
            table.append('<tr><td colspan="10" class="no-order">Không có đơn hàng nào.</td></tr>');
        }
    })
    .fail(function(){
        $('#message').text('Lỗi kết nối tới server').css('color','red');
    });
}

$(document).ready(function(){
    loadOrders();

    $('#btnSearch').click(function(){
        let search = $('#search_id').val().trim();
        loadOrders(search);
    });

    $(document).on('click', '.update-btn', function(){
        let id = $(this).data('id');
        let status = $(this).closest('td').find('.trangthai').val();

        $.post(`${backendPath}/capnhat_trangthai.php`, {id_don_hang: id, trang_thai: status}, function(res){
            if(res.status === 'success'){
                $('#message').text('Cập nhật thành công!').css('color','green');
                loadOrders($('#search_id').val().trim());
            } else {
                $('#message').text('Cập nhật thất bại: ' + (res.message||'')).css('color','red');
            }
        }, 'json')
        .fail(function(){
            $('#message').text('Lỗi server').css('color','red');
        });
    });
});
</script>

</body>
</html>
