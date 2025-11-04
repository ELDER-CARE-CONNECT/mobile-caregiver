<?php
include 'connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = $_POST['id_khieu_nai'];
    $phanhoi = trim($_POST['phan_hoi']);

    if (!empty($id) && !empty($phanhoi)) {
        // Cập nhật phản hồi và trạng thái
        $sql = "UPDATE khieu_nai 
                SET phan_hoi = ?, trang_thai = 'Đã giải quyết'
                WHERE id_khieu_nai = ?";
        $stmt = $conn->prepa<?php include 'check_login.php'; ?>
<?php include 'connect.php'; ?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản Lí Khiếu Nại</title>
<style>
body {
    font-family: "Segoe UI", sans-serif;
    background-color: #f0f4f8;
    margin: 0;
}
.container {
    display: flex;
    min-height: 100vh;
}
.main-content {
    flex-grow: 1;
    background: #fff;
    padding: 25px 40px;
    border-radius: 12px;
    margin: 20px;
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
}
h1 {
    color: #007bff;
    border-bottom: 3px solid #007bff;
    padding-bottom: 10px;
    font-size: 22px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
th {
    background: #007bff;
    color: white;
    padding: 10px;
}
td {
    padding: 10px;
    border-bottom: 1px solid #eee;
    text-align: center;
    vertical-align: top;
}
tr:nth-child(even) { background: #f9f9f9; }
tr:hover { background: #eaf4ff; }
textarea {
    width: 90%;
    border-radius: 6px;
    border: 1px solid #ccc;
    padding: 6px;
    resize: vertical;
    transition: border-color 0.3s;
}
textarea:focus {
    border-color: #007bff;
    outline: none;
}
button {
    background-color: #007bff;
    border: none;
    color: white;
    padding: 6px 10px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}
button:hover { background-color: #0056b3; }
.status-pending { color: orange; font-weight: bold; }
.status-done { color: green; font-weight: bold; }
.reply-box {
    background: #eaf4ff;
    border-radius: 8px;
    padding: 8px;
    color: #333;
    font-style: italic;
}
.reply-form {
    display: none;
    margin-top: 5px;
    transition: all 0.3s ease;
}
.reply-form.show {
    display: block;
}
.no-data {
    text-align: center;
    color: #666;
    font-style: italic;
    padding: 20px;
}
/* Responsive */
@media (max-width: 768px) {
    .main-content {
        padding: 15px 20px;
        margin: 10px;
    }
    table {
        font-size: 14px;
    }
    th, td {
        padding: 8px;
    }
    textarea {
        width: 100%;
    }
}
</style>
</head>

<body>
<div class="container">
<?php 
$activePage = 'quanli_khieunai'; 
include 'sidebar.php'; 
?>

<main class="main-content">
    <h1>Trang Quản Lí Khiếu Nại</h1>

    <table>
        <tr>
            <th>Mã</th>
            <th>Đơn hàng</th>
            <th>Khách hàng</th>
            <th>Nội dung</th>
            <th>Ngày gửi</th>
            <th>Trạng thái</th>
            <th>Phản hồi</th>
        </tr>

        <?php
        $sql = "SELECT kn.*, kh.ten_khach_hang 
                FROM khieu_nai kn
                JOIN khach_hang kh ON kn.id_khach_hang = kh.id_khach_hang
                ORDER BY kn.id_khieu_nai DESC";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $id = htmlspecialchars($row['id_khieu_nai']);
                $status = htmlspecialchars($row['trang_thai']);
                $phanhoi = trim($row['phan_hoi']);
                $isDone = ($status == 'Đã giải quyết');

                echo "<tr>
                    <td>{$id}</td>
                    <td>" . htmlspecialchars($row['id_don_hang']) . "</td>
                    <td>" . htmlspecialchars($row['ten_khach_hang']) . "</td>
                    <td>" . htmlspecialchars($row['noi_dung']) . "</td>
                    <td>" . htmlspecialchars($row['ngay_gui']) . "</td>
                    <td class='" . ($isDone ? "status-done" : "status-pending") . "'>{$status}</td>
                    <td>";

                if (!$isDone) {
                    echo "
                        <button onclick='toggleReply($id)'>Phản hồi</button>
                        <form method='post' action='phanhoi_khieunai.php' class='reply-form' id='form_$id'>
                            <input type='hidden' name='id_khieu_nai' value='{$id}'>
                            <textarea name='phan_hoi' rows='2' placeholder='Nhập phản hồi...' required></textarea><br>
                            <button type='submit' onclick='return confirmSubmit()'>Gửi phản hồi</button>
                        </form>";
                } else {
                    echo "<div class='reply-box'><b>Nội dung phản hồi:</b><br>" . htmlspecialchars($phanhoi) . "</div>";
                }

                echo "</td></tr>";
            }
        } else {
            echo "<tr><td colspan='7' class='no-data'>Không có khiếu nại nào.</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</main>
</div>

<script>
function toggleReply(id) {
    const form = document.getElementById('form_' + id);
    form.classList.toggle('show');
}

function confirmSubmit() {
    return confirm('Bạn có chắc chắn muốn gửi phản hồi này?');
}
</script>
</body>
</html>re($sql);
        $stmt->bind_param("si", $phanhoi, $id);

        if ($stmt->execute()) {
            // Chuyển hướng lại trang quản lí
            header("Location: quanli_khieunai.php?success=1");
            exit;
        } else {
            echo "❌ Lỗi khi cập nhật phản hồi: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "⚠️ Thiếu dữ liệu gửi phản hồi.";
    }
}
$conn->close();
?>
