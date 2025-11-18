<?php
session_start();

require_once '../Backend/db_connect.php';

$pdo = get_pdo_connection();

$id_cham_soc = isset($_GET['id_cs']) ? intval($_GET['id_cs']) : 0;
$id_don_hang = isset($_GET['id_dh']) ? intval($_GET['id_dh']) : 0;

$id_khach_hang_session = $_SESSION['id_khach_hang'] ?? 0;
$caregiver = null;
$message = '';

if ($id_khach_hang_session == 0) {
    $message = '<p class="message error"><i class="fas fa-times-circle"></i> Bạn cần đăng nhập để thực hiện chức năng này.</p>';
} elseif ($id_cham_soc > 0 && $id_don_hang > 0) {
    $stmt = $pdo->prepare("SELECT ho_ten, hinh_anh FROM nguoi_cham_soc WHERE id_cham_soc = :id");
    $stmt->execute(['id' => $id_cham_soc]);
    $caregiver = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$caregiver) {
        $message = '<p class="message error"><i class="fas fa-times-circle"></i> Lỗi: Không tìm thấy người chăm sóc.</p>';
    }
} else {
    $message = '<p class="message error"><i class="fas fa-times-circle"></i> Lỗi: Thiếu ID người chăm sóc hoặc ID đơn hàng.</p>';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh Giá Người Chăm Sóc</title>
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
    display: flex; 
    justify-content: center; 
    align-items: center;
    min-height: 100vh; 
    padding: 30px 15px; 
}

.container { 
    background: #fff; 
    border-radius: 16px; 
    padding: 30px; 
    box-shadow: 0 8px 25px rgba(0,0,0,0.1); 
    width: 100%; 
    max-width: 700px; 
    text-align: center;
}

h1 { 
    color: #FF6B81; 
    font-size: 28px; 
    margin-bottom: 5px;
    font-weight: 800; 
    text-align: center;
}

.subtitle {
    text-align: center; 
    font-size: 14px; 
    color: #888;
    margin-bottom: 25px; 
    font-weight: 500;
}

.section-box {
    background: #fff; 
    padding: 20px; 
    border-radius: 12px;
    margin-bottom: 20px; 
    border: 1px solid #f0f0f0;
    text-align: left;
}

.section-title {
    font-size: 18px; 
    font-weight: 700; 
    color: #333;
    border-bottom: 2px solid #FFD8E0; 
    padding-bottom: 10px; 
    margin-bottom: 15px;
}

.icon { 
    margin-right: 8px; 
    color: #FF6B81; 
}

.caregiver-info {
    display: flex; 
    align-items: center; 
    gap: 15px;
    background: #fff7f9; 
    padding: 15px; 
    border-radius: 10px;
}

.caregiver-info img {
    width: 50px; 
    height: 50px; 
    border-radius: 50%;
    object-fit: cover; 
    border: 2px solid #FFD8E0;
}

.caregiver-name { 
    font-weight: 700; 
    color: #FF6B81; 
}

.caregiver-id { 
    font-size: 13px; 
    color: #888; 
}

.buttons { 
    display: flex; 
    flex-wrap: wrap; 
    gap: 10px; 
    margin-top: 30px; 
}

.button {
    padding: 12px 20px; 
    border: none; 
    border-radius: 8px;
    cursor: pointer; 
    transition: background-color 0.3s, transform 0.1s;
    text-decoration: none; 
    color: white; 
    font-size: 15px;
    font-weight: 600; 
    text-align: center; 
    display: inline-flex;
    align-items: center; 
    gap: 8px; 
    width: 100%;
    justify-content: center;
}

.button:active { 
    transform: scale(0.98); 
}

.btn-submit { 
    background-color: #FF6B81; 
}

.btn-submit:hover { 
    background-color: #E55B70; 
}

.btn-back {
    background-color: #9e9e9e; 
    color: white;
    width: auto; 
}

.btn-back:hover { 
    background-color: #757575; 
}

.rating-stars { 
    text-align: center; 
    margin-bottom: 20px; 
}

.star {
    font-size: 40px; 
    color: #ddd; 
    cursor: pointer;
    transition: color 0.2s, transform 0.1s;
    display: inline-block; 
    margin: 0 3px;
}

.star:hover, 
.star.selected { 
    color: #ffca28; 
}

.star:active { 
    transform: scale(1.1); 
}

textarea[name="comment"] {
    width: 100%; 
    min-height: 120px; 
    border: 1px solid #ddd;
    border-radius: 8px; 
    padding: 12px; 
    font-size: 15px;
    line-height: 1.6; 
    font-family: 'Inter', sans-serif;
    margin-bottom: 20px; 
    transition: border-color 0.3s, box-shadow 0.3s;
}

textarea[name="comment"]:focus {
    outline: none; 
    border-color: #FF6B81;
    box-shadow: 0 0 0 3px rgba(255, 107, 129, 0.2);
}

.message {
    padding: 15px 20px; 
    border-radius: 8px; 
    margin-bottom: 20px;
    font-weight: 500; 
    display: flex; 
    align-items: flex-start; 
    gap: 10px;
    justify-content: center; 
    flex-direction: column; 
    text-align: left; 
    width: 100%;
}

.message.success {
    background-color: #e8f5e9; 
    color: #4caf50; 
    border: 1px solid #c8e6c9;
    font-size: 16px; 
    align-items: center; 
}

.message.error {
    background-color: #ffebee; 
    color: #f44336; 
    border: 1px solid #ffcdd2;
}

