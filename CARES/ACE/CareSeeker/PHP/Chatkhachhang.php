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
$stmt = $pdo->prepare("SELECT ho_ten FROM nguoi_cham_soc WHERE id_cham_soc = :id");
$stmt->execute(['id' => $id_cham_soc]);
$caregiver = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat Với Người Chăm Sóc</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .chat-container {
            width: 100%;
            max-width: 500px;
            height: 80vh;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            background-color: #1a73e8;
            color: white;
            padding: 15px;
            text-align: center;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            font-size: 1.2em;
        }
        .chat-messages {
            flex: 1;
            padding: 15px;
            overflow-y: auto;
            background-color: #e9ecef;
        }
        .message {
            max-width: 70%;
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 10px;
            font-size: 0.9em;
        }
        .message.user {
            background-color: #d1e7dd;
            align-self: flex-end;
            color: #0f5132;
        }
        .message.caregiver {
            background-color: #f8f9fa;
            align-self: flex-start;
            color: #333;
        }
        .chat-input {
            padding: 10px;
            border-top: 1px solid #ddd;
            display: flex;
            gap: 10px;
        }
        .chat-input input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .chat-input button {
            padding: 10px 20px;
            background-color: #1a73e8;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .chat-input button:hover {
            background-color: #1557a0;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">Chat Với Người Chăm Sóc: <?php echo htmlspecialchars($caregiver['ho_ten'] ?? 'N/A'); ?></div>
        <div class="chat-messages" id="chat-messages">
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