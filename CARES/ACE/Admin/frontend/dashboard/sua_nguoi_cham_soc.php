<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chỉnh Sửa Người Chăm Sóc</title>
<style>
/* Giữ nguyên CSS cũ */
body { font-family:"Segoe UI",sans-serif; background:linear-gradient(135deg,#e3f2fd,#bbdefb); margin:0; padding:0; }
.main-content { max-width:600px; margin:30px auto; background:#fff; border-radius:15px; box-shadow:0 5px 20px rgba(0,0,0,0.1); padding:30px; }
h2 { text-align:center; color:#0d47a1; margin-bottom:25px; }
form { display:flex; flex-direction:column; gap:12px; }
label { font-weight:600; color:#0d47a1; }
input, select { padding:10px; border-radius:8px; border:1px solid #90caf9; transition:0.3s; }
input:focus, select:focus { border-color:#1e88e5; outline:none; box-shadow:0 0 4px #64b5f6; }
button { background:#1e88e5; color:#fff; padding:12px; border:none; border-radius:8px; font-size:16px; cursor:pointer; transition:0.3s; }
button:hover { background:#0d47a1; transform:translateY(-1px); }
img { display:block; margin:10px auto; width:120px; height:120px; border-radius:50%; object-fit:cover; }
.back-btn { display:inline-block; margin-bottom:15px; padding:8px 12px; background:#3498db; color:white; border-radius:6px; text-decoration:none; font-weight:600; }
.back-btn:hover { background:#2980b9; }
.message { text-align:center; font-weight:bold; margin-top:15px; }
.loading { display:none; text-align:center; color:#1e88e5; font-weight:bold; }
</style>
</head>
<body>
<main class="main-content">
    <a href="nguoi_cham_soc.php" class="back-btn">⬅ Quay lại</a>
    <h2>Chỉnh Sửa Người Chăm Sóc</h2>

    <form id="editForm" enctype="multipart/form-data">
        <input type="hidden" name="id" id="idInput">
        <label>Họ tên:</label>
        <input type="text" name="ho_ten" id="ho_ten" placeholder="Nhập họ và tên" required>
        <label>Địa chỉ:</label>
        <input type="text" name="dia_chi" id="dia_chi" placeholder="Nhập địa chỉ">
        <label>Tuổi:</label>
        <input type="number" name="tuoi" id="tuoi" placeholder="Nhập tuổi">
        <label>Giới tính:</label>
        <select name="gioi_tinh" id="gioi_tinh">
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
        </select>
        <label>Chiều cao (cm):</label>
        <input type="number" step="0.1" name="chieu_cao" id="chieu_cao" placeholder="Nhập chiều cao">
        <label>Cân nặng (kg):</label>
        <input type="number" step="0.1" name="can_nang" id="can_nang" placeholder="Nhập cân nặng">
        <label>Kinh nghiệm:</label>
        <input type="text" name="kinh_nghiem" id="kinh_nghiem" placeholder="Nhập kinh nghiệm">
        <label>Tiền theo giờ (VNĐ):</label>
        <input type="number" step="1000" name="tong_tien_kiem_duoc" id="tong_tien_kiem_duoc" placeholder="Nhập tổng tiền kiếm được">
        <label>Ảnh hiện tại:</label>
        <img id="currentImg" style="display:none;" alt="Ảnh người chăm sóc">
        <input type="file" name="hinh_anh" accept="image/*">
        <button type="submit">💾 Cập nhật</button>
    </form>
    <div id="message" class="message"></div>
    <div id="loading" class="loading">Đang cập nhật...</div>
</main>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
// Lấy ID từ URL
const urlParams = new URLSearchParams(window.location.search);
const id = urlParams.get('id');

// Load dữ liệu người chăm sóc
function loadCaregiver(id){
    $.getJSON(`../../backend/user/nguoi_cham_soc.php`, { id }, function(res){
        if(!res.success){
            alert(res.message || "Lỗi khi tải dữ liệu");
            return;
        }
        const d = res.data;
        $('#idInput').val(d.id_cham_soc);
        $('#ho_ten').val(d.ho_ten);
        $('#dia_chi').val(d.dia_chi);
        $('#tuoi').val(d.tuoi);
        $('#gioi_tinh').val(d.gioi_tinh);
        $('#chieu_cao').val(d.chieu_cao);
        $('#can_nang').val(d.can_nang);
        $('#kinh_nghiem').val(d.kinh_nghiem);
        $('#tong_tien_kiem_duoc').val(d.tong_tien_kiem_duoc);

        if(d.hinh_anh && d.hinh_anh.trim() !== ""){
            $('#currentImg').attr('src', `../../${d.hinh_anh}?t=${Date.now()}`).show();
        } else {
            $('#currentImg').hide();
        }
    }).fail(function(xhr){
        alert("Lỗi khi tải dữ liệu: " + xhr.status);
    });
}

// Submit form cập nhật
$('#editForm').submit(function(e){
    e.preventDefault();
    $('#loading').show();
    const formData = new FormData(this);

    $.ajax({
        url: `../../backend/user/sua_nguoi_cham_soc.php`,
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function(res){
            $('#loading').hide();
            $('#message').text(res.message).css('color', res.success ? 'green' : 'red');
            if(res.success){
                setTimeout(()=>{ window.location.href='nguoi_cham_soc.php'; }, 1000);
            }
        },
        error: function(xhr){
            $('#loading').hide();
            $('#message').text('Lỗi server: '+xhr.status).css('color','red');
        }
    });
});

if(id) loadCaregiver(id);
</script>
</body>
</html>
