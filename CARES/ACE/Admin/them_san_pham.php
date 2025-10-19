<?php include 'check_login.php'; ?>
<?php
include 'connect.php';

// Lấy danh sách loại sản phẩm
$sql_loai = "SELECT id_loai, ten_loai FROM loai_san_pham";
$result_loai = $conn->query($sql_loai);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_san_pham = $_POST['ten_san_pham'];
    $gia_giam = $_POST['gia_giam'];
    $gia = $_POST['gia'];
    $loai_san_pham = $_POST['loai_san_pham'];

    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $file_name = basename($_FILES["hinh_anh"]["name"]);
    $unique_name = time() . "_" . $file_name;
    $target_file = $target_dir . $unique_name;

    if (move_uploaded_file($_FILES["hinh_anh"]["tmp_name"], $target_file)) {
        $insert_sql = "INSERT INTO san_pham (ten_san_pham, hinh_anh, gia_giam, gia, loai_san_pham) 
                       VALUES ('$ten_san_pham', '$target_file', '$gia_giam', '$gia', '$loai_san_pham')";

        if ($conn->query($insert_sql) === TRUE) {
            header("Location: sanpham.php");
            exit();
        } else {
            echo "Lỗi: " . $conn->error;
        }
    } else {
        echo "❌ Lỗi khi tải ảnh lên. Vui lòng kiểm tra file hoặc thư mục.";
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm Sản Phẩm</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f8;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-container {
            background: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            width: 400px;
            position: relative;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            color: #444;
        }

        input[type="text"],
        input[type="number"],
        input[type="file"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 18px;
            border: 1px solid #ccc;
            border-radius: 6px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        input[type="file"]:focus,
        select:focus {
            border-color: #007bff;
            outline: none;
        }

        input[type="submit"] {
            background-color: #3842d6;
            color: #fff;
            padding: 12px;
            border: none;
            border-radius: 6px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        input[type="submit"]:hover {
            background-color: #4a56c2;
        }

        .back-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #e0e0e0;
            color: #333;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s;
            z-index: 999;
        }

        .back-button:hover {
            background-color: #ccc;
        }
    </style>
</head>
<body>
    <a href="sanpham.php" class="back-button">← Quay lại</a>
    <div class="form-container">
        <h2>Thêm Sản Phẩm Mới</h2>
        <form method="POST" enctype="multipart/form-data">
            <label for="ten_san_pham">Tên sản phẩm:</label>
            <input type="text" name="ten_san_pham" required>

            <label for="hinh_anh">Hình ảnh:</label>
            <input type="file" name="hinh_anh" accept="image/*" required>

            <label for="gia_giam">Giá Giảm:</label>
            <input type="number" name="gia_giam" required>

            <label for="gia">Giá:</label>
            <input type="number" name="gia" required>

            <label for="loai_san_pham">Loại sản phẩm:</label>
            <select name="loai_san_pham" required>
                <option value="">-- Chọn loại --</option>
                <?php
                if ($result_loai->num_rows > 0) {
                    while ($row = $result_loai->fetch_assoc()) {
                        echo '<option value="' . $row['id_loai'] . '">' . $row['ten_loai'] . '</option>';
                    }
                }
                ?>
            </select>

            <input type="submit" value="Thêm sản phẩm">
        </form>
    </div>
</body>
</html>
