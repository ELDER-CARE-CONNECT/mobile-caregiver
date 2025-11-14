<?php
session_start();

if (!isset($_SESSION['id_khach_hang'])) { 

    header("Location: ../../../Admin/login.php"); 
    exit();
}

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Trang cá nhân</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <style>
        :root {
    --accent: #FF6B81;
    --accent-light: #FFE5E8;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --bg-light: #f9fafb;
    --bg-card: #ffffff;
    --shadow-card: 0 4px 12px rgba(0, 0, 0, 0.05);
    --shadow-hover: 0 8px 20px rgba(0, 0, 0, 0.1);
    --radius: 12px;
    --border-color: #e5e7eb;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Inter', sans-serif;
}

body {
    background: var(--bg-light);
    min-height: 100vh;
    padding-top: 50px;
}

.profile-dashboard {
    max-width: 1000px;
    margin: 30px auto 50px;
    padding: 0 15px;
}

.header-banner {
    background: linear-gradient(90deg, #bbded6, #61c0bf);
    color: #fff;
    padding: 40px;
    border-radius: var(--radius);
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
}

.header-banner h1 {
    font-size: 30px;
    font-weight: 700;
    margin-bottom: 5px;
}

.header-banner p {
    font-size: 16px;
    opacity: 0.9;
}

.profile-card {
    background: var(--bg-card);
    padding: 30px;
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-top: -60px;
    position: relative;
    z-index: 10;
    transition: all 0.3s;
}

.avatar-box {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    border: 4px solid var(--accent-light);
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    margin-bottom: 15px;
    background: #f0f0f0;
}

.avatar-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.profile-card h2 {
    font-size: 28px;
    color: var(--text-primary);
    font-weight: 700;
    margin-bottom: 5px;
}

.profile-card span {
    color: var(--text-secondary);
    font-size: 15px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 30px;
}

.details-card,
.health-card {
    background: var(--bg-card);
    padding: 30px;
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
    transition: transform 0.2s;
}

.details-card h3,
.health-card h3 {
    color: var(--text-primary);
    font-size: 22px;
    font-weight: 600;
    margin-bottom: 20px;
    border-bottom: 2px solid var(--accent-light);
    padding-bottom: 10px;
    display: flex;
    align-items: center;
}

.details-card h3 i,
.health-card h3 i {
    color: var(--accent);
    margin-right: 10px;
    font-size: 20px;
}

.info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 15px;
}

.info-item i {
    color: var(--accent);
    margin-right: 15px;
    font-size: 16px;
    margin-top: 3px;
    min-width: 20px;
}

.info-item strong {
    color: var(--text-secondary);
    font-weight: 500;
    min-width: 100px;
}

.info-item span {
    color: var(--text-primary);
    font-weight: 600;
    flex-grow: 1;
}

.metric-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
    gap: 15px;
}

.metric-item {
    background: var(--accent-light);
    padding: 15px;
    border-radius: var(--radius);
    text-align: center;
}

.metric-item i {
    font-size: 24px;
    color: var(--accent);
    margin-bottom: 5px;
}

.metric-item .value {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
}

.metric-item .label {
    font-size: 14px;
    color: var(--text-secondary);
}

.action-buttons {
    display: flex;
    gap: 15px;
    justify-content: center;
    margin-top: 30px;
}

.btn {
    padding: 12px 25px;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    font-weight: 600;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
}

.btn-edit {
    background: var(--accent);
    color: #fff;
}

.btn-complaint {
    background: #fff;
    border: 1px solid var(--accent);
    color: var(--accent);
}

.btn-logout {
    background: #e9ecef;
    color: #333;
}

.hidden {
    display: none !important;
}

#editSection {
    background: var(--bg-card);
    padding: 40px;
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
    margin-top: 20px;
}

#complaintSection {
    background: var(--bg-light);
    padding: 40px;
    border-radius: var(--radius);
    box-shadow: var(--shadow-card);
    margin-top: 20px;
}

#editSection h3,
#complaintSection h3 {
    font-size: 28px;
    color: var(--accent);
    margin-bottom: 25px;
    text-align: center;
}

#editSection label {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 8px;
    display: block;
}

#editSection input,
#editSection select {
    width: 100%;
    font-size: 16px;
    padding: 12px 14px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    margin-top: 0;
    background: #fdfdfd;
    transition: border-color 0.3s, box-shadow 0.3s;
}

