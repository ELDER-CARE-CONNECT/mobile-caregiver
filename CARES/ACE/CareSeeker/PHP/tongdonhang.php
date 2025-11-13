<?php
session_start();
include_once('../../model/get_products.php');
$conn = connectdb();

// 🔒 Kiểm tra nếu chưa đăng nhập thì chuyển hướng
if (!isset($_SESSION['so_dien_thoai'])) {
    header("Location: ../../Admin/login.php");
    exit();
}

// 📱 Lấy thông tin người dùng đang đăng nhập
$so_dien_thoai = $_SESSION['so_dien_thoai'];

// ==========================================================
// 1. LẤY TÊN KHÁCH HÀNG ĐỂ HIỂN THỊ LỜI CHÀO
// ==========================================================
$customer_name = "Khách hàng"; // Tên mặc định
$sql_name = "SELECT ten_khach_hang FROM khach_hang WHERE so_dien_thoai = ?";
$stmt_name = $conn->prepare($sql_name);

if ($stmt_name) {
    $stmt_name->bind_param("s", $so_dien_thoai);
    $stmt_name->execute();
    $result_name = $stmt_name->get_result();
    
    if ($result_name->num_rows > 0) {
        $customer_name = htmlspecialchars($result_name->fetch_assoc()['ten_khach_hang']);
    }
    $stmt_name->close();
}

