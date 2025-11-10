<?php
session_start(); // Khởi tạo session

$host = '127.0.0.1';
$dbname = 'sanpham';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Kết nối DB thất bại: " . $e->getMessage());
}

// Lấy ID người chăm sóc (đã thống nhất dùng id_cs)
$id_cham_soc = isset($_GET['id_cs']) ? intval($_GET['id_cs']) : 0;
// Lấy ID đơn hàng (đã thống nhất dùng id_dh)
$id_don_hang = isset($_GET['id_dh']) ? intval($_GET['id_dh']) : 0;

$id_khach_hang_session = 0;
$caregiver = null;
$message = ''; // Biến lưu thông báo
$is_success_submission = false; // Cờ báo hiệu đánh giá thành công

// 1. Kiểm tra đăng nhập và lấy ID khách hàng từ Session
if (isset($_SESSION['role']) && $_SESSION['role'] === 'khach_hang' && isset($_SESSION['so_dien_thoai'])) {
    $stmt_kh = $pdo->prepare("SELECT id_khach_hang FROM khach_hang WHERE so_dien_thoai = ?");
    $stmt_kh->execute([$_SESSION['so_dien_thoai']]);
    $user = $stmt_kh->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $id_khach_hang_session = $user['id_khach_hang'];
    }
}

// 2. Lấy thông tin người chăm sóc để hiển thị
if ($id_cham_soc > 0) {
    $stmt = $pdo->prepare("SELECT ho_ten, hinh_anh FROM nguoi_cham_soc WHERE id_cham_soc = :id");
    $stmt->execute(['id' => $id_cham_soc]);
    $caregiver = $stmt->fetch(PDO::FETCH_ASSOC);
}

// 3. Xử lý khi form được gửi (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($id_khach_hang_session == 0) {
        $message = '<p class="message error"><i class="fas fa-times-circle"></i> Lỗi: Phiên đăng nhập không hợp lệ.</p>';
    } elseif ($id_cham_soc == 0 || !$caregiver) {
        $message = '<p class="message error"><i class="fas fa-times-circle"></i> Lỗi: Không tìm thấy người chăm sóc.</p>';
    } else {
        $so_sao = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
        $nhan_xet = isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : '';

        if ($so_sao >= 1 && $so_sao <= 5) {
            try {
                // Thêm đánh giá vào CSDL
                $stmt = $pdo->prepare("INSERT INTO danh_gia (id_khach_hang, id_cham_soc, so_sao, nhan_xet) VALUES (:id_kh, :id_cs, :sao, :nx)");
                $stmt->execute([
                    'id_kh' => $id_khach_hang_session,
                    'id_cs' => $id_cham_soc,
                    'sao' => $so_sao,
                    'nx' => $nhan_xet
                ]);
                
                // ĐÁNH DẤU THÀNH CÔNG VÀ CHUẨN BỊ THÔNG BÁO
                $is_success_submission = true;
                
                // === THAY ĐỔI DÒNG NÀY ĐỂ TÁCH PHẦN ĐẾM NGƯỢC RA RIÊNG ===
                $message = '<div class="success-container">
                    <p class="message success">
                        <i class="fas fa-check-circle"></i> Đã đánh giá thành công! Bạn sẽ được chuyển hướng về trang chi tiết đơn hàng sau:
                        <span id="countdown" class="countdown-display">5</span>
                        giây.
                    </p>
                </div>';
                
            } catch (PDOException $e) {
                // Đã loại bỏ UPDATE don_hang, chỉ bắt lỗi INSERT danh_gia
                $message = '<p class="message error"><i class="fas fa-times-circle"></i> Có lỗi xảy ra: '. $e->getMessage() .'</p>';
            }
        } else {
            $message = '<p class="message error"><i class="fas fa-times-circle"></i> Vui lòng chọn ít nhất 1 sao để đánh giá.</p>';
        }
    }
}

