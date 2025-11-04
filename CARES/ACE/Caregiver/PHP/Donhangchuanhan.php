<?php
session_start();
include_once('../../model/get_products.php');
$conn = connectdb();

// Kiểm tra nếu chưa đăng nhập
if (!isset($_SESSION['ten_tai_khoan'])) {
    header("Location: ../../Admin/login.php");
    exit();
}

$ten_tai_khoan = $_SESSION['ten_tai_khoan'];

// 1️⃣ Lấy thông tin người chăm sóc
$sql_chamsoc = "SELECT id_cham_soc, ho_ten FROM nguoi_cham_soc WHERE ten_tai_khoan = ?";
$stmt_cs = $conn->prepare($sql_chamsoc);
$stmt_cs->bind_param("s", $ten_tai_khoan);
$stmt_cs->execute();
$result_cs = $stmt_cs->get_result();

if ($result_cs->num_rows === 0) {
    die("❌ Không tìm thấy người chăm sóc với tài khoản này!");
}

$chamsoc = $result_cs->fetch_assoc();
$id_cham_soc = $chamsoc['id_cham_soc'];
$ho_ten_chamsoc = $chamsoc['ho_ten'];

// 2️⃣ Nếu người dùng nhấn nút “Nhận đơn hàng”
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nhan_don'])) {
    $id_don_hang = $_POST['id_don_hang'];

    $update_sql = "UPDATE don_hang SET trang_thai = 'đang hoàn thành' WHERE id_don_hang = ? AND id_cham_soc = ?";
    $stmt_update = $conn->prepare($update_sql);
    $stmt_update->bind_param("ii", $id_don_hang, $id_cham_soc);
    $stmt_update->execute();

    // Sau khi cập nhật, tải lại trang để cập nhật giao diện
    header("Location: Donhangchuanhan.php");
    exit();
}

// 3️⃣ Lấy danh sách đơn hàng
$sql_donhang = "SELECT id_don_hang, id_khach_hang, ngay_dat, tong_tien, trang_thai 
                FROM don_hang 
                WHERE id_cham_soc = ?";
$stmt_dh = $conn->prepare($sql_donhang);
$stmt_dh->bind_param("i", $id_cham_soc);
$stmt_dh->execute();
$result_dh = $stmt_dh->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng được giao</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
        }

        .accepted-orders-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .hero h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .orders-wrapper {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border: 1px solid #e5e7eb;
        }

        .orders-wrapper h2 {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 25px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }

        .order-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, max-content));
            justify-content: start;
            gap: 24px;
            justify-items: flex-start;
        }

        .order-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            width: 340px;
            height: 190px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
        }

        .order-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
            border-color: #3b82f6;
        }

        .order-card h3 {
            margin: 0 0 10px;
            font-size: 17px;
            font-weight: 700;
            color: #2563eb;
        }

        .order-info p {
            margin: 4px 0;
            color: #374151;
            font-size: 15px;
            line-height: 1.4;
        }

        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .status.completed {
            background: #d1fae5;
            color: #065f46;
        }

        .status.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .btn-container {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .view-btn {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .view-btn:hover {
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
            transform: translateY(-2px);
        }

        .accept-btn {
            background: #dc2626; /* đỏ */
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .accept-btn:hover {
            background: #b91c1c;
            box-shadow: 0 4px 10px rgba(220, 38, 38, 0.3);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .order-card {
                width: 100%;
                height: auto;
            }
        }
    </style>
</head>
<body>
<div class="accepted-orders-container">
    <div class="hero">
        <h1><i class="fas fa-list"></i> Đơn hàng được giao cho bạn</h1>
    </div>

    <div class="orders-wrapper">
        <h2>Xin chào, <?php echo htmlspecialchars($ho_ten_chamsoc); ?>!</h2>

        <div class="order-cards">
            <?php
            if ($result_dh->num_rows > 0) {
                while ($row = $result_dh->fetch_assoc()) {
                    // Lấy tên khách hàng từ id_khach_hang
                    $id_khach_hang = $row['id_khach_hang'];
                    $sql_khach = "SELECT ten_khach_hang FROM khach_hang WHERE id_khach_hang = ?";
                    $stmt_kh = $conn->prepare($sql_khach);
                    $stmt_kh->bind_param("i", $id_khach_hang);
                    $stmt_kh->execute();
                    $result_kh = $stmt_kh->get_result();
                    $ten_khach_hang = $result_kh->fetch_assoc()['ten_khach_hang'] ?? 'Không xác định';
                    $stmt_kh->close();

                    echo "
                    <div class='order-card'>
                        <div>
                            <h3>Mã đơn: #{$row['id_don_hang']}</h3>
                            <div class='order-info'>
                                <p><strong>Khách hàng:</strong> {$ten_khach_hang}</p>
                                <p><strong>Ngày đặt:</strong> {$row['ngay_dat']}</p>
                                <p><strong>Trạng thái:</strong> 
                                    <span class='status " . 
                                        ($row['trang_thai'] == 'đang hoàn thành' ? 'completed' : 'pending') . "'>
                                        {$row['trang_thai']}
                                    </span>
                                </p>
                                <p><strong>Tổng tiền:</strong> " . number_format($row['tong_tien'], 0, ',', '.') . "₫</p>
                            </div>
                        </div>
                        <div class='btn-container'>";
                    
                    if ($row['trang_thai'] == 'chờ xác nhận') {
                        echo "
                        <form method='POST' style='display:inline;'>
                            <input type='hidden' name='id_don_hang' value='{$row['id_don_hang']}'>
                            <button type='submit' name='nhan_don' class='accept-btn'>Nhận đơn</button>
                        </form>";
                    }

                    echo "<button class='view-btn'>Xem</button>
                        </div>
                    </div>";
                }
            } else {
                echo "<p>❌ Hiện tại bạn chưa có đơn hàng nào được giao.</p>";
            }

            $stmt_dh->close();
            $conn->close();
            ?>
        </div>
    </div>
</div>
</body>
</html>
