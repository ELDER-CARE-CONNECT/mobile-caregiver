<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>CARE SEEKER</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
   <header>
    <h1>
        <a href="index.php?page=1" class="logo">CARE SEEKER</a>
    </h1>
    <nav>
        <a href="index.php?page=1">Trang chính</a>
        <a href="index.php?page=2">Dịch vụ</a>
        <a href="index.php?page=3">Liên hệ</a>
        <a href="index.php?page=4">Thông tin cá nhân</a>
    </nav>
</header>


        <main>
            <?php
            $page = isset($_GET['page']) ? $_GET['page'] : 1;
            switch ($page) {
                case 1:
                    include "trang1.php";
                    break;
                case 2:
                    include "trang2.php";
                    break;
                case 3:
                    include "trang3.php";
                    break;
                case 4:
                    include "trang4.php";
                    break;
                default:
                    include "trang1.php";
                    break;
            }
            ?>
        </main>

        <footer>
            <p>&copy; 2025 ELDER-CARE-CONNECT. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