// 4. Kiểm tra lỗi ban đầu
if ($id_khach_hang_session == 0 && empty($message)) {
    $message = '<p class="message error"><i class="fas fa-times-circle"></i> Bạn cần đăng nhập để thực hiện chức năng này.</p>';
    $caregiver = null;
} elseif ($id_cham_soc == 0 || !$caregiver && empty($message) && !$is_success_submission) {
    $message = '<p class="message error"><i class="fas fa-times-circle"></i> Không tìm thấy người chăm sóc hợp lệ.</p>';
    $caregiver = null;
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
        /* (CSS CƠ BẢN) */
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { 
            background: #f8f8fa; color: #333; line-height: 1.6; 
            display: flex; justify-content: center; align-items: center;
            min-height: 100vh; padding: 30px 15px; 
        } 
        .container { 
            background: #fff; border-radius: 16px; padding: 30px; 
            box-shadow: 0 8px 25px rgba(0,0,0,0.1); 
            width: 100%; max-width: 700px; 
            text-align: center;
        }
        h1 { 
            color: #FF6B81; font-size: 28px; margin-bottom: 5px;
            font-weight: 800; text-align: center;
        }
        .subtitle {
            text-align: center; font-size: 14px; color: #888;
            margin-bottom: 25px; font-weight: 500;
        }
        .section-box {
            background: #fff; padding: 20px; border-radius: 12px;
            margin-bottom: 20px; border: 1px solid #f0f0f0;
            text-align: left;
        }
        .section-title {
            font-size: 18px; font-weight: 700; color: #333;
            border-bottom: 2px solid #FFD8E0; padding-bottom: 10px; margin-bottom: 15px;
        }
        .icon { margin-right: 8px; color: #FF6B81; }
        .caregiver-info {
            display: flex; align-items: center; gap: 15px;
            background: #fff7f9; padding: 15px; border-radius: 10px;
        }
        .caregiver-info img {
            width: 50px; height: 50px; border-radius: 50%;
            object-fit: cover; border: 2px solid #FFD8E0;
        }
        .caregiver-name { font-weight: 700; color: #FF6B81; }
        .caregiver-id { font-size: 13px; color: #888; }
        .buttons { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 30px; }
        .button {
            padding: 12px 20px; border: none; border-radius: 8px;
            cursor: pointer; transition: background-color 0.3s, transform 0.1s;
            text-decoration: none; color: white; font-size: 15px;
            font-weight: 600; text-align: center; display: inline-flex;
            align-items: center; gap: 8px; width: 100%;
            justify-content: center;
        }
        .button:active { transform: scale(0.98); }
        .btn-submit { background-color: #FF6B81; }
        .btn-submit:hover { background-color: #E55B70; }
        .btn-back {
            background-color: #9e9e9e; color: white;
            width: auto; 
        }
        .btn-back:hover { background-color: #757575; }
        .rating-stars { text-align: center; margin-bottom: 20px; }
        .star {
            font-size: 40px; color: #ddd; cursor: pointer;
            transition: color 0.2s, transform 0.1s;
            display: inline-block; margin: 0 3px;
        }
        .star:hover, .star.selected { color: #ffca28; }
        .star:active { transform: scale(1.1); }
        textarea[name="comment"] {
            width: 100%; min-height: 120px; border: 1px solid #ddd;
            border-radius: 8px; padding: 12px; font-size: 15px;
            line-height: 1.6; font-family: 'Inter', sans-serif;
            margin-bottom: 20px; transition: border-color 0.3s, box-shadow 0.3s;
        }
        textarea[name="comment"]:focus {
            outline: none; border-color: #FF6B81;
            box-shadow: 0 0 0 3px rgba(255, 107, 129, 0.2);
        }
        .message {
            padding: 15px 20px; border-radius: 8px; margin-bottom: 20px;
            font-weight: 500; display: flex; align-items: flex-start; gap: 10px;
            justify-content: center; 
            flex-direction: column; /* Đảm bảo nội dung thông báo xếp dọc */
            text-align: left; /* Căn lề trái cho nội dung chính */
            width: 100%;
        }
        .message.success {
            background-color: #e8f5e9; color: #4caf50; border: 1px solid #c8e6c9;
            font-size: 16px; 
            align-items: center; /* Căn giữa toàn bộ khối success */
        }
        .message.error {
            background-color: #ffebee; color: #f44336; border: 1px solid #ffcdd2;
        }
        /* THAY ĐỔI CSS Ở ĐÂY */
        .countdown-display {
            font-weight: 700; 
            font-size: 2em; /* Tăng kích thước số */
            color: #FF6B81;
            margin-top: 5px; /* Tạo khoảng cách với dòng trên */
            text-align: center;
            width: 100%; /* Chiếm hết chiều rộng để nổi bật */
            display: block; /* Đảm bảo nó là một dòng riêng */
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
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-star-half-alt"></i> Viết Đánh Giá</h1>
        <p class="subtitle">Chia sẻ trải nghiệm của bạn về dịch vụ</p>

        <?php if (!$is_success_submission) echo $message; ?>

        <?php if ($caregiver && !$is_success_submission): // Chỉ hiển thị form nếu có caregiver VÀ CHƯA đánh giá thành công ?>
        
        <div class="section-box">
            <div class="section-title"><i class="icon fas fa-user-nurse"></i> Bạn đang đánh giá</div>
            <div class="caregiver-info">
                <img src="<?php echo htmlspecialchars($caregiver['hinh_anh'] ?: 'https://via.placeholder.com/150/ff6b81/fff?text=CS'); ?>" alt="Avatar">
                <div>
                    <div class="caregiver-name"><?php echo htmlspecialchars($caregiver['ho_ten']); ?></div>
                    <div class="caregiver-id">ID: #<?php echo htmlspecialchars($id_cham_soc); ?></div>
                </div>
            </div>
        </div>

        <div class="section-box">
            <div class="section-title"><i class="icon fas fa-edit"></i> Đánh giá của bạn</div>
            
            <form action="Danhgia.php?id_cs=<?php echo $id_cham_soc; ?>&id_dh=<?php echo $id_don_hang; ?>" method="POST">
                <div class="rating-stars">
                    <span class="star" data-value="1">&#9733;</span>
                    <span class="star" data-value="2">&#9733;</span>
                    <span class="star" data-value="3">&#9733;</span>
                    <span class="star" data-value="4">&#9733;</span>
                    <span class="star" data-value="5">&#9733;</span>
                </div>
                <input type="hidden" id="rating" name="rating" value="0">
                
                <textarea name="comment" placeholder="Bạn cảm thấy dịch vụ như thế nào? (Không bắt buộc)"></textarea>
                
                <div class="buttons">
                    <button type="submit" class="button btn-submit"><i class="fas fa-paper-plane"></i> Gửi Đánh Giá</button>
                </div>
            </form>
        </div>
        
        <?php elseif ($is_success_submission): // HIỂN THỊ KHỐI THÔNG BÁO VÀ SCRIPT ĐẾM NGƯỢC ?>
            <div class="success-wrapper">
                <p class="message success">
                    <span class="success-text-line">
                        <i class="fas fa-check-circle"></i> Đã đánh giá thành công!
                    </span>
                    <span class="success-text-line">
                        Bạn sẽ được chuyển hướng về trang chi tiết đơn hàng sau: 
                    </span>
                    <span id="countdown" class="countdown-display">5</span>
                    <span class="success-text-line" style="margin-top: 5px;"></span>
                </p>
                <a href="ChiTietLichSuDonHang.php?id=<?php echo $id_don_hang; ?>" class="button btn-back"><i class="fas fa-list-alt"></i> Chuyển Hướng Ngay</a>
            </div>
            <script>
                var seconds = 5;
                var countdownElement = document.getElementById('countdown');
                var redirectUrl = "ChiTietLichSuDonHang.php?id=<?php echo $id_don_hang; ?>";

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

                // Bắt đầu đếm ngược
                updateCountdown();
            </script>

        <?php elseif (empty($message)): // Trường hợp lỗi nhưng chưa có message (dự phòng) ?>
            <p class="message error"><i class="fas fa-times-circle"></i> Không thể tải biểu mẫu đánh giá.</p>
            <div class="buttons" style="justify-content: center;">
                <a href="Lichsudonhang.php" class="button btn-back"><i class="fas fa-list-alt"></i> Quay Lại Lịch Sử</a>
            </div>
        <?php endif; ?>

    </div>

    <script>
        // (Javascript giữ nguyên không đổi)
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
                updateStars(); // Quay về trạng thái đã chọn
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
    </script>
</body>
</html>
