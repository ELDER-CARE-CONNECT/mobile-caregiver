<?php
$activePage = 'danhgia';
$pageTitle = 'Quản Lí Đánh Giá';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $pageTitle; ?></title>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<style>
/* ===== Sidebar + Main CSS ===== */
* { margin:0; padding:0; box-sizing:border-box; font-family:Arial,sans-serif; }
.sidebar { width:250px; background:linear-gradient(180deg,#f8f9fa 0%,#e9ecef 100%); padding:20px; height:100vh; position:fixed; left:0; display:flex; flex-direction:column; }
.sidebar ul { list-style:none; margin-top:10px; padding:0; }
.sidebar ul li { margin:12px 0; }
.sidebar ul li a { display:flex; align-items:center; text-decoration:none; color:#000; font-weight:700; padding:10px 12px; border-radius:8px; transition:0.3s; }
.sidebar ul li.active a, .sidebar ul li a:hover { background:#007bff; color:#fff; transform:translateX(5px); }
.main-content { margin-left:250px; padding:20px; }
.navbar { display:flex; justify-content:space-between; align-items:center; border-bottom:3px solid #3498db; padding-bottom:15px; margin-bottom:10px; }
.navbar h1 { color:#3498db; font-size:22px; font-weight:600; }
.search input { padding:7px 10px; border:1px solid #ccc; border-radius:6px; width:260px; }
.search button { background:#3498db; color:white; border:none; padding:7px 12px; border-radius:6px; cursor:pointer; }
.filter-box { margin-top:15px; margin-bottom:10px; display:flex; justify-content:flex-end; align-items:center; gap:10px; }
.avg-box { background:#eaf4ff; padding:10px 20px; border-radius:8px; margin-top:15px; display:inline-block; font-weight:600; }
table { width:100%; border-collapse:collapse; margin-top:25px; box-shadow:0 2px 6px rgba(0,0,0,0.1);}
th { background:#3498db; color:white; padding:12px; font-weight:600; }
td { padding:10px; border-bottom:1px solid #eee; text-align:center; }
tr:nth-child(even) { background:#f9f9f9; }
tr:hover { background:#eaf4ff; }
.star { color:#f1c40f; font-weight:bold; }
.action-links a { text-decoration:none; margin:0 5px; color:#2980b9; }
.action-links a:hover { color:#e74c3c; }
.action-links span { cursor:pointer; color:#e74c3c; margin:0 5px; } /* Cho xóa */
.action-links span:hover { text-decoration:underline; }
.loading { color:#007bff; font-style:italic; }
.error { color:#e74c3c; }
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main-content">
    <header class="navbar">
        <h1>Quản Lí Đánh Giá Người Chăm Sóc</h1>
        <div class="search">
            <input type="text" id="searchInput" placeholder="Tìm kiếm khách hàng, người chăm sóc...">
            <button id="searchBtn">🔍</button>
        </div>
    </header>

    <div class="filter-box">
        <label for="starFilter">Lọc theo số sao:</label>
        <select id="starFilter">
            <option value="">-- Tất cả --</option>
            <option value="5">5 sao</option>
            <option value="4">4 sao</option>
            <option value="3">3 sao</option>
            <option value="2">2 sao</option>
            <option value="1">1 sao</option>
        </select>
        <button id="resetBtn">↻ Reset</button>
    </div>

    <div class="avg-box">
        ⭐ Trung bình đánh giá: <span id="avgStar">Đang tải...</span>
    </div>

    <table id="ratingTable">
        <thead>
            <tr>
                <th>Mã ĐG</th>
                <th>Khách hàng</th>
                <th>Người chăm sóc</th>
                <th>Số sao</th>
                <th>Nhận xét</th>
                <th>Ngày đánh giá</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <tr><td colspan="7" class="loading">Đang tải dữ liệu...</td></tr>
        </tbody>
    </table>
</main>

<script>
const backendPath = '../../backend/reviews/danhgia.php'; // Load data
const deletePath = '../../backend/reviews/xoa_danh_gia.php'; // Xóa

function loadRatings(keyword='', star=''){
    $('#ratingTable tbody').html('<tr><td colspan="7" class="loading">Đang tải dữ liệu...</td></tr>');
    $.getJSON(backendPath, { keyword, star })
    .done(function(res){
        const tbody = $('#ratingTable tbody');
        tbody.empty();

        if(!res || res.status!=='success'){
            tbody.append('<tr><td colspan="7" class="error">Lỗi: '+(res?.message||'Không xác định')+'</td></tr>');
            $('#avgStar').text('Chưa có đánh giá');
            return;
        }

        const ratings = res.reviews;
        if(ratings.length===0){
            tbody.append('<tr><td colspan="7">Không có đánh giá nào</td></tr>');
            $('#avgStar').text('Chưa có đánh giá');
            return;
        }

        ratings.forEach(r=>{
            tbody.append(`<tr>
                <td>${r.id_danh_gia}</td>
                <td>${r.ten_khach_hang}</td>
                <td>${r.ten_cham_soc}</td>
                <td class="star">${r.so_sao} ⭐</td>
                <td>${r.nhan_xet}</td>
                <td>${r.ngay_danh_gia}</td>
                <td class="action-links">
                    <a href="sua_danh_gia.php?id=${r.id_danh_gia}">✏ Sửa</a> |
                    <span class="delete-link" data-id="${r.id_danh_gia}">🗑 Xóa</span>
                </td>
            </tr>`);
        });

        $('#avgStar').text(res.avg_star+' / 5 ⭐');
    })
    .fail(function(xhr){
        const tbody = $('#ratingTable tbody');
        tbody.empty();
        tbody.append('<tr><td colspan="7" class="error">Lỗi kết nối server: ' + xhr.status + ' - ' + xhr.responseText + '</td></tr>');
        $('#avgStar').text('Chưa có đánh giá');
        console.log("Load error:", xhr); // Debug
    });
}

// Xử lý xóa bằng AJAX
$(document).on('click', '.delete-link', function(){
    const id = $(this).data('id');
    if (!confirm('Bạn có chắc muốn xóa đánh giá này không?')) return;

    $.post(deletePath, { id: id }, function(res){
        if (res.success) {
            alert(res.message);
            loadRatings(); // Reload table sau khi xóa
        } else {
            alert('Lỗi: ' + res.message);
        }
    }, 'json').fail(function(xhr){
        alert('Lỗi kết nối server khi xóa: ' + xhr.status);
        console.log("Delete error:", xhr); // Debug
    });
});

$(document).ready(function(){
    // Load tất cả khi trang load
    loadRatings();

    $('#searchBtn').click(function(){
        const keyword = $('#searchInput').val().trim();
        const star = $('#starFilter').val();
        loadRatings(keyword, star);
    });

    $('#starFilter').change(function(){
        const keyword = $('#searchInput').val().trim();
        const star = $(this).val();
        loadRatings(keyword, star);
    });

    $('#resetBtn').click(function(){
        $('#searchInput').val('');
        $('#starFilter').val('');
        loadRatings();
    });
});
</script>
</body>
</html>