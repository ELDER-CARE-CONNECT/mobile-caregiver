<?php include 'check_login.php'; ?>
<?php include 'connect.php'; ?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

// 🔹 Câu truy vấn mới: tính trung bình đánh giá (AVG)
$sql = "
SELECT 
    ncs.*, 
    COALESCE(AVG(dg.so_sao), 0) AS danh_gia_tb
FROM nguoi_cham_soc ncs
LEFT JOIN danh_gia dg ON ncs.id_cham_soc = dg.id_cham_soc
WHERE ncs.ho_ten LIKE '%$keyword%' 
   OR ncs.dia_chi LIKE '%$keyword%' 
   OR ncs.gioi_tinh LIKE '%$keyword%' 
   OR ncs.kinh_nghiem LIKE '%$keyword%'
GROUP BY ncs.id_cham_soc
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Quản Lý Người Chăm Sóc</title>
<link rel="stylesheet" href="fontend/css/nguoi_cham_soc.css">
<style>
/* ====== GIAO DIỆN TỔNG ====== */
body {
    font-family: "Segoe UI", sans-serif;
    background-color: #f0f4f8;
    color: #333;
    margin: 0;
    padding: 0;
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

/* ====== THANH NAVBAR ====== */
.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 3px solid #3498db;
    padding-bottom: 15px;
    margin-bottom: 10px;
}
.navbar h1 {
    color: #3498db;
    font-size: 22px;
    font-weight: 600;
}

/* ====== THANH TÌM KIẾM ====== */
.search input {
    padding: 7px 10px;
    border: 1px solid #ccc;
    border-radius: 6px;
    width: 260px;
}
.search button {
    background: #3498db;
    color: white;
    border: none;
    padding: 7px 12px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s;
}
.search button:hover {
    background: #2980b9;
}

/* ====== NÚT THÊM ====== */
.add-btn {
    background-color: #2ecc71;
    color: white;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
    display: inline-block;
    margin-top: 15px;
    transition: 0.3s;
}
.add-btn:hover {
    background-color: #27ae60;
}

/* ====== BẢNG ====== */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 25px;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
th {
    background: #3498db;
    color: #fff;
    padding: 12px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 14px;
}
td {
    padding: 10px;
    border-bottom: 1px solid #eee;
    text-align: center;
    font-size: 15px;
}
tr:nth-child(even) {
    background: #f9f9f9;
}
tr:hover {
    background: #eaf4ff;
    transition: 0.2s;
}

/* ====== ẢNH ====== */
img {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    object-fit: cover;
    box-shadow: 0 0 5px rgba(0,0,0,0.1);
}

/* ====== NÚT XEM ĐÁNH GIÁ ====== */
.view-btn {
    background: #f1c40f;
    color: #000;
    border: none;
    padding: 7px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    transition: 0.3s;
}
.view-btn:hover {
    background: #d4ac0d;
    transform: scale(1.05);
}

/* ====== LIÊN KẾT HÀNH ĐỘNG ====== */
.action-links a {
    text-decoration: none;
    color: #2980b9;
    margin: 0 5px;
    font-weight: 500;
    transition: 0.3s;
}
.action-links a:hover {
    color: #e74c3c;
}

/* ====== DÒNG CHI TIẾT ====== */
.order-details-row {
    background: #f8f9fa;
    display: none;
}
.order-details-row table {
    width: 100%;
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-top: 8px;
}
.order-details-row th {
    background: #6c757d;
    color: white;
    padding: 8px;
}
.order-details-row td {
    background: #fff;
    padding: 8px;
}

/* ====== SAO ====== */
.star {
    color: #f1c40f;
    font-weight: bold;
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
            <th>Mã người làm</th>
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

                // ⭐ Hiển thị trung bình đánh giá
                $rating = isset($row['danh_gia_tb']) && $row['danh_gia_tb'] > 0
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

                // 🔹 Chi tiết đánh giá từng người
                echo "<tr class='order-details-row' id='reviews-{$row['id_cham_soc']}'>
                        <td colspan='11'>
                        <table>
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
                                <td class='star'>{$rev['so_sao']}⭐</td>
                                <td>{$rev['nhan_xet']}</td>
                                <td>{$rev['ngay_danh_gia']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' style='text-align:center;'>Chưa có đánh giá nào.</td></tr>";
                }

                echo "</table></td></tr>";
            }
        } else {
            echo "<tr><td colspan='11' style='text-align:center;'>Không có người chăm sóc nào.</td></tr>";
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
        $("#reviews-" + id).slideToggle(300);
    });
});
</script>
</body>
</html>

<?php $conn->close(); ?>