#editSection input:focus,
#editSection select:focus {
    border-color: var(--accent);
    outline: none;
    box-shadow: 0 0 0 3px rgba(255, 107, 129, 0.1);
}

.form-group-edit {
    margin-bottom: 22px;
}

.form-row-edit {
    display: flex;
    gap: 20px;
    margin-bottom: 22px;
}

.form-row-edit>div {
    flex: 1;
}

.edit-buttons {
    margin-top: 30px;
    display: flex;
    gap: 10px;
}

.btn-save {
    background: var(--accent);
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    flex: 1;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.3s;
}

.btn-save:hover {
    background-color: #E65A6E;
}

.btn-back {
    background: #f0f0f0;
    color: #333;
    padding: 12px 20px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.3s;
}

.btn-back:hover {
    background-color: #e0e0e0;
}

.edit-avatar-section {
    text-align: center;
    margin-bottom: 30px;
}

.edit-avatar-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    border: 3px solid var(--accent-light);
    overflow: hidden;
    margin: 0 auto 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f0f0f0;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.edit-avatar-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.edit-avatar-label {
    cursor: pointer;
    color: var(--accent);
    font-weight: 600;
    font-size: 16px;
    transition: opacity 0.2s;
}

.edit-avatar-label:hover {
    opacity: 0.7;
}

.order-card {
    background: #fff7f8;
    border: 1px solid #ffe0e4;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 15px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.order-card p {
    font-size: 15px;
    line-height: 1.4;
    margin: 4px 0;
    color: #444;
}

.order-card strong {
    font-weight: 600;
    color: #000;
}

.send-complaint-btn {
    background: var(--accent);
    color: white;
    border: none;
    border-radius: 6px;
    padding: 8px 14px;
    cursor: pointer;
    font-weight: 500;
    transition: opacity 0.2s;
    flex-shrink: 0;
}

.send-complaint-btn.disabled-btn {
    background-color: #ccc;
    cursor: not-allowed;
}

.spinner-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.7);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--accent);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}

.flash-success {
    background: #dff0d8;
    color: #3c763d;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
    text-align: center;
    font-weight: 600;
}

.flash-error {
    background: #f2dede;
    color: #a94442;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
    text-align: center;
    font-weight: 600;
}

.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 2000;
    opacity: 0;
    transition: opacity 0.3s ease;
    visibility: hidden;
    padding: 15px;
}

.modal-overlay:not(.hidden) {
    opacity: 1;
    visibility: visible;
}

.modal-box {
    background: #fff;
    padding: 30px;
    border-radius: var(--radius);
    width: 100%;
    max-width: 500px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    transform: scale(0.9);
    transition: transform 0.3s ease;
}

.modal-overlay:not(.hidden) .modal-box {
    transform: scale(1);
}

.complaint-modal h3 {
    font-size: 24px;
    font-weight: 700;
    color: var(--accent);
    margin-bottom: 10px;
    text-align: left;
}

.complaint-modal p {
    font-size: 15px;
    color: var(--text-secondary);
    margin-bottom: 20px;
    text-align: left;
    line-height: 1.5;
}

.complaint-modal textarea {
    width: 100%;
    height: 150px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 12px;
    font-size: 16px;
    font-family: 'Inter', sans-serif;
    resize: vertical;
    margin-bottom: 10px;
}

.complaint-modal textarea:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(255, 107, 129, 0.1);
}

.modal-error {
    color: #a94442;
    background: #f2dede;
    padding: 10px;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 15px;
    text-align: center;
}

.modal-buttons {
    display: flex;
    gap: 10px;
    margin-top: 15px;
}

.modal-buttons .btn {
    flex: 1;
    padding: 12px;
}
    </style>
</head>
<body>

<div id="loadingSpinner" class="spinner-overlay hidden">
    <div class="spinner"></div>
</div>

<div id="complaintModalOverlay" class="modal-overlay hidden">
    <div class="modal-box complaint-modal">
        <h3 id="complaintModalTitle">Gửi Khiếu Nại</h3>
        <p>Vui lòng nhập nội dung khiếu nại chi tiết cho đơn hàng <strong id="complaintModalOrderId">#...</strong>:</p>
        <textarea id="complaintModalText" placeholder="Ví dụ: Người chăm sóc đến trễ, thái độ không tốt, làm sai nhiệm vụ..."></textarea>
        <div id="complaintModalError" class="modal-error hidden"></div>
        <div class="modal-buttons">
            <button id="btnCancelComplaint" class="btn btn-back">Hủy</button>
            <button id="btnSubmitComplaint" class="btn btn-save">Gửi Khiếu Nại</button>
        </div>
    </div>