.countdown-display {
    font-weight: 700; 
    font-size: 2em; 
    color: #FF6B81;
    margin-top: 5px; 
    text-align: center;
    width: 100%; 
    display: block; 
}

.success-wrapper {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px;
    margin-top: 30px;
}

.success-wrapper .btn-back {
    width: fit-content;
    margin-top: 15px;
    background-color: #9e9e9e;
}

.success-text-line {
    display: flex;
    align-items: center;
    text-align: center;
    width: 100%;
    justify-content: center;
}

.hidden { 
    display: none; 
}
    </style>
</head>

<body>
    <div class="container">
        <h1><i class="fas fa-star-half-alt"></i> Viết Đánh Giá</h1>
        <p class="subtitle">Chia sẻ trải nghiệm của bạn về dịch vụ</p>

        <div id="messageContainer">
            <?php if (!empty($message)) echo $message; ?>
        </div>

        <?php if ($caregiver): ?>

        <div class="section-box" id="caregiverInfoBox">
            <div class="section-title"><i class="icon fas fa-user-nurse"></i> Bạn đang đánh giá</div>
            <div class="caregiver-info">
                <img src="<?php echo htmlspecialchars($caregiver['hinh_anh'] ?: 'img/default_avatar.png'); ?>" alt="Avatar">
                <div>
                    <div class="caregiver-name"><?php echo htmlspecialchars($caregiver['ho_ten']); ?></div>
                    <div class="caregiver-id">ID: #<?php echo htmlspecialchars($id_cham_soc); ?></div>
                </div>
            </div>
        </div>

        <div class="section-box" id="ratingFormBox">
            <div class="section-title"><i class="icon fas fa-edit"></i> Đánh giá của bạn</div>
            
            <form id="ratingForm">
                <input type="hidden" name="id_cs" value="<?php echo $id_cham_soc; ?>">
                <input type="hidden" name="id_dh" value="<?php echo $id_don_hang; ?>">
                <input type="hidden" name="action" value="submit_rating">
                <input type="hidden" id="rating" name="rating" value="0">
                
                <div class="rating-stars">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
                
                <textarea name="comment" placeholder="Bạn cảm thấy dịch vụ như thế nào? (Không bắt buộc)"></textarea>
                
                <div class="buttons">
                    <button type="submit" class="button btn-submit"><i class="fas fa-paper-plane"></i> Gửi Đánh Giá</button>
                </div>
            </form>
        </div>

        <?php endif; ?>

        <div class="success-wrapper hidden" id="successMessage">
            <p class="message success">
                <span class="success-text-line">
                    <i class="fas fa-check-circle"></i> Đã đánh giá thành công!
                </span>
                <span class="success-text-line">
                    Bạn sẽ được chuyển hướng về trang chi tiết đơn hàng sau:
                </span>
                <span id="countdown" class="countdown-display">5</span>
            </p>
            <a href="Chitietlichsudonhang.php?id=<?php echo $id_don_hang; ?>" class="button btn-back"><i class="fas fa-list-alt"></i> Chuyển Hướng Ngay</a>
        </div>

    </div>

    <script>
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating');
        let selectedRating = 0;

        stars.forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = this.dataset.value;
                ratingInput.value = selectedRating;
                updateStars();
            });
            star.addEventListener('mouseover', function() {
                stars.forEach(s => {
                    s.style.color = s.dataset.value <= this.dataset.value ? '#ffca28' : '#ddd';
                });
            });
            star.addEventListener('mouseout', function() {
                updateStars();
            });
        });

        function updateStars() {
            stars.forEach(s => {
                if (s.dataset.value <= selectedRating) {
                    s.classList.add('selected');
                    s.style.color = '#ffca28';
                } else {
                    s.classList.remove('selected');
                    s.style.color = '#ddd';
                }
            });
        }
        
        const ratingForm = document.getElementById('ratingForm');
        const messageContainer = document.getElementById('messageContainer');
        const successMessage = document.getElementById('successMessage');
        const formBox = document.getElementById('ratingFormBox');
        const infoBox = document.getElementById('caregiverInfoBox');

        if (ratingForm) {
            ratingForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                const formData = new FormData(ratingForm);
                const submitButton = ratingForm.querySelector('.btn-submit');
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';

                try {
                    const response = await fetch('../Backend/api_rating.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();

                    if (response.ok && result.success) {
                        if (formBox) formBox.classList.add('hidden');
                        if (infoBox) infoBox.classList.add('hidden');
                        messageContainer.innerHTML = '';
                        successMessage.classList.remove('hidden');
                        startCountdown();
                    } else {
                        messageContainer.innerHTML = `<p class="message error"><i class="fas fa-times-circle"></i> ${result.message || 'Lỗi không xác định.'}</p>`;
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi Đánh Giá';
                    }
                } catch (error) {
                    messageContainer.innerHTML = `<p class="message error"><i class="fas fa-times-circle"></i> Lỗi kết nối. Vui lòng thử lại. ${error.message}</p>`;
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi Đánh Giá';
                }
            });
        }

        function startCountdown() {
            var seconds = 5;
            var countdownElement = document.getElementById('countdown');
            var redirectUrl = "Chitietlichsudonhang.php?id=<?php echo $id_don_hang; ?>";

            function updateCountdown() {
                if (countdownElement) {
                    countdownElement.textContent = seconds;
                }
                seconds--;
                
                if (seconds < 0) {
                    window.location.href = redirectUrl;
                } else {
                    setTimeout(updateCountdown, 1000);
                }
            }
            updateCountdown();
        }
    </script>

</body>
</html>
