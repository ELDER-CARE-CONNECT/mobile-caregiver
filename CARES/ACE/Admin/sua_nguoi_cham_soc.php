<?php
include 'connect.php';

if (!isset($_GET['id'])) {
    die("Không có ID người chăm sóc được chọn!");
}

$id = intval($_GET['id']);
$result = $conn->query("SELECT * FROM nguoi_cham_soc WHERE id_cham_soc = $id");
if (!$result || $result->num_rows == 0) {
    die("Không tìm thấy người chăm sóc!");
}
$row = $result->fetch_assoc();

// Xử lý cập nhật thông tin
if (isset($_POST['update'])) {
    $ho_ten = $_POST['ho_ten'];
    $dia_chi = $_POST['dia_chi'];
    $tuoi = $_POST['tuoi'];
    $gioi_tinh = $_POST['gioi_tinh'];
    $chieu_cao = $_POST['chieu_cao'];
    $can_nang = $_POST['can_nang'];
    $kinh_nghiem = $_POST['kinh_nghiem'];

    // Nếu có upload ảnh mới
    $hinh_anh = $row['hinh_anh']; // giữ ảnh cũ
    if (!empty($_FILES['hinh_anh']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir);
        $target_file = $target_dir . basename($_FILES["hinh_anh"]["name"]);
        move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_file);
        $hinh_anh = $target_file;
    }

    $sqlUpdate = "UPDATE nguoi_cham_soc 
                  SET ho_ten='$ho_ten', dia_chi='$dia_chi', tuoi='$tuoi', gioi_tinh='$gioi_tinh',
                      chieu_cao='$chieu_cao', can_nang='$can_nang', kinh_nghiem='$kinh_nghiem', hinh_anh='$hinh_anh'
                  WHERE id_cham_soc = $id";

    if ($conn->query($sqlUpdate)) {
        echo "<script>alert('✅ Cập nhật thành công!'); window.location='nguoi_cham_soc.php';</script>";
    } else {
        echo "<script>alert('❌ Lỗi khi cập nhật!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Sửa Người Chăm Sóc</title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f4f6fa;
    padding: 30px;
}
form {
    background: #fff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 0 8px rgba(0,0,0,0.1);
    width: 450px;
    margin: auto;
}
h2 {
    color: #007BFF;
    text-align: center;
}
input, select {
    width: 100%;
    padding: 8px;
    margin: 6px 0;
    border: 1px solid #ccc;
    border-radius: 4px;
}
button {
    width: 100%;
    padding: 10px;
    background: #28a745;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
button:hover {
    background: #1e7e34;
}
img {
    display: block;
    margin: 10px auto;
    width: 100px;
    height: 100px;
    border-radius: 50%;
}
</style>
</head>

<body>
<h2>Chỉnh Sửa Người Chăm Sóc</h2>
<form method="post" enctype="multipart/form-data">
    <label>Họ tên:</label>
    <input type="text" name="ho_ten" value="<?php echo $row['ho_ten']; ?>" required>

    <label>Địa chỉ:</label>
    <input type="text" name="dia_chi" value="<?php echo $row['dia_chi']; ?>">

    <label>Tuổi:</label>
    <input type="number" name="tuoi" value="<?php echo $row['tuoi']; ?>">

    <label>Giới tính:</label>
    <select name="gioi_tinh">
        <option value="Nam" <?php if ($row['gioi_tinh'] == 'Nam') echo 'selected'; ?>>Nam</option>
        <option value="Nữ" <?php if ($row['gioi_tinh'] == 'Nữ') echo 'selected'; ?>>Nữ</option>
    </select>

    <label>Chiều cao (cm):</label>
    <input type="number" name="chieu_cao" value="<?php echo $row['chieu_cao']; ?>">

    <label>Cân nặng (kg):</label>
    <input type="number" name="can_nang" value="<?php echo $row['can_nang']; ?>">

    <label>Kinh nghiệm:</label>
    <input type="text" name="kinh_nghiem" value="<?php echo $row['kinh_nghiem']; ?>">

    <label>Ảnh hiện tại:</label>
    <img src="<?php echo $row['hinh_anh']; ?>" alt="Ảnh người chăm sóc">
    <input type="file" name="hinh_anh">

    <button type="submit" name="update">💾 Cập nhật</button>
</form>
</body>
</html>
