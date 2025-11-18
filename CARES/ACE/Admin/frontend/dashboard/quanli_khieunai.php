<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản Lí Khiếu Nại</title>
<style>
body { font-family:"Segoe UI",sans-serif; background:#f0f4f8; margin:0; }
.container { display:flex; min-height:100vh; }
.main-content { flex-grow:1; background:#fff; padding:25px 40px; border-radius:12px; margin:20px; box-shadow:0 0 10px rgba(0,0,0,0.05); }
h1 { color:#007bff; border-bottom:3px solid #007bff; padding-bottom:10px; font-size:22px; }
table { width:100%; border-collapse:collapse; margin-top:20px; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1); }
th { background:#007bff; color:white; padding:10px; }
td { padding:10px; border-bottom:1px solid #eee; text-align:center; vertical-align:top; }
tr:nth-child(even){background:#f9f9f9;}
tr:hover{background:#eaf4ff;}
textarea { width:90%; border-radius:6px; border:1px solid #ccc; padding:6px; resize:vertical; }
button { background-color:#007bff; border:none; color:white; padding:6px 10px; border-radius:6px; cursor:pointer; transition:0.3s; }
button:hover{background-color:#0056b3;}
.status-pending{color:orange;font-weight:bold;}
.status-done{color:green;font-weight:bold;}
.reply-box{background:#eaf4ff;border-radius:8px;padding:8px;color:#333;font-style:italic;}
.reply-form{display:none;margin-top:5px;transition:all 0.3s;}
.reply-form.show{display:block;}
.no-data{text-align:center;color:#666;font-style:italic;padding:20px;}
</style>
</head>
<body>
<div class="container">
<?php $activePage='quanli_khieunai'; include 'sidebar.php'; ?>
<main class="main-content">
    <h1>Trang Quản Lí Khiếu Nại</h1>
    <table id="khieunaiTable">
        <tr>
            <th>Mã</th>
            <th>Đơn hàng</th>
            <th>Khách hàng</th>
            <th>Nội dung</th>
            <th>Ngày gửi</th>
            <th>Trạng thái</th>
            <th>Phản hồi</th>
        </tr>
    </table>
</main>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
function toggleReply(id){
    $('#form_'+id).toggleClass('show');
}

function loadKhieuNai(){
    $.getJSON('../../backend/quanli/quanli_khieunai.php', function(res){
        const table = $('#khieunaiTable');
        table.find('tr:gt(0)').remove(); // Xóa hàng cũ

        if(!res.success){
            table.append('<tr><td colspan="7">Lỗi tải dữ liệu: '+res.message+'</td></tr>');
            return;
        }

        if(res.data.length===0){
            table.append('<tr><td colspan="7">Không có khiếu nại nào.</td></tr>');
            return;
        }

        res.data.forEach(row=>{
            const isDone = row.trang_thai==='Đã giải quyết';
            let phanhoiHTML = '';

            if(!isDone){
                phanhoiHTML = `
                <button onclick="toggleReply(${row.id_khieu_nai})">Phản hồi</button>
                <form class="reply-form" id="form_${row.id_khieu_nai}" data-id="${row.id_khieu_nai}">
                    <textarea name="phan_hoi" rows="2" placeholder="Nhập phản hồi..." required></textarea><br>
                    <button type="submit">Gửi phản hồi</button>
                </form>`;
            } else {
                phanhoiHTML = `<div class="reply-box"><b>Nội dung phản hồi:</b><br>${row.phan_hoi}</div>`;
            }

            table.append(`
            <tr>
                <td>${row.id_khieu_nai}</td>
                <td>${row.id_don_hang}</td>
                <td>${row.ten_khach_hang}</td>
                <td>${row.noi_dung}</td>
                <td>${row.ngay_gui}</td>
                <td class='${isDone?'status-done':'status-pending'}'>${row.trang_thai}</td>
                <td>${phanhoiHTML}</td>
            </tr>`);
        });
    });
}

// Xử lý gửi phản hồi bằng AJAX
$(document).on('submit', '.reply-form', function(e){
    e.preventDefault();
    const form = $(this);
    const id = form.data('id');
    const phanhoi = form.find('textarea[name="phan_hoi"]').val().trim();

    if(phanhoi===''){
        alert('Vui lòng nhập phản hồi.');
        return;
    }

    $.post('../../backend/quanli/phanhoi_khieunai.php', {
        id_khieu_nai: id,
        phan_hoi: phanhoi
    }, function(res){
        if(res.status==='success'){
            alert(res.message);
            loadKhieuNai(); // Reload bảng
        } else {
            alert(res.message);
        }
    }, 'json');
});

// Load khi mở trang
loadKhieuNai();
</script>
</body>
</html>
