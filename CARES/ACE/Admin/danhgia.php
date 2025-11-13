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
    // ✅ Làm tròn 1 chữ số thập phân, hiển thị dạng 4.3 / 5 ⭐
    $avg_star = $avg_data['trung_binh'] !== null 
        ? number_format(round($avg_data['trung_binh'], 1), 1) 
        : 0;
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
    background-color: #f0f4f8;
    color: #333;
    margin: 0;
    padding: 0;
}

/* ====== BỐ CỤC ====== */
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

/* ====== Ô TÌM KIẾM ====== */
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

/* ====== BỘ LỌC ====== */
.filter-box {
    margin-top: 15px;
    margin-bottom: 10px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    font-size: 15px;
}
.filter-box select {
    padding: 7px;
    border-radius: 6px;
    border: 1px solid #ccc;
    background-color: #fff;
}
.filter-box .reset-btn {
    background: #95a5a6;
    color: white;
    border: none;
    padding: 7px 12px;
    border-radius: 6px;
    text-decoration: none;
    transition: 0.3s;
}
.filter-box .reset-btn:hover {
    background: #7f8c8d;
}

/* ====== HỘP TRUNG BÌNH SAO ====== */
.avg-box {
    background: #eaf4ff;
    padding: 10px 20px;
    border-radius: 8px;
    margin-top: 15px;
    font-weight: 600;
    color: #2c3e50;
    display: inline-block;
}
.avg-box span {
    color: #f1c40f;
    font-weight: bold;
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

/* ====== NGÔI SAO ====== */
.star {
    color: #f1c40f;
    font-weight: bold;
    font-size: 15px;
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
