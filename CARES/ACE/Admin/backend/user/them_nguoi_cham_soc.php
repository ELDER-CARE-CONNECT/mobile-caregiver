<?php
header('Content-Type: application/json');
include __DIR__.'/../config/connect.php';
<<<<<<< HEAD

$conn = connectdb();
$response = ['status'=>'error','message'=>''];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Lấy dữ liệu từ form
    $ho_ten = trim($_POST['ho_ten'] ?? '');
    $dia_chi = trim($_POST['dia_chi'] ?? '');
    $tuoi = intval($_POST['tuoi'] ?? 0);
    $gioi_tinh = trim($_POST['gioi_tinh'] ?? '');
    $chieu_cao = floatval($_POST['chieu_cao'] ?? 0);
    $can_nang = floatval($_POST['can_nang'] ?? 0);
    $kinh_nghiem = trim($_POST['kinh_nghiem'] ?? '');
    $so_dien_thoai = trim($_POST['so_dien_thoai'] ?? '');
    $mat_khau = trim($_POST['mat_khau'] ?? '');
    $tong_tien_kiem_duoc = floatval($_POST['tong_tien_kiem_duoc'] ?? 0);

    // Xử lý upload ảnh
    $hinh_anh = '';
    if(!empty($_FILES['hinh_anh']['name'])){
        $targetDir = __DIR__ . '/../../frontend/upload/';
        if(!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time().'_'.basename($_FILES['hinh_anh']['name']);
        $targetFilePath = $targetDir . $fileName;

        if(move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $targetFilePath)){
            // Lưu đường dẫn relative cho frontend
            $hinh_anh = 'frontend/upload/' . $fileName;
        }
    }

    // Chuẩn bị câu lệnh SQL
    $stmt = $conn->prepare("INSERT INTO nguoi_cham_soc 
        (so_dien_thoai, mat_khau, hinh_anh, ho_ten, dia_chi, tuoi, gioi_tinh, chieu_cao, can_nang, kinh_nghiem, tong_tien_kiem_duoc)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    if(!$stmt){
        $response['message'] = "Prepare statement lỗi: " . $conn->error;
        echo json_encode($response);
        exit;
    }

    // Bind params (chỉnh đúng kiểu dữ liệu)
    $stmt->bind_param(
        "sssssisddsd",
        $so_dien_thoai,
        $mat_khau,
        $hinh_anh,
        $ho_ten,
        $dia_chi,
        $tuoi,
        $gioi_tinh,         // s → string
        $chieu_cao,         // d → double
        $can_nang,          // d → double
        $kinh_nghiem,       // s → string
        $tong_tien_kiem_duoc // d → double
    );
=======
$conn = connectdb();

$response = ['status'=>'error','message'=>''];

if($_SERVER['REQUEST_METHOD']=='POST'){
    $ho_ten = $_POST['ho_ten'] ?? '';
    $dia_chi = $_POST['dia_chi'] ?? '';
    $tuoi = intval($_POST['tuoi'] ?? 0);
    $gioi_tinh = $_POST['gioi_tinh'] ?? '';
    $chieu_cao = floatval($_POST['chieu_cao'] ?? 0);
    $can_nang = floatval($_POST['can_nang'] ?? 0);
    $kinh_nghiem = $_POST['kinh_nghiem'] ?? '';
    $so_dien_thoai = $_POST['so_dien_thoai'] ?? '';
    $mat_khau = $_POST['mat_khau'] ?? '';
    $tong_tien_kiem_duoc = floatval($_POST['tong_tien_kiem_duoc'] ?? 0); // Thêm trường này

    // Upload ảnh vào frontend/upload
    $hinh_anh = '';
    if(!empty($_FILES['hinh_anh']['name'])){
        $targetDir = __DIR__ . '/../../frontend/upload/';
        if(!is_dir($targetDir)) mkdir($targetDir,0777,true);

        $fileName = time().'_'.basename($_FILES['hinh_anh']['name']);
        $targetFilePath = $targetDir.$fileName;

        if(move_uploaded_file($_FILES['hinh_anh']['tmp_name'],$targetFilePath)){
            $hinh_anh = 'frontend/upload/'.$fileName;
        }
    }

    $stmt = $conn->prepare("INSERT INTO nguoi_cham_soc 
        (so_dien_thoai, mat_khau, hinh_anh, ho_ten, dia_chi, tuoi, gioi_tinh, chieu_cao, can_nang, kinh_nghiem, tong_tien_kiem_duoc)
        VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("sssssiiddsd",$so_dien_thoai,$mat_khau,$hinh_anh,$ho_ten,$dia_chi,$tuoi,$gioi_tinh,$chieu_cao,$can_nang,$kinh_nghiem,$tong_tien_kiem_duoc);
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4

    if($stmt->execute()){
        $response = ['status'=>'success','message'=>'Thêm người chăm sóc thành công!'];
    } else {
<<<<<<< HEAD
        $response = [
            'status'=>'error',
            'message'=>'Execute lỗi: '.$stmt->error.' / '.$conn->error
        ];
=======
        $response = ['status'=>'error','message'=>$conn->error];
>>>>>>> b818157e1da1ecb405aab9e6efd25fb21bc2f3d4
    }

    $stmt->close();
}

$conn->close();
echo json_encode($response);
