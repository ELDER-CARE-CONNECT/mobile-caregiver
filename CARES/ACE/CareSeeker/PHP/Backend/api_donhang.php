<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'khach_hang' || !isset($_SESSION['so_dien_thoai'])) {

    header("Location: index.php"); 
    exit();
}

$host = '127.0.0.1';
$dbname = 'sanpham';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
} catch (PDOException $e) {
    die("Kết nối DB thất bại: " . $e->getMessage());
}
n
$order = [];
$services = []; 
$id_khach_hang = 0;
$is_rated = false;
$id_don_hang = isset($_GET['id']) ? intval($_GET['id']) : 0;
$so_dien_thoai_session = $_SESSION['so_dien_thoai'];

$stmt_kh = $pdo->prepare("
    SELECT 
        id_khach_hang, 
        ten_khach_hang, 
        TRIM(CONCAT_WS(', ', ten_duong, phuong_xa, tinh_thanh)) AS dia_chi
    FROM khach_hang 
    WHERE so_dien_thoai = ?
");
$stmt_kh->execute([$so_dien_thoai_session]);
$user = $stmt_kh->fetch();

if ($user) {
    $id_khach_hang = $user['id_khach_hang'];
    $_SESSION['ten_khach_hang'] = $user['ten_khach_hang'];
} else {
    session_destroy();
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'cancel_order') {
    $id_don_hang_to_cancel = isset($_POST['id_don_hang']) ? intval($_POST['id_don_hang']) : 0;

    if ($id_don_hang_to_cancel > 0 && $id_khach_hang > 0) {
        $stmt_check = $pdo->prepare("SELECT trang_thai FROM don_hang WHERE id_don_hang = ? AND id_khach_hang = ?");
        $stmt_check->execute([$id_don_hang_to_cancel, $id_khach_hang]);
        $order_status = $stmt_check->fetchColumn();
        if ($order_status && strtolower(trim($order_status)) === 'chờ xác nhận') {
            try {
                $stmt_update = $pdo->prepare("UPDATE don_hang SET trang_thai = 'đã hủy' WHERE id_don_hang = ?");
                $stmt_update->execute([$id_don_hang_to_cancel]);
                header("Location: ChiTietDonHang.php?id=" . $id_don_hang_to_cancel . "&status=cancelled");
                exit();
            } catch (PDOException $e) {
                header("Location: ChiTietDonHang.php?id=" . $id_don_hang_to_cancel . "&error=cancel_failed");
                exit();
            }
        } else {
            header("Location: ChiTietDonHang.php?id=" . $id_don_hang_to_cancel . "&error=status_mismatch");
            exit();
        }
    } else {
        header("Location: index.php");
        exit();
    }
}

if ($id_khach_hang > 0) {
    if ($id_don_hang == 0) {
        $stmt_latest = $pdo->prepare("SELECT id_don_hang FROM don_hang WHERE id_khach_hang = ? ORDER BY ngay_dat DESC LIMIT 1");
        $stmt_latest->execute([$id_khach_hang]);
        $latest_order_id = $stmt_latest->fetchColumn();
        if ($latest_order_id) {
            $id_don_hang = intval($latest_order_id);
        }
    }
    if ($id_don_hang > 0) {
        $stmt = $pdo->prepare("
            SELECT 
                dh.*, 
                kh.ten_khach_hang, kh.so_dien_thoai, 
                TRIM(CONCAT_WS(', ', kh.ten_duong, kh.phuong_xa, kh.tinh_thanh)) AS dia_chi_kh,
                ncs.ho_ten AS ten_cham_soc, ncs.hinh_anh AS hinh_anh_cham_soc, ncs.id_cham_soc AS caregiver_id
            FROM don_hang dh
            LEFT JOIN khach_hang kh ON dh.id_khach_hang = kh.id_khach_hang
            LEFT JOIN nguoi_cham_soc ncs ON dh.id_nguoi_cham_soc = ncs.id_cham_soc
            WHERE dh.id_don_hang = :id_dh AND dh.id_khach_hang = :id_kh
        ");
        $stmt->bindValue(':id_dh', $id_don_hang, PDO::PARAM_INT);
        $stmt->bindValue(':id_kh', $id_khach_hang, PDO::PARAM_INT);
        $stmt->execute();
        $order = $stmt->fetch();

        if ($order) {
            if (strtolower(trim($order['trang_thai'])) === 'đã hoàn thành' && $order['caregiver_id']) {
                $stmt_check_review = $pdo->prepare("
                    SELECT id_danh_gia 
                    FROM danh_gia 
                    WHERE id_khach_hang = :id_kh AND id_cham_soc = :id_cs AND id_don_hang = :id_dh
                    LIMIT 1
                ");
                $stmt_check_review->bindValue(':id_kh', $id_khach_hang, PDO::PARAM_INT);
                $stmt_check_review->bindValue(':id_cs', $order['caregiver_id'], PDO::PARAM_INT);
                $stmt_check_review->bindValue(':id_dh', $id_don_hang, PDO::PARAM_INT);
                $stmt_check_review->execute();
                if ($stmt_check_review->fetchColumn()) {
                    $is_rated = true;
                }
            }
            
            if (!empty($order['ten_nhiem_vu'])) {
                $is_array_string = (strpos($order['ten_nhiem_vu'] ?? '', '["') === 0) && (strpos($order['ten_nhiem_vu'] ?? '', '"]') !== false);
                        
                if ($is_array_string) {
                    $tasks_list = json_decode(str_replace(';', ',', $order['ten_nhiem_vu']), true);
                } else {
                    $tasks_list = preg_split("/\r\n|\n|\r/", $order['ten_nhiem_vu']);
                }

                $tasks_list = array_filter(array_map('trim', $tasks_list)); 

                foreach ($tasks_list as $task) {
                    $task_name = trim($task, ' "');
                    if (!empty($task_name)) {
                        $services[] = [
                            'ten_nhiem_vu' => $task_name,
                        ];
                    }
                }
            }
        }
    }
}

function status_to_class($status) {
    $status = strtolower(trim($status));
    $status = str_replace(
        [' ', 'á','à','ả','ã','ạ','ă','ằ','ắ','ẳ','ặ','â','ầ','ấ','ẩ','ậ','đ','é','è','ẻ','ẽ','ẹ','ê','ề','ế','ể','ệ','í','ì','ỉ','ĩ','ị','ó','ò','ỏ','õ','ọ','ô','ồ','ố','ổ','ộ','ơ','ờ','ớ','ở','ợ','ú','ù','ủ','ũ','ụ','ư','ừ','ứ','ử','ự','ý','ỳ','ỷ','ỹ','ỵ'],
        ['_','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','d','e','e','e','e','e','e','e','e','e','e','i','i','i','i','i','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','u','u','u','u','u','u','u','u','u','u','y','y','y','y','y'],
        $status
    );
    if ($status === 'dang_hoan_thanh') return 'dang_tien_hanh'; 
    if ($status === 'da_hoan_thanh') return 'hoan_thanh';
    return $status;
}

$message = '';
if (isset($_GET['status']) && $_GET['status'] === 'cancelled') {
    $message = '<div style="background-color: #e8f5e9; color: #4caf50; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; text-align: center;"><i class="fas fa-check-circle"></i> Đơn hàng #' . htmlspecialchars($id_don_hang) . ' đã được hủy thành công!</div>';
} elseif (isset($_GET['error'])) {
    $error_msg = 'Đã xảy ra lỗi khi hủy đơn hàng.';
    if ($_GET['error'] === 'status_mismatch') {
        $error_msg = 'Chỉ có thể hủy đơn hàng ở trạng thái **Chờ xác nhận**.';
    } elseif ($_GET['error'] === 'cancel_failed') {
        $error_msg = 'Lỗi hệ thống: Không thể cập nhật trạng thái đơn hàng.';
    }
    $message = '<div style="background-color: #ffebee; color: #f44336; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-weight: 600; text-align: center;"><i class="fas fa-times-circle"></i> ' . $error_msg . '</div>';
}
?>