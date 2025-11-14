<?php
session_start();

if (!isset($_SESSION['id_khach_hang'])) {
    header("Location: ../../../Admin/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ cá nhân</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
        }
        body {
            background: #f8f8fa;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        h1 {
            text-align: center;
            color: #FF6B81;
            margin-bottom: 30px;
            font-weight: 700;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-group input:focus,
        .form-group select:focus {
            border-color: #FF6B81;
            outline: none;
        }
        .form-group input[readonly] {
            background-color: #eee;
            cursor: not-allowed;
        }
        .form-row {
            display: flex;
            gap: 20px;
        }
        .form-row>.form-group {
            flex: 1;
        }
        .btn-submit {
            display: block;
            width: 100%;
            padding: 14px;
            background-color: #FF6B81;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            transition: background-color 0.3s;
            margin-top: 30px;
        }
        .btn-submit:hover {
            background-color: #E65A6E;
        }
        .alert-message {
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }
        .alert-error {
            color: #d9534f;
            background-color: #f2dede;
            border: 1px solid #ebccd1;
        }
        .alert-success {
            color: #4CAF50;
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
        }
        .avatar-upload {
            text-align: center;
            margin-bottom: 30px;
        }
        .avatar-box {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #ddd;
            overflow: hidden;
            margin: 0 auto 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f0f0f0;
        }
        .avatar-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Cập nhật Hồ sơ Khách hàng</h1>

        <div id="messageContainer">
        </div>

        <form method="POST" id="profileForm" enctype="multipart/form-data">

            <div class="avatar-upload">
                <label for="avatar">Ảnh đại diện</label>
                <div class="avatar-box" id="avatarBox">
                    <div class="small">Đang tải...</div>
                </div>
                <input type="file" id="avatar" name="avatar" accept="image/*" style="display: none;">
                <label for="avatar" style="cursor: pointer; color: #FF6B81; font-weight: 500;">Chọn ảnh</label>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="so_dt">Số điện thoại <span style="color:red;">(*)</span></label>
                    <input type="text" id="so_dt" name="so_dt" placeholder="Vui lòng nhập số điện thoại (10 số)" required pattern="[0-9]{10}" title="Vui lòng nhập đúng 10 chữ số">
                </div>
                <div class="form-group">
                    <label for="email">Email <span style="color:red;">(*)</span></label>
                    <input type="email" id="email" name="email" placeholder="Nhập địa chỉ email" required>
                </div>
            </div>

            <div class="form-group">
                <label for="ho_ten">Họ và tên <span style="color:red;">(*)</span></label>
                <input type="text" id="ho_ten" name="ho_ten" placeholder="Nhập họ và tên" required>
            </div>

            <div class="form-group">
                <label for="ten_duong">Số nhà, Tên đường <span style="color:red;">(*)</span></label>
                <input type="text" id="ten_duong" name="ten_duong" placeholder="Ví dụ: 123 Nguyễn Văn Linh" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phuong_xa">Phường/Xã <span style="color:red;">(*)</span></label>
                    <input type="text" id="phuong_xa" name="phuong_xa" placeholder="Ví dụ: Phường 1" required>
                </div>
                <div class="form-group">
                    <label for="tinh_thanh">Tỉnh/Thành phố <span style="color:red;">(*)</span></label>
                    <input type="text" id="tinh_thanh" name="tinh_thanh" placeholder="Ví dụ: TP. Hồ Chí Minh" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="tuoi">Tuổi <span style="color:red;">(*)</span></label>
                    <input type="number" id="tuoi" name="tuoi" min="1" max="120" placeholder="Nhập tuổi" required>
                </div>
                <div class="form-group">
                    <label for="gioi_tinh">Giới tính <span style="color:red;">(*)</span></label>
                    <select id="gioi_tinh" name="gioi_tinh" required>
                        <option value="">-- Chọn giới tính --</option>
                        <option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option>
                        <option value="Khác">Khác</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="chieu_cao">Chiều cao (cm)</label>
                    <input type="number" id="chieu_cao" name="chieu_cao" min="50" max="250" placeholder="Chiều cao (cm)">
                </div>
                <div class="form-group">
                    <label for="can_nang">Cân nặng (kg)</label>
                    <input type="number" id="can_nang" name="can_nang" min="10" max="200" placeholder="Cân nặng (kg)">
                </div>
            </div>
            <p style="font-size: 14px; text-align: right; color: #555;"><span style="color:red;">(*)</span> Là các trường bắt buộc.</p>

            <button type="submit" class="btn-submit">Cập nhật Hồ sơ</button>
        </form>
    </div>

    <script>
        const API_URL = '../Backend/api_profile.php';
        const form = document.getElementById('profileForm');
        const messageContainer = document.getElementById('messageContainer');
        const avatarBox = document.getElementById('avatarBox');
        const avatarInput = document.getElementById('avatar');

        async function loadProfileData() {
            try {
                const response = await fetch(API_URL);
                const result = await response.json();

                if (result.success) {
                    const profile = result.profile;
                    document.getElementById('so_dt').value = profile.so_dien_thoai || '';
                    document.getElementById('email').value = profile.email || '';
                    document.getElementById('ho_ten').value = profile.ten_khach_hang || '';
                    document.getElementById('ten_duong').value = profile.ten_duong || '';
                    document.getElementById('phuong_xa').value = profile.phuong_xa || '';
                    document.getElementById('tinh_thanh').value = profile.tinh_thanh || '';
                    document.getElementById('tuoi').value = profile.tuoi || '';
                    document.getElementById('chieu_cao').value = profile.chieu_cao || '';
                    document.getElementById('can_nang').value = profile.can_nang || '';
                    document.getElementById('gioi_tinh').value = profile.gioi_tinh || '';

                    avatarBox.innerHTML = '';
                    const avatarUrl = profile.hinh_anh ? profile.hinh_anh : 'uploads/default.png';

                    if (profile.hinh_anh) {
                        const img = document.createElement('img');
                        img.src = avatarUrl;
                        avatarBox.appendChild(img);
                    } else {
                        avatarBox.innerHTML = '<div class="small">Chưa có ảnh</div>';
                    }
                } else {
                    showMessage('Lỗi tải hồ sơ: ' + (result.message || 'Không tìm thấy dữ liệu.'), 'error');
                }
            } catch (error) {
                showMessage('Lỗi kết nối server khi tải hồ sơ.', 'error');
                console.error('Fetch error:', error);
            }
        }

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            messageContainer.innerHTML = '';

            const formData = new FormData(this);

            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    alert(result.message || 'Cập nhật hồ sơ thành công! Đang chuyển đến trang chủ.');
                    window.location.href = 'index.php';

                } else if (result.errors) {
                    const errorHtml = result.errors.map(err => `<p>⚠️ ${err}</p>`).join('');
                    showMessage(errorHtml, 'error');
                } else {
                    showMessage(result.message || 'Đã xảy ra lỗi không xác định.', 'error');
                }

            } catch (error) {
                showMessage('Lỗi kết nối mạng hoặc lỗi server.', 'error');
                console.error('Submit error:', error);
            }
        });

        function showMessage(message, type) {
            messageContainer.innerHTML = `<div class="alert-message alert-${type}">${message}</div>`;
        }

        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file || !file.type.startsWith('image/')) return;

            const reader = new FileReader();
            reader.onload = function(ev) {
                avatarBox.innerHTML = '';
                const img = document.createElement('img');
                img.src = ev.target.result;
                avatarBox.appendChild(img);
            }
            reader.readAsDataURL(file);
        });

        document.addEventListener('DOMContentLoaded', loadProfileData);
    </script>

</body>
</html>