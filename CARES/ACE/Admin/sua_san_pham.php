<?php include 'check_login.php'; ?>
<?php
include 'connect.php';

// Kiểm tra có ID không
if (!isset($_GET['id_san_pham']) || empty($_GET['id_san_pham'])) {
    die("❌ Thiếu ID sản phẩm cần sửa.");
}

$id = intval($_GET['id_san_pham']);
$row = [
    'ten_san_pham' => '',
    'gia_giam' => '',
    'gia' => '',
    'hinh_anh' => '',
    'loai_san_pham' => ''
];

// Lấy thông tin sản phẩm theo ID
$sql = "SELECT * FROM san_pham WHERE id_san_pham = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result && $result->num_rows === 1) {
    $row = $result->fetch_assoc();
} else {
    die("❌ Sản phẩm không tồn tại.");
}

// Lấy danh sách loại sản phẩm
$sql_loai = "SELECT id_loai, ten_loai FROM loai_san_pham";
$result_loai = $conn->query($sql_loai);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_san_pham = $_POST['ten_san_pham'];
    $gia_giam = $_POST['gia_giam'];
    $gia = $_POST['gia'];
    $loai_san_pham = $_POST['loai_san_pham'];
    $hinh_anh_update = $row['hinh_anh'];

    // Nếu có tải ảnh mới
    if (!empty($_FILES['hinh_anh']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0755, true);

        $file_name = basename($_FILES["hinh_anh"]["name"]);
        $unique_name = time() . "_" . $file_name;
        $target_file = $target_dir . $unique_name;

        if (move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_file)) {
            // Xóa ảnh cũ
            if (!empty($hinh_anh_update) && file_exists($hinh_anh_update)) {
                unlink($hinh_anh_update);
            }
            $hinh_anh_update = $target_file;
        } else {
            echo "❌ Lỗi tải ảnh.";
            exit();
        }
    }

    // Cập nhật vào database
    $update_sql = "UPDATE san_pham 
                   SET ten_san_pham = ?, hinh_anh = ?, gia_giam = ?, gia = ?, loai_san_pham = ?
                   WHERE id_san_pham = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param('ssdiis', $ten_san_pham, $hinh_anh_update, $gia_giam, $gia, $loai_san_pham, $id);
    
    if ($stmt->execute()) {
        header("Location: sanpham.php");
        exit();
    } else {
        echo "❌ Lỗi: " . $conn->error;
    }
}
?>

<!-- Giao diện sửa sản phẩm -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa Sản Phẩm</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            padding: 20px;
        }

        .container {
            max-width: 420px;
            margin: 0 auto;
            background-color: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .back-button {
            position: absolute;
            top: 10px;
            left: 10px;
            text-decoration: none;
            background-color: #e0e0e0;
            color: #333;
            padding: 8px 14px;
            border-radius: 6px;
            font-weight: bold;
        }

        .back-button:hover {
            background-color: #ccc;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #444;
        }

        form input[type="text"],
        form input[type="number"],
        form input[type="file"],
        form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            transition: 0.3s;
        }

        form input:focus,
        form select:focus {
            border-color: #007bff;
            outline: none;
        }

        form input[type="submit"] {
            background-color: rgb(56, 66, 214);
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
        }

        form input[type="submit"]:hover {
            background-color: rgb(74, 86, 194);
        }
    </style>
</head>
<body>
    <a href="sanpham.php" class="back-button">← Quay lại</a>
    <div class="container">
        <h2>Sửa Sản Phẩm</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="ten_san_pham">Tên sản phẩm:</label>
            <input type="text" name="ten_san_pham" value="<?= htmlspecialchars($row['ten_san_pham']) ?>" required>

            <?php if (!empty($row['hinh_anh'])): ?>
                <label>Hình ảnh hiện tại:</label><br>
                <img src="<?= htmlspecialchars($row['hinh_anh']) ?>" width="120"><br><br>
            <?php endif; ?>

            <label for="hinh_anh">Hình ảnh mới:</label>
            <input type="file" name="hinh_anh" accept="image/*"><br><br>

            <label for="gia_giam">Giá Giảm:</label>
            <input type="number" name="gia_giam" value="<?= htmlspecialchars($row['gia_giam']) ?>" required><br><br>

            <label for="gia">Giá:</label>
            <input type="number" name="gia" value="<?= htmlspecialchars($row['gia']) ?>" required><br><br>

            <label for="loai_san_pham">Loại sản phẩm:</label>
            <select name="loai_san_pham" required>
                <?php
                if ($result_loai->num_rows > 0) {
                    while ($loai = $result_loai->fetch_assoc()) {
                        $selected = ($loai['id_loai'] == $row['loai_san_pham']) ? 'selected' : '';
                        echo "<option value='{$loai['id_loai']}' $selected>{$loai['ten_loai']}</option>";
                    }
                }
                ?>
            </select><br><br>

            <input type="submit" value="Cập nhật sản phẩm">
        </form>
    </div>

</body>
</html>
