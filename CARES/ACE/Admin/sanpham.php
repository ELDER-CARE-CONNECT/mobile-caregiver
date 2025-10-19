<?php include 'check_login.php'; ?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Kết nối cơ sở dữ liệu
include 'connect.php';

// Lấy từ khóa tìm kiếm nếu có
$keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';

// Truy vấn dữ liệu từ bảng `san_pham` với điều kiện tìm kiếm
$sql = "SELECT * FROM san_pham WHERE ten_san_pham LIKE '%$keyword%'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sản Phẩm</title>
    <link rel="stylesheet" href="fontend/css/sanpham.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="fontend/js/sanpham.js"></script>
    <script src="fontend/js/xoa_nhieu_sp.js"></script>
</head>
<body>
    <div class="container">
        <?php 
        $activePage = 'sanpham'; 
        $pageTitle = 'Quản Lí Sản Phẩm';
        include 'sidebar.php'; 
        ?>
        <main class="main-content">
            <header class="navbar">
                <h1>Trang Quản Lí Sản Phẩm</h1>
                <div class="search">
                    <!-- Form tìm kiếm sản phẩm -->
                    <form method="get" action="">
                        <input type="text" name="keyword" placeholder="Tìm kiếm sản phẩm..." value="<?php echo htmlspecialchars($keyword); ?>">
                        <button type="submit">🔍</button>
                    </form>
                </div>
            </header>

            <section class="stats">
                <form id="form1" name="form1" method="post" action="loaitin_them.php">
                    <table id="distin">
                        <tr>
                            <td colspan="5" align="left">
                            </td>
                            <td colspan="2" align="right">
                                | <a href="them_san_pham.php">➕ Thêm Sản Phẩm</a> |
                            </td>
                        </tr>  

                        <tr class="hang">
                            <th>Mã SP</th>
                            <th>Tên Sản Phẩm</th>
                            <th>Hình Ảnh</th>
                            <th>Giảm giá</th>
                            <th>Giá</th>
                            <th>Thao Tác</th>
                        </tr>

                        <?php
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr class='hang'>";
                                echo "<td>" . $row['id_san_pham'] . "</td>";
                                echo "<td>" . htmlspecialchars($row['ten_san_pham']) . "</td>";
                                echo "<td><img src='" . htmlspecialchars($row['hinh_anh']) . "' alt='Ảnh SP'></td>";
                                echo "<td>" . $row['gia_giam'] . "</td>";
                                echo "<td>" . number_format($row['gia'], 2) . " VND</td>";
                                echo "<td class='action-links'>
                                        <a href='sua_san_pham.php?id_san_pham=" . $row['id_san_pham'] . "'>Chỉnh</a> |
                                        <a href='xoa_san_pham.php?id=" . $row['id_san_pham'] . "' onclick=\"return confirm('Bạn có chắc muốn xóa?');\">Xóa</a>
                                      </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>Không có sản phẩm nào.</td></tr>";
                        }
                        ?>
                    </table>
                </form>
            </section>
        </main>
    </div>
</body>
</html>

<?php
$conn->close();
?>
