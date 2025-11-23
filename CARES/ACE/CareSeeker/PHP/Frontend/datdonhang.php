<?php
session_start();

$id_khach_hang_session = $_SESSION['id_khach_hang'] ?? 0;
if ($id_khach_hang_session === 0) {
    header("Location: ../../../Admin/login.php"); 
    exit();
}

$id_cham_soc_get = 0;
if (isset($_GET['id'])) $id_cham_soc_get = intval($_GET['id']);
elseif (isset($_POST['id_nguoi_cham_soc'])) $id_cham_soc_get = intval($_POST['id_nguoi_cham_soc']);

if ($id_cham_soc_get <= 0) {
    echo "<h2 style='text-align:center;color:red;'>ID người chăm sóc không hợp lệ.</h2>";
    exit;
}

function generateTimeOptions() {
    $options = '';
    for ($h = 0; $h < 24; $h++) {
        for ($m = 0; $m < 60; $m += 30) {
            $time_24 = sprintf("%02d:%02d", $h, $m);
            $time_ampm = date("g:i A", strtotime($time_24));
            $options .= "<option value=\"$time_ampm\">$time_ampm</option>";
        }
    }
    return $options;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đặt dịch vụ</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
* { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
body { background: #FFF9FA; color: #333; overflow-x: hidden; line-height: 1.6;} 

.container { 
    max-width: 1000px; 
    margin: 40px auto; 
    background: #fff; 
    border-radius: 16px; 
    padding: 40px; 
    box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
}
h1 { 
    text-align: center; 
    color: #FF6B81; 
    font-size: 32px;
    margin-bottom: 30px;
    font-weight: 800;
}
form label { 
    display: block; 
    margin: 15px 0 8px; 
    font-weight: 600; 
    color: #444;
}
.row { 
    display: flex; 
    gap: 20px; 
    margin-bottom: 20px;
    flex-wrap: wrap; 
}
.row > div {
    flex: 1;
    min-width: 250px;
}
.date-time-pair {
    display: flex;
    gap: 20px;
    width: 100%;
    margin-bottom: 20px;
}
.date-time-pair > div {
    flex: 1;
    min-width: 45%;
}

select, input:not(#tongTien), input#hoTen, input#diaChi, input#soDienThoai, input[type="date"] { 
    width: 100%; 
    padding: 12px; 
    height: 48px; 
    border: 1px solid #FFD8E0; 
    border-radius: 10px; 
    box-sizing: border-box; 
    font-size: 16px;
    transition: all 0.3s;
}
select:focus, input:focus {
    border-color: #FF6B81;
    box-shadow: 0 0 0 3px rgba(255, 107, 129, 0.15); 
    outline: none;
}
#tongTien {
    background: #fff;
    font-size: 20px;
    color: #FF6B81 !important; 
    font-weight: 700 !important;
    border: 1px solid #FFD8E0; 
}
.btn-row { 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    margin-top: 30px; 
}
.btn-confirm { 
    background: #FF6B81; 
    color: #fff; 
    border: none; 
    padding: 15px 30px; 
    border-radius: 10px; 
    font-weight: 700; 
    cursor: pointer;
    font-size: 18px;
    transition: background 0.3s;
}
.btn-confirm:hover { background: #E55B70; } 
.btn-back { 
    background: none; 
    border: 2px solid #FFD8E0; 
    padding: 10px 20px;
    border-radius: 10px;
    color: #444; 
    cursor: pointer;
    font-size: 16px;
    font-weight: 500;
    transition: background 0.3s, border-color 0.3s;
}
.btn-back:hover { 
    background: #FFF0F3; 
    border-color: #FF6B81;
}
.summary { 
    background: #fff7f9; 
    padding: 25px; 
    border-radius: 12px; 
    margin-bottom: 30px; 
    box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
    border-left: 5px solid #ff6b81; 
}
.summary h3 {
    color: #333;
    margin-top: 0;
    margin-bottom: 15px;
    border-bottom: 1px dashed #FFD8E0; 
    padding-bottom: 10px;
    font-weight: 700;
}
.summary p strong {
    color: #ff6b81; 
}
.summary img {
    border-radius: 8px;
    object-fit: cover;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.error-box { 
    background: #FFF0F3; 
    border: 1px solid #FFB4C4; 
    color: #9B1C3C; 
    padding: 15px; 
    border-radius: 8px; 
    margin-bottom: 20px; 
    font-weight: 500;
}
.btn-remove-service {
    background: #FF6B81;
    color: #fff;
    border: none;
    padding: 10px 15px;
    border-radius: 10px;
    font-weight: 700;
    cursor: pointer;
    width: 48px;
    flex-shrink: 0;
    transition: background 0.3s;
}
.btn-remove-service:hover {
    background: #E55B70;
}
.service-input-group button.btn-remove-service {
    display: flex; 
    align-items: center; 
    justify-content: center;
}
.service-input-group button.btn-remove-service:hover {
    background: #E55B70 !important; 
}

.spinner-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.7); display: flex; justify-content: center; align-items: center; z-index: 1001; }
.spinner { border: 4px solid #f3f3f3; border-top: 4px solid var(--accent); border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; }
@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
.hidden { display: none; }
</style>
</head>
<body>

<div id="loadingSpinner" class="spinner-overlay hidden">
    <div class="spinner"></div>
</div>

<div class="container">
    <h1> Đặt dịch vụ chăm sóc</h1>

    <div id="errorContainer" class="error-box hidden"></div>

    <div class="summary" style="display: flex; align-items: flex-start; gap: 30px;">
        <div style="flex: 2;">
            <h3>Thông tin người chăm sóc</h3>
            <p><strong>Họ tên:</strong> <span id="summaryHoTen">Đang tải...</span></p>
            <p><strong>Kinh nghiệm:</strong> <span id="summaryKinhNghiem">...</span></p>
            <p><strong>Đánh giá:</strong> <span id="summaryDanhGia">...</span></p>
            <p><strong>Giá tiền/giờ:</strong> 
                <span style="color:#FF6B81; font-weight:700;" id="summaryGiaTien">
                    0 đ/giờ
                </span>
            </p>
        </div>
        <div style="flex: 1; text-align: center;">
            <img src="img/default_avatar.png" 
                 alt="Ảnh người chăm sóc" 
                 width="200" height="200"
                 id="summaryHinhAnh">
        </div>
    </div>
    <form id="bookingForm" method="post">
        <input type="hidden" name="id_nguoi_cham_soc" value="<?php echo $id_cham_soc_get; ?>">
        
        <input type="hidden" name="tong_tien" id="tong_tien_input">
        <input type="hidden" name="ngay_bat_dau" id="ngay_bat_dau_input">
        <input type="hidden" name="ngay_ket_thuc" id="ngay_ket_thuc_input">
        <input type="hidden" name="gio_bat_dau" id="gio_bat_dau_input">
        <input type="hidden" name="gio_ket_thuc" id="gio_ket_thuc_input">
        <input type="hidden" name="phuong_thuc" id="phuong_thuc_input">
        <input type="hidden" name="ten_khach_hang" id="ten_khach_hang_input">
        <input type="hidden" name="so_dien_thoai" id="so_dien_thoai_input">
        <input type="hidden" name="dia_chi" id="dia_chi_input"> 

        <label><i class="fas fa-list-alt"></i> Chọn dịch vụ/Nhiệm vụ cụ thể:</label>
        
        <div id="serviceInputs">
            <div class="service-input-group" id="group-1" style="margin-bottom: 15px;">
                <label for="dich_vu_1" style="font-weight: 500;">Nhiệm vụ 1:</label>
                <div style="display: flex; gap: 10px;">
                    <input type="text" id="dich_vu_1" name="dich_vu[]" placeholder="Ví dụ: Hỗ trợ tắm rửa, Nấu ăn theo chế độ" required
                           style="flex-grow: 1; width: 100%; padding: 12px; height: 48px; border: 1px solid #FFD8E0; border-radius: 10px; box-sizing: border-box; font-size: 16px;">
                    <button type="button" class="btn-remove-service" style="visibility: hidden; width: 48px; flex-shrink: 0; background: none; border: none; padding: 0;"><i class="fas fa-minus"></i></button>
                </div>
            </div>
        </div>
        <button type="button" id="addServiceBtn" style="background: #FF6B81; color: #fff; border: none; padding: 10px 15px; border-radius: 8px; font-weight: 600; cursor: pointer; margin-bottom: 20px; font-size: 15px;">
            <i class="fas fa-plus"></i> Thêm Nhiệm Vụ Khác
        </button>

        <label><i class="fas fa-calendar-alt"></i> Chọn thời gian dịch vụ:</label>
        
        <div class="date-time-pair">
            <div>
                <label for="startDate">Ngày bắt đầu:</label>
                <input type="date" id="startDate" required> 
            </div>
            
            <div>
                <label for="startHour">Giờ bắt đầu:</label>
                <select id="startHour" required name="select_start_hour">
                    <option value="">Chọn giờ</option>
                    <?php echo generateTimeOptions(); ?>
                </select>
            </div>
        </div>

        <div class="date-time-pair">
            <div>
                <label for="endDate">Ngày kết thúc:</label>
                <input type="date" id="endDate" required>
            </div>

            <div>
                <label for="endHour">Giờ kết thúc:</label>
                <select id="endHour" required name="select_end_hour">
                    <option value="">Chọn giờ</option>
                    <?php echo generateTimeOptions(); ?>
                </select>
            </div>
        </div>
        <hr style="border:0; border-top: 1px dashed #FFD8E0; margin: 25px 0;">

        <label><i class="fas fa-user-circle"></i> Hồ sơ đặt</label>
        <select id="profileSelect">
            <option value="own" selected>Sử dụng hồ sơ của tôi (Đang tải...)</option>
            <option value="new">Đặt hộ người khác</option>
        </select>

        <div id="customProfile" style="display:none; margin-top:10px">
            <label for="hoTen">Họ và tên người nhận dịch vụ</label>
            <input type="text" id="hoTen" placeholder="Nhập họ tên">
            <label for="diaChi">Địa chỉ nhận dịch vụ</label>
            <input type="text" id="diaChi" placeholder="Nhập địa chỉ chi tiết">
            <label for="soDienThoai">Số điện thoại liên hệ</label>
            <input type="text" id="soDienThoai" placeholder="Nhập số điện thoại">
        </div>

        <div style="margin-top:25px" class="form-group">
            <label for="tongTien"><i class="fas fa-money-bill-wave"></i> Tổng tiền (ước tính)</label>
            <input type="text" id="tongTien" value="0 đ" readonly>
        </div>

        <div style="margin-top:12px" class="form-group">
            <label for="payment"><i class="far fa-credit-card"></i> Phương thức thanh toán</label>
            <select id="payment">
                <option value="Tiền mặt">Tiền mặt khi hoàn thành dịch vụ</option>
                <option value="vnpay">Thanh toán VNPAY (Thẻ/QR)</option>
            </select>
        </div>

        <div class="btn-row">
            <button type="submit" name="submit_booking" class="btn-confirm"><i class="fas fa-check-circle"></i> Xác nhận đặt dịch vụ</button>
            <button type="button" class="btn-back" onclick="window.history.back()"><i class="fas fa-arrow-left"></i> Quay lại</button>
        </div>
    </form>

    </div>

<footer>
    © 2025 Elder Care Connect | Mang yêu thương đến từng mái ấm 💖
</footer>


<script>
let pricePerHour = 0;
let userInfo = null; 

const CAREGIVER_ID = <?php echo $id_cham_soc_get; ?>;

const GATEWAY_URL = '../Backend/api_gateway.php';
const API_CAREGIVER_DETAIL = `${GATEWAY_URL}?route=caregiver/details&id=${CAREGIVER_ID}&action=get_details`;
const API_PROFILE_GET = `${GATEWAY_URL}?route=profile`;
const API_ORDER_CREATE = `${GATEWAY_URL}?route=order/create`;

// ==========================================
// ĐÃ ĐỒNG BỘ LOGIC ẢNH TỪ Chitietdonhang.php
// ==========================================
function processCaregiverImage(path) {
    let hinh_anh_url = 'img/default_avatar.png'; // Ảnh mặc định
    
    if (path && path.trim() !== '') {
        // 1. Nếu là link online (http/https) -> Giữ nguyên
        if (path.startsWith('http')) {
            return path;
        }

        // 2. LOGIC MỚI: Chỉ lấy tên file và ghép vào đường dẫn chuẩn
        // Lấy tên file cuối cùng (loại bỏ mọi đường dẫn thừa trong DB)
        let filename = path.split(/[\\/]/).pop();

        // Ghép vào đường dẫn folder upload chuẩn của hệ thống
        // Lưu ý: Folder uploads của bạn đang có 's' hay không thì check lại trên server, 
        // code dưới đây theo đúng Chitietdonhang.php (có 's')
        hinh_anh_url = `../../../Admin/frontend/uploads/${filename}`;
    }
    return hinh_anh_url;
}
// ==========================================

async function loadInitialData() {
    try {
        const [caregiverRes, profileRes] = await Promise.all([
            fetch(API_CAREGIVER_DETAIL),
            fetch(API_PROFILE_GET)
        ]);

        if (!caregiverRes.ok) throw new Error('Không thể tải thông tin người chăm sóc.');
        if (!profileRes.ok) throw new Error('Không thể tải thông tin hồ sơ của bạn.');
        
        const caregiverResult = await caregiverRes.json();
        const profileResult = await profileRes.json();

        if (caregiverResult.success) {
            const caregiver = caregiverResult.caregiver;
            pricePerHour = parseFloat(caregiver.tong_tien_kiem_duoc) || 0; 
            
            document.getElementById('summaryHoTen').textContent = caregiver.ho_ten;
            document.getElementById('summaryKinhNghiem').textContent = caregiver.kinh_nghiem;
            document.getElementById('summaryDanhGia').textContent = `⭐ ${caregiver.danh_gia_tb}/5`;
            document.getElementById('summaryGiaTien').textContent = pricePerHour.toLocaleString('vi-VN') + " đ/giờ";
            
            // SỬ DỤNG HÀM XỬ LÝ ẢNH ĐÃ ĐỒNG BỘ
            const imageUrl = processCaregiverImage(caregiver.hinh_anh);
            document.getElementById('summaryHinhAnh').src = imageUrl;

        } else {
            throw new Error(caregiverResult.message || 'Lỗi tải NCS.');
        }

        if (profileResult.success) {
            userInfo = profileResult.profile;
            const profileOption = document.querySelector('#profileSelect option[value="own"]');
            profileOption.textContent = `Sử dụng hồ sơ của tôi (${userInfo.ten_khach_hang})`;
        } else {
            throw new Error(profileResult.message || 'Lỗi tải hồ sơ.');
        }

    } catch (error) {
        showError(error.message);
    }
}

function showError(message) {
    const errorContainer = document.getElementById('errorContainer');
    errorContainer.innerHTML = `<li><i class="fas fa-exclamation-triangle"></i> ${message}</li>`;
    errorContainer.classList.remove('hidden');
}

function parseDateTime(dateStr, timeStr) {
    if (!dateStr || !timeStr) return null;
    const [time, ampm] = timeStr.split(' ');
    const [hourStr, minuteStr] = time.split(':');
    let hour = parseInt(hourStr);
    const minute = parseInt(minuteStr);
    if (ampm === "PM" && hour !== 12) hour += 12;
    else if (ampm === "AM" && hour === 12) hour = 0; 
    const dateTimeStr = `${dateStr}T${String(hour).padStart(2,'0')}:${String(minute).padStart(2,'0')}:00`;
    return new Date(dateTimeStr);
}

function calcTotal() {
    const startDateVal = document.getElementById("startDate").value;
    const endDateVal = document.getElementById("endDate").value;
    const startHourVal = document.getElementById("startHour").value;
    const endHourVal = document.getElementById("endHour").value;
    const tongTienEl = document.getElementById("tongTien");

    if (!startDateVal || !endDateVal || !startHourVal || !endHourVal || pricePerHour === 0) {
        tongTienEl.value = "0 đ";
        return 0;
    }

    const start = parseDateTime(startDateVal, startHourVal);
    const end = parseDateTime(endDateVal, endHourVal);

    if (!start || !end) {
        tongTienEl.value = "0 đ";
        return 0;
    }

    const diffMs = end - start;
    if (diffMs <= 0) {
        tongTienEl.value = "Giờ kết thúc phải sau giờ bắt đầu";
        return 0;
    }

    const diffHours = diffMs / (1000 * 60 * 60);
    const total = diffHours * pricePerHour;
    tongTienEl.value = Math.round(total).toLocaleString('vi-VN') + " đ";
    return total;
}

let serviceCount = 1;

function updateRemoveButtonVisibility() {
    const allGroups = document.querySelectorAll('#serviceInputs .service-input-group');
    const isMultiple = allGroups.length > 1;
    allGroups.forEach(group => {
        const removeBtn = group.querySelector('.btn-remove-service');
        if (removeBtn) {
            if (isMultiple) {
                removeBtn.style.visibility = 'visible';
                removeBtn.style.background = '#FF6B81'; 
                removeBtn.style.border = 'none';
                removeBtn.style.padding = '10px 15px';
            } else {
                removeBtn.style.visibility = 'hidden';
                removeBtn.style.background = 'none';
                removeBtn.style.border = 'none';
                removeBtn.style.padding = '0';
            }
        }
    });
}
function updateServiceLabels() {
    const groups = document.querySelectorAll('#serviceInputs .service-input-group');
    groups.forEach((group, index) => {
        const label = group.querySelector('label');
        if (label) {
            label.textContent = `Nhiệm vụ ${index + 1}:`;
        }
    });
    serviceCount = groups.length; 
    updateRemoveButtonVisibility();
}
function createServiceInput() {
    serviceCount++;
    const container = document.getElementById('serviceInputs');
    const divGroup = document.createElement('div');
    divGroup.className = 'service-input-group';
    divGroup.style.marginBottom = '15px';
    divGroup.id = 'group-' + serviceCount;
    const label = document.createElement('label');
    label.htmlFor = 'dich_vu_' + serviceCount;
    label.textContent = `Nhiệm vụ ${serviceCount}:`;
    label.style.fontWeight = '500';
    const inputWrapper = document.createElement('div');
    inputWrapper.style.display = 'flex';
    inputWrapper.style.gap = '10px';
    const input = document.createElement('input');
    input.type = 'text';
    input.id = 'dich_vu_' + serviceCount;
    input.name = 'dich_vu[]'; 
    input.placeholder = 'Nhập tên nhiệm vụ (Ví dụ: Đưa đi khám bệnh)';
    input.required = true;
    input.style.cssText = 'flex-grow: 1; width: 100%; padding: 12px; height: 48px; border: 1px solid #FFD8E0; border-radius: 10px; box-sizing: border-box; font-size: 16px;';
    const removeBtn = document.createElement('button');
    removeBtn.type = 'button';
    removeBtn.className = 'btn-remove-service';
    removeBtn.innerHTML = '<i class="fas fa-minus"></i>';
    removeBtn.title = 'Xóa nhiệm vụ';
    removeBtn.style.cssText = 'width: 48px; flex-shrink: 0; background: #FF6B81; color: #fff; border: none; border-radius: 10px; font-weight: 700; cursor: pointer; transition: background 0.3s;';
    removeBtn.onclick = function() {
        if (container.childElementCount > 1) {
            container.removeChild(divGroup);
            updateServiceLabels();
        } else {
             alert("Phải có ít nhất một Nhiệm vụ cụ thể để đặt dịch vụ.");
        }
    };
    inputWrapper.appendChild(input);
    inputWrapper.appendChild(removeBtn);
    divGroup.appendChild(label);
    divGroup.appendChild(inputWrapper);
    container.appendChild(divGroup);
    updateServiceLabels();
}
document.getElementById("addServiceBtn").addEventListener("click", createServiceInput);

document.querySelectorAll("#startDate, #endDate, #startHour, #endHour")
    .forEach(el => el.addEventListener("change", calcTotal));

document.getElementById("profileSelect").addEventListener("change", function(){
    document.getElementById("customProfile").style.display =
    this.value === "new" ? "block" : "none";
});

document.addEventListener('DOMContentLoaded', function() {
    loadInitialData(); 
    
    const container = document.getElementById('serviceInputs');
    const firstGroup = document.getElementById('group-1');
    const firstRemoveBtn = firstGroup ? firstGroup.querySelector('.btn-remove-service') : null;
    if (firstRemoveBtn) {
        firstRemoveBtn.onclick = function() {
            if (container.childElementCount > 1) {
                container.removeChild(firstGroup);
                updateServiceLabels();
            } else {
                 alert("Phải có ít nhất một Nhiệm vụ cụ thể để đặt dịch vụ.");
            }
        };
    }
    updateRemoveButtonVisibility(); 
});

const bookingForm = document.getElementById("bookingForm");
const loadingSpinner = document.getElementById('loadingSpinner');

bookingForm.addEventListener("submit", async function(e){
    e.preventDefault(); 
    showError(""); 
    
    const total = Math.round(calcTotal());
    
    const serviceInputs = document.querySelectorAll('#serviceInputs input[name="dich_vu[]"]');
    let hasValidService = false;
    serviceInputs.forEach(input => {
        if (input.value.trim() !== '') { hasValidService = true; }
    });
    if (!hasValidService) {
        alert("Vui lòng nhập ít nhất một Nhiệm vụ cụ thể.");
        return;
    }
    if (total <= 0) {
        alert("Vui lòng chọn ngày/giờ hợp lệ để tính tổng tiền.");
        return;
    }
    
    document.getElementById("tong_tien_input").value = total;
    document.getElementById("ngay_bat_dau_input").value = document.getElementById("startDate").value;
    document.getElementById("ngay_ket_thuc_input").value = document.getElementById("endDate").value;
    document.getElementById("gio_bat_dau_input").value = document.getElementById("startHour").value;
    document.getElementById("gio_ket_thuc_input").value = document.getElementById("endHour").value;
    document.getElementById("phuong_thuc_input").value = document.getElementById("payment").value;
    
    if (document.getElementById("profileSelect").value === "new") {
        const ten = document.getElementById("hoTen").value.trim();
        const diachi = document.getElementById("diaChi").value.trim();
        const sdt = document.getElementById("soDienThoai").value.trim();
        if (!ten || !diachi || !sdt) {
            alert("Vui lòng nhập đầy đủ Họ tên, Địa chỉ và SĐT của người được đặt hộ.");
            return;
        }
        document.getElementById("ten_khach_hang_input").value = ten;
        document.getElementById("dia_chi_input").value = diachi;
        document.getElementById("so_dien_thoai_input").value = sdt;
    } else {
        document.getElementById("ten_khach_hang_input").value = "";
        document.getElementById("dia_chi_input").value = "";
        document.getElementById("so_dien_thoai_input").value = "";
    }
    
    loadingSpinner.classList.remove('hidden');
    
    try {
        const formData = new FormData(bookingForm);
        const response = await fetch(API_ORDER_CREATE, {
            method: 'POST',
            body: formData 
        });

        const result = await response.json();
        
        if (response.ok && result.success) {
            window.location.href = result.redirect_url; 
        } else {
            showError(result.message || 'Đã xảy ra lỗi khi tạo đơn hàng.');
        }
        
    } catch (error) {
        console.error('Lỗi Submit:', error);
        showError('Lỗi kết nối Microservice. Vui lòng thử lại.');
    } finally {
        loadingSpinner.classList.add('hidden');
    }
});
</script>
</body>
</html>