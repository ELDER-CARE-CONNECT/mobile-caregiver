<?php
$activePage = 'danhgia';
$pageTitle = 'Sửa Đánh Giá';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $pageTitle; ?></title>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<style>
/* ===== Sidebar + Main CSS (giống danhgia.php) ===== */
* { margin:0; padding:0; box-sizing:border-box; font-family:Arial,sans-serif; }
.sidebar { width:250px; background:linear-gradient(180deg,#f8f9fa 0%,#e9ecef 100%); padding:20px; height:100vh; position:fixed; left:0; display:flex; flex-direction:column; }
.sidebar ul { list-style:none; margin-top:10px; padding:0; }
.sidebar ul li { margin:12px 0; }
.sidebar ul li a { display:flex; align-items:center; text-decoration:none; color:#000; font-weight:700; padding:10px 12px; border-radius:8px; transition:0.3s; }
.sidebar ul li.active a, .sidebar ul li a:hover { background:#007bff; color:#fff; transform:translateX(5px); }
.main-content { margin-left:250px; padding:20px; }
.navbar { display:flex; justify-content:space-between; align-items:center; border-bottom:3px solid #3498db; padding-bottom:15px; margin-bottom:10px; }
.navbar h1 { color:#3498db; font-size:22px; font-weight:600; }
.back-btn { background:#3498db; color:white; border:none; padding:7px 12px; border-radius:6px; cursor:pointer; text-decoration:none; }
.back-btn:hover { background:#0056b3; }

/* Form styles */
.form-container { max-width:600px; margin:20px auto; background:white; padding:20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
h2 { text-align:center; color:#3498db; margin-bottom:20px; }
label { font-weight:bold; display:block; margin-top:10px; }
select, textarea { width:100%; padding:8px; margin:5px 0; border-radius:5px; border:1px solid #ccc; }
button[type="submit"] { padding:10px 20px; background:#007bff; color:white; border:none; border-radius:6px; cursor:pointer; margin-top:15px; }
button[type="submit"]:hover { background:#0056b3; }
.loading { text-align:center; color:#666; margin:20px 0; }
.message { text-align:center; margin:10px 0; padding:10px; border-radius:5px; }
.error { background:#f8d7da; color:#721c24; }
.success { background:#d4edda; color:#155724; }
</style>
</head>
<body>

<?php include 'sidebar.php'; ?>

<main class="main-content">
    <header class="navbar">
        <h1>Sửa Đánh Giá Người Chăm Sóc</h1>
        <a href="danhgia.php" class="back-btn">⬅ Quay lại</a>
    </header>

    <div class="form-container">
        <h2>✏ Chỉnh sửa Đánh Giá</h2>
        <div id="loading" class="loading">Đang tải dữ liệu...</div>
        <div id="message" class="message" style="display:none;"></div>
        <form id="updateForm" style="display:none;">
            <input type="hidden" name="id" id="reviewId">
            
            <label for="so_sao">Số sao:</label>
            <select name="so_sao" id="so_sao" required>
                <!-- Options sẽ được load qua JS -->
            </select>

            <label for="nhan_xet">Nhận xét:</label>
            <textarea name="nhan_xet" id="nhan_xet" rows="4" required></textarea>

            <button type="submit">💾 Lưu thay đổi</button>
        </form>
    </div>
</main>

<script>
const backendPath = '../../backend/reviews/sua_danh_gia.php';
const urlParams = new URLSearchParams(window.location.search);
const id = urlParams.get('id');

if (!id) {
    $('#loading').hide();
    $('#message').addClass('error').show().text('ID không hợp lệ!');
} else {
    // Load dữ liệu đánh giá
    $.getJSON(backendPath, { id })
    .done(function(res) {
        $('#loading').hide();
        if (res.status === 'success') {
            const data = res.data;
            $('#reviewId').val(data.id_danh_gia);

            let options = '';
            for (let i = 1; i <= 5; i++) {
                const selected = (data.so_sao == i) ? 'selected' : '';
                options += `<option value="${i}" ${selected}>${i} sao</option>`;
            }
            $('#so_sao').html(options);
            $('#nhan_xet').val(data.nhan_xet);
            $('#updateForm').show();
        } else {
            $('#message').addClass('error').show().text(res.message);
        }
    })
    .fail(function(xhr, status, error) {
        $('#loading').hide();
        $('#message').addClass('error').show().text('Lỗi kết nối server: ' + error);
    });

    // Submit AJAX POST
    $('#updateForm').submit(function(e) {
        e.preventDefault();
        $('#message').hide().removeClass('error success');
        $('#loading').show().text('Đang xử lý...');

        $.post(backendPath, $(this).serialize())
        .done(function(res) {
            $('#loading').hide();
            if (res.status === 'success') {
                $('#message').addClass('success').show().text(res.message);
                setTimeout(() => window.location.href = 'danhgia.php', 1500);
            } else {
                $('#message').addClass('error').show().text(res.message);
            }
        })
        .fail(function(xhr, status, error) {
            $('#loading').hide();
            $('#message').addClass('error').show().text('Lỗi kết nối server: ' + error);
        });
    });
}
</script>

</body>
</html>
