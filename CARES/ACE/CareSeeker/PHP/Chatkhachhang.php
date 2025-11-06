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

// Biến để lưu thông tin chat và người chăm sóc
$messages = [];
$caregiver_name = 'Người Chăm Sóc';
if (isset($_SESSION['role']) && $_SESSION['role'] === 'khach_hang' && isset($_SESSION['so_dien_thoai'])) {
    $so_dien_thoai = $_SESSION['so_dien_thoai'];
    $stmt = $pdo->prepare("SELECT id_khach_hang FROM khach_hang WHERE so_dien_thoai = ?");
    $stmt->execute([$so_dien_thoai]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        $id_khach_hang = $user['id_khach_hang'];
        // Lấy thông tin người chăm sóc từ đơn hàng gần nhất
        $stmt_caregiver = $pdo->prepare("
            SELECT ncs.ho_ten, ncs.id_cham_soc
            FROM don_hang dh
            LEFT JOIN nguoi_cham_soc ncs ON dh.id_cham_soc = ncs.id_cham_soc
            WHERE dh.id_khach_hang = ? AND LOWER(dh.trang_thai) = 'đã hoàn thành'
            ORDER BY dh.ngay_dat DESC
            LIMIT 1
        ");
        $stmt_caregiver->execute([$id_khach_hang]);
        $caregiver = $stmt_caregiver->fetch(PDO::FETCH_ASSOC);
        if ($caregiver) {
            $caregiver_name = htmlspecialchars($caregiver['ho_ten']);
            $id_cham_soc = $caregiver['id_cham_soc'];
        }

        // Kiểm tra và lấy tin nhắn, xử lý nếu bảng chat không tồn tại
        try {
            $stmt = $pdo->prepare("SELECT * FROM chat WHERE id_khach_hang = ? AND id_cham_soc = ? ORDER BY thoi_gian ASC");
            $stmt->execute([$id_khach_hang, $id_cham_soc]);
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            if ($e->getCode() === '42S02') {
                // Bảng chat không tồn tại, hiển thị thông báo và tiếp tục
                $messages = [];
            } else {
                die("Lỗi truy vấn: " . $e->getMessage());
            }
        }
    }
}

// Xử lý gửi tin nhắn
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $_POST['message'];
    if (!empty($message) && isset($_SESSION['so_dien_thoai']) && isset($id_cham_soc)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO chat (id_khach_hang, id_cham_soc, noi_dung, thoi_gian) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$id_khach_hang, $id_cham_soc, $message]);
            header("Location: Chatkhachhang.php");
            exit();
        } catch (PDOException $e) {
            if ($e->getCode() === '42S02') {
                die("Bảng chat chưa được tạo. Vui lòng tạo bảng chat trong cơ sở dữ liệu.");
            } else {
                die("Lỗi gửi tin nhắn: " . $e->getMessage());
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat với <?php echo $caregiver_name; ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            min-height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 600px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .chat-header {
            background-color: #00C73C;
            color: white;
            padding: 10px 20px;
            font-size: 1.2em;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .chat-header .back {
            font-size: 1.5em;
            cursor: pointer;
        }
        .chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            background-color: #fff;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .message {
            padding: 10px;
            border-radius: 10px;
            max-width: 60%;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .sent {
            background-color: #e3ffd9;
            margin-left: auto;
            align-self: flex-end;
        }
        .received {
            background-color: #e9ecef;
        }
        .message-content {
            word-wrap: break-word;
        }
        .message-time {
            font-size: 0.6em;
            color: #888;
            align-self: flex-end;
            margin-top: 5px;
        }
        .input-area {
            padding: 10px;
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            gap: 10px;
            border-top: 1px solid #ddd;
        }
        .input-area input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 20px;
            font-size: 16px;
            outline: none;
        }
        .input-area button {
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            background-color: #00C73C;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .input-area button:hover {
            background-color: #00a732;
        }
        @media (max-width: 600px) {
            .container { width: 100%; height: 100vh; }
            .chat-box { padding: 10px; }
            .input-area input { padding: 8px; }
            .input-area button { padding: 8px 15px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="chat-header">
            Chat với <?php echo $caregiver_name; ?>
            <span class="back">←</span>
        </div>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'khach_hang' && isset($_SESSION['so_dien_thoai'])): ?>
            <div class="chat-box">
                <?php if (empty($messages)): ?>
                    <div style="text-align: center; color: #888; padding: 20px;">Chưa có tin nhắn.</div>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="message <?php echo $msg['id_khach_hang'] == $id_khach_hang ? 'sent' : 'received'; ?>">
                            <div class="message-content"><?php echo htmlspecialchars($msg['noi_dung']); ?></div>
                            <div class="message-time"><?php echo htmlspecialchars($msg['thoi_gian']); ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <form method="post" class="input-area">
                <input type="text" name="message" placeholder="Nhập tin nhắn..." required>
                <button type="submit">Gửi</button>
            </form>
        <?php else: ?>
            <div style="color: #d32f2f; text-align: center; padding: 20px;">Bạn cần đăng nhập để sử dụng chat.</div>
        <?php endif; ?>
    </div>
</body>
</html>