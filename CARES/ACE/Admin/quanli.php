<?php include 'check_login.php'; ?>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sanpham";

// Kết nối CSDL
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
<<<<<<< HEAD
<<<<<<< HEAD
    die("Kết nối thất bại: " . $conn->connect_error);
=======
    die("<div style='color:red; text-align:center; font-weight:bold;'>❌ Kết nối thất bại: " . $conn->connect_error . "</div>");
>>>>>>> Vy
=======
    die("Kết nối thất bại: " . $conn->connect_error);
>>>>>>> Phong
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản Lí Đơn Hàng</title>
    <link rel="stylesheet" href="fontend/css/sanpham.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<<<<<<< HEAD
<<<<<<< HEAD
=======

    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
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
            margin: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            animation: fadeIn 0.5s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== NAVBAR ===== */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid #2196f3;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .navbar h1 {
            color: #0d47a1;
            font-size: 24px;
        }

        .search input {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #90caf9;
            width: 240px;
        }
        .search button {
            background: #1e88e5;
            color: white;
            border: none;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            margin-left: 5px;
            transition: 0.3s;
        }
        .search button:hover {
            background: #0d47a1;
        }

        /* ===== BẢNG ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th {
            background: #1e88e5;
            color: white;
            text-transform: uppercase;
            font-size: 14px;
            padding: 12px;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #eee;
            text-align: center;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #e3f2fd;
            transition: 0.3s;
        }

        /* ===== FORM CẬP NHẬT ===== */
        form select {
            padding: 5px 8px;
            border-radius: 6px;
            border: 1px solid #90caf9;
        }
        form button {
            background: #2196f3;
            border: none;
            color: white;
            padding: 6px 10px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }
        form button:hover {
            background: #1565c0;
        }

        /* ===== HEADER ===== */
        .order-header h2 {
            color: #0d47a1;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .no-order {
            text-align: center;
            padding: 20px;
            color: #777;
            font-style: italic;
        }
    </style>
>>>>>>> Vy
=======
>>>>>>> Phong
</head>

<body>
<div class="container">
    <?php 
<<<<<<< HEAD
    $activePage = 'quanli'; 
    include 'sidebar.php'; 
=======
        $activePage = 'quanli'; 
        include 'sidebar.php'; 
>>>>>>> Vy
    ?>

    <main class="main-content">
        <header class="navbar">
            <h1>Trang Quản Lí Đơn Hàng</h1>
            <div class="search">
                <form method="get" action="">
                    <input type="text" name="search_id" placeholder="Tìm kiếm theo mã đơn hàng" 
                           value="<?php echo isset($_GET['search_id']) ? $_GET['search_id'] : ''; ?>">
                    <button type="submit">🔍</button>
                </form>
            </div>
        </header>

        <section class="stats">
            <div class="order-header">
                <h2>Danh sách đơn hàng</h2>
            </div>

<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> Phong
            <table border="1" cellpadding="6" cellspacing="0" width="100%">
                <tr class="hang">
=======
            <table>
                <tr>
>>>>>>> Vy
                    <th>Mã đơn hàng</th>
                    <th>Ngày đặt</th>
                    <th>Tên khách hàng</th>
                    <th>Số điện thoại</th>
<<<<<<< HEAD
<<<<<<< HEAD
                    <th>Tên người chăm sóc</th>
=======
                    <th>Người chăm sóc</th>
>>>>>>> Vy
=======
                    <th>Tên người chăm sóc</th>
>>>>>>> Phong
                    <th>Trạng thái</th>
                    <th>Đánh giá</th>
                    <th>Nhận xét</th>
                    <th>Tổng tiền</th>
                    <th>Cập nhật</th>
                </tr>

                <?php
<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> Phong
                // Lấy mã đơn hàng tìm kiếm (nếu có)
                $search_id = isset($_GET['search_id']) ? $_GET['search_id'] : '';

                // Truy vấn kết hợp 3 bảng
              $sql = "
                SELECT 
                    dh.id_don_hang,
                    dh.ngay_dat,
                    dh.ten_khach_hang,
                    dh.so_dien_thoai,
                    ncs.ho_ten AS ten_nguoi_cham_soc,
                    dh.trang_thai,
                    dg.so_sao AS danh_gia,
                    dg.nhan_xet,
                    dh.tong_tien
                FROM don_hang dh
                LEFT JOIN nguoi_cham_soc ncs ON dh.id_cham_soc = ncs.id_cham_soc
                LEFT JOIN danh_gia dg ON dh.id_danh_gia = dg.id_danh_gia
                WHERE 1
            ";

