<?php
session_start();

<<<<<<< HEAD
// ===================== KẾT NỐI DATABASE (ĐÃ SỬA CHO DOCKER) =====================
$servername = "db";             // Tên service MySQL trong docker-compose
$username = "user";             // user docker-compose
$password = "userpassword";     // password docker-compose
$dbname = "caresdb";            // database docker-compose
=======
// ===================== KẾT NỐI DATABASE =====================
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sanpham";
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4

$conn = mysqli_connect($servername, $username, $password, $dbname);
mysqli_set_charset($conn, "utf8");
if (!$conn) die("Kết nối thất bại: " . mysqli_connect_error());

// ===================== LẤY ID ĐƠN HÀNG HIỆN TẠI =====================
$id_don_hang = isset($_GET['id_don_hang']) ? intval($_GET['id_don_hang']) : 0;
if ($id_don_hang <= 0) die("❌ Không có đơn hàng được chọn!");

// ===================== LẤY THÔNG TIN ĐƠN HÀNG =====================
$stmt = $conn->prepare("SELECT id_don_hang, id_khach_hang, id_nguoi_cham_soc FROM don_hang WHERE id_don_hang = ?");
$stmt->bind_param("i", $id_don_hang);
$stmt->execute();
$info = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$info) die("Đơn hàng không tồn tại!");

$id_khach_hang = $info['id_khach_hang'];
$id_cham_soc = $info['id_nguoi_cham_soc'];

// ===================== LẤY TÊN NGƯỜI CHĂM SÓC =====================
$ten_doi_tuong = "Người chăm sóc";
if ($id_cham_soc) {
    $stmt = $conn->prepare("SELECT ho_ten FROM nguoi_cham_soc WHERE id_cham_soc = ?");
    $stmt->bind_param("i", $id_cham_soc);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    if ($r && !empty($r['ho_ten'])) $ten_doi_tuong = $r['ho_ten'];
    $stmt->close();
}

// ===================== SESSION USER =====================
if (!isset($_SESSION['loai_user'])) {
    $_SESSION['loai_user'] = 'khach_hang'; // Trang khách hàng
    $_SESSION['id_khach_hang'] = $id_khach_hang;
    $_SESSION['id_cham_soc'] = $id_cham_soc;
}

