<?php
include 'check_login.php';
include 'connect.php';

if (!isset($_GET['id'])) {
    echo "<script>alert('Không có ID đánh giá!'); window.location='danhgia.php';</script>";
    exit;
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM danh_gia WHERE id_danh_gia = $id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<script>alert('Không tìm thấy đánh giá!'); window.location='danhgia.php';</script>";
    exit;
}

$row = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $so_sao = intval($_POST['so_sao']);
    $nhan_xet = $conn->real_escape_string($_POST['nhan_xet']);

    $update = "UPDATE danh_gia SET so_sao = $so_sao, nhan_xet = '$nhan_xet' WHERE id_danh_gia = $id";
    if ($conn->query($update)) {
        echo "<script>alert('✅ Cập nhật đánh giá thành công!'); window.location='danhgia.php';</script>";
    } else {
        echo "<script>alert('❌ Lỗi khi cập nhật!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sửa Đánh Giá</title>
<style>
body {
    font-family: "Segoe UI", sans-serif;
    background: #f4f6fa;
}
.form-container {
    width: 500px;
    margin: 80px auto;
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}
h2 {
    text-align: center;
    color: #007BFF;
}
label {
    font-weight: bold;
}
select, textarea {
    width: 100%;
    padding: 8px;
    margin: 10px 0;
    border-radius: 5px;
    border: 1px solid #ccc;
}
button {
    padding: 10px 20px;
    background: #007BFF;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}
button:hover {
    background: #0056b3;
}
.back-btn {
    display: inline-block;
    margin-top: 15px;
    text-decoration: none;
    color: #333;
}
</style>
</head>

<body>
<div class="form-container">
    <h2>✏ Sửa Đánh Giá</h2>
    <form method="POST">
        <label for="so_sao">Số sao:</label>
        <select name="so_sao" required>
            <?php
            for ($i = 1; $i <= 5; $i++) {
                $selected = ($row['so_sao'] == $i) ? 'selected' : '';
                echo "<option value='$i' $selected>$i sao</option>";
            }
            ?>
        </select>

        <label for="nhan_xet">Nhận xét:</label>
        <textarea name="nhan_xet" rows="4" required><?php echo htmlspecialchars($row['nhan_xet']); ?></textarea>

        <button type="submit">💾 Lưu thay đổi</button>
        <a href="danhgia.php" class="back-btn">⬅ Quay lại</a>
    </form>
</div>
</body>
</html>