<<<<<<< HEAD
=======
                $search_id = isset($_GET['search_id']) ? $_GET['search_id'] : '';

                $sql = "
                    SELECT 
                        dh.id_don_hang,
                        dh.ngay_dat,
                        dh.ten_khach_hang,
                        dh.so_dien_thoai,
                        ncs.ho_ten AS ten_nguoi_cham_soc,
                        dh.trang_thai,
                        dg.so_sao AS danh_gia,
                        dg.nhan_xet,
                        dh.tong_tien
                    FROM don_hang dh
                    LEFT JOIN nguoi_cham_soc ncs ON dh.id_cham_soc = ncs.id_cham_soc
                    LEFT JOIN danh_gia dg ON dh.id_danh_gia = dg.id_danh_gia
                    WHERE 1
                ";
>>>>>>> Vy
=======
>>>>>>> Phong

                if ($search_id) {
                    $sql .= " AND dh.id_don_hang LIKE '%$search_id%'";
                }

                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
<<<<<<< HEAD
                        echo "<tr class='hang'>";
                        echo "<td>" . $row['id_don_hang'] . "</td>";
                        echo "<td>" . $row['ngay_dat'] . "</td>";
                        echo "<td>" . $row['ten_khach_hang'] . "</td>";
                        echo "<td>" . $row['so_dien_thoai'] . "</td>";
                        echo "<td>" . ($row['ten_nguoi_cham_soc'] ?? 'Chưa có') . "</td>";
                        echo "<td>" . $row['trang_thai'] . "</td>";
                        echo "<td>" . ($row['diem_danh_gia'] ?? 'Chưa đánh giá') . "</td>";
                        echo "<td>" . ($row['nhan_xet'] ?? '—') . "</td>";
                        echo "<td>" . number_format($row['tong_tien'], 0) . " VND</td>";
=======
                        echo "<tr>";
                        echo "<td>{$row['id_don_hang']}</td>";
                        echo "<td>{$row['ngay_dat']}</td>";
                        echo "<td>{$row['ten_khach_hang']}</td>";
                        echo "<td>{$row['so_dien_thoai']}</td>";
                        echo "<td>" . ($row['ten_nguoi_cham_soc'] ?? 'Chưa có') . "</td>";
                        echo "<td>{$row['trang_thai']}</td>";
                        echo "<td>" . ($row['danh_gia'] ?? 'Chưa đánh giá') . "</td>";
                        echo "<td>" . ($row['nhan_xet'] ?? '—') . "</td>";
                        echo "<td>" . number_format($row['tong_tien'], 0, ',', '.') . " VND</td>";
>>>>>>> Vy

                        echo "<td>
<<<<<<< HEAD
                            <form method='post' action='capnhat_trangthai.php'>
                                <select name='trang_thai'>
                                    <option value='Chờ xác nhận' ".($row['trang_thai']=='Chờ xác nhận'?'selected':'').">Chờ xác nhận</option>
                                    <option value='Đang giao' ".($row['trang_thai']=='Đang hoàn thành'?'selected':'').">Đang hoàn thành</option>
                                    <option value='Đã giao' ".($row['trang_thai']=='Đã hoàn thành'?'selected':'').">Đã hoàn thành</option>
                                    <option value='Đã hủy' ".($row['trang_thai']=='Đã hủy'?'selected':'').">Đã hủy</option>
                                </select>
                                <input type='hidden' name='id_don_hang' value='" . $row['id_don_hang'] . "'>
                                <button type='submit'>Cập nhật</button>
                            </form>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' style='text-align:center;'>Không có đơn hàng nào</td></tr>";
<<<<<<< HEAD
=======
                                <form method='post' action='capnhat_trangthai.php'>
                                    <select name='trang_thai'>
                                        <option value='Chờ xác nhận' ".($row['trang_thai']=='Chờ xác nhận'?'selected':'').">Chờ xác nhận</option>
                                        <option value='Đang hoàn thành' ".($row['trang_thai']=='Đang hoàn thành'?'selected':'').">Đang hoàn thành</option>
                                        <option value='Đã hoàn thành' ".($row['trang_thai']=='Đã hoàn thành'?'selected':'').">Đã hoàn thành</option>
                                        <option value='Đã hủy' ".($row['trang_thai']=='Đã hủy'?'selected':'').">Đã hủy</option>
                                    </select>
                                    <input type='hidden' name='id_don_hang' value='{$row['id_don_hang']}'>
                                    <button type='submit'>Lưu</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' class='no-order'>Không có đơn hàng nào.</td></tr>";
>>>>>>> Vy
=======
>>>>>>> Phong
                }

                $conn->close();
                ?>
            </table>
        </section>
    </main>
</div>
</body>
</html>
