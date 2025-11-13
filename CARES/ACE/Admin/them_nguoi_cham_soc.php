<?php include 'check_login.php'; ?>
<?php include 'connect.php'; ?>

<?php
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ho_ten = $_POST['ho_ten'];
    $dia_chi = $_POST['dia_chi'];
    $tuoi = $_POST['tuoi'];
    $gioi_tinh = $_POST['gioi_tinh'];
    $chieu_cao = $_POST['chieu_cao'];
    $can_nang = $_POST['can_nang'];
    $kinh_nghiem = $_POST['kinh_nghiem'];
    $so_dien_thoai = $_POST['so_dien_thoai'];
    $mat_khau = $_POST['mat_khau']; // không mã hóa

    // Upload ảnh
    $hinh_anh = "";
    if (!empty($_FILES['hinh_anh']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["hinh_anh"]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $targetFilePath)) {
            $hinh_anh = $targetFilePath;
        }
    }

    // Thêm vào CSDL (bỏ cột danh_gia_tb)
    $sql = "INSERT INTO nguoi_cham_soc 
            (so_dien_thoai, mat_khau, hinh_anh, ho_ten, dia_chi, tuoi, gioi_tinh, chieu_cao, can_nang, kinh_nghiem)
            VALUES ('$so_dien_thoai', '$mat_khau', '$hinh_anh', '$ho_ten', '$dia_chi', '$tuoi', '$gioi_tinh', '$chieu_cao', '$can_nang', '$kinh_nghiem')";

    if ($conn->query($sql)) {
        header("Location: nguoi_cham_soc.php?success=1");
        exit;
    } else {
        $message = "❌ Lỗi: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Thêm Người Chăm Sóc</title>
<style>
body {
    font-family: "Segoe UI", sans-serif;
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    color: #333;
    margin: 0;
    padding: 0;
}
.container {
    width: 600px;
    margin: 50px auto;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    padding: 35px 45px;
    animation: fadeIn 0.6s ease;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-15px); }
    to { opacity: 1; transform: translateY(0); }
}
h1 {
    color: #0d47a1;
    text-align: center;
    margin-bottom: 25px;
    font-size: 26px;
}
form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}
label {
    font-weight: 600;
    color: #0d47a1;
}
input, select {
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #90caf9;
    transition: all 0.3s;
}
input:focus, select:focus {
    border-color: #1e88e5;
    outline: none;
    box-shadow: 0 0 4px #64b5f6;
}
button {
    background: #1e88e5;
    color: white;
    padding: 12px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    cursor: pointer;
    transition: 0.3s;
}
button:hover {
    background: #0d47a1;
    transform: translateY(-1px);
}
.message {
    text-align: center;
    font-weight: bold;
    color: green;
    margin-bottom: 15px;
}
a {
    color: #1e88e5;
    text-decoration: none;
    display: block;
    text-align: center;
    margin-top: 15px;
}
a:hover { text-decoration: underline; }
</style>
</head>

<body>
<div class="container">
    <h1>Thêm Hồ Sơ Người Chăm Sóc</h1>
    <?php if ($message != "") echo "<div class='message'>$message</div>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Số điện thoại:</label>
        <input type="text" name="so_dien_thoai" required>

        <label>Mật khẩu:</label>
        <input type="text" name="mat_khau" required>

        <label>Họ và tên:</label>
        <input type="text" name="ho_ten" required>

        <label>Địa chỉ:</label>
        <input type="text" name="dia_chi">

        <label>Tuổi:</label>
        <input type="number" name="tuoi">

        <label>Giới tính:</label>
        <select name="gioi_tinh">
            <option value="Nam">Nam</option>
            <option value="Nữ">Nữ</option>
        </select>

        <label>Chiều cao (cm):</label>
        <input type="number" step="0.1" name="chieu_cao">

        <label>Cân nặng (kg):</label>
        <input type="number" step="0.1" name="can_nang">

        <label>Kinh nghiệm:</label>
        <input type="text" name="kinh_nghiem">

        <label>Hình ảnh:</label>
        <input type="file" name="hinh_anh" accept="image/*">

        <button type="submit">💾 Lưu hồ sơ</button>
    </form>

    <a href="nguoi_cham_soc.php">⬅ Quay lại danh sách</a>
</div>
</body>
</html>

<?php $conn->close(); ?>
