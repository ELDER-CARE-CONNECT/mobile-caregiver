<?php
session_start();

// ===================== KẾT NỐI DATABASE =====================
$servername = "db";          // Tên service MySQL trong docker-compose
$username = "user";          // user docker-compose
$password = "userpassword";  // password docker-compose
$dbname = "caresdb";         // database docker-compose

$conn = mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($conn, "utf8");
if (!$conn) die("Kết nối thất bại: " . mysqli_connect_error());

// ===================== LẤY ID ĐƠN HÀNG HIỆN TẠI =====================
$id_don_hang = 0;
if (isset($_GET['id_don_hang'])) $id_don_hang = intval($_GET['id_don_hang']);
elseif (isset($_POST['id_don_hang'])) $id_don_hang = intval($_POST['id_don_hang']);
if ($id_don_hang <= 0) die("❌ Không có đơn hàng được chọn!");

// ===================== LẤY ID ĐƠN GỐC (ORIGIN) =====================
$origin_don_hang = isset($_GET['origin']) ? intval($_GET['origin']) : $id_don_hang;

// ===================== LẤY THÔNG TIN ĐƠN HÀNG =====================
$stmt = $conn->prepare("
    SELECT id_don_hang, id_khach_hang, id_nguoi_cham_soc, ten_khach_hang
    FROM don_hang
    WHERE id_don_hang = ?
");
$stmt->bind_param("i", $id_don_hang);
$stmt->execute();
$info = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$info) die("Đơn hàng không tồn tại!");