</div>

<?php 
include 'navbar.php'; 
?>

<div class="profile-dashboard">
    <div id="flashMessageContainer">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="flash-success">
                ✅ <?php echo htmlspecialchars($_SESSION['success_message']); ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
    </div>

    <div class="header-banner">
        <h1>Quản lý Hồ sơ Cá nhân</h1>
        <p>Kiểm tra thông tin của bạn và theo dõi các chỉ số sức khỏe.</p>
        <br/>
    </div>

    <div id="infoSection">
        <div class="profile-card">
            <div class="avatar-box">
                <img src="uploads/default.png" alt="avatar" id="profileAvatar"> 
            </div>
            <h2 id="profileHoTen">Đang tải...</h2>
            <span>Khách hàng thân thiết</span>
        </div>

        <div class="info-grid">
            <div class="details-card">
                <h3><i class="fas fa-address-card"></i> Thông tin Liên hệ</h3>
                <div class="info-item"><i class="fas fa-map-marker-alt"></i><strong>Địa chỉ:</strong> <span id="profileDiaChi">...</span></div>
                <div class="info-item"><i class="fas fa-envelope"></i><strong>Email:</strong> <span id="profileEmail">...</span></div>
                <div class="info-item"><i class="fas fa-phone-alt"></i><strong>Số điện thoại:</strong> <span id="profileSoDT">...</span></div>
                <div class="info-item"><i class="fas fa-venus-mars"></i><strong>Giới tính:</strong> <span id="profileGioiTinh">...</span></div>
            </div>

            <div class="health-card">
                <h3><i class="fas fa-heartbeat"></i> Chỉ số Sức khỏe</h3>
                <div class="metric-grid">
                    <div class="metric-item"><i class="fas fa-birthday-cake"></i><div class="value" id="profileTuoi">...</div><div class="label">Tuổi</div></div>
                    <div class="metric-item"><i class="fas fa-ruler-vertical"></i><div class="value" id="profileChieuCao">...</div><div class="label">Chiều cao (cm)</div></div>
                    <div class="metric-item"><i class="fas fa-weight"></i><div class="value" id="profileCanNang">...</div><div class="label">Cân nặng (kg)</div></div>
                    <div class="metric-item"><i class="fas fa-chart-bar"></i><div class="value" id="profileBmi">...</div><div class="label">BMI</div></div>
                </div>
            </div>
        </div>

        <div class="action-buttons">
            <button class="btn btn-edit" id="btnEdit"><i class="fas fa-user-edit"></i> Chỉnh sửa hồ sơ</button>
            <button class="btn btn-complaint" id="btnKhieuNai"><i class="fas fa-exclamation-circle"></i> Khiếu nại dịch vụ</button>
            <button class="btn btn-logout" id="btnLogout"><i class="fas fa-sign-out-alt"></i> Đăng xuất</button> 
        </div>
    </div>

    <div id="editSection" class="hidden">
        <h3>Chỉnh sửa Thông tin Cá nhân</h3>
        <form id="profileEditForm" enctype="multipart/form-data"> 
            
            <div class="edit-avatar-section">
                <label for="editAvatarInput">Ảnh đại diện</label>
                <div class="edit-avatar-preview" id="editAvatarPreview">
                    <div class="small">Ảnh hiện tại</div>
                </div>
                <input type="file" id="editAvatarInput" name="avatar" accept="image/*" style="display: none;">
                <label for="editAvatarInput" class="edit-avatar-label">Chọn ảnh mới</label>
            </div>
            <div class="form-row-edit">
                <div class="form-group-edit" style="flex: 1;">
                    <label>Họ và tên</label><input type="text" name="ho_ten" id="editHoTen" value="">
                </div>
                <div class="form-group-edit" style="flex: 1;">
                    <label>Số điện thoại</label><input type="text" name="so_dt" id="editSoDT" value="">
                </div>
            </div>
            <div class="form-group-edit">
                <label>Email</label><input type="email" name="email" id="editEmail" value="">
            </div>
            <div class="form-group-edit">
                <label>Tên đường/Số nhà</label><input type="text" name="ten_duong" id="editTenDuong" value="">
            </div>
            <div class="form-row-edit">
                <div class="form-group-edit" style="flex: 1;">
                    <label>Phường/Xã</label><input type="text" name="phuong_xa" id="editPhuongXa" value="">
                </div>
                <div class="form-group-edit" style="flex: 1;">
                    <label>Tỉnh/Thành phố</label><input type="text" name="tinh_thanh" id="editTinhThanh" value="">
                </div>
            </div>
            <div class="form-row-edit">
                <div class="form-group-edit" style="flex: 1;">
                    <label>Tuổi</label><input type="number" name="tuoi" id="editTuoi" value="">
                </div>
                <div class="form-group-edit" style="flex: 1;">
                    <label>Giới tính</label>
                    <select name="gioi_tinh" id="editGioiTinh">
                        <option value="Nam">Nam</option>
                        <option value="Nữ">Nữ</option>
                        <option value="Khác">Khác</option>
                    </select>
                </div>
            </div>
            <div class="form-row-edit">
                <div class="form-group-edit" style="flex: 1;">
                    <label>Chiều cao (cm)</label><input type="number" name="chieu_cao" id="editChieuCao" value="">
                </div>
                <div class="form-group-edit" style="flex: 1;">
                    <label>Cân nặng (kg)</label><input type="number" name="can_nang" id="editCanNang" value="">
                </div>
            </div>
            
            <div class="edit-buttons">
                <button type="submit" name="update_profile" class="btn btn-save">Lưu thay đổi</button>
                <button type="button" class="btn btn-back" id="btnCancelEdit">Hủy</button>
            </div>
        </form>
    </div>

    <div id="complaintSection" class="hidden">
        <h3><i class="fas fa-bug"></i> Danh sách Đơn hàng & Khiếu nại</h3>
        <div id="complaintListContainer">
            <p style="text-align:center;">Đang tải danh sách đơn hàng...</p>
        </div>
        <button class="btn btn-back" id="backToInfo" style="margin-top: 20px;"><i class="fas fa-arrow-left"></i> Quay lại Hồ sơ</button>
    </div>