// ===================== ĐÁNH DẤU TIN NHẮN TỪ CHĂM SÓC ĐÃ XEM =====================
$stmt_mark_seen = $conn->prepare("
    UPDATE tin_nhan 
    SET da_xem = 1 
    WHERE id_don_hang = ? AND nguoi_gui = 'cham_soc' AND da_xem = 0
");
$stmt_mark_seen->bind_param("i", $id_don_hang);
$stmt_mark_seen->execute();
$stmt_mark_seen->close();

// ===================== GỬI TIN NHẮN =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'send_message') {
    $noi_dung = trim($_POST['noi_dung'] ?? '');
    $nguoi_gui = 'khach_hang'; // user hiện tại
    if ($noi_dung !== '') {
        $stmt = $conn->prepare("
            INSERT INTO tin_nhan (id_don_hang, id_khach_hang, id_cham_soc, nguoi_gui, noi_dung, da_xem) 
            VALUES (?, ?, ?, ?, ?, 0)
        ");
        $stmt->bind_param("iiiss", $id_don_hang, $id_khach_hang, $id_cham_soc, $nguoi_gui, $noi_dung);
        echo $stmt->execute() ? "success" : "error";
        $stmt->close();
    } else echo "error";
    exit;
}

// ===================== LOAD TIN NHẮN =====================
if (isset($_GET['action']) && $_GET['action'] === 'get_messages') {
    $stmt = $conn->prepare("SELECT * FROM tin_nhan WHERE id_don_hang = ? ORDER BY thoi_gian ASC");
    $stmt->bind_param("i", $id_don_hang);
    $stmt->execute();
    $res = $stmt->get_result();
    $messages = [];
    while ($row = $res->fetch_assoc()) $messages[] = $row;
    echo json_encode($messages);
    $stmt->close();
    exit;
}

// ===================== DANH SÁCH CHAT =====================
$stmt_list = $conn->prepare("
    SELECT dh.id_don_hang, dh.id_nguoi_cham_soc,
           SUM(CASE WHEN tn.da_xem = 0 AND tn.nguoi_gui = 'cham_soc' THEN 1 ELSE 0 END) AS chua_xem
    FROM don_hang dh
    INNER JOIN tin_nhan tn ON dh.id_don_hang = tn.id_don_hang
    WHERE dh.id_khach_hang = ?
    GROUP BY dh.id_don_hang
    ORDER BY chua_xem DESC, MAX(tn.thoi_gian) DESC
");
$stmt_list->bind_param("i", $id_khach_hang);
$stmt_list->execute();
$res_list = $stmt_list->get_result();
$chat_orders = [];
while ($row = $res_list->fetch_assoc()) {
    $ten_cham_soc = "Người chăm sóc";
    if (!empty($row['id_nguoi_cham_soc'])) {
        $stmt_name = $conn->prepare("SELECT ho_ten FROM nguoi_cham_soc WHERE id_cham_soc = ?");
        $stmt_name->bind_param("i", $row['id_nguoi_cham_soc']);
        $stmt_name->execute();
        $r2 = $stmt_name->get_result()->fetch_assoc();
        if ($r2 && !empty($r2['ho_ten'])) $ten_cham_soc = $r2['ho_ten'];
        $stmt_name->close();
    }
    $row['ten_cham_soc'] = $ten_cham_soc;
    $chat_orders[] = $row;
}
$stmt_list->close();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Chat với Người Chăm Sóc</title>
<style>
html, body {margin:0; padding:0; height:100%; font-family:Arial,sans-serif; background:#e0e0e0;}
.chat-container-main {display:flex; width:100vw; height:100vh; overflow:hidden; gap:10px;}
.chat-sidebar {width:20%; background:#f7f9fc; border-right:1px solid #ccc; display:flex; flex-direction:column;}
.sidebar-back {padding:15px 10px; text-align:center; background:#007bff;}
.sidebar-back button {background:#0059c9; border:none; color:white; padding:12px 20px; font-size:16px; font-weight:bold; border-radius:8px; cursor:pointer;}
.sidebar-back button:hover {background:#0046a3;}
.sidebar-separator {height:1px; background:#ccc;}
.sidebar-title {padding:12px; font-weight:bold; text-align:center; background:#f0f2f5; border-bottom:1px solid #ddd;}
.chat-list {flex:1; overflow-y:auto;}
.chat-list-item {padding:10px; border-bottom:1px solid #eee; cursor:pointer; display:flex; justify-content:space-between; align-items:center; position:relative;}
.chat-list-item.active {background:#c8d7ff; font-weight:bold;}
.chat-list-item .content {display:flex; align-items:center; gap:8px; flex:1; justify-content:space-between;}
.chat-list-item .name {font-weight:bold; text-align:left; flex-shrink:0;}
.chat-list-item .order-info {display:flex; align-items:center; gap:5px;}
.chat-list-item .badge {background:red; color:white; font-size:12px; padding:2px 6px; border-radius:10px;}
.chat-main {flex:1; display:flex; flex-direction:column; background:#fff; border-radius:10px; margin:10px 0;}
.chat-header {background:#007bff; color:white; padding:15px; font-size:18px; font-weight:bold; display:flex; justify-content:space-between; border-radius:10px 10px 0 0;}
.chat-messages {flex:1; padding:15px; overflow-y:auto; background:#f4f6f9;}
.msg {max-width:60%; padding:8px 12px; margin-bottom:10px; border-radius:12px; line-height:1.4; word-wrap:break-word; box-shadow:0 1px 3px rgba(0,0,0,0.1);}
.me {background:#dbe8ff; margin-right:auto; text-align:left;}
.them {background:#c8f0c8; margin-left:auto; text-align:right;}
.chat-input {display:flex; padding:10px; border-top:1px solid #ddd; background:#f0f2f5;}
.chat-input input {flex:1; padding:12px; border-radius:8px; border:1px solid #aaa;}
.chat-input button {padding:12px 20px; margin-left:10px; background:#007bff; color:white; border:none; border-radius:8px; cursor:pointer; font-weight:bold;}
.chat-input button:hover {background:#0059c9;}
@media (max-width:800px) {.chat-sidebar{display:none;}}
</style>
</head>
<body>
<div class="chat-container-main">
    <div class="chat-sidebar">
    <?php
// Lấy ID đơn hiện tại
$id_don_hang = isset($_GET['id_don_hang']) ? intval($_GET['id_don_hang']) : 0;
if($id_don_hang <= 0) die("❌ Không có đơn hàng được chọn!");

// Lấy origin (nơi user bấm chat đến đây)
$origin_don_hang = isset($_GET['origin']) ? intval($_GET['origin']) : $id_don_hang;
?>

<div class="sidebar-back">
    <button onclick="window.location='Chitietlichsudonhang.php?id=<?php echo $origin_don_hang; ?>'">
        &larr; Quay lại
    </button>
</div>

        <div class="sidebar-separator"></div>
        <div class="sidebar-title">Danh sách chat</div>
        <div class="chat-list" id="chatList">
            <?php foreach($chat_orders as $order): ?>
           <div class="chat-list-item <?php if($order['id_don_hang']==$id_don_hang) echo 'active'; ?>"
     onclick="window.location='?id_don_hang=<?php echo $order['id_don_hang']; ?>&origin=<?php echo $origin_don_hang; ?>'">

                <div class="content">
                    <span class="name"><?php echo htmlspecialchars($order['ten_cham_soc']); ?></span>
                    <span class="order-info">
                        <?php if($order['chua_xem']>0): ?>
                        <span class="badge"><?php echo $order['chua_xem']; ?></span>
                        <?php endif; ?>
                        <span>#<?php echo $order['id_don_hang']; ?></span>
                    </span>
                </div>
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
            <button id="sendBtn">Gửi</button>
        </div>
    </div>
</div>

<script>
const chat = document.getElementById("chatMessages");
const msgInput = document.getElementById("msgInput");
const sendBtn = document.getElementById("sendBtn");

const id_don_hang_js = <?php echo $id_don_hang; ?>;
const loai_user = "<?php echo $_SESSION['loai_user']; ?>"; // user hiện tại

function loadMessages() {
    fetch("?action=get_messages&id_don_hang=" + id_don_hang_js)
        .then(res => res.json())
        .then(data => {
            chat.innerHTML = "";
            data.forEach(msg => {
<<<<<<< HEAD
                // Nếu msg.nguoi_gui === user hiện tại → me (bên phải), ngược lại → them (bên trái)
=======
                // Nếu msg.nguoi_gui === user hiện tại → them (bên phải), ngược lại → me (bên trái)
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
                let type = (msg.nguoi_gui === loai_user) ? "me" : "them";
                addMessage(type, msg.noi_dung);
            });
        }).catch(err => console.error(err));
}

function addMessage(type, text) {
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
    form.append("action","send_message");
    form.append("id_don_hang", id_don_hang_js);
    form.append("noi_dung", text);

    fetch("", { method:"POST", body: form })
        .then(res=>res.text())
        .then(data=>{
            if(data==="success") {
                msgInput.value="";
                loadMessages();
            } else console.log("Send error:", data);
        });
}

sendBtn.addEventListener("click", sendMessage);
msgInput.addEventListener("keypress", e => { if(e.key==="Enter") sendMessage(); });

setInterval(loadMessages,2000);
loadMessages();
</script>
</body>
<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
