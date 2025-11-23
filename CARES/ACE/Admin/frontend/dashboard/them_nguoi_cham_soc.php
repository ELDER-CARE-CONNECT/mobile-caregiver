<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm Người Chăm Sóc</title>
<style>
body { font-family: "Segoe UI", sans-serif; background: linear-gradient(135deg,#e3f2fd,#bbdefb); margin:0; padding:0; color:#333; }
.container { width:600px; margin:50px auto; background:#fff; border-radius:15px; box-shadow:0 5px 20px rgba(0,0,0,0.15); padding:35px 45px; }
h1 { text-align:center; color:#0d47a1; margin-bottom:25px; font-size:26px; }
form { display:flex; flex-direction:column; gap:12px; }
label { font-weight:600; color:#0d47a1; }
input, select { padding:10px; border-radius:8px; border:1px solid #90caf9; transition:0.3s; }
input:focus, select:focus { border-color:#1e88e5; outline:none; box-shadow:0 0 4px #64b5f6; }
button { background:#1e88e5; color:#fff; padding:12px; border:none; border-radius:8px; font-size:16px; cursor:pointer; transition:0.3s; }
button:hover { background:#0d47a1; transform:translateY(-1px); }
.message { text-align:center; font-weight:bold; margin-top:15px; }
a { color:#1e88e5; text-decoration:none; display:block; text-align:center; margin-top:15px; }
a:hover { text-decoration:underline; }
</style>
</head>
<body>
<div class="container">
    <h1>Thêm Hồ Sơ Người Chăm Sóc</h1>

    <form id="addForm" enctype="multipart/form-data">
        <label>Số điện thoại:</label>
        <input type="text" name="so_dien_thoai" required>

        <label>Mật khẩu:</label>
        <input type="text" name="mat_khau" required>

        <label>Họ và tên:</label>
        <input type="text" name="ho_ten" required>

        <label>Địa chỉ:</label>
        <input type="text" name="dia_chi">

        <label>Tuổi:</label>
        <input type="number" name="tuoi">

        <label>Giới tính:</label>
        <select name="gioi_tinh">
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
        </select>

        <label>Chiều cao (cm):</label>
        <input type="number" step="0.1" name="chieu_cao">

        <label>Cân nặng (kg):</label>
        <input type="number" step="0.1" name="can_nang">

        <label>Kinh nghiệm:</label>
        <input type="text" name="kinh_nghiem">

        <label>Tiền theo giờ (VNĐ):</label>
        <input type="number" step="0.01" name="tong_tien_kiem_duoc" value="0">

        <label>Hình ảnh:</label>
        <input type="file" name="hinh_anh" accept="image/*">

        <button type="submit">💾 Lưu hồ sơ</button>
    </form>

    <div id="message" class="message"></div>
    <a href="nguoi_cham_soc.php">⬅ Quay lại danh sách</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function(){
    $('#addForm').submit(function(e){
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
            url:'../../backend/user/them_nguoi_cham_soc.php',
            type:'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success:function(res){
                if(res.status === 'success'){
                    $('#message').css('color','green').text(res.message + ' Đang chuyển về danh sách...');
                    // reset form
                    $('#addForm')[0].reset();
                    // tự động quay lại sau 1.5 giây
                    setTimeout(function(){
                        window.location.href = 'nguoi_cham_soc.php';
                    }, 1500);
                } else {
                    $('#message').css('color','red').text(res.message);
                }
            },
            error:function(xhr){
                $('#message').css('color','red').text('Có lỗi xảy ra: '+xhr.status+' '+xhr.statusText);
            }
        });
    });
});
</script>

</body>
</html>