</div>

<script>
const PROFILE_UPDATE_API_URL = '../Backend/api_profile.php'; 
const PROFILE_LOAD_API_URL = '../Backend/api_canhan.php'; 
const COMPLAINT_API_URL = '../Backend/api_guikhieunai.php'; 
const AUTH_API_URL = '../Backend/api_auth.php'; 

const infoSection = document.getElementById('infoSection');
const editSection = document.getElementById('editSection');
const complaintSection = document.getElementById('complaintSection');
const btnEdit = document.getElementById('btnEdit');
const btnCancelEdit = document.getElementById('btnCancelEdit');
const btnKhieuNai = document.getElementById('btnKhieuNai');
const btnBackToInfo = document.getElementById('backToInfo');
const btnLogout = document.getElementById('btnLogout');
const profileEditForm = document.getElementById('profileEditForm');
const loadingSpinner = document.getElementById('loadingSpinner');

const complaintModalOverlay = document.getElementById('complaintModalOverlay');
const btnCancelComplaint = document.getElementById('btnCancelComplaint');
const btnSubmitComplaint = document.getElementById('btnSubmitComplaint');
const complaintModalText = document.getElementById('complaintModalText');
const complaintModalOrderId = document.getElementById('complaintModalOrderId');
const complaintModalError = document.getElementById('complaintModalError');


let cachedProfileData = null; 

function showLoading(show) {
    loadingSpinner.classList.toggle('hidden', !show);
}


function calculateBMI(height, weight) {
    if (height > 0 && weight > 0) {
        const heightM = height / 100;
        return (weight / (heightM * heightM)).toFixed(1);
    }
    return '...';
}


