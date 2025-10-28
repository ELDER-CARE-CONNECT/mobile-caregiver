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

$id_cham_soc = isset($_GET['id']) ? intval($_GET['id']) : 0;
$stmt = $pdo->prepare("SELECT ho_ten, hinh_anh FROM nguoi_cham_soc WHERE id_cham_soc = :id");
$stmt->execute(['id' => $id_cham_soc]);
$caregiver = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $so_sao = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $nhan_xet = isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : '';
    $id_khach_hang = 1; // Giả sử, thay bằng session
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
    <title>Đánh Giá Dịch Vụ</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            text-align: center;
        }
        h1 {
            color: #1a73e8;
            margin-bottom: 20px;
        }
        .caregiver-info img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #ddd;
        }
        .caregiver-info div {
            margin: 10px 0;
            color: #333;
        }
        .stars {
            font-size: 2em;
            color: #ddd;
            margin: 10px 0;
            cursor: pointer;
        }
        .star {
            display: inline-block;
            transition: color 0.3s;
        }
        .star:hover, .star.selected {
            color: #ffca28;
        }
        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            resize: vertical;
            margin-top: 10px;
        }
        button {
            padding: 10px 20px;
            background-color: #1a73e8;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
        }
        button:hover {
            background-color: #1557a0;
        }
        @media (max-width: 500px) {
            .container { padding: 10px; }
            .caregiver-info img { width: 100px; height: 100px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Đánh Giá Dịch Vụ</h1>
        <div class="caregiver-info">
            <img src="<?php echo htmlspecialchars($caregiver['hinh_anh'] ?: 'https://via.placeholder.com/150'); ?>" alt="Hình ảnh người chăm sóc">
            <div><strong>Tên người chăm sóc:</strong> <?php echo htmlspecialchars($caregiver['ho_ten'] ?? 'N/A'); ?></div>
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
            star.addEventListener('mouseover', function() {
                stars.forEach(s => {
                    s.style.color = s.dataset.value <= this.dataset.value ? '#ffca28' : '#ddd';
                });
            });
            star.addEventListener('mouseout', function() {
                stars.forEach(s => {
                    s.style.color = s.classList.contains('selected') ? '#ffca28' : '#ddd';
                });
            });
        });
    </script>
</body>
</html>