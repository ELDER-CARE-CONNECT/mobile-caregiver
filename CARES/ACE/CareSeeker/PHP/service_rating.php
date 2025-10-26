<?php
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

// Lấy id_cham_soc từ GET
$id_cham_soc = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_cham_soc > 0) {
    $stmt = $pdo->prepare("SELECT ho_ten, hinh_anh FROM nguoi_cham_soc WHERE id_cham_soc = :id");
    $stmt->execute(['id' => $id_cham_soc]);
    $caregiver = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $caregiver = null;
}

if (!$caregiver) {
    echo "Không tìm thấy người chăm sóc.";
    exit;
}

// Xử lý submit đánh giá (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $so_sao = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $nhan_xet = isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : '';
    $id_khach_hang = 1; // Giả sử id_khach_hang từ session, thay bằng $_SESSION['user_id']

    if ($so_sao >= 1 && $so_sao <= 5) {
        $stmt = $pdo->prepare("INSERT INTO danh_gia (id_khach_hang, id_cham_soc, so_sao, nhan_xet) VALUES (:kh, :cs, :sao, :nxet)");
        $stmt->execute(['kh' => $id_khach_hang, 'cs' => $id_cham_soc, 'sao' => $so_sao, 'nxet' => $nhan_xet]);
        echo "<script>alert('Đánh giá đã được gửi!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đánh Giá Dịch Vụ - Elder Care Connect</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #333;
        }
        .caregiver-info {
            margin-bottom: 20px;
        }
        .caregiver-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .stars {
            font-size: 2em;
            color: #ddd;
            cursor: pointer;
        }
        .stars .star {
            display: inline-block;
        }
        .stars .selected {
            color: #ffcc00;
        }
        textarea {
            width: 100%;
            height: 100px;
            margin-top: 10px;
        }
        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Đánh Giá Chất Lượng Dịch Vụ</h1>
        
        <div class="caregiver-info">
            <img src="<?php echo htmlspecialchars($caregiver['hinh_anh'] ?: 'https://via.placeholder.com/150'); ?>" alt="Hình ảnh người chăm sóc" class="caregiver-img">
            <div><strong>Tên người chăm sóc:</strong> <?php echo htmlspecialchars($caregiver['ho_ten']); ?></div>
        </div>
        
        <form method="POST">
            <div class="stars" id="stars">
                <span class="star" data-value="1">&#9733;</span>
                <span class="star" data-value="2">&#9733;</span>
                <span class="star" data-value="3">&#9733;</span>
                <span class="star" data-value="4">&#9733;</span>
                <span class="star" data-value="5">&#9733;</span>
            </div>
            <input type="hidden" id="rating" name="rating" value="0">
            <textarea name="comment" placeholder="Nhận xét của bạn..."></textarea>
            <button type="submit">Gửi Đánh Giá</button>
        </form>
    </div>

    <script>
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating');
        let selectedRating = 0;

        stars.forEach(star => {
            star.addEventListener('click', function() {
                selectedRating = this.dataset.value;
                ratingInput.value = selectedRating;
                stars.forEach(s => {
                    s.classList.toggle('selected', s.dataset.value <= selectedRating);
                });
            });
        });
    </script>
</body>
</html>