function renderProfileData(profile) {
    if (!profile) return;

    let rawPath = profile.hinh_anh;
    let avatarUrl;

    if (rawPath) {
        if (rawPath.startsWith('Frontend/uploads/avatars/')) {
            avatarUrl = rawPath.substring(9); 
        } else {

            avatarUrl = rawPath;
        }
    } else {
  
        avatarUrl = 'uploads/avatars/default.png'; 
    }

    document.getElementById('profileAvatar').src = avatarUrl;
    
    document.getElementById('profileHoTen').textContent = profile.ten_khach_hang || 'Chưa cập nhật';
    
    const dia_chi = [profile.ten_duong, profile.phuong_xa, profile.tinh_thanh].filter(Boolean).join(', ');
    document.getElementById('profileDiaChi').textContent = dia_chi || 'Chưa cập nhật';
    document.getElementById('profileEmail').textContent = profile.email || 'Chưa cập nhật';
    document.getElementById('profileSoDT').textContent = profile.so_dien_thoai || 'Chưa cập nhật';
    document.getElementById('profileGioiTinh').textContent = profile.gioi_tinh || 'Chưa cập nhật';
 
    document.getElementById('profileTuoi').textContent = profile.tuoi || '...';
    document.getElementById('profileChieuCao').textContent = profile.chieu_cao || '...';
    document.getElementById('profileCanNang').textContent = profile.can_nang || '...';
    document.getElementById('profileBmi').textContent = calculateBMI(profile.chieu_cao, profile.can_nang);

    document.getElementById('editHoTen').value = profile.ten_khach_hang || '';
    document.getElementById('editSoDT').value = profile.so_dien_thoai || '';
    document.getElementById('editEmail').value = profile.email || '';
    document.getElementById('editTenDuong').value = profile.ten_duong || '';
    document.getElementById('editPhuongXa').value = profile.phuong_xa || '';
    document.getElementById('editTinhThanh').value = profile.tinh_thanh || '';
    document.getElementById('editTuoi').value = profile.tuoi || '';
    document.getElementById('editGioiTinh').value = profile.gioi_tinh || 'Nam';
    document.getElementById('editChieuCao').value = profile.chieu_cao || '';
    document.getElementById('editCanNang').value = profile.can_nang || '';

    const editAvatarPreview = document.getElementById('editAvatarPreview');
    editAvatarPreview.innerHTML = '';
    
    const img = document.createElement('img');
    img.src = avatarUrl; 
    editAvatarPreview.appendChild(img);
    
    if (!profile.hinh_anh) {
        editAvatarPreview.innerHTML = '<div class="small">Chưa có ảnh</div>';
    }
}

function renderComplaintList(orders) {
    const container = document.getElementById('complaintListContainer');
    if (orders.length === 0) {
        container.innerHTML = '<p style="padding: 15px; background: #fff; border-radius: 8px; text-align: center;">⚠️ Không có đơn hàng nào (Đã hoàn thành/Đã hủy) để khiếu nại.</p>';
        return;
    }

    container.innerHTML = orders.map(order => {
        const hasComplaint = order.da_khieu_nai; 
        const buttonHtml = hasComplaint
            ? `<button class="send-complaint-btn disabled-btn" disabled><i class="fas fa-check"></i> Đã Khiếu Nại</button>`
            : `<button class="send-complaint-btn" data-id="${order.id_don_hang}"><i class="fas fa-exclamation-circle"></i> Khiếu nại</button>`;

        return `
            <div class="order-card">
                <div>
                    <p><strong>Mã đơn hàng:</strong> #${order.id_don_hang}</p>
                    <p><strong>Ngày đặt:</strong> ${new Date(order.ngay_dat).toLocaleDateString('vi-VN')}</p>
                    <p><strong>Trạng thái:</strong> ${order.trang_thai}</p>
                </div>
                ${buttonHtml}
            </div>
        `;
    }).join('');


    attachComplaintListeners();
}


async function loadData() {
    showLoading(true);
    try {

        const response = await fetch(PROFILE_LOAD_API_URL);
        
        if (!response.ok) {
            const result = await response.json();
            alert(`Lỗi tải dữ liệu: ${result.message}`);
            if(response.status === 401) {
                window.location.href = '../../../Admin/login.php';
            }
            return;
        }
        
        const result = await response.json();
        
        if (result.success) {
            cachedProfileData = result.profile;
            renderProfileData(result.profile);
            renderComplaintList(result.orders_for_complaint);
        } else {
            alert(`Lỗi tải dữ liệu: ${result.message}`);
        }
    } catch (error) {
        alert(`Lỗi kết nối server khi tải hồ sơ.`);
        console.error('Fetch error:', error);
    } finally {
        showLoading(false);
    }
}