// ===================== Đánh dấu tin nhắn đã xem (tin nhắn từ khách hàng) =====================
$stmt_mark_seen = $conn->prepare("
    UPDATE tin_nhan
    SET da_xem = 1
    WHERE id_don_hang = ? AND nguoi_gui = 'khach_hang' AND da_xem = 0
");
$stmt_mark_seen->bind_param("i", $id_don_hang);
$stmt_mark_seen->execute();
$stmt_mark_seen->close();

// ===================== SESSION =====================
if (!isset($_SESSION['loai_user'])) {
    $_SESSION['loai_user'] = 'cham_soc';
    $_SESSION['id_cham_soc'] = $info['id_nguoi_cham_soc'];
    $_SESSION['id_khach_hang'] = $info['id_khach_hang'];
}

// ===================== THÔNG TIN CHAT =====================
$ten_doi_tuong = $info['ten_khach_hang']; // tên khách hàng
$id_khach_hang = $info['id_khach_hang'];
$id_cham_soc = $info['id_nguoi_cham_soc'];

// ===================== GỬI TIN NHẮN =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $noi_dung = trim($_POST['noi_dung']);
    $nguoi_gui = $_POST['nguoi_gui'];
    if ($noi_dung !== '') {
        $stmt = $conn->prepare("
            INSERT INTO tin_nhan (id_don_hang, id_khach_hang, id_cham_soc, nguoi_gui, noi_dung)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("iiiss", $id_don_hang, $id_khach_hang, $id_cham_soc, $nguoi_gui, $noi_dung);
        echo $stmt->execute() ? "success" : "error";
        $stmt->close();
    } else echo "error";
    exit;
}

// ===================== LOAD TIN NHẮN =====================
if (isset($_GET['action']) && $_GET['action'] === 'get_messages') {
    $stmt = $conn->prepare("SELECT * FROM tin_nhan WHERE id_don_hang=? ORDER BY thoi_gian ASC");
    $stmt->bind_param("i", $id_don_hang);
    $stmt->execute();
    $res = $stmt->get_result();
    $messages = [];
    while ($row = $res->fetch_assoc()) $messages[] = $row;
    echo json_encode($messages);
    $stmt->close();
    exit;
}

// ===================== LẤY DANH SÁCH CHAT (chỉ những đơn có tin nhắn) =====================
$stmt_list = $conn->prepare("
    SELECT dh.id_don_hang, dh.ten_khach_hang,
           SUM(CASE WHEN tn.da_xem=0 AND tn.nguoi_gui='khach_hang' THEN 1 ELSE 0 END) AS chua_xem
    FROM don_hang dh
    INNER JOIN tin_nhan tn ON dh.id_don_hang = tn.id_don_hang
    WHERE dh.id_nguoi_cham_soc = ?
    GROUP BY dh.id_don_hang
    ORDER BY chua_xem DESC, MAX(tn.thoi_gian) DESC
");
$stmt_list->bind_param("i", $_SESSION['id_cham_soc']);
$stmt_list->execute();
$res_list = $stmt_list->get_result();
$chat_orders = [];
while ($row = $res_list->fetch_assoc()) $chat_orders[] = $row;
$stmt_list->close();

?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chat với Khách Hàng</title>
<style>
    html, body {
    overflow-x: hidden; /* ẩn hoàn toàn thanh cuộn ngang */
}

html, body { margin:0; padding:0; height:100%; font-family: Arial, sans-serif; background:#e0e0e0; }
.chat-container-main { display:flex; width:100vw; height:830px; gap:10px; }
.chat-sidebar { width:20%; background:#f7f9fc; border-right:1px solid #ccc; display:flex; flex-direction:column; box-shadow:2px 0 5px rgba(0,0,0,0.05); }
.sidebar-back { padding:15px 10px; text-align:center; background:#007bff; }
.sidebar-back button { background:#0059c9; border:none; color:white; padding:12px 20px; font-size:16px; font-weight:bold; border-radius:8px; cursor:pointer; }
.sidebar-back button:hover { background:#0046a3; }
.sidebar-separator { height:1px; background:#ccc; }
.sidebar-title { padding:12px; font-weight:bold; text-align:center; background:#f0f2f5; border-bottom:1px solid #ddd; }
.chat-list { flex:1; overflow-y:auto; }
.chat-list-item { padding:10px; border-bottom:1px solid #eee; cursor:pointer; transition: background 0.2s; position:relative; }
.chat-list-item:hover { background:#e0e6f1; }
.chat-list-item.active { background:#c8d7ff; font-weight:bold; }
.chat-list-item .badge { position:absolute; right:50px; top:50%; transform:translateY(-50%); background:red; color:white; font-size:12px; font-weight:bold; padding:2px 6px; border-radius:10px; }
.chat-main { 
    width: 1500px;        /* chiều dài (rộng) cố định */
    height: 820px;       /* chiều cao cố định */
    display: flex; 
    flex-direction: column; 
    background: #ffffff; 
    border-radius: 10px; 
    margin: 10px 30px 10px 0; /* trên 10px, phải 30px, dưới 10px, trái 0 */
    box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
}


.chat-header { background:#007bff; color:white; padding:15px; font-size:18px; font-weight:bold; display:flex; justify-content:space-between; align-items:center; border-radius:10px 10px 0 0; }
.chat-messages { flex:1; padding:15px; overflow-y:auto; background:#f4f6f9; }
.msg { max-width:60%; padding:8px 12px; margin-bottom:10px; border-radius:12px; line-height:1.4; word-wrap:break-word; box-shadow:0 1px 3px rgba(0,0,0,0.1); }
.me { background:#c8f0c8; margin-left:auto; text-align:right; }
.them { background:#dbe8ff; margin-right:auto; text-align:left; }
.chat-input { display:flex; padding:10px; border-top:1px solid #ddd; background:#f0f2f5; }
.chat-input input { flex:1; padding:12px; border-radius:8px; border:1px solid #aaa; outline:none; background:#ffffff; }
.chat-input button { padding:12px 20px; margin-left:10px; background:#007bff; color:white; border:none; border-radius:8px; cursor:pointer; font-weight:bold; }
.chat-input button:hover { background:#0059c9; }
</style>
</head>
<?php
  include 'Dieuhuong.php'; 
  ?>
<body>

<div class="chat-container-main">
    <div class="chat-sidebar">
        <div class="sidebar-back">
            <button onclick="window.location='Chitietdonhang.php?id_don_hang=<?php echo $origin_don_hang; ?>'">&larr; Quay lại</button>
        </div>
        <div class="sidebar-separator"></div>
        <div class="sidebar-title">Danh sách chat</div>
        <div class="chat-list" id="chatList">
            <?php foreach($chat_orders as $order): ?>
            <div class="chat-list-item <?php if($order['id_don_hang']==$id_don_hang) echo 'active'; ?>" 
                 onclick="window.location='Chat.php?id_don_hang=<?php echo $order['id_don_hang']; ?>&origin=<?php echo $origin_don_hang; ?>'">
                <span style="font-weight:bold;"><?php echo htmlspecialchars($order['ten_khach_hang']); ?></span>
                <?php if($order['chua_xem']>0): ?>
                <span class="badge"><?php echo $order['chua_xem']; ?></span>
                <?php endif; ?>
                <span style="float:right; color:#555;">#<?php echo $order['id_don_hang']; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="chat-main">
        <div class="chat-header">
            <span id="chatWith"><?php echo htmlspecialchars($ten_doi_tuong); ?></span>
            <span>#<?php echo $id_don_hang; ?></span>
        </div>

        <div class="chat-messages" id="chatMessages"></div>

        <div class="chat-input">
            <input type="text" id="msgInput" placeholder="Nhập tin nhắn...">
            <button onclick="sendMessage()">Gửi</button>
        </div>
    </div>
</div>

<script>
const chat = document.getElementById("chatMessages");
const msgInput = document.getElementById("msgInput");
const id_don_hang_js = <?php echo $id_don_hang; ?>;
const loai_user = "<?php echo $_SESSION['loai_user']; ?>";
const id_khach_hang_js = <?php echo $id_khach_hang; ?>;
const id_cham_soc_js = <?php echo $id_cham_soc; ?>;

function loadMessages() {
    fetch("Chat.php?action=get_messages&id_don_hang=" + id_don_hang_js)
        .then(res => res.json())
        .then(data => {
            chat.innerHTML = "";
            data.forEach(msg => {
                let type = (msg.nguoi_gui === loai_user) ? "me" : "them";
                addMessage(type, msg.noi_dung);
            });
        });
}

function addMessage(type, text){
    const div = document.createElement("div");
    div.className = "msg " + type;
    div.textContent = text;
    chat.appendChild(div);
    chat.scrollTop = chat.scrollHeight;
}

function sendMessage() {
    const text = msgInput.value.trim();
    if(!text) return;

    const form = new FormData();
    form.append("action", "send_message");
    form.append("id_don_hang", id_don_hang_js);
    form.append("id_khach_hang", id_khach_hang_js);
    form.append("id_cham_soc", id_cham_soc_js);
    form.append("noi_dung", text);
    form.append("nguoi_gui", loai_user);

    fetch("Chat.php", { method: "POST", body: form })
        .then(res => res.text())
        .then(data => {
            if(data === "success") {
                msgInput.value = "";
                loadMessages();
            } else console.log("Send error:", data);
        });
}

msgInput.addEventListener("keypress", function(e){
    if(e.key === "Enter") sendMessage();
});

setInterval(loadMessages, 2000);
loadMessages();
</script>

</body>
</html>
