<?php include 'check_login.php'; ?>
<?php include 'connect.php'; ?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$sql = "SELECT * FROM nguoi_cham_soc WHERE ho_ten LIKE '%$keyword%' 
        OR dia_chi LIKE '%$keyword%' OR gioi_tinh LIKE '%$keyword%' OR kinh_nghiem LIKE '%$keyword%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản Lý Người Chăm Sóc</title>
<link rel="stylesheet" href="fontend/css/nguoi_cham_soc.css">
<style>
body {
    font-family: "Segoe UI", sans-serif;
    background: #f4f6fa;
    color: #333;
}
.container {
    display: flex;
}
.main-content {
    flex-grow: 1;
    background: #fff;
    padding: 20px 40px;
}
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 3px solid #007BFF;
    padding-bottom: 10px;
}
.navbar h1 {
    color: #007BFF;
    margin: 0;
}
.search input {
    padding: 6px;
    border: 1px solid #ccc;
    border-radius: 4px;
}
.search button {
    background: #007BFF;
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 4px;
    cursor: pointer;
}
.search button:hover {
    background: #0056b3;
}
.add-btn {
    background-color: #28a745;
    color: white;
    padding: 8px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: bold;
    float: right;
    margin: 10px 0;
}
.add-btn:hover {
    background-color: #1e7e34;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 0 6px rgba(0,0,0,0.1);
}
th {
    background: #007BFF;
    color: #000;
    padding: 10px;
    font-weight: bold;
}
td {
    padding: 8px;
    border-bottom: 1px solid #eee;
    text-align: center;
}
tr:hover {
    background: #f9f9f9;
}
img {
    width: 80px;
    height: 80px;
    border-radius: 0;
    object-fit: cover;
}

.action-links a {
    text-decoration: none;
    color: #007BFF;
    margin: 0 5px;
    font-weight: 500;
}
.action-links a:hover {
    color: #dc3545;
}
.order-details-row {
    display: none;
    background: #f1f8ff;
}
.view-btn {
    background: #ffc107;
    color: #000;
    border: none;
    padding: 6px 10px;
    border-radius: 4px;
    cursor: pointer;
}
.view-btn:hover {
    background: #e0a800;
}
</style>
</head>

<body>
<div class="container">
<?php 
$activePage = 'nguoi_cham_soc'; 
$pageTitle = 'Quản Lí Người Chăm Sóc';
include 'sidebar.php'; 
?>

<main class="main-content">
    <header class="navbar">
        <h1>Trang Quản Lí Người Chăm Sóc</h1>
        <div class="search">
            <form method="get" action="">
                <input type="text" name="keyword" placeholder="Tìm kiếm người chăm sóc..." 
                       value="<?php echo htmlspecialchars($keyword); ?>">
                <button type="submit">🔍</button>
            </form>
        </div>
    </header>

    <a href="them_nguoi_cham_soc.php" class="add-btn">➕ Thêm Hồ Sơ</a>

    <table>
        <tr>
            <th>Mã người chăm sóc</th>
            <th>Ảnh</th>
            <th>Họ và tên</th>
            <th>Địa chỉ</th>
            <th>Tuổi</th>
            <th>Giới tính</th>
            <th>Chiều cao (cm)</th>
            <th>Cân nặng (kg)</th>
            <th>Đánh giá TB</th>
            <th>Kinh nghiệm</th>
            <th>Thao tác</th>
        </tr>

        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id_cham_soc']}</td>";
                echo "<td><img src='{$row['hinh_anh']}' alt='Ảnh'></td>";
                echo "<td>{$row['ho_ten']}</td>";
                echo "<td>{$row['dia_chi']}</td>";
                echo "<td>{$row['tuoi']}</td>";
                echo "<td>{$row['gioi_tinh']}</td>";
                echo "<td>{$row['chieu_cao']}</td>";
                echo "<td>{$row['can_nang']}</td>";
              $rating = isset($row['danh_gia_tb']) && $row['danh_gia_tb'] !== null 
    ? number_format((float)$row['danh_gia_tb'], 1) . "⭐" 
    : "—";
echo "<td>$rating</td>";

                echo "<td>{$row['kinh_nghiem']}</td>";

                echo "<td class='action-links'>
                        <button class='view-btn' data-id='{$row['id_cham_soc']}'>👁 Xem đánh giá</button><br>
                        <a href='sua_nguoi_cham_soc.php?id={$row['id_cham_soc']}'>✏ Sửa</a> |
                        <a href='xoa_nguoi_cham_soc.php?id={$row['id_cham_soc']}' onclick=\"return confirm('Bạn có chắc muốn xóa?');\">🗑 Xóa</a>
                      </td>";
                echo "</tr>";

                // Dòng ẩn hiển thị đánh giá
                echo "<tr class='order-details-row' id='reviews-{$row['id_cham_soc']}'>
                        <td colspan='13'>
                        <table width='100%' border='1' cellpadding='4' cellspacing='0'>
                            <tr>
                                <th>Khách hàng</th>
                                <th>Số sao</th>
                                <th>Nhận xét</th>
                                <th>Ngày đánh giá</th>
                            </tr>";

                $sqlReview = "SELECT khach_hang.ten_khach_hang, danh_gia.so_sao, danh_gia.nhan_xet, danh_gia.ngay_danh_gia
                              FROM danh_gia 
                              JOIN khach_hang ON danh_gia.id_khach_hang = khach_hang.id_khach_hang
                              WHERE danh_gia.id_cham_soc = {$row['id_cham_soc']}";
                $reviews = $conn->query($sqlReview);

                if ($reviews && $reviews->num_rows > 0) {
                    while ($rev = $reviews->fetch_assoc()) {
                        echo "<tr>
                                <td>{$rev['ten_khach_hang']}</td>
                                <td>{$rev['so_sao']}⭐</td>
                                <td>{$rev['nhan_xet']}</td>
                                <td>{$rev['ngay_danh_gia']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Chưa có đánh giá nào.</td></tr>";
                }

                echo "</table></td></tr>";
            }
        } else {
            echo "<tr><td colspan='13'>Không có người chăm sóc nào.</td></tr>";
        }
        ?>
    </table>
</main>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
$(document).ready(function(){
    $(".view-btn").click(function(){
        const id = $(this).data("id");
        $("#reviews-" + id).toggle(300);
    });
});
</script>
</body>
</html>

<?php $conn->close(); ?>
