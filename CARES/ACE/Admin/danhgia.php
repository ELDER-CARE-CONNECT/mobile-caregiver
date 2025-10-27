<?php include 'check_login.php'; ?>
<?php include 'connect.php'; ?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- Lấy thông tin lọc ---
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
$filter_star = isset($_GET['star']) ? intval($_GET['star']) : 0;

// --- Câu truy vấn chính ---
$sql = "SELECT danh_gia.id_danh_gia, 
               khach_hang.ten_khach_hang, 
               nguoi_cham_soc.ho_ten AS ten_cham_soc,
               danh_gia.so_sao, 
               danh_gia.nhan_xet, 
               danh_gia.ngay_danh_gia
        FROM danh_gia
        JOIN khach_hang ON danh_gia.id_khach_hang = khach_hang.id_khach_hang
        JOIN nguoi_cham_soc ON danh_gia.id_cham_soc = nguoi_cham_soc.id_cham_soc
        WHERE (khach_hang.ten_khach_hang LIKE '%$keyword%'
           OR nguoi_cham_soc.ho_ten LIKE '%$keyword%'
           OR danh_gia.nhan_xet LIKE '%$keyword%')";

// --- Nếu chọn lọc sao ---
if ($filter_star > 0 && $filter_star <= 5) {
    $sql .= " AND danh_gia.so_sao = $filter_star";
}

// --- Sắp xếp giảm dần theo số sao ---
$sql .= " ORDER BY danh_gia.so_sao DESC, danh_gia.ngay_danh_gia DESC";

$result = $conn->query($sql);

// --- Tính trung bình số sao (theo bộ lọc hiện tại) ---
$avg_sql = "SELECT AVG(danh_gia.so_sao) AS trung_binh 
            FROM danh_gia
            JOIN khach_hang ON danh_gia.id_khach_hang = khach_hang.id_khach_hang
            JOIN nguoi_cham_soc ON danh_gia.id_cham_soc = nguoi_cham_soc.id_cham_soc
            WHERE (khach_hang.ten_khach_hang LIKE '%$keyword%'
               OR nguoi_cham_soc.ho_ten LIKE '%$keyword%'
               OR danh_gia.nhan_xet LIKE '%$keyword%')";

if ($filter_star > 0 && $filter_star <= 5) {
    $avg_sql .= " AND danh_gia.so_sao = $filter_star";
}

$avg_result = $conn->query($avg_sql);
$avg_star = 0;
if ($avg_result && $avg_result->num_rows > 0) {
    $avg_data = $avg_result->fetch_assoc();
 $avg_star = $avg_data['trung_binh'] !== null ? round($avg_data['trung_binh'], 2) : 0;

}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Đánh Giá Người Chăm Sóc</title>
<link rel="stylesheet" href="fontend/css/danhgia.css">
<style>
body {
    font-family: "Segoe UI", sans-serif;
    background-color: #f4f6fa;
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
.filter-box {
    margin-top: 15px;
    margin-bottom: 10px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
}
.filter-box select {
    padding: 6px;
    border-radius: 4px;
    border: 1px solid #ccc;
}
.filter-box .reset-btn {
    background: #6c757d;
    color: white;
    border: none;
    padding: 6px 10px;
    border-radius: 4px;
    text-decoration: none;
}
.filter-box .reset-btn:hover {
    background: #5a6268;
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
.star {
    color: #f39c12;
    font-size: 16px;
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
.avg-box {
    background: #e9f2ff;
    padding: 10px 15px;
    border-radius: 6px;
    margin-top: 15px;
    font-weight: 600;
    color: #007BFF;
    width: fit-content;
}
.avg-box span {
    color: #f39c12;
}
</style>
</head>

<body>
<div class="container">
<?php 
$activePage = 'danhgia'; 
$pageTitle = 'Quản Lí Đánh Giá';
include 'sidebar.php'; 
?>

<main class="main-content">
    <header class="navbar">
        <h1>Trang Quản Lí Đánh Giá Người Chăm Sóc</h1>
        <div class="search">
            <form method="get" action="">
                <input type="text" name="keyword" placeholder="Tìm kiếm theo khách hàng, người chăm sóc..." 
                       value="<?php echo htmlspecialchars($keyword); ?>">
                <button type="submit">🔍</button>
            </form>
        </div>
    </header>

    <div class="filter-box">
        <form method="get" action="">
            <label for="star">Lọc theo số sao:</label>
            <select name="star" id="star" onchange="this.form.submit()">
                <option value="">-- Tất cả --</option>
                <?php 
                for ($i = 5; $i >= 1; $i--) {
                    $selected = ($filter_star == $i) ? 'selected' : '';
                    echo "<option value='$i' $selected>$i sao</option>";
                }
                ?>
            </select>
            <noscript><button type="submit">Lọc</button></noscript>
        </form>
        <a href="danhgia.php" class="reset-btn">↻ Reset</a>
    </div>

    <div class="avg-box">
        ⭐ Đánh giá trung bình: 
        <span>
            <?php echo ($avg_star > 0) ? $avg_star . ' / 5 ⭐' : 'Chưa có đánh giá'; ?>
        </span>
    </div>

    <table>
        <tr>
            <th>Mã ĐG</th>
            <th>Khách hàng</th>
            <th>Người chăm sóc</th>
            <th>Số sao</th>
            <th>Nhận xét</th>
            <th>Ngày đánh giá</th>
            <th>Thao tác</th>
        </tr>

        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['id_danh_gia']}</td>";
                echo "<td>{$row['ten_khach_hang']}</td>";
                echo "<td>{$row['ten_cham_soc']}</td>";
                echo "<td class='star'>{$row['so_sao']} ⭐</td>";
                echo "<td>{$row['nhan_xet']}</td>";
                echo "<td>{$row['ngay_danh_gia']}</td>";
                echo "<td class='action-links'>
                        <a href='sua_danh_gia.php?id={$row['id_danh_gia']}'>✏ Sửa</a> |
                        <a href='xoa_danh_gia.php?id={$row['id_danh_gia']}' onclick=\"return confirm('Bạn có chắc muốn xóa đánh giá này không?');\">🗑 Xóa</a>
                      </td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='7'>Không có đánh giá nào.</td></tr>";
        }
        ?>
    </table>
</main>
</div>
</body>
</html>

<?php $conn->close(); ?>