btnEdit.addEventListener('click', () => {
    infoSection.classList.add('hidden');
    editSection.classList.remove('hidden');
    complaintSection.classList.add('hidden');
});
btnCancelEdit.addEventListener('click', () => {
    editSection.classList.add('hidden');
    infoSection.classList.remove('hidden');
});
btnKhieuNai.addEventListener('click', () => {
    infoSection.classList.add('hidden');
    editSection.classList.add('hidden');
    complaintSection.classList.remove('hidden');
});
backToInfo.addEventListener('click', () => {
    complaintSection.classList.add('hidden');
    infoSection.classList.remove('hidden');
});

btnLogout.addEventListener('click', async () => {
    if (!confirm('Bạn có chắc chắn muốn đăng xuất?')) return;
    
    showLoading(true);
    try {
        const response = await fetch(`${AUTH_API_URL}?action=logout`);
        const result = await response.json();
        if (result.success) {
            alert('Đã đăng xuất thành công.');
            window.location.href = result.redirect_url; 
        }
    } catch (error) {
        alert('Lỗi đăng xuất. Vui lòng thử lại.');
    } finally {
        showLoading(false);
    }
});


profileEditForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    showLoading(true);
    const formData = new FormData(profileEditForm);

    try {
        const response = await fetch(PROFILE_UPDATE_API_URL, {
            method: 'POST',
            body: formData 
        });
        const result = await response.json();

        if (response.ok && result.success) {
            alert('Cập nhật hồ sơ thành công!');
            await loadData(); 
            editSection.classList.add('hidden');
            infoSection.classList.remove('hidden'); 
            
        } else if (result.errors) {
            const errorHtml = result.errors.map(err => `• ${err}`).join('\n');
            alert(`Lỗi: Vui lòng kiểm tra lại thông tin:\n\n${errorHtml}`);
        } else {
            alert(`Lỗi cập nhật: ${result.message}`);
        }
    } catch (error) {
        alert(`Lỗi kết nối: ${error.message}`);
    } finally {
        showLoading(false);
    }
});

btnCancelComplaint.addEventListener('click', () => {
    complaintModalOverlay.classList.add('hidden');
});

btnSubmitComplaint.addEventListener('click', async () => {
    const idDon = btnSubmitComplaint.dataset.id;
    const reason = complaintModalText.value.trim();

    if (reason === "") {
        complaintModalError.textContent = 'Vui lòng nhập nội dung khiếu nại.';
        complaintModalError.classList.remove('hidden');
        return;
    }

    complaintModalOverlay.classList.add('hidden');
    showLoading(true);

    const data = new URLSearchParams();
    data.append('id_don_hang', idDon);
    data.append('noi_dung', reason);
    data.append('action', 'submit_complaint');
    try {
        const response = await fetch(COMPLAINT_API_URL, {
            method: 'POST',
            body: data
        });
        const result = await response.json();

        if (result.success) {
            alert(`✅ Gửi khiếu nại thành công cho đơn hàng #${idDon}`);
            await loadData();
        } else {
            alert(`❌ Lỗi khi gửi khiếu nại: ${result.message || 'Không rõ lỗi.'}`);
        }
    } catch (error) {
        alert(`Lỗi kết nối: ${error.message}`);
    } finally {
        showLoading(false);
    }
});

function attachComplaintListeners() {
    document.querySelectorAll('.send-complaint-btn').forEach(btn => {
        btn.replaceWith(btn.cloneNode(true));
    });
    
    document.querySelectorAll('.send-complaint-btn').forEach(btn => {
        if (btn.disabled) return; 
        
        btn.addEventListener('click', (e) => {
            const targetBtn = e.currentTarget;
            const idDon = targetBtn.dataset.id;
            
            complaintModalOrderId.textContent = `#${idDon}`;
            complaintModalText.value = ''; 
            complaintModalError.classList.add('hidden');
            btnSubmitComplaint.dataset.id = idDon; 
            complaintModalOverlay.classList.remove('hidden');
        });
    });
}

const editAvatarInput = document.getElementById('editAvatarInput');
const editAvatarPreview = document.getElementById('editAvatarPreview');

editAvatarInput.addEventListener('change', function(e){
    const file = e.target.files[0];
    if (!file || !file.type.startsWith('image/')) return;
    
    const reader = new FileReader();
    reader.onload = function(ev){
        editAvatarPreview.innerHTML = '';
        const img = document.createElement('img');
        img.src = ev.target.result;
        editAvatarPreview.appendChild(img);
    }
    reader.readAsDataURL(file);
});


document.addEventListener('DOMContentLoaded', loadData);

</script>
</body>
</html>
