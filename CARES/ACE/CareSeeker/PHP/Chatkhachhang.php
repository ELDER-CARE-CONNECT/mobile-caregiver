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
    $stmt = $pdo->prepare("SELECT ho_ten FROM nguoi_cham_soc WHERE id_cham_soc = :id");
    $stmt->execute(['id' => $id_cham_soc]);
    $caregiver = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $caregiver = null;
}

if (!$caregiver) {
    echo "Không tìm thấy người chăm sóc.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Với Người Chăm Sóc - Elder Care Connect</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
        }
        .chat-container {
            flex: 1;
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            background-color: #007bff;
            color: white;
            padding: 10px;
            text-align: center;
            font-weight: bold;
        }
        .chat-messages {
            flex: 1;
            padding: 10px;
            overflow-y: auto;
        }
        .message {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .message.user {
            background-color: #dcf8c6;
            align-self: flex-end;
        }
        .message.caregiver {
            background-color: #ffffff;
            border: 1px solid #ddd;
        }
        .chat-input {
            display: flex;
            padding: 10px;
            border-top: 1px solid #ddd;
        }
        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .chat-input button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            margin-left: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">Chat Với Người Chăm Sóc: <?php echo htmlspecialchars($caregiver['ho_ten']); ?></div>
        <div class="chat-messages" id="chat-messages">
            <!-- Tin nhắn mẫu, có thể tích hợp DB để lưu tin nhắn thực tế -->
            <div class="message caregiver">Xin chào! Tôi có thể giúp gì cho bạn hôm nay?</div>
            <div class="message user">Chào, ông tôi cần hỗ trợ tắm rửa.</div>
        </div>
        <div class="chat-input">
            <input type="text" id="message-input" placeholder="Nhập tin nhắn...">
            <button onclick="sendMessage()">Gửi</button>
        </div>
    </div>

    <script>
        function sendMessage() {
            const input = document.getElementById('message-input');
            const message = input.value.trim();
            if (message) {
                const messagesDiv = document.getElementById('chat-messages');
                const newMessage = document.createElement('div');
                newMessage.classList.add('message', 'user');
                newMessage.textContent = message;
                messagesDiv.appendChild(newMessage);
                input.value = '';
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
                
                // Giả lập phản hồi (có thể thay bằng AJAX gửi đến server lưu DB)
                setTimeout(() => {
                    const reply = document.createElement('div');
                    reply.classList.add('message', 'caregiver');
                    reply.textContent = 'Đã nhận tin nhắn. Tôi sẽ hỗ trợ ngay.';
                    messagesDiv.appendChild(reply);
                    messagesDiv.scrollTop = messagesDiv.scrollHeight;
                }, 1000);
            }
        }
    </script>
</body>
</html>