// ==========================================================
// 2. TRUY VẤN ĐƠN HÀNG CỦA KHÁCH HÀNG
// ==========================================================
$sql = "SELECT id_don_hang, ten_khach_hang, id_cham_soc, ngay_dat, tong_tien, trang_thai 
        FROM don_hang 
        WHERE so_dien_thoai = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $so_dien_thoai);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử đặt hàng</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ----------------------------------- */
        /* CSS CHỈ DÀNH CHO NAVBAR (TỪ navbar.php) */
        /* ----------------------------------- */
        .navbar {
          background: #fff;
          padding: 15px 60px;
          display: flex;
          justify-content: space-between;
          align-items: center;
          width: 95%; /* Đảm bảo Navbar luôn chiếm 100% */
          box-shadow: 0 2px 12px rgba(0,0,0,0.06);
          position: fixed; 
          top: 0; 
          left: 0; 
          z-index: 1000;
          transition: all 0.3s;
        }
        .navbar h2 {
          color: #FF6B81;
          font-size: 26px; font-weight:700;
        }
        .nav-links a {
          color:#555; text-decoration:none; margin:0 16px;
          font-weight:500; position:relative; padding-bottom:3px;
        }
        .nav-links a:hover { color:#FF6B81; }
        .nav-links a::after {
          content: ''; position:absolute; width:0; height:2px; display:block;
          margin-top:5px; right:0; background:#FF6B81; transition:0.3s;
        }
        .nav-links a:hover::after { width:100%; left:0; }
        .nav-links a.active {
          color: #FF6B81;
          font-weight: 600;
        }
        .nav-links a.active::after {
          width: 100%;
          left: 0;
        }

        /* ----------------------------------- */
        /* CSS CỦA TRANG TỔNG ĐƠN HÀNG */
        /* ----------------------------------- */
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
            /* FIX NAVBAR: Điều chỉnh padding-top */
            padding-top: 90px; 
            max-width: 100%;
            overflow-x: hidden;
        }

        .accepted-orders-container {
            /* FIX CHIỀU NGANG: Giữ max-width là 1200px */
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

        /* ===== KHUNG LỚN ===== */
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

        /* ===== GRID (ĐÃ TINH CHỈNH) ===== */
        .order-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            /* Xóa justify-content và justify-items vì 1fr đã lo việc căn chỉnh */
            gap: 10px;
        }

        /* ===== CARD (ĐÃ TINH CHỈNH) ===== */
        .order-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            /* ĐÃ SỬA: Giảm chiều cao cho card trông gọn gàng hơn */
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
            margin: 0 0 12px;
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

        /* ===== Nút Xem (Đã sửa cho thẻ <a>) ===== */
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
            align-self: flex-end;
            text-decoration: none;
            display: inline-block; 
        }

        .view-btn:hover {
            background: linear-gradient(135deg, #1d4ed8, #2563eb);
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.3);
            transform: translateY(-2px);
        }

        /* Khi chỉ có 1 đơn hàng — căn giữa */
        .order-cards:has(.order-card:only-child) {
            justify-content: center;
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

<div class="navbar">
  <h2>Elder Care Connect</h2>
  <div class="nav-links">
    <a href="index.php">Trang chủ</a>
    <a href="dichvu.php">Dịch vụ</a>
    <a href="tongdonhang.php" class="active">Đơn hàng</a>
    <a href="Canhan.php">Cá nhân</a>
  </div>
</div>

<script>
// Logic JavaScript để đánh dấu link đang hoạt động (Active Link)
(function() {
    // Lấy tên file của trang hiện tại (ví dụ: "tongdonhang.php")
    var currentPage = window.location.pathname.split('/').pop();
    if (currentPage === "" || currentPage === "index.php") {
      currentPage = "index.php"; // Mặc định là trang chủ
    }

    // Lấy tất cả các link trong navbar
    var navLinks = document.querySelectorAll('.nav-links a');

    navLinks.forEach(function(link) {
      // Lấy tên file từ thuộc tính href của link
      var linkPage = new URL(link.href).pathname.split('/').pop();
      if (linkPage === "") {
        linkPage = "index.php";
      }

      // So sánh nếu tên file của link trùng với tên file của trang hiện tại
      if (linkPage === currentPage) {
        link.classList.add('active'); // Thêm class 'active'
      }
    });
})();
</script>

<div class="accepted-orders-container">
    <div class="hero">
        <h1><i class="fas fa-check-circle"></i> Lịch sử đặt hàng của bạn</h1>
    </div>

    <div class="orders-wrapper">
        <h2>Xin chào, <span class="highlight"><?php echo $customer_name; ?></span> 👋</h2>

        <div class="order-cards">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {

                    // Lấy ID người chăm sóc
                    $id_cham_soc = $row['id_cham_soc']; 
                    $ten_cham_soc = "Chưa có";
                    
                    // 🔍 Tìm tên người chăm sóc dựa trên id_nguoi_cham_soc
                    if (!empty($id_cham_soc)) {
                        $sql2 = "SELECT ho_ten FROM nguoi_cham_soc WHERE id_cham_soc = ?";
                        $stmt2 = $conn->prepare($sql2);
                        $stmt2->bind_param("i", $id_cham_soc); 
                        $stmt2->execute();
                        $res2 = $stmt2->get_result();
                        if ($res2->num_rows > 0) {
                            $ten_cham_soc = htmlspecialchars($res2->fetch_assoc()['ho_ten']);
                        }
                        $stmt2->close();
                    }

                    // 💬 Hiển thị từng đơn hàng
                    echo "
                    <div class='order-card'>
                        <div>
                            <h3>Mã đơn: #{$row['id_don_hang']}</h3>
                            <div class='order-info'>
                                <p><strong>Người chăm sóc:</strong> {$ten_cham_soc}</p>
                                <p><strong>Ngày đặt:</strong> {$row['ngay_dat']}</p>
                                <p><strong>Trạng thái:</strong> 
                                    <span class='status " . 
                                        ($row['trang_thai'] == 'hoàn thành' ? 'completed' : 'pending') . "'>
                                        {$row['trang_thai']}
                                    </span>
                                </p>
                                <p><strong>Tổng tiền:</strong> " . number_format($row['tong_tien'], 0, ',', '.') . "₫</p>
                            </div>
                        </div>
                        <a href='chitietlichsudonhang.php?id={$row['id_don_hang']}' class='view-btn'>Xem</a>
                    </div>";
                }
            } else {
                echo "<p style='text-align: center; color: #ff6b81;'>❌ Bạn chưa có đơn hàng nào.</p>";
            }

            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>
</div>
</body>
</html>
